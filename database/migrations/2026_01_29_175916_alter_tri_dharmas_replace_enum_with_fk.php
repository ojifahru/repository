<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tri_dharmas', function (Blueprint $table) {

            // Tambah FK baru
            $table->foreignId('category_id')
                ->after('abstract')
                ->constrained('categories')
                ->cascadeOnDelete();

            $table->foreignId('document_type_id')
                ->after('category_id')
                ->constrained('document_types')
                ->cascadeOnDelete();
        });

        // Hapus kolom lama (ENUM & STRING)
        Schema::table('tri_dharmas', function (Blueprint $table) {
            // Index komposit lama dipakai untuk FK (study_program_id).
            // Buat index khusus dulu supaya aman saat drop index komposit.
            $table->index('study_program_id', 'tri_dharmas_study_program_id_index');

            $table->dropIndex(['study_program_id', 'category', 'status', 'publish_year']);
            $table->dropColumn(['category', 'document_type']);
        });

        // Tambah index baru
        Schema::table('tri_dharmas', function (Blueprint $table) {
            $table->index(
                ['study_program_id', 'category_id', 'status', 'publish_year'],
                'tri_dharmas_sp_cat_status_year_idx'
            );
        });
    }

    public function down(): void
    {
        Schema::table('tri_dharmas', function (Blueprint $table) {

            // Balikin kolom lama
            $table->enum('category', [
                'pendidikan',
                'penelitian',
                'pengabdian',
            ])->after('abstract');

            $table->string('document_type')->after('category');
        });

        Schema::table('tri_dharmas', function (Blueprint $table) {
            $table->dropForeign(['category_id']);
            $table->dropForeign(['document_type_id']);
            $table->dropColumn(['category_id', 'document_type_id']);
        });
    }
};
