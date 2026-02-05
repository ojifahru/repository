<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Faculty extends Model
{
    use LogsActivity, SoftDeletes;

    protected $table = 'faculties';

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

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

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('faculty')
            ->logFillable()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}
