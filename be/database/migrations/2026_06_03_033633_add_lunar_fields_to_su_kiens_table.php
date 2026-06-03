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
        Schema::table('su_kiens', function (Blueprint $table) {
            $table->boolean('is_lunar')->default(false)->after('ngay_to_chuc');
            $table->unsignedTinyInteger('ngay_al_ngay')->nullable()->after('is_lunar');
            $table->unsignedTinyInteger('ngay_al_thang')->nullable()->after('ngay_al_ngay');
            $table->unsignedSmallInteger('ngay_al_nam')->nullable()->after('ngay_al_thang');
            $table->boolean('ngay_al_nhuan')->default(false)->after('ngay_al_nam');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('su_kiens', function (Blueprint $table) {
            $table->dropColumn([
                'is_lunar',
                'ngay_al_ngay',
                'ngay_al_thang',
                'ngay_al_nam',
                'ngay_al_nhuan'
            ]);
        });
    }
};
