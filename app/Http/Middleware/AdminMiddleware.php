<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user()?->role !== 'adm') {
            return response()->json(['error' => 'Acesso não autorizado.'], 403);
        }

        return $next($request);
    }
}
