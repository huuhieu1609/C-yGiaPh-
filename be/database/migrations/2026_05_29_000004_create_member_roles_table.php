<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('member_role', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id')->constrained('thanh_viens')->onDelete('cascade');
            $table->foreignId('role_id')->constrained('roles')->onDelete('cascade');
            $table->foreignId('chi_nhanh_id')->nullable()->constrained('chi_nhanhs')->onDelete('set null');
            $table->unsignedBigInteger('assigned_by')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
            $table->unique(['member_id','role_id','chi_nhanh_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('member_role');
    }
};
