<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\InvitationController;
use App\Http\Controllers\PaymentController;
use Illuminate\Http\Request;

use App\Models\ProviderRequest;  // Adjust to your actual model namespace

Route::post('/check-approval-status', function (Request $request) {
    $request->validate([
        'email' => 'required|email',
    ]);

    $provider = ProviderRequest::where('email', $request->email)->first();

    if (!$provider) {
        return response()->json([
            'status' => 'error',
            'message' => 'Provider not found',
        ], 404);
    }

    return response()->json([
        'status' => strtolower($provider->status),  // Normalize status to lowercase
        'name' => $provider->name,
        'email' => $provider->email,
        'token' => $request->bearerToken(), // Optional, if you want to send it back
    ]);
});



// routes/api.php
//Route::post('/verify-email', [AuthController::class, 'verifyOtp']);
Route::post('/verify-otp', [AuthController::class, 'verifyOtp']);
//Route::post('/complete-user-registration', [AuthController::class, 'completeUserRegistration']);
Route::post('/check-approval-status', [AuthController::class, 'checkApprovalStatus']);

Route::middleware(['auth:sanctum', 'verified'])->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/send-otp', [AuthController::class, 'sendOtp']);
// Public routes
//Route::post('/register', [AuthController::class, 'register']);
Route::post('/register/user', [AuthController::class, 'registerUser']);
Route::post('/register/provider', [AuthController::class, 'registerProvider']);
Route::post('/register/company', [AuthController::class, 'registerCompany']);

Route::post('/login', [AuthController::class, 'login']);



Route::middleware('auth.pending')->get('/pending/dashboard', function (Request $request) {
    return response()->json([
        'message' => 'Hello, pending provider!',
        'provider_data' => $request->pending_provider,
    ]);
});


// Protected routes (requires Sanctum authentication)
Route::middleware('auth:sanctum')->group(function () {
    
    // Auth
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [UserController::class, 'me']);

    // Events
    Route::get('/events', [EventController::class, 'index']);
    Route::post('/events', [EventController::class, 'store']);

    // Services
    Route::get('/services', [ServiceController::class, 'index']);
   // Route::post('/services', [ServiceController::class, 'store']);
// routes/api.php
Route::middleware('auth:sanctum')->post('/services', [ServiceController::class, 'store']);

    // Invitations
    Route::post('/invitations', [InvitationController::class, 'store']);
});

Route::post('/payment', [PaymentController::class, 'charge']);


Route::get('/notifications', function () {
    return auth()->user()->notifications;
})->middleware('auth:sanctum');



/*Route::middleware(['auth:sanctum', 'is_admin'])->group(function () {
    Route::get('/admin/provider-requests', [AdminController::class, 'listProviderRequests']);
    Route::post('/admin/provider-requests/{id}/approve', [AdminController::class, 'approveProviderRequest']);
    Route::post('/admin/provider-requests/{id}/reject', [AdminController::class, 'rejectProviderRequest']);
});
*/

Route::middleware('auth:sanctum')->get('/notifications', function () {
    return auth()->user()->notifications;
});
