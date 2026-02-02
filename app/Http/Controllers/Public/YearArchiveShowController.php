<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\TriDharma;
use App\Support\Seo\Seo;
use Illuminate\Contracts\View\View;

class YearArchiveShowController extends Controller
{
    public function __invoke(int $year): View
    {
        if ($year < 1900 || $year > 2100) {
            abort(404);
        }

        $documents = TriDharma::query()
            ->where('status', 'published')
            ->where('publish_year', $year)
            ->with([
                'authors' => function ($authorQuery) {
                    $authorQuery->whereNull('authors.deleted_at');
                },
                'documentType',
                'category',
                'faculty',
                'studyProgram',
            ])
            ->latest()
            ->paginate(12);

        $canonical = route('public.years.show', ['year' => $year]);
        $title = Seo::title(['Dokumen '.$year, 'Arsip Tahun']);
        $description = Seo::description('Daftar dokumen repository yang terbit pada tahun '.$year.'. Lengkap dengan judul, abstrak, penulis, dan PDF.');

        $jsonLd = [
            [
                '@context' => 'https://schema.org',
                '@type' => 'CollectionPage',
                'name' => 'Arsip Tahun '.$year,
                'url' => $canonical,
                'inLanguage' => 'id',
            ],
        ];

        return view('public.years.show', [
            'year' => $year,
            'documents' => $documents,
            'seo' => [
                'title' => $title,
                'description' => $description,
                'canonical' => $canonical,
                'og' => [
                    'type' => 'website',
                    'title' => $title,
                    'description' => $description,
                    'url' => $canonical,
                ],
                'jsonLd' => $jsonLd,
            ],
        ]);
    }
}
