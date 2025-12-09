@extends('layouts.app')

@section('title', 'Notifications - Publ')

@section('content')
<div class="stack-layout-grid">
    <div class="notifications-main-column">
        
        {{-- Success Toast --}}
        @if(session('success'))
            <div class="stack-banner success color-general">
                <span class="material-icons">check_circle</span>
                <span>{{ session('success') }}</span>
            </div>
        @endif
    
        {{-- Warning Card (Location) --}}
        @if(auth()->user() && empty(auth()->user()->location))
            <div class="stack-card warning-type ripple" onclick="loadEditModal()">
                <div class="card-accent-stripe"></div>
                <div class="card-content-wrapper">
                    <div class="leading-visual">
                         <div class="avatar-icon warning-icon">
                            <span class="material-icons">warning</span>
                        </div>
                    </div>
                    <div class="item-content">
                         <div class="headline">Location needed</div>
                        <div class="subhead">
                            Add your location to receive local priority alerts.
                             <button class="text-button warning-text" onclick="loadEditModal(); event.stopPropagation();">Edit Profile</button>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        {{-- Colored Filter Chips --}}
        <div class="filter-chips-wrapper">
            <div class="scroll-container">
                <button class="filter-chip color-all {{ $type == 'all' ? 'selected' : '' }}" data-type="all">
                    <span>All</span>
                </button>
                <button class="filter-chip color-priority {{ $type == 'priority' ? 'selected' : '' }}" data-type="priority">
                    <span>Priority</span>
                </button>
                 <button class="filter-chip color-general {{ $type == 'general' ? 'selected' : '' }}" data-type="general">
                    <span>General</span>
                </button>
                <button class="filter-chip color-social {{ $type == 'social' ? 'selected' : '' }}" data-type="social">
                    <span>Social</span>
                </button>

                @if(in_array($type, ['priority', 'general']))
                    <div class="separator"></div>
                    <select id="accidentTypeFilter" class="filter-chip-select color-{{ $type }}">
                        <option value="all" {{ $accidentType == 'all' ? 'selected' : '' }}>All Types</option>
                        <option value="Fire" {{ $accidentType == 'Fire' ? 'selected' : '' }}>Fire</option>
                        <option value="Crime" {{ $accidentType == 'Crime' ? 'selected' : '' }}>Crime</option>
                        <option value="Traffic" {{ $accidentType == 'Traffic' ? 'selected' : '' }}>Traffic</option>
                        <option value="Others" {{ $accidentType == 'Others' ? 'selected' : '' }}>Others</option>
                    </select>
                @endif
            </div>
        </div>

        {{-- Header --}}
        <div class="section-header">
            <div class="header-title">
                @if($type == 'priority') Priority Reports
                @elseif($type == 'general') General Reports
                @elseif($type == 'social') Social Activity
                @else Notifications
                @endif
                
                @if($unreadCount > 0)
                    <span class="badge-count">{{ $unreadCount }}</span>
                @endif
            </div>

            @if($unreadCount > 0 && $type == 'all')
                <form method="POST" action="{{ route('notifications.markAllRead') }}">
                    @csrf
                    <button type="submit" class="mark-read-btn">
                        Mark all read
                    </button>
                </form>
            @endif
        </div>

        {{-- Stacked Notification List --}}
        <div class="notifications-stack">
            @forelse($notifications as $notification)
                {{-- 
                    Adding specific color classes based on type: 
                    color-priority, color-social, or color-general 
                --}}
                <div class="stack-card color-{{ $notification->notification_type }} {{ $notification->is_read ? 'read' : 'unread' }} ripple"
                     onclick="markAsReadAndNavigate({{ $notification->id }}, '{{ $notification->post ? route('posts.view', $notification->post_id) : '#' }}')">
                    
                    {{-- Colored Accent Stripe on left --}}
                    <div class="card-accent-stripe"></div>

                    <div class="card-content-wrapper">
                        {{-- Leading Icon --}}
                        <div class="leading-visual">
                            <div class="avatar-icon">
                                @if($notification->notification_type === 'priority')
                                    <span class="material-icons">emergency</span>
                                @elseif($notification->notification_type === 'social')
                                    <span class="material-icons">person</span>
                                @else
                                    <span class="material-icons">notifications</span>
                                @endif
                            </div>
                        </div>

                        {{-- Main Content --}}
                        <div class="item-content">
                            <div class="item-header">
                                <span class="category-label">
                                    {{ ucfirst($notification->notification_type) }}
                                    @if($notification->accident_type) • {{ $notification->accident_type }} @endif
                                </span>
                                <span class="time-stamp">{{ $notification->created_at->diffForHumans(null, true, true) }}</span>
                            </div>
                            
                            <div class="item-body">
                                @if ($notification->notification_type === 'social' && ($notification->type === 'comment' || $notification->type === 'reply') && $notification->post && $notification->comment)
                                    @php
                                        $actor_name = $notification->actor->name ?? ($notification->notifier->name ?? 'User'); 
                                        $comment_snippet = Str::limit($notification->comment->content ?? '', 50);
                                        $post_snippet = Str::limit($notification->post->content ?? '', 30);
                                    @endphp
                                    <strong>{{ $actor_name }}</strong> commented on "<em>{{ $post_snippet }}</em>": "{{ $comment_snippet }}"
                                @else
                                    {!! $notification->getNotificationMessage() !!}
                                @endif
                            </div>

                           @if($notification->post && !($notification->notification_type === 'social' && ($notification->type === 'comment' || $notification->type === 'reply')))
                                 <div class="item-footer">
                                    <span class="material-icons icon-tiny">description</span> {{ Str::limit($notification->post->content, 60) }}
                                 </div>
                            @endif

                            @if($notification->post && in_array($notification->notification_type, ['priority', 'general']))
                                <div class="item-footer">
                                    <span class="material-icons icon-tiny">place</span> {{ $notification->post->location ?? 'Unknown location' }}
                                </div>
                            @endif
                        </div>
                        
                        {{-- Trailing Unread Dot --}}
                        @if(!$notification->is_read)
                            <div class="trailing-visual">
                                <div class="unread-dot"></div>
                            </div>
                        @endif
                    </div>
                </div>
            @empty
                <div class="empty-stack-state">
                    <div class="illustration color-{{ $type == 'all' ? 'general' : $type }}">
                        @if($type == 'priority') <span class="material-icons">location_off</span>
                        @elseif($type == 'social') <span class="material-icons">person_off</span>
                        @else <span class="material-icons">inbox</span>
                        @endif
                    </div>
                    <div class="text">
                        @if($type == 'priority') No priority reports nearby
                        @elseif($type == 'social') No new social interactions
                        @else You are all caught up
                        @endif
                    </div>
                </div>
            @endforelse
        </div>

        @if($notifications->hasPages())
            <div class="pagination-wrapper">
                {{ $notifications->appends(request()->except('page'))->links() }}
            </div>
        @endif

    </div>
