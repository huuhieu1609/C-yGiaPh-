<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class VoChongSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Chỉ xóa các cặp vợ chồng thuộc chi nhánh mặc định (ThanhVienSeeder)
        // KHÔNG truncate toàn bộ bảng vì sẽ xóa mất dữ liệu của DoiTacSeeder
        $defaultNames = ['Nguyễn Tân', 'Lê Thị Tí', 'Nguyễn Đức Thắng', 'Phạm Thị Mai', 'Nguyễn Văn Trung', 'Đỗ Thu Trang', 'Lê Anh Tuấn', 'Nguyễn Thị Hương'];
        $defaultMemberIds = DB::table('thanh_viens')->whereIn('ho_ten', $defaultNames)->pluck('id');
        DB::table('vo_chongs')->whereIn('chong_id', $defaultMemberIds)->orWhereIn('vo_id', $defaultMemberIds)->delete();

        // 1. Nguyễn Tân & Lê Thị Tí
        $ongTan = DB::table('thanh_viens')->where('ho_ten', 'Nguyễn Tân')->first();
        $baTi = DB::table('thanh_viens')->where('ho_ten', 'Lê Thị Tí')->first();
        if ($ongTan && $baTi) {
            DB::table('vo_chongs')->insert([
                'chong_id' => $ongTan->id,
                'vo_id' => $baTi->id,
                'ngay_cuoi' => '1965-12-10',
                'trang_thai' => 'Đang sống',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // 2. Nguyễn Đức Thắng & Phạm Thị Mai
        $thang = DB::table('thanh_viens')->where('ho_ten', 'Nguyễn Đức Thắng')->first();
        $mai = DB::table('thanh_viens')->where('ho_ten', 'Phạm Thị Mai')->first();
        if ($thang && $mai) {
            DB::table('vo_chongs')->insert([
                'chong_id' => $thang->id,
                'vo_id' => $mai->id,
                'ngay_cuoi' => '1995-10-15',
                'trang_thai' => 'Đang sống',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // 3. Nguyễn Văn Trung & Đỗ Thu Trang
        $trung = DB::table('thanh_viens')->where('ho_ten', 'Nguyễn Văn Trung')->first();
        $trang = DB::table('thanh_viens')->where('ho_ten', 'Đỗ Thu Trang')->first();
        if ($trung && $trang) {
            DB::table('vo_chongs')->insert([
                'chong_id' => $trung->id,
                'vo_id' => $trang->id,
                'ngay_cuoi' => '2000-05-20',
                'trang_thai' => 'Đang sống',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // 4. Lê Anh Tuấn & Nguyễn Thị Hương
        $tuan = DB::table('thanh_viens')->where('ho_ten', 'Lê Anh Tuấn')->first();
        $huong = DB::table('thanh_viens')->where('ho_ten', 'Nguyễn Thị Hương')->first();
        if ($tuan && $huong) {
            DB::table('vo_chongs')->insert([
                'chong_id' => $tuan->id,
                'vo_id' => $huong->id,
                'ngay_cuoi' => '2003-09-08',
                'trang_thai' => 'Đang sống',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
