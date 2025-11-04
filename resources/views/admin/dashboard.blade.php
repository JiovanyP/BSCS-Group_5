{{-- resources/views/admin/dashboard.blade.php --}}
@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">

<style>
:root {
  --accent: #CF0F47;
  --accent-hover: #FF0B55;
  --muted: #666;
  --card-radius: 12px;
}
body { background:#f9fafc; }

.navbar-admin {
  background: var(--accent);
  color: #fff;
  padding: 12px 20px;
  border-radius: 8px;
}
.navbar-admin h4 { margin:0; font-weight:700; letter-spacing:1px; }

.sidebar {
  background:#fff;
  border-radius:10px;
  box-shadow:0 3px 12px rgba(0,0,0,0.05);
  padding:20px;
}
.sidebar a { display:block; color:#333; padding:8px 10px; border-radius:6px; text-decoration:none; }
.sidebar a:hover { background:var(--accent); color:#fff; }

.card { background:#fff; border-radius:var(--card-radius); border:none; box-shadow:0 2px 8px rgba(0,0,0,0.05); }
.card h5 { font-weight:600; }

.badge-category { background:var(--accent); color:#fff; padding:6px 10px; border-radius:8px; font-size:13px; }
.btn-danger { background:var(--accent); border:none; color:#fff; }
.btn-danger:hover { background:var(--accent-hover); }
.btn-info { background-color: #007bff; border:none; color:#fff; }
.btn-info:hover { background-color: #0056b3; }

.table thead th { background:#fafafa; }
.table-responsive { max-height:520px; overflow:auto; }

.post-media img, .post-media video { max-height:120px; object-fit:cover; border-radius:8px; }

.small-muted { color:#777; font-size:13px; }
.orphan-row { background:#fff7e6; }

/* spacing for stacked buttons */
.btn-sm + .btn-sm, .btn-sm + .d-block { margin-top:5px; }

/* Ensure visibility of the View button */
.view-post-link {
  display:block !important;
  text-align:center;
  text-decoration:none !important;
  padding:6px 10px !important;
  border-radius:18px !important;
  font-weight:700;
  width:86px;
  margin-top:6px !important;
  background:#1e90ff !important;
  color:#fff !important;
  box-shadow:0 3px 8px rgba(0,0,0,0.12);
}
</style>

{{-- Navbar --}}
<div class="navbar-admin d-flex justify-content-between align-items-center mb-3">
  <h4>Admin Dashboard</h4>
  <div>
    <span class="small">Logged in as </span>
    <strong>{{ optional(Auth::user())->name ?: 'Admin' }}</strong>
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

    {{-- Main Content --}}
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
                @if($cat === 'Fire') 🔥 
                @elseif($cat === 'Crime') 🕵️‍♀️ 
                @elseif($cat === 'Traffic') 🚗 
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
          @if(empty($topLocations) || $topLocations->isEmpty())
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
          @if(empty($reportedPosts) || (is_countable($reportedPosts) && count($reportedPosts) === 0))
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
                  @foreach($reportedPosts as $post)
                    @php
                      $isValid = $post && is_object($post) && isset($post->id);
                      $reports = $isValid ? ($post->reports ?? collect()) : collect();
                      $reports_count = $isValid ? ($post->reports_count ?? $reports->count()) : $reports->count();
                    @endphp

                    @if(!$isValid)
                      <tr class="orphan-row">
                        <td>
                          <strong style="color:var(--accent);">#(deleted)</strong>
                          <div class="small-muted">Original post deleted — orphaned reports retained.</div>
                        </td>
                        <td><em class="text-muted">N/A</em></td>
                        <td>
                          @forelse($reports->take(3) as $r)
                            <div>{{ optional($r->user)->name ?? 'Unknown' }} <span class="small-muted">({{ optional($r->created_at)->diffForHumans() }})</span></div>
                          @empty
                            <div class="small-muted">No reporters</div>
                          @endforelse
                          @if($reports->count() > 3)
                            <div class="small-muted">+{{ $reports->count() - 3 }} more</div>
                          @endif
                        </td>
                        <td><span class="badge-category">{{ $reports->first()->reason ?? 'Unknown' }}</span></td>
                        <td class="text-center">{{ $reports->count() }}</td>
                        <td class="text-end">
                          <form action="{{ route('admin.reports.resolveOrphan') }}" method="POST" class="d-inline">
                            @csrf
                            <input type="hidden" name="report_ids" value="{{ $reports->pluck('id')->join(',') }}">
                            <button type="submit" class="btn btn-sm btn-success">Dismiss</button>
                          </form>
                        </td>
                      </tr>
                      @continue
                    @endif

                    <tr id="post-row-{{ $post->id }}">
                      <td style="max-width:300px;">
                        <strong style="color:var(--accent);">#{{ $post->id }}</strong>
                        <div class="ms-2">{{ Str::limit($post->content ?? '(No content)',150) }}</div>

                        @if(!empty($post->image))
                          <div class="mt-2 post-media">
                            @if(($post->media_type ?? '') === 'video')
                              <video width="100%" height="120" controls>
                                <source src="{{ asset('storage/' . $post->image) }}" type="video/mp4">
                              </video>
                            @else
                              <img src="{{ asset('storage/' . $post->image) }}" class="img-fluid rounded" alt="Post image">
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
                          <div>{{ optional($r->user)->name ?? 'Unknown' }} <span class="small-muted">({{ optional($r->created_at)->diffForHumans() }})</span></div>
                        @empty
                          <div class="small-muted">No reporters</div>
                        @endforelse
                        @if($reports->count() > 2)
                          <div class="small-muted">+{{ $reports->count() - 2 }} more</div>
                        @endif
                      </td>

                      <td><span class="badge-category">{{ $reports->first()->reason ?? 'Unknown' }}</span></td>
                      <td class="text-center">{{ $reports_count }}</td>
                      <td class="text-end">
                        {{-- Dismiss reports --}}
                        <form action="{{ route('admin.reports.resolve',['post'=>$post->id]) }}" method="POST" class="d-inline resolve-form">
                          @csrf
                          <input type="hidden" name="action" value="dismiss">
                          <button type="submit" class="btn btn-sm btn-success btn-resolve" data-post-id="{{ $post->id }}">Dismiss</button>
                        </form>

                        {{-- Remove post --}}
                        <form action="{{ route('admin.posts.remove',['post'=>$post->id]) }}" method="POST" class="d-inline remove-post-form" onsubmit="return confirm('Are you sure you want to remove this post?');">
                          @csrf
                          <button type="submit" class="btn btn-sm btn-danger">Remove</button>
                        </form>

                        {{-- View post (Option 2 admin route) --}}
                        <a href="{{ route('admin.posts.view', ['id' => $post->id]) }}"
                           target="_blank"
                           class="view-post-link"
                           data-post-id="{{ $post->id }}">
                           View
                        </a>
                      </td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>

            <div class="p-3">
              @if(method_exists($reportedPosts,'links'))
                {{ $reportedPosts->links() }}
              @endif
            </div>
          @endif
        </div>
      </div>
    </div>
  </div>
</div>

{{-- jQuery + Bootstrap scripts --}}
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
$(function(){
  $.ajaxSetup({ headers:{'X-CSRF-TOKEN':$('meta[name="csrf-token"]').attr('content')} });

  // Dismiss reports asynchronously
  $(document).on('submit','form.resolve-form',function(e){
    e.preventDefault();
    const $form=$(this);
    const $btn=$form.find('button.btn-resolve').prop('disabled',true).text('Processing...');
    $.post($form.attr('action'),$form.serialize())
      .done(function(){
        const postId=$btn.data('post-id');
        $('#post-row-'+postId).fadeOut(350,function(){$(this).remove();});
      })
      .fail(function(){alert('Failed to dismiss reports');})
      .always(function(){$btn.prop('disabled',false).text('Dismiss');});
  });
});
</script>
@endsection
