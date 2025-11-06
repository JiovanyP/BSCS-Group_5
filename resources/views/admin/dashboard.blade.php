{{-- resources/views/admin/dashboard.blade.php --}}
@extends('layouts.admin')

@section('title', 'Admin Dashboard')

@section('content')
@php
  $reportedCount = isset($reportedPosts) ? (is_countable($reportedPosts) ? count($reportedPosts) : ($reportedPosts->total() ?? 0)) : 0;
@endphp

<style>
/* High-specificity admin dashboard styles (expandable vertical action menu per card) */
:root {
  --bg: #071018;
  --panel: rgba(255,255,255,0.02);
  --muted: #98a0a8;
  --accent: #CF0F47;
  --green: #17b06b;
  --red: #ea4d4d;
  --blue: #1482e8;
  --card-radius: 12px;
  --card-shadow: 0 10px 30px rgba(0,0,0,0.6);
}

/* Dashboard layout */

.admin-dashboard { display:block; }
.dashboard-header { display:flex; justify-content:space-between; align-items:flex-start; gap:12px; margin-bottom:18px; }
.dashboard-title h1 { margin:0; font-size:20px; }
.dashboard-meta { color:var(--muted); margin-top:6px; font-size:13px; }

/* Stats */
.stats-row { display:flex; flex-wrap:wrap; gap:12px; margin-bottom:16px; justify-content:center; }
.stats-card { background:var(--panel); border-radius:10px; padding:12px; box-shadow:var(--card-shadow); text-align:center; min-width:150px; flex:1; max-width:200px; }
.stats-card .label { color:var(--muted); font-weight:700; font-size:13px; }
.stats-card .value { font-weight:800; font-size:18px; margin-top:6px; }

/* Reports grid */
.reports-wrap { display:grid; grid-template-columns: 1fr 360px; gap:12px; align-items:start; }

/* Reports list */
.reports-list { display:block; }

/* Each report card: distinct card like Reddit */
.report-card {
  position:relative;
  background: linear-gradient(180deg, rgba(255,255,255,0.012), transparent);
  border-radius: var(--card-radius);
  padding:18px;
  margin-bottom:16px;
  box-shadow: var(--card-shadow);
  border: 1px solid rgba(255,255,255,0.03);
  transition: transform .12s ease, box-shadow .12s ease;
  overflow:visible;
}
.report-card:hover { transform: translateY(-3px); box-shadow: 0 14px 40px rgba(0,0,0,0.6); }

