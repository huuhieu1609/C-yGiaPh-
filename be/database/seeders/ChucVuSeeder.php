<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ChucVuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            ['ten_chuc_vu' => 'Quản Trị Viên Tổng', 'mo_ta' => 'Toàn quyền hệ thống'],
            ['ten_chuc_vu' => 'Trưởng Nhánh', 'mo_ta' => 'Quản lý chi nhánh và dữ liệu nội bộ'],
            ['ten_chuc_vu' => 'Thành Viên', 'mo_ta' => 'Thành viên bình thường của dòng họ'],
        ];

        foreach ($roles as $role) {
            DB::table('chuc_vus')->updateOrInsert(
                ['ten_chuc_vu' => $role['ten_chuc_vu']],
                array_merge($role, [
                    'trang_thai' => 'Hoạt động',
                    'updated_at' => now(),
                    'created_at' => now(),
                ])
            );
        }

        // Map default permissions for Thành Viên
        $branchAdminId = DB::table('chuc_vus')->where('ten_chuc_vu', 'Trưởng Nhánh')->value('id');
        $memberId = DB::table('chuc_vus')->where('ten_chuc_vu', 'Thành Viên')->value('id');

        $memberPermissions = [
            'cay-gia-pha',
            'tra-cuu-xung-ho',
            'quan-ly-mo-phan',
            'quan-ly-su-kien',
            'quan-ly-tai-lieu',
            'quan-ly-thong-bao',
            'nhat-ky-thao-tac'
        ];

        if ($memberId) {
            $permissionIds = DB::table('chuc_nangs')
                ->whereIn('ten_slug', $memberPermissions)
                ->pluck('id');

            foreach ($permissionIds as $pId) {
                DB::table('chi_tiet_phan_quyens')->updateOrInsert(
                    ['chuc_vu_id' => $memberId, 'chuc_nang_id' => $pId],
                    ['created_at' => now(), 'updated_at' => now()]
                );
            }
        }

        // Map default permissions for Trưởng Nhánh
        $branchAdminPermissions = [
            'cay-gia-pha',
            'tra-cuu-xung-ho',
            'quan-ly-chi-nhanh',
            'quan-ly-mo-phan',
            'quan-ly-su-kien',
            'quan-ly-tai-lieu',
            'quan-ly-thong-bao',
            'nhat-ky-thao-tac',
            'quan-ly-thanh-vien',
            'kiem-duyet-de-xuat',
            'quan-ly-goi-dich-vu'
        ];

        if ($branchAdminId) {
            $permissionIds = DB::table('chuc_nangs')
                ->whereIn('ten_slug', $branchAdminPermissions)
                ->pluck('id');

            foreach ($permissionIds as $pId) {
                DB::table('chi_tiet_phan_quyens')->updateOrInsert(
                    ['chuc_vu_id' => $branchAdminId, 'chuc_nang_id' => $pId],
                    ['created_at' => now(), 'updated_at' => now()]
                );
            }
        }
    }
}
