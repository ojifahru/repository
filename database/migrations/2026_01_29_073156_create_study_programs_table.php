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
        Schema::create('study_programs', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100)->nullable()->unique();
            $table->foreignId('faculty_id')->constrained('faculties')->onDelete('cascade');
            $table->string('kode', 10)->nullable()->unique();
            $table->enum('degree', ['Bachelor', 'Master', 'Doctorate'])->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('study_programs');
    }
};
