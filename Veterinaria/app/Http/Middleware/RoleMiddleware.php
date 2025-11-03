<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;


class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // Si no está autenticado, redirige a login
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        // Obtiene el rol del usuario de forma segura
        $user = Auth::user();
        $userRole = data_get($user, 'tipo_permiso');

        // Si el usuario no tiene rol asignado, niega acceso de forma controlada
        if ($userRole === null || $userRole === '') {
            abort(403, 'Tu usuario no tiene un rol asignado. Contacta al administrador.');
        }

        // Normaliza comparación a minúsculas para evitar problemas de mayúsculas/minúsculas
        $normalizedUserRole = strtolower((string) $userRole);
        $normalizedAllowed = array_map(static function ($r) {
            return strtolower((string) $r);
        }, $roles ?? []);

        if (!empty($normalizedAllowed) && !in_array($normalizedUserRole, $normalizedAllowed, true)) {
            abort(403, 'No tienes permiso para acceder a esta sección.');
        }

        return $next($request);
    }
}
