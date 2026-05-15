<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MoPhanSeeder extends Seeder
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

        DB::table('mo_phans')->updateOrInsert(
            ['thanh_vien_id' => $memberId],
            [
                'ten_mo' => 'Mộ phần gia đình Nguyễn',
                'dia_chi' => 'Nghĩa trang Văn Điển, Hà Nội',
                'kinh_do' => '21.0210',
                'vi_do' => '105.8740',
                'hinh_anh' => '...',
                'ghi_chu' => 'Mộ phần thân nhân thuộc dòng họ Nguyễn.',
                'updated_at' => now(),
                'created_at' => now(),
            ]
        );
    }
}
