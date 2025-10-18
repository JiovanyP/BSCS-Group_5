<div class="sidebar" id="sidebar">
    <div>
        <h1 class="sidebar-logo">
            <a href="#" class="logo">Publ.</a>
        </h1>

        <h3>Be part of keeping our community safe. Publish your report with Publ.
            <br>
            <br>• Witness
            <br>• Publish
            <br>• Report
        </h3>

        <ul class="components">
            <li class="{{ request()->routeIs('timeline') ? 'active' : '' }}">
                <a href="{{ route('timeline') }}">
                    <i class="la la-home me-2"></i>
                    <span>Home</span>
                </a>
            </li>

            <li class="{{ request()->routeIs('accidents.create') ? 'active' : '' }}">
                <a href="{{ route('accidents.create') }}">
                    <i class="la la-exclamation-triangle me-2"></i>
                    <span>Notifications</span>

            <li class="{{ request()->routeIs('profile') ? 'active' : '' }}">
                <a href="{{ route('profile') }}">
                    <i class="la la-user me-2"></i>
                    <span>Profile</span>
                </a>
        </ul>
    </div>

    <form action="{{ route('logout') }}" method="POST" class="mt-4">
        @csrf
        <button type="submit" class="btn btn-danger w-100">
            <i class="la la-sign-out me-2"></i>
            Logout
        </button>
    </form>

</div>

<style>
:root {
    --primary: #494ca2;
    --accent: #CF0F47;
    --accent-hover: #FF0B55;
    --sidebar-bg: #ffffff;
    --white: #ffffff;
    --black: #000000;
    --text-muted: #666;
}

body {
    font-family: 'Poppins', Arial, sans-serif;
    font-size: 14px;
    line-height: 1.8;
    font-weight: normal;
    background: #fafafa;
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
    padding: 20px;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    border-right: 1px solid #eee;
    box-shadow: 0 0 20px rgba(0, 0, 0, 0.02);
}

.sidebar h1 {
    margin-bottom: 20px;
    font-weight: 700;
    font-size: 1.5rem;
}

.sidebar h3 {
    margin-bottom: 20px;
    font-weight: 300;
    font-size: 3px;
}

.sidebar .logo {
    color: var(--accent);
    text-decoration: none;
    transition: 0.3s all ease;
}

.sidebar .logo:hover {
    text-decoration: none;
    opacity: 0.9;
}

.sidebar ul.components {
    padding: 0;
    list-style: none;
}

.sidebar ul li {
    font-size: 16px;
}

.sidebar ul li a {
    padding: 10px 0;
    display: flex;
    align-items: center;
    color: var(--text-muted);
    border-bottom: 1px solid rgba(0, 0, 0, 0.05);
    text-decoration: none;
    transition: 0.3s all ease;
}

.sidebar ul li a:hover {
    color: var(--black);
}

.sidebar ul li a i {
    font-size: 18px;
    width: 24px;
}

.sidebar ul li.active > a {
    background: transparent;
    color: var(--accent);
    font-weight: 600;
}

.btn {
    transition: 0.3s all ease;
    padding: 12px 15px;
    border-radius: 40px;
    font-size: 14px;
    font-weight: 600;
    border: none;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
}

.btn:hover,
.btn:focus {
    text-decoration: none !important;
    outline: none !important;
    box-shadow: none !important;
}

.btn-danger {
    background: #dc3545;
    color: var(--white);
}

.btn-danger:hover {
    background: #c82333;
}

.w-100 {
    width: 100%;
}

.mt-4 {
    margin-top: 1.5rem;
}

.me-2 {
    margin-right: 0.5rem;
}

/* Responsive */
@media (max-width: 768px) {
    .sidebar {
        margin-left: -270px;
        position: fixed;
        z-index: 1000;
    }
    
    .sidebar.active {
        margin-left: 0;
    }
}
</style>