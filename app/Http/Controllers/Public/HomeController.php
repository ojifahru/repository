<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Author;
use App\Models\Faculty;
use App\Models\TriDharma;
use Illuminate\Contracts\View\View;

class HomeController extends Controller
{
    public function __invoke(): View
    {
        $publishedDocumentsQuery = TriDharma::query()
            ->where('status', 'published');

        $stats = [
            'documents' => (clone $publishedDocumentsQuery)->count(),
            'authors' => Author::query()->count(),
            'faculties' => Faculty::query()->count(),
        ];

        $latestDocuments = (clone $publishedDocumentsQuery)
            ->with(['authors' => function ($query) {
                $query->whereNull('authors.deleted_at');
            }])
            ->latest()
            ->limit(8)
            ->get();

        return view('public.home', [
            'stats' => $stats,
            'latestDocuments' => $latestDocuments,
        ]);
    }
}
