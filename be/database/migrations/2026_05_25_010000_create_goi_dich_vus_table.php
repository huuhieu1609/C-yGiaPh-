<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('goi_dich_vus', function (Blueprint $table) {
            $table->id();
            $table->string('ten_goi');
            $table->double('gia_ca');
            $table->integer('thoi_han')->default(12); // thời hạn tính bằng tháng, mặc định 12 tháng (1 năm)
            $table->integer('max_doi')->default(5);
            $table->integer('max_thanh_vien')->default(100);
            $table->text('mo_ta')->nullable();
            $table->string('trang_thai')->default('Hoạt động'); // Hoạt động / Tạm dừng
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('goi_dich_vus');
    }
};
