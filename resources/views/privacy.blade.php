<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Privacy Policy</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background: linear-gradient(135deg, #FF0B55 0%, #CF0F47 100%);
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
        <h1>Privacy Policy</h1>
        <p class="last-updated">Last Updated: November 6, 2025</p>

        <p>We value your privacy. This policy explains how we handle your information.</p>

        <h2>1. Information We Collect</h2>
        <p>We collect your email, username, profile info, usage data, and device information to provide our service.</p>

        <h2>2. How We Use Your Data</h2>
        <p>We use your information to maintain your account, improve our service, communicate with you, and ensure security.</p>

        <h2>3. Information Sharing</h2>
        <p>We don't sell your data. We only share with service providers, when legally required, or with your consent.</p>

        <h2>4. Data Security</h2>
        <p>We implement security measures to protect your information, though no system is 100% secure.</p>

        <h2>5. Your Rights</h2>
        <p>You can access, correct, delete, or transfer your data. You may also withdraw consent anytime.</p>

        <h2>6. Cookies</h2>
        <p>We use cookies to improve your experience. You can disable them in your browser settings.</p>

        <h2>7. Contact</h2>
        <p>Questions about privacy? Email us at privacy@example.com</p>

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