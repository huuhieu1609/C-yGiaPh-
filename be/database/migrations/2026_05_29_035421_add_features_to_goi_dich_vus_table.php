<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('goi_dich_vus', function (Blueprint $table) {
            // Lưu danh sách key tính năng phân cách bởi dấu phẩy
            // Ví dụ: "tao_cay_gia_pha,them_thanh_vien,quan_ly_su_kien"
            $table->text('features')->nullable()->after('mo_ta');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('goi_dich_vus', function (Blueprint $table) {
            $table->dropColumn('features');
        });
    }
};

