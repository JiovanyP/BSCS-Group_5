<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PubL</title>
    
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
            color: #333;
            line-height: 1.6;
            overflow-x: hidden;
        }

        .header {
            min-height: 100vh;
            width: 100%;
            background-image: linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.6)), url(https://www.usm.edu.ph/wp-content/uploads/2019/01/USM_Administration_Building-1024x682.png);
            background-position: center;
            background-size: cover;
            position: relative;
            display: flex;
            flex-direction: column;
        }

        nav {
            display: flex;
            padding: 1.5% 4%;
            justify-content: space-between;
            align-items: center;
            width: 100%;
        }

        nav .logo {
            color: #FF0B55;
            /* color: #fff; */
            font-size: 32px;
            font-weight: 700;
            text-decoration: none;
            margin-left: 0;
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

        .text-box {
            width: 100%;
            color: #fff;
            text-align: center;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 0 20px;
        }

        .text-box h2 {
            font-size: 50px;
            font-weight: 400;
            margin-bottom: 10px;
            letter-spacing: 1.5px;
            color: white;
        }

        .text-box h1 {
            font-size: 100px;
            /* font-weight: 1500; */
            color: #FF0B55;
            margin-bottom: 20px;
            letter-spacing: 2px;
            line-height: 1.2;
        }

        .text-box p {
            margin: 20px auto 30px;
            font-size: 15px;
            color: #fff;
            max-width: 800px;
            line-height: 1.8;
            font-weight: 300;
        }

        .auth-buttons {
            margin-top: 30px;
            display: flex;
            gap: 25px;
            justify-content: center;
        }

        .auth-buttons a {
            display: inline-block;
            padding: 16px 42px;
            font-size: 18px;
            font-weight: 600;
            color: #fff;
            border-radius: 4px;
            text-decoration: none;
            transition: all 0.3s ease;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            background: #000000; /* Changed to black background */
            border: 2px solid #FF0B55; /* Keep pink border */
        }

        .auth-buttons a:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.2);
            background: #1a1a1a; /* Darker black on hover */
            border-color: #FF0B55; /* Keep pink border on hover */
        }

        .services {
            width: 80%;
            margin: auto;
            text-align: center;
            padding: 80px 0;
        }

        .section-title {
            font-size: 36px;
            font-weight: 600;
            color: #000;
            margin-bottom: 20px;
            position: relative;
            padding-bottom: 15px;
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

        .section-description {
            color: #555;
            font-size: 18px;
            font-weight: 400;
            line-height: 1.8;
            margin-bottom: 50px;
            max-width: 800px;
            margin-left: auto;
            margin-right: auto;
        }

        .row {
            margin-top: 40px;
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            gap: 30px;
        }

        .services-col {
            flex-basis: 30%;
            min-width: 300px;
            background: #fff;
            border-radius: 8px;
            padding: 30px 20px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            transition: 0.4s;
            border-top: 4px solid #FF0B55;
        }

        .services-col:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 30px rgba(0,0,0,0.1);
        }

        .services-col h3 {
            text-align: center;
            font-weight: 600;
            margin: 15px 0;
            color: #000;
            font-size: 22px;
        }

        .services-col p {
            color: #555;
            font-size: 16px;
            font-weight: 400;
            line-height: 1.7;
            text-align: center;
        }

        .how-it-works {
            background-color: #f9f9f9;
            padding: 0px 0;
        }

        .steps-row {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 30px;
            margin-top: 40px;
        }

        .step-col {
            flex-basis: 30%;
            min-width: 300px;
            background: #fff;
            border-radius: 8px;
            padding: 30px 20px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            transition: 0.4s;
            position: relative;
            border-left: 4px solid #CF0F47;
        }

        .step-number {
            position: absolute;
            top: -20px;
            left: 20px;
            width: 40px;
            height: 40px;
            background: #CF0F47;
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 18px;
        }

        .step-col h3 {
            margin-top: 15px;
            margin-bottom: 15px;
            color: #000;
            font-size: 20px;
        }

        .step-col p {
            color: #555;
            font-size: 16px;
            line-height: 1.7;
        }

        footer {
            background-color: #000;
            color: white;
            padding: 60px 50px 30px;
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
            .text-box h1 {
                font-size: 42px;
            }
            
            .services-col, .step-col {
                flex-basis: 45%;
            }
            
            .footer-container {
                grid-template-columns: 1fr;
                gap: 40px;
            }
        }

        @media(max-width: 700px) {
            .text-box h1 {
                font-size: 36px;
            }
            
            .text-box h2 {
                font-size: 22px;
            }
            
            .text-box p {
                font-size: 16px;
            }
            
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
            
            .services-col, .step-col {
                flex-basis: 100%;
            }
            
            .auth-buttons {
                flex-direction: column;
                align-items: center;
            }
            
            .auth-buttons a {
                width: 220px;
                text-align: center;
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

    @auth
        <div style="background: yellow; padding: 10px; text-align: center;">
            <strong>You are logged in as: {{ Auth::user()->name }}</strong>
        </div>
    @endauth

    <div id="loading-bar-container">
    <div id="loading-bar"></div>
    </div>

    <section class="header">
        <nav>
            <h1 class="logo">PubL</h1>
            <div class="nav-links" id="navLinks">
                <i class="fa fa-times" onclick="hideMenu()"></i>
                <ul>
                    @auth
                        <!-- Show these links if user is logged in -->
                        <li><a href="{{ route('homepage') }}">Dashboard</a></li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                                @csrf
                                <button type="submit" style="background: none; border: none; color: #fff; cursor: pointer; font-size: 16px; font-weight: 500; font-family: 'Poppins', sans-serif; padding: 8px 16px;">
                                    Log Out
                                </button>
                            </form>
                        </li>
                    @else
                        <!-- Show these links if user is NOT logged in -->
                        <li><a href="{{ route('login') }}">Log In</a></li>
                        <li><a href="{{ route('register') }}">Register</a></li>
                    @endauth
                </ul>
            </div>
            <i class="fa fa-bars" onclick="showMenu()"></i>
        </nav>

        <div class="text-box">
            <h2><b>Welcome to</b></h2>
            <h1>PubL</h1>
            <h1>(Change Picture)</h1>
            <p>A crowdsourced platform for reporting incidents and verifying their authenticity through community collaboration.</p>
            
            @auth
                <!-- Show these links if user is logged in -->
                <li><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li>
                    <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                        @csrf
                        <button type="submit" style="background: none; border: none; color: #fff; cursor: pointer; font-size: 16px; font-weight: 500; font-family: 'Poppins', sans-serif; padding: 8px 16px;">
                            Log Out
                        </button>
                    </form>
                </li>
            @else
                <!-- Show these links if user is NOT logged in -->
                <li><a href="{{ route('login') }}">Log In</a></li>
                <li><a href="{{ route('register') }}">Register</a></li>
            @endauth
        </div>
    </section>

    <!-----------Services----------->
    <section class="services">
        <h1 class="section-title"><b>Our Services</b></h1>
        <p class="section-description">PubL provides a comprehensive platform for incident reporting and verification, empowering communities to collaborate on safety issues.</p>

        <div class="row">
            <div class="services-col">
                <h3>Incident Reporting</h3>
                <p>Quickly and easily report incidents in your community with our intuitive reporting system. Provide details, location, and media evidence.</p>
            </div>

            <div class="services-col">
                <h3>Witness Verification</h3>
                <p>Witnesses can confirm or deny reported incidents, adding credibility through crowd-sourced verification of events.</p>
            </div>

            <div class="services-col">
                <h3>Authority Response Tracking</h3>
                <p>Monitor how authorities respond to reported incidents and track resolution progress in real-time.</p>
            </div>
        </div>
    </section>

    <!-----------How it Works----------->
    <section class="how-it-works">
        <div class="services">
            <h1 class="section-title">How PubL Works</h1>
            <p class="section-description">Our process ensures that reported incidents are efficiently verified and addressed by the appropriate authorities.</p>

            <div class="steps-row">
                <div class="step-col">
                    <div class="step-number">1</div>
                    <h3>Report an Incident</h3>
                    <p>Users submit detailed reports of incidents they've witnessed, including location, description, and any supporting media.</p>
                </div>

                <div class="step-col">
                    <div class="step-number">2</div>
                    <h3>Witness Verification</h3>
                    <p>Other users who witnessed the incident can confirm or deny the report, establishing credibility through crowd-sourced verification.</p>
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
                    <p>Resolved incidents contribute to community awareness and help prevent similar issues in the future.</p>
                </div>
            </div>
        </div>
    </section>

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