<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Forgot Password</title>

  <style>
    :root {
      --accent: #CF0F47;
      --accent-2: #FF0B55;
      --card-bg: #ffffff;
      --muted: #666;
    }

    html,body {
      height:100%;
      margin:0;
      background-color:#fff;
      font-family:"Helvetica Neue", Arial, sans-serif;
      -webkit-font-smoothing:antialiased;
      -moz-osx-font-smoothing:grayscale;
    }

    body {
      background-color:#FF0B55;
      display:flex;
      align-items:center;
      justify-content:center;
      min-height:100vh;
    }

    .forgot-container {
      width:420px;
      max-width:calc(100% - 40px);
      background:var(--card-bg);
      border-radius:16px;
      padding:36px;
      box-shadow:0 12px 40px rgba(0,0,0,0.35);
      text-align:center;
    }

    h1 {
      margin:0 0 14px 0;
      color:var(--accent);
      font-size:24px;
      letter-spacing:0.2px;
    }

    .subtitle {
      color:var(--muted);
      margin-bottom:18px;
      font-size:13px;
    }

    label {
      display:block;
      font-size:13px;
      color:#444;
      margin-bottom:6px;
      text-align:left;
    }

    input[type="email"] {
      width:100%;
      padding:12px 12px;
      border-radius:10px;
      border:1px solid #ddd;
      margin-bottom:12px;
      box-sizing:border-box;
      font-size:14px;
      background:#fbfbfb;
    }

    .btn {
      display:inline-block;
      width:100%;
      padding:12px 14px;
      border-radius:10px;
      border:0;
      background:var(--accent);
      color:#fff;
      font-weight:700;
      cursor:pointer;
      font-size:15px;
      transition:background 0.2s ease-in-out;
    }

    .btn:hover {
      background:var(--accent-2);
    }

    .links {
      margin-top:14px;
      display:flex;
      justify-content:center;
      font-size:13px;
    }

    .links a {
      color:var(--accent);
      text-decoration:none;
    }

    .links a:hover {
      text-decoration:underline;
    }

    .error-box {
      background:#fff0f0;
      border:1px solid #ffd0d0;
      color:#a00000;
      padding:10px 12px;
      border-radius:8px;
      margin-bottom:12px;
      font-size:13px;
      text-align:left;
    }

    .success-box {
      background:#f1fff1;
      border:1px solid #cfeecf;
      color:#0a7a0a;
      padding:10px 12px;
      border-radius:8px;
      margin-bottom:12px;
      font-size:13px;
      text-align:left;
    }

    footer.small {
      margin-top:18px;
      color:#888;
      font-size:12px;
    }
  </style>
</head>

<body>
  <main class="forgot-container" role="main" aria-labelledby="forgotTitle">
    <h1 id="forgotTitle">Forgot Password</h1>
    <div class="subtitle">Enter your email to receive a password reset link.</div>

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

    <form method="POST" action="{{ route('password.email') }}">
      @csrf
      <label for="email">Email Address</label>
      <input id="email" name="email" type="email" value="{{ old('email') }}" autocomplete="email" required autofocus aria-required="true" />

      <button type="submit" class="btn">Send Reset Link</button>
    </form>

    <div class="links">
      <a href="{{ route('login') }}">Back to Login</a>
    </div>

    <footer class="small">By continuing, you agree to our <a href="{{ route('terms') }}" style="color:var(--accent)">Terms</a> and <a href="{{ route('privacy') }}" style="color:var(--accent)">Privacy Policy</a>.</footer>
  </main>
</body>
</html>
