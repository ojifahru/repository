<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('program_types', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100)->unique();
            $table->string('code', 30)->nullable()->unique();
            $table->timestamps();
        });

        DB::table('program_types')->insert([
            [
                'name' => 'Akademik',
                'code' => 'Academic',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Vokasional',
                'code' => 'Vocational',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Profesi',
                'code' => 'Profession',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Spesialis',
                'code' => 'Specialist',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('program_types');
    }
};