/* Report content */
.report-top { display:flex; gap:12px; align-items:flex-start; }
.report-info { flex:1; min-width:0; }
.post-id { color:var(--accent); font-weight:800; }
.post-title { margin-top:6px; color:#eaf2fa; font-size:15px; font-weight:700; }
.post-body { margin-top:8px; color:#dce9f2; line-height:1.45; max-height:140px; overflow:hidden; text-overflow:ellipsis; display:-webkit-box; -webkit-line-clamp:4; -webkit-box-orient:vertical; }
.post-meta { margin-top:12px; display:flex; gap:12px; align-items:center; color:var(--muted); font-size:13px; flex-wrap:wrap; }

/* Thumbnail */
.post-thumb { width:140px; height:84px; flex-shrink:0; border-radius:8px; overflow:hidden; background:rgba(255,255,255,0.02); display:flex; align-items:center; justify-content:center; color:var(--muted); }
.post-thumb img { width:100%; height:100%; object-fit:cover; display:block; }

/* === Expandable action trigger / menu (single compact trigger that expands vertically) ===
   - Trigger is a small circular icon (three-dots or plus)
   - When triggered, the menu (vertical list) appears, anchored to the trigger, with small circular action buttons
*/
.action-trigger {
  position:absolute;
  right:12px;
  top:12px;
  width:44px;
  height:44px;
  border-radius:50%;
  display:inline-flex;
  align-items:center;
  justify-content:center;
  background: rgba(0,0,0,0.35);
  color:#fff;
  border:none;
  cursor:pointer;
  box-shadow: 0 8px 20px rgba(0,0,0,0.6);
  z-index:30;
}
.action-trigger .material-icons { font-size:20px; }

.action-menu {
  position:absolute;
  right:12px;
  top:62px; /* initial placement below the trigger */
  display:flex;
  flex-direction:column;
  gap:8px;
  background: transparent;
  z-index:30;
  opacity:0;
  pointer-events:none;
  transform: translateY(-6px);
  transition: opacity .12s ease, transform .12s ease;
}

/* when open */
.report-card.menu-open .action-menu {
  opacity:1;
  pointer-events:auto;
  transform: translateY(0);
}

/* action item buttons inside the menu (circular small icons) */
.action-item {
  width:40px;
  height:40px;
  border-radius:50%;
  display:inline-flex;
  align-items:center;
  justify-content:center;
  background: rgba(255,255,255,0.03);
  color:#fff;
  border:none;
  cursor:pointer;
  box-shadow: 0 8px 18px rgba(0,0,0,0.6);
  transition: transform .12s ease, background .12s ease;
}
.action-item:focus { outline:2px solid rgba(255,255,255,0.06); outline-offset:2px; }
.action-item:hover { transform: translateY(-4px); background: rgba(255,255,255,0.08); }

/* colored variants */
.action-item.dismiss { background: linear-gradient(180deg, var(--green), #12a85b); }
.action-item.remove  { background: linear-gradient(180deg, var(--red), #c43932); }
.action-item.view    { background: linear-gradient(180deg, var(--blue), #076fb8); }

/* small text labels shown on right of menu (optional, hidden by default; will show on hover for extra clarity) */
.action-label {
  position:absolute;
  right:64px;
  background: rgba(0,0,0,0.7);
  color:#fff;
  padding:6px 8px;
  border-radius:8px;
  font-size:13px;
  white-space:nowrap;
  opacity:0;
  transform: translateX(6px);
  transition: opacity .12s ease, transform .12s ease;
  pointer-events:none;
}
.action-item:hover + .action-label { opacity:1; transform: translateX(0); }

/* responsive: on narrow screens place menu inline at bottom-right of card */
@media (max-width:720px) {
  .action-trigger { right:12px; top:12px; }
  .action-menu { position:static; top:auto; right:auto; transform:none; opacity:1; flex-direction:row; gap:8px; margin-top:12px; }
  .report-card.menu-open .action-menu { opacity:1; }
  .action-label { display:none; }
}

/* top locations card */
.locations-card { background:var(--panel); border-radius:12px; padding:14px; box-shadow:var(--card-shadow); }
.locations-table { width:100%; border-collapse:collapse; color:#eaf2fa; }
.locations-table th { color:var(--muted); font-weight:700; padding:10px 6px; text-align:left; }
.locations-table td { padding:10px 6px; border-top:1px solid rgba(255,255,255,0.02); font-weight:700; }

/* small pagination tweaks */
.reports-pagination { margin-top:10px; color:var(--muted); }
</style>

<div class="admin-dashboard">
  <div class="dashboard-header">
    <div class="dashboard-title">
      <h1>Dashboard — Reports</h1>
      <div class="dashboard-meta">Moderation center — manage reported posts and incidents</div>
    </div>

    <div style="display:flex; gap:12px; align-items:center;">
      <div style="background:var(--panel); padding:10px 14px; border-radius:10px; box-shadow:var(--card-shadow);">
        <div style="font-size:12px; color:var(--muted)">Reported posts</div>
        <div style="font-weight:800; margin-top:6px; font-size:18px;">{{ $reportedCount }}</div>
      </div>
    </div>
  </div>

  <div class="stats-row" role="region" aria-label="Report stats">
    @foreach (['Fire','Crime','Traffic','Others'] as $cat)
      @php $count = data_get($accidentCounts->firstWhere('accident_type',$cat),'total',0); @endphp
      <div class="stats-card" aria-label="{{ $cat }}">
        <div class="label">{{ $cat }}</div>
        <div class="value">{{ $count }}</div>
        <div style="color:var(--muted); font-size:12px; margin-top:6px;">reports</div>
      </div>
    @endforeach
  </div>

  <div class="reports-wrap">
    {{-- LEFT: reported posts feed --}}
    <section class="reports-list" aria-labelledby="reported-heading">
      <h2 id="reported-heading" style="font-size:18px; color:var(--muted); margin-bottom:12px;">Reported Posts</h2>

      @if(empty($reportedPosts) || (is_countable($reportedPosts) && count($reportedPosts) === 0))
        <div style="padding:18px; color:var(--muted)">No reported posts.</div>
      @else
        @foreach($reportedPosts as $post)
          @php
            $isValid = $post && is_object($post) && isset($post->id);
            $reports = $isValid ? ($post->reports ?? collect()) : collect();
            $reports_count = $isValid ? ($post->reports_count ?? $reports->count()) : $reports->count();
            $cardId = $isValid ? "post-{$post->id}" : "orphan-{$loop->index}";
          @endphp

          <article class="report-card" id="post-row-{{ $cardId }}" role="article" aria-label="Reported post">
            {{-- single compact trigger in top-right --}}
            <button class="action-trigger js-action-trigger" aria-haspopup="true" aria-expanded="false" aria-controls="menu-{{ $cardId }}" title="Open actions">
              <span class="material-icons">more_horiz</span>
            </button>

            {{-- action menu (hidden by default, reveals when .menu-open on .report-card) --}}
            <div id="menu-{{ $cardId }}" class="action-menu" role="menu" aria-hidden="true">
              {{-- Dismiss --}}
              <button
                class="action-item dismiss btn-dismiss"
                role="menuitem"
                title="Dismiss reports"
                data-action="{{ $isValid ? route('admin.reports.resolve', ['post' => $post->id]) : route('admin.reports.resolveOrphan') }}"
                data-post-id="{{ $isValid ? $post->id : '' }}"
                data-report-ids="{{ $isValid ? '' : $reports->pluck('id')->join(',') }}"
                aria-label="Dismiss reports"
              >
                <span class="material-icons">done</span>
              </button>
              <div class="action-label">Dismiss</div>

              {{-- Remove (only for valid posts) --}}
              @if($isValid)
                <button
                  class="action-item remove btn-remove"
                  role="menuitem"
                  title="Remove post permanently"
                  data-action="{{ route('admin.posts.remove', ['post' => $post->id]) }}"
                  data-post-id="{{ $post->id }}"
                  aria-label="Remove post"
                >
                  <span class="material-icons">delete</span>
                </button>
                <div class="action-label">Remove</div>
              @else
                <button class="action-item remove" disabled aria-hidden="true" title="Remove not available">–</button>
                <div class="action-label">Remove</div>
              @endif

              {{-- View --}}
              @if($isValid)
                <a href="{{ route('admin.posts.view', ['id' => $post->id]) }}" class="action-item view" role="menuitem" title="View post" aria-label="View post" style="text-decoration:none; display:inline-flex; align-items:center; justify-content:center;">
                  <span class="material-icons">visibility</span>
                </a>
                <div class="action-label">View</div>
              @else
                <button class="action-item view" disabled aria-hidden="true">–</button>
                <div class="action-label">View</div>
              @endif
            </div>

            {{-- Report content (left) --}}
            <div class="report-top">
              <div class="report-info">
                @if($isValid)
                  <div class="post-id">#{{ $post->id }} · <span style="color:var(--muted); font-weight:700;">{{ optional($post->user)->name ?? 'Unknown' }}</span></div>
                  <div class="post-title">{{ Str::limit($post->content ?? '(No content)', 80) }}</div>
                  <div class="post-body">{{ Str::limit($post->content ?? '(No content)', 300) }}</div>
                  <div class="post-meta">
                    @if(optional($post->user)->email) <span>{{ $post->user->email }}</span> @endif
                    <span class="reason-pill" style="background:rgba(255,255,255,0.02);">{{ $reports->first()->reason ?? 'Unknown' }}</span>
                    <span style="color:var(--muted)">{{ $reports_count }} report{{ $reports_count !== 1 ? 's' : '' }}</span>
                    @if(optional($post)->created_at) <span style="color:var(--muted)">· {{ optional($post->created_at)->diffForHumans() }}</span> @endif
                  </div>
                @else
                  <div class="post-id">#(deleted)</div>
                  <div class="post-title">Original post deleted — orphaned reports retained</div>
                  <div class="post-body">{{ Str::limit($post->content ?? '', 200) }}</div>
                  <div class="post-meta">
                    <span class="reason-pill" style="background:rgba(255,255,255,0.02)">{{ $reports->first()->reason ?? 'Unknown' }}</span>
                    <span style="color:var(--muted)">{{ $reports->count() }} report{{ $reports->count() !== 1 ? 's' : '' }}</span>
                  </div>
                @endif
              </div>

              <div class="post-thumb" aria-hidden="true">
                @if($isValid && !empty($post->image))
                  <img src="{{ asset('storage/' . $post->image) }}" alt="Post thumbnail">
                @else
                  <div style="padding:6px; color:var(--muted); font-size:13px;">No media</div>
                @endif
              </div>
            </div>
          </article>
        @endforeach

        <div class="reports-pagination">
          @if(method_exists($reportedPosts,'links'))
            {{ $reportedPosts->links() }}
          @endif
        </div>
      @endif
    </section>

    {{-- RIGHT: Top locations --}}
    <aside class="locations-card" aria-labelledby="locations-heading">
      <h3 id="locations-heading" style="margin:0 0 10px 0; color:var(--muted);">Top Locations by Reported Incidents</h3>

      @if(empty($topLocations) || $topLocations->isEmpty())
        <div style="color:var(--muted); padding:12px;">No location data available.</div>
      @else
        <table class="locations-table" role="table" aria-label="Top locations">
          <thead><tr><th style="width:36px">#</th><th>Location</th><th style="text-align:right">Reports</th></tr></thead>
          <tbody>
            @foreach($topLocations as $loc)
              <tr>
                <td style="color:var(--muted)">{{ $loop->iteration }}</td>
                <td style="font-weight:800;">{{ $loc->location ?: 'Unknown' }}</td>
                <td style="text-align:right; color:var(--muted)">{{ $loc->total }}</td>
              </tr>
            @endforeach
          </tbody>
        </table>
      @endif
    </aside>
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

  // CSRF header for AJAX
  $.ajaxSetup({
    headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') }
  });

  // Toggle menu open/close on trigger click
  $(document).on('click', '.js-action-trigger', function(e){
    e.preventDefault();
    const $trigger = $(this);
    const $card = $trigger.closest('.report-card');

    // close any other open menus
    $('.report-card.menu-open').not($card).removeClass('menu-open').find('.action-trigger').attr('aria-expanded','false');
    // toggle current
    const open = $card.hasClass('menu-open');
    if (open) {
      $card.removeClass('menu-open');
      $card.find('.action-trigger').attr('aria-expanded','false');
      $card.find('.action-menu').attr('aria-hidden','true');
    } else {
      $card.addClass('menu-open');
      $card.find('.action-trigger').attr('aria-expanded','true');
      $card.find('.action-menu').attr('aria-hidden','false');
    }
  });

  // close menus if clicked outside
  $(document).on('click', function(e){
    if ($(e.target).closest('.report-card').length === 0) {
      $('.report-card.menu-open').removeClass('menu-open').find('.action-trigger').attr('aria-expanded','false').end().find('.action-menu').attr('aria-hidden','true');
    }
  });

  // keyboard: Esc closes any open menu
  $(document).on('keydown', function(e){
    if (e.key === 'Escape') {
      $('.report-card.menu-open').removeClass('menu-open').find('.action-trigger').attr('aria-expanded','false').end().find('.action-menu').attr('aria-hidden','true');
    }
  });

  // Dismiss (works for both post-specific and orphaned report ids)
  $(document).on('click', '.btn-dismiss', function(e){
    e.preventDefault();
    const $btn = $(this);
    const action = $btn.data('action');
    const postId = $btn.data('post-id') || null;
    const reportIds = $btn.data('report-ids') || null;

    if (!confirm('Dismiss the selected report(s)? This will mark them resolved.')) return;

    $btn.prop('disabled', true).css('opacity',0.6);

    const payload = reportIds ? { report_ids: reportIds } : {};

    $.post(action, payload)
      .done(function(res){
        // remove the card from DOM
        // note: if postId present, card id is post-<postId>
        const selector = postId ? '#post-row-post-' + postId : null;
        if (selector && $(selector).length) {
          $(selector).fadeOut(260, function(){ $(this).remove(); });
        } else {
          $btn.closest('.report-card').fadeOut(260, function(){ $(this).remove(); });
        }
      })
      .fail(function(xhr){
        console.error('Dismiss failed', xhr.responseText || xhr.statusText);
        alert('Failed to dismiss reports. See console for details.');
      })
      .always(function(){
        $btn.prop('disabled', false).css('opacity',1);
      });
  });

  // Remove post (destructive)
  $(document).on('click', '.btn-remove', function(e){
    e.preventDefault();
    const $btn = $(this);
    const action = $btn.data('action');
    const postId = $btn.data('post-id');

    if (!postId || !action) return;
    if (!confirm('Remove this post permanently? This cannot be undone.')) return;

    $btn.prop('disabled', true).css('opacity',0.6).html('…');

    $.post(action, { _token: $('meta[name="csrf-token"]').attr('content') })
      .done(function(res){
        $('#post-row-post-' + postId).fadeOut(260, function(){ $(this).remove(); });
      })
      .fail(function(xhr){
        console.error('Remove failed', xhr.responseText || xhr.statusText);
        alert('Failed to remove post. See console for details.');
      })
      .always(function(){
        // restore icon if needed
        $btn.prop('disabled', false).css('opacity',1).html('<span class="material-icons">delete</span>');
      });
  });

  // Accessibility: ensure Enter activates buttons when trigger is focused
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
