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
        $chiNhanhNguyen = DB::table('chi_nhanhs')->where('ten_chi', 'like', '%Nguyễn%')->first();
        $chiNhanhTran = DB::table('chi_nhanhs')->where('ten_chi', 'like', '%Trần%')->first();
        $chiNhanhLe = DB::table('chi_nhanhs')->where('ten_chi', 'like', '%Lê%')->first();

        $notifications = [
            [
                'tieu_de' => 'Quyên góp tu bổ nhà thờ tổ dòng họ năm 2026',
                'noi_dung' => "Kính thưa toàn thể con cháu trong dòng họ,\n\nBan quản trị gia tộc xin thông báo về việc khởi công tu bổ, sơn sửa lại chánh điện nhà thờ tổ dòng họ vào ngày 15/08 Âm lịch tới đây.\n\nDự kiến kinh phí sửa chữa là 150 triệu đồng. Kính mong các gia đình, con cháu gần xa tùy tâm đóng góp công đức và chuyển khoản trực tiếp qua mã QR đóng góp tự động trên cổng phả hệ để lưu lại bảng vàng công đức dòng họ.\n\nMọi đóng góp của quý vị sẽ được đối soát tự động và cập nhật trực tiếp tại trang Quỹ Đóng Góp.",
                'chi_nhanh_id' => null, // Global
            ],
            [
                'tieu_de' => 'Thông báo họp họ và tổng kết công vụ phả tộc năm 2026',
                'noi_dung' => "Thân gửi các thành viên chi nhánh Nguyễn,\n\nCuộc họp tổng kết hoạt động và công bố phả hệ số hóa của chi tộc sẽ được tổ chức vào lúc 08h00 ngày giỗ tổ năm nay tại nhà thờ họ chi tộc.\n\nNội dung cuộc họp:\n1. Báo cáo tình hình số hóa cây gia phả chi nhánh.\n2. Tổng kết ngân quỹ đóng góp năm qua.\n3. Định hướng tôn tạo mộ tổ chi tộc.\n\nRất mong mọi người sắp xếp thời gian có mặt đông đủ.",
                'chi_nhanh_id' => $chiNhanhNguyen ? $chiNhanhNguyen->id : null,
            ],
            [
                'tieu_de' => 'Qũy khuyến học dòng họ Trần - Trao thưởng đợt 1/2026',
                'noi_dung' => "Kính gửi quý phụ huynh và các em học sinh, sinh viên chi tộc họ Trần,\n\nĐể khuyến khích và cổ vũ tinh thần học tập của con em trong tộc, Ban khuyến học dòng họ Trần sẽ tổ chức lễ phát thưởng cho các em học sinh đạt thành tích xuất sắc và đỗ đại học trong năm học vừa qua.\n\nThời gian: 09:00 Chủ Nhật ngày 28/06/2026.\nĐịa điểm: Nhà văn hóa dòng họ Trần.\n\nQuý phụ huynh vui lòng cập nhật thông tin học tập của các em gửi về cho trưởng chi để ban khuyến học tổng hợp danh sách.",
                'chi_nhanh_id' => $chiNhanhTran ? $chiNhanhTran->id : null,
            ],
            [
                'tieu_de' => 'Thông báo giỗ tổ Chi nhánh họ Lê miền Trung',
                'noi_dung' => "Thông báo kỵ nhật lần thứ 120 của Đức tổ khảo chi nhánh miền Trung.\n\nTế lễ giỗ tổ sẽ diễn ra vào ngày 12 tháng 03 Âm lịch tới đây tại Từ đường Chi họ Lê.\n\nChương trình lễ kỵ:\n- Lễ cáo yết: 16h00 ngày hôm trước.\n- Lễ chính kỵ: 09h00 sáng ngày kỵ nhật.\n- Thụ lộc đại đồng sau khi kết thúc nghi lễ.\n\nKính cáo con cháu về dâng hương đông đủ.",
                'chi_nhanh_id' => $chiNhanhLe ? $chiNhanhLe->id : null,
            ],
            [
                'tieu_de' => 'Số hóa bản đồ số mộ tổ dòng họ toàn quốc',
                'noi_dung' => "Tin vui gửi đến toàn thể dòng tộc!\n\nHệ thống phả hệ số hóa đã hoàn thành tích hợp bản đồ vệ tinh GPS định vị các khu mộ tổ lớn của dòng họ trên toàn quốc.\n\nTừ nay, con cháu gần xa, đặc biệt là thế hệ trẻ hoặc những người đi làm ăn xa xứ có thể trực tiếp mở tính năng 'Bản đồ số' trên điện thoại để tra cứu tọa độ, tìm đường đi đến từng ngôi mộ cụ tổ mà không sợ lạc đường.\n\nBan quản trị đang tiếp tục bổ sung thông tin hình ảnh thực tế cho từng mộ phần.",
                'chi_nhanh_id' => null, // Global
            ],
            [
                'tieu_de' => 'Hướng dẫn sử dụng tính năng dâng lễ vật hương hỏa trực tuyến',
                'noi_dung' => "Kính gửi các gia đình thành viên,\n\nNhằm giúp con cháu ở xa xứ không thể trực tiếp về quê dâng hương kỵ nhật tổ tiên, hệ thống đã mở thêm không gian 'Tưởng Niệm & Tri Ân' ngay tại trang chi tiết của các thành viên phả tộc đã khuất.\n\nTại đây, con cháu có thể dâng nhang, hoa cúng, nến sáng hoặc quả ngọt ảo kèm theo những dòng tâm niệm thành kính gửi tới tiên tổ.\n\nMọi lời thành kính sẽ được lưu lại trang trọng tại sổ tang phả tộc.",
                'chi_nhanh_id' => null, // Global
            ]
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
