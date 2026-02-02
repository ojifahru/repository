<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Faculty;
use App\Support\Seo\Seo;
use Illuminate\Contracts\View\View;

class FacultyIndexController extends Controller
{
    public function __invoke(): View
    {
        $faculties = Faculty::query()
            ->whereNotNull('slug')
            ->withCount([
                'studyPrograms',
                'triDharmas as published_documents_count' => function ($query): void {
                    $query->where('status', 'published');
                },
            ])
            ->orderBy('name')
            ->get();

        $canonical = route('public.faculties.index');
        $title = Seo::title(['Daftar Fakultas']);
        $description = Seo::description('Daftar fakultas di institusi. Temukan program studi dan dokumen repository yang terbit per fakultas.');

        $jsonLd = [
            [
                '@context' => 'https://schema.org',
                '@type' => 'CollectionPage',
                'name' => $title,
                'url' => $canonical,
                'inLanguage' => 'id',
            ],
        ];

        return view('public.faculties.index', [
            'faculties' => $faculties,
            'seo' => [
                'title' => $title,
                'description' => $description,
                'canonical' => $canonical,
                'jsonLd' => $jsonLd,
            ],
        ]);
    }
}
