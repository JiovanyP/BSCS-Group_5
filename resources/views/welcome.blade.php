@vite('resources/js/loadingBar.js')

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Publ.</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;700&family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        :root {
            /* --- MD3 Color Tokens (Generated based on Seed #FF0B55) --- */
            --md-sys-color-primary: #B90044;
            --md-sys-color-on-primary: #FFFFFF;
            --md-sys-color-primary-container: #FFD9DF; /* This is the pink highlighter color */
            --md-sys-color-on-primary-container: #3F0012;
            
            --md-sys-color-secondary: #75565D;
            --md-sys-color-secondary-container: #FFD9E0;
            --md-sys-color-on-secondary-container: #2B151A;

            --md-sys-color-surface: #FFFBFF;
            --md-sys-color-on-surface: #201A1B;
            
            --md-sys-color-surface-variant: #F3DDE0;
            --md-sys-color-on-surface-variant: #514346;
            
            --md-sys-color-outline: #847376;
            --md-sys-color-background: #FFFBFF;

            /* --- Elevation --- */
            --md-sys-elevation-1: 0px 1px 3px 1px rgba(0, 0, 0, 0.15), 0px 1px 2px 0px rgba(0, 0, 0, 0.3);
            --md-sys-elevation-3: 0px 4px 8px 3px rgba(0, 0, 0, 0.15), 0px 1px 3px 0px rgba(0, 0, 0, 0.3);

            /* --- Shapes --- */
            --md-sys-shape-corner-extra-large: 28px;
            --md-sys-shape-corner-full: 100px;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html, body {
            background-color: var(--md-sys-color-background);
            color: var(--md-sys-color-on-surface);
            font-family: 'Roboto', sans-serif;
            line-height: 1.6;
            overflow-x: hidden;
        }

        h1, h2, h3, .logo, .btn-text {
            font-family: 'Outfit', sans-serif;
        }

        /* --- Navigation Bar (Top App Bar Style) --- */
        nav {
            display: flex;
            padding: 20px 4%;
            justify-content: space-between;
            align-items: center;
            background: var(--md-sys-color-surface); /* Solid background for legibility */
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        nav .logo {
            color: var(--md-sys-color-primary);
            font-size: 28px;
            font-weight: 700;
            text-decoration: none;
            letter-spacing: -0.5px;
        }

        .nav-links ul {
            display: flex;
            gap: 8px;
            align-items: center;
        }

        .nav-links ul li {
            list-style: none;
        }

        /* MD3 Navigation Pills */
        .nav-links ul li a,
        .nav-links ul li form button {
            display: inline-block;
            padding: 10px 24px;
            border-radius: var(--md-sys-shape-corner-full);
            color: var(--md-sys-color-on-surface-variant);
            text-decoration: none;
            font-weight: 500;
            font-size: 14px;
            font-family: 'Outfit', sans-serif;
            transition: all 0.2s ease;
            background: transparent;
            border: none;
            cursor: pointer;
        }

        .nav-links ul li a:hover,
        .nav-links ul li form button:hover {
            background-color: var(--md-sys-color-surface-variant);
            color: var(--md-sys-color-on-surface);
        }

        /* --- Hero Section --- */
        .header {
            min-height: 90vh;
            width: 96%; /* Margin on sides */
            margin: 0 auto 20px auto;
            border-radius: var(--md-sys-shape-corner-extra-large);
            
            /* Background image with no overlay */
            background-image: url('assets/img/landingbgwhite.png');
            background-size: cover;
            background-position: center;

            position: relative;
            display: flex;
            align-items: center;
            overflow: hidden;
        }

        .text-box {
            position: relative;
            z-index: 2;
            width: 100%;
            max-width: 800px;
            padding: 0 60px;
        }

        .text-box h1 {
            font-size: 64px;
            color: #000000;
            line-height: 1.1;
            margin-bottom: 24px;
            font-weight: 600;
            /* WIDE SPREAD SHADOW: 3 layers for maximum spread */
            text-shadow: 
                0 0 20px rgba(255, 255, 255, 1), 
                0 0 40px rgba(255, 255, 255, 0.9), 
                0 0 80px rgba(255, 255, 255, 0.8);
        }


        .text-box p {
            font-size: 20px;
            color: #000000;
            margin-bottom: 40px;
            max-width: 600px;
            /* WIDE SPREAD SHADOW for paragraph?
            */
            text-shadow: 
                0 0 15px rgba(255, 255, 255, 1),
                0 0 20px rgba(255, 255, 255, 0.9),
                0 0 25px rgba(255, 255, 255, 0.9), 
                0 0 30px rgba(255, 255, 255, 0.9),
                0 0 35px rgba(255, 255, 255, 0.9),
                0 0 40px rgba(255, 255, 255, 0.9);
        }

        /* --- MD3 Buttons --- */
        .auth-buttons {
            display: flex;
            gap: 16px;
        }

        .auth-buttons a {
            display: inline-flex;
            justify-content: center;
            align-items: center;
            padding: 0 32px;
            height: 48px; /* Standard MD3 touch target */
            border-radius: var(--md-sys-shape-corner-full);
            font-family: 'Outfit', sans-serif;
            font-weight: 500;
            font-size: 16px;
            text-decoration: none;
            transition: 0.2s ease;
        }

        /* Filled Button (Primary) */
        .auth-buttons a:last-child {
            background-color: var(--md-sys-color-primary);
            color: var(--md-sys-color-on-primary);
            box-shadow: var(--md-sys-elevation-1);
        }

        .auth-buttons a:last-child:hover {
            box-shadow: var(--md-sys-elevation-3);
            background-color: #9F003A; /* Slightly darker on hover */
        }

        /* Outlined/Tonal Button (Secondary) */
        .auth-buttons a:first-child {
            background-color: rgba(255, 255, 255, 0.8); /* Semi-transparent white bg for readability */
            backdrop-filter: blur(4px); /* Adds a glass effect */
            color: var(--md-sys-color-primary);
            border: 2px solid var(--md-sys-color-primary);
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        
        .auth-buttons a:first-child:hover {
            background-color: #ffffff;
            box-shadow: 0 4px 8px rgba(0,0,0,0.15);
        }

        /* --- Services Section --- */
        .services {
            width: 92%;
            max-width: 1200px;
            margin: 80px auto;
        }

        .section-header {
            text-align: center;
            margin-bottom: 60px;
        }

        .section-title {
            font-size: 36px;
            color: var(--md-sys-color-on-surface);
            margin-bottom: 16px;
        }

        .section-description {
            max-width: 700px;
            margin: 0 auto;
            color: var(--md-sys-color-on-surface-variant);
            font-size: 18px;
        }

        .row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 24px;
        }

        /* MD3 Cards */
        .services-col {
            background: var(--md-sys-color-surface-variant); /* Surface variant fill */
            border-radius: var(--md-sys-shape-corner-extra-large);
            padding: 32px;
            transition: transform 0.3s ease;
            color: var(--md-sys-color-on-surface-variant);
            border: none; /* No borders in MD3 cards usually */
        }

        .services-col:hover {
            transform: translateY(-5px);
            /* Optional: Add elevation on hover */
            /* box-shadow: var(--md-sys-elevation-1); */
        }

        .services-col h3 {
            margin-bottom: 12px;
            font-size: 24px;
            color: var(--md-sys-color-on-surface);
        }

        /* --- How It Works --- */
        .how-it-works {
            padding: 80px 0;
            /* Alternating background using a very light surface tint */
            background-color: linear-gradient(0deg, rgba(185, 0, 68, 0.05), rgba(185, 0, 68, 0.05)), #FFFBFF; 
        }

        .steps-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 24px;
            margin-top: 40px;
        }

        .step-col {
            background: var(--md-sys-color-surface);
            border-radius: var(--md-sys-shape-corner-extra-large);
            padding: 32px;
            border: 1px solid var(--md-sys-color-outline); /* Outlined card style */
            position: relative;
            /* Remove left border, use MD3 styling */
            border-left: 1px solid var(--md-sys-color-outline); 
        }

        .step-number {
            display: inline-flex;
            justify-content: center;
            align-items: center;
            background-color: var(--md-sys-color-primary-container);
            color: var(--md-sys-color-on-primary-container);
            width: 40px;
            height: 40px;
            border-radius: 12px; /* Squircle */
            font-family: 'Outfit', sans-serif;
            font-weight: 700;
            margin-bottom: 16px;
        }

        /* --- Footer --- */
        footer {
            background-color: var(--md-sys-color-on-surface); /* Dark background */
            color: var(--md-sys-color-surface);
            padding: 60px 4% 30px;
            border-top-left-radius: var(--md-sys-shape-corner-extra-large);
            border-top-right-radius: var(--md-sys-shape-corner-extra-large);
            margin-top: 40px;
        }

        .footer-container {
            max-width: 1200px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 60px;
        }

        footer h2 {
            color: var(--md-sys-color-primary-container);
            font-size: 24px;
            margin-bottom: 16px;
        }

        footer p {
            color: #E6E1E5; /* Light grey for dark bg */
            font-size: 16px;
        }

        .footer-bottom {
            margin-top: 60px;
            padding-top: 20px;
            border-top: 1px solid #49454F;
            text-align: center;
        }

        /* --- Loading Bar --- */
        #loading-bar-container {
            position: fixed;
            top: 0; left: 0; width: 100%; height: 4px;
            background-color: var(--md-sys-color-surface-variant);
            z-index: 9999;
        }

        #loading-bar {
            height: 100%;
            width: 0%;
            background-color: var(--md-sys-color-primary);
            transition: width 0.2s ease;
            border-radius: 0 2px 2px 0;
        }

        /* --- Mobile Responsive --- */
        .fa-bars, .fa-times {
            color: var(--md-sys-color-on-surface);
            font-size: 24px;
            cursor: pointer;
            display: none;
        }

        @media(max-width: 900px) {
            .text-box h1 { font-size: 48px; }
            .header { min-height: 80vh; }
        }

        @media(max-width: 700px) {
            nav .fa-bars { display: block; }
            
            .nav-links {
                position: fixed;
                background: var(--md-sys-color-surface-variant); /* Drawer color */
                height: 100vh;
                width: 280px; /* Standard drawer width */
                top: 0;
                right: -280px;
                z-index: 2000;
                transition: 0.3s cubic-bezier(0.2, 0.0, 0, 1.0); /* Standard ease */
                padding: 24px;
                border-top-left-radius: var(--md-sys-shape-corner-extra-large);
                border-bottom-left-radius: var(--md-sys-shape-corner-extra-large);
                box-shadow: -2px 0 8px rgba(0,0,0,0.1);
            }

            .nav-links ul {
                flex-direction: column;
                align-items: flex-start;
                margin-top: 40px;
            }

            .nav-links ul li {
                width: 100%;
                margin-bottom: 8px;
            }

            .nav-links ul li a, .nav-links ul li form button {
                width: 100%;
                text-align: left;
                padding: 16px 24px; /* Larger touch target for mobile */
            }

            .nav-links .fa-times {
                display: block;
                margin-left: auto;
            }

            .text-box { padding: 0 24px; }
            .text-box h1 { font-size: 40px; }
            
            .auth-buttons {
                flex-direction: column;
                width: 100%;
            }
            .auth-buttons a { width: 100%; }

            .footer-container { grid-template-columns: 1fr; }
        }
    </style>
