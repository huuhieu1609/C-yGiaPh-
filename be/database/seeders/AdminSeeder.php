<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Clear existing data to avoid duplicates
        DB::table('chi_tiet_phan_quyens')->delete();
        DB::table('nguoi_dungs')->where('email', 'admin@master.com')->delete();
        DB::table('chuc_nangs')->delete();
        DB::table('chuc_vus')->delete();

        // 2. Create Default Roles (Chức Vụ)
        $id_chuc_vu = DB::table('chuc_vus')->insertGetId([
            'ten_chuc_vu' => 'Quản Trị Viên Tổng',
            'mo_ta'       => 'Toàn quyền hệ thống',
            'trang_thai'  => 'Hoạt động',
            'created_at'  => now(),
            'updated_at'  => now(),
        ]);

        // 3. Create Default Permissions (Chức Năng)
        $permissions = [
            ['ten_chuc_nang' => 'Admin Dashboard', 'ten_slug' => 'admin-dashboard', 'url' => '/admin/dashboard', 'mo_ta' => 'Xem trang tổng quan'],
            ['ten_chuc_nang' => 'Cây Gia Phả', 'ten_slug' => 'cay-gia-pha', 'url' => '/cay-gia-pha', 'mo_ta' => 'Quản lý cây gia phả'],
            ['ten_chuc_nang' => 'Tra Cứu Xưng Hô', 'ten_slug' => 'tra-cuu-xung-ho', 'url' => '/tra-cuu-xung-ho', 'mo_ta' => 'Sử dụng công cụ tra cứu'],
            ['ten_chuc_nang' => 'Quản Lý Dòng Họ', 'ten_slug' => 'quan-ly-dong-ho', 'url' => '/quan-ly-dong-ho', 'mo_ta' => 'Quản lý chi nhánh, đời, nhà thờ...'],
            ['ten_chuc_nang' => 'Quản Lý Chi Nhánh', 'ten_slug' => 'quan-ly-chi-nhanh', 'url' => '/quan-ly-chi-nhanh', 'mo_ta' => 'Quản lý các chi nhánh dòng họ'],
            ['ten_chuc_nang' => 'Quản Lý Đời Tộc Họ', 'ten_slug' => 'quan-ly-doi-toc-ho', 'url' => '/quan-ly-doi-toc-ho', 'mo_ta' => 'Quản lý các đời trong dòng họ'],
            ['ten_chuc_nang' => 'Quản Lý Nhà Thờ Họ', 'ten_slug' => 'quan-ly-nha-tho-ho', 'url' => '/quan-ly-nha-tho-ho', 'mo_ta' => 'Quản lý thông tin nhà thờ'],
            ['ten_chuc_nang' => 'Quản Lý Mộ Phần', 'ten_slug' => 'quan-ly-mo-phan', 'url' => '/quan-ly-mo-phan', 'mo_ta' => 'Quản lý thông tin mộ phần'],
            ['ten_chuc_nang' => 'Quỹ & Sự Kiện', 'ten_slug' => 'quy-su-kien', 'url' => '/quy-su-kien', 'mo_ta' => 'Quản lý quỹ và các sự kiện'],
            ['ten_chuc_nang' => 'Hệ Thống', 'ten_slug' => 'he-thong', 'url' => '/he-thong', 'mo_ta' => 'Quản lý người dùng, chức vụ, chức năng'],
            ['ten_chuc_nang' => 'Quản Lý Người Dùng', 'ten_slug' => 'quan-ly-nguoi-dung', 'url' => '/quan-ly-nguoi-dung', 'mo_ta' => 'Quản lý tài khoản hệ thống'],
            ['ten_chuc_nang' => 'Quản Lý Chức Vụ', 'ten_slug' => 'quan-ly-chuc-vu', 'url' => '/quan-ly-chuc-vu', 'mo_ta' => 'Quản lý các chức vụ/vai trò'],
            ['ten_chuc_nang' => 'Quản Lý Chức Năng', 'ten_slug' => 'quan-ly-chuc-nang', 'url' => '/quan-ly-chuc-nang', 'mo_ta' => 'Quản lý các quyền hạn'],
        ];

        foreach ($permissions as $p) {
            $id_cn = DB::table('chuc_nangs')->insertGetId(array_merge($p, [
                'trang_thai' => 'Hoạt động',
                'created_at' => now(),
                'updated_at' => now(),
            ]));

            // Assign every permission to the Super Admin Role
            DB::table('chi_tiet_phan_quyens')->insert([
                'chuc_vu_id'   => $id_chuc_vu,
                'chuc_nang_id' => $id_cn,
                'created_at'   => now(),
                'updated_at'   => now(),
            ]);
        }

        // 4. Create Admin User
        DB::table('nguoi_dungs')->insert([
            
            'ho_ten'        => 'Master Admin',
            'email'         => 'admin@master.com',
            'mat_khau'      => Hash::make('123456'),
            'so_dien_thoai' => '0123456789',
            'vai_tro'       => 'Admin',
            'id_chuc_vu'    => $id_chuc_vu,
            'trang_thai'    => 'Hoạt động',
            'created_at'    => now(),
            'updated_at'    => now(),
        ]);
    }
}
