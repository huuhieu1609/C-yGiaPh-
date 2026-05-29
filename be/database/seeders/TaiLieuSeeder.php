<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TaiLieuSeeder extends Seeder
{
    public function run(): void
    {
        $documents = [
            [
                'tieu_de' => 'Gia phả dòng họ Nguyễn',
                'file_path' => 'documents/gia_pha_ho_nguyen.pdf',
                'mo_ta' => 'Tài liệu gia phả đầy đủ của dòng họ Nguyễn.',
            ],
            [
                'tieu_de' => 'Quy định lễ nghi họp họ',
                'file_path' => 'documents/quy_dinh_le_nghi.pdf',
                'mo_ta' => 'Quy định nội dung và lễ nghi khi họp họ.',
            ],
        ];

        foreach ($documents as $document) {
            DB::table('tai_lieus')->updateOrInsert(
                ['tieu_de' => $document['tieu_de']],
                array_merge($document, [
                    'updated_at' => now(),
                    'created_at' => now(),
                ])
            );
        }
    }
}
