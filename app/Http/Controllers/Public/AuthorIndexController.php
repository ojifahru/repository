<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Author;
use App\Support\Seo\Seo;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Throwable;

class AuthorIndexController extends Controller
{
    public function __invoke(Request $request): View
    {
        $search = $request->string('q')->trim()->value();

        $baseQuery = Author::query()
            ->whereNotNull('slug')
            ->whereHas('triDharmas', function ($query): void {
                $query
                    ->where('status', 'published')
                    ->whereNull('tri_dharmas.deleted_at');
            })
            ->withCount([
                'triDharmas as published_documents_count' => function ($query): void {
                    $query
                        ->where('status', 'published')
                        ->whereNull('tri_dharmas.deleted_at');
                },
            ]);

        if (is_string($search) && $search !== '') {
            try {
                // 🔥 SEARCH via Meilisearch (Scout)
                $authors = Author::search($search)
                    ->query(fn($query) => $query
                        ->whereNotNull('slug')
                        ->whereHas('triDharmas', function ($query): void {
                            $query
                                ->where('status', 'published')
                                ->whereNull('tri_dharmas.deleted_at');
                        })
                        ->withCount([
                            'triDharmas as published_documents_count' => function ($query): void {
                                $query
                                    ->where('status', 'published')
                                    ->whereNull('tri_dharmas.deleted_at');
                            },
                        ]))
                    ->paginate(30)
                    ->withQueryString();

                if ($authors->total() === 0) {
                    $authors = (clone $baseQuery)
                        ->where(function ($query) use ($search): void {
                            $query
                                ->where('name', 'like', "%{$search}%")
                                ->orWhere('identifier', 'like', "%{$search}%");
                        })
                        ->orderBy('name')
                        ->paginate(30)
                        ->withQueryString();
                }
            } catch (Throwable) {
                // 🔁 Fallback when Meilisearch isn't reachable
                $authors = (clone $baseQuery)
                    ->where(function ($query) use ($search): void {
                        $query
                            ->where('name', 'like', "%{$search}%")
                            ->orWhere('identifier', 'like', "%{$search}%");
                    })
                    ->orderBy('name')
                    ->paginate(30)
                    ->withQueryString();
            }
        } else {
            // 📚 REGULAR LISTING via Database
            $authors = $baseQuery
                ->orderBy('name')
                ->paginate(30)
                ->withQueryString();
        }

        $canonical = route('public.authors.index');
        $titleParts = ['Daftar Penulis'];
        if (is_string($search) && $search !== '') {
            $titleParts = ['Cari Penulis', $search];
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
