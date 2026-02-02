<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('degrees', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('code', 30)->unique();
            $table->string('slug_suffix', 10)->nullable();
            $table->timestamps();
        });

        DB::table('degrees')->insert([
            [
                'name' => 'Sarjana (S1)',
                'code' => 'Bachelor',
                'slug_suffix' => 's1',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Magister (S2)',
                'code' => 'Master',
                'slug_suffix' => 's2',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Doktor (S3)',
                'code' => 'Doctorate',
                'slug_suffix' => 's3',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('degrees');
    }
};
