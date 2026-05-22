<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ConNuoiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('con_nuois')->truncate();

        $chaId = DB::table('thanh_viens')->where('ho_ten', 'Nguyễn Văn Trung')->value('id');
        $meId = DB::table('thanh_viens')->where('ho_ten', 'Đỗ Thu Trang')->value('id');
        $conId = DB::table('thanh_viens')->where('ho_ten', 'Nguyễn Hoài Nam')->value('id');

        if ($conId) {
            if ($chaId) {
                DB::table('con_nuois')->insert([
                    'cha_me_id' => $chaId,
                    'con_id' => $conId,
                    'ghi_chu' => 'Nguyễn Hoài Nam là con nuôi của Nguyễn Văn Trung.',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
            if ($meId) {
                DB::table('con_nuois')->insert([
                    'cha_me_id' => $meId,
                    'con_id' => $conId,
                    'ghi_chu' => 'Nguyễn Hoài Nam là con nuôi của Đỗ Thu Trang.',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
