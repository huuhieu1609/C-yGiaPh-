<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ChucNangSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            ['ten_chuc_nang' => 'Admin Dashboard', 'ten_slug' => 'admin-dashboard', 'url' => '/admin/dashboard', 'mo_ta' => 'Xem trang tổng quan'],
            ['ten_chuc_nang' => 'Quản Lý Gia Phả Hệ', 'ten_slug' => 'quan-ly-gia-pha-he', 'url' => '/admin/gia-pha', 'mo_ta' => 'Quản lý, xem và chỉnh sửa các cây gia phả hệ thống'],
            ['ten_chuc_nang' => 'Quản Lý Bản Đồ Hệ Thống', 'ten_slug' => 'quan-ly-ban-do-he-thong', 'url' => '/admin/quan-ly-ban-do', 'mo_ta' => 'Quản lý sơ đồ, bản đồ mộ phần và vị trí dòng họ trong hệ thống'],
            ['ten_chuc_nang' => 'Quản Lý Dòng Họ Hệ Thống', 'ten_slug' => 'quan-ly-dong-ho-he-thong', 'url' => '/admin/chi-nhanh', 'mo_ta' => 'Quản lý thông tin dòng họ, chi nhánh dòng họ hệ thống'],
            ['ten_chuc_nang' => 'Quản Lý Sự Kiện Hệ Thống', 'ten_slug' => 'quan-ly-su-kien-he-thong', 'url' => '/admin/su-kien', 'mo_ta' => 'Quản lý, tổ chức và thiết lập các sự kiện lớn nhỏ trong hệ thống'],
            ['ten_chuc_nang' => 'Quản Lý Đóng Góp Hệ Thống', 'ten_slug' => 'quan-ly-dong-gop-he-thong', 'url' => '/admin/dong-gop', 'mo_ta' => 'Quản lý và thống kê quỹ, các khoản đóng góp tự nguyện hệ thống'],
            ['ten_chuc_nang' => 'Quản Lý Nhật Ký Hoạt Động', 'ten_slug' => 'quan-ly-nhat-ky-hoat-dong', 'url' => '/admin/nhat-ky-hoat-dong', 'mo_ta' => 'Xem và theo dõi nhật ký hoạt động, thao tác của người dùng trên hệ thống'],
            ['ten_chuc_nang' => 'Cây Gia Phả', 'ten_slug' => 'cay-gia-pha', 'url' => '/cay-gia-pha', 'mo_ta' => 'Quản lý cây gia phả hệ thống'],
            ['ten_chuc_nang' => 'Tra Cứu Xưng Hô', 'ten_slug' => 'tra-cuu-xung-ho', 'url' => '/tra-cuu-xung-ho', 'mo_ta' => 'Sử dụng công cụ tra cứu xưng hô'],
            ['ten_chuc_nang' => 'Quản Lý Chi Nhánh', 'ten_slug' => 'quan-ly-chi-nhanh', 'url' => '/chi-nhanh', 'mo_ta' => 'Quản lý chi nhánh dòng họ'],
            ['ten_chuc_nang' => 'Quản Lý Mộ Phần', 'ten_slug' => 'quan-ly-mo-phan', 'url' => '/mo-phan', 'mo_ta' => 'Quản lý mộ phần, bản đồ số'],
            ['ten_chuc_nang' => 'Hệ Thống', 'ten_slug' => 'he-thong', 'url' => '/he-thong', 'mo_ta' => 'Phân quyền và quản lý tài khoản hệ thống'],
            ['ten_chuc_nang' => 'Quản Lý Người Dùng', 'ten_slug' => 'quan-ly-nguoi-dung', 'url' => '/nguoi-dung', 'mo_ta' => 'Quản lý tài khoản người dùng'],
            ['ten_chuc_nang' => 'Quản Lý Chức Vụ', 'ten_slug' => 'quan-ly-chuc-vu', 'url' => '/chuc-vu', 'mo_ta' => 'Quản lý chức vụ phân quyền'],
            ['ten_chuc_nang' => 'Quản Lý Chức Năng', 'ten_slug' => 'quan-ly-chuc-nang', 'url' => '/chuc-nang', 'mo_ta' => 'Quản lý các chức năng hệ thống'],
            ['ten_chuc_nang' => 'Quản Lý Sự Kiện', 'ten_slug' => 'quan-ly-su-kien', 'url' => '/su-kien', 'mo_ta' => 'Quản lý các sự kiện chi tiết'],
            ['ten_chuc_nang' => 'Quản Lý Đối Tác', 'ten_slug' => 'quan-ly-doi-tac', 'url' => '/admin/doi-tac', 'mo_ta' => 'Quản lý các gói và tài khoản đối tác'],
            ['ten_chuc_nang' => 'Quản Lý Tài Liệu', 'ten_slug' => 'quan-ly-tai-lieu', 'url' => '/doi-tac/tai-lieu', 'mo_ta' => 'Thư viện và tài liệu gia phả'],
            ['ten_chuc_nang' => 'Quản Lý Thông Báo', 'ten_slug' => 'quan-ly-thong-bao', 'url' => '/doi-tac/thong-bao', 'mo_ta' => 'Quản lý các thông báo và tin tức dòng họ'],
            ['ten_chuc_nang' => 'Nhật Ký Thao Tác', 'ten_slug' => 'nhat-ky-thao-tac', 'url' => '/doi-tac/nhat-ky', 'mo_ta' => 'Xem lịch sử nhật ký hoạt động dòng họ'],
            ['ten_chuc_nang' => 'Quản Lý Thành Viên', 'ten_slug' => 'quan-ly-thanh-vien', 'url' => '/doi-tac/thanh-vien', 'mo_ta' => 'Quản lý thành viên chi nhánh dòng họ'],
            ['ten_chuc_nang' => 'Kiểm Duyệt Đề Xuất', 'ten_slug' => 'kiem-duyet-de-xuat', 'url' => '/doi-tac/de-xuat', 'mo_ta' => 'Kiểm duyệt đề xuất chỉnh sửa gia phả'],
            ['ten_chuc_nang' => 'Quản Lý Gói Dịch Vụ', 'ten_slug' => 'quan-ly-goi-dich-vu', 'url' => '/doi-tac/quan-ly-goi', 'mo_ta' => 'Quản lý thông tin gói và thanh toán'],
        ];

        foreach ($permissions as $permission) {
            DB::table('chuc_nangs')->updateOrInsert(
                ['ten_slug' => $permission['ten_slug']],
                array_merge($permission, [
                    'trang_thai' => 'Hoạt động',
                    'updated_at' => now(),
                    'created_at' => now(),
                ])
            );
        }

        // ── Cấp quyền mặc định cho chức vụ "Thành Viên" ──────────────────────────
        // Các chức năng mà thành viên bình thường được phép truy cập
        $memberPermissions = [
            'Cây Gia Phả',
            'Tra Cứu Xưng Hô',
            'Quản Lý Mộ Phần',
            'Quản Lý Sự Kiện',
            'Quỹ & Sự Kiện',
            'Quản Lý Tài Liệu',
            'Quản Lý Đóng Góp',
            'Quản Lý Thông Báo',
        ];

        $idChucVuThanhVien = DB::table('chuc_vus')
            ->where('ten_chuc_vu', 'like', '%Thành Viên%')
            ->value('id');

        if ($idChucVuThanhVien) {
            foreach ($memberPermissions as $tenChucNang) {
                $idChucNang = DB::table('chuc_nangs')
                    ->where('ten_chuc_nang', $tenChucNang)
                    ->where('trang_thai', 'Hoạt động')
                    ->value('id');

                if ($idChucNang) {
                    DB::table('chi_tiet_phan_quyens')->updateOrInsert(
                        ['chuc_vu_id' => $idChucVuThanhVien, 'chuc_nang_id' => $idChucNang],
                        ['chuc_vu_id' => $idChucVuThanhVien, 'chuc_nang_id' => $idChucNang, 'updated_at' => now(), 'created_at' => now()]
                    );
                }
            }
        }
    }
}
