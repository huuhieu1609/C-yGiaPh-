<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\NguoiDung;

class DongGopSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Xóa dữ liệu đóng góp cũ để tránh trùng lặp
        DB::table('dong_gops')->delete();

        // 2. Tìm ID của Master Admin và 3 đối tác
        $adminId = DB::table('nguoi_dungs')->where('email', 'admin@master.com')->value('id');
        $partnerId1 = DB::table('nguoi_dungs')->where('email', 'doitac@master.com')->value('id');
        $partnerId2 = DB::table('nguoi_dungs')->where('email', 'doitac2@master.com')->value('id');
        $partnerId3 = DB::table('nguoi_dungs')->where('email', 'doitac3@master.com')->value('id');

        $contributions = [];

        // Admin đóng góp
        if ($adminId) {
            $contributions[] = [
                'nguoi_dung_id' => $adminId,
                'noi_dung'      => 'Đóng góp ban đầu xây dựng quỹ hệ thống gia phả chung.',
                'trang_thai'    => 'Đã duyệt',
            ];
        }

        // Đối tác 1 đóng góp
        if ($partnerId1) {
            $contributions[] = [
                'nguoi_dung_id' => $partnerId1,
                'noi_dung'      => 'Đóng góp 50,000,000đ trùng tu tôn tạo nhà thờ tổ dòng họ Nguyễn Đức.',
                'trang_thai'    => 'Đã duyệt',
            ];
            $contributions[] = [
                'nguoi_dung_id' => $partnerId1,
                'noi_dung'      => 'Đóng góp 10,000,000đ cho Quỹ khuyến học Nguyễn Đức mùa hè 2026.',
                'trang_thai'    => 'Đã duyệt',
            ];
        }

        // Đối tác 2 đóng góp
        if ($partnerId2) {
            $contributions[] = [
                'nguoi_dung_id' => $partnerId2,
                'noi_dung'      => 'Đóng góp 30,000,000đ đúc chuông đồng cổ kính và mua sắm đồ thờ tự cho từ đường dòng họ Trần Khắc.',
                'trang_thai'    => 'Đã duyệt',
            ];
            $contributions[] = [
                'nguoi_dung_id' => $partnerId2,
                'noi_dung'      => 'Đóng góp 5,000,000đ Quỹ tương thân tương ái hỗ trợ gia cảnh khó khăn dòng họ Trần Khắc.',
                'trang_thai'    => 'Chờ duyệt',
            ];
        }

        // Đối tác 3 đóng góp
        if ($partnerId3) {
            $contributions[] = [
                'nguoi_dung_id' => $partnerId3,
                'noi_dung'      => 'Đóng góp 20,000,000đ mua cây cảnh và cải tạo khuôn viên từ đường Lê Hữu Thanh Hóa.',
                'trang_thai'    => 'Đã duyệt',
            ];
        }

        // 3. Chèn dữ liệu vào bảng
        foreach ($contributions as $contrib) {
            DB::table('dong_gops')->insert(
                array_merge($contrib, [
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
            );
        }
    }
}
