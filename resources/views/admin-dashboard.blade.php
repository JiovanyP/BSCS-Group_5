@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">

<style>
:root {
    --accent: #CF0F47;
    --accent-hover: #FF0B55;
    --muted: #666;
}
body { background:#f9fafc; }

.navbar-admin {
    background: var(--accent);
    color: #fff;
    padding: 12px 20px;
}
.navbar-admin h4 {
    margin: 0;
    font-weight: 700;
    letter-spacing: 1px;
}

.sidebar {
    background: #fff;
    border-radius: 10px;
    box-shadow: 0 3px 12px rgba(0,0,0,0.05);
    padding: 20px;
}
.sidebar a {
    display:block;
    color:#333;
    padding:8px 10px;
    border-radius:6px;
    transition:0.2s;
    text-decoration:none;
}
.sidebar a:hover {
    background: var(--accent);
    color: white;
}
.card {
    border:none;
    border-radius:12px;
    box-shadow:0 2px 8px rgba(0,0,0,0.05);
    background:white;
}
.card h5 { font-weight:600; }
.table thead th { background:#fafafa; }

.badge-category {
    background: var(--accent);
    color: white;
    padding: 6px 10px;
    border-radius: 8px;
    font-size: 13px;
}
.btn-danger {
    background: var(--accent);
    border: none;
}
.btn-danger:hover { background: var(--accent-hover); }
</style>

<div class="navbar-admin d-flex justify-content-between align-items-center">
    <h4>Admin Dashboard</h4>
    <div>
        <span class="small">Logged in as </span><strong>{{ Auth::user()->name }}</strong>
        <form action="{{ route('logout') }}" method="POST" class="d-inline">
            @csrf
            <button type="submit" class="btn btn-sm btn-light ml-2">Logout</button>
        </form>
    </div>
</div>

<div class="container-fluid mt-4">
  <div class="row">
    <!-- Sidebar -->
    <div class="col-md-3">
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

    <!-- Main Content -->
    <div class="col-md-9">
      <!-- Summary Cards -->
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
                    <small class="text-muted">reports</small>
                </div>
            </div>
        @endforeach
      </div>

      <!-- Top Locations -->
      <div class="card mb-4">
        <div class="card-header bg-light">
          <strong>📍 Top Locations by Reported Incidents</strong>
        </div>
        <div class="card-body p-0">
          @if($topLocations->isEmpty())
            <p class="p-3 text-muted">No data available.</p>
          @else
            <table class="table mb-0">
              <thead>
                <tr>
                  <th>#</th>
                  <th>Location</th>
                  <th class="text-end">Reports</th>
                </tr>
              </thead>
              <tbody>
                @foreach($topLocations as $index => $loc)
                  <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $loc->location ?: 'Unknown' }}</td>
                    <td class="text-end">{{ $loc->total }}</td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          @endif
        </div>
      </div>

      <!-- Reported Posts -->
      <div class="card">
        <div class="card-header bg-light d-flex justify-content-between align-items-center">
          <strong>🚨 Reported Posts</strong>
          <span class="small text-muted">{{ $reportedPosts->total() }} total</span>
        </div>
        <div class="card-body p-0">
          @if($reportedPosts->isEmpty())
            <p class="p-3 text-muted mb-0">No reported posts.</p>
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
                      $reports = $post->reports;
                    @endphp
                    <tr>
                      <td style="max-width:260px;">
                        <strong>#{{ $post->id }}</strong> {{ Str::limit($post->content, 100) }}
                        @if($post->image_url)
                            <div class="mt-2">
                                @if($post->media_type === 'video')
                                    <video width="100%" height="120" controls>
                                        <source src="{{ $post->image_url }}" type="video/mp4">
                                    </video>
                                @else
                                    <img src="{{ $post->image_url }}" class="img-fluid rounded">
                                @endif
                            </div>
                        @endif
                      </td>
                      <td>
                        <strong>{{ $post->user->name }}</strong><br>
                        <small class="text-muted">{{ $post->user->email }}</small>
                      </td>
                      <td>
                        @foreach($reports->take(2) as $r)
                          <div>{{ $r->user->name }} <span class="small text-muted">({{ $r->created_at->diffForHumans() }})</span></div>
                        @endforeach
                        @if($reports->count() > 2)
                          <div class="small text-muted">+{{ $reports->count() - 2 }} more</div>
                        @endif
                      </td>
                      <td><span class="badge-category">{{ $reports->first()->reason }}</span></td>
                      <td class="text-center">{{ $post->reports_count }}</td>
                      <td class="text-end">
                        <form action="{{ route('admin.reports.resolve', ['post'=>$post->id]) }}" method="POST" class="d-inline">
                            @csrf
                            <input type="hidden" name="action" value="dismiss">
                            <button type="submit" class="btn btn-sm btn-success" title="Mark reviewed">Dismiss</button>
                        </form>
                        <form action="{{ route('admin.posts.remove', ['post'=>$post->id]) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-danger" title="Remove post">Remove</button>
                        </form>
                      </td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
            <div class="p-3">{{ $reportedPosts->links() }}</div>
          @endif
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
