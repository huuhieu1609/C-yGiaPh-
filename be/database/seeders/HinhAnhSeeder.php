<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class HinhAnhSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $memberId = DB::table('thanh_viens')->where('ho_ten', 'Nguyen Van C')->value('id');

        if (!$memberId) {
            return;
        }

        DB::table('hinh_anhs')->updateOrInsert(
            ['thanh_vien_id' => $memberId, 'duong_dan' => '...'],
            [
                'mo_ta' => 'Ảnh chân dung mẫu của Nguyen Van C.',
                'updated_at' => now(),
                'created_at' => now(),
            ]
        );
    }
}
