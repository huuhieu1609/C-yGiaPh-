<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\ChiNhanh;
use App\Models\NguoiDung;

class SuKienSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Xóa sạch sự kiện cũ để tránh trùng lặp
        DB::table('su_kiens')->delete();

        // Lấy chi nhánh của Đối tác Demo (ưu tiên chi nhánh sở hữu bởi đối tác)
        $partnerId = NguoiDung::where('email', 'doitac@master.com')->value('id');
        $chiNhanh = $partnerId
            ? ChiNhanh::where('id_nguoi_dung', $partnerId)->first()
            : ChiNhanh::where('ten_chi', 'like', '%Nguyễn Đức%')->first();
        $cnId = $chiNhanh ? $chiNhanh->id : ChiNhanh::value('id');

        $events = [
            [
                'tieu_de' => 'Đại Lễ Giỗ Tổ Dòng Họ Nguyễn Đức',
                'noi_dung' => 'Ban trị sự tổ chức Đại lễ dâng hương tạ ơn Thủy Tổ. Chương trình chi tiết bao gồm: 8:00 Dâng hương, 9:30 Báo cáo hoạt động và tài chính dòng họ, 10:30 Trao thưởng khuyến học mùa hè, 11:30 Thụ lộc liên hoan đoàn viên.',
                'ngay_to_chuc' => now()->addDays(15)->toDateString(),
                'dia_diem' => 'Nhà Thờ Tổ Dòng Họ Nguyễn Đức, Thạch Thất, Hà Nội',
                'chi_nhanh_id' => $cnId,
                'loai' => 'Giỗ tổ',
            ],
            [
                'tieu_de' => 'Họp Mặt Khuyến Học Hè 2026',
                'noi_dung' => 'Trao học bổng khuyến học cho con em có thành tích học tập xuất sắc năm học 2025-2026. Vinh danh các cháu đỗ đại học thủ khoa, đạt giải quốc gia, quốc tế.',
                'ngay_to_chuc' => now()->addDays(5)->toDateString(),
                'dia_diem' => 'Nhà Thờ Tổ Dòng Họ Nguyễn Đức, Thạch Thất, Hà Nội',
                'chi_nhanh_id' => $cnId,
                'loai' => 'Khuyến học',
            ],
            [
                'tieu_de' => 'Họp Họ Tổng Kết Năm & Bàn Kế Hoạch 2027',
                'noi_dung' => 'Họp ban trị sự mở rộng thảo luận kế hoạch đóng góp quỹ tu sửa tường bao nghĩa trang và hoàn thiện phần mềm quản lý cây gia phả dòng họ.',
                'ngay_to_chuc' => now()->addDays(45)->toDateString(),
                'dia_diem' => 'Văn phòng hành chính dòng họ, Cầu Giấy, Hà Nội',
                'chi_nhanh_id' => $cnId,
                'loai' => 'Họp họ',
            ],
        ];

        foreach ($events as $event) {
            DB::table('su_kiens')->insert(
                array_merge($event, [
                    'updated_at' => now(),
                    'created_at' => now(),
                ])
            );
        }
    }
}

