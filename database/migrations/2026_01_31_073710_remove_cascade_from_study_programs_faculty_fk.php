<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('study_programs', function (Blueprint $table) {
            // Hapus FK lama (yang pakai cascade)
            $table->dropForeign(['faculty_id']);

            // Tambahkan FK baru TANPA cascade
            $table->foreign('faculty_id')
                ->references('id')
                ->on('faculties');
        });
    }

    public function down(): void
    {
        Schema::table('study_programs', function (Blueprint $table) {
            $table->dropForeign(['faculty_id']);

            $table->foreign('faculty_id')
                ->references('id')
                ->on('faculties')
                ->onDelete('cascade');
        });
    }
};
