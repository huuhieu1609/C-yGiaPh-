<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Models\NguoiDung;
use App\Models\ChiNhanh;
use App\Models\DoiTocHo;
use App\Models\ThanhVien;
use App\Models\VoChong;
use App\Models\DoiTac;

class DoiTacSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Tránh trùng lặp tài khoản và chi nhánh đối tác
        $existingUser = NguoiDung::where('email', 'doitac@master.com')->first();
        if ($existingUser) {
            // Xóa sạch dữ liệu thành viên & chi nhánh cũ của đối tác này để tạo lại sạch sẽ
            $oldBranchIds = ChiNhanh::where('id_nguoi_dung', $existingUser->id)->pluck('id');
            ThanhVien::whereIn('chi_nhanh_id', $oldBranchIds)->delete();
            DoiTocHo::whereIn('chi_nhanh_id', $oldBranchIds)->delete();
            ChiNhanh::where('id_nguoi_dung', $existingUser->id)->delete();
            DoiTac::where('id_nguoi_dung', $existingUser->id)->delete();
            $existingUser->delete();
        }

        // 2. Tạo tài khoản đối tác
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
            'ten_goi' => 'Gói Đối Tác 5 Năm',
            'so_tien' => 5000000,
            'ngay_bat_dau' => now(),
            'ngay_ket_thuc' => now()->addYears(5),
            'trang_thai' => 1,
        ]);

        // 3. Tạo Chi Nhánh (Cây Gia Phả Dòng Họ Nguyễn Đức)
        $chiNhanh = ChiNhanh::create([
            'ten_chi' => 'Dòng Họ Nguyễn Đức - Hà Nội',
            'mo_ta' => 'Chi Nhánh Nguyễn Đức gốc Thạch Thất, định cư lâu đời tại thủ đô Hà Nội. Gia phả truyền thừa qua nhiều thế hệ hiếu học và thành danh.',
            'id_nguoi_dung' => $userId,
        ]);

        $cnId = $chiNhanh->id;

        // 4. Tạo các đời tộc họ (Đời 1 đến Đời 5) cho chi nhánh này
        DoiTocHo::create(['so_doi' => 1, 'ten_doi' => 'Thủy Tổ Khai Sáng', 'mo_ta' => 'Thế hệ đầu tiên định cư tại Thạch Thất', 'chi_nhanh_id' => $cnId]);
        DoiTocHo::create(['so_doi' => 2, 'ten_doi' => 'Thế Hệ Tiếp Bước', 'mo_ta' => 'Giai đoạn kháng chiến cứu nước', 'chi_nhanh_id' => $cnId]);
        DoiTocHo::create(['so_doi' => 3, 'ten_doi' => 'Thế Hệ Đổi Mới', 'mo_ta' => 'Xây dựng và phục hưng dòng họ', 'chi_nhanh_id' => $cnId]);
        DoiTocHo::create(['so_doi' => 4, 'ten_doi' => 'Thế Hệ Hội Nhập', 'mo_ta' => 'Thế hệ hiện đại năng động', 'chi_nhanh_id' => $cnId]);
        DoiTocHo::create(['so_doi' => 5, 'ten_doi' => 'Thế Hệ Tương Lai', 'mo_ta' => 'Ươm mầm tài năng trẻ dòng họ', 'chi_nhanh_id' => $cnId]);

        // 5. Thêm dữ liệu 5 đời thành viên chân thực
        // ------------------ ĐỜI 1 ------------------
        $cuOng = ThanhVien::create([
            'chi_nhanh_id' => $cnId,
            'ho_ten' => 'Nguyễn Đức Cường',
            'ten_goi' => 'Cụ Cường',
            'gioi_tinh' => 'Nam',
            'ngay_sinh' => '1915-05-10',
            'ngay_mat' => '1998-11-20',
            'noi_sinh' => 'Hà Tây',
            'nghe_nghiep' => 'Lương y',
            'doi_thu' => 1,
            'loai_quan_he' => 'Chính',
            'trang_thai' => 'Đã mất',
            'ghi_chu' => 'Cụ thủy tổ có công lập gia đình tại Thạch Thất, làm thầy thuốc nam bốc thuốc cứu người, sống thanh bạch, mẫu mực.',
            'avatar' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?auto=format&fit=crop&q=80&w=200'
        ]);

        $cuBa = ThanhVien::create([
            'chi_nhanh_id' => $cnId,
            'ho_ten' => 'Phạm Thị Nghĩa',
            'ten_goi' => 'Cụ Nghĩa',
            'gioi_tinh' => 'Nữ',
            'ngay_sinh' => '1918-08-15',
            'ngay_mat' => '2002-04-10',
            'noi_sinh' => 'Nam Định',
            'nghe_nghiep' => 'Làm nông',
            'doi_thu' => 1,
            'loai_quan_he' => 'Vợ/Chồng',
            'spouse_of_id' => $cuOng->id,
            'trang_thai' => 'Đã mất',
            'ghi_chu' => 'Người vợ hiền tần tảo, cùng cụ ông khai khẩn đất đai, chăm lo nuôi dạy các con thành tài.',
            'avatar' => 'https://images.unsplash.com/photo-1544005313-94ddf0286df2?auto=format&fit=crop&q=80&w=200'
        ]);

        VoChong::create([
            'chong_id' => $cuOng->id,
            'vo_id' => $cuBa->id,
            'ngay_cuoi' => '1937-01-15',
            'trang_thai' => 'Qua đời'
        ]);

        // ------------------ ĐỜI 2 ------------------
        // Con cả: Nguyễn Đức Hùng
        $ongHung = ThanhVien::create([
            'chi_nhanh_id' => $cnId,
            'ho_ten' => 'Nguyễn Đức Hùng',
            'ten_goi' => 'Ông Hùng',
            'gioi_tinh' => 'Nam',
            'ngay_sinh' => '1942-03-12',
            'cha_id' => $cuOng->id,
            'me_id' => $cuBa->id,
            'noi_sinh' => 'Hà Nội',
            'nghe_nghiep' => 'Đại tá quân đội',
            'doi_thu' => 2,
            'loai_quan_he' => 'Chính',
            'trang_thai' => 'Còn sống',
            'ghi_chu' => 'Tham gia kháng chiến chống Mỹ, là tấm gương sáng về tinh thần kỷ luật và cống hiến cho con cháu.',
            'avatar' => 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?auto=format&fit=crop&q=80&w=200'
        ]);

        $baMai = ThanhVien::create([
            'chi_nhanh_id' => $cnId,
            'ho_ten' => 'Lê Thị Mai',
            'ten_goi' => 'Bà Mai',
            'gioi_tinh' => 'Nữ',
            'ngay_sinh' => '1945-09-22',
            'noi_sinh' => 'Thái Bình',
            'nghe_nghiep' => 'Giáo viên ưu tú',
            'doi_thu' => 2,
            'loai_quan_he' => 'Vợ/Chồng',
            'spouse_of_id' => $ongHung->id,
            'trang_thai' => 'Còn sống',
            'ghi_chu' => 'Cựu hiệu trưởng trường THCS Thạch Thất, người vun đắp tình yêu con chữ cho nhiều thế hệ.',
            'avatar' => 'https://images.unsplash.com/photo-1494790108377-be9c29b29330?auto=format&fit=crop&q=80&w=200'
        ]);

        VoChong::create([
            'chong_id' => $ongHung->id,
            'vo_id' => $baMai->id,
            'ngay_cuoi' => '1966-05-10',
            'trang_thai' => 'Đang sống'
        ]);

        // Con gái thứ hai: Nguyễn Thị Lan
        $baLan = ThanhVien::create([
            'chi_nhanh_id' => $cnId,
            'ho_ten' => 'Nguyễn Thị Lan',
            'ten_goi' => 'Bà Lan',
            'gioi_tinh' => 'Nữ',
            'ngay_sinh' => '1945-11-05',
            'cha_id' => $cuOng->id,
            'me_id' => $cuBa->id,
            'noi_sinh' => 'Hà Nội',
            'nghe_nghiep' => 'Kỹ sư nông nghiệp',
            'doi_thu' => 2,
            'loai_quan_he' => 'Chính',
            'trang_thai' => 'Còn sống',
            'ghi_chu' => 'Nghỉ hưu định cư tại Hà Nội, hoạt động năng nổ trong hội phụ nữ địa phương.',
            'avatar' => 'https://images.unsplash.com/photo-1438761681033-6461ffad8d80?auto=format&fit=crop&q=80&w=200'
        ]);

        // Con thứ ba: Nguyễn Đức Dũng
        $ongDung = ThanhVien::create([
            'chi_nhanh_id' => $cnId,
            'ho_ten' => 'Nguyễn Đức Dũng',
            'ten_goi' => 'Ông Dũng',
            'gioi_tinh' => 'Nam',
            'ngay_sinh' => '1948-07-19',
            'cha_id' => $cuOng->id,
            'me_id' => $cuBa->id,
            'noi_sinh' => 'Hà Tây',
            'nghe_nghiep' => 'Kỹ sư cơ khí',
            'doi_thu' => 2,
            'loai_quan_he' => 'Chính',
            'trang_thai' => 'Còn sống',
            'ghi_chu' => 'Làm việc tại Tổng công ty Đường sắt Việt Nam, đam mê máy móc và làm thơ chữ Nho.',
            'avatar' => 'https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?auto=format&fit=crop&q=80&w=200'
        ]);

        $baKim = ThanhVien::create([
            'chi_nhanh_id' => $cnId,
            'ho_ten' => 'Trần Thị Kim',
            'ten_goi' => 'Bà Kim',
            'gioi_tinh' => 'Nữ',
            'ngay_sinh' => '1952-01-30',
            'noi_sinh' => 'Hải Dương',
            'nghe_nghiep' => 'Bác sĩ sản khoa',
            'doi_thu' => 2,
            'loai_quan_he' => 'Vợ/Chồng',
            'spouse_of_id' => $ongDung->id,
            'trang_thai' => 'Còn sống',
            'ghi_chu' => 'Nguyên trưởng khoa sản bệnh viện đa khoa tỉnh, mẫu phụ nữ nhân hậu và chu đáo.',
            'avatar' => 'https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?auto=format&fit=crop&q=80&w=200'
        ]);

        VoChong::create([
            'chong_id' => $ongDung->id,
            'vo_id' => $baKim->id,
            'ngay_cuoi' => '1973-10-12',
            'trang_thai' => 'Đang sống'
        ]);


        // ------------------ ĐỜI 3 ------------------
        // Con cả của ông Hùng: Nguyễn Đức Nam
        $anhNam = ThanhVien::create([
            'chi_nhanh_id' => $cnId,
            'ho_ten' => 'Nguyễn Đức Nam',
            'ten_goi' => 'Anh Nam',
            'gioi_tinh' => 'Nam',
            'ngay_sinh' => '1968-12-14',
            'cha_id' => $ongHung->id,
            'me_id' => $baMai->id,
            'noi_sinh' => 'Hà Nội',
            'nghe_nghiep' => 'Giám đốc doanh nghiệp',
            'doi_thu' => 3,
            'loai_quan_he' => 'Chính',
            'trang_thai' => 'Còn sống',
            'ghi_chu' => 'Người đầu tàu đóng góp nhiều công sức phục dựng nhà thờ tổ dòng họ, chăm lo công tác khuyến học.',
            'avatar' => 'https://images.unsplash.com/photo-1519085360753-af0119f7cbe7?auto=format&fit=crop&q=80&w=200'
        ]);

        $chiTrang = ThanhVien::create([
            'chi_nhanh_id' => $cnId,
            'ho_ten' => 'Hoàng Thu Trang',
            'ten_goi' => 'Chị Trang',
            'gioi_tinh' => 'Nữ',
            'ngay_sinh' => '1972-04-05',
            'noi_sinh' => 'Hà Nội',
            'nghe_nghiep' => 'Kế toán trưởng',
            'doi_thu' => 3,
            'loai_quan_he' => 'Vợ/Chồng',
            'spouse_of_id' => $anhNam->id,
            'trang_thai' => 'Còn sống',
            'ghi_chu' => 'Trợ thủ đắc lực quán xuyến việc kinh doanh của gia đình, người mẹ thông thái.',
            'avatar' => 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&q=80&w=200'
        ]);

        VoChong::create([
            'chong_id' => $anhNam->id,
            'vo_id' => $chiTrang->id,
            'ngay_cuoi' => '1992-06-18',
            'trang_thai' => 'Đang sống'
        ]);

        // Con gái thứ hai của ông Hùng: Nguyễn Thị Hương
        $chiHuong = ThanhVien::create([
            'chi_nhanh_id' => $cnId,
            'ho_ten' => 'Nguyễn Thị Hương',
            'ten_goi' => 'Chị Hương',
            'gioi_tinh' => 'Nữ',
            'ngay_sinh' => '1972-08-18',
            'cha_id' => $ongHung->id,
            'me_id' => $baMai->id,
            'noi_sinh' => 'Hà Nội',
            'nghe_nghiep' => 'Nhà báo',
            'doi_thu' => 3,
            'loai_quan_he' => 'Chính',
            'trang_thai' => 'Còn sống',
            'ghi_chu' => 'Biên tập viên đài truyền hình, năng động, đi nhiều viết khỏe.',
            'avatar' => 'https://images.unsplash.com/photo-1558203728-00f45181dd84?auto=format&fit=crop&q=80&w=200'
        ]);

        // Con trai cả của ông Dũng: Nguyễn Đức Hải
        $anhHai = ThanhVien::create([
            'chi_nhanh_id' => $cnId,
            'ho_ten' => 'Nguyễn Đức Hải',
            'ten_goi' => 'Anh Hải',
            'gioi_tinh' => 'Nam',
            'ngay_sinh' => '1976-02-28',
            'cha_id' => $ongDung->id,
            'me_id' => $baKim->id,
            'noi_sinh' => 'Hưng Yên',
            'nghe_nghiep' => 'Kiến trúc sư trưởng',
            'doi_thu' => 3,
            'loai_quan_he' => 'Chính',
            'trang_thai' => 'Còn sống',
            'ghi_chu' => 'Đã thiết kế nhiều dự án khu đô thị lớn, đam mê hội họa và cờ tướng.',
            'avatar' => 'https://images.unsplash.com/photo-1560250097-0b93528c311a?auto=format&fit=crop&q=80&w=200'
        ]);

        $chiThao = ThanhVien::create([
            'chi_nhanh_id' => $cnId,
            'ho_ten' => 'Ngô Phương Thảo',
            'ten_goi' => 'Chị Thảo',
            'gioi_tinh' => 'Nữ',
            'ngay_sinh' => '1979-10-15',
            'noi_sinh' => 'Bắc Ninh',
            'nghe_nghiep' => 'Giảng viên Ngoại ngữ',
            'doi_thu' => 3,
            'loai_quan_he' => 'Vợ/Chồng',
            'spouse_of_id' => $anhHai->id,
            'trang_thai' => 'Còn sống',
            'ghi_chu' => 'Dạy học tại đại học quốc gia Hà Nội, hiền hòa, đoan trang.',
            'avatar' => 'https://images.unsplash.com/photo-1580489944761-15a19d654956?auto=format&fit=crop&q=80&w=200'
        ]);

        VoChong::create([
            'chong_id' => $anhHai->id,
            'vo_id' => $chiThao->id,
            'ngay_cuoi' => '2000-09-20',
            'trang_thai' => 'Đang sống'
        ]);


        // ------------------ ĐỜI 4 ------------------
        // Con của Anh Nam & Chị Trang: Nguyễn Đức Anh (Đối tác thật)
        $anhDucAnh = ThanhVien::create([
            'chi_nhanh_id' => $cnId,
            'ho_ten' => 'Nguyễn Đức Anh',
            'ten_goi' => 'Đức Anh',
            'gioi_tinh' => 'Nam',
            'ngay_sinh' => '1993-06-20',
            'cha_id' => $anhNam->id,
            'me_id' => $chiTrang->id,
            'noi_sinh' => 'Hà Nội',
            'nghe_nghiep' => 'Chuyên gia IT',
            'doi_thu' => 4,
            'loai_quan_he' => 'Chính',
            'trang_thai' => 'Còn sống',
            'ghi_chu' => 'Tốt nghiệp thạc sĩ công nghệ phần mềm tại Nhật Bản. Sáng lập hệ thống quản lý gia phả số.',
            'avatar' => 'https://images.unsplash.com/photo-1539571696357-5a69c17a67c6?auto=format&fit=crop&q=80&w=200'
        ]);

        $chiNgoc = ThanhVien::create([
            'chi_nhanh_id' => $cnId,
            'ho_ten' => 'Nguyễn Bảo Ngọc',
            'ten_goi' => 'Bảo Ngọc',
            'gioi_tinh' => 'Nữ',
            'ngay_sinh' => '1995-11-08',
            'noi_sinh' => 'Hà Nội',
            'nghe_nghiep' => 'Dược sĩ lâm sàng',
            'doi_thu' => 4,
            'loai_quan_he' => 'Vợ/Chồng',
            'spouse_of_id' => $anhDucAnh->id,
            'trang_thai' => 'Còn sống',
            'ghi_chu' => 'Vợ hiền dâu đảm, phụ tá đắc lực cho chồng.',
            'avatar' => 'https://images.unsplash.com/photo-1544005313-94ddf0286df2?auto=format&fit=crop&q=80&w=200'
        ]);

        VoChong::create([
            'chong_id' => $anhDucAnh->id,
            'vo_id' => $chiNgoc->id,
            'ngay_cuoi' => '2016-12-25',
            'trang_thai' => 'Đang sống'
        ]);

        // Con gái thứ hai của Anh Nam & Chị Trang: Nguyễn Minh Vy
        $chiVy = ThanhVien::create([
            'chi_nhanh_id' => $cnId,
            'ho_ten' => 'Nguyễn Minh Vy',
            'ten_goi' => 'Minh Vy',
            'email' => 'minhvy@master.com',
            'gioi_tinh' => 'Nữ',
            'ngay_sinh' => '1996-09-02',
            'cha_id' => $anhNam->id,
            'me_id' => $chiTrang->id,
            'noi_sinh' => 'Hà Nội',
            'nghe_nghiep' => 'Nhà thiết kế đồ họa',
            'doi_thu' => 4,
            'loai_quan_he' => 'Chính',
            'trang_thai' => 'Còn sống',
            'ghi_chu' => 'Hiện đang du học tự túc thạc sĩ mỹ thuật tại Milan, Ý.',
            'avatar' => 'https://images.unsplash.com/photo-1524504388940-b1c1722653e1?auto=format&fit=crop&q=80&w=200'
        ]);

        // Con trai của Anh Hải & Chị Thảo: Nguyễn Đức Duy
        $anhDuy = ThanhVien::create([
            'chi_nhanh_id' => $cnId,
            'ho_ten' => 'Nguyễn Đức Duy',
            'ten_goi' => 'Đức Duy',
            'gioi_tinh' => 'Nam',
            'ngay_sinh' => '2002-05-14',
            'cha_id' => $anhHai->id,
            'me_id' => $chiThao->id,
            'noi_sinh' => 'Hà Nội',
            'nghe_nghiep' => 'Sinh viên Ngoại thương',
            'doi_thu' => 4,
            'loai_quan_he' => 'Chính',
            'trang_thai' => 'Còn sống',
            'ghi_chu' => 'Đang học năm cuối khoa kinh tế đối ngoại, giành học bổng xuất sắc toàn khóa.',
            'avatar' => 'https://images.unsplash.com/photo-1506794778202-cad84cf45f1d?auto=format&fit=crop&q=80&w=200'
        ]);


        // ------------------ ĐỜI 5 ------------------
        // Con trai cả của Đức Anh & Bảo Ngọc: Nguyễn Đức Khải
        $beKhai = ThanhVien::create([
            'chi_nhanh_id' => $cnId,
            'ho_ten' => 'Nguyễn Đức Khải',
            'ten_goi' => 'Bé Khải',
            'gioi_tinh' => 'Nam',
            'ngay_sinh' => '2018-10-12',
            'cha_id' => $anhDucAnh->id,
            'me_id' => $chiNgoc->id,
            'noi_sinh' => 'Hà Nội',
            'nghe_nghiep' => 'Học sinh tiểu học',
            'doi_thu' => 5,
            'loai_quan_he' => 'Chính',
            'trang_thai' => 'Còn sống',
            'ghi_chu' => 'Thông minh, ham học hỏi, yêu thích vẽ tranh và lập trình lắp ráp robot Lego.',
            'avatar' => 'https://images.unsplash.com/photo-1503919545889-aef636e10ad4?auto=format&fit=crop&q=80&w=200'
        ]);

        // Con gái thứ hai của Đức Anh & Bảo Ngọc: Nguyễn Tuệ Lâm
        $beTueLam = ThanhVien::create([
            'chi_nhanh_id' => $cnId,
            'ho_ten' => 'Nguyễn Tuệ Lâm',
            'ten_goi' => 'Bé Tuệ Lâm',
            'gioi_tinh' => 'Nữ',
            'ngay_sinh' => '2021-03-25',
            'cha_id' => $anhDucAnh->id,
            'me_id' => $chiNgoc->id,
            'noi_sinh' => 'Hà Nội',
            'nghe_nghiep' => 'Học sinh mầm non',
            'doi_thu' => 5,
            'loai_quan_he' => 'Chính',
            'trang_thai' => 'Còn sống',
            'ghi_chu' => 'Cô công chúa nhỏ lém lỉnh, thích ca hát và kể chuyện cổ tích.',
            'avatar' => 'https://images.unsplash.com/photo-1519085360753-af0119f7cbe7?auto=format&fit=crop&q=80&w=200'
        ]);

        // 6. Seed một vài dữ liệu thông báo và tài liệu mẫu để test Phase 3 hoàn hảo
        DB::table('thong_baos')->insert([
            [
                'tieu_de' => 'Quyên góp tu sửa từ đường dòng họ Nguyễn Đức 2026',
                'noi_dung' => 'Ban trị sự dòng họ kêu gọi con cháu chung tay đóng góp kinh phí tôn tạo tường bao và vườn hoa từ đường. Dự kiến khởi công vào tháng 9 âm lịch.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'tieu_de' => 'Lễ tuyên dương khuyến học dòng họ mùa hè 2026',
                'noi_dung' => 'Hội đồng gia tộc thông báo tổ chức phát thưởng khuyến học cho các cháu học sinh giỏi, đỗ đại học xuất sắc tại nhà thờ tổ vào ngày rằm tháng Bảy.',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);

        DB::table('tai_lieus')->insert([
            [
                'tieu_de' => 'Gia Phả Toàn Thư dòng họ Nguyễn Đức (Bản PDF)',
                'file_path' => 'https://www.w3.org/WAI/ER/tests/xhtml/testfiles/resources/pdf/dummy.pdf',
                'mo_ta' => 'Bản ghi chép gia phả chữ quốc ngữ chi tiết từ thời Thủy Tổ đến nay, có đính kèm sơ đồ cây gia phả phóng to để in ấn.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'tieu_de' => 'Hương ước, Tộc ước dòng họ Nguyễn Đức (File Word)',
                'file_path' => 'https://example.com/toc-uoc-nguyen-duc.docx',
                'mo_ta' => 'Hương ước và các điều lệ ứng xử truyền thống của dòng họ, áp dụng cho con cháu muôn đời.',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);

        // 7. Seed mộ phần và nhà thờ cho đối tác để ghim trực quan
        DB::table('mo_phans')->insert([
            [
                'thanh_vien_id' => $cuOng->id,
                'ten_mo' => 'Mộ Cụ Nguyễn Đức Cường (Thủy Tổ)',
                'dia_chi' => 'Nghĩa trang Thạch Thất, Hà Nội',
                'kinh_do' => '105.5440',
                'vi_do' => '21.0187',
                'hinh_anh' => 'https://images.unsplash.com/photo-1595126731003-754972d54756?auto=format&fit=crop&q=80&w=400',
                'ghi_chu' => 'Mộ Thủy Tổ dòng họ Nguyễn Đức.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'thanh_vien_id' => $cuBa->id,
                'ten_mo' => 'Mộ Cụ Phạm Thị Nghĩa',
                'dia_chi' => 'Nghĩa trang Thạch Thất, Hà Nội',
                'kinh_do' => '105.5442',
                'vi_do' => '21.0185',
                'hinh_anh' => 'https://images.unsplash.com/photo-1595126731003-754972d54756?auto=format&fit=crop&q=80&w=400',
                'ghi_chu' => 'Mộ Cụ Bà Thủy Tổ dòng họ Nguyễn Đức.',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);

        DB::table('nha_tho_hos')->insert([
            [
                'ten_nha_tho' => 'Nhà Thờ Tổ Dòng Họ Nguyễn Đức',
                'dia_chi' => 'Xã Tân Hội, Huyện Thạch Thất, Hà Nội',
                'mo_ta' => 'Nơi thờ tự hương khói chung của toàn chi nhánh Hà Nội.',
                'chi_nhanh_id' => $cnId,
                'kinh_do' => '105.5450',
                'vi_do' => '21.0200',
                'hinh_anh' => 'https://images.unsplash.com/photo-1600585154340-be6161a56a0c?auto=format&fit=crop&q=80&w=400',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }
}
