<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Login</title>

  <style>
    :root {
      --accent: #CF0F47;
      --accent-2: #FF0B55;
      --bg1: #000000;
      --card-bg: #ffffff;
      --muted: #666;
    }

    html,body { height:100%; margin:0; font-family: "Helvetica Neue", Arial, sans-serif; -webkit-font-smoothing:antialiased; -moz-osx-font-smoothing:grayscale; }
    body {
      background: linear-gradient(135deg, var(--bg1), var(--accent), var(--accent-2));
      background-size: 400% 400%;
      display:flex; align-items:center; justify-content:center; min-height:100vh;
    }

    .login-container {
      width: 420px;
      max-width: calc(100% - 40px);
      background: var(--card-bg);
      border-radius: 16px;
      padding: 36px;
      box-shadow: 0 12px 40px rgba(0,0,0,0.35);
      text-align: center;
    }

    h1 {
      margin: 0 0 14px 0;
      color: var(--accent);
      font-size: 24px;
      letter-spacing: 0.2px;
    }

    .subtitle {
      color: var(--muted);
      margin-bottom: 18px;
      font-size: 13px;
    }

    .social-btn {
      display:flex; gap:12px; align-items:center; justify-content:center;
      width:100%; padding:10px 12px; border-radius:10px; border:1px solid #ddd; background:#fff;
      text-decoration:none; color:#111; font-weight:600; box-sizing:border-box;
    }
    .social-btn img { height:20px; width:20px; display:block; }

    .or {
      margin: 18px 0;
      display:flex; align-items:center; gap:10px; color:var(--muted); font-size:13px;
    }
    .or::before, .or::after { content:""; flex:1; height:1px; background:#eee; display:block; }

    form { width:100%; margin-top:6px; text-align:left; }

    label { display:block; font-size:13px; color:#444; margin-bottom:6px; }
    input[type="text"], input[type="password"], input[type="email"] {
      width:100%; padding:12px 12px; border-radius:10px; border:1px solid #ddd;
      margin-bottom:12px; box-sizing:border-box; font-size:14px; background:#fbfbfb;
    }

    .row { display:flex; gap:10px; align-items:center; }
    .remember { display:flex; align-items:center; gap:8px; font-size:13px; color:#555; }

    .btn {
      display:inline-block; width:100%; padding:12px 14px; border-radius:10px; border:0;
      background:var(--accent); color:#fff; font-weight:700; cursor:pointer; font-size:15px;
    }
    .btn:hover { background:var(--accent-2); }

    .links { margin-top:12px; display:flex; justify-content:space-between; font-size:13px; }
    .links a { color:var(--accent); text-decoration:none; }
    .links a:hover { text-decoration:underline; }

    .error-box {
      background:#fff0f0; border:1px solid #ffd0d0; color:#a00000; padding:10px 12px; border-radius:8px;
      margin-bottom:12px; font-size:13px; text-align:left;
    }

    .success-box {
      background:#f1fff1; border:1px solid #cfeecf; color:#0a7a0a; padding:10px 12px; border-radius:8px;
      margin-bottom:12px; font-size:13px; text-align:left;
    }

    footer.small { margin-top:18px; color:#888; font-size:12px; }
  </style>
</head>
<body>
  <main class="login-container" role="main" aria-labelledby="loginTitle">
    <h1 id="loginTitle">Welcome back — Log in</h1>
    <div class="subtitle">Sign in to your account to continue</div>

    {{-- Flash success --}}
    @if(session('success'))
      <div class="success-box" role="status">{{ session('success') }}</div>
    @endif

    {{-- Validation errors --}}
    @if ($errors->any())
      <div class="error-box" role="alert">
        <strong>There was a problem:</strong>
        <ul style="margin:8px 0 0 18px; padding:0;">
          @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    <!-- Google Login -->
    <a class="social-btn" href="{{ route('google.login') }}" aria-label="Continue with Google">
      <img src="https://developers.google.com/identity/images/g-logo.png" alt="Google logo" />
      <span>Continue with Google</span>
    </a>

    <div class="or" aria-hidden="true">OR</div>

    <!-- Login Form -->
    <form method="POST" action="{{ route('login.post') }}" novalidate>
      @csrf

      <label for="email">Email or Username</label>
      <input id="email" name="email" type="text" value="{{ old('email') }}" autocomplete="username" required aria-required="true" />

      <label for="password">Password</label>
      <input id="password" name="password" type="password" autocomplete="current-password" required aria-required="true" />

      <div class="row" style="margin-bottom:12px;">
        <label class="remember">
          <input type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
          Remember me
        </label>

        <div style="margin-left:auto;">
          <a href="#" onclick="alert('Password reset flow not implemented'); return false;" style="color:var(--accent); text-decoration:none;">Forgot password?</a>
        </div>
      </div>

      <button type="submit" class="btn">Log In</button>
    </form>

    <div class="links" style="margin-top:14px;">
      <a href="{{ route('register') }}">Create account</a>
      <a href="{{ url('/') }}">Back to home</a>
    </div>

    <footer class="small">By continuing you agree to our <a href="#" style="color:var(--accent)">Terms</a> and <a href="#" style="color:var(--accent)">Privacy</a>.</footer>
  </main>

  <!-- optional small client-side snippet to improve UX -->
  <script>
    (function(){
      try {
        const form = document.querySelector('form');
        if (!form) return;
        form.addEventListener('submit', function(){
          const btn = document.querySelector('.btn');
          if (btn) {
            btn.disabled = true;
            btn.textContent = 'Signing in...';
          }
        });
      } catch(e) { /* ignore */ }
    })();
  </script>
</body>
</html>
