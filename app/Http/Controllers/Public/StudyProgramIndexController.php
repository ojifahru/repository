<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\StudyProgram;
use App\Support\Seo\Seo;
use Illuminate\Contracts\View\View;

class StudyProgramIndexController extends Controller
{
    public function __invoke(): View
    {
        $studyPrograms = StudyProgram::query()
            ->whereNotNull('slug')
            ->with(['faculty'])
            ->withCount([
                'triDharmas as published_documents_count' => function ($query): void {
                    $query->where('status', 'published');
                },
            ])
            ->orderBy('name')
            ->paginate(30);

        $canonical = route('public.study-programs.index');
        $title = Seo::title(['Daftar Program Studi']);
        $description = Seo::description('Daftar program studi. Telusuri dokumen repository terbit per prodi, lengkap dengan judul, abstrak, penulis, tahun, dan PDF.');

        $jsonLd = [
            [
                '@context' => 'https://schema.org',
                '@type' => 'CollectionPage',
                'name' => $title,
                'url' => $canonical,
                'inLanguage' => 'id',
            ],
        ];

        return view('public.study-programs.index', [
            'studyPrograms' => $studyPrograms,
            'seo' => [
                'title' => $title,
                'description' => $description,
                'canonical' => $canonical,
                'jsonLd' => $jsonLd,
            ],
        ]);
    }
}
