<?php
// app/Http/Controllers/AccidentReportController.php

namespace App\Http\Controllers;

use App\Models\Accident;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AccidentReportController extends Controller
{
    public function create()
    {
        return view('accidents.create');
    }

    public function showReportDetails($id)
    {
        $report = Accident::findOrFail($id);
        return view('accidents.details', compact('report'));
    }


    public function store(Request $request)
    {
        // Validation
        $validated = $request->validate([
            'full_name' => 'nullable|string|max:255',
            'location' => 'required|string|max:255',
            'accident_type' => 'required|string|max:255',
            'description' => 'required|string',
            'photo' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'urgency' => 'required|in:low,medium,high'
        ]);

        // Handle file upload
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('accident-photos', 'public');
        } else {
            return back()->with('error', 'Photo is required.')->withInput();
        }

        // Create the accident report
        $accident = Accident::create([
            'full_name' => $validated['full_name'],
            'location' => $validated['location'],
            'accident_type' => $validated['accident_type'],
            'description' => $validated['description'],
            'photo_path' => $photoPath,
            'urgency' => $validated['urgency']
        ]);

        if ($accident) {
            return redirect()->route('accidents.create')
                ->with('success', 'Your accident report has been submitted successfully.');
        }

        return back()->with('error', 'Error submitting your report. Please try again.')->withInput();
    }
}