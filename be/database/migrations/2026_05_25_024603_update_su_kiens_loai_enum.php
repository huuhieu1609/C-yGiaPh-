<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // Thay đổi enum values cho column 'loai'
        DB::statement("ALTER TABLE su_kiens MODIFY loai ENUM('Gặp mặt', 'Thăm viếng', 'Từ thiện')");
    }

    public function down()
    {
        // Revert về giá trị cũ
        DB::statement("ALTER TABLE su_kiens MODIFY loai ENUM('Giỗ tổ', 'Họp họ', 'Cưới hỏi', 'Tang lễ')");
    }
};
