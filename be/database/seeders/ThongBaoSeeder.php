<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ThongBaoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $notifications = [
            [
                'tieu_de' => 'Thông báo kế hoạch Đại Lễ Giỗ Tổ năm 2026',
                'noi_dung' => 'Ban trị sự tộc họ trân trọng thông báo lịch tổ chức Đại lễ Giỗ Tổ vào ngày mùng 10 tháng 3 Âm lịch tại Nhà Thờ Tổ. Kính mời toàn thể con cháu nội ngoại về sum họp đông đủ và đóng góp quỹ dâng hương.',
            ],
            [
                'tieu_de' => 'Cập nhật hoàn thiện hệ thống Gia Phả Số hóa',
                'noi_dung' => 'Hội đồng gia tộc đã hoàn thành việc rà soát và cập nhật danh sách thành viên thế hệ thứ 5 vào cây phả hệ điện tử. Đề nghị các trưởng chi kiểm tra lại thông tin cá nhân của con cháu.',
            ],
            [
                'tieu_de' => 'Kêu gọi quyên góp tu sửa khuôn viên nghĩa trang dòng họ',
                'noi_dung' => 'Hiện tại bờ tường bao khu mộ tổ tại nghĩa trang Thạch Thất đã xuống cấp. Ban trị sự kêu gọi tấm lòng hảo tâm của các gia đình đóng góp kinh phí tôn tạo, dự kiến triển khai vào tháng 8 Âm lịch.',
            ],
        ];

        foreach ($notifications as $notification) {
            DB::table('thong_baos')->updateOrInsert(
                ['tieu_de' => $notification['tieu_de']],
                array_merge($notification, [
                    'updated_at' => now(),
                    'created_at' => now(),
                ])
            );
        }
    }
}
