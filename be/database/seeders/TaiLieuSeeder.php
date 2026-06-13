<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TaiLieuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $chiNhanhNguyen = DB::table('chi_nhanhs')->where('ten_chi', 'like', '%Nguyễn%')->first();
        $chiNhanhTran = DB::table('chi_nhanhs')->where('ten_chi', 'like', '%Trần%')->first();
        $chiNhanhLe = DB::table('chi_nhanhs')->where('ten_chi', 'like', '%Lê%')->first();

        $documents = [
            [
                'tieu_de' => 'Gia phả toàn thư dòng họ Nguyễn - Quyển 1',
                'file_path' => 'https://www.w3.org/WAI/ER/tests/xhtml/testfiles/resources/pdf/dummy.pdf',
                'mo_ta' => 'Sách phả hệ ghi chép lịch sử gốc tổ, nguồn cội 5 đời đầu tiên của chi nhánh họ Nguyễn Hà Nội.',
                'chi_nhanh_id' => $chiNhanhNguyen ? $chiNhanhNguyen->id : null,
            ],
            [
                'tieu_de' => 'Quy ước gia tộc & Lễ nghi truyền thống họ Nguyễn',
                'file_path' => 'https://www.w3.org/WAI/ER/tests/xhtml/testfiles/resources/pdf/dummy.pdf',
                'mo_ta' => 'Tài liệu quy ước gia tộc, quy định về ma chay, cưới hỏi, tế tự và đóng góp xây dựng quỹ dòng họ.',
                'chi_nhanh_id' => $chiNhanhNguyen ? $chiNhanhNguyen->id : null,
            ],
            [
                'tieu_de' => 'Lịch sử di dời mộ tổ dòng họ Trần (TP.HCM)',
                'file_path' => 'https://www.w3.org/WAI/ER/tests/xhtml/testfiles/resources/pdf/dummy.pdf',
                'mo_ta' => 'Hồ sơ ghi chép đầy đủ quá trình cất bốc, quy tập mộ phần các vị cao tổ dòng họ Trần về nghĩa trang gia tộc quận 9.',
                'chi_nhanh_id' => $chiNhanhTran ? $chiNhanhTran->id : null,
            ],
            [
                'tieu_de' => 'Gia huấn ca họ Lê - Lời răn dạy thế hệ mai sau',
                'file_path' => 'https://www.w3.org/WAI/ER/tests/xhtml/testfiles/resources/pdf/dummy.pdf',
                'mo_ta' => 'Văn bản gia huấn truyền đời của họ Lê chi nhánh miền Trung, răn dạy con cháu đức hạnh và học tập.',
                'chi_nhanh_id' => $chiNhanhLe ? $chiNhanhLe->id : null,
            ],
            [
                'tieu_de' => 'Quy chế xét thưởng khuyến học năm 2026',
                'file_path' => 'https://www.w3.org/WAI/ER/tests/xhtml/testfiles/resources/pdf/dummy.pdf',
                'mo_ta' => 'Quy chế và mức chi thưởng động viên cho học sinh giỏi, đạt giải quốc gia và đỗ đại học của dòng họ toàn quốc.',
                'chi_nhanh_id' => null, // Global
            ],
            [
                'tieu_de' => 'Hướng dẫn nghi thức cúng giỗ dòng họ',
                'file_path' => 'https://www.w3.org/WAI/ER/tests/xhtml/testfiles/resources/pdf/dummy.pdf',
                'mo_ta' => 'Tài liệu hướng dẫn cách bày trí bàn tế, soạn sớ văn khấn tế lễ giỗ chạp, giỗ tổ dòng tộc chuẩn mực.',
                'chi_nhanh_id' => null, // Global
            ]
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
