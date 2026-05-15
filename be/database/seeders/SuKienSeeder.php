<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SuKienSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $events = [
            [
                'tieu_de' => 'Giỗ tổ dòng họ Nguyễn',
                'noi_dung' => 'Tổ chức giỗ tổ thường niên cho dòng họ Nguyễn.',
                'ngay_to_chuc' => now()->addDays(30)->toDateString(),
                'dia_diem' => '120 Yên Lãng, Hà Nội',
                'loai' => 'Giỗ tổ',
            ],
            [
                'tieu_de' => 'Họp họ đầu năm',
                'noi_dung' => 'Họp họ bàn kế hoạch năm mới.',
                'ngay_to_chuc' => now()->addDays(10)->toDateString(),
                'dia_diem' => '120 Yên Lãng, Hà Nội',
                'loai' => 'Họp họ',
            ],
        ];

        foreach ($events as $event) {
            DB::table('su_kiens')->updateOrInsert(
                ['tieu_de' => $event['tieu_de']],
                array_merge($event, [
                    'updated_at' => now(),
                    'created_at' => now(),
                ])
            );
        }
    }
}
