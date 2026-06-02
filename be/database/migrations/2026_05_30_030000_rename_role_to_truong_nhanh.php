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
        DB::table('chuc_vus')
            ->where('ten_chuc_vu', 'Quản Trị Viên Chi Nhánh')
            ->update([
                'ten_chuc_vu' => 'Trưởng Nhánh',
                'mo_ta' => 'Trưởng nhánh quản lý chi nhánh và dữ liệu nội bộ',
                'updated_at' => now(),
            ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('chuc_vus')
            ->where('ten_chuc_vu', 'Trưởng Nhánh')
            ->update([
                'ten_chuc_vu' => 'Quản Trị Viên Chi Nhánh',
                'mo_ta' => 'Quản lý chi nhánh và dữ liệu nội bộ',
                'updated_at' => now(),
            ]);
    }
};
