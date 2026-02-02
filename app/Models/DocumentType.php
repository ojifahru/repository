<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DocumentType extends Model
{
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
}
