<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ChucNangSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            ['ten_chuc_nang' => 'Admin Dashboard', 'ten_slug' => 'admin-dashboard', 'url' => '/admin/dashboard', 'mo_ta' => 'Xem trang tổng quan'],
            ['ten_chuc_nang' => 'Cây Gia Phả', 'ten_slug' => 'cay-gia-pha', 'url' => '/cay-gia-pha', 'mo_ta' => 'Quản lý cây gia phả'],
            ['ten_chuc_nang' => 'Tra Cứu Xưng Hô', 'ten_slug' => 'tra-cuu-xung-ho', 'url' => '/tra-cuu-xung-ho', 'mo_ta' => 'Sử dụng công cụ tra cứu xưng hô'],
            ['ten_chuc_nang' => 'Quản Lý Dòng Họ', 'ten_slug' => 'quan-ly-dong-ho', 'url' => '/quan-ly-dong-ho', 'mo_ta' => 'Quản lý chi nhánh và đời tộc họ'],
            ['ten_chuc_nang' => 'Quản Lý Nhà Thờ Họ', 'ten_slug' => 'quan-ly-nha-tho-ho', 'url' => '/quan-ly-nha-tho-ho', 'mo_ta' => 'Quản lý thông tin nhà thờ họ'],
            ['ten_chuc_nang' => 'Quản Lý Sự Kiện', 'ten_slug' => 'quan-ly-su-kien', 'url' => '/quan-ly-su-kien', 'mo_ta' => 'Quản lý các sự kiện dòng họ'],
            ['ten_chuc_nang' => 'Hệ Thống', 'ten_slug' => 'he-thong', 'url' => '/he-thong', 'mo_ta' => 'Phân quyền và quản lý tài khoản hệ thống'],
        ];

        foreach ($permissions as $permission) {
            DB::table('chuc_nangs')->updateOrInsert(
                ['ten_slug' => $permission['ten_slug']],
                array_merge($permission, [
                    'trang_thai' => 'Hoạt động',
                    'updated_at' => now(),
                    'created_at' => now(),
                ])
            );
        }
    }
}
