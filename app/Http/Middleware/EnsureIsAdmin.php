<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Restricts access to admin-only routes.
 * Redirects unauthenticated users to the admin login page,
 * and authenticated non-admins with a 403.
 */
class EnsureIsAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user()) {
            return redirect()->route('admin.login');
        }

        if (! $request->user()->isAdmin()) {
            abort(403, 'Acesso negado.');
        }

        return $next($request);
    }
}
