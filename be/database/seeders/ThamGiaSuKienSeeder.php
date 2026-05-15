<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ThamGiaSuKienSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $eventId = DB::table('su_kiens')->where('tieu_de', 'Giỗ tổ dòng họ Nguyễn')->value('id');
        $memberId = DB::table('thanh_viens')->where('ho_ten', 'Nguyen Van C')->value('id');

        if (!$eventId || !$memberId) {
            return;
        }

        DB::table('tham_gia_su_kiens')->updateOrInsert(
            ['su_kien_id' => $eventId, 'thanh_vien_id' => $memberId],
            [
                'updated_at' => now(),
                'created_at' => now(),
            ]
        );
    }
}
