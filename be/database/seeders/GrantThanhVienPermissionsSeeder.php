<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GrantThanhVienPermissionsSeeder extends Seeder
{
    /**
     * Cấp quyền cần thiết cho chức vụ "Thành Viên" (id=3)
     * để tài khoản member như minhvy@master.com có thể xem cây gia phả.
     */
    public function run(): void
    {
        // Lấy ID chức vụ "Thành Viên"
        $idChucVu = DB::table('chuc_vus')
            ->where('ten_chuc_vu', 'like', '%Thành Viên%')
            ->value('id');

        if (!$idChucVu) {
            $this->command->warn('Không tìm thấy chức vụ "Thành Viên". Bỏ qua.');
            return;
        }

        // Lấy IDs các chức năng cần cấp cho Thành Viên
        $memberPermissions = [
            'Cây Gia Phả',
            'Tra Cứu Xưng Hô',
            'Quản Lý Mộ Phần',
            'Quản Lý Sự Kiện',
            'Quỹ & Sự Kiện',
            'Quản Lý Tài Liệu',
            'Quản Lý Đóng Góp',
            'Quản Lý Thông Báo',
        ];

        $insertedCount = 0;
        foreach ($memberPermissions as $tenChucNang) {
            $idChucNang = DB::table('chuc_nangs')
                ->where('ten_chuc_nang', $tenChucNang)
                ->where('trang_thai', 'Hoạt động')
                ->value('id');

            if (!$idChucNang) {
                $this->command->warn("Không tìm thấy chức năng: $tenChucNang");
                continue;
            }

            // Kiểm tra đã có chưa tránh trùng lặp
            $exists = DB::table('chi_tiet_phan_quyens')
                ->where('chuc_vu_id', $idChucVu)
                ->where('chuc_nang_id', $idChucNang)
                ->exists();

            if (!$exists) {
                DB::table('chi_tiet_phan_quyens')->insert([
                    'chuc_vu_id'   => $idChucVu,
                    'chuc_nang_id' => $idChucNang,
                    'created_at'   => now(),
                    'updated_at'   => now(),
                ]);
                $insertedCount++;
                $this->command->info("✓ Đã cấp quyền '$tenChucNang' cho chức vụ 'Thành Viên'");
            } else {
                $this->command->line("  (Đã tồn tại) '$tenChucNang' cho chức vụ 'Thành Viên'");
            }
        }

        $this->command->info("\nHoàn tất: Đã cấp $insertedCount quyền mới cho chức vụ 'Thành Viên' (id=$idChucVu)");
    }
}
