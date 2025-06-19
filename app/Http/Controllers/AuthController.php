<?php

namespace App\Http\Controllers;

use App\Models\ProviderRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;


class AuthController extends Controller
{
 /* public function register(Request $request)
{
    $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:users,email|unique:provider_requests,email',
        'password' => 'required|min:6',
        'type' => 'required|in:user,provider',
        'otp' => 'required|digits:6',
        'provider_type' => 'required_if:type,provider|in:company,individual',
    ]);

    // Verify OTP
    $cachedOtp = Cache::get('otp_' . $request->email);
    if (!$cachedOtp || $cachedOtp != $request->otp) {
        return response()->json(['message' => 'Invalid or expired OTP'], 400);
    }

    if ($request->type === 'user') {
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'type' => 'user',
            'is_approved' => true,
        ]);

        Cache::forget('otp_' . $request->email);
        
        return response()->json([
            'message' => 'Registration complete',
            'token' => $user->createToken('auth_token')->plainTextToken,
            'user' => $user,
        ]);
    }

    // Provider registration
    $providerRequest = ProviderRequest::create([
        'name' => $request->name,
        'email' => $request->email,
        'password' => Hash::make($request->password),
        'provider_type' => $request->provider_type,
        'otp' => $request->otp,
        'status' => 'pending',
        'otp_verified' => true,
        'email_verified_at' => now(),
    ]);

    Cache::forget('otp_' . $request->email);

    return response()->json([
        'message' => 'Provider request submitted. Please wait for admin approval.',
        'status' => 'pending',
    ]);
}*/

public function registerUser(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email|unique:provider_requests,email',
            'password' => 'required|confirmed|min:6',
            'otp' => 'required|digits:6',
        ]);

        $cachedOtp = Cache::get('otp_' . $request->email);
        if (!$cachedOtp || $cachedOtp != $request->otp) {
            return response()->json(['message' => 'Invalid or expired OTP'], 400);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'type' => 'user',
            'is_approved' => true,
        ]);

        Cache::forget('otp_' . $request->email);

        return response()->json([
            'message' => 'Registration complete',
            'token' => $user->createToken('auth_token')->plainTextToken,
            'user' => $user,
        ]);
    }


public function registerProvider(Request $request)
{
    try {
        Log::info('Provider registration attempt', ['email' => $request->email]);
        
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:provider_requests,email|unique:users,email',
            'password' => 'required|confirmed|min:6',
            'otp' => 'required|digits:6',
            'services' => 'required|array|min:1',
        ]);
        
        Log::debug('Validation passed', ['email' => $request->email]);

        $cachedOtp = Cache::get('otp_' . $request->email);
        if (!$cachedOtp || $cachedOtp != $request->otp) {
            Log::warning('Invalid OTP attempt', [
                'email' => $request->email,
                'provided_otp' => $request->otp,
                'cached_otp' => $cachedOtp
            ]);
            return response()->json(['message' => 'Invalid or expired OTP'], 400);
        }

        $providerRequest = ProviderRequest::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'provider_type' => 'individual',
            'otp' => $request->otp,
            'status' => 'pending',
            'otp_verified' => true,
            'email_verified_at' => now(),
            'services' => json_encode($request->services),
            'specializations' => null
        ]);

        Cache::forget('otp_' . $request->email);

        $token = $providerRequest->createToken('auth_token')->plainTextToken;

        Log::info('Provider registration successful', ['email' => $request->email]);

        return response()->json([
            'message' => 'Provider request submitted. Please wait for admin approval.',
            'status' => 'pending',
            'token' => $token,
            'name' => $providerRequest->name,
            'email' => $providerRequest->email,
        ]);

    } catch (\Exception $e) {
        Log::error('Provider registration failed', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
            'request' => $request->all()
        ]);
        return response()->json(['message' => 'Registration failed. Please try again.'], 500);
    }
}

  public function registerCompany(Request $request)
{
    $request->validate([
        'company_name' => 'required|string|max:255',
        'email' => 'required|email|unique:provider_requests,email|unique:users,email',
        'password' => 'required|confirmed|min:6',
        'otp' => 'required|digits:6',
        'specializations' => 'required|array|min:1',
        'specializations.*' => 'string',
    ]);

    $cachedOtp = Cache::get('otp_' . $request->email);
    if (!$cachedOtp || $cachedOtp != $request->otp) {
        return response()->json(['message' => 'Invalid or expired OTP'], 400);
    }

    $providerRequest = ProviderRequest::create([
        'name' => $request->company_name,
        'email' => $request->email,
        'password' => Hash::make($request->password),
        'provider_type' => 'company',
        'otp' => $request->otp,
        'status' => 'pending',
        'otp_verified' => true,
        'email_verified_at' => now(),
        'specializations' => $request->specializations, // No need for json_encode if using $casts
         'services' => null,
    ]);

    Cache::forget('otp_' . $request->email);

    $token = $providerRequest->createToken('auth_token')->plainTextToken;

    return response()->json([
        'message' => 'Company request submitted. Please wait for admin approval.',
        'status' => 'pending',
        'token' => $token,
        'name' => $providerRequest->name,
        'email' => $providerRequest->email,
        'specializations' => $providerRequest->specializations,
    ]);
}



public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Login successful',
            'token' => $token,
            'name' => $user->name,
        ]);
    }

    // Step 4: Logout
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Logged out']);
    }

    // Step 5: Check approval status
public function checkApprovalStatus(Request $request)
{
    $request->validate(['email' => 'required|email']);

    $email = strtolower(trim($request->email));

    // 1. Check if user exists (approved provider)
    $user = User::where('email', $email)->first();
    if ($user) {
        return response()->json([
            'status' => $user->is_approved ? 'approved' : 'pending',
            'name' => $user->name,
            'type' => $user->type,
        ]);
    }

    // 2. Check provider request status
    $providerRequest = ProviderRequest::where('email', $email)->first();
    if ($providerRequest) {
        return response()->json([
            'status' => $providerRequest->status,
            'name' => $providerRequest->name,
            'type' => 'provider',
        ]);
    }

    // 3. Not found in any table
    return response()->json([
        'status' => 'not_found',
        'message' => 'Registration not found',
    ], 404);
}



/*public function sendOtp(Request $request)
{
    $request->validate([
        'email' => 'required|email',
    ]);

    $otp = rand(1000, 9999);
    Cache::put('otp_' . $request->email, $otp, now()->addMinutes(10));

    Mail::raw("Your OTP is: $otp", function ($message) use ($request) {
        $message->to($request->email)->subject('OTP Verification');
    });

    return response()->json(['message' => 'OTP sent to your email']);
}*/

public function sendOtp(Request $request)
{
    $request->validate([
        'email' => 'required|email',
    ]);

    $otp = '123456';  // Constant OTP for testing
    Cache::put('otp_' . $request->email, $otp, now()->addMinutes(10));

    return response()->json(['message' => 'OTP set to 1234 for testing']);
}





}