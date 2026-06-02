<?php

namespace Database\Seeders;

use App\Models\ChiNhanh;
use App\Models\DoiTac;
use App\Models\DoiTocHo;
use App\Models\NguoiDung;
use App\Models\ThanhVien;
use App\Models\VoChong;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class OtherPartnersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // ==========================================
        // SEED PARTNER 2: TRẦN HOÀI NAM (doitac2@master.com)
        // ==========================================

        // Avoid duplicate partner 2
        $existingUser2 = NguoiDung::withTrashed()->where('email', 'doitac2@master.com')->first();
        if ($existingUser2) {
            $oldBranchIds2 = ChiNhanh::where('id_nguoi_dung', $existingUser2->id)->pluck('id');
            ThanhVien::whereIn('chi_nhanh_id', $oldBranchIds2)->delete();
            DoiTocHo::whereIn('chi_nhanh_id', $oldBranchIds2)->delete();
            ChiNhanh::where('id_nguoi_dung', $existingUser2->id)->delete();
            DoiTac::where('id_nguoi_dung', $existingUser2->id)->delete();
            $existingUser2->forceDelete();
        }

        $userId2 = DB::table('nguoi_dungs')->insertGetId([
            'ho_ten' => 'Trần Hoài Nam',
            'email' => 'doitac2@master.com',
            'mat_khau' => Hash::make('123456'),
            'so_dien_thoai' => '0988777666',
            'vai_tro' => 'Thành viên',
            'trang_thai' => 'Hoạt động',
            'is_doi_tac' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('doi_tacs')->insert([
            'id_nguoi_dung'  => $userId2,
            'ten_goi'        => 'Gói Hưng Thịnh',
            'so_tien'        => 3000000,
            'id_goi_dich_vu' => null,
            'features'       => 'tao_cay_gia_pha,them_thanh_vien,sua_xoa_thanh_vien,quan_ly_vo_chong,quan_ly_con_nuoi,xuat_pdf,phe_duyet_de_xuat,tu_dong_phe_duyet,quan_ly_chi_nhanh,phan_quyen_thanh_vien,nhat_ky_hoat_dong,quan_ly_su_kien,dang_ky_su_kien,tuong_niem,nhac_gio_tu,quan_ly_tai_lieu,upload_hinh_anh,album_gia_dinh',
            'max_doi'        => 99,
            'max_thanh_vien' => 10000,
            'ngay_bat_dau'   => now(),
            'ngay_ket_thuc'  => now()->addYears(3),
            'trang_thai'     => 'APPROVED',
            'created_at'     => now(),
            'updated_at'     => now(),
        ]);

        $chiNhanh2 = ChiNhanh::create([
            'ten_chi' => 'Dòng Họ Trần Khắc - Nam Định',
            'mo_ta' => 'Dòng họ Trần Khắc lâu đời tại đất học Nam Định, có truyền thống võ học và kinh doanh.',
            'id_nguoi_dung' => $userId2,
        ]);

        $cnId2 = $chiNhanh2->id;

        // Generations for branch 2
        DoiTocHo::create(['so_doi' => 1, 'ten_doi' => 'Khai Sáng Chi Tộc', 'mo_ta' => 'Thế hệ Thủy Tổ lập dòng họ Trần Khắc', 'chi_nhanh_id' => $cnId2]);
        DoiTocHo::create(['so_doi' => 2, 'ten_doi' => 'Thế Hệ Trấn Biên', 'mo_ta' => 'Giai đoạn kháng chiến chống thực dân', 'chi_nhanh_id' => $cnId2]);
        DoiTocHo::create(['so_doi' => 3, 'ten_doi' => 'Thế Hệ Phát Triển', 'mo_ta' => 'Định hình thương hiệu gia tộc', 'chi_nhanh_id' => $cnId2]);
        DoiTocHo::create(['so_doi' => 4, 'ten_doi' => 'Thế Hệ Vươn Xa', 'mo_ta' => 'Con cháu học tập và làm việc đa ngành', 'chi_nhanh_id' => $cnId2]);
        DoiTocHo::create(['so_doi' => 5, 'ten_doi' => 'Thế Hệ Tinh Anh', 'mo_ta' => 'Mầm non tương lai dòng họ', 'chi_nhanh_id' => $cnId2]);
        DoiTocHo::create(['so_doi' => 6, 'ten_doi' => 'Thế Hệ Tiếp Nối', 'mo_ta' => 'Thế hệ phát huy giá trị truyền thống', 'chi_nhanh_id' => $cnId2]);
        DoiTocHo::create(['so_doi' => 7, 'ten_doi' => 'Thế Hệ Kiến Tạo', 'mo_ta' => 'Thế hệ kiến tạo tương lai phát triển', 'chi_nhanh_id' => $cnId2]);
        DoiTocHo::create(['so_doi' => 8, 'ten_doi' => 'Thế Hệ Vững Bền', 'mo_ta' => 'Thế hệ phát triển vững mạnh toàn diện', 'chi_nhanh_id' => $cnId2]);
        DoiTocHo::create(['so_doi' => 9, 'ten_doi' => 'Thế Hệ Tinh Anh', 'mo_ta' => 'Thế hệ tinh hoa vươn tầm cao mới', 'chi_nhanh_id' => $cnId2]);
        DoiTocHo::create(['so_doi' => 10, 'ten_doi' => 'Thế Hệ Rực Rỡ', 'mo_ta' => 'Thế hệ tương lai tỏa sáng rực rỡ', 'chi_nhanh_id' => $cnId2]);

        // Members for Branch 2
        // ĐỜI 1: Cụ Long (2 vợ)
        $cuLong = ThanhVien::create([
            'chi_nhanh_id' => $cnId2,
            'ho_ten' => 'Trần Khắc Long',
            'ten_goi' => 'Cụ Long',
            'gioi_tinh' => 'Nam',
            'ngay_sinh' => '1920-03-10',
            'ngay_mat' => '1999-08-12',
            'noi_sinh' => 'Nam Định',
            'nghe_nghiep' => 'Lương y',
            'doi_thu' => 1,
            'loai_quan_he' => 'Chính',
            'trang_thai' => 'Đã mất',
            'ghi_chu' => 'Thủy Tổ dòng họ Trần Khắc, bốc thuốc nam cứu người nổi tiếng khắp vùng Giao Thủy.',
            'avatar' => 'https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?auto=format&fit=crop&q=80&w=200',
        ]);

        $cuVan = ThanhVien::create([
            'chi_nhanh_id' => $cnId2,
            'ho_ten' => 'Lê Thị Vân',
            'ten_goi' => 'Cụ Vân',
            'gioi_tinh' => 'Nữ',
            'ngay_sinh' => '1922-05-15',
            'ngay_mat' => '2005-04-20',
            'noi_sinh' => 'Thái Bình',
            'nghe_nghiep' => 'Làm nông',
            'doi_thu' => 1,
            'loai_quan_he' => 'Vợ/Chồng',
            'spouse_of_id' => $cuLong->id,
            'trang_thai' => 'Đã mất',
            'ghi_chu' => 'Vợ cả của cụ Long, tần tảo dệt vải phụ chồng chăm lo gia đạo.',
            'avatar' => 'https://images.unsplash.com/photo-1544005313-94ddf0286df2?auto=format&fit=crop&q=80&w=200',
        ]);

        VoChong::create([
            'chong_id' => $cuLong->id,
            'vo_id' => $cuVan->id,
            'ngay_cuoi' => '1941-01-20',
            'trang_thai' => 'Qua đời',
        ]);

        $cuMai = ThanhVien::create([
            'chi_nhanh_id' => $cnId2,
            'ho_ten' => 'Nguyễn Thị Mai',
            'ten_goi' => 'Cụ Mai',
            'gioi_tinh' => 'Nữ',
            'ngay_sinh' => '1928-10-12',
            'ngay_mat' => '2010-12-05',
            'noi_sinh' => 'Ninh Bình',
            'nghe_nghiep' => 'Dệt lụa',
            'doi_thu' => 1,
            'loai_quan_he' => 'Vợ/Chồng',
            'spouse_of_id' => $cuLong->id,
            'trang_thai' => 'Đã mất',
            'ghi_chu' => 'Vợ thứ của cụ Long, tính tình nhu mì hiền hậu.',
            'avatar' => 'https://images.unsplash.com/photo-1508214751196-bcfd4ca60f91?auto=format&fit=crop&q=80&w=200',
        ]);

        VoChong::create([
            'chong_id' => $cuLong->id,
            'vo_id' => $cuMai->id,
            'ngay_cuoi' => '1952-11-15',
            'trang_thai' => 'Qua đời',
        ]);

        // ĐỜI 2: Con cụ Long & cụ Vân (Vợ 1)
        // 1. Trần Khắc Tiến (Có 2 vợ)
        $ongTien2 = ThanhVien::create([
            'chi_nhanh_id' => $cnId2,
            'ho_ten' => 'Trần Khắc Tiến',
            'ten_goi' => 'Ông Tiến',
            'gioi_tinh' => 'Nam',
            'ngay_sinh' => '1944-04-10',
            'cha_id' => $cuLong->id,
            'me_id' => $cuVan->id,
            'noi_sinh' => 'Nam Định',
            'nghe_nghiep' => 'Nhà báo',
            'doi_thu' => 2,
            'loai_quan_he' => 'Chính',
            'trang_thai' => 'Còn sống',
            'ghi_chu' => 'Nguyên tổng biên tập báo Nam Định, uyên bác nho nhã.',
            'avatar' => 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?auto=format&fit=crop&q=80&w=200',
        ]);

        $baHong = ThanhVien::create([
            'chi_nhanh_id' => $cnId2,
            'ho_ten' => 'Phạm Thị Hồng',
            'ten_goi' => 'Bà Hồng',
            'gioi_tinh' => 'Nữ',
            'ngay_sinh' => '1948-09-12',
            'noi_sinh' => 'Nam Định',
            'nghe_nghiep' => 'Bác sĩ',
            'doi_thu' => 2,
            'loai_quan_he' => 'Vợ/Chồng',
            'spouse_of_id' => $ongTien2->id,
            'trang_thai' => 'Còn sống',
            'ghi_chu' => 'Vợ cả ông Tiến, bác sĩ đa khoa nghỉ hưu.',
            'avatar' => 'https://images.unsplash.com/photo-1494790108377-be9c29b29330?auto=format&fit=crop&q=80&w=200',
        ]);

        VoChong::create([
            'chong_id' => $ongTien2->id,
            'vo_id' => $baHong->id,
            'ngay_cuoi' => '1968-05-15',
            'trang_thai' => 'Đang sống',
        ]);

        $baTuyet2 = ThanhVien::create([
            'chi_nhanh_id' => $cnId2,
            'ho_ten' => 'Vũ Thị Tuyết',
            'ten_goi' => 'Bà Tuyết',
            'gioi_tinh' => 'Nữ',
            'ngay_sinh' => '1960-03-25',
            'noi_sinh' => 'Thái Bình',
            'nghe_nghiep' => 'Thủ quỹ',
            'doi_thu' => 2,
            'loai_quan_he' => 'Vợ/Chồng',
            'spouse_of_id' => $ongTien2->id,
            'trang_thai' => 'Còn sống',
            'ghi_chu' => 'Vợ thứ hai của ông Tiến.',
            'avatar' => 'https://images.unsplash.com/photo-1544005313-94ddf0286df2?auto=format&fit=crop&q=80&w=200',
        ]);

        VoChong::create([
            'chong_id' => $ongTien2->id,
            'vo_id' => $baTuyet2->id,
            'ngay_cuoi' => '1984-06-20',
            'trang_thai' => 'Đang sống',
        ]);

        // 2. Trần Khắc Dũng (Con cụ Long - cụ Vân)
        $ongDung2 = ThanhVien::create([
            'chi_nhanh_id' => $cnId2,
            'ho_ten' => 'Trần Khắc Dũng',
            'ten_goi' => 'Ông Dũng',
            'gioi_tinh' => 'Nam',
            'ngay_sinh' => '1948-08-20',
            'cha_id' => $cuLong->id,
            'me_id' => $cuVan->id,
            'noi_sinh' => 'Nam Định',
            'nghe_nghiep' => 'Kỹ sư cầu đường',
            'doi_thu' => 2,
            'loai_quan_he' => 'Chính',
            'trang_thai' => 'Còn sống',
            'ghi_chu' => 'Làm việc tại Sở Giao thông Nam Định, tính bộc trực, phóng khoáng.',
            'avatar' => 'https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?auto=format&fit=crop&q=80&w=200',
        ]);

        $baThao2 = ThanhVien::create([
            'chi_nhanh_id' => $cnId2,
            'ho_ten' => 'Lê Thị Thảo',
            'ten_goi' => 'Bà Thảo',
            'gioi_tinh' => 'Nữ',
            'ngay_sinh' => '1952-03-12',
            'noi_sinh' => 'Nam Định',
            'nghe_nghiep' => 'Giáo viên',
            'doi_thu' => 2,
            'loai_quan_he' => 'Vợ/Chồng',
            'spouse_of_id' => $ongDung2->id,
            'trang_thai' => 'Còn sống',
            'avatar' => 'https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?auto=format&fit=crop&q=80&w=200',
        ]);

        VoChong::create([
            'chong_id' => $ongDung2->id,
            'vo_id' => $baThao2->id,
            'ngay_cuoi' => '1974-11-20',
            'trang_thai' => 'Đang sống',
        ]);

        // 3. Trần Thị Hoa (Con gái cụ Long - cụ Vân)
        $baHoa2 = ThanhVien::create([
            'chi_nhanh_id' => $cnId2,
            'ho_ten' => 'Trần Thị Hoa',
            'ten_goi' => 'Bà Hoa',
            'gioi_tinh' => 'Nữ',
            'ngay_sinh' => '1950-10-15',
            'cha_id' => $cuLong->id,
            'me_id' => $cuVan->id,
            'noi_sinh' => 'Nam Định',
            'nghe_nghiep' => 'Y tá',
            'doi_thu' => 2,
            'loai_quan_he' => 'Chính',
            'trang_thai' => 'Còn sống',
            'avatar' => 'https://images.unsplash.com/photo-1531746020798-e6953c6e8e04?auto=format&fit=crop&q=80&w=200',
        ]);

        $ongKhang2 = ThanhVien::create([
            'chi_nhanh_id' => $cnId2,
            'ho_ten' => 'Đỗ Minh Khang',
            'ten_goi' => 'Ông Khang',
            'gioi_tinh' => 'Nam',
            'ngay_sinh' => '1947-02-14',
            'noi_sinh' => 'Hải Phòng',
            'nghe_nghiep' => 'Kiến trúc sư',
            'doi_thu' => 2,
            'loai_quan_he' => 'Vợ/Chồng',
            'spouse_of_id' => $baHoa2->id,
            'trang_thai' => 'Còn sống',
            'avatar' => 'https://images.unsplash.com/photo-1492562080023-ab3db95bfbce?auto=format&fit=crop&q=80&w=200',
        ]);

        VoChong::create([
            'chong_id' => $ongKhang2->id,
            'vo_id' => $baHoa2->id,
            'ngay_cuoi' => '1972-01-18',
            'trang_thai' => 'Đang sống',
        ]);

        // ĐỜI 2: Con cụ Long & cụ Mai (Vợ 2)
        // 4. Trần Khắc Hùng (Con cụ Long - cụ Mai)
        $ongHung2 = ThanhVien::create([
            'chi_nhanh_id' => $cnId2,
            'ho_ten' => 'Trần Khắc Hùng',
            'ten_goi' => 'Ông Hùng',
            'gioi_tinh' => 'Nam',
            'ngay_sinh' => '1954-06-15',
            'cha_id' => $cuLong->id,
            'me_id' => $cuMai->id,
            'noi_sinh' => 'Nam Định',
            'nghe_nghiep' => 'Đại tá quân đội',
            'doi_thu' => 2,
            'loai_quan_he' => 'Chính',
            'trang_thai' => 'Còn sống',
            'ghi_chu' => 'Thương binh kháng chiến chống Mỹ, tác phong quân đội nghiêm khắc.',
            'avatar' => 'https://images.unsplash.com/photo-1506794778202-cad84cf45f1d?auto=format&fit=crop&q=80&w=200',
        ]);

        $baCuc2 = ThanhVien::create([
            'chi_nhanh_id' => $cnId2,
            'ho_ten' => 'Lâm Thị Cúc',
            'ten_goi' => 'Bà Cúc',
            'gioi_tinh' => 'Nữ',
            'ngay_sinh' => '1958-11-20',
            'noi_sinh' => 'Hưng Yên',
            'nghe_nghiep' => 'Kinh doanh tự do',
            'doi_thu' => 2,
            'loai_quan_he' => 'Vợ/Chồng',
            'spouse_of_id' => $ongHung2->id,
            'trang_thai' => 'Còn sống',
            'ghi_chu' => 'Vợ ông Hùng, kinh doanh nông sản.',
            'avatar' => 'https://images.unsplash.com/photo-1534751516642-a131ffd103fd?auto=format&fit=crop&q=80&w=200',
        ]);

        VoChong::create([
            'chong_id' => $ongHung2->id,
            'vo_id' => $baCuc2->id,
            'ngay_cuoi' => '1979-10-12',
            'trang_thai' => 'Đang sống',
        ]);

        // 5. Trần Khắc Liêm (Con cụ Long - cụ Mai)
        $ongLiem2 = ThanhVien::create([
            'chi_nhanh_id' => $cnId2,
            'ho_ten' => 'Trần Khắc Liêm',
            'ten_goi' => 'Ông Liêm',
            'gioi_tinh' => 'Nam',
            'ngay_sinh' => '1956-09-20',
            'cha_id' => $cuLong->id,
            'me_id' => $cuMai->id,
            'noi_sinh' => 'Nam Định',
            'nghe_nghiep' => 'Kinh doanh lụa',
            'doi_thu' => 2,
            'loai_quan_he' => 'Chính',
            'trang_thai' => 'Còn sống',
            'avatar' => 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?auto=format&fit=crop&q=80&w=200',
        ]);

        $baPhuong2 = ThanhVien::create([
            'chi_nhanh_id' => $cnId2,
            'ho_ten' => 'Nguyễn Thị Phương',
            'ten_goi' => 'Bà Phương',
            'gioi_tinh' => 'Nữ',
            'ngay_sinh' => '1960-05-18',
            'noi_sinh' => 'Thái Bình',
            'nghe_nghiep' => 'Kế toán',
            'doi_thu' => 2,
            'loai_quan_he' => 'Vợ/Chồng',
            'spouse_of_id' => $ongLiem2->id,
            'trang_thai' => 'Còn sống',
            'avatar' => 'https://images.unsplash.com/photo-1544005313-94ddf0286df2?auto=format&fit=crop&q=80&w=200',
        ]);

        VoChong::create([
            'chong_id' => $ongLiem2->id,
            'vo_id' => $baPhuong2->id,
            'ngay_cuoi' => '1982-12-25',
            'trang_thai' => 'Đang sống',
        ]);

        // ĐỜI 3: Con anh Trần Khắc Tiến & bà Hồng (Vợ 1)
        // 1. Trần Khắc Nam (Chính)
        $anhNam2 = ThanhVien::create([
            'chi_nhanh_id' => $cnId2,
            'ho_ten' => 'Trần Khắc Nam',
            'ten_goi' => 'Anh Nam',
            'gioi_tinh' => 'Nam',
            'ngay_sinh' => '1970-11-14',
            'cha_id' => $ongTien2->id,
            'me_id' => $baHong->id,
            'noi_sinh' => 'Nam Định',
            'nghe_nghiep' => 'Doanh nhân',
            'doi_thu' => 3,
            'loai_quan_he' => 'Chính',
            'trang_thai' => 'Còn sống',
            'ghi_chu' => 'Chủ doanh nghiệp dệt may tư nhân lớn tại Nam Định.',
            'avatar' => 'https://images.unsplash.com/photo-1519085360753-af0119f7cbe7?auto=format&fit=crop&q=80&w=200',
        ]);

        $chiLan2 = ThanhVien::create([
            'chi_nhanh_id' => $cnId2,
            'ho_ten' => 'Nguyễn Thị Lan',
            'ten_goi' => 'Chị Lan',
            'gioi_tinh' => 'Nữ',
            'ngay_sinh' => '1975-04-18',
            'noi_sinh' => 'Nam Định',
            'nghe_nghiep' => 'Kế toán',
            'doi_thu' => 3,
            'loai_quan_he' => 'Vợ/Chồng',
            'spouse_of_id' => $anhNam2->id,
            'trang_thai' => 'Còn sống',
            'avatar' => 'https://images.unsplash.com/photo-1544005313-94ddf0286df2?auto=format&fit=crop&q=80&w=200',
        ]);

        VoChong::create([
            'chong_id' => $anhNam2->id,
            'vo_id' => $chiLan2->id,
            'ngay_cuoi' => '1996-09-20',
            'trang_thai' => 'Đang sống',
        ]);

        // 2. Trần Thị Mai (Chính)
        $chiMai2 = ThanhVien::create([
            'chi_nhanh_id' => $cnId2,
            'ho_ten' => 'Trần Thị Mai',
            'ten_goi' => 'Chị Mai',
            'gioi_tinh' => 'Nữ',
            'ngay_sinh' => '1973-07-22',
            'cha_id' => $ongTien2->id,
            'me_id' => $baHong->id,
            'noi_sinh' => 'Nam Định',
            'nghe_nghiep' => 'Giáo viên',
            'doi_thu' => 3,
            'loai_quan_he' => 'Chính',
            'trang_thai' => 'Còn sống',
            'avatar' => 'https://images.unsplash.com/photo-1558203728-00f45181dd84?auto=format&fit=crop&q=80&w=200',
        ]);

        $anhLam2ConRe = ThanhVien::create([
            'chi_nhanh_id' => $cnId2,
            'ho_ten' => 'Vũ Hoàng Lâm',
            'ten_goi' => 'Anh Lâm',
            'gioi_tinh' => 'Nam',
            'ngay_sinh' => '1970-05-18',
            'noi_sinh' => 'Nam Định',
            'nghe_nghiep' => 'Bác sĩ',
            'doi_thu' => 3,
            'loai_quan_he' => 'Vợ/Chồng',
            'spouse_of_id' => $chiMai2->id,
            'trang_thai' => 'Còn sống',
            'avatar' => 'https://images.unsplash.com/photo-1537368910025-700350fe46c7?auto=format&fit=crop&q=80&w=200',
        ]);

        VoChong::create([
            'chong_id' => $anhLam2ConRe->id,
            'vo_id' => $chiMai2->id,
            'ngay_cuoi' => '1995-10-12',
            'trang_thai' => 'Đang sống',
        ]);

        // ĐỜI 3: Con anh Trần Khắc Tiến & bà Tuyết (Vợ 2)
        // 3. Trần Khắc Minh (Chính)
        $anhMinh2 = ThanhVien::create([
            'chi_nhanh_id' => $cnId2,
            'ho_ten' => 'Trần Khắc Minh',
            'ten_goi' => 'Anh Minh',
            'gioi_tinh' => 'Nam',
            'ngay_sinh' => '1985-04-10',
            'cha_id' => $ongTien2->id,
            'me_id' => $baTuyet2->id,
            'noi_sinh' => 'Nam Định',
            'nghe_nghiep' => 'Cơ khí',
            'doi_thu' => 3,
            'loai_quan_he' => 'Chính',
            'trang_thai' => 'Còn sống',
            'avatar' => 'https://images.unsplash.com/photo-1506794778202-cad84cf45f1d?auto=format&fit=crop&q=80&w=200',
        ]);

        $chiLanAnh2Dau = ThanhVien::create([
            'chi_nhanh_id' => $cnId2,
            'ho_ten' => 'Phạm Thị Lan',
            'ten_goi' => 'Chị Lan',
            'gioi_tinh' => 'Nữ',
            'ngay_sinh' => '1988-08-15',
            'noi_sinh' => 'Nam Định',
            'nghe_nghiep' => 'Dược sĩ',
            'doi_thu' => 3,
            'loai_quan_he' => 'Vợ/Chồng',
            'spouse_of_id' => $anhMinh2->id,
            'trang_thai' => 'Còn sống',
            'avatar' => 'https://images.unsplash.com/photo-1544005313-94ddf0286df2?auto=format&fit=crop&q=80&w=200',
        ]);

        VoChong::create([
            'chong_id' => $anhMinh2->id,
            'vo_id' => $chiLanAnh2Dau->id,
            'ngay_cuoi' => '2010-09-22',
            'trang_thai' => 'Đang sống',
        ]);

        // ĐỜI 3: Con ông Trần Khắc Dũng & bà Thảo
        // 4. Trần Khắc Huy (Chính)
        $anhHuy2 = ThanhVien::create([
            'chi_nhanh_id' => $cnId2,
            'ho_ten' => 'Trần Khắc Huy',
            'ten_goi' => 'Anh Huy',
            'gioi_tinh' => 'Nam',
            'ngay_sinh' => '1976-10-12',
            'cha_id' => $ongDung2->id,
            'me_id' => $baThao2->id,
            'noi_sinh' => 'Nam Định',
            'nghe_nghiep' => 'Kỹ sư cơ điện',
            'doi_thu' => 3,
            'loai_quan_he' => 'Chính',
            'trang_thai' => 'Còn sống',
            'avatar' => 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?auto=format&fit=crop&q=80&w=200',
        ]);

        $chiVan2Dau = ThanhVien::create([
            'chi_nhanh_id' => $cnId2,
            'ho_ten' => 'Bùi Thị Vân',
            'ten_goi' => 'Chị Vân',
            'gioi_tinh' => 'Nữ',
            'ngay_sinh' => '1980-05-18',
            'noi_sinh' => 'Thái Bình',
            'nghe_nghiep' => 'Kế toán',
            'doi_thu' => 3,
            'loai_quan_he' => 'Vợ/Chồng',
            'spouse_of_id' => $anhHuy2->id,
            'trang_thai' => 'Còn sống',
            'avatar' => 'https://images.unsplash.com/photo-1534751516642-a131ffd103fd?auto=format&fit=crop&q=80&w=200',
        ]);

        VoChong::create([
            'chong_id' => $anhHuy2->id,
            'vo_id' => $chiVan2Dau->id,
            'ngay_cuoi' => '2002-12-14',
            'trang_thai' => 'Đang sống',
        ]);

        // 5. Trần Thị Hằng (Chính)
        $chiHang2 = ThanhVien::create([
            'chi_nhanh_id' => $cnId2,
            'ho_ten' => 'Trần Thị Hằng',
            'ten_goi' => 'Chị Hằng',
            'gioi_tinh' => 'Nữ',
            'ngay_sinh' => '1979-11-20',
            'cha_id' => $ongDung2->id,
            'me_id' => $baThao2->id,
            'noi_sinh' => 'Nam Định',
            'nghe_nghiep' => 'Hành chính',
            'doi_thu' => 3,
            'loai_quan_he' => 'Chính',
            'trang_thai' => 'Còn sống',
            'avatar' => 'https://images.unsplash.com/photo-1580489944761-15a19d654956?auto=format&fit=crop&q=80&w=200',
        ]);

        $anhTuan2ConRe = ThanhVien::create([
            'chi_nhanh_id' => $cnId2,
            'ho_ten' => 'Phạm Quốc Tuấn',
            'ten_goi' => 'Anh Tuấn',
            'gioi_tinh' => 'Nam',
            'ngay_sinh' => '1975-04-12',
            'noi_sinh' => 'Nam Định',
            'nghe_nghiep' => 'Kinh doanh tự do',
            'doi_thu' => 3,
            'loai_quan_he' => 'Vợ/Chồng',
            'spouse_of_id' => $chiHang2->id,
            'trang_thai' => 'Còn sống',
            'avatar' => 'https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?auto=format&fit=crop&q=80&w=200',
        ]);

        VoChong::create([
            'chong_id' => $anhTuan2ConRe->id,
            'vo_id' => $chiHang2->id,
            'ngay_cuoi' => '2001-05-15',
            'trang_thai' => 'Đang sống',
        ]);

        // ĐỜI 3: Con ông Trần Thị Hoa & Đỗ Minh Khang
        // 6. Đỗ Minh Quân (Chính)
        $anhQuan2 = ThanhVien::create([
            'chi_nhanh_id' => $cnId2,
            'ho_ten' => 'Đỗ Minh Quân',
            'ten_goi' => 'Anh Quân',
            'gioi_tinh' => 'Nam',
            'ngay_sinh' => '1974-05-15',
            'cha_id' => $ongKhang2->id,
            'me_id' => $baHoa2->id,
            'noi_sinh' => 'Nam Định',
            'nghe_nghiep' => 'Kỹ sư cầu đường',
            'doi_thu' => 3,
            'loai_quan_he' => 'Chính',
            'trang_thai' => 'Còn sống',
            'avatar' => 'https://images.unsplash.com/photo-1506794778202-cad84cf45f1d?auto=format&fit=crop&q=80&w=200',
        ]);

        // 7. Đỗ Thị Tú Anh (Chính)
        $chiTuAnh2 = ThanhVien::create([
            'chi_nhanh_id' => $cnId2,
            'ho_ten' => 'Đỗ Thị Tú Anh',
            'ten_goi' => 'Tú Anh',
            'gioi_tinh' => 'Nữ',
            'ngay_sinh' => '1977-10-12',
            'cha_id' => $ongKhang2->id,
            'me_id' => $baHoa2->id,
            'noi_sinh' => 'Nam Định',
            'nghe_nghiep' => 'Kế toán',
            'doi_thu' => 3,
            'loai_quan_he' => 'Chính',
            'trang_thai' => 'Còn sống',
            'avatar' => 'https://images.unsplash.com/photo-1544005313-94ddf0286df2?auto=format&fit=crop&q=80&w=200',
        ]);

        // ĐỜI 3: Con ông Trần Khắc Hùng & bà Cúc
        // 8. Trần Khắc Hải (Chính)
        $anhHai2 = ThanhVien::create([
            'chi_nhanh_id' => $cnId2,
            'ho_ten' => 'Trần Khắc Hải',
            'ten_goi' => 'Anh Hải',
            'gioi_tinh' => 'Nam',
            'ngay_sinh' => '1982-01-20',
            'cha_id' => $ongHung2->id,
            'me_id' => $baCuc2->id,
            'noi_sinh' => 'Nam Định',
            'nghe_nghiep' => 'Kỹ sư CNTT',
            'doi_thu' => 3,
            'loai_quan_he' => 'Chính',
            'trang_thai' => 'Còn sống',
            'ghi_chu' => 'Chuyên gia thiết kế phần mềm làm việc tại Hà Nội.',
            'avatar' => 'https://images.unsplash.com/photo-1560250097-0b93528c311a?auto=format&fit=crop&q=80&w=200',
        ]);

        $chiHa2 = ThanhVien::create([
            'chi_nhanh_id' => $cnId2,
            'ho_ten' => 'Vũ Thu Hà',
            'ten_goi' => 'Chị Hà',
            'gioi_tinh' => 'Nữ',
            'ngay_sinh' => '1986-06-25',
            'noi_sinh' => 'Thái Bình',
            'nghe_nghiep' => 'Kiến trúc sư',
            'doi_thu' => 3,
            'loai_quan_he' => 'Vợ/Chồng',
            'spouse_of_id' => $anhHai2->id,
            'trang_thai' => 'Còn sống',
            'avatar' => 'https://images.unsplash.com/photo-1580489944761-15a19d654956?auto=format&fit=crop&q=80&w=200',
        ]);

        VoChong::create([
            'chong_id' => $anhHai2->id,
            'vo_id' => $chiHa2->id,
            'ngay_cuoi' => '2010-02-14',
            'trang_thai' => 'Đang sống',
        ]);

        // 9. Trần Thị Thanh (Chính)
        $chiThanh2 = ThanhVien::create([
            'chi_nhanh_id' => $cnId2,
            'ho_ten' => 'Trần Thị Thanh',
            'ten_goi' => 'Chị Thanh',
            'gioi_tinh' => 'Nữ',
            'ngay_sinh' => '1985-05-18',
            'cha_id' => $ongHung2->id,
            'me_id' => $baCuc2->id,
            'noi_sinh' => 'Nam Định',
            'nghe_nghiep' => 'Nhân viên ngân hàng',
            'doi_thu' => 3,
            'loai_quan_he' => 'Chính',
            'trang_thai' => 'Còn sống',
            'avatar' => 'https://images.unsplash.com/photo-1544005313-94ddf0286df2?auto=format&fit=crop&q=80&w=200',
        ]);

        // ĐỜI 3: Con ông Trần Khắc Liêm & bà Phương
        // 10. Trần Khắc Cường (Chính)
        $anhCuong2 = ThanhVien::create([
            'chi_nhanh_id' => $cnId2,
            'ho_ten' => 'Trần Khắc Cường',
            'ten_goi' => 'Anh Cường',
            'gioi_tinh' => 'Nam',
            'ngay_sinh' => '1984-06-15',
            'cha_id' => $ongLiem2->id,
            'me_id' => $baPhuong2->id,
            'noi_sinh' => 'Nam Định',
            'nghe_nghiep' => 'Kinh doanh tự do',
            'doi_thu' => 3,
            'loai_quan_he' => 'Chính',
            'trang_thai' => 'Còn sống',
            'avatar' => 'https://images.unsplash.com/photo-1506794778202-cad84cf45f1d?auto=format&fit=crop&q=80&w=200',
        ]);

        $chiChi2Dau = ThanhVien::create([
            'chi_nhanh_id' => $cnId2,
            'ho_ten' => 'Vũ Thùy Chi',
            'ten_goi' => 'Chị Chi',
            'gioi_tinh' => 'Nữ',
            'ngay_sinh' => '1987-11-20',
            'noi_sinh' => 'Nam Định',
            'nghe_nghiep' => 'Chủ shop thời trang',
            'doi_thu' => 3,
            'loai_quan_he' => 'Vợ/Chồng',
            'spouse_of_id' => $anhCuong2->id,
            'trang_thai' => 'Còn sống',
            'avatar' => 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&q=80&w=200',
        ]);

        VoChong::create([
            'chong_id' => $anhCuong2->id,
            'vo_id' => $chiChi2Dau->id,
            'ngay_cuoi' => '2012-10-18',
            'trang_thai' => 'Đang sống',
        ]);

        // 11. Trần Thị Thu Trang (Chính)
        $chiTrang2 = ThanhVien::create([
            'chi_nhanh_id' => $cnId2,
            'ho_ten' => 'Trần Thị Thu Trang',
            'ten_goi' => 'Chị Trang',
            'gioi_tinh' => 'Nữ',
            'ngay_sinh' => '1987-10-12',
            'cha_id' => $ongLiem2->id,
            'me_id' => $baPhuong2->id,
            'noi_sinh' => 'Nam Định',
            'nghe_nghiep' => 'Văn thư',
            'doi_thu' => 3,
            'loai_quan_he' => 'Chính',
            'trang_thai' => 'Còn sống',
            'avatar' => 'https://images.unsplash.com/photo-1544005313-94ddf0286df2?auto=format&fit=crop&q=80&w=200',
        ]);

        // ĐỜI 4: Con anh Trần Khắc Nam & chị Lan
        $anhHoang2 = ThanhVien::create([
            'chi_nhanh_id' => $cnId2,
            'ho_ten' => 'Trần Khắc Hoàng',
            'ten_goi' => 'Anh Hoàng',
            'gioi_tinh' => 'Nam',
            'ngay_sinh' => '1998-02-12',
            'cha_id' => $anhNam2->id,
            'me_id' => $chiLan2->id,
            'noi_sinh' => 'Nam Định',
            'nghe_nghiep' => 'Chuyên viên tài chính',
            'doi_thu' => 4,
            'loai_quan_he' => 'Chính',
            'trang_thai' => 'Còn sống',
            'avatar' => 'https://images.unsplash.com/photo-1506794778202-cad84cf45f1d?auto=format&fit=crop&q=80&w=200',
        ]);

        $chiNgoc2 = ThanhVien::create([
            'chi_nhanh_id' => $cnId2,
            'ho_ten' => 'Đỗ Thị Ngọc',
            'ten_goi' => 'Chị Ngọc',
            'gioi_tinh' => 'Nữ',
            'ngay_sinh' => '2001-05-20',
            'noi_sinh' => 'Hải Dương',
            'nghe_nghiep' => 'Dược sĩ',
            'doi_thu' => 4,
            'loai_quan_he' => 'Vợ/Chồng',
            'spouse_of_id' => $anhHoang2->id,
            'trang_thai' => 'Còn sống',
            'avatar' => 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&q=80&w=200',
        ]);

        VoChong::create([
            'chong_id' => $anhHoang2->id,
            'vo_id' => $chiNgoc2->id,
            'ngay_cuoi' => '2023-11-20',
            'trang_thai' => 'Đang sống',
        ]);

        $chiThu2 = ThanhVien::create([
            'chi_nhanh_id' => $cnId2,
            'ho_ten' => 'Trần Thị Thu',
            'ten_goi' => 'Chị Thu',
            'gioi_tinh' => 'Nữ',
            'ngay_sinh' => '2000-08-14',
            'cha_id' => $anhNam2->id,
            'me_id' => $chiLan2->id,
            'noi_sinh' => 'Nam Định',
            'nghe_nghiep' => 'Sinh viên cao học',
            'doi_thu' => 4,
            'loai_quan_he' => 'Chính',
            'trang_thai' => 'Còn sống',
            'avatar' => 'https://images.unsplash.com/photo-1544005313-94ddf0286df2?auto=format&fit=crop&q=80&w=200',
        ]);

        $anhDuy2 = ThanhVien::create([
            'chi_nhanh_id' => $cnId2,
            'ho_ten' => 'Trần Khắc Duy',
            'ten_goi' => 'Anh Duy',
            'gioi_tinh' => 'Nam',
            'ngay_sinh' => '2002-12-05',
            'cha_id' => $anhNam2->id,
            'me_id' => $chiLan2->id,
            'noi_sinh' => 'Nam Định',
            'nghe_nghiep' => 'Lập trình viên',
            'doi_thu' => 4,
            'loai_quan_he' => 'Chính',
            'trang_thai' => 'Còn sống',
            'avatar' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?auto=format&fit=crop&q=80&w=200',
        ]);

        // ĐỜI 4: Con anh Vũ Hoàng Lâm & chị Trần Thị Mai
        $anhPhong2 = ThanhVien::create([
            'chi_nhanh_id' => $cnId2,
            'ho_ten' => 'Vũ Hoàng Phong',
            'ten_goi' => 'Hoàng Phong',
            'gioi_tinh' => 'Nam',
            'ngay_sinh' => '1998-05-18',
            'cha_id' => $anhLam2ConRe->id,
            'me_id' => $chiMai2->id,
            'noi_sinh' => 'Nam Định',
            'nghe_nghiep' => 'Bác sĩ trẻ',
            'doi_thu' => 4,
            'loai_quan_he' => 'Chính',
            'trang_thai' => 'Còn sống',
            'avatar' => 'https://images.unsplash.com/photo-1506794778202-cad84cf45f1d?auto=format&fit=crop&q=80&w=200',
        ]);

        $chiLinh2 = ThanhVien::create([
            'chi_nhanh_id' => $cnId2,
            'ho_ten' => 'Vũ Thị Khánh Linh',
            'ten_goi' => 'Khánh Linh',
            'gioi_tinh' => 'Nữ',
            'ngay_sinh' => '2001-09-22',
            'cha_id' => $anhLam2ConRe->id,
            'me_id' => $chiMai2->id,
            'noi_sinh' => 'Nam Định',
            'nghe_nghiep' => 'Sinh viên y khoa',
            'doi_thu' => 4,
            'loai_quan_he' => 'Chính',
            'trang_thai' => 'Còn sống',
            'avatar' => 'https://images.unsplash.com/photo-1544005313-94ddf0286df2?auto=format&fit=crop&q=80&w=200',
        ]);

        // ĐỜI 4: Con anh Trần Khắc Minh & chị Lan (Vợ 2 của Tiến)
        $beKiet2 = ThanhVien::create([
            'chi_nhanh_id' => $cnId2,
            'ho_ten' => 'Trần Khắc Tuấn Kiệt',
            'ten_goi' => 'Cháu Tuấn Kiệt',
            'gioi_tinh' => 'Nam',
            'ngay_sinh' => '2012-04-10',
            'cha_id' => $anhMinh2->id,
            'me_id' => $chiLanAnh2Dau->id,
            'noi_sinh' => 'Nam Định',
            'nghe_nghiep' => 'Học sinh',
            'doi_thu' => 4,
            'loai_quan_he' => 'Chính',
            'trang_thai' => 'Còn sống',
            'avatar' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?auto=format&fit=crop&q=80&w=200',
        ]);

        $beKhue2 = ThanhVien::create([
            'chi_nhanh_id' => $cnId2,
            'ho_ten' => 'Trần Thị Minh Khuê',
            'ten_goi' => 'Cháu Minh Khuê',
            'gioi_tinh' => 'Nữ',
            'ngay_sinh' => '2015-08-15',
            'cha_id' => $anhMinh2->id,
            'me_id' => $chiLanAnh2Dau->id,
            'noi_sinh' => 'Nam Định',
            'nghe_nghiep' => 'Học sinh',
            'doi_thu' => 4,
            'loai_quan_he' => 'Chính',
            'trang_thai' => 'Còn sống',
            'avatar' => 'https://images.unsplash.com/photo-1544005313-94ddf0286df2?auto=format&fit=crop&q=80&w=200',
        ]);

        // ĐỜI 4: Con anh Trần Khắc Huy & chị Vân
        $beDang2 = ThanhVien::create([
            'chi_nhanh_id' => $cnId2,
            'ho_ten' => 'Trần Khắc Đăng',
            'ten_goi' => 'Cháu Đăng',
            'gioi_tinh' => 'Nam',
            'ngay_sinh' => '2004-09-12',
            'cha_id' => $anhHuy2->id,
            'me_id' => $chiVan2Dau->id,
            'noi_sinh' => 'Nam Định',
            'nghe_nghiep' => 'Sinh viên',
            'doi_thu' => 4,
            'loai_quan_he' => 'Chính',
            'trang_thai' => 'Còn sống',
            'avatar' => 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?auto=format&fit=crop&q=80&w=200',
        ]);

        $beQuynh2 = ThanhVien::create([
            'chi_nhanh_id' => $cnId2,
            'ho_ten' => 'Trần Thị Quỳnh',
            'ten_goi' => 'Cháu Quỳnh',
            'gioi_tinh' => 'Nữ',
            'ngay_sinh' => '2006-11-15',
            'cha_id' => $anhHuy2->id,
            'me_id' => $chiVan2Dau->id,
            'noi_sinh' => 'Nam Định',
            'nghe_nghiep' => 'Học sinh THPT',
            'doi_thu' => 4,
            'loai_quan_he' => 'Chính',
            'trang_thai' => 'Còn sống',
            'avatar' => 'https://images.unsplash.com/photo-1544005313-94ddf0286df2?auto=format&fit=crop&q=80&w=200',
        ]);

        // ĐỜI 4: Con anh Trần Khắc Hải & chị Hà
        $beViet2 = ThanhVien::create([
            'chi_nhanh_id' => $cnId2,
            'ho_ten' => 'Trần Khắc Việt',
            'ten_goi' => 'Cháu Việt',
            'gioi_tinh' => 'Nam',
            'ngay_sinh' => '2012-09-10',
            'cha_id' => $anhHai2->id,
            'me_id' => $chiHa2->id,
            'noi_sinh' => 'Hà Nội',
            'nghe_nghiep' => 'Học sinh',
            'doi_thu' => 4,
            'loai_quan_he' => 'Chính',
            'trang_thai' => 'Còn sống',
            'avatar' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?auto=format&fit=crop&q=80&w=200',
        ]);

        $beLanAnh2 = ThanhVien::create([
            'chi_nhanh_id' => $cnId2,
            'ho_ten' => 'Trần Thị Lan Anh',
            'ten_goi' => 'Cháu Lan Anh',
            'gioi_tinh' => 'Nữ',
            'ngay_sinh' => '2015-11-15',
            'cha_id' => $anhHai2->id,
            'me_id' => $chiHa2->id,
            'noi_sinh' => 'Hà Nội',
            'nghe_nghiep' => 'Học sinh',
            'doi_thu' => 4,
            'loai_quan_he' => 'Chính',
            'trang_thai' => 'Còn sống',
            'avatar' => 'https://images.unsplash.com/photo-1544005313-94ddf0286df2?auto=format&fit=crop&q=80&w=200',
        ]);

        $beKhanhNam2 = ThanhVien::create([
            'chi_nhanh_id' => $cnId2,
            'ho_ten' => 'Trần Khắc Nam Khánh',
            'ten_goi' => 'Bé Nam Khánh',
            'gioi_tinh' => 'Nam',
            'ngay_sinh' => '2018-12-14',
            'cha_id' => $anhHai2->id,
            'me_id' => $chiHa2->id,
            'noi_sinh' => 'Hà Nội',
            'nghe_nghiep' => 'Học sinh tiểu học',
            'doi_thu' => 4,
            'loai_quan_he' => 'Chính',
            'trang_thai' => 'Còn sống',
            'avatar' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?auto=format&fit=crop&q=80&w=200',
        ]);

        // ĐỜI 4: Con anh Trần Khắc Cường & chị Chi
        $beDucAnh2 = ThanhVien::create([
            'chi_nhanh_id' => $cnId2,
            'ho_ten' => 'Trần Khắc Đức Anh',
            'ten_goi' => 'Bé Đức Anh',
            'gioi_tinh' => 'Nam',
            'ngay_sinh' => '2014-05-18',
            'cha_id' => $anhCuong2->id,
            'me_id' => $chiChi2Dau->id,
            'noi_sinh' => 'Nam Định',
            'nghe_nghiep' => 'Học sinh',
            'doi_thu' => 4,
            'loai_quan_he' => 'Chính',
            'trang_thai' => 'Còn sống',
            'avatar' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?auto=format&fit=crop&q=80&w=200',
        ]);

        $beBaoVy2 = ThanhVien::create([
            'chi_nhanh_id' => $cnId2,
            'ho_ten' => 'Trần Ngọc Bảo Vy',
            'ten_goi' => 'Bé Bảo Vy',
            'gioi_tinh' => 'Nữ',
            'ngay_sinh' => '2017-09-22',
            'cha_id' => $anhCuong2->id,
            'me_id' => $chiChi2Dau->id,
            'noi_sinh' => 'Nam Định',
            'nghe_nghiep' => 'Học sinh',
            'doi_thu' => 4,
            'loai_quan_he' => 'Chính',
            'trang_thai' => 'Còn sống',
            'avatar' => 'https://images.unsplash.com/photo-1544005313-94ddf0286df2?auto=format&fit=crop&q=80&w=200',
        ]);

        // ĐỜI 5: Con anh Hoàng & chị Ngọc
        $beBao2 = ThanhVien::create([
            'chi_nhanh_id' => $cnId2,
            'ho_ten' => 'Trần Khắc Bảo',
            'ten_goi' => 'Bé Bảo',
            'gioi_tinh' => 'Nam',
            'ngay_sinh' => '2025-05-10',
            'cha_id' => $anhHoang2->id,
            'me_id' => $chiNgoc2->id,
            'noi_sinh' => 'Nam Định',
            'nghe_nghiep' => 'Trẻ sơ sinh',
            'doi_thu' => 5,
            'loai_quan_he' => 'Chính',
            'trang_thai' => 'Còn sống',
            'avatar' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?auto=format&fit=crop&q=80&w=200',
        ]);

        $beGiaHung2 = ThanhVien::create([
            'chi_nhanh_id' => $cnId2,
            'ho_ten' => 'Trần Khắc Gia Hưng',
            'ten_goi' => 'Bé Gia Hưng',
            'gioi_tinh' => 'Nam',
            'ngay_sinh' => '2027-04-12',
            'cha_id' => $anhHoang2->id,
            'me_id' => $chiNgoc2->id,
            'noi_sinh' => 'Nam Định',
            'nghe_nghiep' => 'Trẻ sơ sinh',
            'doi_thu' => 5,
            'loai_quan_he' => 'Chính',
            'trang_thai' => 'Còn sống',
            'avatar' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?auto=format&fit=crop&q=80&w=200',
        ]);

        // Tự động sinh dữ liệu từ đời 6 đến đời 10 cho dòng họ Trần Khắc
        self::generateGenerations6To10($cnId2, 'Trần Khắc', 'Trần Thị');

        // ==========================================
        // SEED PARTNER 3: LÊ MINH KHANG (doitac3@master.com)
        // ==========================================

        // Avoid duplicate partner 3
        $existingUser3 = NguoiDung::withTrashed()->where('email', 'doitac3@master.com')->first();
        if ($existingUser3) {
            $oldBranchIds3 = ChiNhanh::where('id_nguoi_dung', $existingUser3->id)->pluck('id');
            ThanhVien::whereIn('chi_nhanh_id', $oldBranchIds3)->delete();
            DoiTocHo::whereIn('chi_nhanh_id', $oldBranchIds3)->delete();
            ChiNhanh::where('id_nguoi_dung', $existingUser3->id)->delete();
            DoiTac::where('id_nguoi_dung', $existingUser3->id)->delete();
            $existingUser3->forceDelete();
        }

        $userId3 = DB::table('nguoi_dungs')->insertGetId([
            'ho_ten' => 'Lê Minh Khang',
            'email' => 'doitac3@master.com',
            'mat_khau' => Hash::make('123456'),
            'so_dien_thoai' => '0977666555',
            'vai_tro' => 'Thành viên',
            'trang_thai' => 'Hoạt động',
            'is_doi_tac' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('doi_tacs')->insert([
            'id_nguoi_dung'  => $userId3,
            'ten_goi'        => 'Gói Khởi Tạo',
            'so_tien'        => 1200000,
            'id_goi_dich_vu' => null,
            'features'       => 'tao_cay_gia_pha,them_thanh_vien,sua_xoa_thanh_vien,quan_ly_vo_chong,quan_ly_con_nuoi,xuat_pdf,quan_ly_tai_lieu,upload_hinh_anh',
            'max_doi'        => 99,
            'max_thanh_vien' => 10000,
            'ngay_bat_dau'   => now(),
            'ngay_ket_thuc'  => now()->addYears(1),
            'trang_thai'     => 'APPROVED',
            'created_at'     => now(),
            'updated_at'     => now(),
        ]);

        $chiNhanh3 = ChiNhanh::create([
            'ten_chi' => 'Dòng Họ Lê Hữu - Thanh Hóa',
            'mo_ta' => 'Chi nhánh dòng tộc Lê Hữu khởi nguyên từ Thọ Xuân, Thanh Hóa - vùng đất địa linh nhân kiệt.',
            'id_nguoi_dung' => $userId3,
        ]);

        $cnId3 = $chiNhanh3->id;

        // Generations for branch 3
        DoiTocHo::create(['so_doi' => 1, 'ten_doi' => 'Thủy Tổ Khai Cơ', 'mo_ta' => 'Thế hệ đầu khai khẩn chi họ Lê Hữu', 'chi_nhanh_id' => $cnId3]);
        DoiTocHo::create(['so_doi' => 2, 'ten_doi' => 'Thế Hệ Tuyên Úy', 'mo_ta' => 'Giai đoạn giữ vững giềng mối gia đình', 'chi_nhanh_id' => $cnId3]);
        DoiTocHo::create(['so_doi' => 3, 'ten_doi' => 'Thế Hệ Kiến Tạo', 'mo_ta' => 'Thời kỳ đổi mới phát triển thịnh vượng', 'chi_nhanh_id' => $cnId3]);
        DoiTocHo::create(['so_doi' => 4, 'ten_doi' => 'Thế Hệ Khát Vọng', 'mo_ta' => 'Con cháu lập nghiệp phương xa', 'chi_nhanh_id' => $cnId3]);
        DoiTocHo::create(['so_doi' => 5, 'ten_doi' => 'Thế Hệ Tiếp Nối', 'mo_ta' => 'Thế hệ tương lai rực rỡ', 'chi_nhanh_id' => $cnId3]);
        DoiTocHo::create(['so_doi' => 6, 'ten_doi' => 'Thế Hệ Tiếp Nối', 'mo_ta' => 'Thế hệ phát huy giá trị truyền thống', 'chi_nhanh_id' => $cnId3]);
        DoiTocHo::create(['so_doi' => 7, 'ten_doi' => 'Thế Hệ Kiến Tạo', 'mo_ta' => 'Thế hệ kiến tạo tương lai phát triển', 'chi_nhanh_id' => $cnId3]);
        DoiTocHo::create(['so_doi' => 8, 'ten_doi' => 'Thế Hệ Vững Bền', 'mo_ta' => 'Thế hệ phát triển vững mạnh toàn diện', 'chi_nhanh_id' => $cnId3]);
        DoiTocHo::create(['so_doi' => 9, 'ten_doi' => 'Thế Hệ Tinh Anh', 'mo_ta' => 'Thế hệ tinh hoa vươn tầm cao mới', 'chi_nhanh_id' => $cnId3]);
        DoiTocHo::create(['so_doi' => 10, 'ten_doi' => 'Thế Hệ Rực Rỡ', 'mo_ta' => 'Thế hệ tương lai tỏa sáng rực rỡ', 'chi_nhanh_id' => $cnId3]);

        // Members for Branch 3
        // ĐỜI 1: Cụ Phúc (2 vợ)
        $cuPhuc = ThanhVien::create([
            'chi_nhanh_id' => $cnId3,
            'ho_ten' => 'Lê Hữu Phúc',
            'ten_goi' => 'Cụ Phúc',
            'gioi_tinh' => 'Nam',
            'ngay_sinh' => '1918-05-22',
            'ngay_mat' => '1995-10-14',
            'noi_sinh' => 'Thanh Hóa',
            'nghe_nghiep' => 'Dạy học chữ Nho',
            'doi_thu' => 1,
            'loai_quan_he' => 'Chính',
            'trang_thai' => 'Đã mất',
            'ghi_chu' => 'Thủy Tổ dòng họ Lê Hữu, thầy đồ nho tôn kính dạy chữ khắp làng.',
            'avatar' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?auto=format&fit=crop&q=80&w=200',
        ]);

        $cuHue = ThanhVien::create([
            'chi_nhanh_id' => $cnId3,
            'ho_ten' => 'Trần Thị Huệ',
            'ten_goi' => 'Cụ Huệ',
            'gioi_tinh' => 'Nữ',
            'ngay_sinh' => '1920-04-18',
            'ngay_mat' => '1988-03-25',
            'noi_sinh' => 'Nghệ An',
            'nghe_nghiep' => 'Làm nông',
            'doi_thu' => 1,
            'loai_quan_he' => 'Vợ/Chồng',
            'spouse_of_id' => $cuPhuc->id,
            'trang_thai' => 'Đã mất',
            'ghi_chu' => 'Hiền thê của cụ Phúc, hết lòng thờ chồng nuôi con.',
            'avatar' => 'https://images.unsplash.com/photo-1544005313-94ddf0286df2?auto=format&fit=crop&q=80&w=200',
        ]);

        VoChong::create([
            'chong_id' => $cuPhuc->id,
            'vo_id' => $cuHue->id,
            'ngay_cuoi' => '1939-02-14',
            'trang_thai' => 'Qua đời',
        ]);

        $cuLien = ThanhVien::create([
            'chi_nhanh_id' => $cnId3,
            'ho_ten' => 'Vũ Thị Liên',
            'ten_goi' => 'Cụ Liên',
            'gioi_tinh' => 'Nữ',
            'ngay_sinh' => '1930-12-14',
            'ngay_mat' => '2012-07-22',
            'noi_sinh' => 'Thanh Hóa',
            'nghe_nghiep' => 'Kinh doanh nhỏ',
            'doi_thu' => 1,
            'loai_quan_he' => 'Vợ/Chồng',
            'spouse_of_id' => $cuPhuc->id,
            'trang_thai' => 'Đã mất',
            'ghi_chu' => 'Vợ thứ của cụ Phúc, buôn bán gạo sỉ lẻ phụ giúp kinh tế gia đình.',
            'avatar' => 'https://images.unsplash.com/photo-1508214751196-bcfd4ca60f91?auto=format&fit=crop&q=80&w=200',
        ]);

        VoChong::create([
            'chong_id' => $cuPhuc->id,
            'vo_id' => $cuLien->id,
            'ngay_cuoi' => '1955-08-20',
            'trang_thai' => 'Qua đời',
        ]);

        // ĐỜI 2: Con cụ Phúc & cụ Huệ (Vợ 1)
        // 1. Lê Hữu Đạt (Có 2 vợ)
        $ongDat = ThanhVien::create([
            'chi_nhanh_id' => $cnId3,
            'ho_ten' => 'Lê Hữu Đạt',
            'ten_goi' => 'Ông Đạt',
            'gioi_tinh' => 'Nam',
            'ngay_sinh' => '1942-05-18',
            'cha_id' => $cuPhuc->id,
            'me_id' => $cuHue->id,
            'noi_sinh' => 'Thanh Hóa',
            'nghe_nghiep' => 'Hiệu trưởng trường học',
            'doi_thu' => 2,
            'loai_quan_he' => 'Chính',
            'trang_thai' => 'Còn sống',
            'ghi_chu' => 'Hiệu trưởng trường THCS Lam Sơn nghỉ hưu, nhà giáo gương mẫu.',
            'avatar' => 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?auto=format&fit=crop&q=80&w=200',
        ]);

        $baCuc3 = ThanhVien::create([
            'chi_nhanh_id' => $cnId3,
            'ho_ten' => 'Hoàng Thị Cúc',
            'ten_goi' => 'Bà Cúc',
            'gioi_tinh' => 'Nữ',
            'ngay_sinh' => '1946-09-22',
            'noi_sinh' => 'Thanh Hóa',
            'nghe_nghiep' => 'Bác sĩ phụ khoa',
            'doi_thu' => 2,
            'loai_quan_he' => 'Vợ/Chồng',
            'spouse_of_id' => $ongDat->id,
            'trang_thai' => 'Còn sống',
            'avatar' => 'https://images.unsplash.com/photo-1494790108377-be9c29b29330?auto=format&fit=crop&q=80&w=200',
        ]);

        VoChong::create([
            'chong_id' => $ongDat->id,
            'vo_id' => $baCuc3->id,
            'ngay_cuoi' => '1968-10-12',
            'trang_thai' => 'Đang sống',
        ]);

        $baHuong3 = ThanhVien::create([
            'chi_nhanh_id' => $cnId3,
            'ho_ten' => 'Nguyễn Thị Hương',
            'ten_goi' => 'Bà Hương',
            'gioi_tinh' => 'Nữ',
            'ngay_sinh' => '1955-03-12',
            'noi_sinh' => 'Thanh Hóa',
            'nghe_nghiep' => 'Thủ thư',
            'doi_thu' => 2,
            'loai_quan_he' => 'Vợ/Chồng',
            'spouse_of_id' => $ongDat->id,
            'trang_thai' => 'Còn sống',
            'avatar' => 'https://images.unsplash.com/photo-1544005313-94ddf0286df2?auto=format&fit=crop&q=80&w=200',
        ]);

        VoChong::create([
            'chong_id' => $ongDat->id,
            'vo_id' => $baHuong3->id,
            'ngay_cuoi' => '1979-11-20',
            'trang_thai' => 'Đang sống',
        ]);

        // 2. Lê Hữu Mạnh (Con cụ Phúc - cụ Huệ)
        $ongManh = ThanhVien::create([
            'chi_nhanh_id' => $cnId3,
            'ho_ten' => 'Lê Hữu Mạnh',
            'ten_goi' => 'Ông Mạnh',
            'gioi_tinh' => 'Nam',
            'ngay_sinh' => '1945-07-28',
            'cha_id' => $cuPhuc->id,
            'me_id' => $cuHue->id,
            'noi_sinh' => 'Thanh Hóa',
            'nghe_nghiep' => 'Kỹ sư luyện kim',
            'doi_thu' => 2,
            'loai_quan_he' => 'Chính',
            'trang_thai' => 'Còn sống',
            'avatar' => 'https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?auto=format&fit=crop&q=80&w=200',
        ]);

        $baHa3 = ThanhVien::create([
            'chi_nhanh_id' => $cnId3,
            'ho_ten' => 'Bùi Thị Hà',
            'ten_goi' => 'Bà Hà',
            'gioi_tinh' => 'Nữ',
            'ngay_sinh' => '1949-05-18',
            'noi_sinh' => 'Thanh Hóa',
            'nghe_nghiep' => 'Kinh doanh vải',
            'doi_thu' => 2,
            'loai_quan_he' => 'Vợ/Chồng',
            'spouse_of_id' => $ongManh->id,
            'trang_thai' => 'Còn sống',
            'avatar' => 'https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?auto=format&fit=crop&q=80&w=200',
        ]);

        VoChong::create([
            'chong_id' => $ongManh->id,
            'vo_id' => $baHa3->id,
            'ngay_cuoi' => '1970-08-15',
            'trang_thai' => 'Đang sống',
        ]);

        // 3. Lê Thị Hiền (Con gái cụ Phúc - cụ Huệ)
        $baHien3 = ThanhVien::create([
            'chi_nhanh_id' => $cnId3,
            'ho_ten' => 'Lê Thị Hiền',
            'ten_goi' => 'Bà Hiền',
            'gioi_tinh' => 'Nữ',
            'ngay_sinh' => '1947-11-10',
            'cha_id' => $cuPhuc->id,
            'me_id' => $cuHue->id,
            'noi_sinh' => 'Thanh Hóa',
            'nghe_nghiep' => 'Giáo viên',
            'doi_thu' => 2,
            'loai_quan_he' => 'Chính',
            'trang_thai' => 'Còn sống',
            'avatar' => 'https://images.unsplash.com/photo-1531746020798-e6953c6e8e04?auto=format&fit=crop&q=80&w=200',
        ]);

        $ongDuc3ConRe = ThanhVien::create([
            'chi_nhanh_id' => $cnId3,
            'ho_ten' => 'Phạm Minh Đức',
            'ten_goi' => 'Ông Đức',
            'gioi_tinh' => 'Nam',
            'ngay_sinh' => '1944-09-12',
            'noi_sinh' => 'Ninh Bình',
            'nghe_nghiep' => 'Kế toán trưởng',
            'doi_thu' => 2,
            'loai_quan_he' => 'Vợ/Chồng',
            'spouse_of_id' => $baHien3->id,
            'trang_thai' => 'Còn sống',
            'avatar' => 'https://images.unsplash.com/photo-1492562080023-ab3db95bfbce?auto=format&fit=crop&q=80&w=200',
        ]);

        VoChong::create([
            'chong_id' => $ongDuc3ConRe->id,
            'vo_id' => $baHien3->id,
            'ngay_cuoi' => '1969-12-20',
            'trang_thai' => 'Đang sống',
        ]);

        // ĐỜI 2: Con cụ Phúc & cụ Liên (Vợ 2)
        // 4. Lê Hữu Sơn (Con cụ Phúc - cụ Liên)
        $ongSon3 = ThanhVien::create([
            'chi_nhanh_id' => $cnId3,
            'ho_ten' => 'Lê Hữu Sơn',
            'ten_goi' => 'Ông Sơn',
            'gioi_tinh' => 'Nam',
            'ngay_sinh' => '1957-04-12',
            'cha_id' => $cuPhuc->id,
            'me_id' => $cuLien->id,
            'noi_sinh' => 'Thanh Hóa',
            'nghe_nghiep' => 'Kiến trúc sư',
            'doi_thu' => 2,
            'loai_quan_he' => 'Chính',
            'trang_thai' => 'Còn sống',
            'avatar' => 'https://images.unsplash.com/photo-1506794778202-cad84cf45f1d?auto=format&fit=crop&q=80&w=200',
        ]);

        $baMai3 = ThanhVien::create([
            'chi_nhanh_id' => $cnId3,
            'ho_ten' => 'Đặng Thị Mai',
            'ten_goi' => 'Bà Mai',
            'gioi_tinh' => 'Nữ',
            'ngay_sinh' => '1960-11-20',
            'noi_sinh' => 'Ninh Bình',
            'nghe_nghiep' => 'Dược sĩ',
            'doi_thu' => 2,
            'loai_quan_he' => 'Vợ/Chồng',
            'spouse_of_id' => $ongSon3->id,
            'trang_thai' => 'Còn sống',
            'avatar' => 'https://images.unsplash.com/photo-1534751516642-a131ffd103fd?auto=format&fit=crop&q=80&w=200',
        ]);

        VoChong::create([
            'chong_id' => $ongSon3->id,
            'vo_id' => $baMai3->id,
            'ngay_cuoi' => '1981-12-15',
            'trang_thai' => 'Đang sống',
        ]);

        // 5. Lê Hữu Tuấn (Con cụ Phúc - cụ Liên)
        $ongTuan3 = ThanhVien::create([
            'chi_nhanh_id' => $cnId3,
            'ho_ten' => 'Lê Hữu Tuấn',
            'ten_goi' => 'Ông Tuấn',
            'gioi_tinh' => 'Nam',
            'ngay_sinh' => '1960-08-14',
            'cha_id' => $cuPhuc->id,
            'me_id' => $cuLien->id,
            'noi_sinh' => 'Thanh Hóa',
            'nghe_nghiep' => 'Vận tải',
            'doi_thu' => 2,
            'loai_quan_he' => 'Chính',
            'trang_thai' => 'Còn sống',
            'avatar' => 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?auto=format&fit=crop&q=80&w=200',
        ]);

        $baMy3 = ThanhVien::create([
            'chi_nhanh_id' => $cnId3,
            'ho_ten' => 'Trần Thị Mỹ',
            'ten_goi' => 'Bà Mỹ',
            'gioi_tinh' => 'Nữ',
            'ngay_sinh' => '1964-10-18',
            'noi_sinh' => 'Thanh Hóa',
            'nghe_nghiep' => 'Buôn bán',
            'doi_thu' => 2,
            'loai_quan_he' => 'Vợ/Chồng',
            'spouse_of_id' => $ongTuan3->id,
            'trang_thai' => 'Còn sống',
            'avatar' => 'https://images.unsplash.com/photo-1544005313-94ddf0286df2?auto=format&fit=crop&q=80&w=200',
        ]);

        VoChong::create([
            'chong_id' => $ongTuan3->id,
            'vo_id' => $baMy3->id,
            'ngay_cuoi' => '1986-05-20',
            'trang_thai' => 'Đang sống',
        ]);

        // ĐỜI 3: Con ông Lê Hữu Đạt & bà Cúc (Vợ 1)
        // 1. Lê Hữu Trung (Chính)
        $anhTrung3 = ThanhVien::create([
            'chi_nhanh_id' => $cnId3,
            'ho_ten' => 'Lê Hữu Trung',
            'ten_goi' => 'Anh Trung',
            'gioi_tinh' => 'Nam',
            'ngay_sinh' => '1971-11-05',
            'cha_id' => $ongDat->id,
            'me_id' => $baCuc3->id,
            'noi_sinh' => 'Thanh Hóa',
            'nghe_nghiep' => 'Luật sư',
            'doi_thu' => 3,
            'loai_quan_he' => 'Chính',
            'trang_thai' => 'Còn sống',
            'ghi_chu' => 'Văn phòng luật sư uy tín tại Thanh Hóa.',
            'avatar' => 'https://images.unsplash.com/photo-1519085360753-af0119f7cbe7?auto=format&fit=crop&q=80&w=200',
        ]);

        $chiThuy3 = ThanhVien::create([
            'chi_nhanh_id' => $cnId3,
            'ho_ten' => 'Phạm Thị Thùy',
            'ten_goi' => 'Chị Thùy',
            'gioi_tinh' => 'Nữ',
            'ngay_sinh' => '1975-08-14',
            'noi_sinh' => 'Hà Nội',
            'nghe_nghiep' => 'Kiểm toán viên',
            'doi_thu' => 3,
            'loai_quan_he' => 'Vợ/Chồng',
            'spouse_of_id' => $anhTrung3->id,
            'trang_thai' => 'Còn sống',
            'avatar' => 'https://images.unsplash.com/photo-1544005313-94ddf0286df2?auto=format&fit=crop&q=80&w=200',
        ]);

        VoChong::create([
            'chong_id' => $anhTrung3->id,
            'vo_id' => $chiThuy3->id,
            'ngay_cuoi' => '1997-08-18',
            'trang_thai' => 'Đang sống',
        ]);

        // 2. Lê Thị Hoa (Chính)
        $chiHoa3 = ThanhVien::create([
            'chi_nhanh_id' => $cnId3,
            'ho_ten' => 'Lê Thị Hoa',
            'ten_goi' => 'Chị Hoa',
            'gioi_tinh' => 'Nữ',
            'ngay_sinh' => '1974-12-12',
            'cha_id' => $ongDat->id,
            'me_id' => $baCuc3->id,
            'noi_sinh' => 'Thanh Hóa',
            'nghe_nghiep' => 'Giáo viên Tiếng Anh',
            'doi_thu' => 3,
            'loai_quan_he' => 'Chính',
            'trang_thai' => 'Còn sống',
            'avatar' => 'https://images.unsplash.com/photo-1558203728-00f45181dd84?auto=format&fit=crop&q=80&w=200',
        ]);

        $anhLong3ConRe = ThanhVien::create([
            'chi_nhanh_id' => $cnId3,
            'ho_ten' => 'Trịnh Hoàng Long',
            'ten_goi' => 'Anh Long',
            'gioi_tinh' => 'Nam',
            'ngay_sinh' => '1971-04-18',
            'noi_sinh' => 'Thanh Hóa',
            'nghe_nghiep' => 'Công chức',
            'doi_thu' => 3,
            'loai_quan_he' => 'Vợ/Chồng',
            'spouse_of_id' => $chiHoa3->id,
            'trang_thai' => 'Còn sống',
            'avatar' => 'https://images.unsplash.com/photo-1537368910025-700350fe46c7?auto=format&fit=crop&q=80&w=200',
        ]);

        VoChong::create([
            'chong_id' => $anhLong3ConRe->id,
            'vo_id' => $chiHoa3->id,
            'ngay_cuoi' => '1996-09-12',
            'trang_thai' => 'Đang sống',
        ]);

        // ĐỜI 3: Con ông Lê Hữu Đạt & bà Hương (Vợ 2)
        // 3. Lê Hữu Khoa (Chính)
        $anhKhoa3 = ThanhVien::create([
            'chi_nhanh_id' => $cnId3,
            'ho_ten' => 'Lê Hữu Khoa',
            'ten_goi' => 'Anh Khoa',
            'gioi_tinh' => 'Nam',
            'ngay_sinh' => '1981-08-10',
            'cha_id' => $ongDat->id,
            'me_id' => $baHuong3->id,
            'noi_sinh' => 'Thanh Hóa',
            'nghe_nghiep' => 'Doanh nhân',
            'doi_thu' => 3,
            'loai_quan_he' => 'Chính',
            'trang_thai' => 'Còn sống',
            'avatar' => 'https://images.unsplash.com/photo-1506794778202-cad84cf45f1d?auto=format&fit=crop&q=80&w=200',
        ]);

        $chiBichNgoc3Dau = ThanhVien::create([
            'chi_nhanh_id' => $cnId3,
            'ho_ten' => 'Phạm Bích Ngọc',
            'ten_goi' => 'Chị Ngọc',
            'gioi_tinh' => 'Nữ',
            'ngay_sinh' => '1984-12-14',
            'noi_sinh' => 'Hải Dương',
            'nghe_nghiep' => 'Bác sĩ y học cổ truyền',
            'doi_thu' => 3,
            'loai_quan_he' => 'Vợ/Chồng',
            'spouse_of_id' => $anhKhoa3->id,
            'trang_thai' => 'Còn sống',
            'avatar' => 'https://images.unsplash.com/photo-1544005313-94ddf0286df2?auto=format&fit=crop&q=80&w=200',
        ]);

        VoChong::create([
            'chong_id' => $anhKhoa3->id,
            'vo_id' => $chiBichNgoc3Dau->id,
            'ngay_cuoi' => '2008-05-18',
            'trang_thai' => 'Đang sống',
        ]);

        // ĐỜI 3: Con ông Lê Hữu Mạnh & bà Hà
        // 4. Lê Hữu Bình (Chính)
        $anhBinh3 = ThanhVien::create([
            'chi_nhanh_id' => $cnId3,
            'ho_ten' => 'Lê Hữu Bình',
            'ten_goi' => 'Anh Bình',
            'gioi_tinh' => 'Nam',
            'ngay_sinh' => '1973-10-12',
            'cha_id' => $ongManh->id,
            'me_id' => $baHa3->id,
            'noi_sinh' => 'Thanh Hóa',
            'nghe_nghiep' => 'Quản lý dự án',
            'doi_thu' => 3,
            'loai_quan_he' => 'Chính',
            'trang_thai' => 'Còn sống',
            'avatar' => 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?auto=format&fit=crop&q=80&w=200',
        ]);

        $chiNgocHa3Dau = ThanhVien::create([
            'chi_nhanh_id' => $cnId3,
            'ho_ten' => 'Vũ Ngọc Hà',
            'ten_goi' => 'Chị Ngọc Hà',
            'gioi_tinh' => 'Nữ',
            'ngay_sinh' => '1977-05-18',
            'noi_sinh' => 'Thanh Hóa',
            'nghe_nghiep' => 'Hành chính nhân sự',
            'doi_thu' => 3,
            'loai_quan_he' => 'Vợ/Chồng',
            'spouse_of_id' => $anhBinh3->id,
            'trang_thai' => 'Còn sống',
            'avatar' => 'https://images.unsplash.com/photo-1534751516642-a131ffd103fd?auto=format&fit=crop&q=80&w=200',
        ]);

        VoChong::create([
            'chong_id' => $anhBinh3->id,
            'vo_id' => $chiNgocHa3Dau->id,
            'ngay_cuoi' => '1999-12-14',
            'trang_thai' => 'Đang sống',
        ]);

        // 5. Lê Thị Hồng (Chính)
        $chiHong3 = ThanhVien::create([
            'chi_nhanh_id' => $cnId3,
            'ho_ten' => 'Lê Thị Hồng',
            'ten_goi' => 'Chị Hồng',
            'gioi_tinh' => 'Nữ',
            'ngay_sinh' => '1976-08-20',
            'cha_id' => $ongManh->id,
            'me_id' => $baHa3->id,
            'noi_sinh' => 'Thanh Hóa',
            'nghe_nghiep' => 'Nội trợ',
            'doi_thu' => 3,
            'loai_quan_he' => 'Chính',
            'trang_thai' => 'Còn sống',
            'avatar' => 'https://images.unsplash.com/photo-1580489944761-15a19d654956?auto=format&fit=crop&q=80&w=200',
        ]);

        $anhDuyAnh3ConRe = ThanhVien::create([
            'chi_nhanh_id' => $cnId3,
            'ho_ten' => 'Nguyễn Duy Anh',
            'ten_goi' => 'Anh Duy Anh',
            'gioi_tinh' => 'Nam',
            'ngay_sinh' => '1972-04-12',
            'noi_sinh' => 'Nghệ An',
            'nghe_nghiep' => 'Kiến trúc sư',
            'doi_thu' => 3,
            'loai_quan_he' => 'Vợ/Chồng',
            'spouse_of_id' => $chiHong3->id,
            'trang_thai' => 'Còn sống',
            'avatar' => 'https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?auto=format&fit=crop&q=80&w=200',
        ]);

        VoChong::create([
            'chong_id' => $anhDuyAnh3ConRe->id,
            'vo_id' => $chiHong3->id,
            'ngay_cuoi' => '1998-10-18',
            'trang_thai' => 'Đang sống',
        ]);

        // ĐỜI 3: Con bà Lê Thị Hiền & ông Phạm Minh Đức
        // 6. Phạm Minh Khang (Chính)
        $anhKhang3 = ThanhVien::create([
            'chi_nhanh_id' => $cnId3,
            'ho_ten' => 'Phạm Minh Khang',
            'ten_goi' => 'Anh Khang',
            'gioi_tinh' => 'Nam',
            'ngay_sinh' => '1971-10-12',
            'cha_id' => $ongDuc3ConRe->id,
            'me_id' => $baHien3->id,
            'noi_sinh' => 'Thanh Hóa',
            'nghe_nghiep' => 'Cơ khí tàu thủy',
            'doi_thu' => 3,
            'loai_quan_he' => 'Chính',
            'trang_thai' => 'Còn sống',
            'avatar' => 'https://images.unsplash.com/photo-1506794778202-cad84cf45f1d?auto=format&fit=crop&q=80&w=200',
        ]);

        // 7. Phạm Thị Khánh An (Chính)
        $chiKhanhAn3 = ThanhVien::create([
            'chi_nhanh_id' => $cnId3,
            'ho_ten' => 'Phạm Thị Khánh An',
            'ten_goi' => 'Khánh An',
            'gioi_tinh' => 'Nữ',
            'ngay_sinh' => '1974-05-18',
            'cha_id' => $ongDuc3ConRe->id,
            'me_id' => $baHien3->id,
            'noi_sinh' => 'Thanh Hóa',
            'nghe_nghiep' => 'Thiết kế đồ họa',
            'doi_thu' => 3,
            'loai_quan_he' => 'Chính',
            'trang_thai' => 'Còn sống',
            'avatar' => 'https://images.unsplash.com/photo-1544005313-94ddf0286df2?auto=format&fit=crop&q=80&w=200',
        ]);

        // ĐỜI 3: Con ông Lê Hữu Sơn & bà Mai
        // 8. Lê Hữu Việt (Chính)
        $anhViet3 = ThanhVien::create([
            'chi_nhanh_id' => $cnId3,
            'ho_ten' => 'Lê Hữu Việt',
            'ten_goi' => 'Anh Việt',
            'gioi_tinh' => 'Nam',
            'ngay_sinh' => '1983-05-18',
            'cha_id' => $ongSon3->id,
            'me_id' => $baMai3->id,
            'noi_sinh' => 'Thanh Hóa',
            'nghe_nghiep' => 'Bác sĩ thẩm mỹ',
            'doi_thu' => 3,
            'loai_quan_he' => 'Chính',
            'trang_thai' => 'Còn sống',
            'avatar' => 'https://images.unsplash.com/photo-1560250097-0b93528c311a?auto=format&fit=crop&q=80&w=200',
        ]);

        $chiTuyet3 = ThanhVien::create([
            'chi_nhanh_id' => $cnId3,
            'ho_ten' => 'Trần Thị Tuyết',
            'ten_goi' => 'Chị Tuyết',
            'gioi_tinh' => 'Nữ',
            'ngay_sinh' => '1987-10-15',
            'noi_sinh' => 'Thanh Hóa',
            'nghe_nghiep' => 'Giáo viên hóa học',
            'doi_thu' => 3,
            'loai_quan_he' => 'Vợ/Chồng',
            'spouse_of_id' => $anhViet3->id,
            'trang_thai' => 'Còn sống',
            'avatar' => 'https://images.unsplash.com/photo-1580489944761-15a19d654956?auto=format&fit=crop&q=80&w=200',
        ]);

        VoChong::create([
            'chong_id' => $anhViet3->id,
            'vo_id' => $chiTuyet3->id,
            'ngay_cuoi' => '2011-09-20',
            'trang_thai' => 'Đang sống',
        ]);

        // 9. Lê Thị Thanh Hương (Chính)
        $chiHuong3 = ThanhVien::create([
            'chi_nhanh_id' => $cnId3,
            'ho_ten' => 'Lê Thị Thanh Hương',
            'ten_goi' => 'Chị Hương',
            'gioi_tinh' => 'Nữ',
            'ngay_sinh' => '1986-09-12',
            'cha_id' => $ongSon3->id,
            'me_id' => $baMai3->id,
            'noi_sinh' => 'Thanh Hóa',
            'nghe_nghiep' => 'Kế toán ngân hàng',
            'doi_thu' => 3,
            'loai_quan_he' => 'Chính',
            'trang_thai' => 'Còn sống',
            'avatar' => 'https://images.unsplash.com/photo-1544005313-94ddf0286df2?auto=format&fit=crop&q=80&w=200',
        ]);

        // ĐỜI 3: Con ông Lê Hữu Tuấn & bà Mỹ
        // 10. Lê Hữu Nam (Chính)
        $anhNam3 = ThanhVien::create([
            'chi_nhanh_id' => $cnId3,
            'ho_ten' => 'Lê Hữu Nam',
            'ten_goi' => 'Anh Nam',
            'gioi_tinh' => 'Nam',
            'ngay_sinh' => '1988-10-12',
            'cha_id' => $ongTuan3->id,
            'me_id' => $baMy3->id,
            'noi_sinh' => 'Thanh Hóa',
            'nghe_nghiep' => 'Kinh doanh vận tải',
            'doi_thu' => 3,
            'loai_quan_he' => 'Chính',
            'trang_thai' => 'Còn sống',
            'avatar' => 'https://images.unsplash.com/photo-1506794778202-cad84cf45f1d?auto=format&fit=crop&q=80&w=200',
        ]);

        $chiDieu3Dau = ThanhVien::create([
            'chi_nhanh_id' => $cnId3,
            'ho_ten' => 'Lê Thị Diệu',
            'ten_goi' => 'Chị Diệu',
            'gioi_tinh' => 'Nữ',
            'ngay_sinh' => '1991-04-18',
            'noi_sinh' => 'Thanh Hóa',
            'nghe_nghiep' => 'Nội trợ',
            'doi_thu' => 3,
            'loai_quan_he' => 'Vợ/Chồng',
            'spouse_of_id' => $anhNam3->id,
            'trang_thai' => 'Còn sống',
            'avatar' => 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&q=80&w=200',
        ]);

        VoChong::create([
            'chong_id' => $anhNam3->id,
            'vo_id' => $chiDieu3Dau->id,
            'ngay_cuoi' => '2014-11-20',
            'trang_thai' => 'Đang sống',
        ]);

        // 11. Lê Thị Thảo (Chính)
        $chiThao3 = ThanhVien::create([
            'chi_nhanh_id' => $cnId3,
            'ho_ten' => 'Lê Thị Thảo',
            'ten_goi' => 'Chị Thảo',
            'gioi_tinh' => 'Nữ',
            'ngay_sinh' => '1991-12-05',
            'cha_id' => $ongTuan3->id,
            'me_id' => $baMy3->id,
            'noi_sinh' => 'Thanh Hóa',
            'nghe_nghiep' => 'Văn thư',
            'doi_thu' => 3,
            'loai_quan_he' => 'Chính',
            'trang_thai' => 'Còn sống',
            'avatar' => 'https://images.unsplash.com/photo-1544005313-94ddf0286df2?auto=format&fit=crop&q=80&w=200',
        ]);

        // ĐỜI 4: Con anh Lê Hữu Trung & chị Thùy
        $anhLam3 = ThanhVien::create([
            'chi_nhanh_id' => $cnId3,
            'ho_ten' => 'Lê Hữu Lâm',
            'ten_goi' => 'Anh Lâm',
            'gioi_tinh' => 'Nam',
            'ngay_sinh' => '1999-03-12',
            'cha_id' => $anhTrung3->id,
            'me_id' => $chiThuy3->id,
            'noi_sinh' => 'Thanh Hóa',
            'nghe_nghiep' => 'Kỹ sư cơ điện tử',
            'doi_thu' => 4,
            'loai_quan_he' => 'Chính',
            'trang_thai' => 'Còn sống',
            'avatar' => 'https://images.unsplash.com/photo-1506794778202-cad84cf45f1d?auto=format&fit=crop&q=80&w=200',
        ]);

        $chiAnh3 = ThanhVien::create([
            'chi_nhanh_id' => $cnId3,
            'ho_ten' => 'Vũ Ngọc Anh',
            'ten_goi' => 'Chị Ngọc Anh',
            'gioi_tinh' => 'Nữ',
            'ngay_sinh' => '2002-06-20',
            'noi_sinh' => 'Thanh Hóa',
            'nghe_nghiep' => 'Thiết kế đồ họa',
            'doi_thu' => 4,
            'loai_quan_he' => 'Vợ/Chồng',
            'spouse_of_id' => $anhLam3->id,
            'trang_thai' => 'Còn sống',
            'avatar' => 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&q=80&w=200',
        ]);

        VoChong::create([
            'chong_id' => $anhLam3->id,
            'vo_id' => $chiAnh3->id,
            'ngay_cuoi' => '2024-05-18',
            'trang_thai' => 'Đang sống',
        ]);

        $chiDiep3 = ThanhVien::create([
            'chi_nhanh_id' => $cnId3,
            'ho_ten' => 'Lê Thị Diệp',
            'ten_goi' => 'Chị Diệp',
            'gioi_tinh' => 'Nữ',
            'ngay_sinh' => '2002-08-14',
            'cha_id' => $anhTrung3->id,
            'me_id' => $chiThuy3->id,
            'noi_sinh' => 'Thanh Hóa',
            'nghe_nghiep' => 'Sinh viên Ngoại thương',
            'doi_thu' => 4,
            'loai_quan_he' => 'Chính',
            'trang_thai' => 'Còn sống',
            'avatar' => 'https://images.unsplash.com/photo-1544005313-94ddf0286df2?auto=format&fit=crop&q=80&w=200',
        ]);

        $anhTuanAnh3 = ThanhVien::create([
            'chi_nhanh_id' => $cnId3,
            'ho_ten' => 'Lê Hữu Tuấn Anh',
            'ten_goi' => 'Anh Tuấn Anh',
            'gioi_tinh' => 'Nam',
            'ngay_sinh' => '2005-04-10',
            'cha_id' => $anhTrung3->id,
            'me_id' => $chiThuy3->id,
            'noi_sinh' => 'Thanh Hóa',
            'nghe_nghiep' => 'Lập trình viên',
            'doi_thu' => 4,
            'loai_quan_he' => 'Chính',
            'trang_thai' => 'Còn sống',
            'avatar' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?auto=format&fit=crop&q=80&w=200',
        ]);

        // ĐỜI 4: Con anh Trịnh Hoàng Long & chị Lê Thị Hoa
        $anhNam3Re = ThanhVien::create([
            'chi_nhanh_id' => $cnId3,
            'ho_ten' => 'Trịnh Hoàng Nam',
            'ten_goi' => 'Hoàng Nam',
            'gioi_tinh' => 'Nam',
            'ngay_sinh' => '1999-05-18',
            'cha_id' => $anhLong3ConRe->id,
            'me_id' => $chiHoa3->id,
            'noi_sinh' => 'Thanh Hóa',
            'nghe_nghiep' => 'Bác sĩ trẻ',
            'doi_thu' => 4,
            'loai_quan_he' => 'Chính',
            'trang_thai' => 'Còn sống',
            'avatar' => 'https://images.unsplash.com/photo-1506794778202-cad84cf45f1d?auto=format&fit=crop&q=80&w=200',
        ]);

        $chiHang3Re = ThanhVien::create([
            'chi_nhanh_id' => $cnId3,
            'ho_ten' => 'Trịnh Thị Hằng',
            'ten_goi' => 'Thu Hằng',
            'gioi_tinh' => 'Nữ',
            'ngay_sinh' => '2002-09-22',
            'cha_id' => $anhLong3ConRe->id,
            'me_id' => $chiHoa3->id,
            'noi_sinh' => 'Thanh Hóa',
            'nghe_nghiep' => 'Sinh viên sư phạm',
            'doi_thu' => 4,
            'loai_quan_he' => 'Chính',
            'trang_thai' => 'Còn sống',
            'avatar' => 'https://images.unsplash.com/photo-1544005313-94ddf0286df2?auto=format&fit=crop&q=80&w=200',
        ]);

        // ĐỜI 4: Con anh Lê Hữu Khoa & chị Bích Ngọc (Vợ 2 của Đạt)
        $beKhôi3 = ThanhVien::create([
            'chi_nhanh_id' => $cnId3,
            'ho_ten' => 'Lê Hữu Minh Khôi',
            'ten_goi' => 'Cháu Minh Khôi',
            'gioi_tinh' => 'Nam',
            'ngay_sinh' => '2010-04-10',
            'cha_id' => $anhKhoa3->id,
            'me_id' => $chiBichNgoc3Dau->id,
            'noi_sinh' => 'Thanh Hóa',
            'nghe_nghiep' => 'Học sinh',
            'doi_thu' => 4,
            'loai_quan_he' => 'Chính',
            'trang_thai' => 'Còn sống',
            'avatar' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?auto=format&fit=crop&q=80&w=200',
        ]);

        $beAn3 = ThanhVien::create([
            'chi_nhanh_id' => $cnId3,
            'ho_ten' => 'Lê Thị Thu An',
            'ten_goi' => 'Cháu Thu An',
            'gioi_tinh' => 'Nữ',
            'ngay_sinh' => '2013-08-15',
            'cha_id' => $anhKhoa3->id,
            'me_id' => $chiBichNgoc3Dau->id,
            'noi_sinh' => 'Thanh Hóa',
            'nghe_nghiep' => 'Học sinh',
            'doi_thu' => 4,
            'loai_quan_he' => 'Chính',
            'trang_thai' => 'Còn sống',
            'avatar' => 'https://images.unsplash.com/photo-1544005313-94ddf0286df2?auto=format&fit=crop&q=80&w=200',
        ]);

        // ĐỜI 4: Con anh Lê Hữu Bình & chị Hà
        $beHuy3 = ThanhVien::create([
            'chi_nhanh_id' => $cnId3,
            'ho_ten' => 'Lê Hữu Huy',
            'ten_goi' => 'Cháu Huy',
            'gioi_tinh' => 'Nam',
            'ngay_sinh' => '2001-09-12',
            'cha_id' => $anhBinh3->id,
            'me_id' => $chiNgocHa3Dau->id,
            'noi_sinh' => 'Thanh Hóa',
            'nghe_nghiep' => 'Sinh viên',
            'doi_thu' => 4,
            'loai_quan_he' => 'Chính',
            'trang_thai' => 'Còn sống',
            'avatar' => 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?auto=format&fit=crop&q=80&w=200',
        ]);

        $beLinChi3 = ThanhVien::create([
            'chi_nhanh_id' => $cnId3,
            'ho_ten' => 'Lê Thị Linh Chi',
            'ten_goi' => 'Cháu Linh Chi',
            'gioi_tinh' => 'Nữ',
            'ngay_sinh' => '2004-11-15',
            'cha_id' => $anhBinh3->id,
            'me_id' => $chiNgocHa3Dau->id,
            'noi_sinh' => 'Thanh Hóa',
            'nghe_nghiep' => 'Sinh viên đại học',
            'doi_thu' => 4,
            'loai_quan_he' => 'Chính',
            'trang_thai' => 'Còn sống',
            'avatar' => 'https://images.unsplash.com/photo-1544005313-94ddf0286df2?auto=format&fit=crop&q=80&w=200',
        ]);

        // ĐỜI 4: Con anh Lê Hữu Việt & chị Tuyết
        $beBach3 = ThanhVien::create([
            'chi_nhanh_id' => $cnId3,
            'ho_ten' => 'Lê Hữu Bách',
            'ten_goi' => 'Cháu Bách',
            'gioi_tinh' => 'Nam',
            'ngay_sinh' => '2014-07-15',
            'cha_id' => $anhViet3->id,
            'me_id' => $chiTuyet3->id,
            'noi_sinh' => 'Thanh Hóa',
            'nghe_nghiep' => 'Học sinh',
            'doi_thu' => 4,
            'loai_quan_he' => 'Chính',
            'trang_thai' => 'Còn sống',
            'avatar' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?auto=format&fit=crop&q=80&w=200',
        ]);

        $beLinh3 = ThanhVien::create([
            'chi_nhanh_id' => $cnId3,
            'ho_ten' => 'Lê Thị Linh',
            'ten_goi' => 'Cháu Linh',
            'gioi_tinh' => 'Nữ',
            'ngay_sinh' => '2017-09-12',
            'cha_id' => $anhViet3->id,
            'me_id' => $chiTuyet3->id,
            'noi_sinh' => 'Thanh Hóa',
            'nghe_nghiep' => 'Học sinh',
            'doi_thu' => 4,
            'loai_quan_he' => 'Chính',
            'trang_thai' => 'Còn sống',
            'avatar' => 'https://images.unsplash.com/photo-1544005313-94ddf0286df2?auto=format&fit=crop&q=80&w=200',
        ]);

        $beGiaBao3 = ThanhVien::create([
            'chi_nhanh_id' => $cnId3,
            'ho_ten' => 'Lê Hữu Gia Bảo',
            'ten_goi' => 'Bé Gia Bảo',
            'gioi_tinh' => 'Nam',
            'ngay_sinh' => '2020-12-14',
            'cha_id' => $anhViet3->id,
            'me_id' => $chiTuyet3->id,
            'noi_sinh' => 'Thanh Hóa',
            'nghe_nghiep' => 'Học sinh mẫu giáo',
            'doi_thu' => 4,
            'loai_quan_he' => 'Chính',
            'trang_thai' => 'Còn sống',
            'avatar' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?auto=format&fit=crop&q=80&w=200',
        ]);

        // ĐỜI 4: Con anh Lê Hữu Nam & chị Diệu
        $beDuyAnh3 = ThanhVien::create([
            'chi_nhanh_id' => $cnId3,
            'ho_ten' => 'Lê Hữu Duy Anh',
            'ten_goi' => 'Bé Duy Anh',
            'gioi_tinh' => 'Nam',
            'ngay_sinh' => '2016-05-18',
            'cha_id' => $anhNam3->id,
            'me_id' => $chiDieu3Dau->id,
            'noi_sinh' => 'Thanh Hóa',
            'nghe_nghiep' => 'Học sinh',
            'doi_thu' => 4,
            'loai_quan_he' => 'Chính',
            'trang_thai' => 'Còn sống',
            'avatar' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?auto=format&fit=crop&q=80&w=200',
        ]);

        $beMyLinh3 = ThanhVien::create([
            'chi_nhanh_id' => $cnId3,
            'ho_ten' => 'Lê Nguyễn Mỹ Linh',
            'ten_goi' => 'Bé Mỹ Linh',
            'gioi_tinh' => 'Nữ',
            'ngay_sinh' => '2018-09-22',
            'cha_id' => $anhNam3->id,
            'me_id' => $chiDieu3Dau->id,
            'noi_sinh' => 'Thanh Hóa',
            'nghe_nghiep' => 'Học sinh tiểu học',
            'doi_thu' => 4,
            'loai_quan_he' => 'Chính',
            'trang_thai' => 'Còn sống',
            'avatar' => 'https://images.unsplash.com/photo-1544005313-94ddf0286df2?auto=format&fit=crop&q=80&w=200',
        ]);

        // ĐỜI 5: Con anh Lâm & chị Anh
        $beKhoa3 = ThanhVien::create([
            'chi_nhanh_id' => $cnId3,
            'ho_ten' => 'Lê Hữu Khoa',
            'ten_goi' => 'Bé Khoa',
            'gioi_tinh' => 'Nam',
            'ngay_sinh' => '2026-02-14',
            'cha_id' => $anhLam3->id,
            'me_id' => $chiAnh3->id,
            'noi_sinh' => 'Thanh Hóa',
            'nghe_nghiep' => 'Trẻ sơ sinh',
            'doi_thu' => 5,
            'loai_quan_he' => 'Chính',
            'trang_thai' => 'Còn sống',
            'avatar' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?auto=format&fit=crop&q=80&w=200',
        ]);

        $beGiaHung3 = ThanhVien::create([
            'chi_nhanh_id' => $cnId3,
            'ho_ten' => 'Lê Hữu Gia Hưng',
            'ten_goi' => 'Bé Gia Hưng',
            'gioi_tinh' => 'Nam',
            'ngay_sinh' => '2028-04-12',
            'cha_id' => $anhLam3->id,
            'me_id' => $chiAnh3->id,
            'noi_sinh' => 'Thanh Hóa',
            'nghe_nghiep' => 'Trẻ sơ sinh',
            'doi_thu' => 5,
            'loai_quan_he' => 'Chính',
            'trang_thai' => 'Còn sống',
            'avatar' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?auto=format&fit=crop&q=80&w=200',
        ]);

        // Tự động sinh dữ liệu từ đời 6 đến đời 10 cho dòng họ Lê Hữu
        self::generateGenerations6To10($cnId3, 'Lê Hữu', 'Lê Thị');
    }

    /**
     * Tự động sinh dữ liệu từ đời 6 đến đời 10
     */
    private static function generateGenerations6To10($chiNhanhId, $malePrefix, $femalePrefix)
    {
        // 1. Đảm bảo các đời tộc họ đã tồn tại trong DB (nếu chưa có thì tạo)
        for ($i = 6; $i <= 10; $i++) {
            $exists = DoiTocHo::where('chi_nhanh_id', $chiNhanhId)->where('so_doi', $i)->exists();
            if (!$exists) {
                DoiTocHo::create([
                    'so_doi' => $i,
                    'ten_doi' => "Thế Hệ Thứ " . $i,
                    'mo_ta' => "Thế hệ thứ " . $i . " của dòng họ.",
                    'chi_nhanh_id' => $chiNhanhId,
                    'trang_thai' => 'Hoạt động'
                ]);
            }
        }

        // 2. Lấy tất cả thành viên thuộc nhánh chính ở đời 5
        $parents = ThanhVien::where('chi_nhanh_id', $chiNhanhId)
            ->where('doi_thu', 5)
            ->where('loai_quan_he', 'Chính')
            ->get();

        $firstNamesNam = ['Bình', 'Chiến', 'Danh', 'Duy', 'Gia', 'Hải', 'Hiếu', 'Hoàng', 'Hùng', 'Huy', 'Khánh', 'Khoa', 'Lâm', 'Long', 'Minh', 'Nam', 'Nhân', 'Phúc', 'Quân', 'Quốc', 'Sơn', 'Tân', 'Thắng', 'Thành', 'Thiên', 'Thịnh', 'Tiến', 'Toàn', 'Trung', 'Tú', 'Tuấn', 'Việt', 'Vinh', 'Uy'];
        $firstNamesNu = ['An', 'Anh', 'Cúc', 'Chi', 'Diệp', 'Dung', 'Hà', 'Hằng', 'Hạnh', 'Hoa', 'Hồng', 'Hương', 'Khánh', 'Lan', 'Liên', 'Linh', 'Mai', 'My', 'Ngọc', 'Nga', 'Oanh', 'Phương', 'Quỳnh', 'Thảo', 'Thanh', 'Thu', 'Thủy', 'Trang', 'Trinh', 'Tuyết', 'Vân', 'Vy', 'Yến'];
        $spouseLastNames = ['Nguyễn', 'Trần', 'Lê', 'Phạm', 'Hoàng', 'Phan', 'Vũ', 'Võ', 'Đặng', 'Bùi', 'Đỗ', 'Hồ', 'Ngô', 'Dương', 'Lý'];
        $middleNamesNam = ['Văn', 'Minh', 'Hữu', 'Đức', 'Quốc', 'Anh'];
        $middleNamesNu = ['Thị', 'Thu', 'Ngọc', 'Kiều', 'Như', 'Bích'];

        foreach ($parents as $parent) {
            $currParent = $parent;

            for ($gen = 5; $gen <= 9; $gen++) {
                // Tìm hoặc tạo vợ/chồng cho parent hiện tại
                $spouse = ThanhVien::where('spouse_of_id', $currParent->id)->first();
                if (!$spouse) {
                    $isParentMale = ($currParent->gioi_tinh === 'Nam');
                    $spouseGender = $isParentMale ? 'Nữ' : 'Nam';
                    
                    $lastName = $spouseLastNames[array_rand($spouseLastNames)];
                    if ($spouseGender === 'Nam') {
                        $middleName = $middleNamesNam[array_rand($middleNamesNam)];
                        $firstName = $firstNamesNam[array_rand($firstNamesNam)];
                        $spouseName = "{$lastName} {$middleName} {$firstName}";
                    } else {
                        $middleName = $middleNamesNu[array_rand($middleNamesNu)];
                        $firstName = $firstNamesNu[array_rand($firstNamesNu)];
                        $spouseName = "{$lastName} {$middleName} {$firstName}";
                    }

                    $parentBirthYear = 2020;
                    if ($currParent->ngay_sinh) {
                        $parentBirthYear = intval(substr($currParent->ngay_sinh, 0, 4));
                    }
                    $spouseBirthYear = $parentBirthYear + rand(-3, 3);
                    $spouseBirthDate = sprintf("%04d-%02d-%02d", $spouseBirthYear, rand(1, 12), rand(1, 28));

                    $spouse = ThanhVien::create([
                        'chi_nhanh_id' => $chiNhanhId,
                        'ho_ten' => $spouseName,
                        'ten_goi' => $firstName,
                        'gioi_tinh' => $spouseGender,
                        'ngay_sinh' => $spouseBirthDate,
                        'noi_sinh' => $currParent->noi_sinh ?: 'Hà Nội',
                        'nghe_nghiep' => 'Kinh doanh',
                        'doi_thu' => $currParent->doi_thu,
                        'loai_quan_he' => 'Vợ/Chồng',
                        'spouse_of_id' => $currParent->id,
                        'trang_thai' => 'Còn sống',
                        'ghi_chu' => "Vợ/chồng của {$currParent->ho_ten}",
                        'avatar' => $spouseGender === 'Nam' 
                            ? 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?auto=format&fit=crop&q=80&w=200'
                            : 'https://images.unsplash.com/photo-1494790108377-be9c29b29330?auto=format&fit=crop&q=80&w=200'
                    ]);

                    $chongId = $isParentMale ? $currParent->id : $spouse->id;
                    $voId = $isParentMale ? $spouse->id : $currParent->id;
                    VoChong::create([
                        'chong_id' => $chongId,
                        'vo_id' => $voId,
                        'ngay_cuoi' => sprintf("%04d-%02d-%02d", $parentBirthYear + rand(20, 25), rand(1, 12), rand(1, 28)),
                        'trang_thai' => 'Đang sống'
                    ]);
                }

                // Sinh ra 2 con ở đời tiếp theo
                $parentBirthYear = 2020;
                if ($currParent->ngay_sinh) {
                    $parentBirthYear = intval(substr($currParent->ngay_sinh, 0, 4));
                }
                $childBirthYear = $parentBirthYear + rand(24, 28);
                
                // Con 1: Nam (Tiếp tục nối dõi)
                $c1FirstName = $firstNamesNam[array_rand($firstNamesNam)];
                $c1Name = "{$malePrefix} {$c1FirstName}";
                $c1BirthDate = sprintf("%04d-%02d-%02d", $childBirthYear, rand(1, 12), rand(1, 28));

                $child1 = ThanhVien::create([
                    'chi_nhanh_id' => $chiNhanhId,
                    'ho_ten' => $c1Name,
                    'ten_goi' => $c1FirstName,
                    'gioi_tinh' => 'Nam',
                    'ngay_sinh' => $c1BirthDate,
                    'noi_sinh' => $currParent->noi_sinh ?: 'Hà Nội',
                    'nghe_nghiep' => 'Học sinh/Sinh viên',
                    'doi_thu' => $gen + 1,
                    'loai_quan_he' => 'Chính',
                    'cha_id' => ($currParent->gioi_tinh === 'Nam') ? $currParent->id : $spouse->id,
                    'me_id' => ($currParent->gioi_tinh === 'Nữ') ? $currParent->id : $spouse->id,
                    'trang_thai' => 'Còn sống',
                    'ghi_chu' => "Con thứ nhất đời " . ($gen + 1),
                    'avatar' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?auto=format&fit=crop&q=80&w=200'
                ]);

                // Con 2: Nữ
                $c2FirstName = $firstNamesNu[array_rand($firstNamesNu)];
                $c2Name = "{$femalePrefix} {$c2FirstName}";
                $c2BirthDate = sprintf("%04d-%02d-%02d", $childBirthYear + rand(1, 3), rand(1, 12), rand(1, 28));

                $child2 = ThanhVien::create([
                    'chi_nhanh_id' => $chiNhanhId,
                    'ho_ten' => $c2Name,
                    'ten_goi' => $c2FirstName,
                    'gioi_tinh' => 'Nữ',
                    'ngay_sinh' => $c2BirthDate,
                    'noi_sinh' => $currParent->noi_sinh ?: 'Hà Nội',
                    'nghe_nghiep' => 'Học sinh/Sinh viên',
                    'doi_thu' => $gen + 1,
                    'loai_quan_he' => 'Chính',
                    'cha_id' => ($currParent->gioi_tinh === 'Nam') ? $currParent->id : $spouse->id,
                    'me_id' => ($currParent->gioi_tinh === 'Nữ') ? $currParent->id : $spouse->id,
                    'trang_thai' => 'Còn sống',
                    'ghi_chu' => "Con thứ hai đời " . ($gen + 1),
                    'avatar' => 'https://images.unsplash.com/photo-1544005313-94ddf0286df2?auto=format&fit=crop&q=80&w=200'
                ]);

                // Gán cha mẹ mới cho vòng lặp tiếp theo
                $currParent = $child1;
            }
        }
    }
}