</div>

<style>
/* --- THEME COLOR DEFINITIONS --- */
:root {
    /* Base Grays */
    --stack-bg: #f0f2f5;
    --card-bg: #ffffff;
    --text-primary: #1c1e21;
    --text-secondary: #65676b;
    
    /* Priority Theme (Red/Orange) */
    --theme-priority-color: #d93025;
    --theme-priority-bg: #fce8e6;
    --theme-priority-hover: #fdf3f2;
    
    /* Social Theme (Blue) */
    --theme-social-color: #1877f2;
    --theme-social-bg: #e7f3ff;
    --theme-social-hover: #f0f7ff;

    /* General Theme (Purple/Neutral) */
    --theme-general-color: #9334e6;
    --theme-general-bg: #f3e8fd;
    --theme-general-hover: #f8f2fe;

    /* All/Default Theme */
    --theme-all-color: #65676b;
    --theme-all-bg: #e4e6eb;
}

/* Apply themes based on class usage */
.color-priority { --theme-color: var(--theme-priority-color); --theme-bg: var(--theme-priority-bg); --theme-hover: var(--theme-priority-hover); }
.color-social { --theme-color: var(--theme-social-color); --theme-bg: var(--theme-social-bg); --theme-hover: var(--theme-social-hover); }
.color-general { --theme-color: var(--theme-general-color); --theme-bg: var(--theme-general-bg); --theme-hover: var(--theme-general-hover); }
.color-all { --theme-color: var(--theme-all-color); --theme-bg: var(--theme-all-bg); --theme-hover: #ced0d4; }

/* --- LAYOUT --- */
.stack-layout-grid {
    display: flex;
    justify-content: center;
    padding: 1.5rem 1rem;
    background-color: var(--stack-bg);
    min-height: 90vh;
    font-family: 'Roboto', -apple-system, BlinkMacSystemFont, "Segoe UI", Helvetica, Arial, sans-serif;
}

.notifications-main-column {
    width: 100%;
    max-width: 600px; /* Slightly narrower for better stack card feel */
    display: flex;
    flex-direction: column;
    gap: 12px;
}

/* --- FILTER CHIPS (Colorful) --- */
.filter-chips-wrapper {
    overflow-x: auto;
    padding: 4px 0 12px 0;
    scrollbar-width: none; 
    -ms-overflow-style: none;
}
.filter-chips-wrapper::-webkit-scrollbar { display: none; }

.scroll-container {
    display: flex;
    gap: 10px;
    align-items: center;
    width: max-content;
}

.filter-chip {
    display: inline-flex;
    align-items: center;
    height: 36px;
    padding: 0 18px;
    border-radius: 18px;
    border: none;
    background: var(--card-bg);
    color: var(--text-secondary);
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s ease;
    white-space: nowrap;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
}

.filter-chip:hover {
    background-color: #e4e6eb;
    color: var(--text-primary);
}

/* Selected state uses the theme color */
.filter-chip.selected {
    background-color: var(--theme-color);
    color: white;
    box-shadow: 0 2px 5px rgba(0,0,0,0.2);
}

.separator {
    width: 1px;
    height: 24px;
    background-color: #ccc;
    margin: 0 4px;
}

.filter-chip-select {
    height: 36px;
    border-radius: 18px;
    padding: 0 16px;
    border: 1px solid #ddd;
    background: var(--card-bg);
    color: var(--text-secondary);
    font-size: 14px;
    font-weight: 500;
    cursor: pointer;
}
.filter-chip-select:focus { border-color: var(--theme-color); outline: none; }

/* --- HEADERS & BUTTONS --- */
.section-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0 8px;
    margin: 8px 0;
}
.header-title {
    font-size: 15px;
    font-weight: 700;
    color: var(--text-primary);
    display: flex; align-items: center; gap: 8px;
}
.badge-count {
    background: var(--theme-priority-color);
    color: white;
    font-size: 11px; padding: 2px 8px;
    border-radius: 10px; font-weight: bold;
}
.mark-read-btn {
    background: transparent; border: none;
    color: var(--theme-social-color);
    font-weight: 600; font-size: 13px;
    cursor: pointer;
}

