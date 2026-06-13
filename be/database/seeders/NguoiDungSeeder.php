<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\ThanhVien;
use App\Models\NguoiDung;

class NguoiDungSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Giữ các users mẫu ban đầu
        $initialUsers = [
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
            [
                'ho_ten' => 'Nguyễn Minh Vy',
                'email' => 'minhvy@master.com',
                'mat_khau' => Hash::make('member123'),
                'so_dien_thoai' => '0966555444',
                'vai_tro' => 'Thành viên',
                'trang_thai' => 'Hoạt động',
            ],
        ];

        foreach ($initialUsers as $user) {
            DB::table('nguoi_dungs')->updateOrInsert(
                ['email' => $user['email']],
                array_merge($user, [
                    'updated_at' => now(),
                    'created_at' => now(),
                ])
            );
        }

        // 2. Lấy danh sách ID Chức vụ tương ứng
        $idAdmin = DB::table('chuc_vus')->where('ten_chuc_vu', 'like', '%Quản Trị Viên Tổng%')->value('id') 
            ?? DB::table('chuc_vus')->where('ten_chuc_vu', 'like', '%Quản trị%')->value('id') 
            ?? 1;
        $idTruongNhanh = DB::table('chuc_vus')->where('ten_chuc_vu', 'like', '%Trưởng Nhánh%')->value('id') 
            ?? DB::table('chuc_vus')->where('ten_chuc_vu', 'like', '%Nhánh%')->value('id') 
            ?? 2;
        $idThanhVien = DB::table('chuc_vus')->where('ten_chuc_vu', 'like', '%Thành Viên%')->value('id') 
            ?? DB::table('chuc_vus')->where('ten_chuc_vu', 'like', '%Thành viên%')->value('id') 
            ?? 3;

        // 3. Quét tất cả thành viên trong gia phả và đồng bộ sang tài khoản người dùng
        $members = ThanhVien::all();
        
        foreach ($members as $member) {
            // Xác định hoặc tạo email dựa vào họ tên nếu chưa có
            $email = $member->email;
            if (!$email) {
                $email = $this->convertToEmail($member->ho_ten);
                $member->update(['email' => $email]);
            }

            // Mặc định vai trò & chức vụ
            $vaiTro = 'Thành viên'; // Enum chỉ nhận 'Admin' hoặc 'Thành viên'
            $isDoiTac = 0;
            $chucVuId = $idThanhVien;
            $sdt = '09' . rand(10000000, 99999999);

            // Nguyễn Tân (Cụ tổ) là Admin tối cao
            if ($member->ho_ten === 'Nguyễn Tân') {
                $vaiTro = 'Admin';
                $chucVuId = $idAdmin;
            } 
            // Nguyễn Đức Thắng (Con trai cả) làm Trưởng nhánh / Đối tác
            elseif ($member->ho_ten === 'Nguyễn Đức Thắng') {
                $vaiTro = 'Thành viên';
                $isDoiTac = 1;
                $chucVuId = $idTruongNhanh;
            }

            // Cập nhật hoặc thêm tài khoản người dùng
            NguoiDung::updateOrCreate(
                ['email' => $email],
                [
                    'ho_ten'        => $member->ho_ten,
                    'mat_khau'      => Hash::make('123456'), // Mật khẩu mặc định là 123456
                    'so_dien_thoai' => $sdt,
                    'vai_tro'       => $vaiTro,
                    'id_chuc_vu'    => $chucVuId,
                    'trang_thai'    => 'Hoạt động',
                    'is_doi_tac'    => $isDoiTac,
                    'chi_nhanh_id'  => $member->chi_nhanh_id,
                ]
            );
        }
    }

    /**
     * Chuyển đổi tên tiếng Việt có dấu sang email không dấu viết liền.
     */
    private function convertToEmail(string $fullName): string
    {
        $coDau = [
            'a' => ['á','à','ả','ã','ạ','ă','ắ','ằ','ẳ','ẵ','ặ','â','ấ','ầ','ẩ','ẫ','ậ'],
            'd' => ['đ'],
            'e' => ['é','è','ẻ','ẽ','ẹ','ê','ế','ề','ể','ễ','ệ'],
            'i' => ['í','ì','ỉ','ĩ','ị'],
            'o' => ['ó','ò','ỏ','õ','ọ','ô','ố','ồ','ổ','ỗ','ộ','ơ','ớ','ờ','ở','ỡ','ợ'],
            'u' => ['ú','ù','ủ','ũ','ụ','ư','ứ','ừ','ử','ữ','ự'],
            'y' => ['ý','ỳ','ỷ','ỹ','ỵ'],
        ];
        
        $str = mb_strtolower($fullName, 'UTF-8');
        foreach ($coDau as $ascii => $utf8s) {
            foreach ($utf8s as $utf8) {
                $str = str_replace($utf8, $ascii, $str);
            }
        }
        
        $str = str_replace(' ', '', $str);
        return $str . '@giapha.com';
    }
}
