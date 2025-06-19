<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    // Show all services (no approval filter needed anymore)
    public function index()
    {
        return Service::all();
    }

    // Store a new service (auto-approved)
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
            'is_approved' => true, // Auto-approved
        ]);

        return response()->json([
            'message' => 'Service created and approved successfully.'
        ]);
    }
}
