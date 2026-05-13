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
        Schema::table('doi_toc_hos', function (Blueprint $table) {
            $table->unsignedBigInteger('chi_nhanh_id')->nullable()->after('id');
            // We can optionally add a foreign key constraint later if needed, but simple ID is fine
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('doi_toc_hos', function (Blueprint $table) {
            $table->dropColumn('chi_nhanh_id');
        });
    }
};
