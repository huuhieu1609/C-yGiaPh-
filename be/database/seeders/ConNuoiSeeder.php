<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ThanhVien;
use App\Models\ConNuoi;
use Illuminate\Support\Facades\Schema;

class ConNuoiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        ConNuoi::truncate();
        Schema::enableForeignKeyConstraints();

        $cha = ThanhVien::where('ho_ten', 'Nguyễn Văn Trung')->first();
        $me = ThanhVien::where('ho_ten', 'Đỗ Thu Trang')->first();
        $con = ThanhVien::where('ho_ten', 'Nguyễn Hoài Nam')->first();

        if ($con) {
            if ($cha) {
                ConNuoi::create([
                    'cha_me_id' => $cha->id,
                    'con_id' => $con->id,
                    'ghi_chu' => 'Nguyễn Hoài Nam là con nuôi của Nguyễn Văn Trung.',
                ]);
            }
            if ($me) {
                ConNuoi::create([
                    'cha_me_id' => $me->id,
                    'con_id' => $con->id,
                    'ghi_chu' => 'Nguyễn Hoài Nam là con nuôi của Đỗ Thu Trang.',
                ]);
            }
        }
    }
}
