<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('chuc_nangs', 'hien_thi_cho')) {
            Schema::table('chuc_nangs', function (Blueprint $table) {
                $table->string('hien_thi_cho')->nullable();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('chuc_nangs', 'hien_thi_cho')) {
            Schema::table('chuc_nangs', function (Blueprint $table) {
                $table->dropColumn('hien_thi_cho');
            });
        }
    }
};
