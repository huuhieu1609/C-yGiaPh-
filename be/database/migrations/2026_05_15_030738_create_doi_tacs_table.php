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
        Schema::create('doi_tacs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_nguoi_dung');
            $table->string('ten_goi')->nullable();
            $table->decimal('so_tien', 15, 2)->default(0);
            $table->date('ngay_bat_dau')->nullable();
            $table->date('ngay_ket_thuc')->nullable();
            $table->integer('trang_thai')->default(1); // 1: Active, 0: Expired
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('doi_tacs');
    }
};
