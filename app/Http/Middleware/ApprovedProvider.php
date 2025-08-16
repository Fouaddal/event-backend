<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class ApprovedProvider
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        // Check if user is authenticated
        if (!$user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        // For User model
        if ($user instanceof \App\Models\User) {
            // Admins can bypass approval
            if ($user->type === 'admin') {
                return $next($request);
            }

            // Check if provider is approved
           // Check if approved individual or company provider
if (
    $user->type === 'provider' &&
    in_array($user->provider_type, ['individual', 'company']) &&
    $user->is_approved === 1
) {
    return $next($request);
}

        }

        // For ProviderRequest model (if using separate table)
        if ($user instanceof \App\Models\ProviderRequest && $user->status === 'approved') {
            return $next($request);
        }

        return response()->json(['message' => 'Provider account not approved'], 403);
    }
}