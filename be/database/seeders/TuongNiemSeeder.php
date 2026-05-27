<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TuongNiem;
use App\Models\ThanhVien;
use App\Models\NguoiDung;

class TuongNiemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Truncate dữ liệu cũ
        TuongNiem::truncate();

        // 2. Tìm các thành viên đã mất trong dòng họ Nguyễn Đức
        $cuOng = ThanhVien::where('ho_ten', 'Nguyễn Đức Cường')->first();
        $cuBa = ThanhVien::where('ho_ten', 'Phạm Thị Nghĩa')->first();

        // Tìm người dùng thành viên gửi lễ
        $minhVyUser = NguoiDung::where('email', 'minhvy@master.com')->first();
        $partnerUser = NguoiDung::where('email', 'doitac@master.com')->first();

        // 3. Seed dữ liệu tưởng niệm văn hóa tâm linh ấm áp
        if ($cuOng) {
            // Lễ dâng cụ Ông Cường
            TuongNiem::create([
                'thanh_vien_id' => $cuOng->id,
                'nguoi_dung_id' => $minhVyUser ? $minhVyUser->id : 1,
                'ho_ten_nguoi_gui' => 'Cháu Nguyễn Minh Vy',
                'loai_le_vat' => 'Hương hoa',
                'loi_nhan' => 'Cháu Minh Vy kính dâng hương hoa thành kính dâng lên cụ Cường. Cầu nguyện cụ nơi suối vàng an giấc ngàn thu, phù hộ độ trì cho con cháu trong dòng tộc luôn bình an, học hành đỗ đạt cao.',
                'created_at' => now()->subDays(2),
                'updated_at' => now()->subDays(2),
            ]);

            TuongNiem::create([
                'thanh_vien_id' => $cuOng->id,
                'nguoi_dung_id' => $partnerUser ? $partnerUser->id : 1,
                'ho_ten_nguoi_gui' => 'Đại diện Đối Tác Demo',
                'loai_le_vat' => 'Mâm ngũ quả',
                'loi_nhan' => 'Hội đồng gia tộc kính dâng mâm quả chín ngọt thơm dâng lên Thủy Tổ nhân kỷ niệm ngày mất của cụ. Đời đời nhớ ơn công khai sơn lập địa.',
                'created_at' => now()->subHours(10),
                'updated_at' => now()->subHours(10),
            ]);
        }

        if ($cuBa) {
            // Lễ dâng cụ Bà Nghĩa
            TuongNiem::create([
                'thanh_vien_id' => $cuBa->id,
                'nguoi_dung_id' => $minhVyUser ? $minhVyUser->id : 1,
                'ho_ten_nguoi_gui' => 'Cháu Nguyễn Minh Vy',
                'loai_le_vat' => 'Hương hoa',
                'loi_nhan' => 'Cháu kính dâng nén hương thơm kính dâng cụ Bà Phạm Thị Nghĩa. Con cháu luôn ghi nhớ tấm lòng tần tảo, yêu thương che chở của cụ suốt cả cuộc đời.',
                'created_at' => now()->subDays(1),
                'updated_at' => now()->subDays(1),
            ]);
        }
    }
}
