<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <style>
        :root {
            --accent: #CF0F47;
            --accent-2: #FF0B55;
            --card-bg: #ffffff;
            --muted: #666;
        }

        html, body {
            margin: 0;
            height: 100%;
            font-family: "Helvetica Neue", Arial, sans-serif;
            background: var(--accent);
            display: flex;
            justify-content: center;
            align-items: center;
            -webkit-font-smoothing: antialiased;
        }

        .admin-login-container {
            width: 460px;
            max-width: calc(100% - 40px);
            background: var(--card-bg);
            border-radius: 16px;
            padding: 36px;
            box-shadow: 0 12px 40px rgba(0, 0, 0, 0.1);
            text-align: left;
        }

        .admin-login-container h1 {
            margin: 0 0 14px 0;
            color: var(--accent);
            font-size: 24px;
            letter-spacing: 0.2px;
            text-align: center;
        }

        .subtitle {
            color: var(--muted);
            margin-bottom: 18px;
            font-size: 13px;
            text-align: center;
        }

        form {
            width: 100%;
            margin-top: 6px;
        }

        label {
            display: block;
            font-size: 13px;
            color: #444;
            margin-bottom: 6px;
        }

        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 12px;
            border-radius: 10px;
            border: 1px solid #ddd;
            margin-bottom: 12px;
            box-sizing: border-box;
            font-size: 14px;
            background: #fbfbfb;
        }

        input:focus {
            border-color: var(--accent);
            background: #fff;
            box-shadow: 0 0 0 3px rgba(207, 15, 71, 0.1);
            outline: none;
        }

        .row {
            display: flex;
            gap: 10px;
            align-items: center;
        }

        .remember {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 13px;
            color: #555;
        }

        .btn {
            display: block;
            width: 100%;
            padding: 12px 14px;
            border-radius: 10px;
            border: 0;
            font-weight: 700;
            cursor: pointer;
            font-size: 15px;
            transition: 0.25s;
            text-align: center;
            text-decoration: none;
        }

        .btn-primary {
            background: var(--accent);
            color: #fff;
        }

        .btn-primary:hover {
            background: var(--accent-2);
        }

        .btn-secondary {
            margin-top: 10px;
            background: #eee;
            color: #444;
        }

        .btn-secondary:hover {
            background: #ddd;
        }

        .error-box {
            background: #fff0f0;
            border: 1px solid #ffd0d0;
            color: #a00000;
            padding: 10px 12px;
            border-radius: 8px;
            margin-bottom: 12px;
            font-size: 13px;
        }

        .success-box {
            background: #f1fff1;
            border: 1px solid #cfeecf;
            color: #0a7a0a;
            padding: 10px 12px;
            border-radius: 8px;
            margin-bottom: 12px;
            font-size: 13px;
        }

        .forgot-link {
            font-size: 13px;
            color: var(--accent);
            text-decoration: none;
        }

        .forgot-link:hover {
            text-decoration: underline;
        }

        footer.small {
            margin-top: 18px;
            color: #888;
            font-size: 12px;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="admin-login-container" role="main" aria-labelledby="adminLoginTitle">
        <h1 id="adminLoginTitle"><strong>Admin Portal</strong></h1>
        <div class="subtitle">Sign in with your administrator credentials</div>

        <!-- Flash success message (dynamically shown) -->
        <!-- <div class="success-box" role="status">Success message here</div> -->

        <!-- Validation errors (dynamically shown) -->
        <!-- <div class="error-box" role="alert">
            <strong>There was a problem:</strong>
            <ul style="margin:8px 0 0 18px; padding:0;">
                <li>Error message here</li>
            </ul>
        </div> -->

        <form method="POST" action="{{ route('admin.login.post') }}" novalidate>
            @csrf

            <label for="email">Email or Username</label>
            <input id="email" name="email" type="text" value="{{ old('email') }}" required autocomplete="username" />

            <label for="password">Password</label>
            <input id="password" name="password" type="password" required autocomplete="current-password" />

            <div class="row" style="margin-bottom:12px;">
                <label class="remember">
                    <input type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                    Remember me
                </label>
                <div style="margin-left:auto;">
                    <a class="forgot-link" href="{{ route('admin.password.request') }}">Forgot password?</a>
                </div>
            </div>

            <button type="submit" class="btn btn-primary">Log In</button>
            <a href="{{ url('/') }}" class="btn btn-secondary">Back to Home</a>
        </form>

        <footer class="small" style="margin-top:16px;">
            By accessing, you agree to our <a href="#" style="color:var(--accent)">Terms</a> and <a href="#" style="color:var(--accent)">Privacy</a>.
        </footer>
    </div>

    <script>
        (function(){
            const form = document.querySelector('form');
            if (!form) return;
            const btn = document.querySelector('.btn-primary');
            form.addEventListener('submit', function(){
                btn.disabled = true;
                btn.textContent = 'Signing in...';
            });
        })();
    </script>
</body>
</html>