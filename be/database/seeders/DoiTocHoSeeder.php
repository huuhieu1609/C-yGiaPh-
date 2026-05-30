<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DoiTocHoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Vô hiệu hóa ràng buộc khóa ngoại để truncate bảng an toàn
        Schema::disableForeignKeyConstraints();
        DB::table('doi_toc_hos')->truncate();
        Schema::enableForeignKeyConstraints();

        // 4 Chi nhánh tương ứng với 4 dòng họ chính
        $branches = [
            1 => 'Họ Nguyễn',
            2 => 'Họ Trần',
            3 => 'Họ Lê',
            4 => 'Họ Phạm'
        ];

        // Tên các đời từ 1 đến 10
        $generationNames = [
            1 => 'Thủy Tổ Khai Sáng',
            2 => 'Viễn Tổ Trung Hưng',
            3 => 'Tằng Tổ Phát Triển',
            4 => 'Cao Tổ Kiến Thiết',
            5 => 'Tổ Khảo Kế Thừa',
            6 => 'Thế Hệ Tiếp Bước',
            7 => 'Thế Hệ Đổi Mới',
            8 => 'Thế Hệ Hội Nhập',
            9 => 'Thế Hệ Tinh Anh',
            10 => 'Thế Hệ Tương Lai',
        ];

        // Mô tả ý nghĩa của các đời
        $generationDescriptions = [
            1 => 'Thế hệ đầu tiên khai sơn lập địa, đặt nền móng cho dòng tộc.',
            2 => 'Thế hệ thứ hai tiếp nối chí hướng mở mang gia nghiệp.',
            3 => 'Thế hệ thứ ba củng cố gia đạo và phát triển kinh tế.',
            4 => 'Thế hệ thứ tư chấn hưng văn hóa, khuyến khích học hành.',
            5 => 'Thế hệ thứ năm kế thừa di sản văn hóa, giữ gìn giềng mối gia đình.',
            6 => 'Thế hệ tiếp bước gìn giữ nề nếp gia phong, gia tăng uy tín dòng tộc.',
            7 => 'Thế hệ thời kỳ đổi mới, phát triển kinh tế xã hội hiện đại.',
            8 => 'Thế hệ hội nhập quốc tế, học hỏi tinh hoa nhân loại.',
            9 => 'Thế hệ của những tinh hoa ưu tú, mang vinh quang về cho dòng họ.',
            10 => 'Thế hệ trẻ tương lai, niềm hy vọng mới của gia tộc.',
        ];

        // Gieo dữ liệu cho từng Chi Nhánh cụ thể
        foreach ($branches as $branchId => $branchName) {
            for ($i = 1; $i <= 10; $i++) {
                DB::table('doi_toc_hos')->insert([
                    'chi_nhanh_id' => $branchId,
                    'so_doi' => $i,
                    'ten_doi' => 'Đời thứ ' . $i . ' (' . $generationNames[$i] . ' - ' . $branchName . ')',
                    'mo_ta' => $generationDescriptions[$i] . ' Thuộc ' . $branchName . '.',
                    'trang_thai' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        // Gieo dữ liệu chung/mặc định (không gán chi_nhanh_id) làm dữ liệu gốc
        for ($i = 1; $i <= 10; $i++) {
            DB::table('doi_toc_hos')->insert([
                'chi_nhanh_id' => null,
                'so_doi' => $i,
                'ten_doi' => 'Đời thứ ' . $i . ' (' . $generationNames[$i] . ')',
                'mo_ta' => $generationDescriptions[$i],
                'trang_thai' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}

