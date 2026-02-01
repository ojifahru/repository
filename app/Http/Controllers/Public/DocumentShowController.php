<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\TriDharma;
use Illuminate\Contracts\View\View;

class DocumentShowController extends Controller
{
    public function __invoke(int $id): View
    {
        $document = TriDharma::query()
            ->whereKey($id)
            ->where('status', 'published')
            ->with([
                'authors' => function ($authorQuery) {
                    $authorQuery->whereNull('authors.deleted_at');
                },
                'category',
                'documentType',
                'faculty',
                'studyProgram',
            ])
            ->firstOrFail();

        return view('public.documents.show', [
            'document' => $document,
        ]);
    }
}
