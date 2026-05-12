<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\Report;

class ReportController extends Controller
{
    /**
     * Display a paginated list of all reports for the admin dashboard.
     */
    public function index()
    {
        // Eager load related post and user to avoid N+1 issues
        $reports = Report::with(['post', 'user'])
            ->latest()
            ->paginate(20);

        return view('admin.reports', compact('reports'));
    }

    /**
     * Resolve (dismiss) all reports related to a specific post.
     */
    public function resolve(Request $request, $postId)
    {
        Report::where('post_id', $postId)->delete();
        return redirect()->back()->with('success', 'Report resolved successfully!');
    }

    /**
     * (Optional) Handle reports for deleted posts (orphans).
     * This can be used if some reports remain after a post was deleted.
     */
    public function resolveOrphan(Request $request, $reportId)
    {
        $report = Report::find($reportId);

        if ($report && !$report->post) {
            $report->delete();
            return redirect()->back()->with('success', 'Orphaned report removed successfully.');
        }

        return redirect()->back()->with('info', 'No orphaned report found or already handled.');
    }
}
