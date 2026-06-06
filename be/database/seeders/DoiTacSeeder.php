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
        // 1. Tránh trùng lặp tài khoản và chi nhánh đối tác (bao gồm cả tài khoản đã xóa mềm)
        $existingUser = NguoiDung::withTrashed()->where('email', 'doitac@master.com')->first();
        if ($existingUser) {
            // Xóa sạch dữ liệu thành viên & chi nhánh cũ của đối tác này để tạo lại sạch sẽ
            $oldBranchIds = ChiNhanh::where('id_nguoi_dung', $existingUser->id)->pluck('id');
            ThanhVien::whereIn('chi_nhanh_id', $oldBranchIds)->delete();
            DoiTocHo::whereIn('chi_nhanh_id', $oldBranchIds)->delete();
            ChiNhanh::where('id_nguoi_dung', $existingUser->id)->delete();
            DoiTac::withTrashed()->where('id_nguoi_dung', $existingUser->id)->forceDelete();
            $existingUser->forceDelete();
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
            'trang_thai' => 'APPROVED',
            'created_at' => now(),
            'updated_at' => now(),
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

        // 5. Thêm dữ liệu 5 đời thành viên chân thực (Mở rộng quy mô, có nhiều người 2 vợ)
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

        // Vợ 1 cụ Cường
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

        // Vợ 2 cụ Cường
        $cuBa2 = ThanhVien::create([
            'chi_nhanh_id' => $cnId,
            'ho_ten' => 'Trần Thị Sâm',
            'ten_goi' => 'Cụ Sâm',
            'gioi_tinh' => 'Nữ',
            'ngay_sinh' => '1922-04-12',
            'ngay_mat' => '1985-06-18',
            'noi_sinh' => 'Hà Nam',
            'nghe_nghiep' => 'Dệt vải',
            'doi_thu' => 1,
            'loai_quan_he' => 'Vợ/Chồng',
            'spouse_of_id' => $cuOng->id,
            'trang_thai' => 'Đã mất',
            'ghi_chu' => 'Vợ thứ hai của cụ Cường, người hiền lành đức độ, chăm lo công việc khung cửi dệt vải phụ giúp gia đình.',
            'avatar' => 'https://images.unsplash.com/photo-1508214751196-bcfd4ca60f91?auto=format&fit=crop&q=80&w=200'
        ]);

        VoChong::create([
            'chong_id' => $cuOng->id,
            'vo_id' => $cuBa2->id,
            'ngay_cuoi' => '1948-11-20',
            'trang_thai' => 'Qua đời'
        ]);

        // ------------------ ĐỜI 2 ------------------
        // Con của cụ Cường & cụ Nghĩa (Vợ 1)
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

        // Vợ 1 ông Hùng
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

        // Vợ 2 ông Hùng
        $baHanh = ThanhVien::create([
            'chi_nhanh_id' => $cnId,
            'ho_ten' => 'Vũ Thị Hạnh',
            'ten_goi' => 'Bà Hạnh',
            'gioi_tinh' => 'Nữ',
            'ngay_sinh' => '1950-11-30',
            'noi_sinh' => 'Hải Phòng',
            'nghe_nghiep' => 'Thương gia',
            'doi_thu' => 2,
            'loai_quan_he' => 'Vợ/Chồng',
            'spouse_of_id' => $ongHung->id,
            'trang_thai' => 'Còn sống',
            'ghi_chu' => 'Vợ thứ hai của ông Hùng, kinh doanh buôn bán vải lớn ở chợ Đồng Xuân.',
            'avatar' => 'https://images.unsplash.com/photo-1531746020798-e6953c6e8e04?auto=format&fit=crop&q=80&w=200'
        ]);

        VoChong::create([
            'chong_id' => $ongHung->id,
            'vo_id' => $baHanh->id,
            'ngay_cuoi' => '1982-08-15',
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

        $ongSon = ThanhVien::create([
            'chi_nhanh_id' => $cnId,
            'ho_ten' => 'Lê Hoàng Sơn',
            'ten_goi' => 'Ông Sơn',
            'gioi_tinh' => 'Nam',
            'ngay_sinh' => '1943-05-18',
            'noi_sinh' => 'Ninh Bình',
            'nghe_nghiep' => 'Kỹ sư cầu đường',
            'doi_thu' => 2,
            'loai_quan_he' => 'Vợ/Chồng',
            'spouse_of_id' => $baLan->id,
            'trang_thai' => 'Còn sống',
            'ghi_chu' => 'Chồng bà Nguyễn Thị Lan, kỹ sư cầu đường nghỉ hưu.',
            'avatar' => 'https://images.unsplash.com/photo-1492562080023-ab3db95bfbce?auto=format&fit=crop&q=80&w=200'
        ]);

        VoChong::create([
            'chong_id' => $ongSon->id,
            'vo_id' => $baLan->id,
            'ngay_cuoi' => '1968-07-20',
            'trang_thai' => 'Đang sống'
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

        // Con của cụ Cường & cụ Sâm (Vợ 2)
        $ongPhuc = ThanhVien::create([
            'chi_nhanh_id' => $cnId,
            'ho_ten' => 'Nguyễn Đức Phúc',
            'ten_goi' => 'Ông Phúc',
            'gioi_tinh' => 'Nam',
            'ngay_sinh' => '1950-10-15',
            'cha_id' => $cuOng->id,
            'me_id' => $cuBa2->id,
            'noi_sinh' => 'Hà Nội',
            'nghe_nghiep' => 'Nhà báo',
            'doi_thu' => 2,
            'loai_quan_he' => 'Chính',
            'trang_thai' => 'Còn sống',
            'ghi_chu' => 'Con trai của cụ Cường với cụ Sâm. Nguyên tổng biên tập báo địa phương.',
            'avatar' => 'https://images.unsplash.com/photo-1500048993953-d23a436266cf?auto=format&fit=crop&q=80&w=200'
        ]);

        $baLien = ThanhVien::create([
            'chi_nhanh_id' => $cnId,
            'ho_ten' => 'Hoàng Thị Liên',
            'ten_goi' => 'Bà Liên',
            'gioi_tinh' => 'Nữ',
            'ngay_sinh' => '1954-07-20',
            'noi_sinh' => 'Nam Định',
            'nghe_nghiep' => 'Dược sĩ',
            'doi_thu' => 2,
            'loai_quan_he' => 'Vợ/Chồng',
            'spouse_of_id' => $ongPhuc->id,
            'trang_thai' => 'Còn sống',
            'ghi_chu' => 'Vợ ông Nguyễn Đức Phúc. Tận tụy chăm lo sức khỏe gia đình.',
            'avatar' => 'https://images.unsplash.com/photo-1534751516642-a131ffd103fd?auto=format&fit=crop&q=80&w=200'
        ]);

        VoChong::create([
            'chong_id' => $ongPhuc->id,
            'vo_id' => $baLien->id,
            'ngay_cuoi' => '1976-11-05',
            'trang_thai' => 'Đang sống'
        ]);

        // Con của cụ Cường & cụ Nghĩa (Vợ 1) - Mới thêm
        $ongTien = ThanhVien::create([
            'chi_nhanh_id' => $cnId,
            'ho_ten' => 'Nguyễn Đức Tiến',
            'ten_goi' => 'Ông Tiến',
            'gioi_tinh' => 'Nam',
            'ngay_sinh' => '1952-04-10',
            'cha_id' => $cuOng->id,
            'me_id' => $cuBa->id,
            'noi_sinh' => 'Hà Tây',
            'nghe_nghiep' => 'Kiến trúc sư',
            'doi_thu' => 2,
            'loai_quan_he' => 'Chính',
            'trang_thai' => 'Còn sống',
            'ghi_chu' => 'Con trai cụ Cường và cụ Nghĩa. Nguyên Trưởng phòng Quy hoạch đô thị.',
            'avatar' => 'https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?auto=format&fit=crop&q=80&w=200'
        ]);

        $baHoa = ThanhVien::create([
            'chi_nhanh_id' => $cnId,
            'ho_ten' => 'Trần Thị Hoa',
            'ten_goi' => 'Bà Hoa',
            'gioi_tinh' => 'Nữ',
            'ngay_sinh' => '1956-08-15',
            'noi_sinh' => 'Hà Nội',
            'nghe_nghiep' => 'Họa sĩ',
            'doi_thu' => 2,
            'loai_quan_he' => 'Vợ/Chồng',
            'spouse_of_id' => $ongTien->id,
            'trang_thai' => 'Còn sống',
            'ghi_chu' => 'Vợ ông Nguyễn Đức Tiến. Hội viên Hội Mỹ thuật Việt Nam.',
            'avatar' => 'https://images.unsplash.com/photo-1544005313-94ddf0286df2?auto=format&fit=crop&q=80&w=200'
        ]);

        VoChong::create([
            'chong_id' => $ongTien->id,
            'vo_id' => $baHoa->id,
            'ngay_cuoi' => '1978-02-14',
            'trang_thai' => 'Đang sống'
        ]);

        // Con của cụ Cường & cụ Sâm (Vợ 2) - Mới thêm
        $baHaDoi2 = ThanhVien::create([
            'chi_nhanh_id' => $cnId,
            'ho_ten' => 'Nguyễn Thị Hà',
            'ten_goi' => 'Bà Hà',
            'gioi_tinh' => 'Nữ',
            'ngay_sinh' => '1955-09-20',
            'cha_id' => $cuOng->id,
            'me_id' => $cuBa2->id,
            'noi_sinh' => 'Hà Nội',
            'nghe_nghiep' => 'Giáo viên văn',
            'doi_thu' => 2,
            'loai_quan_he' => 'Chính',
            'trang_thai' => 'Còn sống',
            'ghi_chu' => 'Con gái cụ Cường với cụ Sâm, cựu giáo viên THPT Chu Văn An.',
            'avatar' => 'https://images.unsplash.com/photo-1580489944761-15a19d654956?auto=format&fit=crop&q=80&w=200'
        ]);

        $ongHoang = ThanhVien::create([
            'chi_nhanh_id' => $cnId,
            'ho_ten' => 'Vũ Huy Hoàng',
            'ten_goi' => 'Ông Hoàng',
            'gioi_tinh' => 'Nam',
            'ngay_sinh' => '1951-11-05',
            'noi_sinh' => 'Hải Dương',
            'nghe_nghiep' => 'Giảng viên đại học',
            'doi_thu' => 2,
            'loai_quan_he' => 'Vợ/Chồng',
            'spouse_of_id' => $baHaDoi2->id,
            'trang_thai' => 'Còn sống',
            'ghi_chu' => 'Chồng bà Nguyễn Thị Hà, nguyên PGS.TS Ngoại thương.',
            'avatar' => 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?auto=format&fit=crop&q=80&w=200'
        ]);

        VoChong::create([
            'chong_id' => $ongHoang->id,
            'vo_id' => $baHaDoi2->id,
            'ngay_cuoi' => '1977-10-10',
            'trang_thai' => 'Đang sống'
        ]);

        $ongKhang = ThanhVien::create([
            'chi_nhanh_id' => $cnId,
            'ho_ten' => 'Nguyễn Đức Khang',
            'ten_goi' => 'Ông Khang',
            'gioi_tinh' => 'Nam',
            'ngay_sinh' => '1958-03-22',
            'cha_id' => $cuOng->id,
            'me_id' => $cuBa2->id,
            'noi_sinh' => 'Hà Nội',
            'nghe_nghiep' => 'Nhà báo',
            'doi_thu' => 2,
            'loai_quan_he' => 'Chính',
            'trang_thai' => 'Còn sống',
            'ghi_chu' => 'Con út cụ Cường với cụ Sâm, cựu phóng viên đài truyền hình.',
            'avatar' => 'https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?auto=format&fit=crop&q=80&w=200'
        ]);

        $baThuyDoi2 = ThanhVien::create([
            'chi_nhanh_id' => $cnId,
            'ho_ten' => 'Phạm Thu Thủy',
            'ten_goi' => 'Bà Thủy',
            'gioi_tinh' => 'Nữ',
            'ngay_sinh' => '1962-07-12',
            'noi_sinh' => 'Bắc Ninh',
            'nghe_nghiep' => 'Thủ thư',
            'doi_thu' => 2,
            'loai_quan_he' => 'Vợ/Chồng',
            'spouse_of_id' => $ongKhang->id,
            'trang_thai' => 'Còn sống',
            'ghi_chu' => 'Vợ ông Nguyễn Đức Khang, công tác tại thư viện quốc gia.',
            'avatar' => 'https://images.unsplash.com/photo-1544005313-94ddf0286df2?auto=format&fit=crop&q=80&w=200'
        ]);

        VoChong::create([
            'chong_id' => $ongKhang->id,
            'vo_id' => $baThuyDoi2->id,
            'ngay_cuoi' => '1983-05-18',
            'trang_thai' => 'Đang sống'
        ]);

        // ------------------ ĐỜI 3 ------------------
        // Con của ông Hùng & bà Mai (Vợ 1)
        // 1. Nguyễn Đức Nam (Chính)
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

        // Nguyễn Bảo Ngọc (Vợ anh Nam - matching the screenshot)
        $chiNgoc = ThanhVien::create([
            'chi_nhanh_id' => $cnId,
            'ho_ten' => 'Nguyễn Bảo Ngọc',
            'ten_goi' => 'Bảo Ngọc',
            'gioi_tinh' => 'Nữ',
            'ngay_sinh' => '1995-11-08',
            'noi_sinh' => 'Hà Nội',
            'nghe_nghiep' => 'Dược sĩ lâm sàng',
            'doi_thu' => 3,
            'loai_quan_he' => 'Vợ/Chồng',
            'spouse_of_id' => $anhNam->id,
            'trang_thai' => 'Còn sống',
            'ghi_chu' => 'Vợ hiền dâu đảm, phụ tá đắc lực cho chồng.',
            'avatar' => 'https://images.unsplash.com/photo-1544005313-94ddf0286df2?auto=format&fit=crop&q=80&w=200'
        ]);

        VoChong::create([
            'chong_id' => $anhNam->id,
            'vo_id' => $chiNgoc->id,
            'ngay_cuoi' => '2012-12-25',
            'trang_thai' => 'Đang sống'
        ]);

        // 2. Nguyễn Thị Hương (Con ông Hùng & bà Mai)
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

        $ongTuan = ThanhVien::create([
            'chi_nhanh_id' => $cnId,
            'ho_ten' => 'Phạm Minh Tuấn',
            'ten_goi' => 'Anh Tuấn',
            'gioi_tinh' => 'Nam',
            'ngay_sinh' => '1970-05-12',
            'noi_sinh' => 'Vĩnh Phúc',
            'nghe_nghiep' => 'Bác sĩ chuyên khoa',
            'doi_thu' => 3,
            'loai_quan_he' => 'Vợ/Chồng',
            'spouse_of_id' => $chiHuong->id,
            'trang_thai' => 'Còn sống',
            'ghi_chu' => 'Chồng chị Nguyễn Thị Hương, bác sĩ chuyên khoa răng hàm mặt.',
            'avatar' => 'https://images.unsplash.com/photo-1537368910025-700350fe46c7?auto=format&fit=crop&q=80&w=200'
        ]);

        VoChong::create([
            'chong_id' => $ongTuan->id,
            'vo_id' => $chiHuong->id,
            'ngay_cuoi' => '1996-10-18',
            'trang_thai' => 'Đang sống'
        ]);

        // Con của ông Hùng & bà Hạnh (Vợ 2)
        // 3. Nguyễn Đức Bình
        $anhBinh = ThanhVien::create([
            'chi_nhanh_id' => $cnId,
            'ho_ten' => 'Nguyễn Đức Bình',
            'ten_goi' => 'Anh Bình',
            'gioi_tinh' => 'Nam',
            'ngay_sinh' => '1984-06-15',
            'cha_id' => $ongHung->id,
            'me_id' => $baHanh->id,
            'noi_sinh' => 'Hà Nội',
            'nghe_nghiep' => 'Kinh doanh tự do',
            'doi_thu' => 3,
            'loai_quan_he' => 'Chính',
            'trang_thai' => 'Còn sống',
            'ghi_chu' => 'Con trai của ông Hùng với bà Hạnh. Năng động nhạy bén trong kinh doanh.',
            'avatar' => 'https://images.unsplash.com/photo-1506794778202-cad84cf45f1d?auto=format&fit=crop&q=80&w=200'
        ]);

        $chiHa = ThanhVien::create([
            'chi_nhanh_id' => $cnId,
            'ho_ten' => 'Nguyễn Thu Hà',
            'ten_goi' => 'Chị Hà',
            'gioi_tinh' => 'Nữ',
            'ngay_sinh' => '1988-02-28',
            'noi_sinh' => 'Hà Nam',
            'nghe_nghiep' => 'Nhân viên văn phòng',
            'doi_thu' => 3,
            'loai_quan_he' => 'Vợ/Chồng',
            'spouse_of_id' => $anhBinh->id,
            'trang_thai' => 'Còn sống',
            'ghi_chu' => 'Vợ anh Nguyễn Đức Bình, kế toán công ty du lịch.',
            'avatar' => 'https://images.unsplash.com/photo-1544005313-94ddf0286df2?auto=format&fit=crop&q=80&w=200'
        ]);

        VoChong::create([
            'chong_id' => $anhBinh->id,
            'vo_id' => $chiHa->id,
            'ngay_cuoi' => '2010-09-12',
            'trang_thai' => 'Đang sống'
        ]);

        // Con của bà Lan & ông Sơn
        // 4. Lê Thị Bích Ngọc
        $chiBichNgoc = ThanhVien::create([
            'chi_nhanh_id' => $cnId,
            'ho_ten' => 'Lê Thị Bích Ngọc',
            'ten_goi' => 'Bích Ngọc',
            'gioi_tinh' => 'Nữ',
            'ngay_sinh' => '1971-10-22',
            'cha_id' => $ongSon->id,
            'me_id' => $baLan->id,
            'noi_sinh' => 'Hà Nội',
            'nghe_nghiep' => 'Giáo viên toán',
            'doi_thu' => 3,
            'loai_quan_he' => 'Chính',
            'trang_thai' => 'Còn sống',
            'ghi_chu' => 'Con gái đầu bà Lan ông Sơn, giáo viên trung học.',
            'avatar' => 'https://images.unsplash.com/photo-1580489944761-15a19d654956?auto=format&fit=crop&q=80&w=200'
        ]);

        $anhNamConRe = ThanhVien::create([
            'chi_nhanh_id' => $cnId,
            'ho_ten' => 'Đỗ Văn Nam',
            'ten_goi' => 'Anh Nam',
            'gioi_tinh' => 'Nam',
            'ngay_sinh' => '1968-11-05',
            'noi_sinh' => 'Hà Tây',
            'nghe_nghiep' => 'Kỹ sư viễn thông',
            'doi_thu' => 3,
            'loai_quan_he' => 'Vợ/Chồng',
            'spouse_of_id' => $chiBichNgoc->id,
            'trang_thai' => 'Còn sống',
            'ghi_chu' => 'Chồng chị Lê Thị Bích Ngọc, trưởng phòng viễn thông.',
            'avatar' => 'https://images.unsplash.com/photo-1492562080023-ab3db95bfbce?auto=format&fit=crop&q=80&w=200'
        ]);

        VoChong::create([
            'chong_id' => $anhNamConRe->id,
            'vo_id' => $chiBichNgoc->id,
            'ngay_cuoi' => '1994-05-18',
            'trang_thai' => 'Đang sống'
        ]);

        // 5. Lê Anh Tuấn
        $anhTuanConNgoai = ThanhVien::create([
            'chi_nhanh_id' => $cnId,
            'ho_ten' => 'Lê Anh Tuấn',
            'ten_goi' => 'Anh Tuấn',
            'gioi_tinh' => 'Nam',
            'ngay_sinh' => '1975-04-12',
            'cha_id' => $ongSon->id,
            'me_id' => $baLan->id,
            'noi_sinh' => 'Hà Nội',
            'nghe_nghiep' => 'Kinh doanh ô tô',
            'doi_thu' => 3,
            'loai_quan_he' => 'Chính',
            'trang_thai' => 'Còn sống',
            'ghi_chu' => 'Con thứ bà Lan ông Sơn, có showroom ô tô lớn.',
            'avatar' => 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?auto=format&fit=crop&q=80&w=200'
        ]);

        $chiTuyen = ThanhVien::create([
            'chi_nhanh_id' => $cnId,
            'ho_ten' => 'Nguyễn Thị Tuyết',
            'ten_goi' => 'Chị Tuyết',
            'gioi_tinh' => 'Nữ',
            'ngay_sinh' => '1978-01-20',
            'noi_sinh' => 'Hưng Yên',
            'nghe_nghiep' => 'Thiết kế thời trang',
            'doi_thu' => 3,
            'loai_quan_he' => 'Vợ/Chồng',
            'spouse_of_id' => $anhTuanConNgoai->id,
            'trang_thai' => 'Còn sống',
            'ghi_chu' => 'Vợ anh Lê Anh Tuấn, nhà thiết kế thời trang.',
            'avatar' => 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&q=80&w=200'
        ]);

        VoChong::create([
            'chong_id' => $anhTuanConNgoai->id,
            'vo_id' => $chiTuyen->id,
            'ngay_cuoi' => '2002-11-20',
            'trang_thai' => 'Đang sống'
        ]);

        // Con của ông Dũng & bà Kim
        // 6. Nguyễn Đức Hải
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

        // 7. Nguyễn Thị Bích Thủy (Con thứ 2 của Dũng & Kim)
        $chiThuyConDung = ThanhVien::create([
            'chi_nhanh_id' => $cnId,
            'ho_ten' => 'Nguyễn Thị Bích Thủy',
            'ten_goi' => 'Chị Thủy',
            'gioi_tinh' => 'Nữ',
            'ngay_sinh' => '1978-05-14',
            'cha_id' => $ongDung->id,
            'me_id' => $baKim->id,
            'noi_sinh' => 'Hải Dương',
            'nghe_nghiep' => 'Biên dịch viên',
            'doi_thu' => 3,
            'loai_quan_he' => 'Chính',
            'trang_thai' => 'Còn sống',
            'ghi_chu' => 'Con gái thứ hai của ông Dũng bà Kim.',
            'avatar' => 'https://images.unsplash.com/photo-1544005313-94ddf0286df2?auto=format&fit=crop&q=80&w=200'
        ]);

        $anhCuongConRe = ThanhVien::create([
            'chi_nhanh_id' => $cnId,
            'ho_ten' => 'Trần Văn Cường',
            'ten_goi' => 'Anh Cường',
            'gioi_tinh' => 'Nam',
            'ngay_sinh' => '1975-09-22',
            'noi_sinh' => 'Thái Bình',
            'nghe_nghiep' => 'Kỹ sư điện lực',
            'doi_thu' => 3,
            'loai_quan_he' => 'Vợ/Chồng',
            'spouse_of_id' => $chiThuyConDung->id,
            'trang_thai' => 'Còn sống',
            'ghi_chu' => 'Chồng chị Nguyễn Thị Bích Thủy.',
            'avatar' => 'https://images.unsplash.com/photo-1506794778202-cad84cf45f1d?auto=format&fit=crop&q=80&w=200'
        ]);

        VoChong::create([
            'chong_id' => $anhCuongConRe->id,
            'vo_id' => $chiThuyConDung->id,
            'ngay_cuoi' => '1998-10-12',
            'trang_thai' => 'Đang sống'
        ]);

        // 8. Nguyễn Đức Tuấn (Con thứ 3 của Dũng & Kim)
        $anhTuanConDung = ThanhVien::create([
            'chi_nhanh_id' => $cnId,
            'ho_ten' => 'Nguyễn Đức Tuấn',
            'ten_goi' => 'Anh Tuấn',
            'gioi_tinh' => 'Nam',
            'ngay_sinh' => '1982-03-10',
            'cha_id' => $ongDung->id,
            'me_id' => $baKim->id,
            'noi_sinh' => 'Hải Dương',
            'nghe_nghiep' => 'Quản lý dự án',
            'doi_thu' => 3,
            'loai_quan_he' => 'Chính',
            'trang_thai' => 'Còn sống',
            'ghi_chu' => 'Con trai út của ông Dũng bà Kim.',
            'avatar' => 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?auto=format&fit=crop&q=80&w=200'
        ]);

        $chiHuongConDau = ThanhVien::create([
            'chi_nhanh_id' => $cnId,
            'ho_ten' => 'Lê Thị Hương',
            'ten_goi' => 'Chị Hương',
            'gioi_tinh' => 'Nữ',
            'ngay_sinh' => '1985-12-14',
            'noi_sinh' => 'Hải Dương',
            'nghe_nghiep' => 'Giáo viên mầm non',
            'doi_thu' => 3,
            'loai_quan_he' => 'Vợ/Chồng',
            'spouse_of_id' => $anhTuanConDung->id,
            'trang_thai' => 'Còn sống',
            'ghi_chu' => 'Vợ anh Nguyễn Đức Tuấn.',
            'avatar' => 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&q=80&w=200'
        ]);

        VoChong::create([
            'chong_id' => $anhTuanConDung->id,
            'vo_id' => $chiHuongConDau->id,
            'ngay_cuoi' => '2008-05-20',
            'trang_thai' => 'Đang sống'
        ]);

        // Con của ông Phúc & bà Liên
        // 9. Nguyễn Đức Hạnh
        $anhHanhConPhuc = ThanhVien::create([
            'chi_nhanh_id' => $cnId,
            'ho_ten' => 'Nguyễn Đức Hạnh',
            'ten_goi' => 'Anh Hạnh',
            'gioi_tinh' => 'Nam',
            'ngay_sinh' => '1978-08-12',
            'cha_id' => $ongPhuc->id,
            'me_id' => $baLien->id,
            'noi_sinh' => 'Hà Nội',
            'nghe_nghiep' => 'Dược sĩ',
            'doi_thu' => 3,
            'loai_quan_he' => 'Chính',
            'trang_thai' => 'Còn sống',
            'ghi_chu' => 'Chủ chuỗi nhà thuốc tư nhân lớn tại Thạch Thất.',
            'avatar' => 'https://images.unsplash.com/photo-1506794778202-cad84cf45f1d?auto=format&fit=crop&q=80&w=200'
        ]);

        $chiDungConDau = ThanhVien::create([
            'chi_nhanh_id' => $cnId,
            'ho_ten' => 'Phạm Thị Dung',
            'ten_goi' => 'Chị Dung',
            'gioi_tinh' => 'Nữ',
            'ngay_sinh' => '1982-11-05',
            'noi_sinh' => 'Hà Tây',
            'nghe_nghiep' => 'Nha sĩ',
            'doi_thu' => 3,
            'loai_quan_he' => 'Vợ/Chồng',
            'spouse_of_id' => $anhHanhConPhuc->id,
            'trang_thai' => 'Còn sống',
            'ghi_chu' => 'Vợ anh Nguyễn Đức Hạnh, phụ tá quản lý chuỗi phòng khám nha khoa.',
            'avatar' => 'https://images.unsplash.com/photo-1544005313-94ddf0286df2?auto=format&fit=crop&q=80&w=200'
        ]);

        VoChong::create([
            'chong_id' => $anhHanhConPhuc->id,
            'vo_id' => $chiDungConDau->id,
            'ngay_cuoi' => '2004-04-18',
            'trang_thai' => 'Đang sống'
        ]);

        // 10. Nguyễn Đức Lộc (Con thứ 2 của Phúc & Liên)
        $anhLocConPhuc = ThanhVien::create([
            'chi_nhanh_id' => $cnId,
            'ho_ten' => 'Nguyễn Đức Lộc',
            'ten_goi' => 'Anh Lộc',
            'gioi_tinh' => 'Nam',
            'ngay_sinh' => '1980-04-12',
            'cha_id' => $ongPhuc->id,
            'me_id' => $baLien->id,
            'noi_sinh' => 'Hà Nội',
            'nghe_nghiep' => 'Lập trình viên',
            'doi_thu' => 3,
            'loai_quan_he' => 'Chính',
            'trang_thai' => 'Còn sống',
            'ghi_chu' => 'Con thứ hai của ông Phúc bà Liên.',
            'avatar' => 'https://images.unsplash.com/photo-1506794778202-cad84cf45f1d?auto=format&fit=crop&q=80&w=200'
        ]);

        $chiThanhConDau = ThanhVien::create([
            'chi_nhanh_id' => $cnId,
            'ho_ten' => 'Vũ Thị Thanh',
            'ten_goi' => 'Chị Thanh',
            'gioi_tinh' => 'Nữ',
            'ngay_sinh' => '1984-06-20',
            'noi_sinh' => 'Hà Nội',
            'nghe_nghiep' => 'Chuyên viên nhân sự',
            'doi_thu' => 3,
            'loai_quan_he' => 'Vợ/Chồng',
            'spouse_of_id' => $anhLocConPhuc->id,
            'trang_thai' => 'Còn sống',
            'ghi_chu' => 'Vợ anh Nguyễn Đức Lộc.',
            'avatar' => 'https://images.unsplash.com/photo-1544005313-94ddf0286df2?auto=format&fit=crop&q=80&w=200'
        ]);

        VoChong::create([
            'chong_id' => $anhLocConPhuc->id,
            'vo_id' => $chiThanhConDau->id,
            'ngay_cuoi' => '2006-11-25',
            'trang_thai' => 'Đang sống'
        ]);

        // 11. Nguyễn Thị Hồng (Con thứ 3 của Phúc & Liên)
        $chiHongConPhuc = ThanhVien::create([
            'chi_nhanh_id' => $cnId,
            'ho_ten' => 'Nguyễn Thị Hồng',
            'ten_goi' => 'Chị Hồng',
            'gioi_tinh' => 'Nữ',
            'ngay_sinh' => '1983-09-18',
            'cha_id' => $ongPhuc->id,
            'me_id' => $baLien->id,
            'noi_sinh' => 'Hà Nội',
            'nghe_nghiep' => 'Kiểm toán viên',
            'doi_thu' => 3,
            'loai_quan_he' => 'Chính',
            'trang_thai' => 'Còn sống',
            'ghi_chu' => 'Con gái út của ông Phúc bà Liên.',
            'avatar' => 'https://images.unsplash.com/photo-1580489944761-15a19d654956?auto=format&fit=crop&q=80&w=200'
        ]);

        $anhKhanhConRe = ThanhVien::create([
            'chi_nhanh_id' => $cnId,
            'ho_ten' => 'Đặng Quốc Khánh',
            'ten_goi' => 'Anh Khánh',
            'gioi_tinh' => 'Nam',
            'ngay_sinh' => '1980-11-20',
            'noi_sinh' => 'Bắc Ninh',
            'nghe_nghiep' => 'Giảng viên đại học',
            'doi_thu' => 3,
            'loai_quan_he' => 'Vợ/Chồng',
            'spouse_of_id' => $chiHongConPhuc->id,
            'trang_thai' => 'Còn sống',
            'ghi_chu' => 'Chồng chị Nguyễn Thị Hồng.',
            'avatar' => 'https://images.unsplash.com/photo-1492562080023-ab3db95bfbce?auto=format&fit=crop&q=80&w=200'
        ]);

        VoChong::create([
            'chong_id' => $anhKhanhConRe->id,
            'vo_id' => $chiHongConPhuc->id,
            'ngay_cuoi' => '2005-09-15',
            'trang_thai' => 'Đang sống'
        ]);

        // Con của anh Nam & chị Ngọc (Con thứ 3 - Mới thêm)
        $anhKhanh = ThanhVien::create([
            'chi_nhanh_id' => $cnId,
            'ho_ten' => 'Nguyễn Đức Khánh',
            'ten_goi' => 'Anh Khánh',
            'gioi_tinh' => 'Nam',
            'ngay_sinh' => '2000-05-18',
            'cha_id' => $anhNam->id,
            'me_id' => $chiNgoc->id,
            'noi_sinh' => 'Hà Nội',
            'nghe_nghiep' => 'Kỹ sư ô tô',
            'doi_thu' => 3,
            'loai_quan_he' => 'Chính',
            'trang_thai' => 'Còn sống',
            'ghi_chu' => 'Con trai thứ ba của anh Nam chị Ngọc. Làm việc tại VinFast.',
            'avatar' => 'https://images.unsplash.com/photo-1506794778202-cad84cf45f1d?auto=format&fit=crop&q=80&w=200'
        ]);

        $chiPhuongAnh = ThanhVien::create([
            'chi_nhanh_id' => $cnId,
            'ho_ten' => 'Lê Phương Anh',
            'ten_goi' => 'Phương Anh',
            'gioi_tinh' => 'Nữ',
            'ngay_sinh' => '2002-09-12',
            'noi_sinh' => 'Ninh Bình',
            'nghe_nghiep' => 'Chuyên viên Marketing',
            'doi_thu' => 3,
            'loai_quan_he' => 'Vợ/Chồng',
            'spouse_of_id' => $anhKhanh->id,
            'trang_thai' => 'Còn sống',
            'ghi_chu' => 'Vợ anh Nguyễn Đức Khánh.',
            'avatar' => 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&q=80&w=200'
        ]);

        VoChong::create([
            'chong_id' => $anhKhanh->id,
            'vo_id' => $chiPhuongAnh->id,
            'ngay_cuoi' => '2024-03-20',
            'trang_thai' => 'Đang sống'
        ]);

        // Con của ông Tiến & bà Hoa (Mới thêm)
        $anhManh = ThanhVien::create([
            'chi_nhanh_id' => $cnId,
            'ho_ten' => 'Nguyễn Đức Mạnh',
            'ten_goi' => 'Anh Mạnh',
            'gioi_tinh' => 'Nam',
            'ngay_sinh' => '1979-02-28',
            'cha_id' => $ongTien->id,
            'me_id' => $baHoa->id,
            'noi_sinh' => 'Hà Nội',
            'nghe_nghiep' => 'Kiến trúc sư',
            'doi_thu' => 3,
            'loai_quan_he' => 'Chính',
            'trang_thai' => 'Còn sống',
            'ghi_chu' => 'Con cả ông Tiến bà Hoa. Tự lập công ty thiết kế nội thất.',
            'avatar' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?auto=format&fit=crop&q=80&w=200'
        ]);

        $chiThaoManh = ThanhVien::create([
            'chi_nhanh_id' => $cnId,
            'ho_ten' => 'Lê Thị Thảo',
            'ten_goi' => 'Chị Thảo',
            'gioi_tinh' => 'Nữ',
            'ngay_sinh' => '1983-05-14',
            'noi_sinh' => 'Hải Dương',
            'nghe_nghiep' => 'Kế toán trưởng',
            'doi_thu' => 3,
            'loai_quan_he' => 'Vợ/Chồng',
            'spouse_of_id' => $anhManh->id,
            'trang_thai' => 'Còn sống',
            'ghi_chu' => 'Vợ anh Nguyễn Đức Mạnh.',
            'avatar' => 'https://images.unsplash.com/photo-1544005313-94ddf0286df2?auto=format&fit=crop&q=80&w=200'
        ]);

        VoChong::create([
            'chong_id' => $anhManh->id,
            'vo_id' => $chiThaoManh->id,
            'ngay_cuoi' => '2005-11-18',
            'trang_thai' => 'Đang sống'
        ]);

        $anhTrung = ThanhVien::create([
            'chi_nhanh_id' => $cnId,
            'ho_ten' => 'Nguyễn Đức Trung',
            'ten_goi' => 'Anh Trung',
            'gioi_tinh' => 'Nam',
            'ngay_sinh' => '1982-06-15',
            'cha_id' => $ongTien->id,
            'me_id' => $baHoa->id,
            'noi_sinh' => 'Hà Nội',
            'nghe_nghiep' => 'Quản lý khách sạn',
            'doi_thu' => 3,
            'loai_quan_he' => 'Chính',
            'trang_thai' => 'Còn sống',
            'ghi_chu' => 'Con thứ hai ông Tiến bà Hoa. Có 2 đời vợ.',
            'avatar' => 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?auto=format&fit=crop&q=80&w=200'
        ]);

        // Vợ 1 anh Trung
        $chiLanAnh = ThanhVien::create([
            'chi_nhanh_id' => $cnId,
            'ho_ten' => 'Nguyễn Thị Lan Anh',
            'ten_goi' => 'Chị Lan Anh',
            'gioi_tinh' => 'Nữ',
            'ngay_sinh' => '1985-04-10',
            'noi_sinh' => 'Thái Bình',
            'nghe_nghiep' => 'Nhân viên ngân hàng',
            'doi_thu' => 3,
            'loai_quan_he' => 'Vợ/Chồng',
            'spouse_of_id' => $anhTrung->id,
            'trang_thai' => 'Còn sống',
            'ghi_chu' => 'Vợ thứ nhất anh Nguyễn Đức Trung (ly hôn).',
            'avatar' => 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&q=80&w=200'
        ]);

        VoChong::create([
            'chong_id' => $anhTrung->id,
            'vo_id' => $chiLanAnh->id,
            'ngay_cuoi' => '2008-08-08',
            'trang_thai' => 'Ly hôn'
        ]);

        // Vợ 2 anh Trung
        $chiThanhMai = ThanhVien::create([
            'chi_nhanh_id' => $cnId,
            'ho_ten' => 'Phạm Thanh Mai',
            'ten_goi' => 'Chị Thanh Mai',
            'gioi_tinh' => 'Nữ',
            'ngay_sinh' => '1990-10-12',
            'noi_sinh' => 'Nam Định',
            'nghe_nghiep' => 'Dịch giả',
            'doi_thu' => 3,
            'loai_quan_he' => 'Vợ/Chồng',
            'spouse_of_id' => $anhTrung->id,
            'trang_thai' => 'Còn sống',
            'ghi_chu' => 'Vợ thứ hai anh Nguyễn Đức Trung.',
            'avatar' => 'https://images.unsplash.com/photo-1517841905240-472988babdf9?auto=format&fit=crop&q=80&w=200'
        ]);

        VoChong::create([
            'chong_id' => $anhTrung->id,
            'vo_id' => $chiThanhMai->id,
            'ngay_cuoi' => '2018-05-20',
            'trang_thai' => 'Đang sống'
        ]);

        $chiMai = ThanhVien::create([
            'chi_nhanh_id' => $cnId,
            'ho_ten' => 'Nguyễn Thị Mai',
            'ten_goi' => 'Chị Mai',
            'gioi_tinh' => 'Nữ',
            'ngay_sinh' => '1986-11-20',
            'cha_id' => $ongTien->id,
            'me_id' => $baHoa->id,
            'noi_sinh' => 'Hà Nội',
            'nghe_nghiep' => 'Nhà thiết kế web',
            'doi_thu' => 3,
            'loai_quan_he' => 'Chính',
            'trang_thai' => 'Còn sống',
            'ghi_chu' => 'Con út ông Tiến bà Hoa.',
            'avatar' => 'https://images.unsplash.com/photo-1580489944761-15a19d654956?auto=format&fit=crop&q=80&w=200'
        ]);

        $anhMinhRe = ThanhVien::create([
            'chi_nhanh_id' => $cnId,
            'ho_ten' => 'Trần Văn Minh',
            'ten_goi' => 'Anh Minh',
            'gioi_tinh' => 'Nam',
            'ngay_sinh' => '1983-09-18',
            'noi_sinh' => 'Hải Phòng',
            'nghe_nghiep' => 'Kỹ sư điện',
            'doi_thu' => 3,
            'loai_quan_he' => 'Vợ/Chồng',
            'spouse_of_id' => $chiMai->id,
            'trang_thai' => 'Còn sống',
            'ghi_chu' => 'Chồng chị Nguyễn Thị Mai.',
            'avatar' => 'https://images.unsplash.com/photo-1492562080023-ab3db95bfbce?auto=format&fit=crop&q=80&w=200'
        ]);

        VoChong::create([
            'chong_id' => $anhMinhRe->id,
            'vo_id' => $chiMai->id,
            'ngay_cuoi' => '2009-12-25',
            'trang_thai' => 'Đang sống'
        ]);

        // Con của bà Hà & ông Hoàng (Mới thêm)
        $anhLam = ThanhVien::create([
            'chi_nhanh_id' => $cnId,
            'ho_ten' => 'Vũ Hoàng Lâm',
            'ten_goi' => 'Anh Lâm',
            'gioi_tinh' => 'Nam',
            'ngay_sinh' => '1978-08-12',
            'cha_id' => $ongHoang->id,
            'me_id' => $baHaDoi2->id,
            'noi_sinh' => 'Hà Nội',
            'nghe_nghiep' => 'Bác sĩ nhi khoa',
            'doi_thu' => 3,
            'loai_quan_he' => 'Chính',
            'trang_thai' => 'Còn sống',
            'ghi_chu' => 'Con cả bà Hà ông Hoàng. Làm việc tại bệnh viện Nhi Trung ương.',
            'avatar' => 'https://images.unsplash.com/photo-1506794778202-cad84cf45f1d?auto=format&fit=crop&q=80&w=200'
        ]);

        $chiTrangLam = ThanhVien::create([
            'chi_nhanh_id' => $cnId,
            'ho_ten' => 'Trần Thu Trang',
            'ten_goi' => 'Thu Trang',
            'gioi_tinh' => 'Nữ',
            'ngay_sinh' => '1982-04-15',
            'noi_sinh' => 'Hà Nội',
            'nghe_nghiep' => 'Dược sĩ',
            'doi_thu' => 3,
            'loai_quan_he' => 'Vợ/Chồng',
            'spouse_of_id' => $anhLam->id,
            'trang_thai' => 'Còn sống',
            'ghi_chu' => 'Vợ anh Vũ Hoàng Lâm.',
            'avatar' => 'https://images.unsplash.com/photo-1544005313-94ddf0286df2?auto=format&fit=crop&q=80&w=200'
        ]);

        VoChong::create([
            'chong_id' => $anhLam->id,
            'vo_id' => $chiTrangLam->id,
            'ngay_cuoi' => '2004-10-10',
            'trang_thai' => 'Đang sống'
        ]);

        $chiThuHuong = ThanhVien::create([
            'chi_nhanh_id' => $cnId,
            'ho_ten' => 'Vũ Thị Thu Hương',
            'ten_goi' => 'Thu Hương',
            'gioi_tinh' => 'Nữ',
            'ngay_sinh' => '1981-12-05',
            'cha_id' => $ongHoang->id,
            'me_id' => $baHaDoi2->id,
            'noi_sinh' => 'Hà Nội',
            'nghe_nghiep' => 'Kế toán viên',
            'doi_thu' => 3,
            'loai_quan_he' => 'Chính',
            'trang_thai' => 'Còn sống',
            'ghi_chu' => 'Con thứ hai bà Hà ông Hoàng.',
            'avatar' => 'https://images.unsplash.com/photo-1580489944761-15a19d654956?auto=format&fit=crop&q=80&w=200'
        ]);

        $anhHuyRe = ThanhVien::create([
            'chi_nhanh_id' => $cnId,
            'ho_ten' => 'Nguyễn Quang Huy',
            'ten_goi' => 'Anh Huy',
            'gioi_tinh' => 'Nam',
            'ngay_sinh' => '1978-07-22',
            'noi_sinh' => 'Hà Tây',
            'nghe_nghiep' => 'Lập trình viên',
            'doi_thu' => 3,
            'loai_quan_he' => 'Vợ/Chồng',
            'spouse_of_id' => $chiThuHuong->id,
            'trang_thai' => 'Còn sống',
            'ghi_chu' => 'Chồng chị Vũ Thị Thu Hương.',
            'avatar' => 'https://images.unsplash.com/photo-1492562080023-ab3db95bfbce?auto=format&fit=crop&q=80&w=200'
        ]);

        VoChong::create([
            'chong_id' => $anhHuyRe->id,
            'vo_id' => $chiThuHuong->id,
            'ngay_cuoi' => '2003-09-15',
            'trang_thai' => 'Đang sống'
        ]);

        $anhBach = ThanhVien::create([
            'chi_nhanh_id' => $cnId,
            'ho_ten' => 'Vũ Hoàng Bách',
            'ten_goi' => 'Anh Bách',
            'gioi_tinh' => 'Nam',
            'ngay_sinh' => '1985-05-18',
            'cha_id' => $ongHoang->id,
            'me_id' => $baHaDoi2->id,
            'noi_sinh' => 'Hà Nội',
            'nghe_nghiep' => 'Kinh doanh tự do',
            'doi_thu' => 3,
            'loai_quan_he' => 'Chính',
            'trang_thai' => 'Còn sống',
            'ghi_chu' => 'Con út bà Hà ông Hoàng.',
            'avatar' => 'https://images.unsplash.com/photo-1506794778202-cad84cf45f1d?auto=format&fit=crop&q=80&w=200'
        ]);

        $chiDiep = ThanhVien::create([
            'chi_nhanh_id' => $cnId,
            'ho_ten' => 'Phạm Ngọc Diệp',
            'ten_goi' => 'Chị Diệp',
            'gioi_tinh' => 'Nữ',
            'ngay_sinh' => '1988-02-14',
            'noi_sinh' => 'Hải Phòng',
            'nghe_nghiep' => 'Chuyên viên truyền thông',
            'doi_thu' => 3,
            'loai_quan_he' => 'Vợ/Chồng',
            'spouse_of_id' => $anhBach->id,
            'trang_thai' => 'Còn sống',
            'ghi_chu' => 'Vợ anh Vũ Hoàng Bách.',
            'avatar' => 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&q=80&w=200'
        ]);

        VoChong::create([
            'chong_id' => $anhBach->id,
            'vo_id' => $chiDiep->id,
            'ngay_cuoi' => '2011-12-25',
            'trang_thai' => 'Đang sống'
        ]);

        // Con của ông Khang & bà Thủy (Mới thêm)
        $anhSon = ThanhVien::create([
            'chi_nhanh_id' => $cnId,
            'ho_ten' => 'Nguyễn Đức Sơn',
            'ten_goi' => 'Anh Sơn',
            'gioi_tinh' => 'Nam',
            'ngay_sinh' => '1984-09-30',
            'cha_id' => $ongKhang->id,
            'me_id' => $baThuyDoi2->id,
            'noi_sinh' => 'Hà Nội',
            'nghe_nghiep' => 'Luật sư',
            'doi_thu' => 3,
            'loai_quan_he' => 'Chính',
            'trang_thai' => 'Còn sống',
            'ghi_chu' => 'Con cả ông Khang bà Thủy.',
            'avatar' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?auto=format&fit=crop&q=80&w=200'
        ]);

        $chiOanh = ThanhVien::create([
            'chi_nhanh_id' => $cnId,
            'ho_ten' => 'Trần Kim Oanh',
            'ten_goi' => 'Chị Oanh',
            'gioi_tinh' => 'Nữ',
            'ngay_sinh' => '1987-07-15',
            'noi_sinh' => 'Hà Nam',
            'nghe_nghiep' => 'Thẩm phán',
            'doi_thu' => 3,
            'loai_quan_he' => 'Vợ/Chồng',
            'spouse_of_id' => $anhSon->id,
            'trang_thai' => 'Còn sống',
            'ghi_chu' => 'Vợ anh Nguyễn Đức Sơn.',
            'avatar' => 'https://images.unsplash.com/photo-1544005313-94ddf0286df2?auto=format&fit=crop&q=80&w=200'
        ]);

        VoChong::create([
            'chong_id' => $anhSon->id,
            'vo_id' => $chiOanh->id,
            'ngay_cuoi' => '2009-09-09',
            'trang_thai' => 'Đang sống'
        ]);

        $chiMaiAnhDoi3 = ThanhVien::create([
            'chi_nhanh_id' => $cnId,
            'ho_ten' => 'Nguyễn Thị Mai Anh',
            'ten_goi' => 'Chị Mai Anh',
            'gioi_tinh' => 'Nữ',
            'ngay_sinh' => '1987-12-05',
            'cha_id' => $ongKhang->id,
            'me_id' => $baThuyDoi2->id,
            'noi_sinh' => 'Hà Nội',
            'nghe_nghiep' => 'Giáo viên hóa',
            'doi_thu' => 3,
            'loai_quan_he' => 'Chính',
            'trang_thai' => 'Còn sống',
            'ghi_chu' => 'Con thứ hai ông Khang bà Thủy.',
            'avatar' => 'https://images.unsplash.com/photo-1580489944761-15a19d654956?auto=format&fit=crop&q=80&w=200'
        ]);

        $anhLongRe = ThanhVien::create([
            'chi_nhanh_id' => $cnId,
            'ho_ten' => 'Đỗ Hoàng Long',
            'ten_goi' => 'Anh Long',
            'gioi_tinh' => 'Nam',
            'ngay_sinh' => '1984-06-18',
            'noi_sinh' => 'Hải Dương',
            'nghe_nghiep' => 'Kỹ sư xây dựng',
            'doi_thu' => 3,
            'loai_quan_he' => 'Vợ/Chồng',
            'spouse_of_id' => $chiMaiAnhDoi3->id,
            'trang_thai' => 'Còn sống',
            'ghi_chu' => 'Chồng chị Nguyễn Thị Mai Anh.',
            'avatar' => 'https://images.unsplash.com/photo-1492562080023-ab3db95bfbce?auto=format&fit=crop&q=80&w=200'
        ]);

        VoChong::create([
            'chong_id' => $anhLongRe->id,
            'vo_id' => $chiMaiAnhDoi3->id,
            'ngay_cuoi' => '2010-11-20',
            'trang_thai' => 'Đang sống'
        ]);

        $anhHungConKhang = ThanhVien::create([
            'chi_nhanh_id' => $cnId,
            'ho_ten' => 'Nguyễn Đức Hùng',
            'ten_goi' => 'Anh Hùng',
            'gioi_tinh' => 'Nam',
            'ngay_sinh' => '1990-02-14',
            'cha_id' => $ongKhang->id,
            'me_id' => $baThuyDoi2->id,
            'noi_sinh' => 'Hà Nội',
            'nghe_nghiep' => 'Chuyên viên ngân hàng',
            'doi_thu' => 3,
            'loai_quan_he' => 'Chính',
            'trang_thai' => 'Còn sống',
            'ghi_chu' => 'Con út ông Khang bà Thủy.',
            'avatar' => 'https://images.unsplash.com/photo-1506794778202-cad84cf45f1d?auto=format&fit=crop&q=80&w=200'
        ]);

        $chiVan = ThanhVien::create([
            'chi_nhanh_id' => $cnId,
            'ho_ten' => 'Lê Thanh Vân',
            'ten_goi' => 'Chị Vân',
            'gioi_tinh' => 'Nữ',
            'ngay_sinh' => '1993-10-15',
            'noi_sinh' => 'Hưng Yên',
            'nghe_nghiep' => 'Kiểm toán viên',
            'doi_thu' => 3,
            'loai_quan_he' => 'Vợ/Chồng',
            'spouse_of_id' => $anhHungConKhang->id,
            'trang_thai' => 'Còn sống',
            'ghi_chu' => 'Vợ anh Nguyễn Đức Hùng (con ông Khang).',
            'avatar' => 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&q=80&w=200'
        ]);

        VoChong::create([
            'chong_id' => $anhHungConKhang->id,
            'vo_id' => $chiVan->id,
            'ngay_cuoi' => '2015-06-18',
            'trang_thai' => 'Đang sống'
        ]);


        // ------------------ ĐỜI 4 ------------------
        // Con của anh Nam & chị Ngọc
        // 1. Nguyễn Đức Anh
        $anhDucAnh = ThanhVien::create([
            'chi_nhanh_id' => $cnId,
            'ho_ten' => 'Nguyễn Đức Anh',
            'ten_goi' => 'Đức Anh',
            'gioi_tinh' => 'Nam',
            'ngay_sinh' => '1993-06-20',
            'cha_id' => $anhNam->id,
            'me_id' => $chiNgoc->id,
            'noi_sinh' => 'Hà Nội',
            'nghe_nghiep' => 'Chuyên gia IT',
            'doi_thu' => 4,
            'loai_quan_he' => 'Chính',
            'trang_thai' => 'Còn sống',
            'ghi_chu' => 'Tốt nghiệp thạc sĩ công nghệ phần mềm tại Nhật Bản. Sáng lập hệ thống quản lý gia phả số.',
            'avatar' => 'https://images.unsplash.com/photo-1539571696357-5a69c17a67c6?auto=format&fit=crop&q=80&w=200'
        ]);

        // Vợ 1 của Nguyễn Đức Anh: Hoàng Thu Trang (matching the screenshot)
        $chiTrang = ThanhVien::create([
            'chi_nhanh_id' => $cnId,
            'ho_ten' => 'Hoàng Thu Trang',
            'ten_goi' => 'Chị Trang',
            'gioi_tinh' => 'Nữ',
            'ngay_sinh' => '1972-04-05',
            'noi_sinh' => 'Hà Nội',
            'nghe_nghiep' => 'Kế toán trưởng',
            'doi_thu' => 4,
            'loai_quan_he' => 'Vợ/Chồng',
            'spouse_of_id' => $anhDucAnh->id,
            'trang_thai' => 'Còn sống',
            'ghi_chu' => 'Trợ thủ đắc lực quán xuyến việc kinh doanh của gia đình, người mẹ thông thái.',
            'avatar' => 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&q=80&w=200'
        ]);

        VoChong::create([
            'chong_id' => $anhDucAnh->id,
            'vo_id' => $chiTrang->id,
            'ngay_cuoi' => '2016-12-25',
            'trang_thai' => 'Đang sống'
        ]);

        // Vợ 2 của Nguyễn Đức Anh: Trần Thị Tuyết Nhi (matching the screenshot)
        $chiTuyenNhi = ThanhVien::create([
            'chi_nhanh_id' => $cnId,
            'ho_ten' => 'Trần Thị Tuyết Nhi',
            'ten_goi' => 'Tuyết Nhi',
            'gioi_tinh' => 'Nữ',
            'ngay_sinh' => '2006-05-28',
            'noi_sinh' => 'Hà Nội',
            'nghe_nghiep' => 'Sinh viên nghệ thuật',
            'doi_thu' => 4,
            'loai_quan_he' => 'Vợ/Chồng',
            'spouse_of_id' => $anhDucAnh->id,
            'trang_thai' => 'Còn sống',
            'ghi_chu' => 'Vợ thứ hai của anh Nguyễn Đức Anh. Năng động và có năng khiếu âm nhạc.',
            'avatar' => 'https://images.unsplash.com/photo-1517841905240-472988babdf9?auto=format&fit=crop&q=80&w=200'
        ]);

        VoChong::create([
            'chong_id' => $anhDucAnh->id,
            'vo_id' => $chiTuyenNhi->id,
            'ngay_cuoi' => '2024-04-10',
            'trang_thai' => 'Đang sống'
        ]);

        // 2. Nguyễn Minh Vy
        $chiVy = ThanhVien::create([
            'chi_nhanh_id' => $cnId,
            'ho_ten' => 'Nguyễn Minh Vy',
            'ten_goi' => 'Minh Vy',
            'email' => 'minhvy@master.com',
            'gioi_tinh' => 'Nữ',
            'ngay_sinh' => '1996-09-02',
            'cha_id' => $anhNam->id,
            'me_id' => $chiNgoc->id,
            'noi_sinh' => 'Hà Nội',
            'nghe_nghiep' => 'Nhà thiết kế đồ họa',
            'doi_thu' => 4,
            'loai_quan_he' => 'Chính',
            'trang_thai' => 'Còn sống',
            'ghi_chu' => 'Hiện đang du học tự túc thạc sĩ mỹ thuật tại Milan, Ý.',
            'avatar' => 'https://images.unsplash.com/photo-1524504388940-b1c1722653e1?auto=format&fit=crop&q=80&w=200'
        ]);

        $anhLong = ThanhVien::create([
            'chi_nhanh_id' => $cnId,
            'ho_ten' => 'Trần Hoàng Long',
            'ten_goi' => 'Anh Long',
            'gioi_tinh' => 'Nam',
            'ngay_sinh' => '1994-08-12',
            'noi_sinh' => 'Hà Nội',
            'nghe_nghiep' => 'Nhiếp ảnh gia',
            'doi_thu' => 4,
            'loai_quan_he' => 'Vợ/Chồng',
            'spouse_of_id' => $chiVy->id,
            'trang_thai' => 'Còn sống',
            'ghi_chu' => 'Chồng Nguyễn Minh Vy, nhiếp ảnh gia tự do.',
            'avatar' => 'https://images.unsplash.com/photo-1506794778202-cad84cf45f1d?auto=format&fit=crop&q=80&w=200'
        ]);

        VoChong::create([
            'chong_id' => $anhLong->id,
            'vo_id' => $chiVy->id,
            'ngay_cuoi' => '2020-05-20',
            'trang_thai' => 'Đang sống'
        ]);

        // Con của chị Hương & anh Tuấn
        // 3. Phạm Minh Đức
        $anhDuc = ThanhVien::create([
            'chi_nhanh_id' => $cnId,
            'ho_ten' => 'Phạm Minh Đức',
            'ten_goi' => 'Minh Đức',
            'gioi_tinh' => 'Nam',
            'ngay_sinh' => '1998-03-24',
            'cha_id' => $ongTuan->id,
            'me_id' => $chiHuong->id,
            'noi_sinh' => 'Hà Nội',
            'nghe_nghiep' => 'Lập trình viên game',
            'doi_thu' => 4,
            'loai_quan_he' => 'Chính',
            'trang_thai' => 'Còn sống',
            'ghi_chu' => 'Con trai lớn của anh Tuấn chị Hương, hiện đang công tác tại FPT Software.',
            'avatar' => 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?auto=format&fit=crop&q=80&w=200'
        ]);

        // 4. Phạm Thị Hồng Nhung
        $chiNhung = ThanhVien::create([
            'chi_nhanh_id' => $cnId,
            'ho_ten' => 'Phạm Thị Hồng Nhung',
            'ten_goi' => 'Hồng Nhung',
            'gioi_tinh' => 'Nữ',
            'ngay_sinh' => '2001-11-15',
            'cha_id' => $ongTuan->id,
            'me_id' => $chiHuong->id,
            'noi_sinh' => 'Hà Nội',
            'nghe_nghiep' => 'Biên dịch viên',
            'doi_thu' => 4,
            'loai_quan_he' => 'Chính',
            'trang_thai' => 'Còn sống',
            'ghi_chu' => 'Con gái thứ hai của anh Tuấn chị Hương, tốt nghiệp đại học Ngoại ngữ.',
            'avatar' => 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&q=80&w=200'
        ]);

        // Con của anh Bình & chị Hà
        // 5. Nguyễn Đức Minh
        $anhMinh = ThanhVien::create([
            'chi_nhanh_id' => $cnId,
            'ho_ten' => 'Nguyễn Đức Minh',
            'ten_goi' => 'Đức Minh',
            'gioi_tinh' => 'Nam',
            'ngay_sinh' => '2012-07-10',
            'cha_id' => $anhBinh->id,
            'me_id' => $chiHa->id,
            'noi_sinh' => 'Hà Nội',
            'nghe_nghiep' => 'Học sinh trung học',
            'doi_thu' => 4,
            'loai_quan_he' => 'Chính',
            'trang_thai' => 'Còn sống',
            'ghi_chu' => 'Con trai lớn anh Bình chị Hà, học sinh chuyên toán.',
            'avatar' => 'https://images.unsplash.com/photo-1503919545889-aef636e10ad4?auto=format&fit=crop&q=80&w=200'
        ]);

        $beBaoTram = ThanhVien::create([
            'chi_nhanh_id' => $cnId,
            'ho_ten' => 'Nguyễn Thị Bảo Trâm',
            'ten_goi' => 'Bé Bảo Trâm',
            'gioi_tinh' => 'Nữ',
            'ngay_sinh' => '2015-03-18',
            'cha_id' => $anhBinh->id,
            'me_id' => $chiHa->id,
            'noi_sinh' => 'Hà Nội',
            'nghe_nghiep' => 'Học sinh tiểu học',
            'doi_thu' => 4,
            'loai_quan_he' => 'Chính',
            'trang_thai' => 'Còn sống',
            'ghi_chu' => 'Con gái út anh Bình chị Hà.',
            'avatar' => 'https://images.unsplash.com/photo-1519085360753-af0119f7cbe7?auto=format&fit=crop&q=80&w=200'
        ]);

        // Con của chị Bích Ngọc & anh Nam
        // 6. Đỗ Minh Trí
        $anhTri = ThanhVien::create([
            'chi_nhanh_id' => $cnId,
            'ho_ten' => 'Đỗ Minh Trí',
            'ten_goi' => 'Minh Trí',
            'gioi_tinh' => 'Nam',
            'ngay_sinh' => '1996-03-18',
            'cha_id' => $anhNamConRe->id,
            'me_id' => $chiBichNgoc->id,
            'noi_sinh' => 'Hà Nội',
            'nghe_nghiep' => 'Kỹ sư mạng',
            'doi_thu' => 4,
            'loai_quan_he' => 'Chính',
            'trang_thai' => 'Còn sống',
            'ghi_chu' => 'Cháu ngoại bà Lan, đang làm việc tại tập đoàn Viettel.',
            'avatar' => 'https://images.unsplash.com/photo-1539571696357-5a69c17a67c6?auto=format&fit=crop&q=80&w=200'
        ]);

        // 7. Đỗ Thu Thủy
        $chiThuy = ThanhVien::create([
            'chi_nhanh_id' => $cnId,
            'ho_ten' => 'Đỗ Thu Thủy',
            'ten_goi' => 'Thu Thủy',
            'gioi_tinh' => 'Nữ',
            'ngay_sinh' => '1999-08-25',
            'cha_id' => $anhNamConRe->id,
            'me_id' => $chiBichNgoc->id,
            'noi_sinh' => 'Hà Nội',
            'nghe_nghiep' => 'Chuyên viên nhân sự',
            'doi_thu' => 4,
            'loai_quan_he' => 'Chính',
            'trang_thai' => 'Còn sống',
            'ghi_chu' => 'Cháu ngoại bà Lan, tốt nghiệp đại học Kinh tế Quốc dân.',
            'avatar' => 'https://images.unsplash.com/photo-1524504388940-b1c1722653e1?auto=format&fit=crop&q=80&w=200'
        ]);

        // Con của anh Lê Anh Tuấn & chị Tuyết
        // 8. Lê Tuấn Tú
        $beTuanTu = ThanhVien::create([
            'chi_nhanh_id' => $cnId,
            'ho_ten' => 'Lê Tuấn Tú',
            'ten_goi' => 'Tuấn Tú',
            'gioi_tinh' => 'Nam',
            'ngay_sinh' => '2001-12-30',
            'cha_id' => $anhTuanConNgoai->id,
            'me_id' => $chiTuyen->id,
            'noi_sinh' => 'Hà Nội',
            'nghe_nghiep' => 'Kỹ sư CNTT',
            'doi_thu' => 4,
            'loai_quan_he' => 'Chính',
            'trang_thai' => 'Còn sống',
            'ghi_chu' => 'Cháu ngoại bà Lan, kỹ sư CNTT làm việc tại FPT.',
            'avatar' => 'https://images.unsplash.com/photo-1506794778202-cad84cf45f1d?auto=format&fit=crop&q=80&w=200'
        ]);

        $beThuThao = ThanhVien::create([
            'chi_nhanh_id' => $cnId,
            'ho_ten' => 'Lê Thị Thu Thảo',
            'ten_goi' => 'Bé Thu Thảo',
            'gioi_tinh' => 'Nữ',
            'ngay_sinh' => '2003-04-15',
            'cha_id' => $anhTuanConNgoai->id,
            'me_id' => $chiTuyen->id,
            'noi_sinh' => 'Hà Nội',
            'nghe_nghiep' => 'Chuyên viên tài chính',
            'doi_thu' => 4,
            'loai_quan_he' => 'Chính',
            'trang_thai' => 'Còn sống',
            'ghi_chu' => 'Con thứ hai của anh Lê Anh Tuấn chị Tuyết.',
            'avatar' => 'https://images.unsplash.com/photo-1517841905240-472988babdf9?auto=format&fit=crop&q=80&w=200'
        ]);

        // Con của anh Hải & chị Thảo
        // 9. Nguyễn Đức Duy (con anh Hải chị Thảo - matching screenshot)
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

        $chiMyLinh = ThanhVien::create([
            'chi_nhanh_id' => $cnId,
            'ho_ten' => 'Đỗ Thị Mỹ Linh',
            'ten_goi' => 'Mỹ Linh',
            'gioi_tinh' => 'Nữ',
            'ngay_sinh' => '2003-04-18',
            'noi_sinh' => 'Hải Phòng',
            'nghe_nghiep' => 'Chuyên viên ngân hàng',
            'doi_thu' => 4,
            'loai_quan_he' => 'Vợ/Chồng',
            'spouse_of_id' => $anhDuy->id,
            'trang_thai' => 'Còn sống',
            'ghi_chu' => 'Vợ anh Nguyễn Đức Duy.',
            'avatar' => 'https://images.unsplash.com/photo-1544005313-94ddf0286df2?auto=format&fit=crop&q=80&w=200'
        ]);

        VoChong::create([
            'chong_id' => $anhDuy->id,
            'vo_id' => $chiMyLinh->id,
            'ngay_cuoi' => '2023-08-20',
            'trang_thai' => 'Đang sống'
        ]);

        // 10. Nguyễn Thị Quỳnh Chi (Con thứ 2 của anh Hải & chị Thảo)
        $chiQuynhChi = ThanhVien::create([
            'chi_nhanh_id' => $cnId,
            'ho_ten' => 'Nguyễn Thị Quỳnh Chi',
            'ten_goi' => 'Quỳnh Chi',
            'gioi_tinh' => 'Nữ',
            'ngay_sinh' => '2002-08-20',
            'cha_id' => $anhHai->id,
            'me_id' => $chiThao->id,
            'noi_sinh' => 'Hà Nội',
            'nghe_nghiep' => 'Chuyên viên Marketing',
            'doi_thu' => 4,
            'loai_quan_he' => 'Chính',
            'trang_thai' => 'Còn sống',
            'ghi_chu' => 'Con gái út anh Hải chị Thảo.',
            'avatar' => 'https://images.unsplash.com/photo-1517841905240-472988babdf9?auto=format&fit=crop&q=80&w=200'
        ]);

        // Con của anh Hạnh & chị Dung
        // 11. Nguyễn Đức Thiện
        $beThien = ThanhVien::create([
            'chi_nhanh_id' => $cnId,
            'ho_ten' => 'Nguyễn Đức Thiện',
            'ten_goi' => 'Bé Thiện',
            'gioi_tinh' => 'Nam',
            'ngay_sinh' => '2006-03-25',
            'cha_id' => $anhHanhConPhuc->id,
            'me_id' => $chiDungConDau->id,
            'noi_sinh' => 'Hà Nội',
            'nghe_nghiep' => 'Học sinh phổ thông',
            'doi_thu' => 4,
            'loai_quan_he' => 'Chính',
            'trang_thai' => 'Còn sống',
            'ghi_chu' => 'Cháu nội ông Phúc, hiếu học và ham tìm hiểu khoa học.',
            'avatar' => 'https://images.unsplash.com/photo-1503919545889-aef636e10ad4?auto=format&fit=crop&q=80&w=200'
        ]);

        // 12. Nguyễn Thị Mỹ Hạnh
        $beMyHanh = ThanhVien::create([
            'chi_nhanh_id' => $cnId,
            'ho_ten' => 'Nguyễn Thị Mỹ Hạnh',
            'ten_goi' => 'Bé Mỹ Hạnh',
            'gioi_tinh' => 'Nữ',
            'ngay_sinh' => '2010-09-14',
            'cha_id' => $anhHanhConPhuc->id,
            'me_id' => $chiDungConDau->id,
            'noi_sinh' => 'Hà Nội',
            'nghe_nghiep' => 'Học sinh tiểu học',
            'doi_thu' => 4,
            'loai_quan_he' => 'Chính',
            'trang_thai' => 'Còn sống',
            'ghi_chu' => 'Cháu nội út ông Phúc, ngoan ngoãn chăm chỉ.',
            'avatar' => 'https://images.unsplash.com/photo-1519085360753-af0119f7cbe7?auto=format&fit=crop&q=80&w=200'
        ]);

        // Con của chị Bích Thủy & anh Cường (Đời 4)
        // 13. Trần Minh Hoàng
        $anhHoang = ThanhVien::create([
            'chi_nhanh_id' => $cnId,
            'ho_ten' => 'Trần Minh Hoàng',
            'ten_goi' => 'Minh Hoàng',
            'gioi_tinh' => 'Nam',
            'ngay_sinh' => '2000-11-05',
            'cha_id' => $anhCuongConRe->id,
            'me_id' => $chiThuyConDung->id,
            'noi_sinh' => 'Hà Nội',
            'nghe_nghiep' => 'Kỹ sư phần mềm',
            'doi_thu' => 4,
            'loai_quan_he' => 'Chính',
            'trang_thai' => 'Còn sống',
            'ghi_chu' => 'Con cả chị Thủy anh Cường.',
            'avatar' => 'https://images.unsplash.com/photo-1506794778202-cad84cf45f1d?auto=format&fit=crop&q=80&w=200'
        ]);

        $chiTrangConDau = ThanhVien::create([
            'chi_nhanh_id' => $cnId,
            'ho_ten' => 'Phạm Thu Trang',
            'ten_goi' => 'Thu Trang',
            'gioi_tinh' => 'Nữ',
            'ngay_sinh' => '2003-03-12',
            'noi_sinh' => 'Hà Nội',
            'nghe_nghiep' => 'Kế toán viên',
            'doi_thu' => 4,
            'loai_quan_he' => 'Vợ/Chồng',
            'spouse_of_id' => $anhHoang->id,
            'trang_thai' => 'Còn sống',
            'ghi_chu' => 'Vợ anh Trần Minh Hoàng.',
            'avatar' => 'https://images.unsplash.com/photo-1544005313-94ddf0286df2?auto=format&fit=crop&q=80&w=200'
        ]);

        VoChong::create([
            'chong_id' => $anhHoang->id,
            'vo_id' => $chiTrangConDau->id,
            'ngay_cuoi' => '2024-06-18',
            'trang_thai' => 'Đang sống'
        ]);

        // 14. Trần Thu Hà
        $chiHaConThuy = ThanhVien::create([
            'chi_nhanh_id' => $cnId,
            'ho_ten' => 'Trần Thu Hà',
            'ten_goi' => 'Thu Hà',
            'gioi_tinh' => 'Nữ',
            'ngay_sinh' => '2003-08-15',
            'cha_id' => $anhCuongConRe->id,
            'me_id' => $chiThuyConDung->id,
            'noi_sinh' => 'Hà Nội',
            'nghe_nghiep' => 'Sinh viên Ngoại ngữ',
            'doi_thu' => 4,
            'loai_quan_he' => 'Chính',
            'trang_thai' => 'Còn sống',
            'ghi_chu' => 'Con thứ hai chị Thủy anh Cường.',
            'avatar' => 'https://images.unsplash.com/photo-1524504388940-b1c1722653e1?auto=format&fit=crop&q=80&w=200'
        ]);

        // Con của anh Tuấn & chị Hương (Đời 4)
        // 15. Nguyễn Đức Huy
        $beHuyConTuan = ThanhVien::create([
            'chi_nhanh_id' => $cnId,
            'ho_ten' => 'Nguyễn Đức Huy',
            'ten_goi' => 'Đức Huy',
            'gioi_tinh' => 'Nam',
            'ngay_sinh' => '2010-06-18',
            'cha_id' => $anhTuanConDung->id,
            'me_id' => $chiHuongConDau->id,
            'noi_sinh' => 'Hải Dương',
            'nghe_nghiep' => 'Học sinh trung học',
            'doi_thu' => 4,
            'loai_quan_he' => 'Chính',
            'trang_thai' => 'Còn sống',
            'ghi_chu' => 'Con cả anh Tuấn chị Hương.',
            'avatar' => 'https://images.unsplash.com/photo-1503919545889-aef636e10ad4?auto=format&fit=crop&q=80&w=200'
        ]);

        // 16. Nguyễn Thị Minh Thư
        $beThuConTuan = ThanhVien::create([
            'chi_nhanh_id' => $cnId,
            'ho_ten' => 'Nguyễn Thị Minh Thư',
            'ten_goi' => 'Minh Thư',
            'gioi_tinh' => 'Nữ',
            'ngay_sinh' => '2013-09-25',
            'cha_id' => $anhTuanConDung->id,
            'me_id' => $chiHuongConDau->id,
            'noi_sinh' => 'Hải Dương',
            'nghe_nghiep' => 'Học sinh tiểu học',
            'doi_thu' => 4,
            'loai_quan_he' => 'Chính',
            'trang_thai' => 'Còn sống',
            'ghi_chu' => 'Con thứ hai anh Tuấn chị Hương.',
            'avatar' => 'https://images.unsplash.com/photo-1519085360753-af0119f7cbe7?auto=format&fit=crop&q=80&w=200'
        ]);

        // Con của anh Lộc & chị Thanh (Đời 4)
        // 17. Nguyễn Đức Trọng
        $beTrongConLoc = ThanhVien::create([
            'chi_nhanh_id' => $cnId,
            'ho_ten' => 'Nguyễn Đức Trọng',
            'ten_goi' => 'Đức Trọng',
            'gioi_tinh' => 'Nam',
            'ngay_sinh' => '2008-10-15',
            'cha_id' => $anhLocConPhuc->id,
            'me_id' => $chiThanhConDau->id,
            'noi_sinh' => 'Hà Nội',
            'nghe_nghiep' => 'Học sinh phổ thông',
            'doi_thu' => 4,
            'loai_quan_he' => 'Chính',
            'trang_thai' => 'Còn sống',
            'ghi_chu' => 'Con cả anh Lộc chị Thanh.',
            'avatar' => 'https://images.unsplash.com/photo-1503919545889-aef636e10ad4?auto=format&fit=crop&q=80&w=200'
        ]);

        // 18. Nguyễn Thị Mai Anh
        $beMaiAnhConLoc = ThanhVien::create([
            'chi_nhanh_id' => $cnId,
            'ho_ten' => 'Nguyễn Thị Mai Anh',
            'ten_goi' => 'Mai Anh',
            'gioi_tinh' => 'Nữ',
            'ngay_sinh' => '2011-12-05',
            'cha_id' => $anhLocConPhuc->id,
            'me_id' => $chiThanhConDau->id,
            'noi_sinh' => 'Hà Nội',
            'nghe_nghiep' => 'Học sinh tiểu học',
            'doi_thu' => 4,
            'loai_quan_he' => 'Chính',
            'trang_thai' => 'Còn sống',
            'ghi_chu' => 'Con thứ hai anh Lộc chị Thanh.',
            'avatar' => 'https://images.unsplash.com/photo-1519085360753-af0119f7cbe7?auto=format&fit=crop&q=80&w=200'
        ]);

        // Con của chị Hồng & anh Khánh (Đời 4)
        // 19. Đặng Thị Phương Thảo
        $beThaoConKhanh = ThanhVien::create([
            'chi_nhanh_id' => $cnId,
            'ho_ten' => 'Đặng Thị Phương Thảo',
            'ten_goi' => 'Phương Thảo',
            'gioi_tinh' => 'Nữ',
            'ngay_sinh' => '2007-02-14',
            'cha_id' => $anhKhanhConRe->id,
            'me_id' => $chiHongConPhuc->id,
            'noi_sinh' => 'Bắc Ninh',
            'nghe_nghiep' => 'Học sinh phổ thông',
            'doi_thu' => 4,
            'loai_quan_he' => 'Chính',
            'trang_thai' => 'Còn sống',
            'ghi_chu' => 'Con cả chị Hồng anh Khánh.',
            'avatar' => 'https://images.unsplash.com/photo-1517841905240-472988babdf9?auto=format&fit=crop&q=80&w=200'
        ]);

        // 20. Đặng Minh Quân
        $beQuanConKhanh = ThanhVien::create([
            'chi_nhanh_id' => $cnId,
            'ho_ten' => 'Đặng Minh Quân',
            'ten_goi' => 'Minh Quân',
            'gioi_tinh' => 'Nam',
            'ngay_sinh' => '2011-05-30',
            'cha_id' => $anhKhanhConRe->id,
            'me_id' => $chiHongConPhuc->id,
            'noi_sinh' => 'Bắc Ninh',
            'nghe_nghiep' => 'Học sinh tiểu học',
            'doi_thu' => 4,
            'loai_quan_he' => 'Chính',
            'trang_thai' => 'Còn sống',
            'ghi_chu' => 'Con thứ hai chị Hồng anh Khánh.',
            'avatar' => 'https://images.unsplash.com/photo-1503919545889-aef636e10ad4?auto=format&fit=crop&q=80&w=200'
        ]);

        // --- CÁC THÀNH VIÊN ĐỜI 4 THÊM MỚI ---

        // Con của Bình & Hà (Con thứ 3 - Mới thêm)
        $beAnConBinh = ThanhVien::create([
            'chi_nhanh_id' => $cnId,
            'ho_ten' => 'Nguyễn Đức An',
            'ten_goi' => 'Bé An',
            'gioi_tinh' => 'Nam',
            'ngay_sinh' => '2018-05-10',
            'cha_id' => $anhBinh->id,
            'me_id' => $chiHa->id,
            'noi_sinh' => 'Hà Nội',
            'nghe_nghiep' => 'Học sinh tiểu học',
            'doi_thu' => 4,
            'loai_quan_he' => 'Chính',
            'trang_thai' => 'Còn sống',
            'ghi_chu' => 'Con út anh Bình chị Hà.',
            'avatar' => 'https://images.unsplash.com/photo-1503919545889-aef636e10ad4?auto=format&fit=crop&q=80&w=200'
        ]);

        // Con của Hạnh & Dung (Con thứ 3 - Mới thêm)
        $beHuyenConHanh = ThanhVien::create([
            'chi_nhanh_id' => $cnId,
            'ho_ten' => 'Nguyễn Thị Khánh Huyền',
            'ten_goi' => 'Khánh Huyền',
            'gioi_tinh' => 'Nữ',
            'ngay_sinh' => '2014-08-15',
            'cha_id' => $anhHanhConPhuc->id,
            'me_id' => $chiDungConDau->id,
            'noi_sinh' => 'Hà Nội',
            'nghe_nghiep' => 'Học sinh tiểu học',
            'doi_thu' => 4,
            'loai_quan_he' => 'Chính',
            'trang_thai' => 'Còn sống',
            'ghi_chu' => 'Con thứ ba anh Hạnh chị Dung.',
            'avatar' => 'https://images.unsplash.com/photo-1519085360753-af0119f7cbe7?auto=format&fit=crop&q=80&w=200'
        ]);

        // Con của Mạnh & Thảo (Mới thêm)
        $beTungConManh = ThanhVien::create([
            'chi_nhanh_id' => $cnId,
            'ho_ten' => 'Nguyễn Đức Tùng',
            'ten_goi' => 'Bé Tùng',
            'gioi_tinh' => 'Nam',
            'ngay_sinh' => '2006-03-22',
            'cha_id' => $anhManh->id,
            'me_id' => $chiThaoManh->id,
            'noi_sinh' => 'Hà Nội',
            'nghe_nghiep' => 'Sinh viên',
            'doi_thu' => 4,
            'loai_quan_he' => 'Chính',
            'trang_thai' => 'Còn sống',
            'ghi_chu' => 'Con cả anh Mạnh chị Thảo.',
            'avatar' => 'https://images.unsplash.com/photo-1506794778202-cad84cf45f1d?auto=format&fit=crop&q=80&w=200'
        ]);

        $beChiConManh = ThanhVien::create([
            'chi_nhanh_id' => $cnId,
            'ho_ten' => 'Nguyễn Thị Mai Chi',
            'ten_goi' => 'Bé Mai Chi',
            'gioi_tinh' => 'Nữ',
            'ngay_sinh' => '2010-07-12',
            'cha_id' => $anhManh->id,
            'me_id' => $chiThaoManh->id,
            'noi_sinh' => 'Hà Nội',
            'nghe_nghiep' => 'Học sinh trung học',
            'doi_thu' => 4,
            'loai_quan_he' => 'Chính',
            'trang_thai' => 'Còn sống',
            'ghi_chu' => 'Con út anh Mạnh chị Thảo.',
            'avatar' => 'https://images.unsplash.com/photo-1517841905240-472988babdf9?auto=format&fit=crop&q=80&w=200'
        ]);

        // Con của Trung & Lan Anh (Vợ 1 - Mới thêm)
        $beKhanhConTrung = ThanhVien::create([
            'chi_nhanh_id' => $cnId,
            'ho_ten' => 'Nguyễn Đức Nam Khánh',
            'ten_goi' => 'Bé Nam Khánh',
            'gioi_tinh' => 'Nam',
            'ngay_sinh' => '2010-10-15',
            'cha_id' => $anhTrung->id,
            'me_id' => $chiLanAnh->id,
            'noi_sinh' => 'Hà Nội',
            'nghe_nghiep' => 'Học sinh trung học',
            'doi_thu' => 4,
            'loai_quan_he' => 'Chính',
            'trang_thai' => 'Còn sống',
            'ghi_chu' => 'Con trai anh Trung với vợ đầu Lan Anh.',
            'avatar' => 'https://images.unsplash.com/photo-1503919545889-aef636e10ad4?auto=format&fit=crop&q=80&w=200'
        ]);

        // Con của Trung & Thanh Mai (Vợ 2 - Mới thêm)
        $beKietConTrung = ThanhVien::create([
            'chi_nhanh_id' => $cnId,
            'ho_ten' => 'Nguyễn Đức Tuấn Kiệt',
            'ten_goi' => 'Bé Tuấn Kiệt',
            'gioi_tinh' => 'Nam',
            'ngay_sinh' => '2019-12-05',
            'cha_id' => $anhTrung->id,
            'me_id' => $chiThanhMai->id,
            'noi_sinh' => 'Hà Nội',
            'nghe_nghiep' => 'Học sinh tiểu học',
            'doi_thu' => 4,
            'loai_quan_he' => 'Chính',
            'trang_thai' => 'Còn sống',
            'ghi_chu' => 'Con cả anh Trung với vợ hai Thanh Mai.',
            'avatar' => 'https://images.unsplash.com/photo-1503919545889-aef636e10ad4?auto=format&fit=crop&q=80&w=200'
        ]);

        $beKhueConTrung = ThanhVien::create([
            'chi_nhanh_id' => $cnId,
            'ho_ten' => 'Nguyễn Thị Minh Khuê',
            'ten_goi' => 'Bé Minh Khuê',
            'gioi_tinh' => 'Nữ',
            'ngay_sinh' => '2022-08-25',
            'cha_id' => $anhTrung->id,
            'me_id' => $chiThanhMai->id,
            'noi_sinh' => 'Hà Nội',
            'nghe_nghiep' => 'Trẻ em',
            'doi_thu' => 4,
            'loai_quan_he' => 'Chính',
            'trang_thai' => 'Còn sống',
            'ghi_chu' => 'Con út anh Trung với vợ hai Thanh Mai.',
            'avatar' => 'https://images.unsplash.com/photo-1519085360753-af0119f7cbe7?auto=format&fit=crop&q=80&w=200'
        ]);

        // Con của Mai & Minh (Mới thêm)
        $beAnhConMai = ThanhVien::create([
            'chi_nhanh_id' => $cnId,
            'ho_ten' => 'Trần Đức Anh',
            'ten_goi' => 'Đức Anh (Mai)',
            'gioi_tinh' => 'Nam',
            'ngay_sinh' => '2011-09-12',
            'cha_id' => $anhMinhRe->id,
            'me_id' => $chiMai->id,
            'noi_sinh' => 'Hà Nội',
            'nghe_nghiep' => 'Học sinh tiểu học',
            'doi_thu' => 4,
            'loai_quan_he' => 'Chính',
            'trang_thai' => 'Còn sống',
            'ghi_chu' => 'Con cả chị Mai anh Minh.',
            'avatar' => 'https://images.unsplash.com/photo-1503919545889-aef636e10ad4?auto=format&fit=crop&q=80&w=200'
        ]);

        $beVyConMai = ThanhVien::create([
            'chi_nhanh_id' => $cnId,
            'ho_ten' => 'Trần Ngọc Bảo Vy',
            'ten_goi' => 'Bảo Vy',
            'gioi_tinh' => 'Nữ',
            'ngay_sinh' => '2015-11-20',
            'cha_id' => $anhMinhRe->id,
            'me_id' => $chiMai->id,
            'noi_sinh' => 'Hà Nội',
            'nghe_nghiep' => 'Học sinh tiểu học',
            'doi_thu' => 4,
            'loai_quan_he' => 'Chính',
            'trang_thai' => 'Còn sống',
            'ghi_chu' => 'Con út chị Mai anh Minh.',
            'avatar' => 'https://images.unsplash.com/photo-1519085360753-af0119f7cbe7?auto=format&fit=crop&q=80&w=200'
        ]);

        // Con của Lâm & Trang (Mới thêm)
        $bePhongConLam = ThanhVien::create([
            'chi_nhanh_id' => $cnId,
            'ho_ten' => 'Vũ Hoàng Phong',
            'ten_goi' => 'Bé Phong',
            'gioi_tinh' => 'Nam',
            'ngay_sinh' => '2006-05-18',
            'cha_id' => $anhLam->id,
            'me_id' => $chiTrangLam->id,
            'noi_sinh' => 'Hà Nội',
            'nghe_nghiep' => 'Sinh viên',
            'doi_thu' => 4,
            'loai_quan_he' => 'Chính',
            'trang_thai' => 'Còn sống',
            'ghi_chu' => 'Con cả anh Lâm chị Trang.',
            'avatar' => 'https://images.unsplash.com/photo-1506794778202-cad84cf45f1d?auto=format&fit=crop&q=80&w=200'
        ]);

        $beLinhConLam = ThanhVien::create([
            'chi_nhanh_id' => $cnId,
            'ho_ten' => 'Vũ Khánh Linh',
            'ten_goi' => 'Bé Khánh Linh',
            'gioi_tinh' => 'Nữ',
            'ngay_sinh' => '2011-09-30',
            'cha_id' => $anhLam->id,
            'me_id' => $chiTrangLam->id,
            'noi_sinh' => 'Hà Nội',
            'nghe_nghiep' => 'Học sinh tiểu học',
            'doi_thu' => 4,
            'loai_quan_he' => 'Chính',
            'trang_thai' => 'Còn sống',
            'ghi_chu' => 'Con út anh Lâm chị Trang.',
            'avatar' => 'https://images.unsplash.com/photo-1517841905240-472988babdf9?auto=format&fit=crop&q=80&w=200'
        ]);

        // Con của Thu Hương & Huy (Mới thêm)
        $beNamConHuong = ThanhVien::create([
            'chi_nhanh_id' => $cnId,
            'ho_ten' => 'Nguyễn Hoàng Nam',
            'ten_goi' => 'Bé Hoàng Nam',
            'gioi_tinh' => 'Nam',
            'ngay_sinh' => '2005-03-22',
            'cha_id' => $anhHuyRe->id,
            'me_id' => $chiThuHuong->id,
            'noi_sinh' => 'Hà Nội',
            'nghe_nghiep' => 'Sinh viên',
            'doi_thu' => 4,
            'loai_quan_he' => 'Chính',
            'trang_thai' => 'Còn sống',
            'ghi_chu' => 'Con cả chị Hương anh Huy.',
            'avatar' => 'https://images.unsplash.com/photo-1506794778202-cad84cf45f1d?auto=format&fit=crop&q=80&w=200'
        ]);

        $beHangConHuong = ThanhVien::create([
            'chi_nhanh_id' => $cnId,
            'ho_ten' => 'Nguyễn Thị Minh Hằng',
            'ten_goi' => 'Bé Minh Hằng',
            'gioi_tinh' => 'Nữ',
            'ngay_sinh' => '2009-12-14',
            'cha_id' => $anhHuyRe->id,
            'me_id' => $chiThuHuong->id,
            'noi_sinh' => 'Hà Nội',
            'nghe_nghiep' => 'Học sinh phổ thông',
            'doi_thu' => 4,
            'loai_quan_he' => 'Chính',
            'trang_thai' => 'Còn sống',
            'ghi_chu' => 'Con út chị Hương anh Huy.',
            'avatar' => 'https://images.unsplash.com/photo-1517841905240-472988babdf9?auto=format&fit=crop&q=80&w=200'
        ]);

        // Con của Bách & Diệp (Mới thêm)
        $beHaiConBach = ThanhVien::create([
            'chi_nhanh_id' => $cnId,
            'ho_ten' => 'Vũ Hoàng Hải',
            'ten_goi' => 'Bé Hoàng Hải',
            'gioi_tinh' => 'Nam',
            'ngay_sinh' => '2013-10-18',
            'cha_id' => $anhBach->id,
            'me_id' => $chiDiep->id,
            'noi_sinh' => 'Hà Nội',
            'nghe_nghiep' => 'Học sinh tiểu học',
            'doi_thu' => 4,
            'loai_quan_he' => 'Chính',
            'trang_thai' => 'Còn sống',
            'ghi_chu' => 'Con cả anh Bách chị Diệp.',
            'avatar' => 'https://images.unsplash.com/photo-1503919545889-aef636e10ad4?auto=format&fit=crop&q=80&w=200'
        ]);

        $beAnhConBach = ThanhVien::create([
            'chi_nhanh_id' => $cnId,
            'ho_ten' => 'Vũ Ngọc Diệp Anh',
            'ten_goi' => 'Bé Diệp Anh',
            'gioi_tinh' => 'Nữ',
            'ngay_sinh' => '2017-04-12',
            'cha_id' => $anhBach->id,
            'me_id' => $chiDiep->id,
            'noi_sinh' => 'Hà Nội',
            'nghe_nghiep' => 'Học sinh tiểu học',
            'doi_thu' => 4,
            'loai_quan_he' => 'Chính',
            'trang_thai' => 'Còn sống',
            'ghi_chu' => 'Con út anh Bách chị Diệp.',
            'avatar' => 'https://images.unsplash.com/photo-1519085360753-af0119f7cbe7?auto=format&fit=crop&q=80&w=200'
        ]);

        // Con của Sơn & Oanh (Mới thêm)
        $beVietConSon = ThanhVien::create([
            'chi_nhanh_id' => $cnId,
            'ho_ten' => 'Nguyễn Đức Việt',
            'ten_goi' => 'Bé Việt',
            'gioi_tinh' => 'Nam',
            'ngay_sinh' => '2010-02-14',
            'cha_id' => $anhSon->id,
            'me_id' => $chiOanh->id,
            'noi_sinh' => 'Hà Nội',
            'nghe_nghiep' => 'Học sinh trung học',
            'doi_thu' => 4,
            'loai_quan_he' => 'Chính',
            'trang_thai' => 'Còn sống',
            'ghi_chu' => 'Con cả anh Sơn chị Oanh.',
            'avatar' => 'https://images.unsplash.com/photo-1503919545889-aef636e10ad4?auto=format&fit=crop&q=80&w=200'
        ]);

        $beVyConSon = ThanhVien::create([
            'chi_nhanh_id' => $cnId,
            'ho_ten' => 'Nguyễn Thị Phương Vy',
            'ten_goi' => 'Bé Phương Vy',
            'gioi_tinh' => 'Nữ',
            'ngay_sinh' => '2014-06-18',
            'cha_id' => $anhSon->id,
            'me_id' => $chiOanh->id,
            'noi_sinh' => 'Hà Nội',
            'nghe_nghiep' => 'Học sinh tiểu học',
            'doi_thu' => 4,
            'loai_quan_he' => 'Chính',
            'trang_thai' => 'Còn sống',
            'ghi_chu' => 'Con út anh Sơn chị Oanh.',
            'avatar' => 'https://images.unsplash.com/photo-1517841905240-472988babdf9?auto=format&fit=crop&q=80&w=200'
        ]);

        // Con của Mai Anh & Long (Mới thêm)
        $beNamConLong = ThanhVien::create([
            'chi_nhanh_id' => $cnId,
            'ho_ten' => 'Đỗ Hoàng Nam',
            'ten_goi' => 'Bé Hoàng Nam',
            'gioi_tinh' => 'Nam',
            'ngay_sinh' => '2012-04-12',
            'cha_id' => $anhLongRe->id,
            'me_id' => $chiMaiAnhDoi3->id,
            'noi_sinh' => 'Hà Nội',
            'nghe_nghiep' => 'Học sinh trung học',
            'doi_thu' => 4,
            'loai_quan_he' => 'Chính',
            'trang_thai' => 'Còn sống',
            'ghi_chu' => 'Con cả chị Mai Anh anh Long.',
            'avatar' => 'https://images.unsplash.com/photo-1503919545889-aef636e10ad4?auto=format&fit=crop&q=80&w=200'
        ]);

        $beTrangConLong = ThanhVien::create([
            'chi_nhanh_id' => $cnId,
            'ho_ten' => 'Đỗ Thu Trang',
            'ten_goi' => 'Bé Thu Trang',
            'gioi_tinh' => 'Nữ',
            'ngay_sinh' => '2016-08-20',
            'cha_id' => $anhLongRe->id,
            'me_id' => $chiMaiAnhDoi3->id,
            'noi_sinh' => 'Hà Nội',
            'nghe_nghiep' => 'Học sinh tiểu học',
            'doi_thu' => 4,
            'loai_quan_he' => 'Chính',
            'trang_thai' => 'Còn sống',
            'ghi_chu' => 'Con út chị Mai Anh anh Long.',
            'avatar' => 'https://images.unsplash.com/photo-1517841905240-472988babdf9?auto=format&fit=crop&q=80&w=200'
        ]);

        // Con của Hùng (con Khang) & Vân (Mới thêm)
        $bePhongConHungKhang = ThanhVien::create([
            'chi_nhanh_id' => $cnId,
            'ho_ten' => 'Nguyễn Đức Phong',
            'ten_goi' => 'Bé Phong (Hùng)',
            'gioi_tinh' => 'Nam',
            'ngay_sinh' => '2017-09-15',
            'cha_id' => $anhHungConKhang->id,
            'me_id' => $chiVan->id,
            'noi_sinh' => 'Hà Nội',
            'nghe_nghiep' => 'Học sinh tiểu học',
            'doi_thu' => 4,
            'loai_quan_he' => 'Chính',
            'trang_thai' => 'Còn sống',
            'ghi_chu' => 'Con cả anh Hùng (con Khang) chị Vân.',
            'avatar' => 'https://images.unsplash.com/photo-1503919545889-aef636e10ad4?auto=format&fit=crop&q=80&w=200'
        ]);

        $beChiConHungKhang = ThanhVien::create([
            'chi_nhanh_id' => $cnId,
            'ho_ten' => 'Nguyễn Thị Linh Chi',
            'ten_goi' => 'Bé Linh Chi',
            'gioi_tinh' => 'Nữ',
            'ngay_sinh' => '2020-05-18',
            'cha_id' => $anhHungConKhang->id,
            'me_id' => $chiVan->id,
            'noi_sinh' => 'Hà Nội',
            'nghe_nghiep' => 'Học sinh mầm non',
            'doi_thu' => 4,
            'loai_quan_he' => 'Chính',
            'trang_thai' => 'Còn sống',
            'ghi_chu' => 'Con út anh Hùng (con Khang) chị Vân.',
            'avatar' => 'https://images.unsplash.com/photo-1519085360753-af0119f7cbe7?auto=format&fit=crop&q=80&w=200'
        ]);


        // --- CÁC BẠN ĐỜI MỚI THÊM CHO ĐỜI 4 ---

        // Vợ của Phạm Minh Đức (Mới thêm)
        $chiQuynhChiDuc = ThanhVien::create([
            'chi_nhanh_id' => $cnId,
            'ho_ten' => 'Đặng Quỳnh Chi',
            'ten_goi' => 'Quỳnh Chi (Đức)',
            'gioi_tinh' => 'Nữ',
            'ngay_sinh' => '2000-11-20',
            'noi_sinh' => 'Hải Dương',
            'nghe_nghiep' => 'Thiết kế đồ họa',
            'doi_thu' => 4,
            'loai_quan_he' => 'Vợ/Chồng',
            'spouse_of_id' => $anhDuc->id,
            'trang_thai' => 'Còn sống',
            'ghi_chu' => 'Vợ anh Phạm Minh Đức.',
            'avatar' => 'https://images.unsplash.com/photo-1544005313-94ddf0286df2?auto=format&fit=crop&q=80&w=200'
        ]);

        VoChong::create([
            'chong_id' => $anhDuc->id,
            'vo_id' => $chiQuynhChiDuc->id,
            'ngay_cuoi' => '2022-09-12',
            'trang_thai' => 'Đang sống'
        ]);

        // Chồng của Phạm Thị Hồng Nhung (Mới thêm)
        $anhDuyAnhRe = ThanhVien::create([
            'chi_nhanh_id' => $cnId,
            'ho_ten' => 'Nguyễn Duy Anh',
            'ten_goi' => 'Duy Anh',
            'gioi_tinh' => 'Nam',
            'ngay_sinh' => '1998-08-15',
            'noi_sinh' => 'Thái Nguyên',
            'nghe_nghiep' => 'Lập trình viên',
            'doi_thu' => 4,
            'loai_quan_he' => 'Vợ/Chồng',
            'spouse_of_id' => $chiNhung->id,
            'trang_thai' => 'Còn sống',
            'ghi_chu' => 'Chồng chị Phạm Thị Hồng Nhung.',
            'avatar' => 'https://images.unsplash.com/photo-1492562080023-ab3db95bfbce?auto=format&fit=crop&q=80&w=200'
        ]);

        VoChong::create([
            'chong_id' => $anhDuyAnhRe->id,
            'vo_id' => $chiNhung->id,
            'ngay_cuoi' => '2023-11-25',
            'trang_thai' => 'Đang sống'
        ]);

        // Vợ của Đỗ Minh Trí (Mới thêm)
        $chiDuongTri = ThanhVien::create([
            'chi_nhanh_id' => $cnId,
            'ho_ten' => 'Nguyễn Thùy Dương',
            'ten_goi' => 'Thùy Dương',
            'gioi_tinh' => 'Nữ',
            'ngay_sinh' => '1998-05-14',
            'noi_sinh' => 'Hà Nội',
            'nghe_nghiep' => 'Chuyên viên nhân sự',
            'doi_thu' => 4,
            'loai_quan_he' => 'Vợ/Chồng',
            'spouse_of_id' => $anhTri->id,
            'trang_thai' => 'Còn sống',
            'ghi_chu' => 'Vợ anh Đỗ Minh Trí.',
            'avatar' => 'https://images.unsplash.com/photo-1544005313-94ddf0286df2?auto=format&fit=crop&q=80&w=200'
        ]);

        VoChong::create([
            'chong_id' => $anhTri->id,
            'vo_id' => $chiDuongTri->id,
            'ngay_cuoi' => '2021-10-18',
            'trang_thai' => 'Đang sống'
        ]);

        // Chồng của Đỗ Thu Thủy (Mới thêm)
        $anhVietRe = ThanhVien::create([
            'chi_nhanh_id' => $cnId,
            'ho_ten' => 'Phạm Hoàng Việt',
            'ten_goi' => 'Hoàng Việt',
            'gioi_tinh' => 'Nam',
            'ngay_sinh' => '1996-03-20',
            'noi_sinh' => 'Quảng Ninh',
            'nghe_nghiep' => 'Kiến trúc sư',
            'doi_thu' => 4,
            'loai_quan_he' => 'Vợ/Chồng',
            'spouse_of_id' => $chiThuy->id,
            'trang_thai' => 'Còn sống',
            'ghi_chu' => 'Chồng chị Đỗ Thu Thủy.',
            'avatar' => 'https://images.unsplash.com/photo-1492562080023-ab3db95bfbce?auto=format&fit=crop&q=80&w=200'
        ]);

        VoChong::create([
            'chong_id' => $anhVietRe->id,
            'vo_id' => $chiThuy->id,
            'ngay_cuoi' => '2022-05-20',
            'trang_thai' => 'Đang sống'
        ]);

        // Vợ của Lê Tuấn Tú (Mới thêm)
        $chiYenTu = ThanhVien::create([
            'chi_nhanh_id' => $cnId,
            'ho_ten' => 'Vũ Hoàng Yến',
            'ten_goi' => 'Hoàng Yến',
            'gioi_tinh' => 'Nữ',
            'ngay_sinh' => '2003-02-14',
            'noi_sinh' => 'Hà Nội',
            'nghe_nghiep' => 'Dược sĩ',
            'doi_thu' => 4,
            'loai_quan_he' => 'Vợ/Chồng',
            'spouse_of_id' => $beTuanTu->id,
            'trang_thai' => 'Còn sống',
            'ghi_chu' => 'Vợ anh Lê Tuấn Tú.',
            'avatar' => 'https://images.unsplash.com/photo-1544005313-94ddf0286df2?auto=format&fit=crop&q=80&w=200'
        ]);

        VoChong::create([
            'chong_id' => $beTuanTu->id,
            'vo_id' => $chiYenTu->id,
            'ngay_cuoi' => '2024-06-18',
            'trang_thai' => 'Đang sống'
        ]);

        // Chồng của Lê Thị Thu Thảo (Mới thêm)
        $anhToanRe = ThanhVien::create([
            'chi_nhanh_id' => $cnId,
            'ho_ten' => 'Trần Quốc Toàn',
            'ten_goi' => 'Quốc Toàn',
            'gioi_tinh' => 'Nam',
            'ngay_sinh' => '2000-09-22',
            'noi_sinh' => 'Nam Định',
            'nghe_nghiep' => 'Nhân viên kinh doanh',
            'doi_thu' => 4,
            'loai_quan_he' => 'Vợ/Chồng',
            'spouse_of_id' => $beThuThao->id,
            'trang_thai' => 'Còn sống',
            'ghi_chu' => 'Chồng chị Lê Thị Thu Thảo.',
            'avatar' => 'https://images.unsplash.com/photo-1492562080023-ab3db95bfbce?auto=format&fit=crop&q=80&w=200'
        ]);

        VoChong::create([
            'chong_id' => $anhToanRe->id,
            'vo_id' => $beThuThao->id,
            'ngay_cuoi' => '2025-10-10',
            'trang_thai' => 'Đang sống'
        ]);

        // Chồng của Trần Thu Hà (Mới thêm)
        $anhTienRe = ThanhVien::create([
            'chi_nhanh_id' => $cnId,
            'ho_ten' => 'Hoàng Minh Tiến',
            'ten_goi' => 'Minh Tiến',
            'gioi_tinh' => 'Nam',
            'ngay_sinh' => '2001-08-15',
            'noi_sinh' => 'Thái Bình',
            'nghe_nghiep' => 'Kỹ sư cầu đường',
            'doi_thu' => 4,
            'loai_quan_he' => 'Vợ/Chồng',
            'spouse_of_id' => $chiHaConThuy->id,
            'trang_thai' => 'Còn sống',
            'ghi_chu' => 'Chồng chị Trần Thu Hà.',
            'avatar' => 'https://images.unsplash.com/photo-1492562080023-ab3db95bfbce?auto=format&fit=crop&q=80&w=200'
        ]);

        VoChong::create([
            'chong_id' => $anhTienRe->id,
            'vo_id' => $chiHaConThuy->id,
            'ngay_cuoi' => '2024-05-18',
            'trang_thai' => 'Đang sống'
        ]);

        // Chồng của Nguyễn Thị Quỳnh Chi (Mới thêm)
        $anhHoangRe = ThanhVien::create([
            'chi_nhanh_id' => $cnId,
            'ho_ten' => 'Trần Huy Hoàng',
            'ten_goi' => 'Huy Hoàng',
            'gioi_tinh' => 'Nam',
            'ngay_sinh' => '2000-11-20',
            'noi_sinh' => 'Hà Nội',
            'nghe_nghiep' => 'Chuyên viên ngân hàng',
            'doi_thu' => 4,
            'loai_quan_he' => 'Vợ/Chồng',
            'spouse_of_id' => $chiQuynhChi->id,
            'trang_thai' => 'Còn sống',
            'ghi_chu' => 'Chồng chị Nguyễn Thị Quỳnh Chi.',
            'avatar' => 'https://images.unsplash.com/photo-1492562080023-ab3db95bfbce?auto=format&fit=crop&q=80&w=200'
        ]);

        VoChong::create([
            'chong_id' => $anhHoangRe->id,
            'vo_id' => $chiQuynhChi->id,
            'ngay_cuoi' => '2024-09-09',
            'trang_thai' => 'Đang sống'
        ]);

        // ------------------ ĐỜI 5 ------------------
        // Con của Đức Anh & Hoàng Thu Trang (Vợ 1)
        // 1. Nguyễn Đức Khải
        $beKhai = ThanhVien::create([
            'chi_nhanh_id' => $cnId,
            'ho_ten' => 'Nguyễn Đức Khải',
            'ten_goi' => 'Bé Khải',
            'gioi_tinh' => 'Nam',
            'ngay_sinh' => '2018-10-12',
            'cha_id' => $anhDucAnh->id,
            'me_id' => $chiTrang->id,
            'noi_sinh' => 'Hà Nội',
            'nghe_nghiep' => 'Học sinh tiểu học',
            'doi_thu' => 5,
            'loai_quan_he' => 'Chính',
            'trang_thai' => 'Còn sống',
            'ghi_chu' => 'Thông minh, ham học hỏi, yêu thích vẽ tranh và lập trình lắp ráp robot Lego.',
            'avatar' => 'https://images.unsplash.com/photo-1503919545889-aef636e10ad4?auto=format&fit=crop&q=80&w=200'
        ]);

        // 2. Nguyễn Tuệ Lâm
        $beTueLam = ThanhVien::create([
            'chi_nhanh_id' => $cnId,
            'ho_ten' => 'Nguyễn Tuệ Lâm',
            'ten_goi' => 'Bé Tuệ Lâm',
            'gioi_tinh' => 'Nữ',
            'ngay_sinh' => '2021-03-25',
            'cha_id' => $anhDucAnh->id,
            'me_id' => $chiTrang->id,
            'noi_sinh' => 'Hà Nội',
            'nghe_nghiep' => 'Học sinh mầm non',
            'doi_thu' => 5,
            'loai_quan_he' => 'Chính',
            'trang_thai' => 'Còn sống',
            'ghi_chu' => 'Cô công chúa nhỏ lém lỉnh, thích ca hát và kể chuyện cổ tích.',
            'avatar' => 'https://images.unsplash.com/photo-1519085360753-af0119f7cbe7?auto=format&fit=crop&q=80&w=200'
        ]);

        // Con của Đức Anh & Trần Thị Tuyết Nhi (Vợ 2)
        // 3. Nguyễn Đức Gia Huy
        $beGiaHuy = ThanhVien::create([
            'chi_nhanh_id' => $cnId,
            'ho_ten' => 'Nguyễn Đức Gia Huy',
            'ten_goi' => 'Bé Gia Huy',
            'gioi_tinh' => 'Nam',
            'ngay_sinh' => '2025-01-15',
            'cha_id' => $anhDucAnh->id,
            'me_id' => $chiTuyenNhi->id,
            'noi_sinh' => 'Hà Nội',
            'nghe_nghiep' => 'Trẻ sơ sinh',
            'doi_thu' => 5,
            'loai_quan_he' => 'Chính',
            'trang_thai' => 'Còn sống',
            'ghi_chu' => 'Bé trai nhỏ tuổi nhất, con của anh Đức Anh với chị Tuyết Nhi.',
            'avatar' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?auto=format&fit=crop&q=80&w=200'
        ]);

        // Con của Minh Vy & Hoàng Long
        // 4. Trần Minh Khang
        $beMinhKhang = ThanhVien::create([
            'chi_nhanh_id' => $cnId,
            'ho_ten' => 'Trần Minh Khang',
            'ten_goi' => 'Bé Minh Khang',
            'gioi_tinh' => 'Nam',
            'ngay_sinh' => '2022-09-30',
            'cha_id' => $anhLong->id,
            'me_id' => $chiVy->id,
            'noi_sinh' => 'Hà Nội',
            'nghe_nghiep' => 'Trẻ em',
            'doi_thu' => 5,
            'loai_quan_he' => 'Chính',
            'trang_thai' => 'Còn sống',
            'ghi_chu' => 'Con trai chị Minh Vy và anh Hoàng Long.',
            'avatar' => 'https://images.unsplash.com/photo-1503919545889-aef636e10ad4?auto=format&fit=crop&q=80&w=200'
        ]);

        // Con của Đức Duy & Mỹ Linh
        // 5. Nguyễn Đức Gia Bảo
        $beGiaBao = ThanhVien::create([
            'chi_nhanh_id' => $cnId,
            'ho_ten' => 'Nguyễn Đức Gia Bảo',
            'ten_goi' => 'Bé Gia Bảo',
            'gioi_tinh' => 'Nam',
            'ngay_sinh' => '2024-05-10',
            'cha_id' => $anhDuy->id,
            'me_id' => $chiMyLinh->id,
            'noi_sinh' => 'Hà Nội',
            'nghe_nghiep' => 'Trẻ em',
            'doi_thu' => 5,
            'loai_quan_he' => 'Chính',
            'trang_thai' => 'Còn sống',
            'ghi_chu' => 'Con trai anh Đức Duy và chị Mỹ Linh.',
            'avatar' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?auto=format&fit=crop&q=80&w=200'
        ]);

        // 6. Trần Nguyễn Khánh An (con anh Hoàng & chị Trang)
        $beKhanhAn = ThanhVien::create([
            'chi_nhanh_id' => $cnId,
            'ho_ten' => 'Trần Nguyễn Khánh An',
            'ten_goi' => 'Bé Khánh An',
            'gioi_tinh' => 'Nữ',
            'ngay_sinh' => '2025-07-20',
            'cha_id' => $anhHoang->id,
            'me_id' => $chiTrangConDau->id,
            'noi_sinh' => 'Hà Nội',
            'nghe_nghiep' => 'Trẻ sơ sinh',
            'doi_thu' => 5,
            'loai_quan_he' => 'Chính',
            'trang_thai' => 'Còn sống',
            'ghi_chu' => 'Con đầu lòng anh Hoàng chị Trang.',
            'avatar' => 'https://images.unsplash.com/photo-1519085360753-af0119f7cbe7?auto=format&fit=crop&q=80&w=200'
        ]);

        // 7. Nguyễn Thị Ngọc Vy (con thứ 2 của anh Đức Anh & chị Tuyết Nhi - Vợ 2)
        $beNgocVy = ThanhVien::create([
            'chi_nhanh_id' => $cnId,
            'ho_ten' => 'Nguyễn Thị Ngọc Vy',
            'ten_goi' => 'Bé Ngọc Vy',
            'gioi_tinh' => 'Nữ',
            'ngay_sinh' => '2026-05-01',
            'cha_id' => $anhDucAnh->id,
            'me_id' => $chiTuyenNhi->id,
            'noi_sinh' => 'Hà Nội',
            'nghe_nghiep' => 'Trẻ sơ sinh',
            'doi_thu' => 5,
            'loai_quan_he' => 'Chính',
            'trang_thai' => 'Còn sống',
            'ghi_chu' => 'Con thứ hai của anh Đức Anh và chị Tuyết Nhi.',
            'avatar' => 'https://images.unsplash.com/photo-1519085360753-af0119f7cbe7?auto=format&fit=crop&q=80&w=200'
        ]);

        // 8. Nguyễn Đức Thiện cưới vợ & sinh con
        // Hoàng Minh Anh (Vợ Thiện)
        $chiMinhAnh = ThanhVien::create([
            'chi_nhanh_id' => $cnId,
            'ho_ten' => 'Hoàng Minh Anh',
            'ten_goi' => 'Minh Anh',
            'gioi_tinh' => 'Nữ',
            'ngay_sinh' => '2007-08-12',
            'noi_sinh' => 'Hà Nội',
            'nghe_nghiep' => 'Sinh viên',
            'doi_thu' => 4,
            'loai_quan_he' => 'Vợ/Chồng',
            'spouse_of_id' => $beThien->id,
            'trang_thai' => 'Còn sống',
            'ghi_chu' => 'Vợ anh Nguyễn Đức Thiện.',
            'avatar' => 'https://images.unsplash.com/photo-1544005313-94ddf0286df2?auto=format&fit=crop&q=80&w=200'
        ]);

        VoChong::create([
            'chong_id' => $beThien->id,
            'vo_id' => $chiMinhAnh->id,
            'ngay_cuoi' => '2025-05-20',
            'trang_thai' => 'Đang sống'
        ]);

        // Nguyễn Đức Bảo Khang (Con Thiện & Minh Anh)
        $beBaoKhang = ThanhVien::create([
            'chi_nhanh_id' => $cnId,
            'ho_ten' => 'Nguyễn Đức Bảo Khang',
            'ten_goi' => 'Bé Bảo Khang',
            'gioi_tinh' => 'Nam',
            'ngay_sinh' => '2026-01-10',
            'cha_id' => $beThien->id,
            'me_id' => $chiMinhAnh->id,
            'noi_sinh' => 'Hà Nội',
            'nghe_nghiep' => 'Trẻ sơ sinh',
            'doi_thu' => 5,
            'loai_quan_he' => 'Chính',
            'trang_thai' => 'Còn sống',
            'ghi_chu' => 'Con trai anh Nguyễn Đức Thiện và chị Hoàng Minh Anh.',
            'avatar' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?auto=format&fit=crop&q=80&w=200'
        ]);

        // --- CÁC THÀNH VIÊN ĐỜI 5 THÊM MỚI ---

        // Con của Minh Đức & Quỳnh Chi (Mới thêm)
        $beKhangConDuc = ThanhVien::create([
            'chi_nhanh_id' => $cnId,
            'ho_ten' => 'Phạm Minh Khang',
            'ten_goi' => 'Bé Minh Khang',
            'gioi_tinh' => 'Nam',
            'ngay_sinh' => '2023-05-18',
            'cha_id' => $anhDuc->id,
            'me_id' => $chiQuynhChiDuc->id,
            'noi_sinh' => 'Hà Nội',
            'nghe_nghiep' => 'Trẻ em',
            'doi_thu' => 5,
            'loai_quan_he' => 'Chính',
            'trang_thai' => 'Còn sống',
            'ghi_chu' => 'Con cả anh Minh Đức chị Quỳnh Chi.',
            'avatar' => 'https://images.unsplash.com/photo-1503919545889-aef636e10ad4?auto=format&fit=crop&q=80&w=200'
        ]);

        $beAnConDuc = ThanhVien::create([
            'chi_nhanh_id' => $cnId,
            'ho_ten' => 'Phạm Khánh An',
            'ten_goi' => 'Bé Khánh An (Đức)',
            'gioi_tinh' => 'Nữ',
            'ngay_sinh' => '2025-02-14',
            'cha_id' => $anhDuc->id,
            'me_id' => $chiQuynhChiDuc->id,
            'noi_sinh' => 'Hà Nội',
            'nghe_nghiep' => 'Trẻ sơ sinh',
            'doi_thu' => 5,
            'loai_quan_he' => 'Chính',
            'trang_thai' => 'Còn sống',
            'ghi_chu' => 'Con thứ hai anh Minh Đức chị Quỳnh Chi.',
            'avatar' => 'https://images.unsplash.com/photo-1519085360753-af0119f7cbe7?auto=format&fit=crop&q=80&w=200'
        ]);

        $beTrietConDuc = ThanhVien::create([
            'chi_nhanh_id' => $cnId,
            'ho_ten' => 'Phạm Minh Triết',
            'ten_goi' => 'Bé Minh Triết',
            'gioi_tinh' => 'Nam',
            'ngay_sinh' => '2026-05-01',
            'cha_id' => $anhDuc->id,
            'me_id' => $chiQuynhChiDuc->id,
            'noi_sinh' => 'Hà Nội',
            'nghe_nghiep' => 'Trẻ sơ sinh',
            'doi_thu' => 5,
            'loai_quan_he' => 'Chính',
            'trang_thai' => 'Còn sống',
            'ghi_chu' => 'Con út anh Minh Đức chị Quỳnh Chi.',
            'avatar' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?auto=format&fit=crop&q=80&w=200'
        ]);

        // Con của Hồng Nhung & Duy Anh (Mới thêm)
        $beBachConNhung = ThanhVien::create([
            'chi_nhanh_id' => $cnId,
            'ho_ten' => 'Nguyễn Duy Bách',
            'ten_goi' => 'Bé Duy Bách',
            'gioi_tinh' => 'Nam',
            'ngay_sinh' => '2024-10-12',
            'cha_id' => $anhDuyAnhRe->id,
            'me_id' => $chiNhung->id,
            'noi_sinh' => 'Hà Nội',
            'nghe_nghiep' => 'Trẻ em',
            'doi_thu' => 5,
            'loai_quan_he' => 'Chính',
            'trang_thai' => 'Còn sống',
            'ghi_chu' => 'Con chị Hồng Nhung anh Duy Anh.',
            'avatar' => 'https://images.unsplash.com/photo-1503919545889-aef636e10ad4?auto=format&fit=crop&q=80&w=200'
        ]);

        // Con của Minh Trí & Thùy Dương (Mới thêm)
        $beQuanConTri = ThanhVien::create([
            'chi_nhanh_id' => $cnId,
            'ho_ten' => 'Đỗ Minh Quân',
            'ten_goi' => 'Bé Minh Quân (Trí)',
            'gioi_tinh' => 'Nam',
            'ngay_sinh' => '2022-03-24',
            'cha_id' => $anhTri->id,
            'me_id' => $chiDuongTri->id,
            'noi_sinh' => 'Hà Nội',
            'nghe_nghiep' => 'Trẻ em',
            'doi_thu' => 5,
            'loai_quan_he' => 'Chính',
            'trang_thai' => 'Còn sống',
            'ghi_chu' => 'Con cả anh Minh Trí chị Thùy Dương.',
            'avatar' => 'https://images.unsplash.com/photo-1503919545889-aef636e10ad4?auto=format&fit=crop&q=80&w=200'
        ]);

        $beAnConTri = ThanhVien::create([
            'chi_nhanh_id' => $cnId,
            'ho_ten' => 'Đỗ Thu An',
            'ten_goi' => 'Bé Thu An',
            'gioi_tinh' => 'Nữ',
            'ngay_sinh' => '2024-07-15',
            'cha_id' => $anhTri->id,
            'me_id' => $chiDuongTri->id,
            'noi_sinh' => 'Hà Nội',
            'nghe_nghiep' => 'Trẻ em',
            'doi_thu' => 5,
            'loai_quan_he' => 'Chính',
            'trang_thai' => 'Còn sống',
            'ghi_chu' => 'Con út anh Minh Trí chị Thùy Dương.',
            'avatar' => 'https://images.unsplash.com/photo-1519085360753-af0119f7cbe7?auto=format&fit=crop&q=80&w=200'
        ]);

        // Con của Thu Thủy & Hoàng Việt (Mới thêm)
        $beMinhConThuy = ThanhVien::create([
            'chi_nhanh_id' => $cnId,
            'ho_ten' => 'Phạm Hoàng Minh',
            'ten_goi' => 'Bé Hoàng Minh',
            'gioi_tinh' => 'Nam',
            'ngay_sinh' => '2023-11-20',
            'cha_id' => $anhVietRe->id,
            'me_id' => $chiThuy->id,
            'noi_sinh' => 'Hà Nội',
            'nghe_nghiep' => 'Trẻ em',
            'doi_thu' => 5,
            'loai_quan_he' => 'Chính',
            'trang_thai' => 'Còn sống',
            'ghi_chu' => 'Con chị Thu Thủy anh Hoàng Việt.',
            'avatar' => 'https://images.unsplash.com/photo-1503919545889-aef636e10ad4?auto=format&fit=crop&q=80&w=200'
        ]);

        // Con của Tuấn Tú & Hoàng Yến (Mới thêm)
        $beBachConTu = ThanhVien::create([
            'chi_nhanh_id' => $cnId,
            'ho_ten' => 'Lê Hoàng Bách',
            'ten_goi' => 'Bé Hoàng Bách (Tú)',
            'gioi_tinh' => 'Nam',
            'ngay_sinh' => '2025-01-10',
            'cha_id' => $beTuanTu->id,
            'me_id' => $chiYenTu->id,
            'noi_sinh' => 'Hà Nội',
            'nghe_nghiep' => 'Trẻ sơ sinh',
            'doi_thu' => 5,
            'loai_quan_he' => 'Chính',
            'trang_thai' => 'Còn sống',
            'ghi_chu' => 'Con anh Lê Tuấn Tú chị Vũ Hoàng Yến.',
            'avatar' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?auto=format&fit=crop&q=80&w=200'
        ]);

        // Con của Thu Hà & Minh Tiến (Mới thêm)
        $beAnhConHa = ThanhVien::create([
            'chi_nhanh_id' => $cnId,
            'ho_ten' => 'Hoàng Thu Anh',
            'ten_goi' => 'Bé Thu Anh',
            'gioi_tinh' => 'Nữ',
            'ngay_sinh' => '2025-05-12',
            'cha_id' => $anhTienRe->id,
            'me_id' => $chiHaConThuy->id,
            'noi_sinh' => 'Hà Nội',
            'nghe_nghiep' => 'Trẻ sơ sinh',
            'doi_thu' => 5,
            'loai_quan_he' => 'Chính',
            'trang_thai' => 'Còn sống',
            'ghi_chu' => 'Con chị Trần Thu Hà anh Hoàng Minh Tiến.',
            'avatar' => 'https://images.unsplash.com/photo-1519085360753-af0119f7cbe7?auto=format&fit=crop&q=80&w=200'
        ]);

        // Con của Quỳnh Chi & Huy Hoàng (Mới thêm)
        $beDiepConChi = ThanhVien::create([
            'chi_nhanh_id' => $cnId,
            'ho_ten' => 'Trần Ngọc Diệp',
            'ten_goi' => 'Bé Ngọc Diệp (Chi)',
            'gioi_tinh' => 'Nữ',
            'ngay_sinh' => '2025-08-15',
            'cha_id' => $anhHoangRe->id,
            'me_id' => $chiQuynhChi->id,
            'noi_sinh' => 'Hà Nội',
            'nghe_nghiep' => 'Trẻ sơ sinh',
            'doi_thu' => 5,
            'loai_quan_he' => 'Chính',
            'trang_thai' => 'Còn sống',
            'ghi_chu' => 'Con chị Nguyễn Thị Quỳnh Chi anh Trần Huy Hoàng.',
            'avatar' => 'https://images.unsplash.com/photo-1519085360753-af0119f7cbe7?auto=format&fit=crop&q=80&w=200'
        ]);

        // Con của Đức Anh & Tuyết Nhi (Con thứ 3 - Vợ 2 - Mới thêm)
        $beKhoiConAnh = ThanhVien::create([
            'chi_nhanh_id' => $cnId,
            'ho_ten' => 'Nguyễn Đức Minh Khôi',
            'ten_goi' => 'Bé Minh Khôi',
            'gioi_tinh' => 'Nam',
            'ngay_sinh' => '2026-05-15',
            'cha_id' => $anhDucAnh->id,
            'me_id' => $chiTuyenNhi->id,
            'noi_sinh' => 'Hà Nội',
            'nghe_nghiep' => 'Trẻ sơ sinh',
            'doi_thu' => 5,
            'loai_quan_he' => 'Chính',
            'trang_thai' => 'Còn sống',
            'ghi_chu' => 'Con thứ ba của anh Đức Anh và chị Tuyết Nhi.',
            'avatar' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?auto=format&fit=crop&q=80&w=200'
        ]);

        // Con của Đức Duy & Mỹ Linh (Con thứ 2 - Mới thêm)
        $beHungConDuy = ThanhVien::create([
            'chi_nhanh_id' => $cnId,
            'ho_ten' => 'Nguyễn Đức Gia Hưng',
            'ten_goi' => 'Bé Gia Hưng',
            'gioi_tinh' => 'Nam',
            'ngay_sinh' => '2026-04-10',
            'cha_id' => $anhDuy->id,
            'me_id' => $chiMyLinh->id,
            'noi_sinh' => 'Hà Nội',
            'nghe_nghiep' => 'Trẻ sơ sinh',
            'doi_thu' => 5,
            'loai_quan_he' => 'Chính',
            'trang_thai' => 'Còn sống',
            'ghi_chu' => 'Con thứ hai anh Đức Duy và chị Mỹ Linh.',
            'avatar' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?auto=format&fit=crop&q=80&w=200'
        ]);

        // Con của Minh Hoàng & Thu Trang (Con thứ 2 - Mới thêm)
        $beNgocConHoang = ThanhVien::create([
            'chi_nhanh_id' => $cnId,
            'ho_ten' => 'Trần Nguyễn Khánh Ngọc',
            'ten_goi' => 'Bé Khánh Ngọc',
            'gioi_tinh' => 'Nữ',
            'ngay_sinh' => '2027-02-14',
            'cha_id' => $anhHoang->id,
            'me_id' => $chiTrangConDau->id,
            'noi_sinh' => 'Hà Nội',
            'nghe_nghiep' => 'Trẻ sơ sinh',
            'doi_thu' => 5,
            'loai_quan_he' => 'Chính',
            'trang_thai' => 'Còn sống',
            'ghi_chu' => 'Con thứ hai anh Hoàng chị Trang.',
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
