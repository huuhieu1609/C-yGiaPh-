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
        $userId = DB::table('nguoi_dungs')->where('email', 'minhvy@master.com')->value('id')
            ?? DB::table('nguoi_dungs')->where('email', 'admin@master.com')->value('id');

        if (!$userId) {
            $userId = DB::table('nguoi_dungs')->insertGetId([
                'ho_ten' => 'Nguyễn Minh Vy',
                'email' => 'minhvy@master.com',
                'mat_khau' => bcrypt('member123'),
                'so_dien_thoai' => '0966555444',
                'vai_tro' => 'Thành viên',
                'trang_thai' => 'Hoạt động',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        DB::table('dong_gops')->updateOrInsert(
            [
                'nguoi_dung_id' => $userId,
                'noi_dung' => 'Ủng hộ quỹ khuyến học dòng họ phát thưởng cho các cháu đạt học sinh giỏi năm học 2025-2026.'
            ],
            [
                'trang_thai' => 'Đã duyệt',
                'updated_at' => now(),
                'created_at' => now(),
            ]
        );

        DB::table('dong_gops')->updateOrInsert(
            [
                'nguoi_dung_id' => $userId,
                'noi_dung' => 'Đóng góp kinh phí mua sắm lễ vật và đồ dùng chuẩn bị cho ngày giỗ tổ.'
            ],
            [
                'trang_thai' => 'Đã duyệt',
                'updated_at' => now(),
                'created_at' => now(),
            ]
        );
    }
}
