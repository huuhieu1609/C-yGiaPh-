<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ThanhVien;
use App\Models\VoChong;
use Illuminate\Support\Facades\Schema;

class VoChongSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        VoChong::truncate();
        Schema::enableForeignKeyConstraints();

        $couples = [
            [
                'chong' => 'Nguyễn Tân',
                'vo' => 'Lê Thị Tí',
                'ngay_cuoi' => '1965-12-10',
                'trang_thai' => 'Đang sống',
            ],
            [
                'chong' => 'Nguyễn Đức Thắng',
                'vo' => 'Phạm Thị Mai',
                'ngay_cuoi' => '1995-10-15',
                'trang_thai' => 'Đang sống',
            ],
            [
                'chong' => 'Nguyễn Văn Trung',
                'vo' => 'Đỗ Thu Trang',
                'ngay_cuoi' => '2000-05-20',
                'trang_thai' => 'Đang sống',
            ],
            [
                'chong' => 'Lê Anh Tuấn',
                'vo' => 'Nguyễn Thị Hương',
                'ngay_cuoi' => '2003-09-08',
                'trang_thai' => 'Đang sống',
            ],
        ];

        foreach ($couples as $c) {
            $chong = ThanhVien::where('ho_ten', $c['chong'])->first();
            $vo = ThanhVien::where('ho_ten', $c['vo'])->first();

            if ($chong && $vo) {
                VoChong::create([
                    'chong_id' => $chong->id,
                    'vo_id' => $vo->id,
                    'ngay_cuoi' => $c['ngay_cuoi'],
                    'trang_thai' => $c['trang_thai'],
                ]);
            }
        }
    }
}
