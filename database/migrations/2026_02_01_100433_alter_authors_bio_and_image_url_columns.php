<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('authors', function () {
            DB::statement('ALTER TABLE authors MODIFY bio TEXT NULL');
            DB::statement('ALTER TABLE authors MODIFY image_url VARCHAR(500) NULL');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('authors', function () {
            DB::statement('ALTER TABLE authors MODIFY bio VARCHAR(255) NULL');
            DB::statement('ALTER TABLE authors MODIFY image_url VARCHAR(255) NULL');
        });
    }
};
