<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ThanhVien;
use App\Models\ChiNhanh;
use Illuminate\Support\Facades\Schema;

class ThanhVienSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Lấy chi nhánh đầu tiên hoặc tạo mới nếu chưa có
        $chiNhanh = ChiNhanh::where('ten_chi', 'Chi Nhánh Dòng Họ Nguyễn')->first() 
            ?? ChiNhanh::first() 
            ?? ChiNhanh::create([
                'ten_chi' => 'Chi Nhánh Dòng Họ Nguyễn',
                'mo_ta' => 'Chi nhánh đầu tiên của dòng họ Nguyễn, tập trung tại Hà Nội.',
            ]);

        $chiNhanhId = $chiNhanh->id;

        // Clear existing members safely
        Schema::disableForeignKeyConstraints();
        ThanhVien::truncate();
        Schema::enableForeignKeyConstraints();

        // 1. Thế hệ 0 (Cụ tổ)
        $ongTan = ThanhVien::create([
            'ho_ten' => 'Nguyễn Tân',
            'ten_goi' => 'NT',
            'gioi_tinh' => 'Nam',
            'ngay_sinh' => '1945-02-12',
            'noi_sinh' => 'Hà Nội',
            'nghe_nghiep' => 'Nông dân',
            'chi_nhanh_id' => $chiNhanhId,
            'doi_thu' => 0,
            'loai_quan_he' => 'Chính',
            'trang_thai' => 'Còn sống',
            'tinh_trang_hon_nhan' => 'Đã kết hôn',
            'ghi_chu' => 'Cụ tổ chi nhánh mặc định.',
        ]);

        $baTi = ThanhVien::create([
            'ho_ten' => 'Lê Thị Tí',
            'ten_goi' => 'LT',
            'gioi_tinh' => 'Nữ',
            'ngay_sinh' => '1945-01-21',
            'noi_sinh' => 'Hà Nội',
            'nghe_nghiep' => 'Nội trợ',
            'chi_nhanh_id' => $chiNhanhId,
            'doi_thu' => 0,
            'loai_quan_he' => 'Vợ/Chồng',
            'trang_thai' => 'Còn sống',
            'tinh_trang_hon_nhan' => 'Đã kết hôn',
            'spouse_of_id' => $ongTan->id,
            'ghi_chu' => 'Vợ cụ tổ Lê Thị Tí.',
        ]);

        $ongTan->update(['spouse_of_id' => $baTi->id]);

        // 2. Thế hệ 1 (Con cái cụ tổ)
        $thang = ThanhVien::create([
            'ho_ten' => 'Nguyễn Đức Thắng',
            'ten_goi' => 'NĐT',
            'gioi_tinh' => 'Nam',
            'ngay_sinh' => '1970-05-14',
            'noi_sinh' => 'Hà Nội',
            'nghe_nghiep' => 'Kỹ sư',
            'chi_nhanh_id' => $chiNhanhId,
            'doi_thu' => 1,
            'cha_id' => $ongTan->id,
            'me_id' => $baTi->id,
            'loai_quan_he' => 'Chính',
            'trang_thai' => 'Còn sống',
            'tinh_trang_hon_nhan' => 'Đã kết hôn',
            'ghi_chu' => 'Con trai cả đời 1.',
        ]);

        $trung = ThanhVien::create([
            'ho_ten' => 'Nguyễn Văn Trung',
            'ten_goi' => 'NVT',
            'gioi_tinh' => 'Nam',
            'ngay_sinh' => '1975-10-20',
            'noi_sinh' => 'Hà Nội',
            'nghe_nghiep' => 'Kinh doanh',
            'chi_nhanh_id' => $chiNhanhId,
            'doi_thu' => 1,
            'cha_id' => $ongTan->id,
            'me_id' => $baTi->id,
            'loai_quan_he' => 'Chính',
            'trang_thai' => 'Còn sống',
            'tinh_trang_hon_nhan' => 'Đã kết hôn',
            'ghi_chu' => 'Con trai thứ đời 1.',
        ]);

        $huong = ThanhVien::create([
            'ho_ten' => 'Nguyễn Thị Hương',
            'ten_goi' => 'NTH',
            'gioi_tinh' => 'Nữ',
            'ngay_sinh' => '1978-08-15',
            'noi_sinh' => 'Hà Nội',
            'nghe_nghiep' => 'Giáo viên',
            'chi_nhanh_id' => $chiNhanhId,
            'doi_thu' => 1,
            'cha_id' => $ongTan->id,
            'me_id' => $baTi->id,
            'loai_quan_he' => 'Chính',
            'trang_thai' => 'Còn sống',
            'tinh_trang_hon_nhan' => 'Đã kết hôn',
            'ghi_chu' => 'Con gái út đời 1.',
        ]);

        // 3. Phối ngẫu thế hệ 1 (Con dâu/rể)
        $mai = ThanhVien::create([
            'ho_ten' => 'Phạm Thị Mai',
            'ten_goi' => 'PTM',
            'gioi_tinh' => 'Nữ',
            'ngay_sinh' => '1972-03-12',
            'noi_sinh' => 'Thái Bình',
            'nghe_nghiep' => 'Dược sĩ',
            'chi_nhanh_id' => $chiNhanhId,
            'doi_thu' => 1,
            'loai_quan_he' => 'Vợ/Chồng',
            'spouse_of_id' => $thang->id,
            'trang_thai' => 'Còn sống',
            'tinh_trang_hon_nhan' => 'Đã kết hôn',
            'ghi_chu' => 'Vợ anh Nguyễn Đức Thắng.',
        ]);
        $thang->update(['spouse_of_id' => $mai->id]);

        $trang = ThanhVien::create([
            'ho_ten' => 'Đỗ Thu Trang',
            'ten_goi' => 'ĐTT',
            'gioi_tinh' => 'Nữ',
            'ngay_sinh' => '1977-11-05',
            'noi_sinh' => 'Hải Phòng',
            'nghe_nghiep' => 'Kế toán',
            'chi_nhanh_id' => $chiNhanhId,
            'doi_thu' => 1,
            'loai_quan_he' => 'Vợ/Chồng',
            'spouse_of_id' => $trung->id,
            'trang_thai' => 'Còn sống',
            'tinh_trang_hon_nhan' => 'Đã kết hôn',
            'ghi_chu' => 'Vợ anh Nguyễn Văn Trung.',
        ]);
        $trung->update(['spouse_of_id' => $trang->id]);

        $tuan = ThanhVien::create([
            'ho_ten' => 'Lê Anh Tuấn',
            'ten_goi' => 'LAT',
            'gioi_tinh' => 'Nam',
            'ngay_sinh' => '1975-04-18',
            'noi_sinh' => 'Nam Định',
            'nghe_nghiep' => 'Bác sĩ',
            'chi_nhanh_id' => $chiNhanhId,
            'doi_thu' => 1,
            'loai_quan_he' => 'Vợ/Chồng',
            'spouse_of_id' => $huong->id,
            'trang_thai' => 'Còn sống',
            'tinh_trang_hon_nhan' => 'Đã kết hôn',
            'ghi_chu' => 'Chồng chị Nguyễn Thị Hương (con rể dòng họ).',
        ]);
        $huong->update(['spouse_of_id' => $tuan->id]);

        // 4. Thế hệ 2 (Cháu nội/ngoại)
        $nam = ThanhVien::create([
            'ho_ten' => 'Nguyễn Đức Nam',
            'ten_goi' => 'NĐN',
            'gioi_tinh' => 'Nam',
            'ngay_sinh' => '1998-09-05',
            'noi_sinh' => 'Hà Nội',
            'nghe_nghiep' => 'Lập trình viên',
            'chi_nhanh_id' => $chiNhanhId,
            'doi_thu' => 2,
            'cha_id' => $thang->id,
            'me_id' => $mai->id,
            'loai_quan_he' => 'Chính',
            'trang_thai' => 'Còn sống',
            'tinh_trang_hon_nhan' => 'Độc thân',
            'ghi_chu' => 'Cháu nội thứ nhất - con anh Thắng.',
        ]);

        $minh = ThanhVien::create([
            'ho_ten' => 'Nguyễn Văn Minh',
            'ten_goi' => 'NVM',
            'gioi_tinh' => 'Nam',
            'ngay_sinh' => '2002-06-25',
            'noi_sinh' => 'Hà Nội',
            'nghe_nghiep' => 'Sinh viên',
            'chi_nhanh_id' => $chiNhanhId,
            'doi_thu' => 2,
            'cha_id' => $trung->id,
            'me_id' => $trang->id,
            'loai_quan_he' => 'Chính',
            'trang_thai' => 'Còn sống',
            'tinh_trang_hon_nhan' => 'Độc thân',
            'ghi_chu' => 'Cháu nội thứ hai - con anh Trung.',
        ]);

        $ha = ThanhVien::create([
            'ho_ten' => 'Lê Thu Hà',
            'ten_goi' => 'LTH',
            'gioi_tinh' => 'Nữ',
            'ngay_sinh' => '2005-12-14',
            'noi_sinh' => 'Hà Nội',
            'nghe_nghiep' => 'Học sinh',
            'chi_nhanh_id' => $chiNhanhId,
            'doi_thu' => 2,
            'cha_id' => $tuan->id,
            'me_id' => $huong->id,
            'loai_quan_he' => 'Chính',
            'trang_thai' => 'Còn sống',
            'tinh_trang_hon_nhan' => 'Độc thân',
            'ghi_chu' => 'Cháu ngoại - con chị Hương.',
        ]);

        // 5. Con nuôi thế hệ 2
        $hoaiNam = ThanhVien::create([
            'ho_ten' => 'Nguyễn Hoài Nam',
            'ten_goi' => 'NHN',
            'gioi_tinh' => 'Nam',
            'ngay_sinh' => '2010-04-01',
            'noi_sinh' => 'Hà Nội',
            'nghe_nghiep' => 'Học sinh',
            'chi_nhanh_id' => $chiNhanhId,
            'doi_thu' => 2,
            'loai_quan_he' => 'Con nuôi',
            'trang_thai' => 'Còn sống',
            'tinh_trang_hon_nhan' => 'Độc thân',
            'ghi_chu' => 'Con nuôi của Nguyễn Văn Trung và Đỗ Thu Trang.',
        ]);
    }
}
