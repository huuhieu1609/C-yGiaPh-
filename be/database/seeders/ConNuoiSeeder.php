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
        $parentId = DB::table('thanh_viens')->where('ho_ten', 'Nguyen Van A')->value('id');
        $childId = DB::table('thanh_viens')->where('ho_ten', 'Nguyen Van C')->value('id');

        if (!$parentId || !$childId) {
            return;
        }

        DB::table('con_nuois')->updateOrInsert(
            ['cha_me_id' => $parentId, 'con_id' => $childId],
            [
                'ghi_chu' => 'Nguyen Van C là con nuôi của Nguyen Van A.',
                'updated_at' => now(),
                'created_at' => now(),
            ]
        );
    }
}
