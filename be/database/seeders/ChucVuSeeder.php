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
            ['ten_chuc_vu' => 'Quản Trị Viên Chi Nhánh', 'mo_ta' => 'Quản lý chi nhánh và dữ liệu nội bộ'],
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
    }
}
