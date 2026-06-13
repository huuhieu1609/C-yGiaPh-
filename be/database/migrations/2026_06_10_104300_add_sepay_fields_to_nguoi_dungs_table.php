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
        Schema::table('nguoi_dungs', function (Blueprint $table) {
            $table->string('sepay_api_token')->nullable()->after('is_doi_tac');
            $table->string('sepay_bank_account')->nullable()->after('sepay_api_token');
            $table->string('sepay_bank_name')->nullable()->after('sepay_bank_account');
            $table->string('sepay_bank_owner')->nullable()->after('sepay_bank_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('nguoi_dungs', function (Blueprint $table) {
            $table->dropColumn(['sepay_api_token', 'sepay_bank_account', 'sepay_bank_name', 'sepay_bank_owner']);
        });
    }
};
