<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'MyApp')</title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Line Awesome (icons) -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/line-awesome/1.3.0/line-awesome/css/line-awesome.min.css" rel="stylesheet">

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
    </style>
</head>

<body>
    <div class="d-flex">
        @include('partials.sidebar')

        <div class="main-content flex-grow-1">
            @yield('content')

            <footer class="mt-5">
                © 2025 All rights reserved | Publ.
            </footer>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
