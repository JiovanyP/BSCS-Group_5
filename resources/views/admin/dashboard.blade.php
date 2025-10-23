<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Admin Dashboard</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <style>
        :root {
            --accent: #CF0F47;
            --accent-hover: #FF0B55;
            --accent-light: rgba(207, 15, 71, 0.1);
            --muted: #666;
            --border-radius: 16px;
            --shadow-sm: 0 2px 8px rgba(0,0,0,0.04);
            --shadow-md: 0 4px 16px rgba(0,0,0,0.08);
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body { 
            background: linear-gradient(135deg, #f5f7fa 0%, #e8ecf1 100%);
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            min-height: 100vh;
        }

        .navbar-admin {
            background: linear-gradient(135deg, var(--accent) 0%, #a00c38 100%);
            color: #fff;
            padding: 20px 0;
            box-shadow: 0 4px 20px rgba(207, 15, 71, 0.2);
            position: sticky;
            top: 0;
            z-index: 1000;
        }
        
        .navbar-admin h4 {
            margin: 0;
            font-weight: 700;
            letter-spacing: 0.5px;
            font-size: 1.5rem;
        }
        
        .navbar-admin .user-info {
            display: flex;
            align-items: center;
            gap: 16px;
        }
        
        .navbar-admin .btn-light {
            padding: 8px 20px;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        
        .navbar-admin .btn-light:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }

        .dashboard-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 40px 24px;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 24px;
            margin-bottom: 40px;
        }

        .stat-card {
            background: white;
            border: none;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-sm);
            padding: 28px 24px;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        
        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--accent), var(--accent-hover));
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        
        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow-md);
        }
        
        .stat-card:hover::before {
            opacity: 1;
        }
        
        .stat-icon {
            font-size: 48px;
            margin-bottom: 16px;
            display: inline-block;
            filter: drop-shadow(0 2px 4px rgba(0,0,0,0.1));
        }
        
        .stat-card h5 {
            font-weight: 600;
            color: #2d3748;
            font-size: 1rem;
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .stat-number {
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--accent);
            line-height: 1;
            margin-bottom: 4px;
        }
        
        .stat-label {
            font-size: 0.875rem;
            color: var(--muted);
            font-weight: 500;
        }

        .content-card {
            background: white;
            border: none;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-sm);
            margin-bottom: 32px;
            overflow: hidden;
        }
        
        .card-header {
            background: linear-gradient(135deg, #fafbfc 0%, #f5f7fa 100%);
            border-bottom: 2px solid #e8ecf1;
            padding: 20px 28px;
            font-weight: 600;
            color: #2d3748;
            font-size: 1.1rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .card-header strong {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .table {
            margin-bottom: 0;
        }
        
        .table thead th {
            background: #fafbfc;
            border-bottom: 2px solid #e8ecf1;
            color: #4a5568;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.5px;
            padding: 16px 20px;
        }
        
        .table tbody td {
            padding: 20px;
            vertical-align: middle;
            border-bottom: 1px solid #f1f3f5;
        }
        
        .table tbody tr:last-child td {
            border-bottom: none;
        }
        
        .table tbody tr {
            transition: background-color 0.2s ease;
        }
        
        .table tbody tr:hover {
            background-color: #fafbfc;
        }

        .badge-category {
            background: linear-gradient(135deg, var(--accent), var(--accent-hover));
            color: white;
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 0.813rem;
            font-weight: 600;
            display: inline-block;
            box-shadow: 0 2px 8px rgba(207, 15, 71, 0.2);
        }
        
        .btn {
            border-radius: 8px;
            font-weight: 500;
            padding: 8px 16px;
            transition: all 0.3s ease;
            border: none;
        }
        
        .btn-success {
            background: linear-gradient(135deg, #10b981, #059669);
            color: white;
        }
        
        .btn-success:hover {
            background: linear-gradient(135deg, #059669, #047857);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
        }
        
        .btn-danger {
            background: linear-gradient(135deg, var(--accent), var(--accent-hover));
        }
        
        .btn-danger:hover {
            background: linear-gradient(135deg, var(--accent-hover), #d91650);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(207, 15, 71, 0.3);
        }
        
        .post-media {
            margin-top: 12px;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: var(--shadow-sm);
        }
        
        .post-media img,
        .post-media video {
            border-radius: 8px;
        }
        
        .reporter-info {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }
        
        .reporter-item {
            padding: 6px 0;
        }
        
        .pagination {
            justify-content: center;
            gap: 8px;
        }
        
        .page-link {
            border-radius: 8px;
            border: 1px solid #e8ecf1;
            color: var(--accent);
            font-weight: 500;
            padding: 8px 16px;
            transition: all 0.2s ease;
        }
        
        .page-link:hover {
            background: var(--accent-light);
            border-color: var(--accent);
            color: var(--accent);
        }
        
        .page-item.active .page-link {
            background: var(--accent);
            border-color: var(--accent);
        }
        
        .empty-state {
            padding: 60px 40px;
            text-align: center;
            color: var(--muted);
        }
        
        .action-buttons {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }
        
        @media (max-width: 768px) {
            .dashboard-container {
                padding: 24px 16px;
            }
            
            .stats-grid {
                grid-template-columns: 1fr;
                gap: 16px;
            }
            
            .navbar-admin h4 {
                font-size: 1.2rem;
            }
            
            .stat-number {
                font-size: 2rem;
            }
        }
    </style>
</head>
<body>

<div class="navbar-admin">
    <div class="container-fluid px-4">
        <div class="d-flex justify-content-between align-items-center">
            <h4>🛡️ Admin Dashboard</h4>
            <div class="user-info">
                <div class="text-end d-none d-md-block">
                    <div class="small opacity-75">Logged in as</div>
                    <strong>{{ Auth::user()->name }}</strong>
                </div>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-light">Logout</button>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="dashboard-container">
    <!-- Statistics Cards -->
    <div class="stats-grid">
        @foreach (['Fire','Crime','Traffic','Others'] as $cat)
            @php
                $count = data_get($accidentCounts->firstWhere('accident_type', $cat), 'total', 0);
                $icons = ['Fire' => '🔥', 'Crime' => '🕵️‍♀️', 'Traffic' => '🚗', 'Others' => '📍'];
            @endphp
            <div class="stat-card text-center">
                <div class="stat-icon">{{ $icons[$cat] }}</div>
                <h5>{{ $cat }}</h5>
                <div class="stat-number">{{ $count }}</div>
                <div class="stat-label">reports</div>
            </div>
        @endforeach
    </div>

    <!-- Top Locations -->
    <div class="content-card">
        <div class="card-header">
            <strong>📍 Top Locations by Reported Incidents</strong>
        </div>
        <div class="card-body p-0">
            @if($topLocations->isEmpty())
                <div class="empty-state">
                    <div style="font-size: 48px; margin-bottom: 16px;">📊</div>
                    <p class="mb-0">No location data available yet.</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th style="width: 80px;">#</th>
                                <th>Location</th>
                                <th class="text-end" style="width: 150px;">Reports</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($topLocations as $index => $loc)
                                <tr>
                                    <td><strong>{{ $loop->iteration }}</strong></td>
                                    <td>{{ $loc->location ?: 'Unknown' }}</td>
                                    <td class="text-end"><strong>{{ $loc->total }}</strong></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

    <!-- Reported Posts -->
    <div class="content-card">
        <div class="card-header">
            <strong>🚨 Reported Posts</strong>
            <span class="badge bg-secondary">{{ $reportedPosts->total() }} total</span>
        </div>
        <div class="card-body p-0">
            @if($reportedPosts->isEmpty())
                <div class="empty-state">
                    <div style="font-size: 48px; margin-bottom: 16px;">✅</div>
                    <p class="mb-0">No reported posts to review.</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th style="width: 35%;">Post</th>
                                <th style="width: 15%;">Author</th>
                                <th style="width: 18%;">Reporter(s)</th>
                                <th style="width: 12%;">Reason</th>
                                <th class="text-center" style="width: 8%;"># Reports</th>
                                <th class="text-end" style="width: 12%;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($reportedPosts as $group)
                                @php
                                    $post = $group->post;
                                    $reports = $group->reports;
                                @endphp
                                <tr>
                                    <td>
                                        <div class="mb-2">
                                            <strong style="color: var(--accent);">#{{ $post->id }}</strong>
                                            <span class="ms-2">{{ Str::limit($post->content, 100) }}</span>
                                        </div>
                                        @if($post->image_url)
                                            <div class="post-media">
                                                @if($post->media_type === 'video')
                                                    <video width="100%" height="120" controls>
                                                        <source src="{{ $post->image_url }}" type="video/mp4">
                                                    </video>
                                                @else
                                                    <img src="{{ $post->image_url }}" class="img-fluid rounded" alt="Post image">
                                                @endif
                                            </div>
                                        @endif
                                    </td>
                                    <td>
                                        <div>
                                            <strong>{{ $post->user->name }}</strong>
                                        </div>
                                        <small class="text-muted">{{ $post->user->email }}</small>
                                    </td>
                                    <td>
                                        <div class="reporter-info">
                                            @foreach($reports->take(2) as $r)
                                                <div class="reporter-item">
                                                    <div>{{ $r->user->name }}</div>
                                                    <small class="text-muted">{{ $r->created_at->diffForHumans() }}</small>
                                                </div>
                                            @endforeach
                                            @if($reports->count() > 2)
                                                <small class="text-muted">+{{ $reports->count() - 2 }} more</small>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge-category">{{ $reports->first()->reason }}</span>
                                    </td>
                                    <td class="text-center">
                                        <strong style="font-size: 1.1rem; color: var(--accent);">{{ $group->reports_count }}</strong>
                                    </td>
                                    <td>
                                        <div class="action-buttons justify-content-end">
                                            <form action="{{ route('admin.reports.resolve', ['post'=>$post->id]) }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="action" value="dismiss">
                                                <button type="submit" class="btn btn-sm btn-success" title="Mark reviewed">Dismiss</button>
                                            </form>
                                            <form action="{{ route('admin.posts.remove', ['post'=>$post->id]) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-danger" title="Remove post">Remove</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="p-4">
                    {{ $reportedPosts->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>