{{-- resources/views/admin/dashboard.blade.php --}}
@extends('layouts.admin')

@section('title', 'Admin Dashboard')

@section('content')
@php
  $reportedCount = isset($reportedPosts) ? (is_countable($reportedPosts) ? count($reportedPosts) : ($reportedPosts->total() ?? 0)) : 0;
@endphp

<style>
:root {
  --bg-primary: #0B1416;
  --bg-secondary: #1A1A1B;
  --bg-hover: #272729;
  --border: #343536;
  --text-primary: #D7DADC;
  --text-secondary: #818384;
  --accent-red: #FF0558;
  --accent-blue: #0079D3;
  --accent-green: #46D160;
  --accent-orange: #FF9F0A;
  --card-radius: 8px;
}

body {
  background: var(--bg-primary);
  color: var(--text-primary);
}

/* Dashboard container */
.dashboard-container {
  max-width: 1400px;
  margin: 0 auto;
  padding: 20px;
}

/* Header section */
.dashboard-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  margin-bottom: 24px;
  gap: 16px;
  flex-wrap: wrap;
}

.dashboard-title h1 {
  font-size: 28px;
  font-weight: 500;
  margin: 0 0 8px 0;
  color: var(--text-primary);
}

.dashboard-subtitle {
  color: var(--text-secondary);
  font-size: 14px;
  line-height: 1.5;
}

.header-stat {
  background: var(--bg-secondary);
  border: 1px solid var(--border);
  border-left: 3px solid var(--accent-red);
  border-radius: var(--card-radius);
  padding: 16px 20px;
  min-width: 140px;
}

.header-stat-label {
  font-size: 12px;
  color: var(--text-secondary);
  text-transform: uppercase;
  letter-spacing: 0.5px;
  margin-bottom: 8px;
}

.header-stat-value {
  font-size: 32px;
  font-weight: 700;
  color: var(--accent-red);
}

/* Stats grid */
.stats-grid {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 16px;
}

.stat-card {
  background: var(--bg-secondary);
  border: 1px solid var(--border);
  border-left: 3px solid var(--accent-red);
  border-radius: var(--card-radius);
  padding: 20px;
  transition: all 0.2s;
}

.stat-card:hover {
  background: var(--bg-hover);
  border-color: var(--text-secondary);
  transform: translateY(-2px);
}

.stat-label {
  font-size: 13px;
  color: var(--text-secondary);
  text-transform: uppercase;
  letter-spacing: 0.5px;
  font-weight: 600;
  margin-bottom: 12px;
}

.stat-value {
  font-size: 36px;
  font-weight: 700;
  color: var(--text-primary);
  margin-bottom: 4px;
}

.stat-sublabel {
  font-size: 12px;
  color: var(--text-secondary);
}

/* Top bar grid - stats and locations side by side */
.top-bar-grid {
  display: grid;
  grid-template-columns: 1fr 380px;
  gap: 20px;
  align-items: start;
  margin-bottom: 24px;
}

/* Main content - full width below */
.main-content {
  width: 100%;
}

/* Section headers */
.section-header {
  font-size: 18px;
  font-weight: 500;
  color: var(--text-primary);
  margin-bottom: 16px;
  display: flex;
  align-items: center;
  gap: 8px;
}

.section-header .material-icons {
  font-size: 20px;
  color: var(--accent-red);
}

