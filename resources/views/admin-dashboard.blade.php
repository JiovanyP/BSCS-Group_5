{{-- resources/views/admin/dashboard.blade.php --}}
@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">

<style>
:root{
  --accent: #CF0F47;
  --accent-hover: #FF0B55;
  --muted: #666;
  --card-radius: 12px;
}
body { background: #f9fafc; }

.navbar-admin {
  background: var(--accent);
  color: #fff;
  padding: 12px 20px;
  border-radius: 8px;
}
.navbar-admin h4 { margin: 0; font-weight: 700; letter-spacing: 1px; }

.sidebar {
  background: #fff;
  border-radius: 10px;
  box-shadow: 0 3px 12px rgba(0,0,0,0.05);
  padding: 20px;
}
.sidebar a { display:block; color:#333; padding:8px 10px; border-radius:6px; text-decoration:none; }
.sidebar a:hover { background: var(--accent); color:#fff; }

.card { background: #fff; border-radius: var(--card-radius); border: none; box-shadow: 0 2px 8px rgba(0,0,0,0.05); }
.card h5 { font-weight:600; }

.badge-category { background: var(--accent); color: #fff; padding: 6px 10px; border-radius: 8px; font-size: 13px; }
.btn-danger { background: var(--accent); border: none; color: #fff; }
.btn-danger:hover { background: var(--accent-hover); }

.post-media img, .post-media video { max-height: 120px; object-fit: cover; border-radius: 8px; }
.table thead th { background: #fafafa; }
.table-responsive { max-height: 520px; overflow:auto; }

/* small helper styles */
.small-muted { color: #777; font-size: 13px; }
.orphan-row { background: #fff7e6; }
</style>

<div class="navbar-admin d-flex justify-content-between align-items-center mb-3">
  <h4>Admin Dashboard</h4>
  <div>
    <span class="small">Logged in as </span><strong>{{ optional(Auth::user())->name ?: 'Admin' }}</strong>
    <form action="{{ route('admin.logout') }}" method="POST" class="d-inline">
      @csrf
      <button type="submit" class="btn btn-sm btn-light ml-2">Logout</button>
    </form>
  </div>
</div>

<div class="container-fluid mt-1">
  <div class="row">
    {{-- Sidebar --}}
    <div class="col-md-3 mb-3">
      <div class="sidebar">
        <h5><i class="glyphicon glyphicon-cog"></i> Admin Tools</h5>
        <hr>
        <a href="{{ route('admin.dashboard') }}"><i class="glyphicon glyphicon-dashboard"></i> Dashboard</a>
        <a href="{{ route('dashboard') }}"><i class="glyphicon glyphicon-home"></i> User View</a>
        <a href="#"><i class="glyphicon glyphicon-user"></i> Manage Users</a>
        <a href="#"><i class="glyphicon glyphicon-stats"></i> Analytics</a>
        <a href="#"><i class="glyphicon glyphicon-wrench"></i> Settings</a>
      </div>
    </div>

    {{-- Main --}}
    <div class="col-md-9">
      {{-- Summary Cards --}}
      <div class="row mb-4">
        @foreach (['Fire','Crime','Traffic','Others'] as $cat)
          @php
            $count = data_get($accidentCounts->firstWhere('accident_type', $cat), 'total', 0);
          @endphp
          <div class="col-md-3 mb-3">
            <div class="card text-center p-3">
              <div style="font-size:32px;">
                @if ($cat === 'Fire') 🔥 
                @elseif ($cat === 'Crime') 🕵️‍♀️ 
                @elseif ($cat === 'Traffic') 🚗 
                @else 📍 @endif
              </div>
              <h5>{{ $cat }}</h5>
              <div class="h4 mb-0">{{ $count }}</div>
              <small class="small-muted">reports</small>
            </div>
          </div>
        @endforeach
      </div>

      {{-- Top Locations --}}
      <div class="card mb-4">
        <div class="card-header bg-light">
          <strong>📍 Top Locations by Reported Incidents</strong>
        </div>
        <div class="card-body p-0">
          @if(!isset($topLocations) || $topLocations->isEmpty())
            <p class="p-3 small-muted mb-0">No data available.</p>
          @else
            <div class="table-responsive">
              <table class="table table-sm mb-0">
                <thead>
                  <tr><th>#</th><th>Location</th><th class="text-end">Reports</th></tr>
                </thead>
                <tbody>
                  @foreach($topLocations as $loc)
                    <tr>
                      <td>{{ $loop->iteration }}</td>
                      <td>{{ $loc->location ?: 'Unknown' }}</td>
                      <td class="text-end">{{ $loc->total }}</td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
          @endif
        </div>
      </div>

      {{-- Reported Posts --}}
      <div class="card">
        <div class="card-header bg-light d-flex justify-content-between align-items-center">
          <strong>🚨 Reported Posts</strong>
          <span class="small text-muted">
            {{ isset($reportedPosts) && method_exists($reportedPosts, 'total') ? $reportedPosts->total() : (isset($reportedPosts) ? count($reportedPosts) : 0) }} total
          </span>
        </div>

        <div class="card-body p-0">
          @if(!isset($reportedPosts) || (is_countable($reportedPosts) && count($reportedPosts) === 0))
            <p class="p-3 small-muted mb-0">No reported posts.</p>
          @else
            <div class="table-responsive">
              <table class="table table-striped mb-0">
                <thead>
                  <tr>
                    <th>Post</th>
                    <th>Author</th>
                    <th>Reporter(s)</th>
                    <th>Reason</th>
                    <th class="text-center"># Reports</th>
                    <th class="text-end">Actions</th>
                  </tr>
                </thead>

                <tbody>
                  {{-- Option 2: each $post is a Post model with ->reports and ->reports_count --}}
                  @foreach($reportedPosts as $post)
                    @php
                      // Defensive: ensure $post is an object
                      $postIsValid = is_object($post) && isset($post->id);
                      // If post is valid, ensure reports collection exists
                      $reports = collect($postIsValid ? ($post->reports ?? []) : []);
                      $reports_count = $postIsValid ? ($post->reports_count ?? $reports->count()) : $reports->count();
                    @endphp

                    @if(!$postIsValid)
                      {{-- Orphaned reports row (should not normally happen with Option 2, but we handle it) --}}
                      <tr class="orphan-row">
                        <td>
                          <strong style="color: var(--accent);">#(deleted)</strong>
                          <div class="small-muted">Target post was deleted — showing orphaned reports.</div>
                        </td>
                        <td><em class="text-muted">N/A</em></td>
                        <td>
                          @forelse($reports->take(3) as $r)
                            <div>{{ $r->user->name ?? 'Unknown' }} <span class="small-muted">({{ optional($r->created_at)->diffForHumans() }})</span></div>
                          @empty
                            <div class="small-muted">No reporters found</div>
                          @endforelse
                          @if($reports->count() > 3)
                            <div class="small-muted">+{{ $reports->count() - 3 }} more</div>
                          @endif
                        </td>
                        <td><span class="badge-category">{{ $reports->first()->reason ?? 'Unknown' }}</span></td>
                        <td class="text-center">{{ $reports->count() }}</td>
                        <td class="text-end">
                          {{-- resolve orphan reports route (optional) --}}
                          <form action="{{ route('admin.reports.resolveOrphan') }}" method="POST" class="d-inline" onsubmit="return confirm('Mark these orphaned reports as reviewed?')">
                            @csrf
                            <input type="hidden" name="report_ids" value="{{ $reports->pluck('id')->join(',') }}">
                            <button type="submit" class="btn btn-sm btn-success">Dismiss</button>
                          </form>
                        </td>
                      </tr>
                    @else
                      {{-- Normal post row --}}
                      <tr id="post-row-{{ $post->id }}">
                        <td style="max-width:300px;">
                          <strong style="color: var(--accent);">#{{ $post->id }}</strong>
                          <div class="ms-2">{{ Str::limit($post->content ?? '(No content)', 150) }}</div>

                          @if(!empty($post->image_url))
                            <div class="mt-2 post-media">
                              @if(($post->media_type ?? '') === 'video')
                                <video width="100%" height="120" controls>
                                  <source src="{{ $post->image_url }}" type="video/mp4">
                                  Your browser does not support the video tag.
                                </video>
                              @else
                                <img src="{{ $post->image_url }}" class="img-fluid rounded" alt="Post image">
                              @endif
                            </div>
                          @endif
                        </td>

                        <td>
                          @if(optional($post->user)->name)
                            <strong>{{ $post->user->name }}</strong><br>
                            <small class="text-muted">{{ $post->user->email }}</small>
                          @else
                            <em class="text-muted">User deleted</em>
                          @endif
                        </td>

                        <td>
                          @forelse($reports->take(2) as $r)
                            <div>{{ $r->user->name ?? 'Unknown Reporter' }} <span class="small-muted">({{ optional($r->created_at)->diffForHumans() }})</span></div>
                          @empty
                            <div class="small-muted">No reporters</div>
                          @endforelse
                          @if($reports->count() > 2)
                            <div class="small-muted">+{{ $reports->count() - 2 }} more</div>
                          @endif
                        </td>

                        <td>
                          <span class="badge-category">{{ $reports->first()->reason ?? 'Unknown' }}</span>
                        </td>

                        <td class="text-center">{{ $reports_count }}</td>

                        <td class="text-end">
                          {{-- Dismiss (resolve) reports for this post --}}
                          <form action="{{ route('admin.reports.resolve', ['post' => $post->id]) }}" method="POST" class="d-inline resolve-form">
                            @csrf
                            <input type="hidden" name="action" value="dismiss">
                            <button type="submit" class="btn btn-sm btn-success btn-resolve" data-post-id="{{ $post->id }}">Dismiss</button>
                          </form>

                          {{-- Remove post (admin action) --}}
                          <form action="{{ route('admin.posts.remove', ['post' => $post->id]) }}" method="POST" class="d-inline remove-post-form" onsubmit="return confirm('Are you sure you want to remove this post?');">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-danger">Remove</button>
                          </form>
                        </td>
                      </tr>
                    @endif
                  @endforeach
                </tbody>
              </table>
            </div>

            <div class="p-3">
              {{-- Pagination (if provided) --}}
              @if(method_exists($reportedPosts, 'links'))
                {{ $reportedPosts->links() }}
              @endif
            </div>
          @endif
        </div>
      </div>

    </div>
  </div>
</div>

{{-- OPTIONAL: include a tiny modal for async confirmations (not required by forms) --}}
<div class="modal fade" id="adminActionModal" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Confirm</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">&times;</button>
      </div>
      <div class="modal-body">
        <p id="adminActionMessage">Are you sure?</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
        <button id="adminActionConfirmBtn" type="button" class="btn btn-primary">Confirm</button>
      </div>
    </div>
  </div>
</div>

{{-- Scripts: jQuery + small AJAX handlers to keep the UI responsive.
     We keep standard form fallback (server-side) for compatibility. --}}
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
{{-- If you use bootstrap bundle in layout, omit next line; otherwise include for modal support --}}
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
$(function(){
  $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });

  // AJAX: resolve (dismiss) reports without reload (optimistic)
  $(document).on('submit', 'form.resolve-form', function(e){
    e.preventDefault();
    const $form = $(this);
    const url = $form.attr('action');
    const $btn = $form.find('button.btn-resolve').prop('disabled', true).text('Processing...');

    // POST via AJAX - server should accept and return success message
    $.post(url, $form.serialize())
      .done(function(res){
        // visually reduce the reports_count row or remove the row
        // we find table row
        const postId = $btn.data('post-id');
        if (postId) {
          const $row = $('#post-row-' + postId);
          // fade out row to indicate resolved (alternatively update UI)
          $row.fadeOut(350, function(){ $(this).remove(); });
        } else {
          // fallback: reload page to reflect server state
          location.reload();
        }
      })
      .fail(function(xhr){
        console.error('Resolve failed:', xhr.responseText || xhr.statusText);
        alert('Failed to mark reports resolved. Try refreshing the page.');
        $btn.prop('disabled', false).text('Dismiss');
      });
  });

  // Optional: intercept post remove form and use AJAX for faster feedback
  $(document).on('submit', 'form.remove-post-form', function(e){
    // keep default synchronous behavior if confirm is used inline; still provide AJAX path
    // If you prefer full-AJAX removal, uncomment the block below and comment out "return true;"
    /*
    e.preventDefault();
    if (!confirm('Are you sure you want to remove this post?')) return false;
    const $form = $(this);
    const url = $form.attr('action');
    const $btn = $form.find('button').prop('disabled', true).text('Removing...');

    $.post(url, $form.serialize())
      .done(function(res){
        // remove row
        const postId = url.match(/\/posts\/(\d+)\/remove/);
        if (postId && postId[1]) {
          $('#post-row-' + postId[1]).fadeOut(350, function(){ $(this).remove(); });
        } else {
          location.reload();
        }
      })
      .fail(function(){
        alert('Failed to remove post. Try again.');
        $btn.prop('disabled', false).text('Remove');
      });

    return false;
    */
    return true; // fall back to normal server POST (keeps behavior simple & robust)
  });

});
</script>

@endsection
