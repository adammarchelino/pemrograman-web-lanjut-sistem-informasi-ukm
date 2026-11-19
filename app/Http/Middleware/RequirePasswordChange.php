<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RequirePasswordChange
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user()?->wajib_ganti_password && ! $request->routeIs('account.password.change')) {
            return redirect()->route('account.password.change');
        }

        return $next($request);
    }
}
