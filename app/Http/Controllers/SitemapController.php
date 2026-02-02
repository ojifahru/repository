<?php

namespace App\Http\Controllers;

use App\Models\Author;
use App\Models\Categories;
use App\Models\DocumentType;
use App\Models\Faculty;
use App\Models\StudyProgram;
use App\Models\TriDharma;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function __invoke(Request $request): Response
    {
        $urls = [];

        $urls[] = [
            'loc' => route('public.home'),
            'lastmod' => now()->toAtomString(),
        ];

        $urls[] = [
            'loc' => route('public.documents.index'),
            'lastmod' => now()->toAtomString(),
        ];

        $urls[] = [
            'loc' => route('public.document-types.index'),
            'lastmod' => now()->toAtomString(),
        ];

        $urls[] = [
            'loc' => route('public.categories.index'),
            'lastmod' => now()->toAtomString(),
        ];

        $urls[] = [
            'loc' => route('public.years.index'),
            'lastmod' => now()->toAtomString(),
        ];

        $urls[] = [
            'loc' => route('public.authors.index'),
            'lastmod' => now()->toAtomString(),
        ];

        $urls[] = [
            'loc' => route('public.faculties.index'),
            'lastmod' => now()->toAtomString(),
        ];

        $urls[] = [
            'loc' => route('public.study-programs.index'),
            'lastmod' => now()->toAtomString(),
        ];

        TriDharma::query()
            ->where('status', 'published')
            ->whereNotNull('slug')
            ->select(['id', 'slug', 'updated_at'])
            ->orderBy('id')
            ->chunkById(500, function ($rows) use (&$urls): void {
                foreach ($rows as $row) {
                    $urls[] = [
                        'loc' => route('public.repository.show', $row->slug),
                        'lastmod' => optional($row->updated_at)->toAtomString() ?? now()->toAtomString(),
                    ];
                }
            });

        Author::query()
            ->whereNotNull('slug')
            ->whereHas('triDharmas', function ($q): void {
                $q->where('status', 'published');
            })
            ->select(['id', 'slug', 'updated_at'])
            ->orderBy('id')
            ->chunkById(500, function ($rows) use (&$urls): void {
                foreach ($rows as $row) {
                    $urls[] = [
                        'loc' => route('public.authors.show', $row->slug),
                        'lastmod' => optional($row->updated_at)->toAtomString() ?? now()->toAtomString(),
                    ];
                }
            });

        Faculty::query()
            ->whereNotNull('slug')
            ->select(['id', 'slug', 'updated_at'])
            ->orderBy('id')
            ->chunkById(500, function ($rows) use (&$urls): void {
                foreach ($rows as $row) {
                    $urls[] = [
                        'loc' => route('public.faculties.show', $row->slug),
                        'lastmod' => optional($row->updated_at)->toAtomString() ?? now()->toAtomString(),
                    ];
                }
            });

        StudyProgram::query()
            ->whereNotNull('slug')
            ->select(['id', 'slug', 'updated_at'])
            ->orderBy('id')
            ->chunkById(500, function ($rows) use (&$urls): void {
                foreach ($rows as $row) {
                    $urls[] = [
                        'loc' => route('public.study-programs.show', $row->slug),
                        'lastmod' => optional($row->updated_at)->toAtomString() ?? now()->toAtomString(),
                    ];
                }
            });

        DocumentType::query()
            ->whereNotNull('slug')
            ->whereHas('triDharmas', function ($q): void {
                $q->where('status', 'published');
            })
            ->select(['id', 'slug', 'updated_at'])
            ->orderBy('id')
            ->chunkById(500, function ($rows) use (&$urls): void {
                foreach ($rows as $row) {
                    $urls[] = [
                        'loc' => route('public.document-types.show', $row->slug),
                        'lastmod' => optional($row->updated_at)->toAtomString() ?? now()->toAtomString(),
                    ];
                }
            });

        Categories::query()
            ->whereNotNull('slug')
            ->whereHas('triDharmas', function ($q): void {
                $q->where('status', 'published');
            })
            ->select(['id', 'slug', 'updated_at'])
            ->orderBy('id')
            ->chunkById(500, function ($rows) use (&$urls): void {
                foreach ($rows as $row) {
                    $urls[] = [
                        'loc' => route('public.categories.show', $row->slug),
                        'lastmod' => optional($row->updated_at)->toAtomString() ?? now()->toAtomString(),
                    ];
                }
            });

        TriDharma::query()
            ->where('status', 'published')
            ->select('publish_year')
            ->distinct()
            ->orderByDesc('publish_year')
            ->pluck('publish_year')
            ->filter()
            ->each(function ($year) use (&$urls): void {
                $urls[] = [
                    'loc' => route('public.years.show', ['year' => $year]),
                    'lastmod' => now()->toAtomString(),
                ];
            });

        $xml = $this->renderXml($urls);

        return response($xml, 200)
            ->header('Content-Type', 'application/xml; charset=UTF-8');
    }

    /**
     * @param  array<int, array{loc:string,lastmod:string}>  $urls
     */
    protected function renderXml(array $urls): string
    {
        $lines = [];
        $lines[] = '<?xml version="1.0" encoding="UTF-8"?>';
        $lines[] = '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

        foreach ($urls as $url) {
            $loc = htmlspecialchars($url['loc'], ENT_QUOTES | ENT_XML1, 'UTF-8');
            $lastmod = htmlspecialchars($url['lastmod'], ENT_QUOTES | ENT_XML1, 'UTF-8');

            $lines[] = '  <url>';
            $lines[] = '    <loc>'.$loc.'</loc>';
            $lines[] = '    <lastmod>'.$lastmod.'</lastmod>';
            $lines[] = '  </url>';
        }

        $lines[] = '</urlset>';

        return implode("\n", $lines)."\n";
    }
}
