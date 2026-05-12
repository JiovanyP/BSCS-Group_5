{{-- resources/views/admin/reports/index.blade.php --}}
@extends('layouts.admin')

@section('title', 'Reported Posts')

@section('content')
<style>
    /* === THEME SYNC === */
    :root {
        --md-sys-color-surface: #ffffff;
        --md-sys-color-on-surface: #333333;
        --md-sys-color-on-surface-variant: #666666;
        --md-sys-color-outline: #eeeeee;
        --md-sys-color-primary: #494ca2;
        --md-sys-color-error-container: #fbebf1; /* Matches sidebar pink */
        --md-sys-color-on-error-container: #CF0F47; /* Matches accent red */
    }

    /* Force Poppins to match Layout */
    .admin-container, 
    .post-title, 
    .dropdown-item, 
    .reason-badge,
    .status-indicator {
        font-family: 'Poppins', sans-serif !important;
    }

    .admin-container {
        max-width: 900px;
        margin: 0 auto;
        padding-top: 10px;
    }

    /* Header */
    .page-header { margin-bottom: 24px; }
    .page-subtitle { 
        font-size: 14px; 
        color: var(--md-sys-color-on-surface-variant); 
        margin-top: -10px;
        font-weight: 500;
    }

    /* Grid */
    .reports-grid {
        display: flex;
        flex-direction: column;
        gap: 16px;
    }

    /* CARD DESIGN - Light Theme */
    .report-card {
        position: relative;
        background-color: var(--md-sys-color-surface);
        border-radius: 16px;
        padding: 20px;
        transition: all 0.25s ease;
        border: 1px solid var(--md-sys-color-outline);
        display: flex;
        flex-direction: column;
        gap: 8px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.03);
    }

    .report-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 24px rgba(0,0,0,0.08);
        border-color: var(--md-sys-color-primary);
    }

    /* Stretched Link */
    .card-link::after {
        content: "";
        position: absolute;
        top: 0; left: 0; right: 0; bottom: 0;
        z-index: 1;
        cursor: pointer;
    }

    .card-top-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 4px;
    }

    .reason-badge {
        background-color: var(--md-sys-color-error-container);
        color: var(--md-sys-color-on-error-container);
        padding: 4px 12px;
        border-radius: 8px;
        font-size: 11px;
        font-weight: 700;
        letter-spacing: 0.5px;
        text-transform: uppercase;
    }

    .status-indicator {
        font-size: 12px;
        display: flex;
        align-items: center;
        gap: 6px;
        color: var(--md-sys-color-on-surface-variant);
        font-weight: 600;
    }
    .status-dot { width: 8px; height: 8px; border-radius: 50%; }
    .status-dot.pending { background-color: #FFB703; }
    .status-dot.resolved { background-color: #6DD58C; }

    /* Content Section */
    .post-title {
        font-size: 17px;
        font-weight: 700;
        color: #111;
        margin: 4px 0;
        text-decoration: none;
        line-height: 1.4;
        display: block;
    }
    
    .post-content-preview {
        font-size: 14px;
        color: var(--md-sys-color-on-surface-variant);
        line-height: 1.6;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        margin-bottom: 8px;
    }

    /* Footer Metadata */
    .meta-footer {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 16px;
        padding-top: 12px;
        border-top: 1px solid var(--md-sys-color-outline);
        font-size: 12px;
        color: var(--md-sys-color-on-surface-variant);
    }

    .meta-item { display: flex; align-items: center; gap: 6px; }
    .meta-item strong { color: var(--md-sys-color-primary); font-weight: 600; }
    .meta-icon { font-size: 16px; color: var(--md-sys-color-primary); }

    /* Action Menu */
    .action-wrapper {
        position: absolute;
        top: 15px;
        right: 15px;
        z-index: 10;
    }

    .btn-icon {
        background: #f8f9fa;
        border: 1px solid #eee;
        color: var(--md-sys-color-on-surface-variant);
        width: 36px; height: 36px;
        border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        cursor: pointer;
        transition: 0.2s;
    }
    .btn-icon:hover { background-color: var(--md-sys-color-primary); color: white; border-color: var(--md-sys-color-primary); }

    .dropdown-menu {
        position: absolute;
        right: 0; top: 110%;
        background-color: white;
        min-width: 180px;
        border-radius: 12px;
        padding: 8px 0;
        box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        display: none;
        z-index: 20;
        border: 1px solid #eee;
    }
    .action-wrapper.active .dropdown-menu { display: block; }

    .dropdown-item {
        width: 100%; text-align: left;
        padding: 10px 16px;
        background: none; border: none;
        color: #444;
        font-size: 13px; cursor: pointer;
        font-weight: 500;
        display: flex; align-items: center; gap: 8px;
    }
    .dropdown-item:hover { background-color: #f8f9fa; color: var(--md-sys-color-primary); }
    .dropdown-item.resolve { color: #2e7d32; }
    
    /* Utilities */
    .alert-success { 
        padding: 12px 20px; 
        background: #e8f5e9; 
        color: #2e7d32; 
        border-radius: 12px; 
        margin-bottom: 20px; 
        font-weight: 600;
        border: none;
    }
    .empty-state { 
        text-align: center; 
        padding: 60px; 
        background: white;
        border-radius: 20px;
        border: 1px solid #eee;
        color: var(--md-sys-color-on-surface-variant); 
    }
</style>

<div class="admin-container">
    <div class="page-header">
        <p class="page-subtitle">Moderation Queue</p>
    </div>

    @if (session('success'))
        <div class="alert-success">{{ session('success') }}</div>
    @endif

    @if ($reports->isEmpty())
        <div class="empty-state">
            <span class="material-icons" style="font-size: 48px; color: #6DD58C; margin-bottom: 10px;">check_circle</span>
            <p style="font-weight: 600;">All clean! No reports found.</p>
        </div>
    @else
        <div class="reports-grid">
            @foreach ($reports as $report)
                <div class="report-card">
                    
                    <div class="card-top-row">
                        <div style="display: flex; gap: 8px; align-items: center;">
                            <span class="reason-badge">{{ $report->reason ?? 'General' }}</span>
                            <div class="status-indicator">
                                <span class="status-dot {{ $report->resolved ? 'resolved' : 'pending' }}"></span>
                                {{ $report->resolved ? 'Resolved' : 'Pending' }}
                            </div>
                        </div>

                        <div class="action-wrapper js-dropdown-wrapper">
                            <button class="btn-icon js-dropdown-trigger">
                                <span class="material-icons">more_horiz</span>
                            </button>
                            <div class="dropdown-menu">
                                @if (!$report->resolved)
                                    <form method="POST" action="{{ route('admin.reports.resolve', $report->post_id) }}">
                                        @csrf
                                        <button type="submit" class="dropdown-item resolve">
                                            <span class="material-icons" style="font-size: 18px;">done_all</span> Mark Resolved
                                        </button>
                                    </form>
                                @else
                                    <form method="POST" action="{{ route('admin.reports.resolveOrphan') }}">
                                        @csrf
                                        <input type="hidden" name="report_id" value="{{ $report->id }}">
                                        <button type="submit" class="dropdown-item">
                                            <span class="material-icons" style="font-size: 18px;">settings_backup_restore</span> Reopen Report
                                        </button>
                                    </form>
                                @endif
                                <a href="{{ route('admin.posts.view', $report->post_id) }}" class="dropdown-item" target="_blank">
                                    <span class="material-icons" style="font-size: 18px;">visibility</span> View Full Post
                                </a>
                            </div>
                        </div>
                    </div>

                    @if($report->post)
                        <a href="{{ route('admin.posts.view', $report->post_id) }}" class="post-title card-link" target="_blank">
                            {{ Str::limit($report->post->title, 70) }}
                        </a>
                        
                        <div class="post-content-preview">
                            {{ Str::limit($report->post->content ?? 'No text content.', 150) }}
                        </div>
                    @else
                        <span class="post-title" style="color: #999; font-style: italic;">
                            Content deleted
                        </span>
                    @endif

                    <div class="meta-footer">
                        <div class="meta-item" title="Reporter">
                            <span class="material-icons meta-icon">flag</span>
                            <span>Report by <strong>{{ $report->user->name ?? 'Unknown' }}</strong></span>
                        </div>

                        <div class="meta-item" title="Post Author">
                            <span class="material-icons meta-icon">account_circle</span>
                            <span>Author: <strong>{{ $report->post->user->name ?? 'Unknown' }}</strong></span>
                        </div>

                        <div class="meta-item" style="margin-left: auto;">
                            <span class="material-icons meta-icon">history</span>
                            <span>{{ $report->created_at->diffForHumans(null, true) }}</span>
                        </div>
                    </div>

                </div>
            @endforeach
        </div>
    @endif
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const dropdowns = document.querySelectorAll('.js-dropdown-wrapper');

        dropdowns.forEach(wrapper => {
            const trigger = wrapper.querySelector('.js-dropdown-trigger');
            trigger.addEventListener('click', (e) => {
                e.preventDefault(); 
                e.stopPropagation();
                
                dropdowns.forEach(w => { if(w!==wrapper) w.classList.remove('active'); });
                wrapper.classList.toggle('active');
            });
        });

        document.addEventListener('click', (e) => {
            if (!e.target.closest('.js-dropdown-wrapper')) {
                dropdowns.forEach(w => w.classList.remove('active'));
            }
        });
    });
</script>
@endsection