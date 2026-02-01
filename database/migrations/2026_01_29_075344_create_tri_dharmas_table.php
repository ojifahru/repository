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
        Schema::create('tri_dharmas', function (Blueprint $table) {
            $table->id();

            // Konten utama
            $table->string('title');
            $table->text('abstract')->nullable();

            // Klasifikasi Tri Dharma
            $table->enum('category', [
                'pendidikan',
                'penelitian',
                'pengabdian'
            ]);

            // Jenis dokumen
            $table->string('document_type');
            // contoh: skripsi, artikel, pkm, buku

            // Relasi akademik
            $table->foreignId('faculty_id')
                ->constrained('faculties')
                ->cascadeOnDelete();

            $table->foreignId('study_program_id')
                ->constrained('study_programs')
                ->cascadeOnDelete();

            // Publikasi
            $table->year('publish_year');
            $table->enum('status', ['draft', 'published'])
                ->default('draft');

            // File
            $table->string('file_path');
            $table->unsignedInteger('file_size')->nullable();
            $table->unsignedInteger('download_count')->default(0);

            // Audit
            $table->foreignId('created_by')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->timestamps();

            // Index untuk pencarian & filter
            $table->index([
                'study_program_id',
                'category',
                'status',
                'publish_year'
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tri_dharmas');
    }
};
