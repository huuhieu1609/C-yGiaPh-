<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('nguoi_dungs', function (Blueprint $table) {
            $table->foreignId('chi_nhanh_id')->nullable()->constrained('chi_nhanhs')->nullOnDelete()->after('trang_thai');
        });
    }

    public function down()
    {
        Schema::table('nguoi_dungs', function (Blueprint $table) {
            $table->dropConstrainedForeignId('chi_nhanh_id');
        });
    }
};
