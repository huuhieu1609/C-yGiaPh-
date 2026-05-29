<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('doi_tacs', function (Blueprint $table) {
            // Liên kết với gói dịch vụ gốc (nullable vì có thể là gói tạo thủ công từ Admin)
            $table->unsignedBigInteger('id_goi_dich_vu')->nullable()->after('id_nguoi_dung');
            // Lưu features của gói tại thời điểm mua (CSV string, ví dụ: "tao_cay_gia_pha,them_thanh_vien")
            $table->text('features')->nullable()->after('ten_goi');
            // Giới hạn đời và thành viên (copy từ gói tại thời điểm mua)
            $table->integer('max_doi')->nullable()->after('features');
            $table->integer('max_thanh_vien')->nullable()->after('max_doi');

            // Foreign key lỏng — không cascade vì gói dịch vụ có thể bị xóa sau khi mua
            $table->foreign('id_goi_dich_vu')
                  ->references('id')
                  ->on('goi_dich_vus')
                  ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('doi_tacs', function (Blueprint $table) {
            $table->dropForeign(['id_goi_dich_vu']);
            $table->dropColumn(['id_goi_dich_vu', 'features', 'max_doi', 'max_thanh_vien']);
        });
    }
};
