<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\NguoiDung;

class NhatKyHoatDongSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Xóa sạch dữ liệu cũ
        DB::table('nhat_ky_hoat_dongs')->delete();

        // 1. Tìm tài khoản Admin và Đối tác
        $adminId = NguoiDung::where('email', 'admin@master.com')->value('id');
        $partnerId = NguoiDung::where('email', 'doitac@master.com')->value('id');
        $memberId = NguoiDung::where('email', 'minhvy@master.com')->value('id');

        // 2. Tạo logs cho Admin
        if ($adminId) {
            $adminActivities = [
                'Khởi tạo hệ thống quản lý phả hệ số thông minh',
                'Cấp quyền Đối tác quản trị cấp cao cho tài khoản doitac@master.com',
                'Đã đồng bộ hóa dữ liệu sao lưu của dòng họ Nguyễn Đức',
            ];
            foreach ($adminActivities as $act) {
                DB::table('nhat_ky_hoat_dongs')->insert([
                    'nguoi_dung_id' => $adminId,
                    'hanh_dong' => $act,
                    'thoi_gian' => now()->subDays(5),
                    'created_at' => now()->subDays(5),
                    'updated_at' => now()->subDays(5),
                ]);
            }
        }

        // 3. Tạo logs cho Đối tác (Partner) - thể hiện hoạt động thực tế trên mobile
        if ($partnerId) {
            $partnerActivities = [
                [
                    'act' => 'Đã thêm mới thành viên Nguyễn Tuệ Lâm vào Đời thứ 5 chi nhánh Hà Nội.',
                    'time' => now()->subDays(2),
                ],
                [
                    'act' => 'Đã cập nhật tọa độ bản đồ cho Mộ Cụ Nguyễn Đức Cường (Thủy Tổ).',
                    'time' => now()->subDays(1)->subHours(8),
                ],
                [
                    'act' => 'Đã tạo sự kiện mới: "Đại Lễ Giỗ Tổ Dòng Họ Nguyễn Đức" diễn ra tại Thạch Thất.',
                    'time' => now()->subDays(1)->subHours(2),
                ],
                [
                    'act' => 'Đã phê duyệt đề xuất chỉnh sửa thông tin thành viên Nguyễn Đức Anh (thăng chức CTO).',
                    'time' => now()->subHours(5),
                ],
                [
                    'act' => 'Đã bật chế độ tự động phê duyệt đề xuất chỉnh sửa cho chi nhánh Hà Nội.',
                    'time' => now()->subHours(1),
                ],
            ];

            foreach ($partnerActivities as $item) {
                DB::table('nhat_ky_hoat_dongs')->insert([
                    'nguoi_dung_id' => $partnerId,
                    'hanh_dong' => $item['act'],
                    'thoi_gian' => $item['time'],
                    'created_at' => $item['time'],
                    'updated_at' => $item['time'],
                ]);
            }
        }

        // 4. Tạo logs cho Thành viên (Member) - thể hiện tương tác của người dùng thường
        if ($memberId) {
            $memberActivities = [
                [
                    'act' => 'Đã gửi dâng hương hoa tưởng niệm Cụ Nguyễn Đức Cường.',
                    'time' => now()->subDays(2),
                ],
                [
                    'act' => 'Đã gửi đề xuất cập nhật thông tin cá nhân (nghề nghiệp và tiểu sử du học).',
                    'time' => now()->subHours(2),
                ],
            ];

            foreach ($memberActivities as $item) {
                DB::table('nhat_ky_hoat_dongs')->insert([
                    'nguoi_dung_id' => $memberId,
                    'hanh_dong' => $item['act'],
                    'thoi_gian' => $item['time'],
                    'created_at' => $item['time'],
                    'updated_at' => $item['time'],
                ]);
            }
        }
    }
}

