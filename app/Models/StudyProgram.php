<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class StudyProgram extends Model
{
    use SoftDeletes;

    protected $table = 'study_programs';

    protected $fillable = [
        'name',
        'faculty_id',
        'kode',
        'degree_id',
        'program_type_id',
        'slug',
    ];

    public function faculty(): BelongsTo
    {
        return $this->belongsTo(Faculty::class);
    }

    public function degree(): BelongsTo
    {
        return $this->belongsTo(Degree::class);
    }

    public function programType(): BelongsTo
    {
        return $this->belongsTo(ProgramType::class);
    }

    public function triDharmas(): HasMany
    {
        return $this->hasMany(TriDharma::class);
    }

    protected static function booted(): void
    {
        static::deleting(function (self $studyProgram): void {
            if ($studyProgram->isForceDeleting()) {
                $studyProgram->triDharmas()->forceDelete();
            } else {
                $studyProgram->triDharmas()->delete();
            }
        });
    }
}
