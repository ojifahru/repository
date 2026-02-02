<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Categories;
use App\Models\TriDharma;
use App\Support\Seo\Seo;
use Illuminate\Contracts\View\View;

class CategoryShowController extends Controller
{
    public function __invoke(Categories $category): View
    {
        $documents = TriDharma::query()
            ->where('status', 'published')
            ->where('category_id', $category->getKey())
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

        $canonical = route('public.categories.show', $category);
        $title = Seo::title([$category->name, 'Kategori']);
        $description = Seo::description('Repository kategori "'.$category->name.'": daftar dokumen terbit beserta abstrak dan PDF yang dapat diakses publik.');

        $jsonLd = [
            [
                '@context' => 'https://schema.org',
                '@type' => 'CollectionPage',
                'name' => $category->name,
                'url' => $canonical,
                'inLanguage' => 'id',
            ],
        ];

        return view('public.categories.show', [
            'category' => $category,
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
