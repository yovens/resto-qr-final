<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // Si itilizatè a pa konekte
        if (!auth()->check()) {
            return redirect('/login');
        }

        $userRole = auth()->user()->role;

        // Si wòl itilizatè a nan lis wòl otorize yo
        if (in_array($userRole, $roles)) {
            return $next($request);
        }

        // Si l pa gen aksè, voye l kote l dwe ale (pa egzanp, dashboard li oswa anèks)
        return abort(403, "Otorizasyon refize. Ou pa gen aksè nan paj sa a.");
    }
}
