<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ThanhVienSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $chiNhanhId = DB::table('chi_nhanhs')->value('id') ?? DB::table('chi_nhanhs')->insertGetId([
            'ten_chi' => 'Chi Nhánh Mặc Định',
            'mo_ta' => 'Chi nhánh mặc định để tạo dữ liệu thành viên mẫu.',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Clear existing members (temporarily disable foreign key checks to allow truncate)
        \Illuminate\Support\Facades\Schema::disableForeignKeyConstraints();
        DB::table('thanh_viens')->truncate();
        \Illuminate\Support\Facades\Schema::enableForeignKeyConstraints();

        // 1. Generation 0 (Grandparents)
        $ongTanId = DB::table('thanh_viens')->insertGetId([
            'ho_ten' => 'Nguyễn Tân',
            'ten_goi' => 'NT',
            'gioi_tinh' => 'Nam',
            'ngay_sinh' => '1945-02-12',
            'noi_sinh' => 'Hà Nội',
            'nghe_nghiep' => 'Nông dân',
            'chi_nhanh_id' => $chiNhanhId,
            'doi_thu' => 0,
            'trang_thai' => 'Còn sống',
            'ghi_chu' => 'Cụ tổ chi nhánh mặc định.',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $baTiId = DB::table('thanh_viens')->insertGetId([
            'ho_ten' => 'Lê Thị Tí',
            'ten_goi' => 'LT',
            'gioi_tinh' => 'Nữ',
            'ngay_sinh' => '1945-01-21',
            'noi_sinh' => 'Hà Nội',
            'nghe_nghiep' => 'Nội trợ',
            'chi_nhanh_id' => $chiNhanhId,
            'doi_thu' => 0,
            'trang_thai' => 'Còn sống',
            'spouse_of_id' => $ongTanId,
            'ghi_chu' => 'Vợ cụ tổ Lê Thị Tí.',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Connect Husband to Wife
        DB::table('thanh_viens')->where('id', $ongTanId)->update(['spouse_of_id' => $baTiId]);

        // 2. Generation 1 (Children of G0)
        $thangId = DB::table('thanh_viens')->insertGetId([
            'ho_ten' => 'Nguyễn Đức Thắng',
            'ten_goi' => 'NĐT',
            'gioi_tinh' => 'Nam',
            'ngay_sinh' => '1970-05-14',
            'noi_sinh' => 'Hà Nội',
            'nghe_nghiep' => 'Kỹ sư',
            'chi_nhanh_id' => $chiNhanhId,
            'doi_thu' => 1,
            'cha_id' => $ongTanId,
            'me_id' => $baTiId,
            'trang_thai' => 'Còn sống',
            'email' => 'member1@master.com',
            'ghi_chu' => 'Con trai cả đời 1.',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $trungId = DB::table('thanh_viens')->insertGetId([
            'ho_ten' => 'Nguyễn Văn Trung',
            'ten_goi' => 'NVT',
            'gioi_tinh' => 'Nam',
            'ngay_sinh' => '1975-10-20',
            'noi_sinh' => 'Hà Nội',
            'nghe_nghiep' => 'Kinh doanh',
            'chi_nhanh_id' => $chiNhanhId,
            'doi_thu' => 1,
            'cha_id' => $ongTanId,
            'me_id' => $baTiId,
            'trang_thai' => 'Còn sống',
            'ghi_chu' => 'Con trai thứ đời 1.',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $huongId = DB::table('thanh_viens')->insertGetId([
            'ho_ten' => 'Nguyễn Thị Hương',
            'ten_goi' => 'NTH',
            'gioi_tinh' => 'Nữ',
            'ngay_sinh' => '1978-08-15',
            'noi_sinh' => 'Hà Nội',
            'nghe_nghiep' => 'Giáo viên',
            'chi_nhanh_id' => $chiNhanhId,
            'doi_thu' => 1,
            'cha_id' => $ongTanId,
            'me_id' => $baTiId,
            'trang_thai' => 'Còn sống',
            'ghi_chu' => 'Con gái út đời 1.',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 3. Spouses of G1
        $maiId = DB::table('thanh_viens')->insertGetId([
            'ho_ten' => 'Phạm Thị Mai',
            'ten_goi' => 'PTM',
            'gioi_tinh' => 'Nữ',
            'ngay_sinh' => '1972-03-12',
            'noi_sinh' => 'Thái Bình',
            'nghe_nghiep' => 'Dược sĩ',
            'chi_nhanh_id' => $chiNhanhId,
            'doi_thu' => 1,
            'spouse_of_id' => $thangId,
            'trang_thai' => 'Còn sống',
            'email' => 'member2@master.com',
            'ghi_chu' => 'Vợ anh Nguyễn Đức Thắng.',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('thanh_viens')->where('id', $thangId)->update(['spouse_of_id' => $maiId]);

        $trangId = DB::table('thanh_viens')->insertGetId([
            'ho_ten' => 'Đỗ Thu Trang',
            'ten_goi' => 'ĐTT',
            'gioi_tinh' => 'Nữ',
            'ngay_sinh' => '1977-11-05',
            'noi_sinh' => 'Hải Phòng',
            'nghe_nghiep' => 'Kế toán',
            'chi_nhanh_id' => $chiNhanhId,
            'doi_thu' => 1,
            'spouse_of_id' => $trungId,
            'trang_thai' => 'Còn sống',
            'ghi_chu' => 'Vợ anh Nguyễn Văn Trung.',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('thanh_viens')->where('id', $trungId)->update(['spouse_of_id' => $trangId]);

        $tuanId = DB::table('thanh_viens')->insertGetId([
            'ho_ten' => 'Lê Anh Tuấn',
            'ten_goi' => 'LAT',
            'gioi_tinh' => 'Nam',
            'ngay_sinh' => '1975-04-18',
            'noi_sinh' => 'Nam Định',
            'nghe_nghiep' => 'Bác sĩ',
            'chi_nhanh_id' => $chiNhanhId,
            'doi_thu' => 1,
            'spouse_of_id' => $huongId,
            'trang_thai' => 'Còn sống',
            'ghi_chu' => 'Chồng chị Nguyễn Thị Hương (con rể dòng họ).',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('thanh_viens')->where('id', $huongId)->update(['spouse_of_id' => $tuanId]);

        // 4. Generation 2 (Grandchildren of G0)
        $namId = DB::table('thanh_viens')->insertGetId([
            'ho_ten' => 'Nguyễn Đức Nam',
            'ten_goi' => 'NĐN',
            'gioi_tinh' => 'Nam',
            'ngay_sinh' => '1998-09-05',
            'noi_sinh' => 'Hà Nội',
            'nghe_nghiep' => 'Lập trình viên',
            'chi_nhanh_id' => $chiNhanhId,
            'doi_thu' => 2,
            'cha_id' => $thangId,
            'me_id' => $maiId,
            'trang_thai' => 'Còn sống',
            'ghi_chu' => 'Cháu nội thứ nhất - con anh Thắng.',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $minhId = DB::table('thanh_viens')->insertGetId([
            'ho_ten' => 'Nguyễn Văn Minh',
            'ten_goi' => 'NVM',
            'gioi_tinh' => 'Nam',
            'ngay_sinh' => '2002-06-25',
            'noi_sinh' => 'Hà Nội',
            'nghe_nghiep' => 'Sinh viên',
            'chi_nhanh_id' => $chiNhanhId,
            'doi_thu' => 2,
            'cha_id' => $trungId,
            'me_id' => $trangId,
            'trang_thai' => 'Còn sống',
            'ghi_chu' => 'Cháu nội thứ hai - con anh Trung.',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $haId = DB::table('thanh_viens')->insertGetId([
            'ho_ten' => 'Lê Thu Hà',
            'ten_goi' => 'LTH',
            'gioi_tinh' => 'Nữ',
            'ngay_sinh' => '2005-12-14',
            'noi_sinh' => 'Hà Nội',
            'nghe_nghiep' => 'Học sinh',
            'chi_nhanh_id' => $chiNhanhId,
            'doi_thu' => 2,
            'cha_id' => $tuanId,
            'me_id' => $huongId,
            'trang_thai' => 'Còn sống',
            'ghi_chu' => 'Cháu ngoại - con chị Hương.',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 5. Adopted child member (Generation 2)
        $hoaiNamId = DB::table('thanh_viens')->insertGetId([
            'ho_ten' => 'Nguyễn Hoài Nam',
            'ten_goi' => 'NHN',
            'gioi_tinh' => 'Nam',
            'ngay_sinh' => '2010-04-01',
            'noi_sinh' => 'Hà Nội',
            'nghe_nghiep' => 'Học sinh',
            'chi_nhanh_id' => $chiNhanhId,
            'doi_thu' => 2,
            'trang_thai' => 'Còn sống',
            'ghi_chu' => 'Con nuôi của Nguyễn Văn Trung và Đỗ Thu Trang.',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
