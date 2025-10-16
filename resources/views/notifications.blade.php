<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notifications - PubL</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@100;200;300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            color: #fff;
            line-height: 1.6;
            background: #000;
            min-height: 100vh;
        }

        .header {
            min-height: 40vh;
            width: 100%;
            background-image: linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.8)), url('https://www.usm.edu.ph/wp-content/uploads/2019/01/USM_Administration_Building-1024x682.png');
            background-position: center;
            background-size: cover;
            position: relative;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            padding: 100px 20px 60px;
        }

        nav {
            display: flex;
            padding: 1.5% 4%;
            justify-content: space-between;
            align-items: center;
            width: 100%;
            position: fixed;
            top: 0;
            left: 0;
            background: rgba(0,0,0,0.95);
            z-index: 1000;
            border-bottom: 2px solid #FF0B55;
        }

        nav .logo {
            color: #FF0B55;
            font-size: 32px;
            font-weight: 700;
            text-decoration: none;
        }

        .nav-links {
            flex: 1;
            text-align: right;
        }

        .nav-links ul li {
            list-style: none;
            display: inline-block;
            padding: 8px 16px;
            position: relative;
        }

        .nav-links ul li a {
            color: #fff;
            text-decoration: none;
            font-size: 16px;
            font-weight: 500;
            transition: 0.3s;
        }

        .nav-links ul li a:hover {
            color: #FF0B55;
        }

        .nav-links ul li::after {
            content: '';
            width: 0%;
            height: 2px;
            background: #FF0B55;
            display: block;
            margin: auto;
            transition: 0.5s;
        }

        .nav-links ul li:hover::after {
            width: 100%;
        }

        .page-title {
            font-size: 48px;
            font-weight: 600;
            margin-bottom: 15px;
            color: #fff;
        }

        .page-subtitle {
            font-size: 18px;
            font-weight: 300;
            opacity: 0.9;
            max-width: 600px;
            margin: 0 auto;
            color: #fff;
        }

        .notifications-container {
            max-width: 1200px;
            margin: 40px auto;
            padding: 0 20px;
        }

        .section-title {
            font-size: 32px;
            font-weight: 600;
            margin: 60px 0 30px;
            position: relative;
            padding-bottom: 15px;
            text-align: center;
        }

        .section-title::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 4px;
            background: #FF0B55;
        }

        .priority-title {
            color: #FF0B55;
        }

        .general-title {
            color: #fff;
        }

        .section-description {
            color: #ccc;
            font-size: 16px;
            text-align: center;
            margin-bottom: 40px;
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
        }

        .notification-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(380px, 1fr));
            gap: 30px;
            margin-top: 30px;
        }

        .notification-card {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 12px;
            padding: 30px;
            transition: all 0.3s ease;
            border: 1px solid rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
        }

        .priority-card {
            border-top: 4px solid #FF0B55;
            background: rgba(255, 11, 85, 0.05);
        }

        .general-card {
            border-top: 4px solid #666;
            background: rgba(255, 255, 255, 0.03);
        }

        .notification-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 15px 40px rgba(255, 11, 85, 0.2);
            border-color: rgba(255, 11, 85, 0.3);
        }

        .notification-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 20px;
        }

        .notification-title {
            font-size: 22px;
            font-weight: 600;
            color: #fff;
            margin-bottom: 10px;
        }

        .notification-type {
            padding: 6px 16px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
        }

        .priority-badge {
            background: #FF0B55;
            color: white;
        }

        .general-badge {
            background: #666;
            color: white;
        }

        .notification-location {
            color: #FF0B55;
            font-size: 14px;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 10px;
            font-weight: 500;
        }

        .notification-location i {
            color: #FF0B55;
        }

        .notification-description {
            color: #ccc;
            font-size: 14px;
            line-height: 1.7;
            margin-bottom: 20px;
        }

        .notification-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-top: 20px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            font-size: 13px;
            color: #888;
        }

        .notification-time {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .notification-distance {
            padding: 6px 14px;
            border-radius: 15px;
            font-weight: 600;
            font-size: 12px;
        }

        .priority-distance {
            color: #FF0B55;
            background: rgba(255, 11, 85, 0.2);
            border: 1px solid rgba(255, 11, 85, 0.3);
        }

        .general-distance {
            color: #ccc;
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .empty-state {
            text-align: center;
            padding: 80px 20px;
            color: #666;
        }

        .empty-state i {
            font-size: 80px;
            color: #333;
            margin-bottom: 25px;
        }

        .empty-state h3 {
            font-size: 28px;
            margin-bottom: 15px;
            color: #666;
        }

        .empty-state p {
            font-size: 16px;
            color: #555;
        }

        footer {
            background-color: #000;
            color: white;
            padding: 60px 50px 30px;
            margin-top: 80px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }

        .footer-container {
            max-width: 1200px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 50px;
            align-items: start;
        }

        footer h2 {
            margin: 0 0 20px 0;
            color: #FF0B55;
            font-size: 24px;
            font-weight: 600;
        }

        footer p {
            margin: 0 0 20px 0;
            line-height: 1.8;
            opacity: 0.9;
            font-size: 16px;
        }

        .footer-bottom {
            text-align: center;
            margin-top: 60px;
            padding-top: 30px;
            border-top: 1px solid rgba(255,255,255,0.1);
        }

        .footer-bottom p {
            margin: 0;
            opacity: 0.7;
            font-size: 14px;
        }

        /* Responsive design */
        @media(max-width: 900px) {
            .notification-cards {
                grid-template-columns: 1fr;
            }

            .page-title {
                font-size: 36px;
            }

            .footer-container {
                grid-template-columns: 1fr;
                gap: 40px;
            }
        }

        @media(max-width: 700px) {
            .nav-links {
                position: fixed;
                background: #000;
                height: 100vh;
                width: 200px;
                top: 0;
                right: -200px;
                text-align: left;
                z-index: 2;
                transition: 0.5s;
                padding-top: 60px;
            }

            .nav-links ul li {
                display: block;
                margin: 15px 0;
            }

            nav .fa {
                display: block;
                color: #fff;
                margin: 10px;
                font-size: 24px;
                cursor: pointer;
            }

            .nav-links ul {
                padding: 30px;
            }

            .page-title {
                font-size: 32px;
            }
        }

        /* Menu icon styling */
        nav .fa {
            display: none;
        }

        @media(max-width: 700px) {
            nav .fa {
                display: block;
            }
        }
    </style>
</head>

<body>
    <!-- Navigation -->
    <nav>
        <h1 class="logo">PubL</h1>
        <div class="nav-links" id="navLinks">
            <i class="fa fa-times" onclick="hideMenu()"></i>
            <ul>
                @auth
                    <li><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li><a href="{{ route('notifications') }}" class="active">Notifications</a></li>
                    <li>
                        <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                            @csrf
                            <button type="submit" style="background: none; border: none; color: #fff; cursor: pointer; font-size: 16px; font-weight: 500; font-family: 'Poppins', sans-serif; padding: 8px 16px;">
                                Log Out
                            </button>
                        </form>
                    </li>
                @else
                    <li><a href="{{ route('login') }}">Log In</a></li>
                    <li><a href="{{ route('register') }}">Register</a></li>
                @endauth
            </ul>
        </div>
        <i class="fa fa-bars" onclick="showMenu()"></i>
    </nav>

    <!-- Header -->
    <section class="header">
        <div class="text-box">
            <h1 class="page-title">Notifications</h1>
            <p class="page-subtitle">Stay updated with incident reports in your area and beyond through community collaboration</p>
        </div>
    </section>

    <!-- Notifications Content -->
    <div class="notifications-container">
        <!-- Priority Notifications -->
        <section>
            <h2 class="section-title priority-title">
                <i class="fas fa-exclamation-circle"></i> Priority Reports
            </h2>
            <p class="section-description">Incidents reported within your immediate area (less than 5km away) - Requires immediate attention</p>

            <div class="notification-cards">
                <!-- Priority Notification 1 -->
                <div class="notification-card priority-card">
                    <div class="notification-header">
                        <div>
                            <h3 class="notification-title">Road Accident</h3>
                            <span class="notification-type priority-badge">High Priority</span>
                        </div>
                    </div>
                    <div class="notification-location">
                        <i class="fas fa-map-marker-alt"></i>
                        Main Street, Downtown Area
                    </div>
                    <p class="notification-description">
                        Two-car collision near the central intersection. Emergency services have been notified. Avoid the area if possible. Multiple witnesses confirmed.
                    </p>
                    <div class="notification-meta">
                        <div class="notification-time">
                            <i class="far fa-clock"></i> 15 minutes ago
                        </div>
                        <div class="notification-distance priority-distance">2.3 km away</div>
                    </div>
                </div>

                <!-- Priority Notification 2 -->
                <div class="notification-card priority-card">
                    <div class="notification-header">
                        <div>
                            <h3 class="notification-title">Fire Emergency</h3>
                            <span class="notification-type priority-badge">Critical</span>
                        </div>
                    </div>
                    <div class="notification-location">
                        <i class="fas fa-map-marker-alt"></i>
                        Residential Area, North District
                    </div>
                    <p class="notification-description">
                        Building fire reported with visible flames. Fire department is on scene. Please avoid the area and keep roads clear for emergency vehicles.
                    </p>
                    <div class="notification-meta">
                        <div class="notification-time">
                            <i class="far fa-clock"></i> 45 minutes ago
                        </div>
                        <div class="notification-distance priority-distance">1.8 km away</div>
                    </div>
                </div>

                <!-- Priority Notification 3 -->
                <div class="notification-card priority-card">
                    <div class="notification-header">
                        <div>
                            <h3 class="notification-title">Power Outage</h3>
                            <span class="notification-type priority-badge">Medium Priority</span>
                        </div>
                    </div>
                    <div class="notification-location">
                        <i class="fas fa-map-marker-alt"></i>
                        Commercial District
                    </div>
                    <p class="notification-description">
                        Widespread power outage affecting multiple blocks and businesses. Utility company has been notified and is investigating the cause.
                    </p>
                    <div class="notification-meta">
                        <div class="notification-time">
                            <i class="far fa-clock"></i> 1 hour ago
                        </div>
                        <div class="notification-distance priority-distance">3.1 km away</div>
                    </div>
                </div>
            </div>
        </section>

        <!-- General Notifications -->
        <section>
            <h2 class="section-title general-title">
                <i class="fas fa-globe-americas"></i> General Reports
            </h2>
            <p class="section-description">Incidents reported further from your location - Good to be aware of</p>

            <div class="notification-cards">
                <!-- General Notification 1 -->
                <div class="notification-card general-card">
                    <div class="notification-header">
                        <div>
                            <h3 class="notification-title">Water Main Break</h3>
                            <span class="notification-type general-badge">General</span>
                        </div>
                    </div>
                    <div class="notification-location">
                        <i class="fas fa-map-marker-alt"></i>
                        South Industrial Zone
                    </div>
                    <p class="notification-description">
                        Major water main break causing road closures and water service interruptions in the southern district. Repair crews are on site.
                    </p>
                    <div class="notification-meta">
                        <div class="notification-time">
                            <i class="far fa-clock"></i> 2 hours ago
                        </div>
                        <div class="notification-distance general-distance">8.7 km away</div>
                    </div>
                </div>

                <!-- General Notification 2 -->
                <div class="notification-card general-card">
                    <div class="notification-header">
                        <div>
                            <h3 class="notification-title">Public Demonstration</h3>
                            <span class="notification-type general-badge">General</span>
                        </div>
                    </div>
                    <div class="notification-location">
                        <i class="fas fa-map-marker-alt"></i>
                        City Hall Plaza
                    </div>
                    <p class="notification-description">
                        Peaceful demonstration taking place with approximately 200 participants. Expect traffic delays in the government district area.
                    </p>
                    <div class="notification-meta">
                        <div class="notification-time">
                            <i class="far fa-clock"></i> 3 hours ago
                        </div>
                        <div class="notification-distance general-distance">12.2 km away</div>
                    </div>
                </div>

                <!-- General Notification 3 -->
                <div class="notification-card general-card">
                    <div class="notification-header">
                        <div>
                            <h3 class="notification-title">Bridge Maintenance</h3>
                            <span class="notification-type general-badge">Scheduled</span>
                        </div>
                    </div>
                    <div class="notification-location">
                        <i class="fas fa-map-marker-alt"></i>
                        East River Bridge
                    </div>
                    <p class="notification-description">
                        Scheduled maintenance causing lane closures on the eastern bridge. Expected to continue through the week with alternating lane closures.
                    </p>
                    <div class="notification-meta">
                        <div class="notification-time">
                            <i class="far fa-clock"></i> 5 hours ago
                        </div>
                        <div class="notification-distance general-distance">15.5 km away</div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <!-----Footer---->
    <footer>
        <div class="footer-container">
            <!-- Mission -->
            <div>
                <h2>Our Mission</h2>
                <p>
                    At PubL, our mission is to create safer communities by enabling transparent incident reporting and verification.
                    We believe in the power of collective awareness and responsible authority engagement to address community concerns effectively.
                </p>
            </div>

            <!-- Vision -->
            <div>
                <h2>Our Vision</h2>
                <p>
                    We envision a world where community members and authorities collaborate seamlessly to create safer environments,
                    leveraging technology to verify and respond to incidents quickly and effectively, fostering trust and transparency.
                </p>
            </div>
        </div>

        <div class="footer-bottom">
            <p>&copy; 2025 PubL. All rights reserved.</p>
        </div>
    </footer>

    <!--------Javascript for toggle Menu--------->
    <script>
        var navLinks = document.getElementById("navLinks");

        function showMenu(){
            navLinks.style.right = "0";
        }
        function hideMenu(){
            navLinks.style.right = "-200px";
        }
    </script>
</body>
</html>
