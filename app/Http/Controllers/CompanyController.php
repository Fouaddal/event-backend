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





class CompanyController extends Controller
{

    
    // Get company profile
    public function show($id)
    {
        try {
            /** @var User $company */
            $company = User::where('id', $id)
                ->where('type', 'provider')
                ->where('provider_type', 'company')
                ->firstOrFail();

            return response()->json([
                'company' => $company->makeHidden(['password', 'remember_token'])
            ]);

        } catch (\Exception $e) {
            return response()->json(['message' => 'Company not found'], 404);
        }
    }

    // Update company profile
    public function update(Request $request, $id)
    {
        try {
            /** @var User $authUser */
            $authUser = auth()->user();
            
            if ($authUser->id != $id && !$authUser->isAdmin()) {
                return response()->json(['message' => 'Unauthorized'], 403);
            }

            /** @var User $company */
            $company = User::where('id', $id)
                ->where('type', 'provider')
                ->where('provider_type', 'company')
                ->firstOrFail();

            $validator = Validator::make($request->all(), [
                'name' => 'sometimes|string|max:255',
                'email' => 'sometimes|string|max:255',
                'description' => 'sometimes|string|max:1000',
                'specializations' => 'sometimes|array',
                'specializations.*' => 'string|max:100',
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
                if ($company->profile_image) {
                    Storage::delete('public/'.$company->profile_image);
                }
                $path = $request->file('profile_image')
                    ->store('company/profile_images', 'public');
                $data['profile_image'] = $path;
            }

            $company->update($data);

            return response()->json([
                'message' => 'Company updated successfully',
                'company' => $company->fresh()->makeHidden(['password', 'remember_token'])
            ]);

        } catch (\Exception $e) {
            Log::error('Company update failed: '.$e->getMessage());
            return response()->json(['message' => 'Update failed'], 500);
        }
    }

    // Gallery management
    public function addToGallery(Request $request)
    {
        try {
            /** @var User $company */
            $company = auth()->user();
            
            if (!$company->isCompany()) {
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
                $path = $image->store('company_gallery', 'public');
                $images[] = $company->gallery()->create([
                    'image_path' => $path,
                    'company_id' => $company->id
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
            /** @var User $company */
            $company = auth()->user();
            
            if (!$company->isCompany()) {
                return response()->json(['message' => 'Unauthorized'], 403);
            }

            $image = $company->gallery()
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

    // Event management
  

public function createEvent(Request $request)
{
    try {
        /** @var User $company */
        $company = auth()->user();
        
        if (!$company->isCompany()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'date_time' => 'required|date|after:now',
            'location' => 'required|string',
            'image' => 'sometimes|image|mimes:jpeg,png,jpg,gif|max:2048',
            'ticket_price' => 'required|numeric|min:0',
            'capacity' => 'required|integer|min:1',
            'category' => 'required|string|max:100'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $data = $validator->validated();
        $data['user_id'] = $company->id;

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('event_request_images', 'public');
            $data['image'] = $path;
        }

        $eventRequest = EventRequest::create($data);

        return response()->json([
            'message' => 'Event request submitted successfully. Awaiting admin approval.',
            'request' => $eventRequest
        ]);

    } catch (\Exception $e) {
        Log::error('Event request failed: '.$e->getMessage());
        return response()->json(['message' => 'Event request failed'], 500);
    }
}


    // Statistics
    public function getStatistics()
    {
        try {
            /** @var User $company */
            $company = auth()->user();
            
            if (!$company->isCompany()) {
                return response()->json(['message' => 'Unauthorized'], 403);
            }

            $statistics = $company->statistics()->firstOrCreate([]);

            // Calculate real-time stats
            $calculatedStats = [
                'reservations_count' => $company->events()->withCount('reservations')->get()->sum('reservations_count'),
                'reviews_count' => $company->events()->withCount('reviews')->get()->sum('reviews_count'),
                'average_rating' => round($company->events()->withAvg('reviews', 'rating')->get()->avg('reviews_avg_rating') ?? 0, 1),
                'total_revenue' => $company->events()->withSum('reservations', 'total_price')->get()->sum('reservations_sum_total_price') ?? 0
            ];

            $statistics->update($calculatedStats);

            return response()->json([
                'statistics' => $calculatedStats
            ]);

        } catch (\Exception $e) {
            Log::error('Statistics fetch failed: '.$e->getMessage());
            return response()->json(['message' => 'Failed to get statistics'], 500);
        }
    }


   
public function index()
{
    $events = Event::where('user_id', Auth::id())->get();
    return response()->json($events);
}

public function showevent(Event $event)
{
    // Check ownership
    if ($event->user_id !== Auth::id()) {
        return response()->json(['message' => 'Unauthorized'], 403);
    }

    return response()->json($event);
}

public function updateevent(Request $request, Event $event)
{
    // Check ownership
    if ($event->user_id !== Auth::id()) {
        return response()->json(['message' => 'Unauthorized'], 403);
    }

    $validated = $request->validate([
        'title' => 'sometimes|string|max:255',
        'description' => 'sometimes|string',
        'date_time' => 'sometimes|date',
        'location' => 'sometimes|string|max:255',
        'image' => 'sometimes|string',
        'ticket_price' => 'sometimes|numeric',
        'capacity' => 'sometimes|integer',
        'category' => 'sometimes|string|max:255',
    ]);

    $event->update($validated);

    return response()->json(['message' => 'Event updated', 'event' => $event]);
}

public function destroy(Event $event)
{
    // Check ownership
    if ($event->user_id !== Auth::id()) {
        return response()->json(['message' => 'Unauthorized'], 403);
    }

    $event->delete();

    return response()->json(['message' => 'Event deleted']);
}

}