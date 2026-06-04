<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // Si no está autenticado, redirigir al login
        if (!$request->user()) {
            return redirect()->route('login');
        }

        $usuario = $request->user();
        
        // Verificar si tiene alguno de los roles requeridos
        foreach ($roles as $role) {
            if ($role === 'admin' && $usuario->isAdmin()) {
                return $next($request);
            }
            // Para verificar por nombre de rol
            if ($usuario->hasRoleByName($role)) {
                return $next($request);
            }
            // Para verificar por ID de rol
            if (is_numeric($role) && $usuario->hasRole($role)) {
                return $next($request);
            }
        }

        abort(403, 'No tienes permiso para acceder a esta página.');
    }
}
