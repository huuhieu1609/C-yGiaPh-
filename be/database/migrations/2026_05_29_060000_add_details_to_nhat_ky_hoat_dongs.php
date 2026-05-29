<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('nhat_ky_hoat_dongs', function (Blueprint $table) {
            if (!Schema::hasColumn('nhat_ky_hoat_dongs', 'chi_tiet')) {
                $table->text('chi_tiet')->nullable();
            }
            if (!Schema::hasColumn('nhat_ky_hoat_dongs', 'ip')) {
                $table->string('ip', 45)->nullable();
            }
            if (!Schema::hasColumn('nhat_ky_hoat_dongs', 'trang_thai')) {
                $table->string('trang_thai', 50)->nullable()->default('Thành công');
            }
        });
    }

    public function down(): void
    {
        Schema::table('nhat_ky_hoat_dongs', function (Blueprint $table) {
            $table->dropColumn(['chi_tiet', 'ip', 'trang_thai']);
        });
    }
};
