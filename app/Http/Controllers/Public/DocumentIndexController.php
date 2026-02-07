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

        $search = $validated['q'] ?? null;
        $search = is_string($search) ? trim($search) : null;

        $filters = $validated;
        if (is_string($search)) {
            $filters['q'] = $search;
        }

        $relations = [
            'authors' => function ($authorQuery) {
                $authorQuery
                    ->whereNull('authors.deleted_at');
            },
            'category',
            'documentType',
            'faculty',
            'studyProgram',
        ];

        $useMeilisearch = is_string($search)
            && $search !== ''
            && config('scout.driver') === 'meilisearch';

        if ($useMeilisearch) {
            $searchBuilder = TriDharma::search($search)
                ->where('status', 'published');

            if (isset($filters['author_id'])) {
                $searchBuilder->where('author_ids', (int) $filters['author_id']);
            }

            if (isset($filters['faculty_id'])) {
                $searchBuilder->where('faculty_id', (int) $filters['faculty_id']);
            }

            if (isset($filters['study_program_id'])) {
                $searchBuilder->where('study_program_id', (int) $filters['study_program_id']);
            }

            if (isset($filters['document_type_id'])) {
                $searchBuilder->where('document_type_id', (int) $filters['document_type_id']);
            }

            if (isset($filters['category_id'])) {
                $searchBuilder->where('category_id', (int) $filters['category_id']);
            }

            if (isset($filters['publish_year'])) {
                $searchBuilder->where('publish_year', (int) $filters['publish_year']);
            }

            $documents = $searchBuilder
                ->query(fn($query) => $query->with($relations))
                ->paginate(12)
                ->withQueryString();
        } else {
            $query = TriDharma::query()
                ->where('status', 'published')
                ->with($relations);

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

            if (isset($filters['author_id'])) {
                $query->whereHas('authors', function ($authorQuery) use ($filters) {
                    $authorQuery
                        ->whereNull('authors.deleted_at')
                        ->where('authors.id', $filters['author_id']);
                });
            }

            if (isset($filters['faculty_id'])) {
                $query->where('faculty_id', $filters['faculty_id']);
            }

            if (isset($filters['study_program_id'])) {
                $query->where('study_program_id', $filters['study_program_id']);
            }

            if (isset($filters['document_type_id'])) {
                $query->where('document_type_id', $filters['document_type_id']);
            }

            if (isset($filters['category_id'])) {
                $query->where('category_id', $filters['category_id']);
            }

            if (isset($filters['publish_year'])) {
                $query->where('publish_year', $filters['publish_year']);
            }

            $documents = $query
                ->orderBy('publish_year', 'desc')
                ->paginate(12)
                ->withQueryString();
        }

        $studyProgramsQuery = StudyProgram::query()->orderBy('name');
        if (! empty($filters['faculty_id'])) {
            $studyProgramsQuery->where('faculty_id', $filters['faculty_id']);
        }

        $studyProgramsAll = StudyProgram::query()
            ->orderBy('name')
            ->get(['id', 'name', 'faculty_id']);

        $filterOptions = [
            'authors' => Author::query()
                ->whereHas('triDharmas', function ($triDharmaQuery) {
                    $triDharmaQuery
                        ->where('tri_dharmas.status', 'published')
                        ->whereNull('tri_dharmas.deleted_at');
                })
                ->orderBy('name')
                ->get(['id', 'name']),
            'faculties' => Faculty::query()->orderBy('name')->get(['id', 'name']),
            'studyPrograms' => $studyProgramsQuery->get(['id', 'name']),
            'studyProgramsAll' => $studyProgramsAll,
            'categories' => Categories::query()->orderBy('name')->get(['id', 'name']),
            'documentTypes' => DocumentType::query()->orderBy('name')->get(['id', 'name']),
            'publishYears' => TriDharma::query()
                ->where('status', 'published')
                ->select('publish_year')
                ->distinct()
                ->orderByDesc('publish_year')
                ->pluck('publish_year'),
        ];

        $hasFilters = collect($filters)
            ->filter(fn($value) => $value !== null && $value !== '' && $value !== [])
            ->isNotEmpty();

        $titleParts = ['Dokumen Repository'];
        if (! empty($filters['publish_year'])) {
            $titleParts[] = (string) $filters['publish_year'];
        }
        if (! empty($filters['q'])) {
            $titleParts[] = 'Pencarian: ' . Str::limit((string) $filters['q'], 30);
        }

        $title = Seo::title($titleParts);

        $description = Seo::description('Telusuri dokumen repository kampus: judul, abstrak, penulis, tahun, dan filter akademik.');

        return view('public.documents.index', [
            'documents' => $documents,
            'filters' => $filters,
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
