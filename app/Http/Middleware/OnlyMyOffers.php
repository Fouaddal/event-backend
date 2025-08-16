<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class OnlyMyOffers
{
    public function handle(Request $request, Closure $next)
    {
        // Pass the provider ID to the request so controllers can filter offers
        $request->merge(['user_id' => auth()->id()]);

        return $next($request);
    }
}
