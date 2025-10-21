@extends('layouts.app')

@section('title', 'Notifications - PubL')

@section('content')
<div class="main-content">
    <!-- Profile Header -->
    <div class="profile-header">
        <h3>NOTIFICATIONS</h3>
    </div>

    <!-- Notifications Content -->
    <div class="notifications-container">
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
                    <button type="submit" class="mark-all-read-btn">
                        <i class="la la-check-double"></i> Mark All as Read
                    </button>
                </form>
            @endif
        </div>

        @forelse($notifications as $notification)
            <div class="notification-item {{ $notification->is_read ? '' : 'unread' }} 
                {{ $notification->notification_type }}-notification"
                onclick="markAsReadAndNavigate({{ $notification->id }}, '{{ $notification->post ? route('timeline') . '#post-' . $notification->post_id : '#' }}')">
                
                @if(!$notification->is_read)
                    <span class="unread-badge"></span>
                @endif
                
                <!-- Notification Icon -->
                <!-- <div class="notification-icon" style="background: {{ $notification->getIconColor() }}20; color: {{ $notification->getIconColor() }};">
                    <i class="{{ $notification->getIcon() }}"></i>
                </div> -->

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

                <!-- REMOVED DELETE BUTTON -->
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

        <!-- Pagination -->
        @if($notifications->hasPages())
            <div class="pagination">
                {{ $notifications->appends(request()->except('page'))->links() }}
            </div>
        @endif
    </div>
</div>

