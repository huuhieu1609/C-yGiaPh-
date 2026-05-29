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
        TuongNiem::truncate();

        $cuOng = ThanhVien::where('ho_ten', 'Nguyễn Tân')->first();
        $thanhVienId = $cuOng ? $cuOng->id : (ThanhVien::value('id') ?? 1);

        $minhVyUser = NguoiDung::where('email', 'minhvy@master.com')->first();
        $partnerUser = NguoiDung::where('email', 'doitac@master.com')->first();

        $nguoiDungId1 = $minhVyUser ? $minhVyUser->id : (NguoiDung::value('id') ?? 1);
        $nguoiDungId2 = $partnerUser ? $partnerUser->id : (NguoiDung::value('id') ?? 1);

        TuongNiem::create([
            'thanh_vien_id' => $thanhVienId,
            'nguoi_dung_id' => $nguoiDungId1,
            'ho_ten_nguoi_gui' => 'Cháu Nguyễn Minh Vy',
            'loai_le_vat' => 'Hoa',
            'loi_nhan' => 'Cháu Minh Vy thành kính dâng hoa tươi lên hương hồn cụ Tổ. Cầu nguyện tổ tiên linh thiêng phù hộ độ trì cho con cháu trong gia quyến luôn bình an, mạnh khỏe, tai qua nạn khỏi.',
            'created_at' => now()->subDays(2),
            'updated_at' => now()->subDays(2),
        ]);

        TuongNiem::create([
            'thanh_vien_id' => $thanhVienId,
            'nguoi_dung_id' => $nguoiDungId2,
            'ho_ten_nguoi_gui' => 'Hội đồng gia tộc Nguyễn Đức',
            'loai_le_vat' => 'TraiCay',
            'loi_nhan' => 'Toàn thể con cháu trong dòng tộc kính dâng mâm ngũ quả tươi tốt, lòng thành tri ân công đức sinh thành và khai phá bờ cõi của Tiền nhân.',
            'created_at' => now()->subHours(6),
            'updated_at' => now()->subHours(6),
        ]);

        TuongNiem::create([
            'thanh_vien_id' => $thanhVienId,
            'nguoi_dung_id' => $nguoiDungId1,
            'ho_ten_nguoi_gui' => 'Cháu Nguyễn Đức Nam',
            'loai_le_vat' => 'Nhang',
            'loi_nhan' => 'Kính dâng nén hương thơm tưởng nhớ ngày giỗ cụ, lòng thành nhớ về cội nguồn.',
            'created_at' => now()->subMinutes(15),
            'updated_at' => now()->subMinutes(15),
        ]);
    }
}
