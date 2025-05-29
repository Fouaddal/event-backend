<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Support\Facades\Hash;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
      public function store(LoginRequest $request): RedirectResponse
{
    $credentials = $request->only('email', 'password');

    $user = \App\Models\User::where('email', $credentials['email'])->first();

   if (!$user || !Hash::check($credentials['password'], $user->password)){
        return back()->withErrors([
            'email' => 'The provided credentials are incorrect.',
        ]);
    }

    if ($user->type !== 'admin') {
        return back()->withErrors([
            'email' => 'Access denied. Only admins are allowed.',
        ]);
    }

    Auth::login($user);
    $request->session()->regenerate();

    return redirect('/dashboard');
}



    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
