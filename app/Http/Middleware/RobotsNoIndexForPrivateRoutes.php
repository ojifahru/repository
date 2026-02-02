<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RobotsNoIndexForPrivateRoutes
{
    /**
     * @param  Closure(Request): Response  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if ($this->shouldNoIndex($request)) {
            $response->headers->set('X-Robots-Tag', 'noindex, nofollow');
        }

        return $response;
    }

    protected function shouldNoIndex(Request $request): bool
    {
        $path = '/'.ltrim($request->path(), '/');

        return $path === '/admin'
            || str_starts_with($path, '/admin/')
            || str_starts_with($path, '/api/')
            || str_starts_with($path, '/filament/')
            || str_starts_with($path, '/livewire/')
            || $path === '/login'
            || str_starts_with($path, '/login/')
            || $path === '/register'
            || str_starts_with($path, '/register/')
            || $path === '/dashboard'
            || str_starts_with($path, '/dashboard/');
    }
}
