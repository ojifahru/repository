<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\TriDharma;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DocumentDownloadController extends Controller
{
    public function __invoke(TriDharma $document): StreamedResponse
    {
        if ($document->status !== 'published') {
            abort(404);
        }

        $path = (string) $document->file_path;
        if ($path === '') {
            abort(404);
        }

        $disk = config('filesystems.default');
        if (! Storage::disk($disk)->exists($path)) {
            abort(404);
        }

        $document->increment('download_count');

        $extension = pathinfo($path, PATHINFO_EXTENSION);
        $baseNameSource = $document->title ?: pathinfo($path, PATHINFO_FILENAME);
        $baseName = Str::slug((string) $baseNameSource, '_');
        if ($baseName === '') {
            $baseName = 'document';
        }
        $downloadName = $extension !== '' ? $baseName.'.'.$extension : $baseName;

        return Storage::disk($disk)->download($path, $downloadName);
    }
}
