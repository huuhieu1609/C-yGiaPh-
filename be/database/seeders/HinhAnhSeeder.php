<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\ThanhVien;
use App\Models\HinhAnh;

class HinhAnhSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Xóa sạch dữ liệu cũ
        HinhAnh::truncate();

        // 1. Nguyễn Đức Nam
        $nam = ThanhVien::where('ho_ten', 'Nguyễn Đức Nam')->first();
        if ($nam) {
            HinhAnh::create([
                'thanh_vien_id' => $nam->id,
                'duong_dan' => 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?q=80&w=600&auto=format&fit=crop',
                'mo_ta' => 'Ảnh chân dung Nguyễn Đức Nam chụp tại lễ trao giải lập trình viên xuất sắc năm 2025.',
                'created_at' => now()->subDays(10),
                'updated_at' => now()->subDays(10),
            ]);

            HinhAnh::create([
                'thanh_vien_id' => $nam->id,
                'duong_dan' => 'https://images.unsplash.com/photo-1522075469751-3a6694fb2f61?q=80&w=600&auto=format&fit=crop',
                'mo_ta' => 'Nguyễn Đức Nam tham gia hoạt động Team Building cùng công ty tại Đà Nẵng.',
                'created_at' => now()->subDays(8),
                'updated_at' => now()->subDays(8),
            ]);
        }

        // 2. Nguyễn Văn Minh
        $minh = ThanhVien::where('ho_ten', 'Nguyễn Văn Minh')->first();
        if ($minh) {
            HinhAnh::create([
                'thanh_vien_id' => $minh->id,
                'duong_dan' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?q=80&w=600&auto=format&fit=crop',
                'mo_ta' => 'Nguyễn Văn Minh trong ngày tựu trường Đại học Bách Khoa.',
                'created_at' => now()->subDays(15),
                'updated_at' => now()->subDays(15),
            ]);
        }

        // 3. Nguyễn Đức Thắng
        $thang = ThanhVien::where('ho_ten', 'Nguyễn Đức Thắng')->first();
        if ($thang) {
            HinhAnh::create([
                'thanh_vien_id' => $thang->id,
                'duong_dan' => 'https://images.unsplash.com/photo-1506794778202-cad84cf45f1d?q=80&w=600&auto=format&fit=crop',
                'mo_ta' => 'Bác Nguyễn Đức Thắng chụp ảnh kỷ niệm nhân dịp khánh thành nhà thờ họ chi thứ nhất.',
                'created_at' => now()->subMonths(2),
                'updated_at' => now()->subMonths(2),
            ]);
        }

        // 4. Lê Thu Hà
        $ha = ThanhVien::where('ho_ten', 'Lê Thu Hà')->first();
        if ($ha) {
            HinhAnh::create([
                'thanh_vien_id' => $ha->id,
                'duong_dan' => 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?q=80&w=600&auto=format&fit=crop',
                'mo_ta' => 'Cháu Lê Thu Hà đạt giải nhất cuộc thi vẽ tranh cấp tỉnh.',
                'created_at' => now()->subDays(5),
                'updated_at' => now()->subDays(5),
            ]);
        }

        // 5. Phạm Thị Mai
        $mai = ThanhVien::where('ho_ten', 'Phạm Thị Mai')->first();
        if ($mai) {
            HinhAnh::create([
                'thanh_vien_id' => $mai->id,
                'duong_dan' => 'https://images.unsplash.com/photo-1544005313-94ddf0286df2?q=80&w=600&auto=format&fit=crop',
                'mo_ta' => 'Cô Phạm Thị Mai tại hội nghị dược sĩ Hà Nội.',
                'created_at' => now()->subMonths(1),
                'updated_at' => now()->subMonths(1),
            ]);
        }
    }
}
