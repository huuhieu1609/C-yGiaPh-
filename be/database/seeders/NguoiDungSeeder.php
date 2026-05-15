<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class NguoiDungSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            [
                'ho_ten' => 'Nguyễn Văn Quỳnh',
                'email' => 'member1@master.com',
                'mat_khau' => Hash::make('member123'),
                'so_dien_thoai' => '0912345678',
                'vai_tro' => 'Thành viên',
                'trang_thai' => 'Hoạt động',
            ],
            [
                'ho_ten' => 'Trần Thị Lan',
                'email' => 'member2@master.com',
                'mat_khau' => Hash::make('member123'),
                'so_dien_thoai' => '0987654321',
                'vai_tro' => 'Thành viên',
                'trang_thai' => 'Hoạt động',
            ],
        ];

        foreach ($users as $user) {
            DB::table('nguoi_dungs')->updateOrInsert(
                ['email' => $user['email']],
                array_merge($user, [
                    'updated_at' => now(),
                    'created_at' => now(),
                ])
            );
        }
    }
}
