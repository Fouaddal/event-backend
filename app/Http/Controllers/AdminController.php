<?php

namespace App\Http\Controllers;

use App\Models\ProviderRequest;
use App\Models\User;
use App\Models\Event;
use Illuminate\Http\Request;
use App\Models\EventRequest;
class AdminController extends Controller
{
   public function dashboard()
{
    $requests = ProviderRequest::where('status', 'pending')
                               ->where('provider_type', 'individual')
                               ->get();

    return view('admin.dashboard', compact('requests'));
}



    public function listProviderRequests()
{
    $requests = ProviderRequest::where('status', 'pending')->get();
    return view('admin.provider-requests', compact('requests'));
}

public function listCompanies()
{
    $requests = ProviderRequest::where('status', 'pending')
                                ->where('provider_type', 'company')
                                ->get();

    return view('admin.companies', compact('requests'));
}


    // ✅ Approve a provider request with redirec

public function approveProviderRequest($id)
{
    $request = ProviderRequest::findOrFail($id);

    // Create the user
    $user = User::create([
        'name'           => $request->name,
        'email'          => $request->email,
        'password'       => $request->password, // Ensure this is already hashed
        'type'           => 'provider',
        'provider_type'  => $request->provider_type,
        'is_approved' => true,
        'services' =>$request->services,
        'specializations' => $request->specializations,
    ]);

    // Generate a token using Laravel Sanctum
    $token = $user->createToken('provider-token')->plainTextToken;

    // Save token and status in ProviderRequest for Flutter to fetch later
    $request->status = 'approved';
      $request->is_approved   = true;
    //$request->token = $token; // Make sure 'token' column exists in DB
    $request->save();

    return redirect()
        ->route('admin.providerRequests')
        ->with('success', 'Provider approved and token generated.');
}


   public function rejectProviderRequest($id)
{
    $request = ProviderRequest::findOrFail($id);
    $request->status = 'rejected';
    $request->save();

    return redirect()
        ->route('admin.providerRequests')
        ->with('success', 'Provider request rejected.');
}

    // Optional: Approve or reject existing providers
    public function pendingProviders()
    {
        return User::where('type', 'provider')->where('is_approved', false)->get();
    }

    public function approveProvider($id)
    {
        $provider = User::findOrFail($id);
        $provider->is_approved = true;
        $provider->save();

        return redirect()->back()->with('success', 'Provider approved.');
    }

    public function rejectProvider($id)
    {
        $provider = User::findOrFail($id);
        $provider->delete();

        return redirect()->back()->with('success', 'Provider rejected and deleted.');
    }


// Admin approves event request and creates actual event
public function approveEventRequest($id)
{
    // Retrieve the pending event request
    $request = EventRequest::where('id', $id)->where('status', 'pending')->firstOrFail();

    // Create the event in the events table
    $event = Event::create([
        'user_id'       => $request->user_id,
        'title'         => $request->title,
        'description'   => $request->description,
        'date_time'     => $request->date_time,
        'location'      => $request->location,
        'image'         => $request->image,
        'ticket_price'  => $request->ticket_price,
        'capacity'      => $request->capacity,
        'category'      => $request->category,
        'status'        => "approved"
    ]);

    // Mark the request as approved
    $request->update(['status' => 'approved']);

    // Optional: you can add a notification or log here

    // Return response (HTML or JSON depending on usage)
    return redirect()->back()->with('success', 'Event approved and published.');
}

public function rejectEventRequest($id)
{
    $request = EventRequest::where('id', $id)->where('status', 'pending')->firstOrFail();
    $request->update(['status' => 'rejected']);

    return redirect()->back()->with('success', 'Event request rejected.');
}



// Admin rejects event request




    // app/Http/Controllers/AdminController.php
public function pendingEvents()
{
    $events = EventRequest::where('status', 'pending')->with('company')->get();
    return view('admin.pending-events', compact('events'));
}

public function approveEvent($id)
{
    $event = Event::findOrFail($id);
    $event->status = 'approved';
    $event->save();

    return redirect()->back()->with('success', 'Event approved');
}

public function rejectEvent($id)
{
    $event = Event::findOrFail($id);
    $event->status = 'rejected';
    $event->save();

    return redirect()->back()->with('success', 'Event rejected');
}
}
