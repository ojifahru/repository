<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\TriDharma;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DocumentDownloadController extends Controller
{
    public function __invoke(int $id): StreamedResponse
    {
        $document = TriDharma::query()
            ->whereKey($id)
            ->where('status', 'published')
            ->firstOrFail();

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
        $baseName = $document->title ? Str::slug($document->title, '_') : pathinfo($path, PATHINFO_FILENAME);
        $downloadName = $extension !== '' ? $baseName.'.'.$extension : $baseName;

        return Storage::disk($disk)->download($path, $downloadName);
    }
}
