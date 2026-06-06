<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Fix: SQLite CHECK constraint on 'type' column did not include "delete"
 * because the table was created with an older migration.
 * This migration recreates the table with the correct enum values.
 */
return new class extends Migration
{
    public function up(): void
    {
        // 1. Backup existing data
        $existing = DB::table('de_xuat_chinh_suas')->get();

        // 2. Drop old table (SQLite cannot ALTER CHECK constraints)
        Schema::drop('de_xuat_chinh_suas');

        // 3. Recreate with correct SQLite-compatible enum (includes "delete")
        DB::statement('
            CREATE TABLE "de_xuat_chinh_suas" (
                "id" integer primary key autoincrement not null,
                "thanh_vien_id" integer,
                "proposed_by_user_id" integer not null,
                "type" varchar not null check ("type" in (\'edit\', \'add_child\', \'add_spouse\', \'delete\')),
                "data" text not null,
                "status" varchar not null default \'pending\' check ("status" in (\'pending\', \'approved\', \'rejected\')),
                "approved_by" integer,
                "note" text,
                "created_at" datetime,
                "updated_at" datetime,
                foreign key("thanh_vien_id") references "thanh_viens"("id") on delete set null,
                foreign key("proposed_by_user_id") references "nguoi_dungs"("id") on delete cascade,
                foreign key("approved_by") references "nguoi_dungs"("id") on delete set null
            )
        ');

        // 4. Restore data (only valid types)
        $allowedTypes = ['edit', 'add_child', 'add_spouse', 'delete'];
        foreach ($existing as $row) {
            if (in_array($row->type, $allowedTypes)) {
                DB::table('de_xuat_chinh_suas')->insert([
                    'id'                  => $row->id,
                    'thanh_vien_id'       => $row->thanh_vien_id,
                    'proposed_by_user_id' => $row->proposed_by_user_id,
                    'type'                => $row->type,
                    'data'                => $row->data,
                    'status'              => $row->status,
                    'approved_by'         => $row->approved_by,
                    'note'                => $row->note,
                    'created_at'          => $row->created_at,
                    'updated_at'          => $row->updated_at,
                ]);
            }
        }
    }

    public function down(): void
    {
        // Recreate without "delete" type (revert)
        $existing = DB::table('de_xuat_chinh_suas')->get();

        Schema::drop('de_xuat_chinh_suas');

        DB::statement('
            CREATE TABLE "de_xuat_chinh_suas" (
                "id" integer primary key autoincrement not null,
                "thanh_vien_id" integer,
                "proposed_by_user_id" integer not null,
                "type" varchar not null check ("type" in (\'edit\', \'add_child\', \'add_spouse\')),
                "data" text not null,
                "status" varchar not null default \'pending\' check ("status" in (\'pending\', \'approved\', \'rejected\')),
                "approved_by" integer,
                "note" text,
                "created_at" datetime,
                "updated_at" datetime,
                foreign key("thanh_vien_id") references "thanh_viens"("id") on delete set null,
                foreign key("proposed_by_user_id") references "nguoi_dungs"("id") on delete cascade,
                foreign key("approved_by") references "nguoi_dungs"("id") on delete set null
            )
        ');

        $allowed = ['edit', 'add_child', 'add_spouse'];
        foreach ($existing as $row) {
            if (in_array($row->type, $allowed)) {
                DB::table('de_xuat_chinh_suas')->insert([
                    'id'                  => $row->id,
                    'thanh_vien_id'       => $row->thanh_vien_id,
                    'proposed_by_user_id' => $row->proposed_by_user_id,
                    'type'                => $row->type,
                    'data'                => $row->data,
                    'status'              => $row->status,
                    'approved_by'         => $row->approved_by,
                    'note'                => $row->note,
                    'created_at'          => $row->created_at,
                    'updated_at'          => $row->updated_at,
                ]);
            }
        }
    }
};
