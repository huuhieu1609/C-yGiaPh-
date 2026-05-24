<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('chi_nhanhs', function (Blueprint $table) {
            $table->boolean('is_auto_approve')->default(false)->after('ten_chi');
        });
    }

    public function down()
    {
        Schema::table('chi_nhanhs', function (Blueprint $table) {
            $table->dropColumn('is_auto_approve');
        });
    }
};
