<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Laravel\Scout\Searchable;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Author extends Model
{
    use LogsActivity, Searchable, SoftDeletes;

    protected $table = 'authors';

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    protected $fillable = [
        'name',
        'slug',
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
        static::creating(function (self $author): void {
            if (! is_string($author->slug) || trim($author->slug) === '') {
                $base = Str::slug((string) $author->name);
                $base = $base !== '' ? $base : 'author';

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

                $author->slug = $candidate;
            }
        });

        static::deleting(function ($author) {
            if ($author->isForceDeleting()) {
                $author->triDharmas()->detach();
            }
        });
    }

    public function toSearchableArray(): array
    {
        return [
            'name' => $this->name,
            'bio' => $this->bio,
            'identifier' => $this->identifier,
        ];
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->useLogName('author')
            ->setDescriptionForEvent(fn (string $eventName) => "Author has been {$eventName}")
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
        // Chain fluent methods for configuration options
    }
}
