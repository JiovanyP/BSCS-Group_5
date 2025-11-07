{{-- resources/views/layouts/admin.blade.php --}}
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>@yield('title','Admin') — Publ.</title>

  {{-- Fonts / Icons --}}
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
  <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

  {{-- Bootstrap (used for utilities) --}}
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">

  <style>
    :root{
      --bg: #0b0f12;
      --panel: #0f1619;
      --muted: #98a0a8;
      --accent: #CF0F47;
      --accent-2: #FF0B55;
      --success: #18b26a;
      --danger: #e74c3c;
      --info: #1e90ff;
      --card-radius: 12px;
      --shadow: 0 12px 40px rgba(2,6,12,0.6);
    }
    html,body{height:100%;}
    body{
      margin:0;
      font-family: 'Inter', system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial;
      background: linear-gradient(180deg, #060708 0%, #071018 100%);
      color: #e6eef6;
      -webkit-font-smoothing:antialiased;
      -moz-osx-font-smoothing:grayscale;
      font-size:14px;
    }

    /* Layout - Fixed sidebar on the left */
    .admin-wrap { 
      display: flex;
      min-height: 100vh;
      padding: 0;
      margin: 0;
      gap: 0;
    }
    
    .admin-sidebar {
      flex: 0 0 260px;
      background: linear-gradient(180deg, var(--panel), #0c1113);
      padding: 24px 18px;
      box-shadow: 2px 0 20px rgba(0,0,0,0.4);
      height: 100vh;
      position: fixed; 
      left: 0;
      top: 0;
      overflow-y: auto;
      overflow-x: hidden;
      order: 1;
      z-index: 100;
    }
    
    .admin-main {
      flex: 1;
      min-width: 0;
      margin-left: 260px;
      width: calc(100% - 260px);
      order: 2;
      padding: 32px 24px;
      padding-bottom: 44px;
      max-width: none;
    }
    
    .admin-brand { display:flex; gap:12px; align-items:center; margin-bottom:12px; }
    .brand-logo { width:46px; height:46px; border-radius:10px; background:var(--accent); color:#111; display:flex; align-items:center; justify-content:center; font-weight:800; font-size:18px; }
    .brand-name { font-weight:700; letter-spacing:0.6px; }
    .brand-sub { color:var(--muted); font-size:12px; margin-top:3px; }

    .admin-nav { margin-top:14px; display:flex; flex-direction:column; gap:6px; }
    .admin-nav a { color:var(--muted); display:flex; align-items:center; gap:10px; padding:10px 12px; border-radius:10px; text-decoration:none; font-weight:600; transition: all .15s ease; }
    .admin-nav a svg { opacity:0.9; }
    .admin-nav a:hover { background: rgba(255,255,255,0.02); color:#fff; transform:translateX(6px); }

    .sidebar-footer { margin-top:18px; }

    /* Main area */
    .admin-header { display:flex; justify-content:space-between; align-items:center; gap:12px; margin-bottom:14px; }
    .admin-title { background: linear-gradient(90deg, rgba(255,255,255,0.02), transparent); padding:12px 16px; border-radius:10px; box-shadow:0 8px 20px rgba(0,0,0,0.5); }
    .admin-title h1{ margin:0; font-size:18px; }
    .admin-meta { color:var(--muted); font-size:13px; margin-top:6px; }

    /* Cards */
    .stats-row { display:grid; grid-template-columns: repeat(4,1fr); gap:12px; margin-bottom:14px; }
    .stat { background:linear-gradient(180deg,var(--panel), rgba(255,255,255,0.01)); padding:12px; border-radius:10px; text-align:center; }
    .stat .label{ color:var(--muted); font-weight:700; font-size:13px; }
    .stat .value{ font-weight:800; font-size:18px; margin-top:6px; }

    /* Reports layout */
    .reports-grid { display:grid; grid-template-columns: 1fr 360px; gap:12px; align-items:start; }
    .reports-panel { background: linear-gradient(180deg,var(--panel), #0b1113); padding:12px; border-radius:12px; box-shadow:0 8px 30px rgba(0,0,0,0.6); }
    .report-row { display:flex; gap:12px; border-radius:10px; padding:12px; align-items:flex-start; background: linear-gradient(180deg, rgba(255,255,255,0.01), transparent); margin-bottom:10px; }
    .report-left { flex:1; min-width:0; }
    .post-id { color:var(--accent); font-weight:800; }
    .post-content { color:#dfe9f2; margin-top:6px; line-height:1.4; max-height:72px; overflow:hidden; text-overflow:ellipsis; display:-webkit-box; -webkit-line-clamp:4; -webkit-box-orient:vertical; }
    .post-meta { margin-top:8px; color:var(--muted); font-size:13px; display:flex; gap:10px; align-items:center; flex-wrap:wrap; }

    .thumb { width:132px; height:84px; border-radius:8px; overflow:hidden; flex-shrink:0; background: rgba(255,255,255,0.02); display:flex; align-items:center; justify-content:center; color:var(--muted); font-size:13px; }
    .thumb img{ width:100%; height:100%; object-fit:cover; display:block; }

    /* Triple action group (horizontal stack, fixed area) */
    .report-actions { width:170px; display:flex; gap:10px; justify-content:flex-end; align-items:center; flex-direction:column; }
    .action-row { display:flex; gap:8px; width:100%; }
    .btn-action {
      flex:1; display:inline-flex; align-items:center; justify-content:center; gap:8px;
      padding:8px 10px; border-radius:10px; color:#fff; border:none; cursor:pointer; font-weight:700;
      box-shadow: 0 6px 18px rgba(2,6,12,0.6);
    }
    .btn-dismiss { background: linear-gradient(180deg,var(--success), #12a85b); }
    .btn-remove  { background: linear-gradient(180deg,var(--danger), #c43932); }
    .btn-view    { background: linear-gradient(180deg,var(--info), #076fb8); text-decoration:none; color:#fff; display:inline-flex; align-items:center; justify-content:center; }
    .btn-disabled { opacity:0.5; cursor:not-allowed; }

    /* small pill */
    .reason-pill { display:inline-block; padding:6px 10px; border-radius:999px; background:rgba(255,255,255,0.02); color:var(--muted); font-weight:700; font-size:12px; }

    /* Pagination wrapper */
    .pag { margin-top:8px; }

    @media (max-width: 1024px){ 
      .stats-row{ grid-template-columns: repeat(2,1fr);} 
      .reports-grid{ grid-template-columns: 1fr; } 
      .admin-sidebar { flex: 0 0 220px; width: 220px; }
      .admin-main { margin-left: 220px; }
      .report-actions{ flex-direction:row; width:auto; } 
    }
    
    @media (max-width: 640px){ 
      .admin-sidebar{ 
        position: fixed;
        left: -260px;
        transition: left 0.3s ease;
        height: 100vh;
      } 
      .admin-sidebar.open { left: 0; }
      .admin-main { margin-left: 0; width: 100%; }
      .report-actions{ width:100%; } 
      .action-row{ width:100%; } 
    }
  </style>

  @stack('head')
</head>
<body>
  <div class="admin-wrap">
    {{-- SIDEBAR (left side) --}}
    <aside class="admin-sidebar" role="navigation" aria-label="Admin sidebar">
      <div class="admin-brand">
        <div class="brand-logo">P</div>
        <div>
          <div class="brand-name">Publ. Admin</div>
          <div class="brand-sub">{{ optional(Auth::guard('admin')->user())->role ?? 'Super Administrator' }}</div>
        </div>
      </div>

      <nav class="admin-nav" aria-label="Admin tools">
        <a href="{{ route('admin.dashboard') }}"><svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M3 13h8V3H3v10zM13 21h8V11h-8v10zM13 3v6h8V3h-8zM3 21h8v-8H3v8z"/></svg> Dashboard</a>

        <a href="{{ route('admin.reports.index') }}"><svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2L2 7v6c0 5 4 9 10 9s10-4 10-9V7l-10-5z"/></svg> Reports <span style="margin-left:auto" class="reason-pill">{{ isset($reportedPosts) ? (is_countable($reportedPosts) ? count($reportedPosts) : ($reportedPosts->total() ?? 0)) : 0 }}</span></a>

        <a href="{{ route('admin.notifications.index') }}"><svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M12 22a2 2 0 0 0 2-2H10a2 2 0 0 0 2 2zM19 17H5v-6a7 7 0 0 1 14 0v6z"/></svg> Notifications</a>

        <a href="{{ route('admin.users.index') }}"><svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5s-3 1.34-3 3 1.34 3 3 3zM6 8c0 1.66 1.34 3 3 3s3-1.34 3-3S10.66 5 9 5 6 6.34 6 8zM6 20v-1c0-2.21 4-3.34 6-3.34s6 1.13 6 3.34V20H6z"/></svg> Manage Users</a>

        <a href="{{ route('admin.analytics') }}"><svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M3 13h4v8H3zM10 3h4v18h-4zM17 8h4v13h-4z"/></svg> Analytics</a>

        <a href="{{ route('admin.settings') }}"><svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M19.14 12.936a7.07 7.07 0 000-1.872l2.03-1.57a.5.5 0 00.12-.64l-1.92-3.32a.5.5 0 00-.6-.22l-2.39.96a7.023 7.023 0 00-1.62-.94L14.5 2.3a.5.5 0 00-.5-.3h-4a.5.5 0 00-.5.3l-.38 2.8c-.58.22-1.12.5-1.62.94l-2.39-.96a.5.5 0 00-.6.22L2.7 8.88a.5.5 0 00.12.64l2.03 1.57c-.05.31-.07.63-.07.94 0 .31.02.63.07.94L2.82 14.6a.5.5 0 00-.12.64l1.92 3.32a.5.5 0 00.6.22l2.39-.96c.5.44 1.04.82 1.62.94l.38 2.8a.5.5 0 00.5.3h4a.5.5 0 00.5-.3l.38-2.8c.58-.12 1.12-.5 1.62-.94l2.39.96a.5.5 0 00.6-.22l1.92-3.32a.5.5 0 00-.12-.64l-2.03-1.57z"/></svg> Settings</a>
      </nav>

      <div class="sidebar-footer">
        <form action="{{ route('admin.logout') }}" method="POST">
          @csrf
          <button type="submit" class="btn btn-block btn-outline-light" style="border-radius:10px; padding:10px; font-weight:700;">
            Logout
          </button>
        </form>
      </div>
    </aside>

    {{-- MAIN CONTENT (right side) --}}
    <main class="admin-main">
      <div style="padding-bottom:18px;">
        @yield('content')
      </div>
    </main>
  </div>

  {{-- Scripts --}}
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
  @stack('scripts')