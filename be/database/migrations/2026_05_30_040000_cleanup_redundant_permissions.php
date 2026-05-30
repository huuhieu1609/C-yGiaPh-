<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $redundantNames = [
            'Quản Lý Dòng Họ',
            'Quản Lý Đời Tộc Họ',
            'Quản Lý Nhà Thờ Họ',
            'Quỹ & Sự Kiện',
            'Quản Lý Đóng Góp'
        ];

        // 1. Lấy IDs của các chức năng dư thừa
        $ids = DB::table('chuc_nangs')
            ->whereIn('ten_chuc_nang', $redundantNames)
            ->pluck('id')
            ->toArray();

        if (!empty($ids)) {
            // 2. Xóa liên kết trong chi_tiet_phan_quyens
            DB::table('chi_tiet_phan_quyens')->whereIn('chuc_nang_id', $ids)->delete();
            
            // 3. Xóa liên kết trong thanh_vien_chuc_nangs
            DB::table('thanh_vien_chuc_nangs')->whereIn('chuc_nang_id', $ids)->delete();

            // 4. Xóa chức năng dư thừa trong chuc_nangs
            DB::table('chuc_nangs')->whereIn('id', $ids)->delete();
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No rollback required for data cleanup
    }
};
