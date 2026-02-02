<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Categories;
use App\Support\Seo\Seo;
use Illuminate\Contracts\View\View;

class CategoryIndexController extends Controller
{
    public function __invoke(): View
    {
        $categories = Categories::query()
            ->whereNotNull('slug')
            ->withCount([
                'triDharmas as published_documents_count' => function ($query): void {
                    $query->where('status', 'published');
                },
            ])
            ->orderBy('name')
            ->get();

        $canonical = route('public.categories.index');
        $title = Seo::title(['Kategori']);
        $description = Seo::description('Telusuri kategori dokumen repository untuk mempermudah pencarian topik dan bidang.');

        $jsonLd = [
            [
                '@context' => 'https://schema.org',
                '@type' => 'CollectionPage',
                'name' => $title,
                'url' => $canonical,
                'inLanguage' => 'id',
            ],
        ];

        return view('public.categories.index', [
            'categories' => $categories,
            'seo' => [
                'title' => $title,
                'description' => $description,
                'canonical' => $canonical,
                'jsonLd' => $jsonLd,
            ],
        ]);
    }
}
