<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\NguoiDung;
use App\Models\DoiTac;

class DoiTacSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Tạo tài khoản người dùng
        $user = NguoiDung::updateOrCreate(
            ['email' => 'doitac@gmail.com'],
            [
                'ho_ten' => 'Đối Tác Demo',
                'mat_khau' => Hash::make('123456'),
                'so_dien_thoai' => '0999888777',
                'vai_tro' => 'Thành viên',
                'trang_thai' => 'Hoạt động',
            ]
        );

        // 2. Tạo gói đăng ký cho tài khoản này
        DoiTac::updateOrCreate(
            ['id_nguoi_dung' => $user->id],
            [
                'ten_goi' => 'Gói Gia Phả Trọn Đời',
                'so_tien' => 1000000,
                'ngay_bat_dau' => now(),
                'ngay_ket_thuc' => now()->addYears(10), // Giả sử gói dài hạn
                'trang_thai' => 'Hoạt động'
            ]
        );
    }
}