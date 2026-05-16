<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ChiNhanhSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Xóa dữ liệu cũ để tránh trùng lặp
        Schema::disableForeignKeyConstraints();
        DB::table('chi_nhanhs')->truncate();
        Schema::enableForeignKeyConstraints();

        // Tạo dữ liệu mẫu cho các chi nhánh
        $chiNhanhs = [
            [
                'ten_chi' => 'Chi Nhánh Dòng Họ Nguyễn',
                'mo_ta' => 'Chi nhánh đầu tiên của dòng họ Nguyễn, tập trung tại Hà Nội.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'ten_chi' => 'Chi Nhánh Dòng Họ Trần',
                'mo_ta' => 'Chi nhánh phía Nam, phát triển tại TP.HCM.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'ten_chi' => 'Chi Nhánh Dòng Họ Lê',
                'mo_ta' => 'Chi nhánh miền Trung, có lịch sử lâu đời.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'ten_chi' => 'Chi Nhánh Dòng Họ Phạm',
                'mo_ta' => 'Chi nhánh mới thành lập tại Đà Nẵng.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        // Chèn dữ liệu vào bảng
        DB::table('chi_nhanhs')->insert($chiNhanhs);
    }
}