/* Report cards */
.reports-list {
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.report-card {
  position: relative;
  background: var(--bg-secondary);
  border: 1px solid var(--border);
  border-left: 3px solid var(--accent-red);
  border-radius: var(--card-radius);
  padding: 20px;
  transition: all 0.2s;
  overflow: visible;
}

.report-card:hover {
  background: var(--bg-hover);
  border-color: var(--text-secondary);
  transform: translateX(4px);
}

/* Report content layout */
.report-content {
  display: flex;
  gap: 16px;
  align-items: flex-start;
}

.report-info {
  flex: 1;
  min-width: 0;
}

.report-id {
  display: flex;
  align-items: center;
  gap: 8px;
  margin-bottom: 8px;
}

.report-id-text {
  color: var(--accent-red);
  font-weight: 700;
  font-size: 13px;
}

.report-author {
  color: var(--text-secondary);
  font-size: 13px;
}

.report-title {
  font-size: 15px;
  font-weight: 600;
  color: var(--text-primary);
  margin-bottom: 8px;
  line-height: 1.4;
}

.report-body {
  color: var(--text-secondary);
  font-size: 14px;
  line-height: 1.6;
  max-height: 120px;
  overflow: hidden;
  text-overflow: ellipsis;
  display: -webkit-box;
  -webkit-line-clamp: 4;
  -webkit-box-orient: vertical;
  margin-bottom: 12px;
}

.report-meta {
  display: flex;
  gap: 12px;
  align-items: center;
  flex-wrap: wrap;
  font-size: 12px;
  color: var(--text-secondary);
}

.reason-badge {
  display: inline-flex;
  align-items: center;
  background: rgba(255, 69, 0, 0.15);
  color: var(--accent-red);
  padding: 4px 10px;
  border-radius: 12px;
  font-size: 11px;
  font-weight: 600;
  text-transform: uppercase;
}

.report-count {
  display: inline-flex;
  align-items: center;
  gap: 4px;
  font-weight: 600;
}

.report-count .material-icons {
  font-size: 14px;
}

/* Thumbnail */
.report-thumbnail {
  width: 120px;
  height: 90px;
  flex-shrink: 0;
  border-radius: 6px;
  overflow: hidden;
  background: rgba(255, 255, 255, 0.03);
  display: flex;
  align-items: center;
  justify-content: center;
}

.report-thumbnail img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.report-thumbnail-placeholder {
  color: var(--text-secondary);
  font-size: 12px;
  text-align: center;
  padding: 8px;
}

/* Action menu trigger */
.action-trigger {
  position: absolute;
  right: 16px;
  top: 16px;
  width: 36px;
  height: 36px;
  border-radius: 50%;
  background: rgba(0, 0, 0, 0.4);
  border: 1px solid var(--border);
  color: var(--text-primary);
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: all 0.2s;
  z-index: 10;
}

.action-trigger:hover {
  background: rgba(0, 0, 0, 0.6);
  border-color: var(--text-primary);
}

.action-trigger .material-icons {
  font-size: 20px;
}

/* Action menu */
.action-menu {
  position: absolute;
  right: 16px;
  top: 58px;
  display: flex;
  flex-direction: column;
  gap: 8px;
  background: var(--bg-secondary);
  border: 1px solid var(--border);
  border-radius: var(--card-radius);
  padding: 8px;
  box-shadow: 0 8px 24px rgba(0, 0, 0, 0.6);
  z-index: 20;
  opacity: 0;
  pointer-events: none;
  transform: translateY(-8px);
  transition: all 0.2s;
  min-width: 160px;
}

.report-card.menu-open .action-menu {
  opacity: 1;
  pointer-events: auto;
  transform: translateY(0);
}

.action-item {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 10px 12px;
  border-radius: 6px;
  background: transparent;
  border: none;
  color: var(--text-primary);
  font-size: 14px;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s;
  text-decoration: none;
  width: 100%;
}

.action-item:hover:not(:disabled) {
  background: var(--bg-hover);
}

.action-item:disabled {
  opacity: 0.4;
  cursor: not-allowed;
}

.action-item .material-icons {
  font-size: 18px;
}

.action-item.dismiss {
  color: var(--accent-green);
}

.action-item.remove {
  color: var(--accent-red);
}

.action-item.view {
  color: var(--accent-blue);
}

/* Locations sidebar */
.locations-card {
  background: var(--bg-secondary);
  border: 1px solid var(--border);
  border-left: 3px solid var(--accent-red);
  border-radius: var(--card-radius);
  padding: 20px;
  height: fit-content;
}

.locations-header {
  font-size: 16px;
  font-weight: 600;
  color: var(--text-primary);
  margin-bottom: 16px;
  display: flex;
  align-items: center;
  gap: 8px;
}

.locations-header .material-icons {
  font-size: 18px;
  color: var(--accent-red);
}

.locations-list {
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.location-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 12px;
  background: rgba(255, 255, 255, 0.03);
  border-radius: 6px;
  transition: all 0.2s;
}

.location-item:hover {
  background: rgba(255, 255, 255, 0.05);
}

.location-rank {
  width: 28px;
  height: 28px;
  border-radius: 50%;
  background: var(--accent-red);
  color: white;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 700;
  font-size: 13px;
  flex-shrink: 0;
}

.location-info {
  flex: 1;
  margin: 0 12px;
  min-width: 0;
}

.location-name {
  font-weight: 600;
  font-size: 14px;
  color: var(--text-primary);
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.location-count {
  display: flex;
  align-items: center;
  gap: 4px;
  font-weight: 700;
  font-size: 16px;
  color: var(--text-primary);
  flex-shrink: 0;
}

.location-count .material-icons {
  font-size: 16px;
  color: var(--text-secondary);
}

/* Empty state */
.empty-state {
  text-align: center;
  padding: 60px 20px;
  color: var(--text-secondary);
  background: var(--bg-secondary);
  border: 1px solid var(--border);
  border-radius: var(--card-radius);
}

.empty-state .material-icons {
  font-size: 64px;
  opacity: 0.3;
  margin-bottom: 16px;
}

/* Pagination */
.pagination-wrapper {
  margin-top: 20px;
  padding: 16px;
  text-align: center;
}

/* Responsive */
@media (max-width: 1200px) {
  .top-bar-grid {
    grid-template-columns: 1fr;
  }
  
  .locations-card {
    position: static;
  }
}

@media (max-width: 768px) {
  .dashboard-container {
    padding: 12px;
  }
  
  .dashboard-header {
    flex-direction: column;
  }
  
  .stats-grid {
    grid-template-columns: repeat(2, 1fr);
    gap: 12px;
  }
  
  .stat-value {
    font-size: 28px;
  }
  
  .header-stat-value {
    font-size: 24px;
  }
  
  .report-content {
    flex-direction: column-reverse;
  }
  
  .report-thumbnail {
    width: 100%;
    height: 160px;
  }
  
  .action-menu {
    position: fixed;
    right: 12px;
    left: 12px;
    top: auto;
    bottom: 12px;
    width: auto;
  }
}

@media (max-width: 480px) {
  .dashboard-title h1 {
    font-size: 22px;
  }
  
  .section-header {
    font-size: 16px;
  }
  
  .report-card {
    padding: 16px;
  }
  
  .action-trigger {
    right: 12px;
    top: 12px;
  }
  
  .stat-card {
    padding: 16px;
  }
  
  .report-meta {
    font-size: 11px;
  }
}
</style>

<div class="dashboard-container">
  <!-- Header -->
  <div class="dashboard-header">
    <div class="dashboard-title">
      <h1>Dashboard — Reports</h1>
      <div class="dashboard-subtitle">Moderation center — manage reported posts and incidents</div>
    </div>

    <div class="header-stat">
      <div class="header-stat-label">Reported Posts</div>
      <div class="header-stat-value">{{ $reportedCount }}</div>
    </div>
  </div>

  <!-- Stats and Locations Grid -->
  <div class="top-bar-grid">
    <!-- Stats Grid -->
    <div class="stats-grid" role="region" aria-label="Report statistics">
      @foreach (['Fire','Crime','Traffic','Others'] as $cat)
        @php $count = data_get($accidentCounts->firstWhere('accident_type',$cat),'total',0); @endphp
        <div class="stat-card" aria-label="{{ $cat }} reports">
          <div class="stat-label">{{ $cat }}</div>
          <div class="stat-value">{{ $count }}</div>
          <div class="stat-sublabel">reported incidents</div>
        </div>
      @endforeach
    </div>

    <!-- Top Locations -->
    <aside class="locations-card" aria-labelledby="locations-heading">
      <h3 id="locations-heading" class="locations-header">
        <span class="material-icons">place</span>
        Top Locations
      </h3>

      @if(empty($topLocations) || $topLocations->isEmpty())
        <div style="color:var(--text-secondary); padding:12px; text-align:center;">
          No location data available
        </div>
      @else
        <div class="locations-list">
          @foreach($topLocations as $loc)
            <div class="location-item">
              <div class="location-rank">{{ $loop->iteration }}</div>
              <div class="location-info">
                <div class="location-name">{{ $loc->location ?: 'Unknown' }}</div>
              </div>
              <div class="location-count">
                <span class="material-icons">flag</span>
                {{ $loc->total }}
              </div>
            </div>
          @endforeach
        </div>
      @endif
    </aside>
  </div>

  <!-- Reported Posts - Full Width Below -->
  <section class="main-content" aria-labelledby="reported-heading">
      <h2 id="reported-heading" class="section-header">
        <span class="material-icons">flag</span>
        Reported Posts
      </h2>

      @if(empty($reportedPosts) || (is_countable($reportedPosts) && count($reportedPosts) === 0))
        <div class="empty-state">
          <span class="material-icons">check_circle</span>
          <div>No reported posts</div>
        </div>
      @else
        <div class="reports-list">
          @foreach($reportedPosts as $post)
            @php
              $isValid = $post && is_object($post) && isset($post->id);
              $reports = $isValid ? ($post->reports ?? collect()) : collect();
              $reports_count = $isValid ? ($post->reports_count ?? $reports->count()) : $reports->count();
              $cardId = $isValid ? "post-{$post->id}" : "orphan-{$loop->index}";
            @endphp

            <article class="report-card" id="post-row-{{ $cardId }}" role="article">
              <!-- Action trigger -->
              <button class="action-trigger js-action-trigger" 
                      aria-haspopup="true" 
                      aria-expanded="false" 
                      aria-controls="menu-{{ $cardId }}"
                      aria-label="Open actions menu">
                <span class="material-icons">more_vert</span>
              </button>

              <!-- Action menu -->
              <div id="menu-{{ $cardId }}" class="action-menu" role="menu" aria-hidden="true">
                <!-- Dismiss -->
                <button
                  class="action-item dismiss btn-dismiss"
                  role="menuitem"
                  data-action="{{ $isValid ? route('admin.reports.resolve', ['post' => $post->id]) : route('admin.reports.resolveOrphan') }}"
                  data-post-id="{{ $isValid ? $post->id : '' }}"
                  data-report-ids="{{ $isValid ? '' : $reports->pluck('id')->join(',') }}"
                >
                  <span class="material-icons">check_circle</span>
                  <span>Dismiss Reports</span>
                </button>

                <!-- Remove -->
                @if($isValid)
                  <button
                    class="action-item remove btn-remove"
                    role="menuitem"
                    data-action="{{ route('admin.posts.remove', ['post' => $post->id]) }}"
                    data-post-id="{{ $post->id }}"
                  >
                    <span class="material-icons">delete</span>
                    <span>Remove Post</span>
                  </button>
                @else
                  <button class="action-item remove" disabled>
                    <span class="material-icons">delete</span>
                    <span>Remove Post</span>
                  </button>
                @endif

                <!-- View -->
                @if($isValid)
                  <a href="{{ route('admin.posts.view', ['id' => $post->id]) }}" 
                     class="action-item view" 
                     role="menuitem">
                    <span class="material-icons">visibility</span>
                    <span>View Post</span>
                  </a>
                @else
                  <button class="action-item view" disabled>
                    <span class="material-icons">visibility</span>
                    <span>View Post</span>
                  </button>
                @endif
              </div>

              <!-- Report content -->
              <div class="report-content">
                <div class="report-info">
                  <div class="report-id">
                    @if($isValid)
                      <span class="report-id-text">#{{ $post->id }}</span>
                      <span class="report-author">by {{ optional($post->user)->name ?? 'Unknown' }}</span>
                    @else
                      <span class="report-id-text">#(deleted)</span>
                      <span class="report-author">Original post removed</span>
                    @endif
                  </div>

                  @if($isValid)
                    <div class="report-title">{{ Str::limit($post->content ?? '(No content)', 80) }}</div>
                    <div class="report-body">{{ Str::limit($post->content ?? '(No content)', 300) }}</div>
                  @else
                    <div class="report-title">Orphaned Reports</div>
                    <div class="report-body">The original post has been deleted, but reports remain for record keeping.</div>
                  @endif

                  <div class="report-meta">
                    <span class="reason-badge">{{ $reports->first()->reason ?? 'Unknown' }}</span>
                    
                    <span class="report-count">
                      <span class="material-icons">flag</span>
                      {{ $reports_count }} report{{ $reports_count !== 1 ? 's' : '' }}
                    </span>

                    @if($isValid && optional($post->user)->email)
                      <span>{{ $post->user->email }}</span>
                    @endif

                    @if($isValid && optional($post)->created_at)
                      <span>{{ optional($post->created_at)->diffForHumans() }}</span>
                    @endif
                  </div>
                </div>

                <div class="report-thumbnail">
                  @if($isValid && !empty($post->image))
                    <img src="{{ asset('storage/' . $post->image) }}" alt="Post thumbnail">
                  @else
                    <div class="report-thumbnail-placeholder">
                      <span class="material-icons" style="font-size: 32px; opacity: 0.3;">image</span>
                    </div>
                  @endif
                </div>
              </div>
            </article>
          @endforeach
        </div>

        <div class="pagination-wrapper">
          @if(method_exists($reportedPosts,'links'))
            {{ $reportedPosts->links() }}
          @endif
        </div>
      @endif
    </section>
  </div>
</div>

@push('scripts')
<script>
(function(){
  'use strict';
  
  if (typeof $ === 'undefined') {
    console.error('jQuery required for admin dashboard actions.');
    return;
  }

  $.ajaxSetup({
    headers: { 
      'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
      'Accept': 'application/json'
    }
  });

  // Toggle action menu
  $(document).on('click', '.js-action-trigger', function(e){
    e.preventDefault();
    e.stopPropagation();
    
    const $trigger = $(this);
    const $card = $trigger.closest('.report-card');
    const isOpen = $card.hasClass('menu-open');

    // Close all other menus
    $('.report-card.menu-open').not($card).removeClass('menu-open')
      .find('.action-trigger').attr('aria-expanded', 'false')
      .end().find('.action-menu').attr('aria-hidden', 'true');

    // Toggle current menu
    if (isOpen) {
      $card.removeClass('menu-open');
      $trigger.attr('aria-expanded', 'false');
      $card.find('.action-menu').attr('aria-hidden', 'true');
    } else {
      $card.addClass('menu-open');
      $trigger.attr('aria-expanded', 'true');
      $card.find('.action-menu').attr('aria-hidden', 'false');
    }
  });

  // Close menu when clicking outside
  $(document).on('click', function(e){
    if (!$(e.target).closest('.report-card').length) {
      $('.report-card.menu-open').removeClass('menu-open')
        .find('.action-trigger').attr('aria-expanded', 'false')
        .end().find('.action-menu').attr('aria-hidden', 'true');
    }
  });

  // Close menu on Escape key
  $(document).on('keydown', function(e){
    if (e.key === 'Escape') {
      $('.report-card.menu-open').removeClass('menu-open')
        .find('.action-trigger').attr('aria-expanded', 'false')
        .end().find('.action-menu').attr('aria-hidden', 'true');
    }
  });

  // Dismiss reports
  $(document).on('click', '.btn-dismiss', function(e){
    e.preventDefault();
    
    const $btn = $(this);
    const action = $btn.data('action');
    const postId = $btn.data('post-id') || null;
    const reportIds = $btn.data('report-ids') || null;

    if (!confirm('Dismiss these reports? This will mark them as resolved.')) return;

    $btn.prop('disabled', true).css('opacity', 0.6);

    const payload = reportIds ? { report_ids: reportIds } : {};

    $.post(action, payload)
      .done(function(res){
        const selector = postId ? '#post-row-post-' + postId : null;
        if (selector && $(selector).length) {
          $(selector).fadeOut(300, function(){ $(this).remove(); });
        } else {
          $btn.closest('.report-card').fadeOut(300, function(){ $(this).remove(); });
        }
      })
      .fail(function(xhr){
        console.error('Dismiss failed', xhr);
        alert('Failed to dismiss reports. Please try again.');
      })
      .always(function(){
        $btn.prop('disabled', false).css('opacity', 1);
      });
  });

  // Remove post
  $(document).on('click', '.btn-remove', function(e){
    e.preventDefault();
    
    const $btn = $(this);
    const action = $btn.data('action');
    const postId = $btn.data('post-id');

    if (!postId || !action) return;
    if (!confirm('Remove this post permanently? This action cannot be undone.')) return;

    $btn.prop('disabled', true).css('opacity', 0.6);

    $.post(action, { _token: $('meta[name="csrf-token"]').attr('content') })
      .done(function(res){
        $('#post-row-post-' + postId).fadeOut(300, function(){ $(this).remove(); });
      })
      .fail(function(xhr){
        console.error('Remove failed', xhr);
        alert('Failed to remove post. Please try again.');
      })
      .always(function(){
        $btn.prop('disabled', false).css('opacity', 1);
      });
  });

  // Keyboard accessibility for trigger
  $(document).on('keydown', '.js-action-trigger', function(e){
    if (e.key === 'Enter' || e.key === ' ') {
      e.preventDefault();
      $(this).trigger('click');
    }
  });

})();
</script>
@endpush

@endsection