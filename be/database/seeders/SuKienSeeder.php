<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SuKienSeeder extends Seeder
{
    public function run(): void
    {
        $chiNhanh = DB::table('chi_nhanhs')->where('ten_chi', 'like', '%Nguyễn Đức%')->first();
        $cnId = $chiNhanh ? $chiNhanh->id : (DB::table('chi_nhanhs')->value('id') ?? 1);

        $events = [
            [
                'tieu_de' => 'Đại Lễ Giỗ Tổ Dòng Họ Nguyễn Đức - Xuân 2026',
                'noi_dung' => 'Hội đồng gia tộc tổ chức Đại lễ dâng hương tạ ơn công đức Thủy Tổ. Chương trình chi tiết: 08:00 Khai mạc dâng hương, 09:30 Tổng kết hoạt động dòng họ năm 2025, 10:30 Trao thưởng quỹ khuyến học cho con cháu có thành tích xuất sắc, 11:30 Thụ lộc liên hoan đoàn viên.',
                'ngay_to_chuc' => now()->addDays(15)->toDateString(),
                'dia_diem' => 'Nhà Thờ Tổ Dòng Họ Nguyễn Đức, Thạch Thất, Hà Nội',
                'chi_nhanh_id' => $cnId,
                'loai' => 'Giỗ tổ',
            ],
            [
                'tieu_de' => 'Lễ Tuyên Dương Khuyến Học & Vinh Danh Thành Tích Hè 2026',
                'noi_dung' => 'Trao học bổng khuyến học cho con em có thành tích học tập xuất sắc năm học 2025-2026. Vinh danh các tân sinh viên đỗ đại học điểm cao, các cháu đạt giải quốc gia và quốc tế nhằm khích lệ tinh thần hiếu học của dòng họ.',
                'ngay_to_chuc' => now()->addDays(5)->toDateString(),
                'dia_diem' => 'Hội trường Văn hóa chi nhánh Hà Nội, Cầu Giấy',
                'chi_nhanh_id' => $cnId,
                'loai' => 'Họp họ',
            ],
            [
                'tieu_de' => 'Hội Nghị Gia Tộc Tổng Kết Năm 2026 & Phương Hướng 2027',
                'noi_dung' => 'Họp Hội đồng gia tộc mở rộng thảo luận kế hoạch đóng góp quỹ tu sửa tường bao nghĩa trang, bàn phương án tổ chức mừng thọ cho các cụ cao tuổi vào xuân mới và báo cáo tiến độ hoàn thiện phần mềm quản lý Gia Phả Số.',
                'ngay_to_chuc' => now()->addDays(45)->toDateString(),
                'dia_diem' => 'Phòng họp Ban trị sự dòng họ, Thạch Thất, Hà Nội',
                'chi_nhanh_id' => $cnId,
                'loai' => 'Họp họ',
            ],
        ];

        foreach ($events as $event) {
            DB::table('su_kiens')->updateOrInsert(
                ['tieu_de' => $event['tieu_de']],
                array_merge($event, [
                    'updated_at' => now(),
                    'created_at' => now(),
                ])
            );
        }
    }
}
