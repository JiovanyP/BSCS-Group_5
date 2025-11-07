{{-- resources/views/layouts/admin.blade.php --}}
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>@yield('title','Admin') — Publ.</title>

  {{-- Fonts / Icons --}}
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap
" rel="stylesheet">
  <link href="https://fonts.googleapis.com/icon?family=Material+Icons
" rel="stylesheet">

  {{-- Bootstrap (kept) --}}
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css
">

  <style>
    :root {
      --bg: #0b0f12;
      --panel: #101618;
      --panel-opaque: #0f1416; /* solid bg for sidebar so it won't visually blend */
      --muted: #98a0a8;
      --accent: #CF0F47;
      --card-radius: 12px;
      --shadow: 0 12px 40px rgba(2,6,12,0.6);
      --sidebar-width: 260px;
      --breakpoint: 900px;
    }

    html, body {
      height: 100%;
      margin: 0;
      font-family: 'Inter', system-ui, -apple-system, 'Segoe UI', Roboto, Arial;
      background: linear-gradient(180deg, #060708 0%, #071018 100%);
      color: #e6eef6;
      font-size: 14px;
      -webkit-font-smoothing: antialiased;
      -moz-osx-font-smoothing: grayscale;
      overflow-x: hidden;
    }

    /* WRAPPER */
    .admin-wrap {
      display: flex;
      min-height: 100vh;
      overflow-x: hidden;
      position: relative;
      background: var(--bg);
    }

    /* SIDEBAR - fixed on left */
    .admin-sidebar {
      width: var(--sidebar-width);
      background: var(--panel-opaque); /* use opaque background so main won't show through */
      color: var(--muted);
      height: 100vh;
      position: fixed;
      left: 0;
      top: 0;
      z-index: 1000;
      padding: 20px 16px;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
      box-shadow: 2px 0 20px rgba(0, 0, 0, 0.4);
      transition: transform 0.28s ease, width 0.28s ease, left 0.28s ease;
    }

    /* collapsed state for desktop */
    .admin-sidebar.closed {
      width: 72px; /* narrow collapsed width showing icons (if you use icons later) */
      transform: none;
      overflow: hidden;
      padding-left: 10px;
      padding-right: 10px;
    }

    /* slide-in state for mobile */
    .admin-sidebar.open {
      transform: translateX(0);
    }

    .admin-brand {
      display: flex;
      align-items: center;
      gap: 12px;
    }

    .brand-logo {
      width: 42px;
      height: 42px;
      background: var(--accent);
      color: #111;
      border-radius: 10px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-weight: 800;
      font-size: 18px;
    }

    .brand-name { font-weight: 700; }
    .brand-sub { font-size: 12px; color: var(--muted); }

    /* NAV */
    .admin-nav {
      display: flex;
      flex-direction: column;
      gap: 6px;
      margin-top: 18px;
    }

    .admin-nav a {
      display: flex;
      align-items: center;
      gap: 10px;
      text-decoration: none;
      color: var(--muted);
      font-weight: 600;
      padding: 10px 12px;
      border-radius: 10px;
      transition: 0.15s ease;
      white-space: nowrap;
    }

    .admin-nav a:hover,
    .admin-nav a.active {
      color: #fff;
      background: rgba(255,255,255,0.04);
      transform: translateX(4px);
    }

    /* MAIN content */
    .admin-main {
      margin-left: var(--sidebar-width);
      width: calc(100% - var(--sidebar-width));
      padding: 30px 24px 60px;
      transition: margin-left 0.28s ease, width 0.28s ease, padding 0.2s ease;
      z-index: 1;
      position: relative;
      min-height: 100vh;
    }

    /* when sidebar is collapsed on desktop */
    .admin-main.sidebar-collapsed {
      margin-left: 72px;
      width: calc(100% - 72px);
    }

    .admin-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 18px;
    }

    .admin-title h1 {
      font-size: 18px;
      margin: 0;
      font-weight: 700;
    }

    .admin-meta {
      color: var(--muted);
      font-size: 13px;
    }

    .toggle-btn {
      background: transparent;
      border: 1px solid rgba(255,255,255,0.05);
      border-radius: 8px;
      color: var(--muted);
      padding: 8px 10px;
      display: flex;
      align-items: center;
      justify-content: center;
      transition: 0.18s;
    }

    .toggle-btn:hover {
      border-color: var(--accent);
      color: #fff;
    }

    /* Overlay - hidden by default on desktop */
    .sidebar-overlay {
      display: none; /* default hidden on desktop */
      position: fixed;
      top: 0; left: 0;
      width: 100%; height: 100%;
      background: rgba(0,0,0,0.55);
      z-index: 900;
      opacity: 0;
      visibility: hidden;
      transition: opacity 0.22s ease;
    }

    .sidebar-overlay.active {
      opacity: 1;
      visibility: visible;
    }

    /* MOBILE / small screens */
    @media (max-width: 900px) {
      .admin-sidebar {
        transform: translateX(-100%);
        left: 0;
      }

      .admin-sidebar.open {
        transform: translateX(0);
        left: 0;
      }

      .admin-sidebar.closed {
        /* closed does not apply on mobile */
      }

      /* On mobile the main area should be full width */
      .admin-main {
        margin-left: 0;
        width: 100%;
        padding: 22px 16px;
      }

      .admin-header {
        position: sticky;
        top: 0;
        background: var(--bg);
        z-index: 10;
        padding-bottom: 8px;
      }

      .sidebar-overlay {
        display: block; /* enable overlay on mobile */
      }
    }
  </style>

  @stack('head')
