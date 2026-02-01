<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TriDharma extends Model
{
    use SoftDeletes;

    protected $table = 'tri_dharmas';

    protected $fillable = [
        'title',
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
