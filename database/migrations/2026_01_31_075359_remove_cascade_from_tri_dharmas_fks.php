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
        Schema::table('tri_dharmas', function (Blueprint $table) {

            $table->dropForeign(['category_id']);
            $table->dropForeign(['document_type_id']);
            $table->dropForeign(['faculty_id']);
            $table->dropForeign(['study_program_id']);
            $table->dropForeign(['created_by']); // ⬅️ BUKAN user_id

            $table->foreign('category_id')
                ->references('id')->on('categories');

            $table->foreign('document_type_id')
                ->references('id')->on('document_types');

            $table->foreign('faculty_id')
                ->references('id')->on('faculties');

            $table->foreign('study_program_id')
                ->references('id')->on('study_programs');

            $table->foreign('created_by')
                ->references('id')->on('users');
        });
    }

    public function down(): void
    {
        Schema::table('tri_dharmas', function (Blueprint $table) {

            $table->dropForeign(['category_id']);
            $table->dropForeign(['document_type_id']);
            $table->dropForeign(['faculty_id']);
            $table->dropForeign(['study_program_id']);
            $table->dropForeign(['created_by']);

            $table->foreign('category_id')
                ->references('id')->on('categories')->onDelete('cascade');

            $table->foreign('document_type_id')
                ->references('id')->on('document_types')->onDelete('cascade');

            $table->foreign('faculty_id')
                ->references('id')->on('faculties')->onDelete('cascade');

            $table->foreign('study_program_id')
                ->references('id')->on('study_programs')->onDelete('cascade');

            $table->foreign('created_by')
                ->references('id')->on('users')->onDelete('cascade');
        });
    }
};
