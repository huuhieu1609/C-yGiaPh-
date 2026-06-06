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
        Schema::table('thong_baos', function (Blueprint $table) {
            if (!Schema::hasColumn('thong_baos', 'chi_nhanh_id')) {
                $table->unsignedBigInteger('chi_nhanh_id')->nullable()->after('noi_dung');
            }
        });

        Schema::table('tai_lieus', function (Blueprint $table) {
            if (!Schema::hasColumn('tai_lieus', 'chi_nhanh_id')) {
                $table->unsignedBigInteger('chi_nhanh_id')->nullable()->after('mo_ta');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('thong_baos', function (Blueprint $table) {
            if (Schema::hasColumn('thong_baos', 'chi_nhanh_id')) {
                $table->dropColumn('chi_nhanh_id');
            }
        });

        Schema::table('tai_lieus', function (Blueprint $table) {
            if (Schema::hasColumn('tai_lieus', 'chi_nhanh_id')) {
                $table->dropColumn('chi_nhanh_id');
            }
        });
    }
};
