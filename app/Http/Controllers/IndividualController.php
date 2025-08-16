<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Gallery;
use App\Models\Event;
use App\Models\CompanyStatistics;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use App\Models\EventRequest;
use Illuminate\Support\Facades\Auth;
use App\Models\Offer;




class IndividualController extends Controller
{

    
    // Get individual profile
    public function show($id)
    {
        try {
            /** @var User $individual */
            $individual = User::where('id', $id)
                ->where('type', 'provider')
                ->where('provider_type', 'individual')
                ->firstOrFail();

            return response()->json([
                'individual' => $individual->makeHidden(['password', 'remember_token'])
            ]);

        } catch (\Exception $e) {
            return response()->json(['message' => 'provider not found'], 404);
        }
    }

    // Update individual profile
    public function update(Request $request, $id)
    {
        try {
            /** @var User $authUser */
            $authUser = auth()->user();
            
            if ($authUser->id != $id && !$authUser->isAdmin()) {
                return response()->json(['message' => 'Unauthorized'], 403);
            }

            /** @var User $individual */
            $individual = User::where('id', $id)
                ->where('type', 'provider')
                ->where('provider_type', 'individual')
                ->firstOrFail();

            $validator = Validator::make($request->all(), [
                'name' => 'sometimes|string|max:255',
                'email' => 'sometimes|string|max:255',
                'description' => 'sometimes|string|max:1000',
                'services' => 'sometimes|array',
                'services.*' => 'string|max:100',
                'profile_image' => 'sometimes|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
             
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $data = $validator->validated();

            if ($request->hasFile('profile_image')) {
                if ($individual->profile_image) {
                    Storage::delete('public/'.$individual->profile_image);
                }
                $path = $request->file('profile_image')
                    ->store('individual/profile_images', 'public');
                $data['profile_image'] = $path;
            }

            $individual->update($data);

            return response()->json([
                'message' => 'Individual updated successfully',
                'individual' => $individual->fresh()->makeHidden(['password', 'remember_token'])
            ]);

        } catch (\Exception $e) {
            Log::error('Individual update failed: '.$e->getMessage());
            return response()->json(['message' => 'Update failed'], 500);
        }
    }

    // Gallery management
    public function addToGallery(Request $request)
    {
        try {
            /** @var User $individual */
            $individual = auth()->user();
            
            if (!$individual->isIndividual()) {
                return response()->json(['message' => 'Unauthorized'], 403);
            }

            $validator = Validator::make($request->all(), [
                'images' => 'required|array|max:10',
                'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048'
            ]);

            if ($validator->fails()) {
                return response()->json($validator->errors(), 422);
            }

            $images = [];
            foreach ($request->file('images') as $image) {
                $path = $image->store('individual_gallery', 'public');
                $images[] = $individual->gallery()->create([
                    'image_path' => $path,
                    'user_id' => $individual->id
                ]);
            }

            return response()->json([
                'message' => 'Images added successfully',
                'images' => $images
            ]);

        } catch (\Exception $e) {
            Log::error('Gallery upload failed: '.$e->getMessage());
            return response()->json(['message' => 'Upload failed'], 500);
        }
    }

    public function removeFromGallery($imageId)
    {
        try {
            /** @var User $individual */
            $individual = auth()->user();
            
            if (!$individual->isIndividual()) {
                return response()->json(['message' => 'Unauthorized'], 403);
            }

            $image = $individual->gallery()
                ->where('id', $imageId)
                ->firstOrFail();

            Storage::delete('public/'.$image->image_path);
            $image->delete();

            return response()->json([
                'message' => 'Image deleted successfully',
                'deleted_id' => $imageId
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['message' => 'Image not found'], 404);
        } catch (\Exception $e) {
            Log::error('Image deletion failed: '.$e->getMessage());
            return response()->json(['message' => 'Deletion failed'], 500);
        }
    }

    // offer management
  // Update an offer


public function updateOffer(Request $request, Offer $offer)
{
    // Check ownership
    if ($offer->user_id !== auth()->id()) {
        return response()->json(['message' => 'Unauthorized'], 403);
    }

    $validated = $request->validate([
       'price' => 'sometimes|numeric|min:0',
    'description' => 'sometimes|string|max:1000',
    'image' => 'sometimes|image|mimes:jpeg,png,jpg,webp|max:2048',
    ]);

    // Handle image upload
    if ($request->hasFile('image')) {
        if ($offer->image) {
            Storage::disk('public')->delete($offer->image); // Use disk for clarity
        }

        $validated['image'] = $request->file('image')->store('offers', 'public');
    }

    $offer->update($validated);

    return response()->json([
        'message' => 'Offer updated',
        'offer' => $offer
    ]);
}

// Delete an offer
public function deleteOffer(Offer $offer)
{
    // Check ownership
    if ($offer->user_id !== auth()->id()) {
        return response()->json(['message' => 'Unauthorized'], 403);
    }

    // Delete image if exists
    if ($offer->image) {
        Storage::delete('public/' . $offer->image);
    }

    $offer->delete();

    return response()->json(['message' => 'Offer deleted']);
}


public function createOffer(Request $request)
{
    
        /** @var User $individual */
        $individual = auth()->user();
        
        if (!$individual->isIndividual()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validator = Validator::make($request->all(), [
            'price' => 'required|numeric|min:0',
            'description' => 'required|string|max:1000',
            'image' => 'sometimes|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $data = $validator->validated();
        $data['user_id'] = $individual->id;

        if ($request->hasFile('image')) {
        $data['image'] = $request->file('image')->store('offers', 'public');
    }

    $offer = Offer::create($data);

    return response()->json(['message' => 'Offer created', 'offer' => $offer]);

}


    // Statistics
  public function getStatistics()
{
    try {
         /** @var User $individual */
        $individual = Auth::user();

        if (!$individual || !$individual->isIndividual()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $statistics = $individual->individualStatistics()->firstOrCreate([]);

        $offers = $individual->offers()->with(['reservations', 'reviews'])->get();

        $reservationsCount = $offers->sum(fn($offer) => $offer->reservations->count());
        $reviewsCount = $offers->sum(fn($offer) => $offer->reviews->count());
        $averageRating = round(
            $offers->flatMap->reviews->avg('rating') ?? 0,
            1
        );

        $statistics->update([
            'reservations_count' => $reservationsCount,
            'reviews_count' => $reviewsCount,
            'average_rating' => $averageRating,
        ]);

        return response()->json([
            'reservations' => $reservationsCount,
            'reviews' => $reviewsCount,
            'average_rating' => $averageRating,
        ]);

    } catch (\Exception $e) {
        Log::error('Error in getStatistics: ' . $e->getMessage());
        return response()->json(['message' => 'Could not get statistics'], 500);
    }
}


   
public function index()
{
    $offers = Offer::where('user_id', Auth::id())->get();
    return response()->json($offers);
}

public function showOffer(Offer $offer)
{
    // Check ownership
    if ($offer->user_id !== Auth::id()) {
        return response()->json(['message' => 'Unauthorized'], 403);
    }

    return response()->json($offer);
}



public function destroy(Offer $offer)
{
    // Check ownership
    if ($offer->user_id !== Auth::id()) {
        return response()->json(['message' => 'Unauthorized'], 403);
    }

    $offer->delete();

    return response()->json(['message' => 'Event deleted']);
}

}