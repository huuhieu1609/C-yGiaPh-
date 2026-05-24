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
        Schema::table('nha_tho_hos', function (Blueprint $table) {
            $table->foreignId('chi_nhanh_id')->nullable()->constrained('chi_nhanhs')->cascadeOnDelete();
            $table->double('kinh_do')->nullable();
            $table->double('vi_do')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('nha_tho_hos', function (Blueprint $table) {
            $table->dropForeign(['chi_nhanh_id']);
            $table->dropColumn(['chi_nhanh_id', 'kinh_do', 'vi_do']);
        });
    }
};
