<?php

namespace App\Http\Middleware;

use Closure;

class CorsMiddleware
{
    public function handle($request, Closure $next)
    {
        // Menambahkan header CORS
        return $next($request)
            ->header('Access-Control-Allow-Origin', '*') // Ganti '*' dengan URL frontend Anda untuk produksi
            ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
            ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization');
    }
}