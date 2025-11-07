<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Post;
use App\Models\Report;
use App\Models\Admin;

/**
 * AdminController
 *
 * Responsible for admin authentication and admin-only features.
 * Preserves existing logic, adds full defensive handling, 
 * and ensures safe last_login_at display for Settings view.
 */
class AdminController extends Controller
{
    /**
     * Show admin login form.
     */
    public function showLoginForm()
    {
        return view('admin.adminlogin');
    }

    /**
     * Handle admin login attempt.
     *
     * Uses 'admin' guard. Regenerates session on successful auth and
     * attempts to update last_login_at only if the column exists.
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        if (Auth::guard('admin')->attempt([
            'email' => $credentials['email'],
            'password' => $credentials['password']
        ], $request->boolean('remember'))) {

            $request->session()->regenerate();

            $admin = Auth::guard('admin')->user();

            // Session fallback for displaying latest login instantly
            session(['admin_last_login' => now()]);

            // Update last_login_at safely if column exists
            try {
                if ($admin && Schema::hasColumn($admin->getTable(), 'last_login_at')) {
                    $admin->last_login_at = now();
                    $admin->save();
                }
            } catch (\Throwable $e) {
                Log::warning('Could not update admin.last_login_at: '.$e->getMessage());
            }

            return redirect()->route('admin.dashboard')
                ->with('success', 'Welcome back, Super Admin!');
        }

        return back()->withErrors([
            'email' => 'Invalid email or password.',
        ]);
    }

    /**
     * Log out the current admin.
     */
    public function logout(Request $request)
    {
        Auth::guard('admin')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login')
            ->with('success', 'You have been logged out.');
    }

    /**
     * Analytics dashboard for admin.
     * Gathers counts and aggregates safely.
     */
    public function analytics()
    {
        $totalUsers = User::count();
        $totalPosts = Post::count();
        $totalReports = Report::count();

        // Active users (safe fallback if column missing)
        $activeUsers = 0;
        try {
            if (Schema::hasColumn((new User)->getTable(), 'last_login_at')) {
                $activeUsers = User::where('last_login_at', '>=', now()->subDays(30))->count();
            } else {
                $activeUsers = User::where('created_at', '>=', now()->subDays(30))->count();
            }
        } catch (\Throwable $e) {
            Log::warning('Could not compute active users: '.$e->getMessage());
        }

        $postsByType = Post::selectRaw('accident_type, COUNT(*) as count')
            ->whereNotNull('accident_type')
            ->groupBy('accident_type')
            ->get();

        $reportsByReason = Report::selectRaw('reason, COUNT(*) as count')
            ->groupBy('reason')
            ->get();

        $topLocations = Post::selectRaw('location, COUNT(*) as count')
            ->whereNotNull('location')
            ->groupBy('location')
            ->orderBy('count', 'desc')
            ->limit(5)
            ->get();

        $postsOverTime = Post::selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('count', 'date')
            ->toArray();

        $usersOverTime = User::selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('count', 'date')
            ->toArray();

        $reportsOverTime = Report::selectRaw('DATE(created_at) as date, COUNT(*) as count')
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

    /**
     * Show posts filtered by accident type.
     */
    public function postsByAccidentType($type)
    {
        $posts = Post::where('accident_type', $type)
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.posts-by-type', compact('posts', 'type'));
    }

    /**
     * Settings page (reads DB-backed settings safely).
     * Also prepares formatted $lastLogin for Blade view.
     */
    public function settings()
    {
        $admin = Auth::guard('admin')->user();

        // Default (non-persistent) settings
        $defaults = [
            'email_notifications' => 'enabled',
            'theme' => 'dark',
            'allow_user_reports' => 'enabled',
            'items_per_page' => 20,
        ];

        // Safe read of settings JSON
        $dbSettings = [];
        try {
            if ($admin && is_array($admin->settings)) {
                $dbSettings = $admin->settings;
            } elseif ($admin && !empty($admin->settings)) {
                $decoded = json_decode($admin->settings, true);
                $dbSettings = is_array($decoded) ? $decoded : [];
            }
        } catch (\Throwable $e) {
            Log::warning('Could not read admin.settings: '.$e->getMessage());
        }

        $settings = array_merge($defaults, $dbSettings);

        // Compute last login (safe Carbon conversion)
        $lastLogin = null;
        if ($admin) {
            try {
                if (Schema::hasColumn($admin->getTable(), 'last_login_at') && $admin->last_login_at) {
                    $lastLogin = $admin->last_login_at instanceof Carbon
                        ? $admin->last_login_at
                        : Carbon::parse($admin->last_login_at);
                } else {
                    $lastLogin = session('admin_last_login')
                        ? Carbon::parse(session('admin_last_login'))
                        : null;
                }
            } catch (\Throwable $e) {
                Log::warning('Could not parse last_login_at: '.$e->getMessage());
            }
        }

        return view('admin.settings', compact('admin', 'settings', 'lastLogin'));
    }

    /**
     * Update admin password securely.
     */
    public function updatePassword(Request $request)
    {
        $admin = Auth::guard('admin')->user();

        $data = $request->validate([
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        if (! Hash::check($data['current_password'], $admin->password)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect.']);
        }

        $admin->password = Hash::make($data['new_password']);
        $admin->setRememberToken(Str::random(60));

        try {
            $admin->save();
        } catch (\Throwable $e) {
            Log::error('Admin password update failed: '.$e->getMessage());
            return back()->withErrors(['current_password' => 'Password update failed. Please try again.']);
        }

        $request->session()->regenerate();

        return redirect()->route('admin.settings')->with('success', 'Password updated successfully.');
    }

    /**
     * Update admin theme preference.
     */
    public function updateTheme(Request $request)
    {
        $admin = Auth::guard('admin')->user();

        $data = $request->validate([
            'theme' => 'required|string|in:dark,light',
        ]);

        $current = [];
        try {
            if ($admin && is_array($admin->settings)) {
                $current = $admin->settings;
            } elseif ($admin && !empty($admin->settings)) {
                $decoded = json_decode($admin->settings, true);
                $current = is_array($decoded) ? $decoded : [];
            }
        } catch (\Throwable $e) {
            Log::warning('Could not read admin settings for update: '.$e->getMessage());
        }

        $current['theme'] = $data['theme'];

        try {
            $admin->settings = $current;
            $admin->save();
        } catch (\Throwable $e) {
            Log::error('Failed to save admin theme: '.$e->getMessage());
            return redirect()->route('admin.settings')
                ->with('success', 'Theme preference updated (temporary)');
        }

        return redirect()->route('admin.settings')->with('success', 'Theme preference updated.');
    }
}
