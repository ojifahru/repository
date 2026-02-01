<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StudyProgram extends Model
{
    use SoftDeletes;
    protected $table = 'study_programs';

    protected $fillable = [
        'name',
        'faculty_id',
        'kode',
        'degree',
        'slug',
    ];

    public function faculty()
    {
        return $this->belongsTo(Faculty::class);
    }

    public function triDharmas()
    {
        return $this->hasMany(TriDharma::class);
    }

    protected static function booted()
    {
        static::deleting(function ($prodi) {
            if ($prodi->isForceDeleting()) {
                $prodi->triDharmas()->forceDelete();
            } else {
                $prodi->triDharmas()->delete();
            }
        });
    }
}
