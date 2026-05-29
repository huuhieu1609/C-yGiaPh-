<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DoiTocHoSeeder extends Seeder
{
    public function run(): void
    {
        $families = [
            [
                'so_doi' => 12,
                'ten_doi' => 'Dòng Họ Nguyễn Đức',
                'mo_ta' => 'Dòng họ Nguyễn Đức khởi nguồn từ đất tổ Thạch Thất - Hà Nội, có truyền thống hiếu học và khoa bảng qua nhiều thế hệ.'
            ],
            [
                'so_doi' => 9,
                'ten_doi' => 'Dòng Họ Trần Khắc',
                'mo_ta' => 'Chi họ Trần Khắc định cư lâu đời tại Nam Định, lưu giữ nhiều tư liệu cổ về văn hóa dòng tộc miền Bắc.'
            ],
            [
                'so_doi' => 6,
                'ten_doi' => 'Dòng Họ Lê Vũ',
                'mo_ta' => 'Dòng họ Lê Vũ tại Quảng Nam, gìn giữ các giá trị truyền thống võ thuật và văn hóa đặc trưng miền Trung.'
            ],
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
