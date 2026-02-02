<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Http\Requests\Public\DocumentIndexRequest;
use App\Models\Author;
use App\Models\Categories;
use App\Models\DocumentType;
use App\Models\Faculty;
use App\Models\StudyProgram;
use App\Models\TriDharma;
use App\Support\Seo\Seo;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Str;

class DocumentIndexController extends Controller
{
    public function __invoke(DocumentIndexRequest $request): View
    {
        $validated = $request->validated();

        $query = TriDharma::query()
            ->where('status', 'published')
            ->with([
                'authors' => function ($authorQuery) {
                    $authorQuery->whereNull('authors.deleted_at');
                },
                'category',
                'documentType',
                'faculty',
                'studyProgram',
            ]);

        $search = $validated['q'] ?? null;
        if (is_string($search) && $search !== '') {
            $query->where(function ($builder) use ($search) {
                $builder
                    ->where('title', 'like', "%{$search}%")
                    ->orWhere('abstract', 'like', "%{$search}%")
                    ->orWhereHas('authors', function ($authorQuery) use ($search) {
                        $authorQuery
                            ->whereNull('authors.deleted_at')
                            ->where('name', 'like', "%{$search}%");
                    });
            });
        }

        if (isset($validated['author_id'])) {
            $query->whereHas('authors', function ($authorQuery) use ($validated) {
                $authorQuery
                    ->whereNull('authors.deleted_at')
                    ->where('authors.id', $validated['author_id']);
            });
        }

        if (isset($validated['faculty_id'])) {
            $query->where('faculty_id', $validated['faculty_id']);
        }

        if (isset($validated['study_program_id'])) {
            $query->where('study_program_id', $validated['study_program_id']);
        }

        if (isset($validated['document_type_id'])) {
            $query->where('document_type_id', $validated['document_type_id']);
        }

        if (isset($validated['category_id'])) {
            $query->where('category_id', $validated['category_id']);
        }

        if (isset($validated['publish_year'])) {
            $query->where('publish_year', $validated['publish_year']);
        }

        $documents = $query
            ->latest()
            ->paginate(12)
            ->withQueryString();

        $filterOptions = [
            'authors' => Author::query()->orderBy('name')->get(['id', 'name']),
            'faculties' => Faculty::query()->orderBy('name')->get(['id', 'name']),
            'studyPrograms' => StudyProgram::query()->orderBy('name')->get(['id', 'name']),
            'categories' => Categories::query()->orderBy('name')->get(['id', 'name']),
            'documentTypes' => DocumentType::query()->orderBy('name')->get(['id', 'name']),
            'publishYears' => TriDharma::query()
                ->where('status', 'published')
                ->select('publish_year')
                ->distinct()
                ->orderByDesc('publish_year')
                ->pluck('publish_year'),
        ];

        $hasFilters = collect($validated)
            ->filter(fn ($value) => $value !== null && $value !== '' && $value !== [])
            ->isNotEmpty();

        $titleParts = ['Dokumen Repository'];
        if (! empty($validated['publish_year'])) {
            $titleParts[] = (string) $validated['publish_year'];
        }
        if (! empty($validated['q'])) {
            $titleParts[] = 'Pencarian: '.Str::limit((string) $validated['q'], 30);
        }

        $title = Seo::title($titleParts);

        $description = Seo::description('Telusuri dokumen repository kampus: judul, abstrak, penulis, tahun, dan filter akademik.');

        return view('public.documents.index', [
            'documents' => $documents,
            'filters' => $validated,
            'filterOptions' => $filterOptions,
            'seo' => [
                'title' => $title,
                'description' => $description,
                'canonical' => route('public.documents.index'),
                'robots' => $hasFilters ? 'noindex, follow' : null,
            ],
        ]);
    }
}
