<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChiNhanh extends Model
{
    use HasFactory;

    protected $table = 'chi_nhanhs';

    // Cần đảm bảo database bảng chi_nhanhs có cột 'id_nguoi_dung' để kiểm tra quyền sở hữu
    protected $fillable = [
        'ten_chi', 'mo_ta', 'id_nguoi_dung', 'is_auto_approve',
    ];

    // Một chi nhánh có nhiều thành viên
    public function thanhViens()
    {
        return $this->hasMany(ThanhVien::class, 'chi_nhanh_id', 'id');
    }

    /**
     * Lấy danh sách ID chi nhánh mà một người dùng (Admin hoặc Đối tác) được quyền quản lý.
     *
     * - Admin -> toàn bộ chi nhánh.
     * - Đối tác -> chi nhánh tự tạo + chi nhánh được liên kết của chính họ.
     */
    public static function getManagedBranchIds($user): array
    {
        if (!$user) {
            return [];
        }

        if (strtolower(trim($user->vai_tro)) === 'admin') {
            return self::pluck('id')->toArray();
        }

        if ($user->is_doi_tac == 1) {
            // 1. Chi nhánh do đối tác này tự tạo/sở hữu trực tiếp
            $ownedBranchIds = self::where('id_nguoi_dung', $user->id)->pluck('id')->toArray();

            // 2. Chi nhánh của thành viên liên kết trực tiếp trong bảng nguoi_dungs
            $memberBranchId = $user->chi_nhanh_id;

            // 3. Chi nhánh của thành viên liên kết qua Email trong bảng thanh_viens
            $emailBranchId = \App\Models\ThanhVien::where('email', $user->email)
                ->whereNotNull('email')
                ->value('chi_nhanh_id');

            $allBranchIds = array_merge($ownedBranchIds, [$memberBranchId, $emailBranchId]);
            return array_values(array_unique(array_filter($allBranchIds)));
        }

        // Người dùng thường: chỉ có chi nhánh họ thuộc về
        $memberBranchId = $user->chi_nhanh_id;
        $emailBranchId = \App\Models\ThanhVien::where('email', $user->email)
            ->whereNotNull('email')
            ->value('chi_nhanh_id');

        return array_values(array_unique(array_filter([$memberBranchId, $emailBranchId])));
    }
}
