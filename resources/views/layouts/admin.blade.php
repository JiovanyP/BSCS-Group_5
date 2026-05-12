{{-- resources/views/layouts/admin.blade.php --}}
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>@yield('title','Admin') — Publ.</title>

  {{-- Fonts / Icons --}}
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet" />
  <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

  <style>
    :root {
        --primary: #494ca2;
        --accent: #CF0F47;
        --accent-hover: #FF0B55;
        --sidebar-bg: #ffffff;
        --white: #ffffff;
        --black: #000000;
        --text-muted: #666;
        --light-pink: #fbebf1;
        --sidebar-width: 270px;
    }

    body {
        font-family: 'Poppins', Arial, sans-serif;
        font-size: 14px;
        line-height: 1.8;
        background: #fafafa;
        margin: 0;
        display: flex;
    }

    /* Layout Wrapper */
    .admin-wrap {
        display: flex;
        width: 100%;
    }

    /* Sidebar Styling */
    .sidebar {
        min-width: var(--sidebar-width);
        max-width: var(--sidebar-width);
        background: var(--sidebar-bg);
        color: var(--text-muted);
        transition: all 0.3s;
        position: fixed;
        left: 0;
        top: 0;
        height: 100vh;
        display: flex;
        flex-direction: column;
        border-right: 1px solid #eee;
        box-shadow: 0 0 20px rgba(0, 0, 0, 0.02);
        background-image: linear-gradient(to bottom, #fff 40%, var(--light-pink) 100%);
        z-index: 1000;
    }

    .sidebar-content {
        flex: 1;
        overflow-y: auto;
        padding: 20px;
        display: flex;
        flex-direction: column;
        scrollbar-width: thin;
        scrollbar-color: transparent transparent;
    }

    .sidebar-content::-webkit-scrollbar { width: 6px; background: transparent; }
    .sidebar-content::-webkit-scrollbar-thumb { background-color: rgba(0,0,0,0.2); border-radius: 3px; }
    .sidebar:hover .sidebar-content::-webkit-scrollbar-thumb { background-color: rgba(0,0,0,0.4); }

    /* Main Content Area */
    .admin-main {
        flex: 1;
        margin-left: var(--sidebar-width);
        padding: 30px;
        min-height: 100vh;
        transition: all 0.3s;
    }

    /* Logo & Nav */
    .logo-container { display: flex; align-items: center; justify-content: space-between; margin-bottom: 20px; }
    .logo-container h1 { margin: 0; font-weight: 700; font-size: 1.5rem; }
    .logo { color: var(--accent); text-decoration: none; }
    
    .components { list-style: none; padding: 0; margin: 0; }
    .components li a, .components li button {
        padding: 10px 0; display: flex; align-items: center; gap: 10px;
        color: var(--text-muted); border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        text-decoration: none; transition: 0.3s; background: transparent;
        border: none; width: 100%; text-align: left; cursor: pointer; font: inherit;
    }
    .components li a:hover, .components li.active > a { color: var(--accent); font-weight: 600; }

    /* Profile Expandable */
    .profile-section {
        overflow: hidden; max-height: 0; opacity: 0;
        transition: all 0.5s ease; margin-left: 15px; padding-left: 20px;
    }
    .profile-item:hover .profile-section { max-height: 220px; opacity: 1; margin-top: 5px; }
    .profile-section a { display: flex; align-items: center; gap: 6px; padding: 4px 0; color: var(--text-muted); text-decoration: none; font-size: 14px; }
    .profile-section a:hover { color: var(--accent); }

    /* Weather Widget */
    .weather-widget {
        margin-top: 20px; padding: 15px; background: #f9f9ff;
        border: 1px solid #eee; border-radius: 15px; text-align: center;
        box-shadow: 0 4px 10px rgba(0,0,0,0.05);
    }
    .weather-widget h3 { font-size: 14px; font-weight: 600; color: var(--primary); margin-bottom: 8px; }

    /* Buttons */
    .btn-logout {
        background: var(--accent); color: white; border: none;
        padding: 12px; border-radius: 40px; font-weight: 600;
        width: 100%; margin-top: 20px; cursor: pointer;
        display: flex; align-items: center; justify-content: center; gap: 8px;
    }

    /* Responsive */
    @media (max-width: 900px) {
        .sidebar { margin-left: -270px; }
        .sidebar.active { margin-left: 0; }
        .admin-main { margin-left: 0; }
        .sidebar-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 900; }
        .sidebar-overlay.active { display: block; }
    }
  </style>
  @stack('head')
