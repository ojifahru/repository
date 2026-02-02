<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class TriDharma extends Model
{
    use SoftDeletes;

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    protected $table = 'tri_dharmas';

    protected $fillable = [
        'title',
        'slug',
        'abstract',
        'category_id',
        'document_type_id',
        'faculty_id',
        'study_program_id',
        'publish_year',
        'status',
        'file_path',
        'file_size',
        'download_count',
        'created_by',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $triDharma): void {
            if (! is_string($triDharma->slug) || trim($triDharma->slug) === '') {
                $base = Str::slug((string) $triDharma->title);
                $base = $base !== '' ? $base : 'dokumen';

                $candidate = $base;
                $suffix = 2;

                while (self::query()->where('slug', $candidate)->exists()) {
                    $candidate = $base.'-'.$suffix;
                    $suffix++;

                    if ($suffix > 50) {
                        $candidate = $base.'-'.Str::lower(Str::random(6));
                        break;
                    }
                }

                $triDharma->slug = $candidate;
            }
        });
    }

    protected $casts = [
        'publish_year' => 'integer',
        'file_size' => 'integer',
        'download_count' => 'integer',
        'deleted_at' => 'datetime',
    ];

    public function authors()
    {
        return $this->belongsToMany(Author::class)->withTimestamps()->withTrashed();
    }

    public function faculty()
    {
        return $this->belongsTo(Faculty::class)->withTrashed();
    }

    public function studyProgram()
    {
        return $this->belongsTo(StudyProgram::class)->withTrashed();
    }

    public function documentType()
    {
        return $this->belongsTo(DocumentType::class, 'document_type_id');
    }

    public function category()
    {
        return $this->belongsTo(Categories::class, 'category_id')->withTrashed();
    }
}
