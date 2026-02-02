<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\DocumentType;
use App\Support\Seo\Seo;
use Illuminate\Contracts\View\View;

class DocumentTypeIndexController extends Controller
{
    public function __invoke(): View
    {
        $documentTypes = DocumentType::query()
            ->whereNotNull('slug')
            ->withCount([
                'triDharmas as published_documents_count' => function ($query): void {
                    $query->where('status', 'published');
                },
            ])
            ->orderBy('name')
            ->get();

        $canonical = route('public.document-types.index');
        $title = Seo::title(['Jenis Dokumen']);
        $description = Seo::description('Telusuri jenis dokumen di repository institusi (skripsi, tesis, jurnal, artikel, dll) beserta dokumen terbit dan PDF.');

        $jsonLd = [
            [
                '@context' => 'https://schema.org',
                '@type' => 'CollectionPage',
                'name' => $title,
                'url' => $canonical,
                'inLanguage' => 'id',
            ],
        ];

        return view('public.document-types.index', [
            'documentTypes' => $documentTypes,
            'seo' => [
                'title' => $title,
                'description' => $description,
                'canonical' => $canonical,
                'jsonLd' => $jsonLd,
            ],
        ]);
    }
}
