<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class DocumentType extends Model
{
    use LogsActivity;

    protected $table = 'document_types';

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    protected $fillable = [
        'name',
        'slug',
    ];

    public function triDharmas(): HasMany
    {
        return $this->hasMany(TriDharma::class);
    }

    protected static function booted(): void
    {
        static::deleting(function (self $type): void {
            if ($type->triDharmas()->exists()) {
                throw new \Exception('Jenis dokumen masih digunakan.');
            }
        });
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('document_type')
            ->logFillable()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}
