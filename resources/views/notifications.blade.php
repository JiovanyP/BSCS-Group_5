@extends('layouts.app')

@section('title', 'Notifications - PubL')

@section('content')
<div class="post-container" role="main" aria-labelledby="notificationsTitle">
    <h1 id="notificationsTitle"><strong>Notifications</strong></h1>
    <div class="subtitle">Stay updated with recent activities</div>

    @if(session('success'))
        <div class="success-message">
            {{ session('success') }}
        </div>
    @endif

    <!-- Notification Categories Tabs -->
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
            
            <!-- Accident Type Filter - Only show for Priority and General -->
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

    <!-- Notifications Header -->
    <div class="notifications-header">
        <div class="notifications-count">
            @if($type == 'all')
                You have <span class="count">{{ $unreadCount }}</span> unread notification{{ $unreadCount != 1 ? 's' : '' }}
            @else
                <span class="category-title">
                    @if($type == 'priority') 
                        🚨 Priority Reports in Your Area
                    @elseif($type == 'general')
                        📍 General Reports
                    @elseif($type == 'social')
                        💬 Social Interactions
                    @else
                        All Notifications
                    @endif
                </span>
            @endif
        </div>
        <!-- ONLY SHOW IN ALL TAB -->
        @if($unreadCount > 0 && $type == 'all')
            <form method="POST" action="{{ route('notifications.markAllRead') }}" style="display: inline;">
                @csrf
                <button type="submit" class="btn btn-primary mark-all-read-btn">
                    Mark All as Read
                </button>
            </form>
        @endif
    </div>

    <div class="notifications-list">
        @forelse($notifications as $notification)
            <div class="notification-item {{ $notification->is_read ? '' : 'unread' }} 
                {{ $notification->notification_type }}-notification"
                onclick="markAsReadAndNavigate({{ $notification->id }}, '{{ $notification->post ? route('timeline') . '#post-' . $notification->post_id : '#' }}')">
                
                @if(!$notification->is_read)
                    <span class="unread-badge"></span>
                @endif

                <div class="notification-content">
                    <!-- Notification Header -->
                    <div class="notification-header">
                        <div class="notification-message">
                            {!! $notification->getNotificationMessage() !!}
                        </div>
                        <div class="notification-badges">
                            @if($notification->notification_type === 'priority')
                                <span class="badge priority-badge">
                                    <i class="la la-exclamation-triangle"></i> Priority
                                </span>
                            @elseif($notification->notification_type === 'general')
                                <span class="badge general-badge">
                                    <i class="la la-map-marker-alt"></i> General
                                </span>
                            @elseif($notification->notification_type === 'social')
                                <span class="badge social-badge">
                                    <i class="la la-users"></i> Social
                                </span>
                            @endif
                            
                            @if($notification->accident_type && in_array($notification->notification_type, ['priority', 'general']))
                                <span class="badge accident-badge accident-{{ strtolower($notification->accident_type) }}">
                                    {{ $notification->accident_type }}
                                </span>
                            @endif
                        </div>
                    </div>

                    <!-- Location/Distance Info - Only show for Priority and General -->
                    @if($notification->distance_km !== null && in_array($notification->notification_type, ['priority', 'general']) && $notification->post)
                        <div class="location-info">
                            <i class="la la-map-marker"></i>
                            @if($notification->distance_km == 0)
                                <strong>Reported in your area: {{ $notification->post->location }}</strong>
                            @else
                                <strong>Reported in: {{ $notification->post->location }}</strong>
                            @endif
                        </div>
                    @endif

                    <!-- Post/Comment Content -->
                    @if($notification->post)
                        <div class="notification-post-preview">
                            "{{ Str::limit($notification->post->content, 100) }}"
                        </div>
                    @endif

                    @if($notification->comment && in_array($notification->type, ['comment', 'reply']))
                        <div class="notification-post-preview">
                            <i class="la la-comment"></i> "{{ Str::limit($notification->comment->content, 80) }}"
                        </div>
                    @endif

                    <!-- Timestamp -->
                    <div class="notification-time">
                        <i class="la la-clock"></i> {{ $notification->created_at->diffForHumans() }}
                    </div>
                </div>
            </div>
        @empty
            <div class="empty-state">
                @if($type == 'priority')
                    <i class="la la-map-marker-slash"></i>
                    <h3>No Priority Reports</h3>
                    <p>When incidents occur in your area, they'll appear here as priority notifications</p>
                @elseif($type == 'general')
                    <i class="la la-globe"></i>
                    <h3>No General Reports</h3>
                    <p>Reports from other areas will appear here</p>
                @elseif($type == 'social')
                    <i class="la la-bell-slash"></i>
                    <h3>No Social Notifications</h3>
                    <p>When someone interacts with your posts, you'll see notifications here</p>
                @else
                    <i class="la la-bell-slash"></i>
                    <h3>No Notifications Yet</h3>
                    <p>You're all caught up! New notifications will appear here</p>
                @endif
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($notifications->hasPages())
        <div class="pagination">
            {{ $notifications->appends(request()->except('page'))->links() }}
        </div>
    @endif

    <!-- <a href="{{ route('timeline') }}" class="btn btn-secondary" style="margin-top: 20px;">Back to Timeline</a> -->
