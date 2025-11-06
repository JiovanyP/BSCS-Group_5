<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    public function showLoginForm()
    {
        return view('admin.adminlogin');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        // Use the admin guard
        if (Auth::guard('admin')->attempt([
            'email' => $credentials['email'],
            'password' => $credentials['password']
        ], $request->boolean('remember'))) {

            $request->session()->regenerate();

            // Update last login timestamp for admin
            Auth::guard('admin')->user()->update(['last_login_at' => now()]);

            return redirect()->route('admin.dashboard')
                ->with('success', 'Welcome back, Super Admin!');
        }

        return back()->withErrors([
            'email' => 'Invalid email or password.',
        ]);
    }

    public function logout(Request $request)
    {
        Auth::guard('admin')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login')
            ->with('success', 'You have been logged out.');
    }

    public function analytics()
    {
        // Total users
        $totalUsers = \App\Models\User::count();

        // Active users (e.g., users who logged in within the last 30 days)
        $activeUsers = \App\Models\User::where('last_login_at', '>=', now()->subDays(30))->count();

        // Total posts
        $totalPosts = \App\Models\Post::count();

        // Total reports
        $totalReports = \App\Models\Report::count();

        // Posts by accident type
        $postsByType = \App\Models\Post::selectRaw('accident_type, COUNT(*) as count')
            ->whereNotNull('accident_type')
            ->groupBy('accident_type')
            ->get();

        // Reports by reason
        $reportsByReason = \App\Models\Report::selectRaw('reason, COUNT(*) as count')
            ->groupBy('reason')
            ->get();

        // Top locations by posts
        $topLocations = \App\Models\Post::selectRaw('location, COUNT(*) as count')
            ->whereNotNull('location')
            ->groupBy('location')
            ->orderBy('count', 'desc')
            ->limit(5)
            ->get();

        // Graph data: Posts over last 30 days
        $postsOverTime = \App\Models\Post::selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('count', 'date')
            ->toArray();

        // Graph data: User registrations over last 30 days
        $usersOverTime = \App\Models\User::selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('count', 'date')
            ->toArray();

        // Graph data: Reports over last 30 days
        $reportsOverTime = \App\Models\Report::selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('count', 'date')
            ->toArray();

        return view('admin.analytics', compact(
            'totalUsers',
            'activeUsers',
            'totalPosts',
            'totalReports',
            'postsByType',
            'reportsByReason',
            'topLocations',
            'postsOverTime',
            'usersOverTime',
            'reportsOverTime'
        ));
    }

    public function postsByAccidentType($type)
    {
        $posts = \App\Models\Post::where('accident_type', $type)
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.posts-by-type', compact('posts', 'type'));
    }
}
