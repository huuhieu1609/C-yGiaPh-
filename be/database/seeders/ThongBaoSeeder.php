<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ThongBaoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $notifications = [
            [
                'tieu_de' => 'Thông báo họp họ tháng 5',
                'noi_dung' => 'Cuộc họp họ tháng 5 sẽ diễn ra tại nhà thờ họ Nguyễn.',
            ],
            [
                'tieu_de' => 'Cập nhật dữ liệu gia phả',
                'noi_dung' => 'Đã cập nhật danh sách thành viên mới trong gia phả.',
            ],
        ];

        foreach ($notifications as $notification) {
            DB::table('thong_baos')->updateOrInsert(
                ['tieu_de' => $notification['tieu_de']],
                array_merge($notification, [
                    'updated_at' => now(),
                    'created_at' => now(),
                ])
            );
        }
    }
}
