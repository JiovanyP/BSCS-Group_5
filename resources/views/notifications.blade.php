@extends('layouts.app')

@section('title', 'Notifications - Publ')

@section('content')
<div class="notifications-wrapper">
    <div class="notifications-container">
        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif
    
        {{-- No location warning --}}
        @if(auth()->user() && empty(auth()->user()->location))
            <div class="notification-item unread" onclick="loadEditModal()">
                <span class="unread-dot"></span>
                <div class="notification-time">
                    <span class="material-icons">schedule</span> {{ now()->diffForHumans() }}
                </div>
                <div class="notification-content">
                    <div class="notification-meta-top">
                        <div class="notification-badges">
                            <span class="badge priority-badge">
                                <span class="material-icons" style="font-size:11px; vertical-align: bottom;">warning</span> Priority
                            </span>
                        </div>
                    </div>
                    <div class="notification-message">
                        <strong>No location set!</strong> <br>You haven't added your location in your profile. 
                        <span style="text-decoration: underline; color: var(--primary-color); cursor: pointer;" onclick="loadEditModal(); event.stopPropagation();">
                            Edit your profile
                        </span> to add your location and get local notifications.
                    </div>
                </div>
            </div>
        @endif

        <div class="categories-tabs">
            <div class="tabs-header">
                <div class="tabs">
                    <button class="tab-btn {{ $type == 'all' ? 'active' : '' }}" data-type="all">
                        All <span class="tab-count">{{ $totalCount }}</span>
                    </button>
                    <button class="tab-btn {{ $type == 'priority' ? 'active' : '' }}" data-type="priority">
                        Priority <span class="tab-count">{{ $priorityCount }}</span>
                    </button>
                    <button class="tab-btn {{ $type == 'general' ? 'active' : '' }}" data-type="general">
                        General <span class="tab-count">{{ $generalCount }}</span>
                    </button>
                    <button class="tab-btn {{ $type == 'social' ? 'active' : '' }}" data-type="social">
                        Social <span class="tab-count">{{ $socialCount }}</span>
                    </button>
                </div>
                
                @if(in_array($type, ['priority', 'general']))
                <div class="filter-section">
                    <select id="accidentTypeFilter" class="filter-select">
                        <option value="all" {{ $accidentType == 'all' ? 'selected' : '' }}>All Accident Types</option>
                        <option value="Fire" {{ $accidentType == 'Fire' ? 'selected' : '' }}>Fire</option>
                        <option value="Crime" {{ $accidentType == 'Crime' ? 'selected' : '' }}>Crime</option>
                        <option value="Traffic" {{ $accidentType == 'Traffic' ? 'selected' : '' }}>Traffic</option>
                        <option value="Others" {{ $accidentType == 'Others' ? 'selected' : '' }}>Others</option>
                    </select>
                </div>
                @endif
            </div>
        </div>

        <div class="notifications-header">
            <div class="notifications-count">
                @if($type == 'all')
                    You have <span class="count">{{ $unreadCount }}</span> unread notification{{ $unreadCount != 1 ? 's' : '' }}
                @else
                    <span class="category-title">
                        @if($type == 'priority') 
                            <span class="material-icons">emergency</span> Priority Reports in Your Area
                        @elseif($type == 'general')
                            <span class="material-icons">location_on</span> General Reports
                        @elseif($type == 'social')
                            <span class="material-icons">chat</span> Social Interactions
                        @else
                            All Notifications
                        @endif
                    </span>
                @endif
            </div>
            @if($unreadCount > 0 && $type == 'all')
                <form method="POST" action="{{ route('notifications.markAllRead') }}" style="display: inline;">
                    @csrf
                    <button type="submit" class="mark-all-read-btn">
                        <span class="material-icons">done_all</span> Mark All as Read
                    </button>
                </form>
            @endif
        </div>

        @forelse($notifications as $notification)
            <div class="notification-item {{ $notification->is_read ? '' : 'unread' }}"
                onclick="markAsReadAndNavigate({{ $notification->id }}, '{{ $notification->post ? route('posts.view', $notification->post_id) : '#' }}')">
                
                @if(!$notification->is_read)
                    <span class="unread-dot"></span>
                @endif
                
                <div class="notification-time">
                    <span class="material-icons">schedule</span> {{ $notification->created_at->diffForHumans() }}
                </div>
                
                <div class="notification-content">
                    <div class="notification-meta-top">
                        <div class="notification-badges">
                            
                            @if($notification->notification_type === 'priority')
                                <span class="badge priority-badge">
                                    <span class="material-icons" style="font-size: 11px; vertical-align: bottom;">warning</span> Priority
                                </span>
                            @elseif($notification->notification_type === 'general')
                                <span class="badge general-badge">
                                    <span class="material-icons" style="font-size: 11px; vertical-align: bottom;">push_pin</span> General
                                </span>
                            @elseif($notification->notification_type === 'social')
                                <span class="badge social-badge">
                                    <span class="material-icons" style="font-size: 11px; vertical-align: bottom;">group</span> Social
                                </span>
                            @endif
                            
                            @if($notification->accident_type && in_array($notification->notification_type, ['priority', 'general']))
                                <span class="badge accident-badge accident-{{ strtolower($notification->accident_type) }}">
                                    {{ $notification->accident_type }}
                                </span>
                            @endif

                            @if($notification->post && in_array($notification->notification_type, ['priority', 'general']))
                                <div class="location-info location-inline">
                                    <span class="material-icons">place</span>
                                    <strong>Reported in: {{ $notification->post->location }}</strong>
                                </div>
                            @endif
                        </div>
                    </div>
                    
                    <div class="notification-message">
                        @if ($notification->notification_type === 'social' && ($notification->type === 'comment' || $notification->type === 'reply') && $notification->post && $notification->comment)
                            @php
                                $actor_name = $notification->actor->name ?? ($notification->notifier->name ?? 'A user'); 
                                $comment_content = Str::limit($notification->comment->content ?? 'a comment', 50);
                                $post_content_preview = Str::limit($notification->post->content, 50);
                            @endphp
                            <strong>{{ $actor_name }}</strong> commented on your post "<strong>{{ $post_content_preview }}</strong>" with: "<strong>{{ $comment_content }}</strong>"
                        @else
                            {!! $notification->getNotificationMessage() !!}
                        @endif
                    </div>

                    @if($notification->post && !($notification->notification_type === 'social' && ($notification->type === 'comment' || $notification->type === 'reply')))
                        <div class="notification-post-preview">
                            "{{ Str::limit($notification->post->content, 100) }}"
                        </div>
                    @endif

                    @if($notification->comment && in_array($notification->type, ['like_comment', 'reply_comment']))
                        <div class="notification-post-preview">
                            <span class="material-icons" style="font-size: 13px; vertical-align: middle;">comment</span> "{{ Str::limit($notification->comment->content, 80) }}"
                        </div>
                    @endif
                </div>
            </div>
        @empty
            <div class="empty-state">
                @if($type == 'priority')
                    <span class="material-icons">location_off</span>
                    <h3>No Priority Reports</h3>
                    <p>When incidents occur in your area, they'll appear here as priority notifications</p>
                @elseif($type == 'general')
                    <span class="material-icons">public</span>
                    <h3>No General Reports</h3>
                    <p>Reports from other areas will appear here</p>
                @elseif($type == 'social')
                    <span class="material-icons">person_off</span>
                    <h3>No Social Notifications</h3>
                    <p>When someone interacts with your posts, you'll see notifications here</p>
                @else
                    <span class="material-icons">notifications_off</span>
                    <h3>No Notifications Yet</h3>
                    <p>You're all caught up! New notifications will appear here</p>
                @endif
            </div>
        @endforelse

        @if($notifications->hasPages())
            <div class="pagination">
                {{ $notifications->appends(request()->except('page'))->links() }}
            </div>
        @endif
    </div>
