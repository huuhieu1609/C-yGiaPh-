<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NhaThoHoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $shrines = [
            [
                'ten_nha_tho' => 'Nhà thờ họ Nguyễn',
                'dia_chi' => 'Xã Tân Hội, Hà Nội',
                'hinh_anh' => '...',
                'mo_ta' => 'Nhà thờ họ Nguyễn dùng để họp mặt và tổ chức giỗ chạp.',
            ],
            [
                'ten_nha_tho' => 'Nhà thờ họ Trần',
                'dia_chi' => 'Quận 1, TP.HCM',
                'hinh_anh' => '...',
                'mo_ta' => 'Nhà thờ họ Trần phục vụ nghi lễ và lưu giữ gia phả.',
            ],
        ];

        foreach ($shrines as $shrine) {
            DB::table('nha_tho_hos')->updateOrInsert(
                ['ten_nha_tho' => $shrine['ten_nha_tho']],
                array_merge($shrine, [
                    'updated_at' => now(),
                    'created_at' => now(),
                ])
            );
        }
    }
}
