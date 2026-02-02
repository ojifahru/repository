<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\DocumentType;
use App\Models\TriDharma;
use App\Support\Seo\Seo;
use Illuminate\Contracts\View\View;

class DocumentTypeShowController extends Controller
{
    public function __invoke(DocumentType $documentType): View
    {
        $documents = TriDharma::query()
            ->where('status', 'published')
            ->where('document_type_id', $documentType->getKey())
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

        $canonical = route('public.document-types.show', $documentType);
        $title = Seo::title([$documentType->name, 'Jenis Dokumen']);
        $description = Seo::description('Repository jenis dokumen "'.$documentType->name.'": daftar dokumen terbit beserta abstrak dan PDF yang dapat diakses publik.');

        $jsonLd = [
            [
                '@context' => 'https://schema.org',
                '@type' => 'CollectionPage',
                'name' => $documentType->name,
                'url' => $canonical,
                'inLanguage' => 'id',
            ],
        ];

        return view('public.document-types.show', [
            'documentType' => $documentType,
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
