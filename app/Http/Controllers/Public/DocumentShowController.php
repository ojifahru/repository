<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\TriDharma;
use App\Support\Seo\Seo;
use Illuminate\Contracts\View\View;

class DocumentShowController extends Controller
{
    public function __invoke(TriDharma $document): View
    {
        if ($document->status !== 'published') {
            abort(404);
        }

        $document->load([
            'authors' => function ($authorQuery) {
                $authorQuery->whereNull('authors.deleted_at');
            },
            'category',
            'documentType',
            'faculty',
            'studyProgram',
        ]);

        $canonical = route('public.repository.show', $document);
        $pdfUrl = route('public.repository.pdf', $document);

        $authors = $document->authors->pluck('name')->filter()->values()->all();
        $year = $document->publish_year ? (string) $document->publish_year : null;

        $titleParts = [$document->title];
        if (is_string($year) && $year !== '') {
            $titleParts[] = $year;
        }

        $title = Seo::title($titleParts);

        $fallbackDescriptionParts = array_filter([
            $document->documentType?->name ? 'Jenis: '.$document->documentType->name : null,
            $year ? 'Tahun: '.$year : null,
            $authors !== [] ? 'Penulis: '.implode(', ', $authors) : null,
            $document->faculty?->name ? 'Fakultas: '.$document->faculty->name : null,
            $document->studyProgram?->name ? 'Program Studi: '.$document->studyProgram->name : null,
            'Abstrak tersedia dalam halaman ini, dan PDF bisa diunduh.',
        ]);

        $description = Seo::description(
            $document->abstract ?: implode(' · ', $fallbackDescriptionParts)
        );

        $jsonLd = [
            [
                '@context' => 'https://schema.org',
                '@type' => 'ScholarlyArticle',
                'headline' => (string) ($document->title ?? ''),
                'name' => (string) ($document->title ?? ''),
                'author' => array_map(fn (string $name): array => [
                    '@type' => 'Person',
                    'name' => $name,
                ], $authors),
                'datePublished' => $year,
                'abstract' => $document->abstract ? (string) $document->abstract : null,
                'publisher' => [
                    '@type' => 'Organization',
                    'name' => (string) config('app.name'),
                ],
                'inLanguage' => 'id',
                'url' => $canonical,
                'encoding' => [
                    '@type' => 'MediaObject',
                    'contentUrl' => $pdfUrl,
                    'fileFormat' => 'application/pdf',
                ],
            ],
        ];

        $scholar = [
            'citation_title' => (string) ($document->title ?? ''),
            'citation_author' => $authors,
            'citation_publication_date' => $year,
            'citation_pdf_url' => $pdfUrl,
            'citation_abstract_html_url' => $canonical,
            'citation_language' => 'id',
        ];

        return view('public.documents.show', [
            'document' => $document,
            'seo' => [
                'title' => $title,
                'description' => $description,
                'canonical' => $canonical,
                'og' => [
                    'type' => 'article',
                    'title' => $title,
                    'description' => $description,
                    'url' => $canonical,
                ],
                'jsonLd' => $jsonLd,
                'scholar' => $scholar,
            ],
        ]);
    }
}
