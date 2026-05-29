<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        // Indexes for nguoi_dungs
        Schema::table('nguoi_dungs', function (Blueprint $table) {
            if (!$this->indexExists('nguoi_dungs', 'nguoi_dungs_email_index')) {
                $table->index('email', 'nguoi_dungs_email_index');
            }
            if (!$this->indexExists('nguoi_dungs', 'nguoi_dungs_is_doi_tac_index')) {
                $table->index('is_doi_tac', 'nguoi_dungs_is_doi_tac_index');
            }
            if (!$this->indexExists('nguoi_dungs', 'nguoi_dungs_trang_thai_index')) {
                $table->index('trang_thai', 'nguoi_dungs_trang_thai_index');
            }
            if (!$this->indexExists('nguoi_dungs', 'nguoi_dungs_vai_tro_index')) {
                $table->index('vai_tro', 'nguoi_dungs_vai_tro_index');
            }
            if (!$this->indexExists('nguoi_dungs', 'nguoi_dungs_deleted_at_index')) {
                $table->index('deleted_at', 'nguoi_dungs_deleted_at_index');
            }
        });

        // Indexes for doi_tacs
        Schema::table('doi_tacs', function (Blueprint $table) {
            if (!$this->indexExists('doi_tacs', 'doi_tacs_trang_thai_index')) {
                $table->index('trang_thai', 'doi_tacs_trang_thai_index');
            }
            if (!$this->indexExists('doi_tacs', 'doi_tacs_ngay_ket_thuc_index')) {
                $table->index('ngay_ket_thuc', 'doi_tacs_ngay_ket_thuc_index');
            }
        });
    }

    public function down(): void {
        Schema::table('nguoi_dungs', function (Blueprint $table) {
            $table->dropIndex('nguoi_dungs_email_index');
            $table->dropIndex('nguoi_dungs_is_doi_tac_index');
            $table->dropIndex('nguoi_dungs_trang_thai_index');
            $table->dropIndex('nguoi_dungs_vai_tro_index');
            $table->dropIndex('nguoi_dungs_deleted_at_index');
        });
        Schema::table('doi_tacs', function (Blueprint $table) {
            $table->dropIndex('doi_tacs_trang_thai_index');
            $table->dropIndex('doi_tacs_ngay_ket_thuc_index');
        });
    }

    private function indexExists(string $table, string $index): bool {
        try {
            $sm = Schema::getConnection()->getDoctrineSchemaManager();
            $indexes = $sm->listTableIndexes($table);
            return array_key_exists(strtolower($index), $indexes);
        } catch (\Exception $e) {
            return false;
        }
    }
};
