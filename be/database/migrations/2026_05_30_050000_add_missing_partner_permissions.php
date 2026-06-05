<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $newPermissions = [
            [
                'ten_chuc_nang' => 'Quản Lý Thành Viên',
                'ten_slug' => 'quan-ly-thanh-vien',
                'url' => '/doi-tac/thanh-vien',
                'mo_ta' => 'Quản lý thành viên chi nhánh dòng họ',
                'trang_thai' => 'Hoạt động',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'ten_chuc_nang' => 'Kiểm Duyệt Đề Xuất',
                'ten_slug' => 'kiem-duyet-de-xuat',
                'url' => '/doi-tac/de-xuat',
                'mo_ta' => 'Kiểm duyệt đề xuất chỉnh sửa gia phả',
                'trang_thai' => 'Hoạt động',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'ten_chuc_nang' => 'Quản Lý Gói Dịch Vụ',
                'ten_slug' => 'quan-ly-goi-dich-vu',
                'url' => '/doi-tac/quan-ly-goi',
                'mo_ta' => 'Quản lý thông tin gói và thanh toán',
                'trang_thai' => 'Hoạt động',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        $truongNhanhId = DB::table('chuc_vus')->where('ten_chuc_vu', 'Trưởng Nhánh')->value('id') ?? 2;
        $superAdminId = DB::table('chuc_vus')->where('ten_chuc_vu', 'Quản Trị Viên Tổng')->value('id') ?? 1;

        foreach ($newPermissions as $perm) {
            // Kiểm tra tránh trùng lặp
            $existing = DB::table('chuc_nangs')->where('ten_slug', $perm['ten_slug'])->first();
            if ($existing) {
                $id = $existing->id;
            } else {
                $id = DB::table('chuc_nangs')->insertGetId($perm);
            }

            // Gán cho Trưởng Nhánh
            DB::table('chi_tiet_phan_quyens')->updateOrInsert([
                'chuc_vu_id' => $truongNhanhId,
                'chuc_nang_id' => $id,
            ], [
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Gán cho Super Admin
            DB::table('chi_tiet_phan_quyens')->updateOrInsert([
                'chuc_vu_id' => $superAdminId,
                'chuc_nang_id' => $id,
            ], [
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No down needed
    }
};
