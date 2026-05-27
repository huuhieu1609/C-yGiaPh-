<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('mo_phans', function (Blueprint $table) {
            // Thông tin vị trí mộ phần chi tiết
            $table->string('khu')->nullable()->after('dia_chi');        // Khu A, B, C
            $table->string('hang')->nullable()->after('khu');           // Hàng 1, 2, 3
            $table->string('so_mo')->nullable()->after('hang');         // Số mộ 1, 2, 3
            $table->string('huong_mo')->nullable()->after('so_mo');     // Hướng: Bắc, Nam, Đông, Tây
            $table->string('ten_nghia_trang')->nullable()->after('huong_mo'); // Tên nghĩa trang
        });
    }

    public function down(): void
    {
        Schema::table('mo_phans', function (Blueprint $table) {
            $table->dropColumn(['khu', 'hang', 'so_mo', 'huong_mo', 'ten_nghia_trang']);
        });
    }
};
