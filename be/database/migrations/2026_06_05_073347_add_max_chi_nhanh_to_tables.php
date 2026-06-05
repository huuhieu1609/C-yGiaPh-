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
            $table->integer('max_chi_nhanh')->default(1)->after('max_thanh_vien');
        });

        Schema::table('doi_tacs', function (Blueprint $table) {
            $table->integer('max_chi_nhanh')->default(1)->after('max_thanh_vien');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('goi_dich_vus', function (Blueprint $table) {
            $table->dropColumn('max_chi_nhanh');
        });

        Schema::table('doi_tacs', function (Blueprint $table) {
            $table->dropColumn('max_chi_nhanh');
        });
    }
};