<style>
    /* === Enhanced Styles === */
    :root {
        --priority-color: #FF5722;
        --general-color: #9C27B0;
        --social-color: #2196F3;
        --fire-color: #FF4444;
        --crime-color: #FF9800;
        --traffic-color: #4CAF50;
        --others-color: #607D8B;
    }

    /* === Main Content === */
    .main-content {
        flex: 1;
        overflow-y: auto;
        padding: 0;
    }

    /* === Profile Header === */
    .profile-header {
        background: #FF0B55;
        text-align: center;
        padding: 60px 20px 30px;
        color: white;
        position: relative;
        box-shadow: 0 3px 10px rgba(0,0,0,0.1);
        margin-bottom: 0;
    }
    .profile-header h3 {
        font-weight: 700;
        letter-spacing: 1px;
        font-size: 2rem;
        margin: 0;
    }

    /* === Notifications Container === */
    .notifications-container {
        max-width: 900px;
        margin: 0 auto;
        padding: 30px 20px;
    }

    /* === Categories Tabs === */
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
        background: #FF0B55;
        border-color: #FF0B55;
        color: white;
    }

    .tab-btn:hover:not(.active) {
        border-color: #FF0B55;
        color: #FF0B55;
    }

    .tab-count {
        background: rgba(255,255,255,0.2);
        padding: 2px 8px;
        border-radius: 12px;
        font-size: 12px;
        font-weight: 600;
    }

    .tab-btn:not(.active) .tab-count {
        background: #f0f0f0;
        color: #666;
    }

    /* === Filter Section === */
    .filter-section {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .filter-select {
        padding: 8px 12px;
        border: 1px solid #ddd;
        border-radius: 8px;
        background: #fff;
        font-size: 14px;
        min-width: 160px;
    }

    .filter-select:focus {
        border-color: #FF0B55;
        outline: none;
    }

    /* === Notifications Header === */
    .notifications-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 25px;
        padding: 0;
        background: transparent;
        box-shadow: none;
    }

    .notifications-count {
        font-size: 18px;
        color: #333;
        font-weight: 500;
    }

    .notifications-count .count {
        color: #FF0B55;
        font-weight: 600;
    }

    .category-title {
        font-weight: 600;
        color: #333;
        font-size: 18px;
    }

    .mark-all-read-btn {
        background: #FF0B55;
        color: white;
        border: none;
        padding: 10px 20px;
        border-radius: 8px;
        cursor: pointer;
        font-size: 14px;
        font-weight: 500;
        transition: all 0.3s;
    }

    .mark-all-read-btn:hover {
        background: #e00a4a;
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(255, 11, 85, 0.3);
    }

    /* === Notification Badges === */
    .notification-badges {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
        margin-top: 8px;
    }

    .badge {
        padding: 4px 8px;
        border-radius: 12px;
        font-size: 11px;
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

    /* === Notification Header === */
    .notification-header {
        margin-bottom: 8px;
    }

    /* === Location Info === */
    .location-info {
        font-size: 13px;
        color: #666;
        margin-bottom: 8px;
        display: flex;
        align-items: center;
        gap: 5px;
    }

    .location-info strong {
        color: #333;
    }

    /* === Notification Type Styles === */
    .priority-notification {
        border-left-color: var(--priority-color) !important;
    }

    .priority-notification.unread {
        background: rgba(255, 87, 34, 0.05);
    }

    .general-notification {
        border-left-color: var(--general-color) !important;
    }

    .general-notification.unread {
        background: rgba(156, 39, 176, 0.05);
    }

    .social-notification {
        border-left-color: var(--social-color) !important;
    }

    .social-notification.unread {
        background: rgba(33, 150, 243, 0.05);
    }

    /* === Enhanced Notification Item === */
    .notification-item {
        background: #fff;
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 15px;
        transition: all 0.3s ease;
        border-left: 4px solid transparent;
        display: flex;
        align-items: flex-start;
        gap: 15px;
        cursor: pointer;
        position: relative;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    }

    .notification-item.unread {
        background: rgba(255, 11, 85, 0.05);
    }

    .notification-item:hover {
        transform: translateX(5px);
        box-shadow: 0 4px 14px rgba(0,0,0,0.08);
    }

    .notification-icon {
        width: 45px;
        height: 45px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
        flex-shrink: 0;
    }

    .notification-content {
        flex: 1;
        min-width: 0; /* Prevent overflow */
    }

    .notification-message {
        font-size: 15px;
        margin-bottom: 8px;
        color: #1a1a1a;
        line-height: 1.4;
    }

    .notification-message .actor-name {
        font-weight: 600;
        color: #FF0B55;
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

    .notification-time {
        font-size: 12px;
        color: #666;
        margin-top: 5px;
    }

    .unread-badge {
        position: absolute;
        top: 20px;
        right: 20px;
        width: 10px;
        height: 10px;
        background: #FF0B55;
        border-radius: 50%;
        animation: pulse 2s infinite;
    }

    @keyframes pulse {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.5; }
    }

    /* === Empty State === */
    .empty-state {
        text-align: center;
        padding: 60px 20px;
        color: #666;
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        margin-top: 20px;
    }

    .empty-state i {
        font-size: 60px;
        color: #ccc;
        margin-bottom: 20px;
    }

    .empty-state h3 {
        font-size: 24px;
        margin-bottom: 12px;
        color: #666;
    }

    .empty-state p {
        font-size: 15px;
        color: #999;
        line-height: 1.5;
    }

    /* === Success Message === */
    .success-message {
        background: rgba(76, 175, 80, 0.1);
        color: #4CAF50;
        padding: 15px;
        border-radius: 8px;
        margin-bottom: 20px;
        border-left: 4px solid #4CAF50;
    }

    /* === Pagination === */
    .pagination {
        display: flex;
        justify-content: center;
        gap: 10px;
        margin-top: 30px;
    }

    .pagination a, .pagination span {
        padding: 8px 12px;
        background: #fff;
        border: 1px solid #eee;
        border-radius: 8px;
        color: #666;
        text-decoration: none;
        transition: all 0.3s;
        font-size: 14px;
    }

    .pagination a:hover {
        background: #FF0B55;
        border-color: #FF0B55;
        color: #fff;
    }

    .pagination .active {
        background: #FF0B55;
        border-color: #FF0B55;
        color: #fff;
    }

    /* === Responsive === */
    @media (max-width: 768px) {
        .profile-header {
            padding: 40px 20px 20px;
        }
        
        .profile-header h3 {
            font-size: 1.5rem;
        }

        .notifications-container {
            padding: 20px 15px;
        }

        .tabs-header {
            flex-direction: column;
            align-items: stretch;
            gap: 15px;
        }

        .tabs {
            justify-content: center;
        }

        .filter-section {
            justify-content: center;
        }

        .notifications-header {
            flex-direction: column;
            gap: 15px;
            align-items: flex-start;
        }

        .notification-badges {
            justify-content: flex-start;
        }

        .notification-header {
            flex-direction: column;
            gap: 8px;
        }

        .notification-item {
            padding: 15px;
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
            gap: 5px;
        }

        .empty-state {
            padding: 40px 15px;
        }

        .empty-state i {
            font-size: 50px;
        }

        .empty-state h3 {
            font-size: 20px;
        }
    }
</style>

<script>
    // Tab Switching - SIMPLE FIX
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const type = this.dataset.type;
            
            // Simple URL update
            const url = new URL(window.location.href);
            url.searchParams.set('type', type);
            
            // Navigate to the new URL
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