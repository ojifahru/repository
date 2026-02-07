<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

class RobotsController extends Controller
{
    public function __invoke(Request $request): Response
    {
        $appUrl = rtrim((string) config('app.url'), '/');
        $sitemapUrl = $appUrl . '/sitemap.xml';

        $content = implode("\n", [
            'User-agent: *',
            'Disallow: /admin/',
            'Disallow: /api/',
            'Disallow: /filament/',
            'Disallow: /livewire/',
            '',
            'Sitemap: ' . $sitemapUrl,
            '',
        ]);

        return response($content, 200)
            ->header('Content-Type', 'text/plain; charset=UTF-8');
    }
}
