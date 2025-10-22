<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Report;

class ReportController extends Controller
{
    /**
     * Resolve a report (dismiss)
     */
    public function resolve(Request $request, $postId)
    {
        // Delete all reports for this post
        Report::where('post_id', $postId)->delete();

        return redirect()->back()->with('success', 'Report resolved successfully!');
    }
}
