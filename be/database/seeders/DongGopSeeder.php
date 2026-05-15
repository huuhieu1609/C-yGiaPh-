<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DongGopSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $userId = DB::table('nguoi_dungs')->where('email', 'admin@master.com')->value('id')
            ?? DB::table('nguoi_dungs')->insertGetId([
                'ho_ten' => 'Admin Giả Lập',
                'email' => 'admin-fallback@master.com',
                'mat_khau' => bcrypt('123456'),
                'so_dien_thoai' => '0909009009',
                'vai_tro' => 'Admin',
                'trang_thai' => 'Hoạt động',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

        DB::table('dong_gops')->updateOrInsert(
            ['nguoi_dung_id' => $userId, 'noi_dung' => 'Đóng góp ban đầu cho quỹ dòng họ.'],
            [
                'trang_thai' => 'Đã duyệt',
                'updated_at' => now(),
                'created_at' => now(),
            ]
        );
    }
}
