<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Contrôle d'accès par rôle.
 *   Route::middleware('role:admin,caissier')
 *
 * L'administrateur a accès à tout.
 * Corriger le rôle d'un compte :  php artisan user:role email@exemple.com admin
 */
class CheckRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (!$user) {
            return $request->expectsJson()
                ? response()->json(['message' => 'Session expirée, reconnectez-vous.'], 401)
                : redirect()->guest(route('login'));
        }

        $role = self::role($user);

        if ($role === 'admin' || in_array($role, array_map('strtolower', $roles), true)) {
            return $next($request);
        }

        if ($request->expectsJson()) {
            return response()->json(['message' => "Accès refusé pour le rôle « $role »."], 403);
        }

        abort(403, "Otorizasyon refize. Ou pa gen aksè nan paj sa a.");
    }

    /** Rôle normalisé de l'utilisateur. */
    public static function role($user): string
    {
        $role = strtolower(trim((string) ($user->role ?? '')));

        // Compte administrateur historique du projet
        if ($role === '' || $role === 'client') {
            if (strtolower((string) $user->email) === 'admin@resto.com') {
                return 'admin';
            }
        }

        return $role;
    }

    /** Page d'accueil selon le rôle (utilisée après la connexion). */
    public static function accueil($user): string
    {
        return match (self::role($user)) {
            'admin'                              => '/admin/dashboard',
            'cuisinier', 'cuisine', 'cuisiniere' => '/cuisine',
            'caissier', 'caissiere', 'serveur', 'serveuse' => '/caisse/dashboard',
            default                              => '/acces-refuse',
        };
    }
}
