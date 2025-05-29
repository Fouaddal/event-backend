<?php

namespace App\Http\Controllers;


use App\Models\Invitation;
use Illuminate\Http\Request;

class InvitationController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'event_id' => 'required|exists:events,id',
            'guest_email' => 'required|email'
        ]);

        $invitation = Invitation::create([
            'event_id' => $request->event_id,
            'guest_email' => $request->guest_email
        ]);

        return response()->json($invitation, 201);
    }
}