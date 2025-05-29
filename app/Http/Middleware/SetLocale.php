<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    public function handle($request, \Closure $next)
{
    if (session()->has('locale')) {
        app()->setLocale(session('locale'));
    }

    return $next($request);
}
}
