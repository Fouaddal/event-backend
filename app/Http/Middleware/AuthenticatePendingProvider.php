<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Cache;
use App\Models\ProviderRequest;

class AuthenticatePendingProvider
{
    public function handle(Request $request, Closure $next)
    {
        $token = $request->bearerToken();

        if (!$token || !Cache::has('provider_token_' . $token)) {
            return response()->json(['message' => 'Unauthorized. Invalid token.'], 401);
        }

        $providerId = Cache::get('provider_token_' . $token);
        $provider = ProviderRequest::find($providerId);

        if (!$provider) {
            return response()->json(['message' => 'Unauthorized. Provider not found.'], 401);
        }

        // Attach provider to request
        $request->merge(['pending_provider' => $provider]);

        return $next($request);
    }
}
