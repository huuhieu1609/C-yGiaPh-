<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ThanhVienSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $chiNhanhId = DB::table('chi_nhanhs')->value('id') ?? DB::table('chi_nhanhs')->insertGetId([
            'ten_chi' => 'Chi Nhánh Mặc Định',
            'mo_ta' => 'Chi nhánh mặc định để tạo dữ liệu thành viên mẫu.',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $members = [
            [
                'ho_ten' => 'Nguyễn Tân',
                'ten_goi' => 'NT',
                'gioi_tinh' => 'Nam',
                'ngay_sinh' => '1945-02-12',
                'noi_sinh' => 'Hà Nội',
                'nghe_nghiep' => 'Nông dân',
                'chi_nhanh_id' => $chiNhanhId,
                'doi_thu' => 0,
                'trang_thai' => 'Còn sống',
            ],
            [
                'ho_ten' => 'Lê Thị Tí',
                'ten_goi' => 'LT',
                'gioi_tinh' => 'Nữ',
                'ngay_sinh' => '1945-01-21',
                'noi_sinh' => 'Hà Nội',
                'nghe_nghiep' => 'Nội trợ',
                'chi_nhanh_id' => $chiNhanhId,
                'doi_thu' => 0,
                'trang_thai' => 'Còn sống',
            ],
            [
                'ho_ten' => 'Nguyễn Đức Thắng',
                'ten_goi' => 'NT',
                'gioi_tinh' => 'Nam',
                'ngay_sinh' => '1979-05-14',
                'noi_sinh' => 'Hà Nội',
                'nghe_nghiep' => 'Kỹ sư',
                'chi_nhanh_id' => $chiNhanhId,
                'doi_thu' => 1,
                'trang_thai' => 'Còn sống',
            ],
        ];

        foreach ($members as $member) {
            $search = ['ho_ten' => $member['ho_ten']];
            DB::table('thanh_viens')->updateOrInsert(
                $search,
                array_merge($member, [
                    'ghi_chu' => $member['ho_ten'] . ' là thành viên của dòng họ Nguyễn.',
                    'updated_at' => now(),
                    'created_at' => now(),
                ])
            );
        }

        $fatherId = DB::table('thanh_viens')->where('ho_ten', 'Nguyễn Tân')->value('id');
        $motherId = DB::table('thanh_viens')->where('ho_ten', 'Lê Thị Tí')->value('id');
        $childId = DB::table('thanh_viens')->where('ho_ten', 'Nguyễn Đức Thắng')->value('id');

        if ($fatherId && $motherId && $childId) {
            DB::table('thanh_viens')->where('id', $childId)->update([
                'cha_id' => $fatherId,
                'me_id' => $motherId,
                'updated_at' => now(),
            ]);
        }
    }
}
