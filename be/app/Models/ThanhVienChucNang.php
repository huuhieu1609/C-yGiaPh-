<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ThanhVienChucNang extends Model
{
    use HasFactory;

    protected $table = 'thanh_vien_chuc_nangs';

    protected $fillable = [
        'thanh_vien_id',
        'chuc_nang_id',
        'is_enabled',
    ];

    protected $casts = [
        'is_enabled' => 'boolean',
    ];

    public function thanhVien()
    {
        return $this->belongsTo(ThanhVien::class, 'thanh_vien_id');
    }

    public function chucNang()
    {
        return $this->belongsTo(ChucNang::class, 'chuc_nang_id');
    }

    public static function getMemberActivePermissions($user)
    {
        if (!$user) {
            return [];
        }

        // 1. Master Admin luôn có toàn quyền
        if (strtolower(trim($user->vai_tro)) === 'admin') {
            return \App\Models\ChucNang::where('trang_thai', 'Hoạt động')
                ->pluck('ten_chuc_nang')
                ->toArray();
        }

        // Lấy danh sách quyền được Admin cấp cho vai trò "Trưởng Nhánh" (dành cho đối tác)
        $truongNhanhId = \Illuminate\Support\Facades\DB::table('chuc_vus')
            ->where('ten_chuc_vu', 'Trưởng Nhánh')
            ->value('id') ?? 2;

        $truongNhanhPermissions = ChiTietPhanQuyen::join('chuc_nangs', 'chi_tiet_phan_quyens.chuc_nang_id', '=', 'chuc_nangs.id')
            ->where('chi_tiet_phan_quyens.chuc_vu_id', $truongNhanhId)
            ->where('chuc_nangs.trang_thai', 'Hoạt động')
            ->pluck('chuc_nangs.ten_chuc_nang')
            ->toArray();

        // 2. Đối tác tối cao (chủ tài khoản đối tác) không có chức vụ -> đồng bộ với quyền của Trưởng Nhánh
        if ($user->is_doi_tac == 1 && !$user->id_chuc_vu) {
            return $truongNhanhPermissions;
        }

        // 3. Với thành viên bình thường hoặc người dùng có chức vụ:
        $idChucVu = $user->id_chuc_vu;
        if (!$idChucVu) {
            $idChucVu = \Illuminate\Support\Facades\DB::table('chuc_vus')
                ->where('ten_chuc_vu', 'like', '%Thành Viên%')
                ->value('id') ?? 3;
        }

        // Lấy danh sách quyền Admin đã cấp cho chức vụ này
        $admin_permissions = ChiTietPhanQuyen::join('chuc_nangs', 'chi_tiet_phan_quyens.chuc_nang_id', '=', 'chuc_nangs.id')
            ->where('chi_tiet_phan_quyens.chuc_vu_id', $idChucVu)
            ->where('chuc_nangs.trang_thai', 'Hoạt động')
            ->pluck('chuc_nangs.ten_chuc_nang')
            ->toArray();

        $result_permissions = $admin_permissions;

        // Tìm thành viên tương ứng qua email
        $thanhVien = \App\Models\ThanhVien::where('email', $user->email)->whereNotNull('email')->first();
        if ($thanhVien) {
            // Kiểm tra xem đối tác đã tùy chỉnh phân quyền cho thành viên này chưa
            $custom_exists = self::where('thanh_vien_id', $thanhVien->id)->exists();
            if ($custom_exists) {
                // Lấy các quyền được BẬT tùy chỉnh bởi đối tác
                $partner_enabled = self::join('chuc_nangs', 'thanh_vien_chuc_nangs.chuc_nang_id', '=', 'chuc_nangs.id')
                    ->where('thanh_vien_chuc_nangs.thanh_vien_id', $thanhVien->id)
                    ->where('thanh_vien_chuc_nangs.is_enabled', true)
                    ->where('chuc_nangs.trang_thai', 'Hoạt động')
                    ->pluck('chuc_nangs.ten_chuc_nang')
                    ->toArray();

                // Thực hiện giao thoa (intersection)
                $result_permissions = array_values(array_intersect($admin_permissions, $partner_enabled));
            }
        }

        // Nếu tài khoản này thuộc đối tác, phải đồng bộ giới hạn bởi quyền của Trưởng Nhánh
        if ($user->is_doi_tac == 1) {
            return array_values(array_intersect($result_permissions, $truongNhanhPermissions));
        }

        return $result_permissions;
    }
}