</head>

<body>

    <div id="loading-bar-container">
        <div id="loading-bar"></div>
    </div>

    <nav>
        <a href="#" class="logo">Publ.</a>
        <div class="nav-links" id="navLinks">
            <i class="fa fa-times" onclick="hideMenu()"></i>
            <ul>
                @guest
                    <li><a href="{{ route('login') }}">Log In</a></li>
                    <li><a href="{{ route('register') }}" style="background-color: var(--md-sys-color-primary); color: var(--md-sys-color-on-primary);">Register</a></li>
                @else
                    <li><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li>
                        <form method="POST" action="{{ url('/logout') }}">
                            @csrf
                            <button type="submit">Log Out</button>
                        </form>
                    </li>
                @endguest
            </ul>
        </div>
        <i class="fa fa-bars" onclick="showMenu()"></i>
    </nav>

    <section class="header">
        <div class="text-box">
            <h1><span class="text-highlight">Report with Publ.</span></h1>
            <p>A crowdsourced platform for reporting incidents and verifying their authenticity through community collaboration.</p>

            <div class="auth-buttons">
                @auth
                    <a href="{{ route('dashboard') }}">Go to Dashboard</a>
                @else
                    <a href="{{ route('login') }}">Log In</a>
                    <a href="{{ route('register') }}">Get Started</a>
                @endauth
            </div>
        </div>
    </section>

    <section class="services">
        <div class="section-header">
            <h1 class="section-title">Our Services</h1>
            <p class="section-description">Publ provides a comprehensive platform for incident reporting and verification, empowering communities to collaborate.</p>
        </div>

        <div class="row">
            <div class="services-col">
                <i class="fa-solid fa-bullhorn" style="font-size: 32px; color: var(--md-sys-color-primary); margin-bottom: 16px;"></i>
                <h3>Incident Reporting</h3>
                <p>Quickly and easily report incidents in your community with our intuitive reporting system.</p>
            </div>

            <div class="services-col">
                <i class="fa-solid fa-users-viewfinder" style="font-size: 32px; color: var(--md-sys-color-primary); margin-bottom: 16px;"></i>
                <h3>Witness Verification</h3>
                <p>Witnesses can confirm or deny reported incidents, adding credibility through crowd-sourced verification.</p>
            </div>

            <div class="services-col">
                <i class="fa-solid fa-earth-americas" style="font-size: 32px; color: var(--md-sys-color-primary); margin-bottom: 16px;"></i>
                <h3>Public Awareness</h3>
                <p>Allow for broader information dissemination and enhance public awareness on incidents.</p>
            </div>
        </div>
    </section>

    <section class="how-it-works">
        <div class="services">
            <div class="section-header">
                <h1 class="section-title">How Publ Works</h1>
                <p class="section-description">Our process ensures that reported incidents are efficiently verified.</p>
            </div>

            <div class="steps-row">
                <div class="step-col">
                    <div class="step-number">1</div>
                    <h3>Report an Incident</h3>
                    <p>Users submit detailed reports of incidents they've witnessed, including location and media.</p>
                </div>

                <div class="step-col">
                    <div class="step-number">2</div>
                    <h3>Witness Verification</h3>
                    <p>Other users who witnessed the incident can confirm or deny the report, establishing credibility.</p>
                </div>

                <div class="step-col">
                    <div class="step-number">3</div>
                    <h3>Authority Notification</h3>
                    <p>Verified incidents are automatically forwarded to the relevant authorities for review and action.</p>
                </div>

                <div class="step-col">
                    <div class="step-number">4</div>
                    <h3>Status Updates</h3>
                    <p>Reporters and witnesses receive updates as authorities process and address the reported incident.</p>
                </div>

                <div class="step-col">
                    <div class="step-number">5</div>
                    <h3>Resolution Tracking</h3>
                    <p>Track the resolution process and provide feedback on the outcome once the incident has been addressed.</p>
                </div>

                <div class="step-col">
                    <div class="step-number">6</div>
                    <h3>Community Awareness</h3>
                    <p>Resolved incidents contribute to community awareness and help prevent similar issues.</p>
                </div>
            </div>
        </div>
    </section>

    <footer>
        <div class="footer-container">
            <div>
                <h2>Our Mission</h2>
                <p>
                    At Publ, our mission is to create safer communities by enabling transparent incident reporting and verification. 
                </p>
            </div>

            <div>
                <h2>Our Vision</h2>
                <p>
                    We envision a world where community members and authorities collaborate seamlessly to create safer environments.
                </p>
            </div>
        </div>

        <div class="footer-bottom">
            <p>&copy; 2025 Publ. All rights reserved.</p>
        </div>
    </footer>

    <script>
        var navLinks = document.getElementById("navLinks");

        function showMenu(){
            navLinks.style.right = "0";
        }
        function hideMenu(){
            navLinks.style.right = "-280px";
        }
    </script>
</body>
</html>