<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Author;
use App\Models\TriDharma;
use Illuminate\Contracts\View\View;

class AuthorShowController extends Controller
{
    public function __invoke(int $id): View
    {
        $author = Author::query()->findOrFail($id);

        $documents = TriDharma::query()
            ->where('status', 'published')
            ->whereHas('authors', function ($authorQuery) use ($author) {
                $authorQuery
                    ->whereNull('authors.deleted_at')
                    ->where('authors.id', $author->getKey());
            })
            ->with([
                'authors' => function ($authorQuery) {
                    $authorQuery->whereNull('authors.deleted_at');
                },
            ])
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return view('public.authors.show', [
            'author' => $author,
            'documents' => $documents,
        ]);
    }
}
