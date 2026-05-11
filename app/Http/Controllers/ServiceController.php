<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Service;
use Illuminate\Support\Facades\Auth;

class ServiceController extends Controller
{
    public function index()
    {
        // Users ONLY see approved services
        $services = Service::where('status', 'approved')->latest()->get();
        return view('services.index', compact('services'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'business_name' => 'required|string|max:255',
            'business_description' => 'nullable|string',
            'service_offered' => 'required|string|max:255',
            'contact_number' => 'required|string|max:20',
            'address' => 'required|string|max:500',
        ]);

        Service::create([
            'user_id' => Auth::id(), // Link it to the logged-in user
            'status' => 'pending',   // Defaults to pending
            'business_name' => $request->business_name,
            'description' => $request->business_description,
            'services_offered' => $request->service_offered,
            'contact_number' => $request->contact_number,
            'address' => $request->address,
        ]);

        // Tell the user about the next steps (Payment)
        return redirect()->back()->with('success', 'Application submitted! Please pay the advertisement fee at the Admin office to get your service approved and listed.');
    }
}