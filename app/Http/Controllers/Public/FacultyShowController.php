<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Faculty;
use App\Models\TriDharma;
use App\Support\Seo\Seo;
use Illuminate\Contracts\View\View;

class FacultyShowController extends Controller
{
    public function __invoke(Faculty $faculty): View
    {
        $faculty->load([
            'studyPrograms' => function ($query): void {
                $query->orderBy('name');
            },
        ]);

        $documents = TriDharma::query()
            ->where('status', 'published')
            ->where('faculty_id', $faculty->getKey())
            ->with([
                'authors' => function ($authorQuery) {
                    $authorQuery->whereNull('authors.deleted_at');
                },
                'documentType',
                'category',
                'studyProgram',
            ])
            ->latest()
            ->paginate(12);

        $canonical = route('public.faculties.show', $faculty);
        $title = Seo::title(['Fakultas '.$faculty->name]);
        $description = Seo::description('Repository Fakultas '.$faculty->name.': daftar program studi dan dokumen terbit (judul, abstrak, penulis, tahun, dan PDF).');

        $jsonLd = [
            [
                '@context' => 'https://schema.org',
                '@type' => 'CollegeOrUniversity',
                'name' => 'Fakultas '.$faculty->name,
                'url' => $canonical,
                'inLanguage' => 'id',
                'parentOrganization' => [
                    '@type' => 'Organization',
                    'name' => (string) config('app.name'),
                ],
            ],
        ];

        return view('public.faculties.show', [
            'faculty' => $faculty,
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
