<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Http\Request;

class ServiceController extends Controller
{

   public function unapprovedServices()
{
    $services = Service::where('is_approved', false)->get();
    return view('services.index', compact('services'));
}


    public function index()
    {
        return Service::where('is_approved', true)->get();
    }

   public function store(Request $request)
{
    $request->validate([
        'type' => 'required|in:hall,food,dj,photographer,car,singer,performer',
        'name' => 'required',
        'description' => 'required',
        'price' => 'required|numeric',
    ]);

    $service = Service::create([
        'provider_id' => auth()->id(),
        'type' => $request->type,
        'name' => $request->name,
        'description' => $request->description,
        'price' => $request->price,
        'is_approved' => false,
    ]);

  return response()->json([
                'message' => 'Your request was submitted. Wait for admin approval.'
            ]);
}

    public function approve($id)
{
    $service = Service::findOrFail($id);
    $service->is_approved = true;
    $service->save();

    return redirect()->route('admin.services.index')->with('success', 'Service approved.');
}

public function reject($id)
{
    $service = Service::findOrFail($id);
    $service->delete();

    return redirect()->route('admin.services.index')->with('success', 'Service rejected and removed.');
}

}
