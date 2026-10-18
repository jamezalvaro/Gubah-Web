<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class CheckPremiumRole
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check() && in_array(Auth::user()->role, ['premium', 'superadmin'])) {
            return $next($request);
        }

        return redirect()->route('dashboard')->with('error', 'Fitur Kalkulasi HPP dan Strategi Bisnis terkunci. Silakan upgrade ke Premium.');
    }
}