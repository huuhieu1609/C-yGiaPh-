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
        Schema::table('thanh_viens', function (Blueprint $table) {
            $table->unsignedTinyInteger('ngay_mat_al_ngay')->nullable()->after('ngay_mat');
            $table->unsignedTinyInteger('ngay_mat_al_thang')->nullable()->after('ngay_mat_al_ngay');
            $table->unsignedSmallInteger('ngay_mat_al_nam')->nullable()->after('ngay_mat_al_thang');
            $table->boolean('ngay_mat_al_nhuan')->default(false)->after('ngay_mat_al_nam');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('thanh_viens', function (Blueprint $table) {
            $table->dropColumn([
                'ngay_mat_al_ngay',
                'ngay_mat_al_thang',
                'ngay_mat_al_nam',
                'ngay_mat_al_nhuan'
            ]);
        });
    }
};
