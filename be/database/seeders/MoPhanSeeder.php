<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MoPhanSeeder extends Seeder
{
    public function run(): void
    {
        // Lấy tất cả thành viên (ưu tiên đã mất, fallback tất cả)
        $thanhViens = DB::table('thanh_viens')->pluck('id')->toArray();

        if (empty($thanhViens)) {
            return;
        }

        // Nghĩa trang Văn Điển, Hà Nội (tọa độ thực)
        $nghiaTrang = [
            [
                'ten'        => 'Nghĩa trang Văn Điển',
                'dia_chi'    => 'Xã Tứ Hiệp, huyện Thanh Trì, Hà Nội',
                'center_lat' => 20.9403,
                'center_lng' => 105.8453,
            ],
            [
                'ten'        => 'Nghĩa trang Bình Hưng Hòa',
                'dia_chi'    => 'Phường Bình Hưng Hòa, quận Bình Tân, TP.HCM',
                'center_lat' => 10.7825,
                'center_lng' => 106.5938,
            ],
            [
                'ten'        => 'Nghĩa trang Linh Sơn',
                'dia_chi'    => 'Thành phố Đà Lạt, tỉnh Lâm Đồng',
                'center_lat' => 11.9361,
                'center_lng' => 108.4419,
            ],
        ];

        $huongList = ['Bắc', 'Nam', 'Đông', 'Tây', 'Đông Bắc', 'Đông Nam', 'Tây Bắc', 'Tây Nam'];

        foreach ($thanhViens as $idx => $memberId) {
            $nt        = $nghiaTrang[$idx % count($nghiaTrang)];
            $khu       = chr(65 + ($idx % 5));              // A, B, C, D, E
            $hang      = ($idx % 10) + 1;
            $soMo      = ($idx % 20) + 1;
            $huong     = $huongList[$idx % count($huongList)];

            // Tọa độ offset nhỏ quanh tâm nghĩa trang (khoảng 2–5m mỗi mộ)
            $latOff = (($idx % 20) - 10) * 0.00005 + intdiv($idx, 20) * 0.0003;
            $lngOff = (($idx % 10) - 5)  * 0.00005 + intdiv($idx, 10) * 0.0002;

            DB::table('mo_phans')->updateOrInsert(
                ['thanh_vien_id' => $memberId],
                [
                    'ten_mo'          => "Mộ - Khu {$khu} - Hàng {$hang} - Số {$soMo}",
                    'dia_chi'         => $nt['dia_chi'],
                    'khu'             => "Khu {$khu}",
                    'hang'            => "Hàng {$hang}",
                    'so_mo'           => "Số {$soMo}",
                    'huong_mo'        => $huong,
                    'ten_nghia_trang' => $nt['ten'],
                    'vi_do'           => (string) round($nt['center_lat'] + $latOff, 7),
                    'kinh_do'         => (string) round($nt['center_lng'] + $lngOff, 7),
                    'hinh_anh'        => null,
                    'ghi_chu'         => "Mộ tại {$nt['ten']}, Khu {$khu}, Hàng {$hang}, Số {$soMo}. Hướng {$huong}.",
                    'updated_at'      => now(),
                    'created_at'      => now(),
                ]
            );
        }
    }
}