/* --- THE STACKED CARDS --- */
.notifications-stack {
    display: flex;
    flex-direction: column;
    gap: 10px; /* Gap between cards in the stack */
}

.stack-card {
    position: relative;
    background: var(--card-bg);
    border-radius: 16px; /* Rounded stack corners */
    box-shadow: 0 2px 8px rgba(0,0,0,0.08), 0 1px 2px rgba(0,0,0,0.05); /* Depth shadow */
    overflow: hidden;
    cursor: pointer;
    transition: transform 0.2s cubic-bezier(0.34, 1.56, 0.64, 1), box-shadow 0.2s ease;
    display: flex;
    align-items: stretch;
}

.stack-card:hover {
    transform: translateY(-3px) scale(1.01); /* Lift effect on hover */
    box-shadow: 0 6px 15px rgba(0,0,0,0.12), 0 2px 4px rgba(0,0,0,0.08);
    z-index: 2;
}

/* The colored stripe on the left */
.card-accent-stripe {
    width: 6px;
    background-color: var(--theme-color);
    flex-shrink: 0;
}

/* Container for the actual content next to the stripe */
.card-content-wrapper {
    flex: 1;
    display: flex;
    align-items: flex-start;
    padding: 16px 16px 16px 12px;
    gap: 14px;
}

