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
        Schema::table('study_programs', function (Blueprint $table) {
            $table->foreignId('degree_id')->nullable()->after('kode')->constrained('degrees')->restrictOnDelete();
            $table->foreignId('program_type_id')->nullable()->after('degree_id')->constrained('program_types')->restrictOnDelete();
        });

        $degreeIdByCode = DB::table('degrees')->pluck('id', 'code');

        foreach ($degreeIdByCode as $degreeCode => $degreeId) {
            DB::table('study_programs')
                ->where('degree', $degreeCode)
                ->update(['degree_id' => $degreeId]);
        }

        Schema::table('study_programs', function (Blueprint $table) {
            $table->dropColumn('degree');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('study_programs', function (Blueprint $table) {
            $table->enum('degree', ['Bachelor', 'Master', 'Doctorate'])->nullable()->after('kode');
        });

        $degreeCodeById = DB::table('degrees')->pluck('code', 'id');

        foreach ($degreeCodeById as $degreeId => $degreeCode) {
            DB::table('study_programs')
                ->where('degree_id', $degreeId)
                ->update(['degree' => $degreeCode]);
        }

        Schema::table('study_programs', function (Blueprint $table) {
            $table->dropConstrainedForeignId('program_type_id');
            $table->dropConstrainedForeignId('degree_id');
        });
    }
};
