<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DocumentType extends Model
{
    protected $table = 'document_types';

    protected $fillable = [
        'name',
        'slug',
    ];

    public function triDharmas()
    {
        return $this->hasMany(TriDharma::class);
    }

    protected static function booted()
    {
        static::deleting(function ($type) {
            if ($type->triDharmas()->exists()) {
                throw new \Exception('Jenis dokumen masih digunakan.');
            }
        });
    }
}
