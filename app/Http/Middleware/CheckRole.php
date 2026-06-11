<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class CheckRole
{
    public function handle($request, Closure $next, ...$roles)
    {
        if (!Auth::check()) {
            return redirect('/login');
        }

        $user   = Auth::user();
        $perfil = trim((string) optional($user->perfil)->descripcion);

        // Soporta 'role:Admin|Usuario externo' y múltiples parámetros
        $allowed = collect($roles)
            ->flatMap(fn ($r) => explode('|', (string) $r))
            ->map(fn ($r) => trim($r))
            ->filter()
            ->all();

        // Comparación case-insensitive y con trim
        $ok = false;
        foreach ($allowed as $r) {
            if (strcasecmp($perfil, $r) === 0) {
                $ok = true; break;
            }
        }

        if (!$ok) {
            // No cierres sesión ni borres cookies; devuelve 403 o redirige
            abort(403, 'Acceso denegado.');
            // o: return redirect('/')->withErrors('Acceso denegado.');
        }

        return $next($request);
    }
}
