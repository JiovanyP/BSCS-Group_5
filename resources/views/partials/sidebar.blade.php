<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width,initial-scale=1" />
<title>Publ Sidebar with Weather</title>

<!-- Material icons/fonts -->
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet" />
<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">

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
}

body {
    font-family: 'Poppins', Arial, sans-serif;
    font-size: 14px;
    line-height: 1.8;
    background: #fafafa;
    margin: 0;
}

.sidebar {
    min-width: 270px;
    max-width: 270px;
    background: var(--sidebar-bg);
    color: var(--text-muted);
    transition: all 0.3s;
    position: sticky;
    top: 0;
    height: 100vh;
    display: flex;
    flex-direction: column;
    border-right: 1px solid #eee;
    box-shadow: 0 0 20px rgba(0, 0, 0, 0.02);
    background-image: linear-gradient(to bottom, #fff 40%, var(--light-pink) 100%);
}

/* Make only the content scrollable */
.sidebar-content {
    flex: 1;
    overflow-y: auto;
    padding: 20px;
    display: flex;
    flex-direction: column;
}

/* Sidebar heading/logo */
.sidebar h1 {
    margin-bottom: 20px;
    font-weight: 700;
    font-size: 1.5rem;
}
.logo {
    color: var(--accent);
    text-decoration: none;
    transition: 0.3s;
}
.logo:hover {
    opacity: 0.9;
}

/* Sidebar links */
.components {
    list-style: none;
    padding: 0;
    margin: 0;
}
.components li {
    font-size: 16px;
}
.components li a,
.components li button {
    padding: 10px 0;
    display: flex;
    align-items: center;
    gap: 10px;
    color: var(--text-muted);
    border-bottom: 1px solid rgba(0, 0, 0, 0.05);
    text-decoration: none;
    transition: 0.3s all ease;
    background: transparent;
    border: none;
    width: 100%;
    text-align: left;
    cursor: pointer;
    font: inherit;
}
.components li a:hover,
.components li button:hover {
    color: var(--black);
}
.components li.active > a,
.components li.active > button {
    color: var(--accent);
    font-weight: 600;
}

/* Material icons */
.material-icons,
.material-symbols-outlined {
    font-size: 20px !important;
    flex-shrink: 0;
    line-height: 1 !important;
    vertical-align: middle !important;
}
.sidebar li a span:last-child,
.sidebar li button span:last-child {
    font-size: 15px !important;
    font-weight: 500 !important;
    line-height: 1 !important;
}

/* Hide sidebar scrollbar by default */
.sidebar-content {
    flex: 1;
    overflow-y: auto;
    padding: 20px;
    display: flex;
    flex-direction: column;

    /* Hide scrollbar for Firefox */
    scrollbar-width: thin;
    scrollbar-color: transparent transparent;
}

/* Hide scrollbar for Webkit (Chrome, Edge, Safari) */
.sidebar-content::-webkit-scrollbar {
    width: 6px;
    background: transparent; /* Hide by default */
}

/* Scrollbar thumb */
.sidebar-content::-webkit-scrollbar-thumb {
    background-color: rgba(0,0,0,0.2);
    border-radius: 3px;
}

/* Show scrollbar on hover */
.sidebar:hover .sidebar-content::-webkit-scrollbar-thumb {
    background-color: rgba(0,0,0,0.4);
}
.sidebar:hover .sidebar-content {
    scrollbar-color: rgba(0,0,0,0.4) transparent;
}

/* Profile expandable */
.profile-section {
    overflow: hidden;
    max-height: 0;
    opacity: 0;
    transition: all 0.5s ease;
    margin-left: 15px;
    padding-left: 20px;
    position: relative;
    z-index: 100;
    background: var(--sidebar-bg);
}
.profile-section a,
.profile-section button {
    display: flex;
    align-items: center;
    gap: 6px !important;
    font-size: 10px !important;
    padding: 4px 0 !important;
    color: var(--text-muted);
    border: none;
    text-decoration: none;
    transition: color 0.2s ease;
    background: transparent;
    cursor: pointer;
    font: inherit;
}
.profile-section a:hover,
.profile-section button:hover {
    color: var(--accent);
}
.profile-item:hover .profile-section,
.profile-section:hover {
    max-height: 220px;
    opacity: 1;
    margin-top: 5px;
}

/* Weather Widget */
.weather-widget {
    margin-top: 20px;
    padding: 15px;
    background: #f9f9ff;
    border: 1px solid #eee;
    border-radius: 15px;
    text-align: center;
    box-shadow: 0 4px 10px rgba(0,0,0,0.05);
    font-size: 14px;
}
.weather-widget h3 {
    font-size: 16px;
    font-weight: 600;
    margin-bottom: 8px;
    color: var(--primary);
}
.weather-widget p {
    margin: 4px 0;
    font-size: 13px;
}

/* Buttons */
.btn {
    transition: 0.3s;
    padding: 12px 15px;
    border-radius: 40px;
    font-size: 14px;
    font-weight: 600;
    border: none;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
}
.btn-danger {
    background: var(--accent);
    color: var(--white);
}
.btn-danger:hover {
    background: var(--accent-hover);
}
.mt-4 { margin-top: 1.5rem; }

/* Responsive sidebar for mobile */
@media (max-width: 768px) {
    .sidebar {
        margin-left: -270px;
        position: fixed;
        z-index: 1000;
    }
    .sidebar.active { margin-left: 0; }
}

/* Fallback modal helper (unchanged) */
.fallback-modal-backdrop {
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,0.5);
    z-index: 1050;
}
.fallback-modal {
    position: fixed;
    left: 50%;
    top: 50%;
    transform: translate(-50%,-50%);
    z-index: 1060;
    background: white;
    border-radius: 8px;
    max-width: 750px;
    width: 90%;
    max-height: 90vh;
    overflow: auto;
    box-shadow: 0 8px 30px rgba(0,0,0,0.2);
    padding: 16px;
}