</head>
<body>
  <div class="admin-wrap">
    {{-- Sidebar --}}
    <aside id="sidebar" class="sidebar">
      <div class="sidebar-content">
        <div class="logo-container">
          <h1><a href="/" class="logo">Publ.</a></h1>
          <a href="{{ route('admin.users.index') }}" class="search-btn" title="Manage Users">
            <span class="material-icons">search</span>
          </a>
        </div>

        <ul class="components">
          <li class="{{ request()->routeIs('admin.posts.*') ? 'active' : '' }}">
            <a href="{{ route('admin.posts.create') }}">
              <span class="material-icons">add_circle</span>
              <span>Create Announcement</span>
            </a>
          </li>
          <li class="{{ request()->routeIs('admin.reports.*') ? 'active' : '' }}">
            <a href="{{ route('admin.reports.index') }}">
              <span class="material-icons">warning</span>
              <span>Reports</span>
            </a>
          </li>
          <li class="{{ request()->routeIs('admin.services.*') ? 'active' : '' }}">
            <a href="{{ route('admin.services.index') }}">
                <span class="material-icons">business_center</span>
                <span>Services</span>
            </a>
        </li>
          <li class="{{ request()->routeIs('admin.analytics') ? 'active' : '' }}">
            <a href="{{ route('admin.analytics') }}">
              <span class="material-icons">analytics</span>
              <span>Analytics</span>
            </a>
          </li>

          {{-- Profile Expandable --}}
          <li class="profile-item">
            <button type="button">
              @php
                $user = Auth::guard('admin')->user();
                $avatar = $user->avatar_url ?? 'https://ui-avatars.com/api/?name=Admin';
              @endphp
              <img src="{{ $avatar }}" style="width:22px; height:22px; border-radius:50%; object-fit:cover;">
              <span>{{ $user->name ?? 'Admin Profile' }}</span>
            </button>
            <div class="profile-section">
              <a href="{{ route('admin.settings') }}"><span class="material-icons">settings</span> Settings</a>
              <a href="{{ route('admin.chat.index') }}"><span class="material-icons">smart_toy</span> AI Assistant</a>
            </div>
          </li>
        </ul>

        <div class="weather-widget">
          <h3>🌤 Kabacan Weather</h3>
          <div id="weather-info"><p>Loading...</p></div>
        </div>

        <form action="{{ route('admin.logout') }}" method="POST">
          @csrf
          <button type="submit" class="btn-logout">
            <span class="material-symbols-outlined">logout</span> Logout
          </button>
        </form>
      </div>
    </aside>

    <div id="sidebarOverlay" class="sidebar-overlay"></div>

    {{-- Main Content --}}
    <main class="admin-main">
        <header style="margin-bottom: 2rem;">
            <h2 style="font-weight: 700; color: var(--black);">@yield('title')</h2>
            @hasSection('subtitle')
                <p class="text-muted">@yield('subtitle')</p>
            @endif
        </header>

        @yield('content')
    </main>
  </div>

  <script>
    // Weather Logic
    async function fetchWeather() {
        try {
            const res = await fetch("https://api.open-meteo.com/v1/forecast?latitude=7.1067&longitude=124.8294&current_weather=true");
            const data = await res.json();
            const weather = data.current_weather;
            const container = document.getElementById("weather-info");
            if (weather) {
                let icon = "☁️";
                if (weather.weathercode === 0) icon = "☀️";
                else if ([1,2].includes(weather.weathercode)) icon = "⛅";
                else if ([51,61,80].includes(weather.weathercode)) icon = "🌧️";
                container.innerHTML = `<p style="font-size: 24px; margin:0;">${icon}</p>
                                       <p style="margin:0;">🌡️ ${weather.temperature}°C</p>`;
            }
        } catch (err) { console.error("Weather load failed"); }
    }
    fetchWeather();
  </script>
  @stack('scripts')
</body>
</html>