</div>

<style>
:root {
    --primary-color: #c82333;
    --priority-color: #FF5722;
    --general-color: #9C27B0;
    --social-color: #2196F3;
    --fire-color: #FF4444;
    --crime-color: #FF9800;
    --traffic-color: #4CAF50;
    --others-color: #607D8B;
}

.notifications-wrapper {
    max-width: 100%;
    margin: -2rem;
    padding: 2rem;
    background: #f8f9fa;
}

.notifications-wrapper .material-icons {
    font-family: 'Material Icons';
    font-weight: normal;
    font-style: normal;
    font-size: 24px;
    display: inline-block;
    line-height: 1;
    text-transform: none;
    letter-spacing: normal;
    word-wrap: normal;
    white-space: nowrap;
    direction: ltr;
    -webkit-font-smoothing: antialiased;
    text-rendering: optimizeLegibility;
    -moz-osx-font-smoothing: grayscale;
    font-feature-settings: 'liga';
}

.notifications-container {
    max-width: 750px; 
    margin: 0 auto;
    padding: 30px 20px;
}

.categories-tabs {
    background: #fff;
    border-radius: 12px;
    padding: 20px;
    margin-bottom: 20px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
}

.tabs-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 15px;
}

.tabs {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
}

.tab-btn {
    padding: 10px 20px;
    border: 2px solid #eee;
    background: #fff;
    border-radius: 25px;
    cursor: pointer;
    font-size: 14px;
    font-weight: 500;
    transition: all 0.3s;
    display: flex;
    align-items: center;
    gap: 6px;
}

