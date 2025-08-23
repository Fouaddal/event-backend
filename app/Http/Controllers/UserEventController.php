<?php

namespace App\Http\Controllers;

use App\Models\UserEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
  use Stripe\Stripe;
use Stripe\PaymentIntent;

class UserEventController extends Controller
{

    /**
 * Delete an event created by the logged-in user
 */
public function destroy(UserEvent $event)
{
    // Ensure the logged-in user owns the event
    if ($event->user_id !== auth()->id()) {
        return response()->json([
            'status' => false,
            'message' => 'You are not authorized to delete this event.'
        ], 403);
    }

    $event->delete();

    return response()->json([
        'status' => true,
        'message' => 'Event deleted successfully.'
    ], 200);
}


    /**
 * Get all events created by the logged-in user
 */
public function getUserEvents()
{
    $userId = auth()->id();

    $events = UserEvent::with(['offers.user']) // only load offers + provider user
        ->where('user_id', $userId)
        ->get();

    return response()->json([
        'events' => $events
    ]);
}


    /**
     * List all events (with providers & services)
     */
    public function index()
    {
        return UserEvent::with('services', 'providers')->get();
    }

    /**
     * Create a new event and attach providers as pending
     */
 public function store(Request $request)
{
    $request->validate([
        'title' => 'required|string|max:255',
        'description' => 'nullable|string',
        'date' => 'required|date|after_or_equal:today',
        'time' => 'required|date_format:h:i A',
        'is_public' => 'required|in:private,public',
        'type' => 'required|in:Creative & Cultural,Social Celebrations,Music & Performance,Wellness & Lifestyle,Entertainment & Fun,Media & Content,Educational & Academic,Training & Development',
        'location' => 'required|string|max:255',
        'offers' => 'sometimes|array',
        'offers.*' => 'exists:offers,id',
        'price' => 'required_if:is_public,public|numeric|min:0'
    ]);

    $time = \Carbon\Carbon::createFromFormat('h:i A', $request->time)->format('H:i:s');

    // Create event with dynamic status
    $event = UserEvent::create([
        'user_id' => auth()->id(),
        'title' => $request->title,
        'description' => $request->description,
        'date' => $request->date,
        'time' => $time,
        'is_public' => $request->is_public === 'public',
        'price' => $request->is_public === 'public' ? $request->price : null,
        'type' => $request->type,
        'location' => $request->location,
        'invitation_code' => Str::random(10),
        'status' => $request->has('offers') && count($request->offers) > 0 
                    ? 'pending' 
                    : 'approved'
    ]);

    // Attach offers if provided
    if ($request->has('offers') && count($request->offers) > 0) {
        foreach ($request->offers as $offerId) {
            $event->offers()->attach($offerId, ['status' => 'pending']);
        }
    }

    return response()->json([
        'message' => $event->status === 'approved' 
                        ? 'Event created successfully and automatically approved (no offers).' 
                        : 'Event created successfully and is pending offer approvals.',
        'event' => $event->load('offers')
    ], 201);
}



    /**
     * Provider responds to an event invitation (approve/reject)
     */



public function respondToEvent(Request $request, UserEvent $event)
{
    $request->validate([
        'status' => 'required|in:approved,rejected'
    ]);

    $providerId = auth()->id();

    // Only the provider who made the offer can respond
    $offer = $event->offers()->where('user_id', $providerId)->first();

    if (!$offer) {
        return response()->json(['error' => 'You do not have an offer for this event'], 403);
    }

    // Update pivot status
    $event->offers()->updateExistingPivot($offer->id, ['status' => $request->status]);

    // Update event status based on all offer responses
    $statuses = $event->offers()->pluck('event_offer.status');

    if ($statuses->contains('rejected')) {
        $event->update(['status' => 'rejected']);
    } elseif ($statuses->every(fn($s) => $s === 'approved')) {
        $event->update(['status' => 'approved']);
        // Trigger payments here if needed
    } else {
        $event->update(['status' => 'pending']);
    }

    return response()->json([
        'message' => 'Response recorded',
        'event' => $event->fresh('offers.user')
    ]);
}


    /**
     * Get all event requests for the logged-in provider
     */
    public function getProviderRequests()
{
    $requests = \App\Models\UserEvent::with([
            'user',
            'offers.user' // Load provider info for each offer
        ])
        ->whereHas('offers') // Only events with offers
        ->get()
        ->map(function ($event) {
            return [
                'id' => $event->id,
                'title' => $event->title,
                'status' => $event->status,
                'date' => $event->date,
                'time' => $event->time,
                'user' => [
                    'id' => $event->user->id,
                    'name' => $event->user->name,
                ],
                'offers' => $event->offers->map(function ($offer) {
                    return [
                        'id' => $offer->id,
                        'title' => $offer->title,
                        'price' => $offer->price,
                        'status' => $offer->pivot->status,
                        'provider' => [
                            'id' => $offer->user->id,
                            'name' => $offer->user->name,
                        ]
                    ];
                })
            ];
        });

    return response()->json([
        'requests' => $requests
    ]);
}



}