/* Unread State: Subtle background tint based on theme */
.stack-card.unread {
    background-color: var(--theme-bg);
}
.stack-card.unread .item-body {
     color: var(--text-primary);
     font-weight: 500;
}


/* Card Internal Elements */
.leading-visual { flex-shrink: 0; }

.avatar-icon {
    width: 44px; height: 44px;
    border-radius: 12px; /* Soft square look for icons */
    display: flex; align-items: center; justify-content: center;
    /* Icon background depends on theme */
    background-color: var(--theme-bg);
    color: var(--theme-color);
}
.avatar-icon .material-icons { font-size: 22px; }


.item-content {
    flex: 1;
    display: flex; flex-direction: column; gap: 5px;
    min-width: 0;
}

.item-header {
    display: flex; justify-content: space-between;
    font-size: 12px; color: var(--text-secondary);
}
.category-label {
    font-weight: 700; letter-spacing: 0.5px;
    font-size: 10px; text-transform: uppercase;
    color: var(--theme-color); /* Label matches theme */
}

.item-body {
    font-size: 15px; color: var(--text-primary);
    line-height: 1.4;
    word-wrap: break-word;
}
.item-body strong { font-weight: 700; }

.item-footer {
    font-size: 12px; color: var(--text-secondary);
    display: flex; align-items: center; gap: 5px;
    margin-top: 2px;
    white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
}
.icon-tiny { font-size: 14px; opacity: 0.7; }


/* Unread Dot */
.trailing-visual {
    display: flex; align-items: flex-start;
    padding-top: 5px;
}
.unread-dot {
    width: 10px; height: 10px;
    border-radius: 50%;
    background-color: var(--theme-color); /* Dot matches theme */
    box-shadow: 0 0 0 2px var(--card-bg);
}
.stack-card.unread .unread-dot {
    box-shadow: 0 0 0 2px var(--theme-bg);
}


/* --- SPECIAL CARDS (Warning/Success) --- */
.stack-card.warning-type {
    --theme-color: var(--theme-priority-color);
    background: #fff5f4;
}
.warning-icon { background: white; }
.headline { font-weight: 700; font-size: 16px; margin-bottom: 4px; }
.subhead { font-size: 14px; color: var(--text-secondary); }
.warning-text { 
    background: none; border: none; font-weight: 700; 
    color: var(--theme-priority-color); cursor: pointer; 
    padding: 0; margin-left: 5px;
}

.stack-banner {
    padding: 12px 16px; border-radius: 12px;
    display: flex; align-items: center; gap: 12px;
    font-weight: 600; font-size: 14px;
    background: var(--theme-bg); color: var(--theme-color);
    margin-bottom: 10px;
}

/* --- EMPTY STATE --- */
.empty-stack-state {
    text-align: center; padding: 60px 0;
    color: var(--text-secondary);
}
.empty-stack-state .illustration {
    width: 80px; height: 80px;
    border-radius: 50%; background: var(--theme-bg);
    color: var(--theme-color);
    display: flex; align-items: center; justify-content: center;
    margin: 0 auto 20px auto;
}
.empty-stack-state .material-icons { font-size: 40px; }
.empty-stack-state .text { font-size: 17px; font-weight: 600; }

