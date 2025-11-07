<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'MyApp')</title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Line Awesome (icons) -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/line-awesome/1.3.0/line-awesome/css/line-awesome.min.css" rel="stylesheet">

    <!-- Material Icons -->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: #f8f9fa;
        }

        .sidebar {
            position: sticky;
            top: 0;
            height: 100vh;
            width: 250px;
            background-color: #ffffff;
            border-right: 1px solid #dee2e6;
            padding: 20px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .sidebar h4 {
            font-size: 1.3rem;
            font-weight: bold;
            margin-bottom: 1rem;
        }

        .sidebar-menu a {
            display: block;
            padding: 10px 0;
            color: #333;
            text-decoration: none;
            transition: 0.2s;
        }

        .sidebar-menu a.active,
        .sidebar-menu a:hover {
            color: #0d6efd;
            font-weight: 600;
        }

        .main-content {
            flex-grow: 1;
            background-color: #fff;
            padding: 2rem;
            min-height: 100vh;
        }

        footer {
            border-top: 1px solid #dee2e6;
            padding-top: 10px;
            text-align: center;
            font-size: 0.9rem;
            color: #666;
            background-color: #fff;
        }

        /* Responsive sidebar */
        @media (max-width: 768px) {
            .sidebar {
                position: fixed;
                left: 0;
                top: 0;
                height: 100vh;
                z-index: 1000;
                width: 270px;
                transform: translateX(-100%);
                transition: transform 0.3s ease;
            }
            .sidebar.active {
                transform: translateX(0);
            }
            .main-content {
                position: relative;
            }
        }

        #sidebar-toggle {
            background: none;
            border: none;
            font-size: 24px;
            cursor: pointer;
            color: #ff0b55;
            transition: left 0.3s ease; /* added for slide */
        }

        @media (max-width: 768px) {
            #sidebar-toggle {
                position: fixed;
                top: 10px;
                left: 10px;
                z-index: 10101010;
                font-size: 32px;
                padding: 10px;
                background: rgba(255, 255, 255, 1);
                border-radius: 30;
                box-shadow: 0 5px 5px rgba(0,0,0,0.2);
            }
            /* toggle moves when sidebar is active */
            #sidebar-toggle.active {
                left: 228px;
                box-shadow: 0;
            }
        }
    </style>

    @stack('styles')
</head>

<body>
    <div class="d-flex">
        @include( 'partials.sidebar')

        <div class="main-content flex-grow-1">
            <button id="sidebar-toggle" class="d-md-none d-block mb-3">
                <span class="material-icons">menu</span>
            </button>
            @yield('content')
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const sidebar = document.getElementById('sidebar');
        const toggle = document.getElementById('sidebar-toggle');
        const mainContent = document.querySelector('.main-content');

        // Toggle sidebar and slide toggle
        toggle.addEventListener('click', (e) => {
            e.stopPropagation(); // prevent main content click
            sidebar.classList.toggle('active');
            toggle.classList.toggle('active'); // slide toggle
        });

        // Collapse sidebar and toggle when clicking main content
        mainContent.addEventListener('click', () => {
            if (sidebar.classList.contains('active')) {
                sidebar.classList.remove('active');
                toggle.classList.remove('active'); // slide toggle back
            }
        });
    </script>
    @stack('scripts')
</body>
</html>
