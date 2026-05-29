<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ThamGiaSuKienSeeder extends Seeder
{
    public function run(): void
    {
        // Lấy sự kiện và thành viên thực tế đã được seed từ trước
        $eventId = DB::table('su_kiens')->where('tieu_de', 'like', '%Đại Lễ Giỗ Tổ%')->value('id');
        $memberId = DB::table('thanh_viens')->where('ho_ten', 'Nguyễn Đức Thắng')->value('id');

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