.tab-btn.active {
    background: var(--primary-color);
    border-color: var(--primary-color);
    color: white;
}

.tab-count {
    background: rgba(255,255,255,0.2);
    padding: 2px 8px;
    border-radius: 12px;
    font-size: 12px;
    font-weight: 600;
}

.filter-section {
    flex-shrink: 0;
}

.filter-select {
    padding: 10px 20px;
    border: 2px solid #eee;
    border-radius: 25px;
    background: #fff;
    cursor: pointer;
    font-size: 14px;
    font-weight: 500;
    color: #333;
    appearance: menulist;
    -webkit-appearance: menulist;
    -moz-appearance: menulist;
    transition: all 0.3s;
    box-sizing: border-box; 
    line-height: normal;
}

.filter-select:hover {
    border-color: #ccc;
}

.filter-select:focus {
    border-color: var(--primary-color);
    outline: none;
}

.notifications-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 25px;
}

.notifications-count {
    font-size: 16px;
    font-weight: 500;
    color: #333;
}

.category-title {
    display: flex;
    align-items: center;
    gap: 8px;
}

.category-title .material-icons {
    font-size: 20px;
    vertical-align: middle;
}

.mark-all-read-btn {
    background: var(--primary-color);
    color: white;
    border: none;
    padding: 10px 20px;
    border-radius: 8px;
    cursor: pointer;
    font-size: 14px;
    font-weight: 500;
    transition: all 0.3s;
    display: flex;
    align-items: center;
    gap: 6px;
}

.mark-all-read-btn .material-icons {
    font-size: 18px;
}

.mark-all-read-btn:hover {
    background: #a91b2c;
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(200, 35, 51, 0.3);
}

.notification-meta-top {
    display: flex;
    justify-content: space-between; 
    align-items: center;
    margin-bottom: 10px; 
}

.notification-badges {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
    margin-top: 0; 
    align-items: center; 
}

.badge {
    padding: 4px 8px;
    border-radius: 12px;
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
    display: flex;
    align-items: center;
    gap: 4px;
}

.badge .material-icons {
    font-size: 11px !important;
}

.priority-badge {
    background: rgba(255, 87, 34, 0.1);
    color: var(--priority-color);
    border: 1px solid var(--priority-color);
}

.general-badge {
    background: rgba(156, 39, 176, 0.1);
    color: var(--general-color);
    border: 1px solid var(--general-color);
}

.social-badge {
    background: rgba(33, 150, 243, 0.1);
    color: var(--social-color);
    border: 1px solid var(--social-color);
}

.accident-badge {
    font-size: 10px;
    border: none;
    color: white;
}

.accident-fire {
    background: var(--fire-color);
}

.accident-crime {
    background: var(--crime-color);
}

.accident-traffic {
    background: var(--traffic-color);
}

.accident-others {
    background: var(--others-color);
}

.location-info {
    font-size: 13px;
    color: #666;
    margin-bottom: 0;
    display: flex;
    align-items: center;
    gap: 5px;
    padding-left: 8px;
    border-left: 1px solid #eee;
    line-height: 1;
}

.location-info.location-inline strong {
    font-weight: 500;
}

.location-info .material-icons {
    font-size: 16px;
    color: #999;
}

.notification-item {
    background: #fff;
    border-radius: 16px; 
    padding: 20px;
    padding-right: 120px; 
    margin-bottom: 15px;
    transition: all 0.3s ease;
    border-left: 4px solid transparent; 
    display: flex;
    align-items: flex-start;
    gap: 15px;
    cursor: pointer;
    position: relative; 
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1); 
}

.notification-item:hover {
    transform: translateY(-2px); 
    box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15); 
}

.notification-content {
    flex: 1;
    min-width: 0; 
}

.notification-message {
    font-size: 15px;
    margin-bottom: 8px;
    color: #1a1a1a;
    line-height: 1.4;
}

.notification-message strong {
    font-weight: 600;
    color: var(--primary-color);
}

.notification-post-preview {
    font-size: 13px;
    color: #999;
    margin-bottom: 8px;
    font-style: italic;
    overflow: hidden;
    text-overflow: ellipsis;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    line-height: 1.4;
}

.unread-dot {
    position: absolute;
    top: 15px;
    right: 15px;
    width: 12px;
    height: 12px;
    background: var(--primary-color);
    border-radius: 50%;
    z-index: 2;
    box-shadow: 0 0 0 3px rgba(200, 35, 51, 0.2);
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0%, 100% {
        box-shadow: 0 0 0 3px rgba(200, 35, 51, 0.2);
    }
    50% {
        box-shadow: 0 0 0 6px rgba(200, 35, 51, 0.1);
    }
}

.notification-time {
    position: absolute; 
    top: 20px; 
    right: 20px; 
    font-size: 12px;
    color: #666;
    display: flex;
    align-items: center;
    gap: 4px;
    flex-shrink: 0; 
    z-index: 1; 
}

