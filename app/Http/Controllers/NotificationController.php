<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Notification;
use App\Models\Post;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Display notifications with filtering
     */
    public function index(Request $request)
    {
        $type = $request->get('type', 'all');
        $accidentType = $request->get('accident_type', 'all');

        // Base query
        $query = Notification::with(['actor', 'post', 'comment'])
            ->where('user_id', Auth::id());

        // Filter by notification type
        if ($type === 'priority') {
            $query->priority();
        } elseif ($type === 'general') {
            $query->general();
        } elseif ($type === 'social') {
            $query->social();
        }

        // Filter by accident type
        $query->byAccidentType($accidentType);

        $notifications = $query->orderBy('created_at', 'desc')
            ->paginate(20)
            ->appends($request->except('page'));

        // Counts for each category
        $priorityCount = Notification::where('user_id', Auth::id())->priority()->count();
        $generalCount = Notification::where('user_id', Auth::id())->general()->count();
        $socialCount = Notification::where('user_id', Auth::id())->social()->count();
        $totalCount = $priorityCount + $generalCount + $socialCount;

        $unreadCount = Notification::where('user_id', Auth::id())
            ->unread()
            ->count();

        return view('notifications', compact(
            'notifications', 
            'unreadCount',
            'priorityCount',
            'generalCount',
            'socialCount',
            'totalCount',
            'type',
            'accidentType'
        ));
    }

    /**
     * Create location-based notifications for all users when a new post is created
     */
    /**
 * Create location-based notifications for all users when a new post is created
 */
        public static function createLocationNotifications(Post $post)
        {
            $users = User::where('id', '!=', $post->user_id)
                        ->whereNotNull('location')
                        ->get();
            
            foreach ($users as $user) {
                // Compare user location with post location
                $userLocation = strtolower(trim($user->location));
                $postLocation = strtolower(trim($post->location));
                
                // Determine notification type based on location match
                if ($userLocation === $postLocation) {
                    // Same location = Priority
                    $notificationType = 'priority';
                    $distance = 0;
                } else {
                    // Different location = General
                    $notificationType = 'general';
                    $distance = 10; // Simple default distance
                }

                // Create the notification
                Notification::create([
                    'user_id' => $user->id,
                    'actor_id' => $post->user_id,
                    'post_id' => $post->id,
                    'type' => 'location_alert',
                    'notification_type' => $notificationType,
                    'accident_type' => $post->accident_type,
                    'distance_km' => $distance,
                    'is_read' => false,
                ]);
            }
        }

        /**
         * Simple distance calculation based on location string similarity
         * In production, you'd use geocoding APIs like Google Maps
         */
        /**
     * Better distance calculation based on location string similarity
     */
    /**
 * Simple distance calculation - just check if locations match
 */
    private static function calculateSimpleDistance($userLocation, $postLocation)
    {
        $userLocation = strtolower(trim($userLocation));
        $postLocation = strtolower(trim($postLocation));
        
        // If locations are exactly the same
        if ($userLocation === $postLocation) {
            return 0; // Same location
        }
        
        // If locations are completely different
        return 10; // Default distance for different locations
    }

        /**
         * Mark a single notification as read
         */
        public function markAsRead($id)
        {
            $notification = Notification::where('user_id', Auth::id())
                ->findOrFail($id);
            
            $notification->markAsRead();

            return response()->json(['success' => true]);
        }

        /**
         * Mark all notifications as read
         */
        public function markAllAsRead()
        {
            Notification::where('user_id', Auth::id())
                ->unread()
                ->update(['is_read' => true]);

            return redirect()->back()->with('success', 'All notifications marked as read');
        }

    /**
     * Delete a notification
     */
    public function destroy($id)
    {
        $notification = Notification::where('user_id', Auth::id())
            ->findOrFail($id);
        
        $notification->delete();

        return response()->json(['success' => true]);
    }

    /**
     * Get unread notification count (for AJAX/nav badge)
     */
    public function getUnreadCount()
    {
        $count = Notification::where('user_id', Auth::id())
            ->unread()
            ->count();

        return response()->json(['count' => $count]);
    }
}