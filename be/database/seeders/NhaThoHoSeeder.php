<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\ChiNhanh;

class NhaThoHoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Xóa dữ liệu cũ để tránh trùng lặp dữ liệu
        DB::table('nha_tho_hos')->delete();

        // 2. Tìm ID chi nhánh của 3 đối tác đăng ký
        $cnId1 = DB::table('chi_nhanhs')->where('ten_chi', 'like', '%Nguyễn Đức%')->value('id') 
            ?? DB::table('chi_nhanhs')->value('id');

        $cnId2 = DB::table('chi_nhanhs')->where('ten_chi', 'like', '%Trần Khắc%')->value('id');
        $cnId3 = DB::table('chi_nhanhs')->where('ten_chi', 'like', '%Lê Hữu%')->value('id');

        // 3. Chuẩn bị dữ liệu nhà thờ họ mẫu có tọa độ thực tế
        $shrines = [];

        // Đối tác 1: Nguyễn Đức - Hà Nội
        if ($cnId1) {
            $shrines[] = [
                'ten_nha_tho'  => 'Nhà Thờ Tổ Dòng Họ Nguyễn Đức',
                'dia_chi'      => 'Xã Thạch Hòa, huyện Thạch Thất, Hà Nội',
                'hinh_anh'     => 'https://images.unsplash.com/photo-1590076214227-1f4868ab3054?auto=format&fit=crop&q=80&w=600',
                'mo_ta'        => 'Nơi thờ tự Thủy Tổ dòng họ Nguyễn Đức định cư lâu đời tại Thạch Thất, Hà Nội. Nơi lưu giữ phả đồ 5 thế hệ tinh anh, giáo dục truyền thống hiếu học cho con cháu đời sau.',
                'chi_nhanh_id' => $cnId1,
                'vi_do'        => 21.0116,
                'kinh_do'      => 105.5701,
            ];
        }

        // Đối tác 2: Trần Khắc - Nam Định
        if ($cnId2) {
            $shrines[] = [
                'ten_nha_tho'  => 'Nhà Thờ Tổ Dòng Họ Trần Khắc Nam Định',
                'dia_chi'      => 'Thị trấn Ngô Đồng, huyện Giao Thủy, tỉnh Nam Định',
                'hinh_anh'     => 'https://images.unsplash.com/photo-1546482502-04b341777095?auto=format&fit=crop&q=80&w=600',
                'mo_ta'        => 'Nơi thờ tự tôn nghiêm của dòng họ Trần Khắc Nam Định, kiến trúc từ đường ba gian hai chái cổ kính, lưu giữ gia phả chữ Nho cổ và sắc phong của vương triều xưa.',
                'chi_nhanh_id' => $cnId2,
                'vi_do'        => 20.2520,
                'kinh_do'      => 106.3262,
            ];
        }

        // Đối tác 3: Lê Hữu - Thanh Hóa
        if ($cnId3) {
            $shrines[] = [
                'ten_nha_tho'  => 'Nhà Thờ Tổ Dòng Họ Lê Hữu Thanh Hóa',
                'dia_chi'      => 'Xã Xuân Lam, huyện Thọ Xuân, tỉnh Thanh Hóa',
                'hinh_anh'     => 'https://images.unsplash.com/photo-1596436889106-be35e843f974?auto=format&fit=crop&q=80&w=600',
                'mo_ta'        => 'Từ đường dòng họ Lê Hữu tọa lạc trên mảnh đất Thọ Xuân - Thanh Hóa địa linh nhân kiệt. Công trình kiến trúc gỗ lim tinh xảo, là nơi họp họ dâng hương báo công mỗi dịp lễ tết.',
                'chi_nhanh_id' => $cnId3,
                'vi_do'        => 19.9075,
                'kinh_do'      => 105.4740,
            ];
        }

        // 4. Chèn dữ liệu vào bảng
        foreach ($shrines as $shrine) {
            DB::table('nha_tho_hos')->insert(
                array_merge($shrine, [
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
            );
        }
    }
}
