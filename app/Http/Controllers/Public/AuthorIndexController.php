<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Author;
use App\Support\Seo\Seo;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AuthorIndexController extends Controller
{
    public function __invoke(Request $request): View
    {
        $search = $request->string('q')->trim()->value();

        $authors = Author::query()
            ->whereNotNull('slug')
            ->whereHas('triDharmas', function ($query): void {
                $query
                    ->where('status', 'published')
                    ->whereNull('tri_dharmas.deleted_at');
            })
            ->when(is_string($search) && $search !== '', function ($query) use ($search): void {
                $query->where(function ($builder) use ($search): void {
                    $builder
                        ->where('name', 'like', "%{$search}%")
                        ->orWhere('identifier', 'like', "%{$search}%");
                });
            })
            ->withCount([
                'triDharmas as published_documents_count' => function ($query): void {
                    $query
                        ->where('status', 'published')
                        ->whereNull('tri_dharmas.deleted_at');
                },
            ])
            ->orderBy('name')
            ->paginate(30)
            ->withQueryString();

        $canonical = route('public.authors.index');
        $titleParts = ['Penulis'];
        if (is_string($search) && $search !== '') {
            $titleParts[] = 'Cari: '.Str::limit($search, 30);
        }

        $title = Seo::title($titleParts);
        $description = Seo::description('Daftar penulis di repository institusi. Telusuri profil penulis dan publikasi terbit beserta PDF yang dapat diakses publik.');

        $jsonLd = [
            [
                '@context' => 'https://schema.org',
                '@type' => 'CollectionPage',
                'name' => $title,
                'url' => $canonical,
                'inLanguage' => 'id',
            ],
        ];

        return view('public.authors.index', [
            'authors' => $authors,
            'search' => $search,
            'seo' => [
                'title' => $title,
                'description' => $description,
                'canonical' => $canonical,
                'robots' => (is_string($search) && $search !== '') ? 'noindex, follow' : null,
                'jsonLd' => $jsonLd,
            ],
        ]);
    }
}
