<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            AdminSeeder::class,
            ChucNangSeeder::class,
            ChucVuSeeder::class,
            ChiNhanhSeeder::class,
            NguoiDungSeeder::class,
            ThanhVienSeeder::class,
            DoiTocHoSeeder::class,
            NhaThoHoSeeder::class,
            SuKienSeeder::class,
            TaiLieuSeeder::class,
            ThongBaoSeeder::class,
            HinhAnhSeeder::class,
            MoPhanSeeder::class,
            DongGopSeeder::class,
            ConNuoiSeeder::class,
            ThamGiaSuKienSeeder::class,
            NhatKyHoatDongSeeder::class,
            VoChongSeeder::class,
        ]);
    }
}
