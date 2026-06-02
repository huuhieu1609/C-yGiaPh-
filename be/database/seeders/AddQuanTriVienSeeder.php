<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AddQuanTriVienSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Thêm chức vụ Quản trị viên
        DB::table('chuc_vus')->updateOrInsert(
            ['ten_chuc_vu' => 'Quản trị viên'],
            [
                'mo_ta' => 'Quản trị viên phụ tá, dưới quyền Admin tối cao và được phân quyền cụ thể',
                'trang_thai' => 'Hoạt động',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        $chucVuId = DB::table('chuc_vus')->where('ten_chuc_vu', 'Quản trị viên')->value('id');

        // 2. Thêm người dùng Quản trị viên
        DB::table('nguoi_dungs')->updateOrInsert(
            ['email' => 'quantrivien@master.com'],
            [
                'ho_ten' => 'Quản Trị Viên Phụ Tá',
                'mat_khau' => Hash::make('123456'),
                'so_dien_thoai' => '0977888999',
                'vai_tro' => 'Thành viên',
                'id_chuc_vu' => $chucVuId,
                'trang_thai' => 'Hoạt động',
                'avatar' => 'https://images.unsplash.com/photo-1535713875002-d1d0cf377fde?auto=format&fit=crop&q=80&w=200',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }
}
