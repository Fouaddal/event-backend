<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserEventController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\InvitationController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Models\ProviderRequest;  // Adjust to your actual model namespace
use Faker\Provider\ar_EG\Company;

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
Route::middleware(['auth:sanctum', 'approved.provider'])->group(function () {
    
    // Auth
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [UserController::class, 'me']);

    // Events
    Route::get('/events', [UserEventController::class, 'index']);
    Route::post('/events', [UserEventController::class, 'store']);

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

Route::post('/register-admin', function (Request $request) {
    $request->validate([
        'name' => 'required|string',
        'email' => 'required|email|unique:users',
        'password' => 'required|min:6',
    ]);

    $admin = User::create([
        'name' => $request->name,
        'email' => $request->email,
        'password' => Hash::make($request->password),
        'type' => 'admin',
        'is_approved' => true,
    ]);

    return response()->json([
        'message' => 'Admin created (FOR TESTING ONLY!)',
        'token' => $admin->createToken('auth_token')->plainTextToken,
    ]);
});

/*Route::middleware(['auth:sanctum', 'is_admin'])->group(function () {
    Route::get('/admin/provider-requests', [AdminController::class, 'listProviderRequests']);
    Route::post('/admin/provider-requests/{id}/approve', [AdminController::class, 'approveProviderRequest']);
    Route::post('/admin/provider-requests/{id}/reject', [AdminController::class, 'rejectProviderRequest']);
});
*/


Route::middleware('auth:sanctum')->get('/notifications', function () {
    return auth()->user()->notifications;
});




Route::middleware(['auth:sanctum'])->group(function () {
    // Company Profile
    Route::get('/company/profile/{id}', [CompanyController::class, 'show']);
    Route::put('/company/profile/{id}', [CompanyController::class, 'update']);
    
    // Gallery
    Route::post('/company/gallery/{id}', [CompanyController::class, 'addToGallery']);
  Route::delete('/company/gallery/{imageId}', [CompanyController::class, 'removeFromGallery'])
    ->middleware(['auth:sanctum']);
    // Events
   

// Statistics
    Route::get('/company/statistics', [CompanyController::class, 'getStatistics']);
});


// web.php or api.php route file
Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('/company/events', [CompanyController::class, 'createEvent']);
     Route::get('/company/events', [CompanyController::class, 'index']);
    Route::get('/company/events/{event}', [CompanyController::class, 'showevent']);
    Route::put('/company/events/{event}', [CompanyController::class, 'updateevent']);
    Route::delete('/company/events/{event}', [CompanyController::class, 'destroy']);

});
use App\Http\Controllers\IndividualController;

Route::middleware('auth:sanctum')->prefix('individual')->group(function () {
    // Profile
    Route::get('/{id}', [IndividualController::class, 'show']);        // View individual profile
    Route::put('/{id}', [IndividualController::class, 'update']);      // Update individual profile

    // Gallery
    Route::post('/gallery', [IndividualController::class, 'addToGallery']);  // Add images
    Route::delete('/gallery/{imageId}', [IndividualController::class, 'removeFromGallery']);  // Remove image

    // Offers
    ///Route::get('/offers', [IndividualController::class, 'index']);           // List offers of authenticated user
    Route::post('/offers', [IndividualController::class, 'createOffer']);    // Create offer
    Route::get('/offers/{offer}', [IndividualController::class, 'showOffer']); // Show offer detail
    //Route::put('/offers/{offer}', [IndividualController::class, 'updateOffer']); // Update offer
    Route::delete('/offers/{offer}', [IndividualController::class, 'deleteOffer']); // Delete offer

    // Statistics
    //Route::get('/statistics', [IndividualController::class, 'getStatistics']); // Get statistics
});



Route::middleware('auth:sanctum')->group(function () {
    Route::get('/events', [UserEventController::class, 'index']);
    Route::post('/events', [UserEventController::class, 'store']);
    Route::post('/events/{event}/respond', [UserEventController::class, 'respondToEvent']);
     Route::get('/provider/requests', [UserEventController::class, 'getProviderRequests']);
});

use App\Http\Controllers\UserPublicController;

Route::get('/all-events', [UserPublicController::class, 'getAllEvents']);
Route::get('/offers', [UserPublicController::class, 'getAllOffers']);
Route::get('/company/{id}', [UserPublicController::class, 'getCompanyById']);
Route::get('/offer/{id}', [UserPublicController::class, 'getProviderByOfferId']);


Route::middleware('auth:sanctum')->get('/my-pending-events', [UserPublicController::class, 'getMyPendingEvents']);


Route::middleware('auth:sanctum')->group(function () {
    Route::get('/users/{id}', [UserPublicController::class, 'show']);
    Route::put('/users/{id}', [UserPublicController::class, 'update']);
});


use App\Http\Controllers\TicketController;

Route::middleware('auth:sanctum')->group(function () {
    Route::post('events/{event}/buy-tickets', [TicketController::class, 'buyTickets']);
});


use App\Http\Controllers\EventHistoryController;

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/my-events/upcoming', [EventHistoryController::class, 'myUpcomingEvents']);
    Route::get('/my-events/past', [EventHistoryController::class, 'myPastEvents']);
});
