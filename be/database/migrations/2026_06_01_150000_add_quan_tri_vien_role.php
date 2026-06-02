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
        // 1. Insert/Update the Quản Trị Viên role
        DB::table('chuc_vus')->updateOrInsert(
            ['ten_chuc_vu' => 'Quản Trị Viên'],
            [
                'mo_ta' => 'Quản trị viên hệ thống dưới quyền Admin',
                'trang_thai' => 'Hoạt động',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        $quanTriVienId = DB::table('chuc_vus')->where('ten_chuc_vu', 'Quản Trị Viên')->value('id');

        if ($quanTriVienId) {
            // 2. Define default permissions (chức năng) for Quản Trị Viên
            $defaultSlugs = [
                'admin-dashboard',
                'cay-gia-pha',
                'tra-cuu-xung-ho',
                'quan-ly-chi-nhanh',
                'quan-ly-mo-phan',
                'he-thong',
                'quan-ly-nguoi-dung',
                'quan-ly-chuc-vu'
            ];

            $permissionIds = DB::table('chuc_nangs')
                ->whereIn('ten_slug', $defaultSlugs)
                ->pluck('id');

            foreach ($permissionIds as $pId) {
                DB::table('chi_tiet_phan_quyens')->updateOrInsert(
                    ['chuc_vu_id' => $quanTriVienId, 'chuc_nang_id' => $pId],
                    ['created_at' => now(), 'updated_at' => now()]
                );
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $quanTriVienId = DB::table('chuc_vus')->where('ten_chuc_vu', 'Quản Trị Viên')->value('id');
        if ($quanTriVienId) {
            DB::table('chi_tiet_phan_quyens')->where('chuc_vu_id', $quanTriVienId)->delete();
            DB::table('chuc_vus')->where('id', $quanTriVienId)->delete();
        }
    }
};
