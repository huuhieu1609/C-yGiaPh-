<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class VoChongSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $husbandId = DB::table('thanh_viens')->where('ho_ten', 'Nguyễn Tân')->value('id');
        $wifeId = DB::table('thanh_viens')->where('ho_ten', 'Lê Thị Tí')->value('id');

        if (!$husbandId || !$wifeId) {
            return;
        }

        DB::table('vo_chongs')->updateOrInsert(
            ['chong_id' => $husbandId, 'vo_id' => $wifeId],
            [
                'ngay_cuoi' => '1965-12-10',
                'trang_thai' => 'Đang sống',
                'updated_at' => now(),
                'created_at' => now(),
            ]
        );
    }
}
