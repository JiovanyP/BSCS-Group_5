<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Terms of Service</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background: linear-gradient(135deg, #FF0B55, #CF0F47 100%);
            min-height: 100vh;
            padding: 40px 20px;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .container {
            background: #faf9f8;
            border-radius: 24px;
            padding: 50px 60px;
            max-width: 700px;
            width: 100%;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        }

        h1 {
            color: #c9184a;
            font-size: 2.2em;
            margin-bottom: 8px;
            text-align: center;
        }

        .last-updated {
            text-align: center;
            color: #666;
            margin-bottom: 30px;
            font-size: 0.85em;
        }

        h2 {
            color: #c9184a;
            font-size: 1.2em;
            margin-top: 20px;
            margin-bottom: 10px;
        }

        p {
            color: #333;
            line-height: 1.6;
            margin-bottom: 15px;
            font-size: 0.95em;
        }

        ul {
            margin-left: 25px;
            margin-bottom: 15px;
        }

        li {
            color: #333;
            line-height: 1.6;
            margin-bottom: 6px;
            font-size: 0.95em;
        }

        .back-btn {
            display: inline-block;
            margin-top: 30px;
            padding: 12px 28px;
            background: #c9184a;
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            transition: background 0.3s ease;
            font-size: 0.95em;
            cursor: pointer;
            border: none;
        }

        .back-btn:hover {
            background: #a01640;
        }

        .btn-container {
            text-align: center;
        }

        @media (max-width: 768px) {
            .container {
                padding: 35px 25px;
            }

            h1 {
                font-size: 1.8em;
            }

            h2 {
                font-size: 1.1em;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Terms of Service</h1>
        <p class="last-updated">Last Updated: November 6, 2025</p>

        <p>By using our service, you agree to these terms. Please read them carefully before continuing.</p>

        <h2>1. Account Responsibility</h2>
        <p>You must provide accurate information and keep your account secure. You're responsible for all activities under your account.</p>

        <h2>2. Acceptable Use</h2>
        <p>Do not use our service to violate laws, infringe rights, transmit harmful content, or interfere with the platform.</p>

        <h2>3. Content & Intellectual Property</h2>
        <p>All platform content is protected by copyright. You may not copy or distribute without permission.</p>

        <h2>4. Account Termination</h2>
        <p>We may suspend or terminate accounts that violate these terms at any time.</p>

        <h2>5. Disclaimers</h2>
        <p>Service is provided "as is" without warranties. We're not liable for indirect damages from your use.</p>

        <h2>6. Contact</h2>
        <p>Questions? Email us at support@example.com</p>

        <div class="btn-container">
            <button onclick="goBack()" class="back-btn">Back</button>
        </div>
    </div>

    <script>
        function goBack() {
            // Check if there's a previous page in history
            if (document.referrer && document.referrer !== '') {
                // Go back to previous page
                window.history.back();
            } else {
                // If no referrer, go to home page
                window.location.href = "{{ route('welcome') }}";
            }
        }
    </script>
</body>
</html>