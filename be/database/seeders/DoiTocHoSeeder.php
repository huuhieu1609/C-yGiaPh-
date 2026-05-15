<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DoiTocHoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $families = [
            ['so_doi' => 1, 'ten_doi' => 'Dòng Họ Nguyễn', 'mo_ta' => 'Dòng họ Nguyễn với truyền thống văn hóa lâu đời.'],
            ['so_doi' => 2, 'ten_doi' => 'Dòng Họ Trần', 'mo_ta' => 'Dòng họ Trần tại TP.HCM.'],
            ['so_doi' => 3, 'ten_doi' => 'Dòng Họ Lê', 'mo_ta' => 'Dòng họ Lê giữ các giá trị truyền thống miền Trung.'],
        ];

        foreach ($families as $family) {
            DB::table('doi_toc_hos')->updateOrInsert(
                ['ten_doi' => $family['ten_doi']],
                array_merge($family, [
                    'updated_at' => now(),
                    'created_at' => now(),
                ])
            );
        }
    }
}
