<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Offer;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;


class UserPublicController extends Controller
{
    /**
     * Get all events
     */
    public function getAllEvents()
    {
        $events = Event::with('user') // Include event owner (company/provider)
            ->where('status', 'approved')
            ->orderBy('date_time', 'asc')
            ->get();

        return response()->json([
            'events' => $events
        ]);
    }

    /**
     * Get all offers
     */
    public function getAllOffers()
    {
        $offers = Offer::with('user') // Include provider/company
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'offers' => $offers
        ]);
    }

    /**
     * Get company details by ID
     */
    public function getCompanyById($companyId)
    {
        $company = User::where('id', $companyId)
            ->where('type', 'provider')
            ->where('provider_type', 'company')
            ->with(['gallery', 'statistics'])
            ->firstOrFail();

        return response()->json([
            'company' => $company
        ]);
    }

    /**
     * Get provider details by offer ID
     */
    public function getProviderByOfferId($offerId)
    {
        $offer = Offer::with('user')
            ->findOrFail($offerId);

        return response()->json([
            'provider' => $offer->user
        ]);
    }

    public function getMyPendingEvents()
{
    $userId = auth()->id();

    $events = \App\Models\UserEvent::with('providers')
        ->where('user_id', $userId)
        ->where('status', 'pending')
        ->get();

    return response()->json([
        'pending_events' => $events
    ]);
}


// Get normal user profile
public function show($id)
{
    try {
        /** @var User $user */
        $user = User::where('id', $id)
            ->where('type', 'user')
            ->whereNull('provider_type')
            ->firstOrFail();

        return response()->json([
            'user' => $user->makeHidden(['password', 'remember_token'])
        ]);

    } catch (\Exception $e) {
        return response()->json(['message' => 'User not found'], 404);
    }
}

// Update normal user profile
public function update(Request $request, $id)
{
    try {
        /** @var User $authUser */
        $authUser = auth()->user();
        
        // Authorization check
        if ($authUser->id != $id && !$authUser->isAdmin()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        /** @var User $user */
        $user = User::where('id', $id)
            ->where('type', 'user')
            ->whereNull('provider_type')
            ->firstOrFail();

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|max:255|unique:users,email,' . $user->id,
              'password' => 'sometimes|string|min:6|confirmed',
            'profile_image' => 'sometimes|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $validator->validated();

        // Handle profile image upload
        if ($request->hasFile('profile_image')) {
            if ($user->profile_image) {
                Storage::delete('public/'.$user->profile_image);
            }
            $path = $request->file('profile_image')
                ->store('user/profile_images', 'public');
            $data['profile_image'] = $path;
        }

        $user->update($data);

        return response()->json([
            'message' => 'User updated successfully',
            'user' => $user->fresh()->makeHidden(['password', 'remember_token'])
        ]);

    } catch (\Exception $e) {
        Log::error('User update failed: '.$e->getMessage());
        return response()->json(['message' => 'Update failed'], 500);
    }
}




}
