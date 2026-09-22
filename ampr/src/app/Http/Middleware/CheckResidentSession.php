<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckResidentSession
{
    public function handle(Request $request, Closure $next): Response
    {
        // Cek apakah session penghuni ada?
        if (! session()->has('resident_unit_id')) {
            // Kalau tidak ada, tendang ke login
            return to_route('login');
        }

        return $next($request);
    }
}
