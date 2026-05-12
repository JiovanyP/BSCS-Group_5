<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Service; // <-- CRITICAL: This connects the model
use Illuminate\Support\Facades\Auth; // <-- Added to track which admin created it

class ServiceController extends Controller
{
    public function index()
    {
        // STEP 5: Split the services so the admin can review pending ones easily
        $pendingServices = Service::where('status', 'pending')->latest()->get();
        $approvedServices = Service::where('status', 'approved')->latest()->get(); 

        // Make sure to pass both variables to the view!
        return view('admin.services.index', compact('pendingServices', 'approvedServices'));
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

        // Save to the database
        Service::create([
            'user_id' => Auth::id(), // Link it to the admin who created it
            'status' => 'approved',  // Auto-approve since the Admin is creating it
            'business_name' => $request->business_name,
            'description' => $request->business_description,
            'services_offered' => $request->service_offered,
            'contact_number' => $request->contact_number,
            'address' => $request->address,
        ]);

        return redirect()->back()->with('success', 'Service added successfully!');
    }

    // STEP 5: Add the Approve Method
    public function approve($id)
    {
        $service = Service::findOrFail($id);
        
        // Change the status to approved
        $service->update(['status' => 'approved']);
        
        return redirect()->back()->with('success', $service->business_name . ' has been approved and is now live!');
    }

    public function destroy($id)
    {
        // Actually delete from the database
        Service::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'Service removed from directory.');
    }
}