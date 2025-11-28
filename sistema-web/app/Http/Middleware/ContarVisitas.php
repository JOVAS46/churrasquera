<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\ContadorVisitas;
use Symfony\Component\HttpFoundation\Response;

class ContarVisitas
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Solo contar visitas para métodos GET y rutas que no sean API o AJAX
        if ($request->method() === 'GET' && 
            !$request->expectsJson() && 
            !$request->ajax() &&
            !str_starts_with($request->path(), 'api/')) {
            
            $pagina = $request->path() === '/' ? '/home' : $request->path();
            ContadorVisitas::incrementar($pagina);
        }

        return $response;
    }
}