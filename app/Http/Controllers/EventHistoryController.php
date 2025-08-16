<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;
use Carbon\Carbon;

class EventHistoryController extends Controller
{
    // ✅ Get upcoming events I have tickets for
    public function myUpcomingEvents(Request $request)
    {
        $userId = auth()->id();
        $today = Carbon::today();

        $events = Event::whereHas('tickets', function($query) use ($userId) {
                $query->where('user_id', $userId);
            })
            ->where('date_time', '>', $today)
            ->orderBy('date_time', 'asc')
            ->get();

        return response()->json([
            'message' => 'Upcoming events with my tickets',
            'events' => $events
        ]);
    }

    // ✅ Get past events I attended
    public function myPastEvents(Request $request)
    {
        $userId = auth()->id();
        $today = Carbon::today();

        $events = Event::whereHas('tickets', function($query) use ($userId) {
                $query->where('user_id', $userId);
            })
            ->where('date_time', '<', $today)
            ->orderBy('date_time', 'desc')
            ->get();

        return response()->json([
            'message' => 'Past events I attended',
            'events' => $events
        ]);
    }
}
