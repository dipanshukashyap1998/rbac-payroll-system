<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAdminHasCompany
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && $user->isAdmin() && ! $user->ownedCompany()->exists()) {
            if (! $request->routeIs('companies.create') && ! $request->routeIs('companies.store') && ! $request->routeIs('logout')) {
                return redirect()->route('companies.create')
                    ->with('status', 'Create your company first to continue.');
            }
        }

        return $next($request);
    }
}
