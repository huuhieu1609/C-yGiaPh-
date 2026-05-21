<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('de_xuat_chinh_suas', function (Blueprint $table) {
            $table->id();
            $table->foreignId("thanh_vien_id")->nullable()->constrained("thanh_viens")->nullOnDelete();
            $table->foreignId("proposed_by_user_id")->constrained("nguoi_dungs")->cascadeOnDelete();
            $table->enum("type", ["edit", "add_child", "add_spouse"]);
            $table->json("data");
            $table->enum("status", ["pending", "approved", "rejected"])->default("pending");
            $table->foreignId("approved_by")->nullable()->constrained("nguoi_dungs")->nullOnDelete();
            $table->text("note")->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('de_xuat_chinh_suas');
    }
};