</div>

<style>
    :root {
        --accent: #CF0F47;
        --accent-2: #FF0B55;
        --card-bg: #ffffff;
        --muted: #666;
        --priority-color: #FF5722;
        --general-color: #9C27B0;
        --social-color: #2196F3;
        --fire-color: #FF4444;
        --crime-color: #FF9800;
        --traffic-color: #4CAF50;
        --others-color: #607D8B;
    }

    .post-container {
        width: 800px; /* Increased from 600px to 800px */
        max-width: calc(100% - 40px);
        background: var(--card-bg);
        border-radius: 16px;
        padding: 40px; /* Increased from 36px */
        box-shadow: 0 12px 40px rgba(0,0,0,0.1);
        margin: 0 auto;
    }

    .post-container h1 {
        margin: 0 0 16px 0; /* Increased margin */
        color: var(--accent);
        font-size: 28px; /* Slightly larger */
        letter-spacing: 0.2px;
        text-align: center;
    }

    .subtitle {
        color: var(--muted);
        margin-bottom: 24px; /* Increased margin */
        font-size: 15px; /* Slightly larger */
        text-align: center;
    }

    /* Categories Tabs */
    .categories-tabs {
        background: #fff;
        border-radius: 12px;
        padding: 25px; /* Increased padding */
        margin-bottom: 25px; /* Increased margin */
        border: 1px solid #eee;
    }

    .tabs-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 18px; /* Increased gap */
    }

    .tabs {
        display: flex;
        gap: 10px; /* Increased gap */
        flex-wrap: wrap;
    }

    .tab-btn {
        padding: 12px 22px; /* Increased padding */
        border: 2px solid #eee;
        background: #fff;
        border-radius: 25px;
        cursor: pointer;
        font-size: 15px; /* Slightly larger */
        font-weight: 500;
        transition: all 0.3s;
        display: flex;
        align-items: center;
        gap: 8px; /* Increased gap */
    }

    .tab-btn.active {
        background: var(--accent);
        border-color: var(--accent);
        color: white;
    }

    .tab-btn:hover:not(.active) {
        border-color: var(--accent);
        color: var(--accent);
    }

    .tab-count {
        background: rgba(255,255,255,0.2);
        padding: 3px 10px; /* Increased padding */
        border-radius: 12px;
        font-size: 13px; /* Slightly larger */
        font-weight: 600;
    }

    .tab-btn:not(.active) .tab-count {
        background: #f0f0f0;
        color: #666;
    }

    /* Filter Section */
    .filter-section {
        display: flex;
        align-items: center;
        gap: 12px; /* Increased gap */
    }

    .filter-select {
        padding: 10px 14px; /* Increased padding */
        border: 1px solid #ddd;
        border-radius: 8px;
        background: #fff;
        font-size: 15px; /* Slightly larger */
        min-width: 180px; /* Slightly wider */
    }

    .filter-select:focus {
        border-color: var(--accent);
        outline: none;
        box-shadow: 0 0 0 3px rgba(207, 15, 71, 0.1);
    }

    /* Notifications Header */
    .notifications-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 30px; /* Increased margin */
        padding: 0;
        background: transparent;
    }

    .notifications-count {
        font-size: 18px; /* Slightly larger */
        color: #333;
        font-weight: 500;
    }

    .notifications-count .count {
        color: var(--accent);
        font-weight: 600;
        font-size: 20px; /* Slightly larger */
    }

    .category-title {
        font-weight: 600;
        color: #333;
        font-size: 18px; /* Slightly larger */
    }

    /* Buttons */
    .post-container .btn {
        display: block;
        padding: 14px 20px; /* Increased padding */
        border-radius: 10px;
        border: 0;
        font-weight: 700;
        cursor: pointer;
        font-size: 15px; /* Slightly larger */
        transition: 0.25s;
        text-align: center;
        text-decoration: none;
        box-sizing: border-box;
        min-width: 140px; /* Minimum width */
    }

    .post-container .btn-primary {
        background: var(--accent);
        color: #fff;
    }

    .post-container .btn-primary:hover {
        background: var(--accent-2);
    }

    .post-container .btn-secondary {
        background: #eee;
        color: #444;
    }

    .post-container .btn-secondary:hover {
        background: #ddd;
    }

    .mark-all-read-btn {
        width: auto;
        display: inline-block;
    }

    /* Notification Items */
    .notifications-list {
        margin-bottom: 25px; /* Increased margin */
    }

    .notification-item {
        background: #fff;
        border-radius: 12px;
        padding: 25px; /* Increased padding */
        margin-bottom: 18px; /* Increased margin */
        transition: all 0.3s ease;
        border-left: 4px solid transparent;
        cursor: pointer;
        position: relative;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        border: 1px solid #eee;
    }

    .notification-item.unread {
        background: rgba(255, 11, 85, 0.05);
        border-left-color: var(--accent);
    }

    .notification-item:hover {
        transform: translateX(5px);
        box-shadow: 0 4px 14px rgba(0,0,0,0.08);
    }

    .notification-content {
        flex: 1;
        min-width: 0;
    }

    .notification-message {
        font-size: 16px; /* Slightly larger */
        margin-bottom: 10px; /* Increased margin */
        color: #1a1a1a;
        line-height: 1.5; /* Better line height */
    }

    .notification-message .actor-name {
        font-weight: 600;
        color: var(--accent);
    }

    /* Notification Badges */
    .notification-badges {
        display: flex;
        gap: 10px; /* Increased gap */
        flex-wrap: wrap;
        margin-top: 10px; /* Increased margin */
    }

    .badge {
        padding: 5px 10px; /* Increased padding */
        border-radius: 12px;
        font-size: 12px; /* Slightly larger */
        font-weight: 600;
        text-transform: uppercase;
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
        font-size: 11px; /* Slightly larger */
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

    /* Location Info */
    .location-info {
        font-size: 14px; /* Slightly larger */
        color: #666;
        margin-bottom: 10px; /* Increased margin */
        display: flex;
        align-items: center;
        gap: 6px; /* Increased gap */
    }

    .location-info strong {
        color: #333;
    }

    /* Notification Preview */
    .notification-post-preview {
        font-size: 14px; /* Slightly larger */
        color: #999;
        margin-bottom: 10px; /* Increased margin */
        font-style: italic;
        overflow: hidden;
        text-overflow: ellipsis;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        line-height: 1.5; /* Better line height */
    }

    .notification-time {
        font-size: 13px; /* Slightly larger */
        color: #666;
        margin-top: 8px; /* Increased margin */
    }

    .unread-badge {
        position: absolute;
        top: 25px; /* Adjusted position */
        right: 25px; /* Adjusted position */
        width: 12px; /* Slightly larger */
        height: 12px; /* Slightly larger */
        background: var(--accent);
        border-radius: 50%;
        animation: pulse 2s infinite;
    }

    @keyframes pulse {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.5; }
    }

    /* Empty State */
    .empty-state {
        text-align: center;
        padding: 70px 25px; /* Increased padding */
        color: #666;
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        margin-top: 25px; /* Increased margin */
        border: 1px solid #eee;
    }

    .empty-state i {
        font-size: 70px; /* Larger icon */
        color: #ccc;
        margin-bottom: 25px; /* Increased margin */
    }

    .empty-state h3 {
        font-size: 22px; /* Slightly larger */
        margin-bottom: 15px; /* Increased margin */
        color: #666;
    }

    .empty-state p {
        font-size: 15px; /* Slightly larger */
        color: #999;
        line-height: 1.6; /* Better line height */
    }

    /* Success Message */
    .success-message {
        background: rgba(76, 175, 80, 0.1);
        color: #4CAF50;
        padding: 18px; /* Increased padding */
        border-radius: 8px;
        margin-bottom: 25px; /* Increased margin */
        border-left: 4px solid #4CAF50;
        font-size: 15px; /* Slightly larger */
    }

    /* Pagination */
    .pagination {
        display: flex;
        justify-content: center;
        gap: 12px; /* Increased gap */
        margin-top: 35px; /* Increased margin */
    }

    .pagination a, .pagination span {
        padding: 10px 15px; /* Increased padding */
        background: #fff;
        border: 1px solid #eee;
        border-radius: 8px;
        color: #666;
        text-decoration: none;
        transition: all 0.3s;
        font-size: 15px; /* Slightly larger */
    }

    .pagination a:hover {
        background: var(--accent);
        border-color: var(--accent);
        color: #fff;
    }

    .pagination .active {
        background: var(--accent);
        border-color: var(--accent);
        color: #fff;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .post-container {
            padding: 30px 25px; /* Adjusted padding */
        }

        .tabs-header {
            flex-direction: column;
            align-items: stretch;
            gap: 18px; /* Increased gap */
        }

        .tabs {
            justify-content: center;
        }

        .filter-section {
            justify-content: center;
        }

        .notifications-header {
            flex-direction: column;
            gap: 18px; /* Increased gap */
            align-items: flex-start;
        }

        .mark-all-read-btn {
            width: 100%;
        }
    }

    @media (max-width: 480px) {
        .tabs {
            flex-direction: column;
            width: 100%;
        }

        .tab-btn {
            justify-content: center;
            width: 100%;
        }

        .notification-badges {
            flex-direction: column;
            align-items: flex-start;
            gap: 8px; /* Increased gap */
        }
    }
</style>

<script>
    // Tab Switching
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const type = this.dataset.type;
            
            const url = new URL(window.location.href);
            url.searchParams.set('type', type);
            
            window.location.href = url.toString();
        });
    });

    // Accident Type Filter
    const accidentTypeFilter = document.getElementById('accidentTypeFilter');
    if (accidentTypeFilter) {
        accidentTypeFilter.addEventListener('change', function() {
            const url = new URL(window.location.href);
            url.searchParams.set('accident_type', this.value);
            window.location.href = url.toString();
        });
    }

    // Mark as read and navigate to post
    function markAsReadAndNavigate(notificationId, postUrl) {
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        fetch(`/notifications/${notificationId}/mark-read`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            }
        }).then(response => {
            if (response.ok && postUrl !== '#') {
                window.location.href = postUrl;
            }
        });
    }
</script>
@endsection