.empty-state {
    text-align: center;
    padding: 60px 20px;
    color: #999;
}

.empty-state .material-icons {
    font-size: 60px;
    color: #ccc;
    margin-bottom: 15px;
}

.empty-state h3 {
    font-size: 20px;
    color: #999;
    margin-bottom: 10px;
    font-weight: 500;
}

.empty-state p {
    font-size: 14px;
    color: #aaa;
    max-width: 400px;
    margin: 0 auto;
    line-height: 1.6;
}

@media (max-width: 768px) {
    .notifications-wrapper {
        margin: -2rem;
        padding: 1rem;
    }

    .notification-item {
        padding: 15px; 
        padding-right: 15px; 
        flex-direction: column; 
        align-items: flex-start;
        border-radius: 12px; 
    }

    .notifications-container {
        max-width: 100%; 
        padding: 20px 15px;
    }

    .notification-content {
        padding-right: 0;
        width: 100%;
    }
    
    .notification-time {
        position: static; 
        order: -1; 
        align-self: flex-end; 
        margin-bottom: 5px;
        font-size: 11px;
        right: auto;
        top: auto;
    }

    .unread-dot {
        top: 10px;
        right: 10px;
    }

    .notification-meta-top {
        flex-direction: column; 
        align-items: flex-start;
        width: 100%;
        margin-bottom: 10px;
    }

    .notification-badges {
        flex-direction: column;
        align-items: flex-start;
        gap: 5px;
        margin-top: 0;
        width: 100%;
    }

    .location-info {
        padding-left: 0;
        border-left: none;
        width: 100%;
    }
    
    .filter-select {
        width: 100%; 
        box-sizing: border-box;
    }
}
</style>

<script>
(function() {
    'use strict';
    
    // Tab switching functionality
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const type = this.dataset.type;
            const url = new URL(window.location.href);
            
            // Set the type parameter
            url.searchParams.set('type', type);
            
            // Reset accident_type filter for non-relevant tabs
            if (type === 'all' || type === 'social') {
                url.searchParams.delete('accident_type');
            } else if (type === 'priority' || type === 'general') {
                // Keep current accident_type if it exists, otherwise set to 'all'
                if (!url.searchParams.has('accident_type')) {
                    url.searchParams.set('accident_type', 'all');
                }
            }
            
            window.location.href = url.toString();
        });
    });

    // Accident type filter functionality
    const accidentTypeFilter = document.getElementById('accidentTypeFilter');
    if (accidentTypeFilter) {
        accidentTypeFilter.addEventListener('change', function() {
            const url = new URL(window.location.href);
            const currentType = url.searchParams.get('type') || 'all';
            
            // Set accident_type parameter
            url.searchParams.set('accident_type', this.value);
            
            // Ensure we're on a tab that supports accident_type filtering
            if (currentType === 'all' || currentType === 'social') {
                url.searchParams.set('type', 'priority'); // Default to priority when filtering
            }
            
            window.location.href = url.toString();
        });
    }

    // Mark as read and navigate function
    window.markAsReadAndNavigate = function(notificationId, postUrl) {
        if (!postUrl || postUrl === '#' || postUrl === '') {
            console.log('No valid post URL');
            return;
        }

        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        
        if (csrfToken) {
            fetch(`/notifications/${notificationId}/mark-read`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({})
            })
            .then(response => {
                if (response.ok) {
                    window.location.href = postUrl;
                } else {
                    console.error('Failed to mark notification as read');
                    window.location.href = postUrl;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                window.location.href = postUrl;
            });
        } else {
            // Fallback if CSRF token not found
            window.location.href = postUrl;
        }
    };

    let autoRefreshInterval;
    let lastNotificationCount = {{ $notifications->count() }}; // Initial count from server
    
    function startSmartAutoRefresh() {
        autoRefreshInterval = setInterval(() => {
            // Check for new notifications via AJAX without full page reload
            fetch(window.location.href + '&check_only=1', {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.text())
            .then(html => {
                // Simple check - if the HTML contains notification items, count them
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                const currentCount = doc.querySelectorAll('.notification-item').length;
                
                if (currentCount !== lastNotificationCount) {
                    // Only reload if count changed
                    lastNotificationCount = currentCount;
                    window.location.reload();
                }
            })
            .catch(error => console.error('Error checking notifications:', error));
        }, 50000); // Check every 1 second, but only reload if changes
    }
    
    function stopSimpleAutoRefresh() {
        if (autoRefreshInterval) {
            clearInterval(autoRefreshInterval);
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        startSmartAutoRefresh();
    });

    window.addEventListener('beforeunload', stopSimpleAutoRefresh);

})();
</script>
@endsection