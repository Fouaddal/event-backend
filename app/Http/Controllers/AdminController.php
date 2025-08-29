<?php

namespace App\Http\Controllers;

use App\Models\ProviderRequest;
use App\Models\User;
use App\Models\Event;
use App\Models\Offer;
use Illuminate\Http\Request;
use App\Models\EventRequest;
class AdminController extends Controller
{


    public function dashboardStats()
{
    $totalProviders = \App\Models\User::where('provider_type', 'individual')->count();
    $totalCompanies = \App\Models\User::where('provider_type', 'company')->count();
    $totalUsers = \App\Models\User::count();
    $totalEvents = \App\Models\Event::count();
    $totalOffers = \App\Models\Offer::count();

    return response()->json([
        'totalUsers' => $totalUsers,
        'totalProviders' => $totalProviders,
        'totalCompanies' => $totalCompanies,
        'totalEvents' => $totalEvents,
        'totalOffers' => $totalOffers,
    ]);
}


    // Providers by type
    public function providersByType()
    {
        $data = User::select('provider_type')
            ->where('type', 'provider')
            ->selectRaw('count(*) as count')
            ->groupBy('provider_type')
            ->get();

        return response()->json($data);
    }

    // Events status
    public function eventsStatus()
    {
        $pending = Event::where('status', 'pending')->count();
        $approved = Event::where('status', 'approved')->count();
        $rejected = Event::where('status', 'rejected')->count();

        return response()->json(compact('pending', 'approved', 'rejected'));
    }

    // Offers over time
    public function offersOverTime()
    {
        $data = Offer::selectRaw('DATE(created_at) as date, count(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return response()->json($data);
    }

    // Recent activity (example)
    public function recentActivity()
{
   $activities = EventRequest::latest()->take(10)->get()->map(function ($event) {
        return [
            'type' => 'event_created',
            'message' => "New event created: {$event->title}",
            'time' => $event->created_at->diffForHumans(),
            'icon' => 'calendar-check',
            'color' => 'purple',
        ];
    });

    return response()->json($activities);
}


    // Blade view
   ///////////////////////////////////////
    // Providers by type (company vs individual)


   public function index()
{
    // Counts
    $totalProviders = User::where('provider_type', 'individual')->count();
    $totalCompanies = User::where('provider_type', 'company')->count();
    $totalUsers     = User::count();
    $totalEvents    = Event::count();
    $totalOffers    = Offer::count();

    // Percentages
    $providerPercentage = $totalUsers > 0 ? round(($totalProviders / $totalUsers) * 100, 1) : 0;
    $companyPercentage  = $totalUsers > 0 ? round(($totalCompanies / $totalUsers) * 100, 1) : 0;

    // Event statuses
    $approvedEvents = Event::where('status', 'approved')->count();
    $pendingEvents  = Event::where('status', 'pending')->count();
    $rejectedEvents = Event::where('status', 'rejected')->count();

    // Offers over time
    $offersOverTime = Offer::selectRaw('DATE(created_at) as date, count(*) as count')
        ->groupBy('date')
        ->orderBy('date')
        ->get();

    // Top companies (by events)
    $topCompanies = User::where('provider_type', 'company')
        ->withCount('events')
        ->orderByDesc('events_count')
        ->take(5)
        ->get();

    // Recent activity
    $activities = EventRequest::latest()
        ->take(10)
        ->get()
        ->map(function ($event) {
            return [
                'type'    => 'event_created',
                'message' => "New event created: {$event->title}",
                'time'    => $event->created_at->diffForHumans(),
                'icon'    => 'calendar-check',
                'color'   => 'purple',
            ];
        });

    // **Recent Events and Offers**
    // Recent Events and Offers
$events = Event::with('user')->latest()->take(10)->get();
$offers = Offer::with('user')->latest()->take(10)->get();


    return view('admin.new', compact(
        'totalProviders',
        'totalCompanies',
        'totalUsers',
        'totalEvents',
        'totalOffers',
        'providerPercentage',
        'companyPercentage',
        'approvedEvents',
        'pendingEvents',
        'rejectedEvents',
        'offersOverTime',
        'topCompanies',
        'activities',
        'events',       // Added
        'offers'        // Added
    ));
}



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