/* --- PAGINATION --- */
.pagination-wrapper {
    margin-top: 24px; display: flex; justify-content: center;
}
.pagination-wrapper .page-item .page-link {
    border: none; color: var(--text-secondary);
    border-radius: 50%; width: 36px; height: 36px;
    display: flex; align-items: center; justify-content: center;
    background: transparent; font-weight: 600;
}
.pagination-wrapper .page-item.active .page-link {
    background-color: var(--theme-social-color); color: white;
    box-shadow: 0 2px 5px rgba(24, 119, 242, 0.3);
}

/* Ripple Animation (Kept for interactive feel) */
.ripple { position: relative; overflow: hidden; transform: translate3d(0, 0, 0); }
.ripple:after {
    content: ""; display: block; position: absolute;
    width: 100%; height: 100%; top: 0; left: 0;
    pointer-events: none;
    background-image: radial-gradient(circle, #000 10%, transparent 10.01%);
    background-repeat: no-repeat; background-position: 50%;
    transform: scale(10, 10); opacity: 0;
    transition: transform .5s, opacity 1s;
}
.ripple:active:after { transform: scale(0, 0); opacity: .1; transition: 0s; }

@media (max-width: 600px) {
    .stack-layout-grid { padding: 1rem 0.5rem; }
    .card-content-wrapper { padding: 14px 12px; gap: 10px; }
    .avatar-icon { width: 38px; height: 38px; }
    .item-body { font-size: 14px; }
}
</style>

<script>
// ... (Keep your existing JavaScript exactly the same as before) ...
(function() {
    'use strict';
    document.querySelectorAll('.filter-chip').forEach(btn => {
        btn.addEventListener('click', function() {
            const type = this.dataset.type;
            const url = new URL(window.location.href);
            url.searchParams.set('type', type);
            if (type === 'all' || type === 'social') {
                url.searchParams.delete('accident_type');
            } else if (type === 'priority' || type === 'general') {
                if (!url.searchParams.has('accident_type')) {
                    url.searchParams.set('accident_type', 'all');
                }
            }
            window.location.href = url.toString();
        });
    });

    const accidentTypeFilter = document.getElementById('accidentTypeFilter');
    if (accidentTypeFilter) {
        accidentTypeFilter.addEventListener('change', function() {
            const url = new URL(window.location.href);
            const currentType = url.searchParams.get('type') || 'all';
            url.searchParams.set('accident_type', this.value);
            if (currentType === 'all' || currentType === 'social') {
                url.searchParams.set('type', 'priority'); 
            }
            window.location.href = url.toString();
        });
    }

    window.markAsReadAndNavigate = function(notificationId, postUrl) {
        if (!postUrl || postUrl === '#' || postUrl === '') return;
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        if (csrfToken) {
            fetch(`/notifications/${notificationId}/mark-read`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                body: JSON.stringify({})
            }).then(() => { window.location.href = postUrl; })
              .catch(() => { window.location.href = postUrl; });
        } else {
            window.location.href = postUrl;
        }
    };

    // Auto Refresh Logic (Kept the same)
    let autoRefreshInterval;
    let lastNotificationCount = {{ $notifications->count() }}; 
    function startSmartAutoRefresh() {
        autoRefreshInterval = setInterval(() => {
            fetch(window.location.href + (window.location.search ? '&' : '?') + 'check_only=1', {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(response => response.text())
            .then(html => {
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                // Updated selector to match new card class
                const currentCount = doc.querySelectorAll('.stack-card').length; 
                if (currentCount !== lastNotificationCount) {
                    lastNotificationCount = currentCount;
                    window.location.reload();
                }
            })
            .catch(error => console.error('Error checking'));
        }, 50000); 
    }
    function stopSimpleAutoRefresh() { if (autoRefreshInterval) clearInterval(autoRefreshInterval); }
    document.addEventListener('DOMContentLoaded', startSmartAutoRefresh);
    window.addEventListener('beforeunload', stopSimpleAutoRefresh);
})();
</script>
@endsection