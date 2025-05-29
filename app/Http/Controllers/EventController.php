<?php

namespace App\Http\Controllers;


use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Str;


class EventController extends Controller
{
    public function index()
    {
        return Event::with('services')->get();
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'type' => 'required|in:user_created,ready_made',
            'date' => 'required|date',
            'location' => 'required',
            'is_public' => 'boolean',
        ]);

        $event = Event::create([
            'user_id' => auth()->id(),
            'provider_id' => $request->provider_id,
            'title' => $request->title,
            'type' => $request->type,
            'is_public' => $request->is_public ?? false,
            'date' => $request->date,
            'location' => $request->location,
            'invitation_code' => Str::random(10)
        ]);

        return response()->json($event, 201);
    }
}