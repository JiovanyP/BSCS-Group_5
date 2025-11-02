<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Report;
use App\Models\Post;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{
    /**
     * Admin listing of reports grouped by post (optional route).
     * Returns a simple grouped dataset that admin blade can use.
     *
     * Note: Your PostController::adminDashboard already builds $reportedPosts.
     * This method is provided if you prefer a dedicated reports route (e.g. /admin/reports).
     */
    public function index(Request $request)
    {
        // Only allow admin guard or admin middleware should protect this route.
        // If you're using auth:admin middleware in routes, this double-check is optional.
        if (!Auth::guard('admin')->check() && !Auth::check()) {
            abort(403);
        }

        // Load reports with related post and reporter
        $reports = Report::with(['user', 'post.user'])
            ->orderByDesc('created_at')
            ->get();

        // Group by post_id for admin consumption in blade
        $groups = $reports->groupBy('post_id')->map(function ($reportsForPost) {
            return (object) [
                'post' => $reportsForPost->first()->post,
                'reports' => $reportsForPost,
                'reports_count' => $reportsForPost->count(),
            ];
        })->values();

        return view('admin.reports-index', ['groups' => $groups]);
    }

    /**
     * Resolve (dismiss) reports for a post.
     * Deletes all report rows for that post.
     *
     * This method is wired from admin routes: POST /admin/reports/{post}/resolve
     */
    public function resolve(Request $request, $postId)
    {
        // Basic permission check: ensure admin (route middleware should already enforce)
        if (!Auth::guard('admin')->check() && !Auth::check()) {
            return redirect()->back()->with('error', 'Unauthorized');
        }

        // Ensure post exists (optional)
        $post = Post::find($postId);

        // Delete reports for the post (DB has cascade but we do explicit delete to be explicit)
        Report::where('post_id', $postId)->delete();

        // Optionally flash a message; your admin UI expects a redirect
        return redirect()->back()->with('success', 'Reports resolved for the post.');
    }
}
