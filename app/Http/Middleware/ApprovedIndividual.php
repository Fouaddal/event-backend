<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class ApprovedIndividual
{
   public function handle($request, Closure $next)
{
    /** @var \App\Models\User $user */
    $user = auth('sanctum')->user();

    if (!$user || !$user->isIndividual()) {
        return response()->json(['message' => 'Individual account not approved'], 403);
    }

    return $next($request);
}


}
