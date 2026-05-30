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
            RolesAndPermissionsSeeder::class,
            DoiTacSeeder::class,
            OtherPartnersSeeder::class,
            NhaThoHoSeeder::class,
            SuKienSeeder::class,
            DongGopSeeder::class,
            TaiLieuSeeder::class,
            ThongBaoSeeder::class,
            HinhAnhSeeder::class,
            MoPhanSeeder::class,
            ConNuoiSeeder::class,
            ThamGiaSuKienSeeder::class,
            NhatKyHoatDongSeeder::class,
            VoChongSeeder::class,
            DeXuatChinhSuaSeeder::class,
            TuongNiemSeeder::class,
            GoiDichVuSeeder::class,
        ]);
    }
}


