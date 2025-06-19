<?php

namespace App\Http\Controllers;

use App\Models\ProviderRequest;
use App\Models\User;
use Illuminate\Http\Request;

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
        'services' =>$request->services
       
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
}
