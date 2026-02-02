<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\StudyProgram;
use App\Models\TriDharma;
use App\Support\Seo\Seo;
use Illuminate\Contracts\View\View;

class StudyProgramShowController extends Controller
{
    public function __invoke(StudyProgram $studyProgram): View
    {
        $studyProgram->load(['faculty', 'degree', 'programType']);

        $documents = TriDharma::query()
            ->where('status', 'published')
            ->where('study_program_id', $studyProgram->getKey())
            ->with([
                'authors' => function ($authorQuery) {
                    $authorQuery->whereNull('authors.deleted_at');
                },
                'documentType',
                'category',
                'faculty',
            ])
            ->latest()
            ->paginate(12);

        $canonical = route('public.study-programs.show', $studyProgram);

        $name = $studyProgram->name;
        $facultyName = $studyProgram->faculty?->name;

        $titleParts = ['Program Studi '.$name];
        if (is_string($facultyName) && $facultyName !== '') {
            $titleParts[] = 'Fakultas '.$facultyName;
        }

        $title = Seo::title($titleParts);
        $description = Seo::description('Repository Program Studi '.$name.': daftar dokumen terbit (judul, abstrak, penulis, tahun, dan PDF) untuk kebutuhan akademik.');

        $jsonLd = [
            [
                '@context' => 'https://schema.org',
                '@type' => 'EducationalOrganization',
                'name' => 'Program Studi '.$name,
                'url' => $canonical,
                'inLanguage' => 'id',
                'parentOrganization' => [
                    '@type' => 'Organization',
                    'name' => $facultyName ? 'Fakultas '.$facultyName : (string) config('app.name'),
                ],
            ],
        ];

        return view('public.study-programs.show', [
            'studyProgram' => $studyProgram,
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