</head>
<body>
  <div class="admin-wrap">
    {{-- Sidebar --}}
    <aside id="adminSidebar" class="admin-sidebar" aria-hidden="false">
      <div>
        <div class="admin-brand">
          <div class="brand-logo">P</div>
          <div class="brand-meta">
            <div class="brand-name">Publ. Admin</div>
            <div class="brand-sub">{{ optional(Auth::guard('admin')->user())->role ?? 'Super Administrator' }}</div>
          </div>
        </div>

        <nav class="admin-nav" role="navigation" aria-label="Admin navigation">
          <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">🏠 Dashboard</a>
          <a href="{{ route('admin.reports.index') }}" class="{{ request()->routeIs('admin.reports.*') ? 'active' : '' }}">⚠️ Reports</a>
          {{-- Notifications removed per request --}}
          <a href="{{ route('admin.users.index') }}" class="{{ request()->routeIs('admin.users.*') ? 'active' : '' }}">👥 Manage Users</a>
          <a href="{{ route('admin.analytics') }}" class="{{ request()->routeIs('admin.analytics') ? 'active' : '' }}">📊 Analytics</a>
          <a href="{{ route('admin.settings') }}" class="{{ request()->routeIs('admin.settings') ? 'active' : '' }}">⚙️ Settings</a>
        </nav>
      </div>

      <div>
        <form action="{{ route('admin.logout') }}" method="POST">
          @csrf
          <button type="submit" class="btn btn-outline-light btn-block" style="font-weight:700;border-radius:10px;">
            Logout
          </button>
        </form>
      </div>
    </aside>

    {{-- Overlay --}}
    <div id="sidebarOverlay" class="sidebar-overlay" aria-hidden="true"></div>

    {{-- Main --}}
    <main id="adminMain" class="admin-main" role="main">
      <div class="admin-header">
        <button id="sidebarToggle" class="toggle-btn" aria-label="Toggle Sidebar" aria-controls="adminSidebar" aria-expanded="true">
          <svg width="22" height="22" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M3 6h18v2H3zM3 11h18v2H3zM3 16h18v2H3z"/></svg>
        </button>

        <div class="admin-title">
          <h1>@yield('title', 'Admin')</h1>
          @hasSection('subtitle')
            <div class="admin-meta">@yield('subtitle')</div>
          @endif
        </div>
      </div>

      @yield('content')
    </main>
  </div>

  {{-- JS --}}
  <script>
    document.addEventListener("DOMContentLoaded", () => {
      const sidebar = document.getElementById("adminSidebar");
      const overlay = document.getElementById("sidebarOverlay");
      const toggle = document.getElementById("sidebarToggle");
      const main = document.getElementById("adminMain");

      const DESKTOP_BREAK = 900;

      // Apply desktop collapsed state
      const applyDesktopCollapse = (collapsed) => {
        if (collapsed) {
          sidebar.classList.add('closed');
          // adjust main content classes
          main.classList.add('sidebar-collapsed');
          toggle.setAttribute('aria-expanded', 'false');
        } else {
          sidebar.classList.remove('closed');
          main.classList.remove('sidebar-collapsed');
          toggle.setAttribute('aria-expanded', 'true');
        }
      };

      // Open mobile overlay sidebar
      const openMobileSidebar = () => {
        sidebar.classList.add('open');
        overlay.classList.add('active');
        overlay.setAttribute('aria-hidden', 'false');
      };

      const closeMobileSidebar = () => {
        sidebar.classList.remove('open');
        overlay.classList.remove('active');
        overlay.setAttribute('aria-hidden', 'true');
      };

      // Toggle handler: behave differently on mobile and desktop
      toggle.addEventListener("click", () => {
        if (window.innerWidth <= DESKTOP_BREAK) {
          // mobile — slide sidebar over content and show overlay
          if (sidebar.classList.contains('open')) closeMobileSidebar();
          else openMobileSidebar();
        } else {
          // desktop — collapse/expand sidebar (no overlay)
          const isCollapsed = sidebar.classList.contains('closed');
          applyDesktopCollapse(!isCollapsed);
        }
      });

      // overlay click (mobile only) closes sidebar
      overlay.addEventListener("click", () => {
        if (window.innerWidth <= DESKTOP_BREAK) closeMobileSidebar();
      });

      document.addEventListener("keydown", (e) => {
        if (e.key === "Escape") {
          if (window.innerWidth <= DESKTOP_BREAK) closeMobileSidebar();
          else {
            // on desktop pressing ESC will expand sidebar if collapsed
            applyDesktopCollapse(false);
          }
        }
      });

      // Reset states on resize so overlay doesn't get stuck and margins stay correct
      const resetSidebarOnResize = () => {
        if (window.innerWidth > DESKTOP_BREAK) {
          // Desktop: ensure overlay is hidden, allow collapse feature
          overlay.classList.remove('active');
          overlay.setAttribute('aria-hidden','true');
          sidebar.classList.remove('open'); // remove mobile open
          // ensure .closed class remains whatever user toggled it to OR set default expanded:
          // (we'll default to expanded on larger screens)
          if (!sidebar.classList.contains('closed')) {
            // already expanded — ensure main has correct margin
            main.classList.remove('sidebar-collapsed');
          } else {
            // if closed then keep collapsed styling
            main.classList.add('sidebar-collapsed');
          }
        } else {
          // Mobile: ensure collapsed desktop state doesn't interfere
          sidebar.classList.remove('closed');
          main.classList.remove('sidebar-collapsed');
          // don't auto-open sidebar on mobile
          sidebar.classList.remove('open');
          overlay.classList.remove('active');
          overlay.setAttribute('aria-hidden','true');
        }
      };

      // initialize on load with default expanded desktop sidebar
      const init = () => {
        if (window.innerWidth > DESKTOP_BREAK) {
          // start expanded on desktop
          applyDesktopCollapse(false);
        } else {
          // mobile defaults
          sidebar.classList.remove('closed');
          sidebar.classList.remove('open');
          overlay.classList.remove('active');
          main.classList.remove('sidebar-collapsed');
        }
      };

      window.addEventListener('resize', resetSidebarOnResize);
      init();
      resetSidebarOnResize();
    });
  </script>

  @stack('scripts')
</body>
</html>
Write to Negi Alcait


