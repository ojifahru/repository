<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\TriDharma;
use App\Support\Seo\Seo;
use Illuminate\Contracts\View\View;

class YearArchiveIndexController extends Controller
{
    public function __invoke(): View
    {
        $years = TriDharma::query()
            ->where('status', 'published')
            ->select('publish_year')
            ->distinct()
            ->orderByDesc('publish_year')
            ->pluck('publish_year')
            ->filter()
            ->values();

        $canonical = route('public.years.index');
        $title = Seo::title(['Arsip Tahun']);
        $description = Seo::description('Arsip repository berdasarkan tahun terbit. Pilih tahun untuk melihat daftar dokumen terbit beserta abstrak dan PDF.');

        $jsonLd = [
            [
                '@context' => 'https://schema.org',
                '@type' => 'CollectionPage',
                'name' => $title,
                'url' => $canonical,
                'inLanguage' => 'id',
            ],
        ];

        return view('public.years.index', [
            'years' => $years,
            'seo' => [
                'title' => $title,
                'description' => $description,
                'canonical' => $canonical,
                'jsonLd' => $jsonLd,
            ],
        ]);
    }
}
