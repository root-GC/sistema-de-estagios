<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle($request, Closure $next, $roles)
{
    if (!$request->user()) {
        return response()->json(['erro' => 'Não autenticado'], 401);
    }

    $rolesArray = explode(',', $roles);

    if (!in_array($request->user()->role, $rolesArray)) {
        return response()->json(['erro' => 'Acesso negado'], 403);
    }

    return $next($request);
}
}
