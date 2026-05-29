<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('thanh_viens', function (Blueprint $table) {
            $table->enum('tinh_trang_hon_nhan', ['Độc thân', 'Đã kết hôn', 'Ly hôn', 'Góa'])
                  ->nullable()
                  ->default(null)
                  ->after('trang_thai');
        });
    }

    public function down()
    {
        Schema::table('thanh_viens', function (Blueprint $table) {
            $table->dropColumn('tinh_trang_hon_nhan');
        });
    }
};
