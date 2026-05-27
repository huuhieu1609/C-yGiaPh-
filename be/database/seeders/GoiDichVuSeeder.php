<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\GoiDichVu;
use Carbon\Carbon;

class GoiDichVuSeeder extends Seeder
{
    public function run()
    {
        GoiDichVu::insert([
            [
                'ten_goi' => 'Gói Khởi Tạo',
                'gia_ca' => 100000,
                'thoi_han' => 12,
                'max_doi' => 3,
                'max_thanh_vien' => 50,
                'mo_ta' => 'Phù hợp cho các dòng họ nhỏ, quản lý cơ bản.',
                'trang_thai' => 'Hoạt động',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'ten_goi' => 'Gói Hưng Thịnh',
                'gia_ca' => 3000000,
                'thoi_han' => 12,
                'max_doi' => 10,
                'max_thanh_vien' => 500,
                'mo_ta' => 'Gói tiêu chuẩn cho dòng họ trung bình, đầy đủ tính năng.',
                'trang_thai' => 'Hoạt động',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'ten_goi' => 'Gói Trường Tồn',
                'gia_ca' => 5000000,
                'thoi_han' => 12,
                'max_doi' => 99,
                'max_thanh_vien' => 10000,
                'mo_ta' => 'Gói cao cấp cho dòng họ lớn, không giới hạn lưu trữ.',
                'trang_thai' => 'Hoạt động',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]
        ]);
    }
}
