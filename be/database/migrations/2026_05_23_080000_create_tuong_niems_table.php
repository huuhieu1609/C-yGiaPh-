<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function run(): void
    {
        Schema::create('tuong_niems', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('thanh_vien_id'); // Người đã khuất
            $table->unsignedBigInteger('nguoi_dung_id'); // Con cháu thắp hương
            $table->string('ho_ten_nguoi_gui');
            $table->string('loai_le_vat'); // 'Nhang', 'Hoa', 'Nen', 'TraiCay'
            $table->text('loi_nhan')->nullable(); // Lời nguyện cầu / Tri ân
            $table->timestamps();

            $table->foreign('thanh_vien_id')->references('id')->on('thanh_viens')->onDelete('cascade');
            $table->foreign('nguoi_dung_id')->references('id')->on('nguoi_dungs')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tuong_niems');
    }
};
