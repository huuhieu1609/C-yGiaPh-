<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\NguoiDung;
use App\Models\DoiTac;

class DoiTacSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Xóa bình luận cũ và thêm logic tạo tài khoản đối tác mẫu để test
        $userId = DB::table('nguoi_dungs')->insertGetId([
            'ho_ten' => 'Đối Tác Demo',
            'email' => 'doitac@master.com',
            'mat_khau' => Hash::make('123456'),
            'so_dien_thoai' => '0999888777',
            'vai_tro' => 'Thành viên',
            'trang_thai' => 'Hoạt động',
            'is_doi_tac' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('doi_tacs')->insert([
            'id_nguoi_dung' => $userId,
            'ten_goi' => 'Gói Đối Tác 1 Năm',
            'so_tien' => 1000000,
            'ngay_bat_dau' => now(),
            'ngay_ket_thuc' => now()->addYear(),
            'trang_thai' => 1,
        ]);
    }
}