/* Align logo and search icon neatly */
.logo-container {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 20px;
}

.search-btn {
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 20px;
    background: transparent;
    size: 30px !important;
    border: none;
    cursor: pointer;
    color: var(--text-muted);
    padding: 0px !important;
    border-radius: 50%;
    text-decoration: none;
    transition: all 0.2s ease;
}

.search-btn:hover {
    background: rgba(0, 0, 0, 0.05);
    color: var(--accent);
}

</style>
</head>
<body>

<div class="sidebar" id="sidebar">
    <div class="sidebar-content">
        <div class="logo-container">
            <h1><a href="#" class="logo">Publ.</a></h1>

            <!-- Search Icon Button -->
            <a href="{{ route('userExplore') }}" class="search-btn" title="Explore Users">
                <span class="material-icons">search</span>
            </a>
        </div>

        <ul class="components">
            <li class="{{ request()->routeIs('timeline') ? 'active' : '' }}">
                <a href="{{ route('timeline') }}">
                    <span class="material-icons">home</span>
                    <span>Home</span>
                </a>
            </li>

            <li class="{{ request()->routeIs('posts.create') ? 'active' : '' }}">
                <a href="{{ route('posts.create') }}">
                    <span class="material-icons">add_circle</span>
                    <span>Create Report</span>
                </a>
            </li>

            <li class="{{ request()->is('notifications*') ? 'active' : '' }}">
                <a href="{{ route('notifications') }}">
                    <span class="material-icons">notifications</span>
                    <span>Notifications</span>
                </a>
            </li>

            <!-- Profile Expandable -->
            <li class="profile-item {{ request()->routeIs('profile') ? 'active' : '' }}">
                <button type="button" aria-expanded="false" aria-controls="profile-section" onclick="/* noop */">
                    <!-- Replace icon with avatar -->
                    <img src="{{ auth()->user()->avatar_url ?? asset('images/avatar.png') }}" 
                        alt="Avatar" 
                        style="width:22px; height:22px; border-radius:50%; flex-shrink:0;">
                    <span>Profile</span>
                </button>

                <div class="profile-section" id="profile-section" aria-hidden="true">
                    <a href="{{ route('profile') }}">
                        <span class="material-icons">visibility</span>
                        View Profile
                    </a>
                    <a href="{{ route('profile.modal') }}">
                        <span class="material-icons">edit</span>
                        Edit Info
                    </a>
                    <a href="{{ route('profile.modal') }}">
                        <span class="material-icons">settings</span>
                        Settings
                    </a>
                </div>
            </li>
        </ul>

        <div class="weather-widget">
            <h3>🌤 Kabacan Weather</h3>
            <div id="weather-info">
                <p>Loading...</p>
            </div>
        </div>
    </div>

    <form action="{{ route('logout') }}" method="POST" class="mt-4">
        @csrf
        <button type="submit" class="btn btn-danger w-100">
            <span class="material-symbols-outlined">logout</span>
            Logout
        </button>
    </form>
</div>

