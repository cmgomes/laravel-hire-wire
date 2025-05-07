<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PreventInertiaOnApi
{
    /**
     * Remove o cabeçalho x-inertia de todas as respostas oriundas de endpoints do grupo "/api"
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);
        if ($request->is('api/*')) {
            $response->headers->remove('X-Inertia');
            $response->headers->set('X-Inertia', 'false');
        }
        return $response;
    }
}
