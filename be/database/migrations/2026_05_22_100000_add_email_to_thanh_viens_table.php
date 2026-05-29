<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('thanh_viens', function (Blueprint $table) {
            if (!Schema::hasColumn('thanh_viens', 'email')) {
                $table->string('email')->nullable()->after('ho_ten');
            }
            if (!Schema::hasColumn('thanh_viens', 'thong_tin_them')) {
                $table->text('thong_tin_them')->nullable()->after('ghi_chu');
            }
        });
    }

    public function down(): void
    {
        if (Schema::hasColumn('thanh_viens', 'email')) {
            Schema::table('thanh_viens', function (Blueprint $table) {
                $table->dropColumn('email');
            });
        }

        if (Schema::hasColumn('thanh_viens', 'thong_tin_them')) {
            Schema::table('thanh_viens', function (Blueprint $table) {
                $table->dropColumn('thong_tin_them');
            });
        }
    }
};
