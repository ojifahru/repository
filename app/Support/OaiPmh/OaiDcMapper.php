<?php

namespace App\Support\OaiPmh;

use App\Models\TriDharma;

final class OaiDcMapper
{
    /**
     * @return array{title:string, creators:list<string>, subjects:list<string>, description:?string, publisher:string, date:?string, type:string, identifiers:list<string>, language:string}
     */
    public function map(TriDharma $document): array
    {
        $document->loadMissing([
            'authors:id,name,deleted_at,slug',
            'category:id,name,slug,deleted_at',
            'documentType:id,name,slug',
            'faculty:id,name,slug,deleted_at',
            'studyProgram:id,name,slug,deleted_at',
        ]);

        $creators = $document->authors
            ->filter(fn ($author) => $author->deleted_at === null)
            ->pluck('name')
            ->filter()
            ->values()
            ->map(fn ($name) => OaiPmh::xmlSafe((string) $name))
            ->all();

        $subjects = collect([
            $document->category?->name,
            $document->documentType?->name,
            $document->faculty?->name,
            $document->studyProgram?->name,
        ])
            ->filter()
            ->values()
            ->map(fn ($value) => OaiPmh::xmlSafe((string) $value))
            ->unique()
            ->all();

        $landingUrl = route('public.repository.show', $document, true);
        $pdfUrl = route('public.repository.pdf', $document, true);

        $identifiers = collect([$landingUrl, $pdfUrl])
            ->filter()
            ->values()
            ->map(fn ($value) => OaiPmh::xmlSafe((string) $value))
            ->unique()
            ->all();

        $type = $this->mapType($document->documentType?->name);

        return [
            'title' => OaiPmh::xmlSafe((string) $document->title),
            'creators' => $creators,
            'subjects' => $subjects,
            'description' => $document->abstract !== null ? OaiPmh::xmlSafe((string) $document->abstract) : null,
            'publisher' => OaiPmh::xmlSafe((string) config('oai.publisher')),
            'date' => $document->publish_year ? (string) $document->publish_year : null,
            'type' => $type,
            'identifiers' => $identifiers,
            'language' => OaiPmh::xmlSafe((string) config('oai.language', 'id')),
        ];
    }

    private function mapType(?string $documentTypeName): string
    {
        $name = mb_strtolower((string) ($documentTypeName ?? ''));

        if ($name === '') {
            return 'Text';
        }

        if (str_contains($name, 'skripsi') || str_contains($name, 'thesis') || str_contains($name, 'tesis') || str_contains($name, 'disertasi')) {
            return 'Thesis';
        }

        if (str_contains($name, 'artikel') || str_contains($name, 'article') || str_contains($name, 'paper')) {
            return 'Article';
        }

        return 'Text';
    }
}
