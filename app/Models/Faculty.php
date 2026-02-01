<?php

namespace App\Models;

use App\Models\StudyProgram;
use App\Models\TriDharma;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Faculty extends Model
{
    use SoftDeletes;
    protected $table = 'faculties';

    protected $fillable = [
        'name',
        'kode',
        'slug',
    ];

    public function studyPrograms()
    {
        return $this->hasMany(StudyProgram::class);
    }

    public function triDharmas()
    {
        return $this->hasManyThrough(TriDharma::class, StudyProgram::class);
    }

    protected static function booted()
    {
        static::deleting(function ($faculty) {
            if ($faculty->isForceDeleting()) {
                $faculty->studyPrograms()->forceDelete();
            } else {
                $faculty->studyPrograms()->delete();
            }
        });
    }
}
