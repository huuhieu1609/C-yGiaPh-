<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\ChiNhanh;

class SuKienSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Xóa sạch sự kiện cũ để tránh trùng lặp
        DB::table('su_kiens')->delete();

        // 2. Tìm ID chi nhánh của 3 đối tác đăng ký
        $cnId1 = DB::table('chi_nhanhs')->where('ten_chi', 'like', '%Nguyễn Đức%')->value('id') 
            ?? DB::table('chi_nhanhs')->value('id');

        $cnId2 = DB::table('chi_nhanhs')->where('ten_chi', 'like', '%Trần Khắc%')->value('id');
        $cnId3 = DB::table('chi_nhanhs')->where('ten_chi', 'like', '%Lê Hữu%')->value('id');

        $events = [];

        // Đối tác 1: Dòng Họ Nguyễn Đức - Hà Nội
        if ($cnId1) {
            $events[] = [
                'tieu_de'      => 'Đại Lễ Giỗ Tổ Dòng Họ Nguyễn Đức',
                'noi_dung'     => 'Ban trị sự tổ chức Đại lễ dâng hương tạ ơn Thủy Tổ. Chương trình chi tiết bao gồm: 8:00 Dâng hương, 9:30 Báo cáo hoạt động và tài chính dòng họ, 10:30 Trao thưởng khuyến học mùa hè, 11:30 Thụ lộc liên hoan đoàn viên.',
                'ngay_to_chuc' => now()->addDays(15)->toDateString(),
                'dia_diem'     => 'Nhà Thờ Tổ Dòng Họ Nguyễn Đức, Thạch Thất, Hà Nội',
                'chi_nhanh_id' => $cnId1,
                'loai'         => 'Giỗ tổ',
            ];
            $events[] = [
                'tieu_de'      => 'Họp Mặt Khuyến Học Hè 2026',
                'noi_dung'     => 'Trao học bổng khuyến học cho con em có thành tích học tập xuất sắc năm học 2025-2026. Vinh danh các cháu đỗ đại học thủ khoa, đạt giải quốc gia, quốc tế.',
                'ngay_to_chuc' => now()->addDays(5)->toDateString(),
                'dia_diem'     => 'Nhà Thờ Tổ Dòng Họ Nguyễn Đức, Thạch Thất, Hà Nội',
                'chi_nhanh_id' => $cnId1,
                'loai'         => 'Họp họ',
            ];
            $events[] = [
                'tieu_de'      => 'Họp Họ Tổng Kết Năm & Bàn Kế Hoạch 2027',
                'noi_dung'     => 'Họp ban trị sự mở rộng thảo luận kế hoạch đóng góp quỹ tu sửa tường bao nghĩa trang và hoàn thiện phần mềm quản lý cây gia phả dòng họ.',
                'ngay_to_chuc' => now()->addDays(45)->toDateString(),
                'dia_diem'     => 'Văn phòng hành chính dòng họ, Cầu Giấy, Hà Nội',
                'chi_nhanh_id' => $cnId1,
                'loai'         => 'Họp họ',
            ];
        }

        // Đối tác 2: Dòng Họ Trần Khắc - Nam Định
        if ($cnId2) {
            $events[] = [
                'tieu_de'      => 'Đại Lễ Giỗ Tổ Trần Khắc Nam Định',
                'noi_dung'     => 'Dâng hương tưởng niệm các thế hệ Thủy Tổ có công lập chi họ Trần Khắc tại Giao Thủy. Báo công các thành tựu võ học, kinh doanh của con cháu trong năm qua và bàn kế hoạch nâng cấp cổng nhà thờ tổ.',
                'ngay_to_chuc' => now()->addDays(20)->toDateString(),
                'dia_diem'     => 'Nhà Thờ Tổ Dòng Họ Trần Khắc, Giao Thủy, Nam Định',
                'chi_nhanh_id' => $cnId2,
                'loai'         => 'Giỗ tổ',
            ];
            $events[] = [
                'tieu_de'      => 'Ngày Hội Gặp Mặt Con Cháu Đầu Xuân',
                'noi_dung'     => 'Chương trình giao lưu văn nghệ, thơ ca, các trò chơi dân gian mừng xuân mới dành cho toàn bộ các hộ gia đình thuộc dòng họ Trần Khắc. Thắt chặt tình đoàn kết gia tộc.',
                'ngay_to_chuc' => now()->addDays(8)->toDateString(),
                'dia_diem'     => 'Nhà Thờ Tổ Dòng Họ Trần Khắc, Giao Thủy, Nam Định',
                'chi_nhanh_id' => $cnId2,
                'loai'         => 'Họp họ',
            ];
            $events[] = [
                'tieu_de'      => 'Trao Quỹ Khuyến Học Trần Khắc Lần 5',
                'noi_dung'     => 'Vinh danh con cháu đạt thành tích cao trong học tập, trao tặng các phần quà khuyến khích tinh thần hiếu học truyền thống của vùng đất Nam Định.',
                'ngay_to_chuc' => now()->addDays(12)->toDateString(),
                'dia_diem'     => 'Nhà Thờ Tổ Dòng Họ Trần Khắc, Giao Thủy, Nam Định',
                'chi_nhanh_id' => $cnId2,
                'loai'         => 'Họp họ',
            ];
        }

        // Đối tác 3: Dòng Họ Lê Hữu - Thanh Hóa
        if ($cnId3) {
            $events[] = [
                'tieu_de'      => 'Lễ Khánh Thành Từ Đường Lê Hữu Thanh Hóa',
                'noi_dung'     => 'Đại lễ khánh thành khu từ đường dòng họ Lê Hữu sau 6 tháng trùng tu tôn tạo bằng nguồn vốn đóng góp tự nguyện của con cháu muôn phương. Kính mời toàn thể nội ngoại dâu rể về dự lễ dâng hương.',
                'ngay_to_chuc' => now()->addDays(30)->toDateString(),
                'dia_diem'     => 'Nhà Thờ Tổ Dòng Họ Lê Hữu, Thọ Xuân, Thanh Hóa',
                'chi_nhanh_id' => $cnId3,
                'loai'         => 'Giỗ tổ',
            ];
            $events[] = [
                'tieu_de'      => 'Họp Họ Bàn Phương Án Xây Dựng Quỹ Tình Nghĩa',
                'noi_dung'     => 'Họp mặt đại diện các gia đình dòng họ Lê Hữu để thảo luận, thống nhất quy chế đóng góp và chi dùng cho Quỹ Tình nghĩa gia tộc, hỗ trợ các gia cảnh khó khăn, thăm hỏi đau ốm.',
                'ngay_to_chuc' => now()->addDays(10)->toDateString(),
                'dia_diem'     => 'Nhà Thờ Tổ Dòng Họ Lê Hữu, Thọ Xuân, Thanh Hóa',
                'chi_nhanh_id' => $cnId3,
                'loai'         => 'Họp họ',
            ];
        }

        // 3. Chèn dữ liệu vào bảng
        foreach ($events as $event) {
            DB::table('su_kiens')->insert(
                array_merge($event, [
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
            );
        }
    }
}