<script>
/*
  loadEditModal()
  - non-jQuery fetch for /profile/modal
  - inserts returned HTML into body if not present
  - tries to use bootstrap.Modal if available (Bootstrap 5)
  - fallback: toggles a simple modal display/show classes for the inserted element
*/
// async function loadEditModal() {
//     try {
//         // if modal already exists, try to show it using bootstrap or fallback
//         const existing = document.getElementById('editProfileModal');
//         if (existing) {
//             showModalElement(existing);
//             return;
//         }

//         const res = await fetch('/profile/modal', { credentials: 'same-origin' });
//         if (!res.ok) throw new Error('Failed to fetch modal HTML');

//         const html = await res.text();
//         // append HTML to body
//         const container = document.createElement('div');
//         container.innerHTML = html;
//         document.body.appendChild(container);

//         const modalEl = document.getElementById('editProfileModal') || container.querySelector('.modal') || container.firstElementChild;
//         if (!modalEl) {
//             // If the returned HTML doesn't contain an element with id 'editProfileModal',
//             // wrap returned content inside a fallback modal container.
//             const fallbackBackdrop = document.createElement('div');
//             fallbackBackdrop.className = 'fallback-modal-backdrop';
//             fallbackBackdrop.addEventListener('click', () => {
//                 fallbackBackdrop.remove();
//                 fallbackModal.remove();
//             });

//             const fallbackModal = document.createElement('div');
//             fallbackModal.className = 'fallback-modal';
//             fallbackModal.innerHTML = html;

//             document.body.appendChild(fallbackBackdrop);
//             document.body.appendChild(fallbackModal);
//             return;
//         }

//         // Try to show via Bootstrap's JS if available (Bootstrap 5)
//         showModalElement(modalEl);

//     } catch (err) {
//         console.error('loadEditModal error:', err);
//         alert('Could not load profile editor. Try again or check your network.');
//     }
// }

// function showModalElement(modalEl) {
//     // If bootstrap modal is available (Bootstrap 5), use it
//     try {
//         if (window.bootstrap && typeof window.bootstrap.Modal === 'function') {
//             // If modal instance exists, reuse it
//             let instance = window.bootstrap.Modal.getInstance(modalEl);
//             if (!instance) instance = new window.bootstrap.Modal(modalEl, {});
//             instance.show();
//             return;
//         }
//     } catch (e) {
//         console.warn('bootstrap.Modal failed, falling back:', e);
//     }

//     // Fallback for environments without bootstrap JS:
//     // - Add a backdrop if none
//     // - Add .show and inline styles to simulate modal open
//     const backdrop = document.createElement('div');
//     backdrop.className = 'fallback-modal-backdrop';
//     backdrop.addEventListener('click', () => {
//         backdrop.remove();
//         modalEl.classList.remove('show');
//         modalEl.style.display = 'none';
//     });

//     document.body.appendChild(backdrop);

//     // Make sure modalEl is visible and on top
//     modalEl.style.display = 'block';
//     modalEl.style.position = 'fixed';
//     modalEl.style.zIndex = 1060;
//     modalEl.style.left = '50%';
//     modalEl.style.top = '50%';
//     modalEl.style.transform = 'translate(-50%, -50%)';
//     modalEl.classList.add('show');
// }

/* Weather fetch (unchanged logic) */
async function fetchWeather() {
    try {
        const res = await fetch("https://api.open-meteo.com/v1/forecast?latitude=7.1067&longitude=124.8294&current_weather=true");
        const data = await res.json();
        const weather = data.current_weather;
        const container = document.getElementById("weather-info");

        if (weather) {
            const temp = weather.temperature;
            const wind = weather.windspeed;
            const code = weather.weathercode;
            let icon = "☁️";
            if (code === 0) icon = "☀️";
            else if ([1,2].includes(code)) icon = "⛅";
            else if ([3,45,48].includes(code)) icon = "☁️";
            else if ([51,61,80].includes(code)) icon = "🌧️";
            else if ([71,85].includes(code)) icon = "❄️";
            else if ([95,96,99].includes(code)) icon = "⛈️";

            container.innerHTML = `
                <p style="font-size: 32px;">${icon}</p>
                <p>🌡️ ${temp}°C</p>
                <p>💨 ${wind} km/h</p>
            `;
        } else {
            container.innerHTML = "<p>Weather data unavailable.</p>";
        }
    } catch (err) {
        document.getElementById("weather-info").innerHTML = "<p>Failed to load weather.</p>";
    }
}
fetchWeather();
setInterval(fetchWeather, 600000);
</script>
</body>

</html>