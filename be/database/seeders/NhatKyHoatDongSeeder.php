<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NhatKyHoatDongSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $userId = DB::table('nguoi_dungs')->where('email', 'admin@master.com')->value('id');

        if (!$userId) {
            return;
        }

        $activities = [
            ['hanh_dong' => 'Tạo tài khoản admin'],
            ['hanh_dong' => 'Cập nhật thông tin chi nhánh'],
            ['hanh_dong' => 'Phê duyệt đóng góp quỹ'],
        ];

        foreach ($activities as $activity) {
            DB::table('nhat_ky_hoat_dongs')->updateOrInsert(
                ['nguoi_dung_id' => $userId, 'hanh_dong' => $activity['hanh_dong']],
                [
                    'thoi_gian' => now(),
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );
        }
    }
}
