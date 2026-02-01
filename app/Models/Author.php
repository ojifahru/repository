<?php

namespace App\Models;

use Carbon\Traits\Timestamp;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Author extends Model
{
    use SoftDeletes;
    protected $table = 'authors';

    protected $fillable = [
        'name',
        'email',
        'bio',
        'image_url',
        'identifier',
    ];
    protected $casts = [
        'deleted_at' => 'datetime',
    ];

    public function triDharmas()
    {
        return $this->belongsToMany(TriDharma::class, 'author_tri_dharma', 'author_id', 'tri_dharma_id')
            ->withTimestamps()->withTrashed();
    }

    protected static function booted()
    {
        static::deleting(function ($author) {
            if ($author->isForceDeleting()) {
                $author->triDharmas()->detach();
            }
        });
    }
}
