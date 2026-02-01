<?php

namespace App\Observers;

use App\Models\TriDharma;
use Illuminate\Support\Facades\Storage;

class TriDharmaObserver
{
    public function created(TriDharma $triDharma): void
    {
        $this->updateFileSize($triDharma);
    }

    public function updated(TriDharma $triDharma): void
    {
        // kalau file diganti
        if ($triDharma->wasChanged('file_path')) {
            $this->updateFileSize($triDharma);
        }
    }

    protected function updateFileSize(TriDharma $triDharma): void
    {
        if (
            $triDharma->file_path &&
            Storage::disk('public')->exists($triDharma->file_path)
        ) {
            $triDharma->updateQuietly([
                'file_size' => Storage::disk('public')->size($triDharma->file_path),
            ]);
        }
    }
}
