<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\TriDharma;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DocumentFileController extends Controller
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

        $extension = pathinfo($path, PATHINFO_EXTENSION);
        $baseName = $document->title ? Str::slug($document->title, '_') : pathinfo($path, PATHINFO_FILENAME);
        $downloadName = $extension !== '' ? $baseName.'.'.$extension : $baseName;

        return Storage::disk($disk)->response($path, $downloadName, [
            'Content-Disposition' => 'inline; filename="'.$downloadName.'"',
        ]);
    }
}
