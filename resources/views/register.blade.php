<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Register</title>

  <style>
    :root {
      --accent: #CF0F47;
      --accent-2: #FF0B55;
      --card-bg: #ffffff;
      --muted: #666;
    }

    html,body { height:100%; margin:0; font-family: "Helvetica Neue", Arial, sans-serif; -webkit-font-smoothing:antialiased; -moz-osx-font-smoothing:grayscale; }
    body {
      background: linear-gradient(135deg, var(--bg1), var(--accent), var(--accent-2));
      background-size: 400% 400%;
      display:flex; align-items:center; justify-content:center; min-height:100vh;
    }

    .register-container {
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

    form { width:100%; margin-top:6px; text-align:left; }

    label { display:block; font-size:13px; color:#444; margin-bottom:6px; }
    input[type="text"], input[type="password"], input[type="email"] {
      width:100%; padding:12px 12px; border-radius:10px; border:1px solid #ddd;
      margin-bottom:12px; box-sizing:border-box; font-size:14px; background:#fbfbfb;
    }

    input:focus {
      border-color: var(--accent);
      background: #fff;
      box-shadow: 0 0 0 3px rgba(207, 15, 71, 0.1);
      outline: none;
    }

    .btn {
      display:inline-block; width:100%; padding:12px 14px; border-radius:10px; border:0;
      background:var(--accent); color:#fff; font-weight:700; cursor:pointer; font-size:15px;
      transition:0.25s;
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
  <main class="register-container" role="main" aria-labelledby="registerTitle">
    <h1 id="registerTitle">Create an Account</h1>
    <div class="subtitle">Join our community and get started today</div>

    {{-- Success message --}}
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

    <!-- Registration Form -->
    <form method="POST" action="{{ route('register.post') }}" novalidate>
      @csrf

      <label for="name">Full Name</label>
      <input id="name" name="name" type="text" value="{{ old('name') }}" required aria-required="true" />

      <label for="email">Email Address</label>
      <input id="email" name="email" type="email" value="{{ old('email') }}" required aria-required="true" />

      <label for="location">Location</label>
      <input id="location" name="location" type="text" value="{{ old('location') }}" required aria-required="true" />

      <label for="password">Password</label>
      <input id="password" name="password" type="password" required aria-required="true" />

      <label for="password_confirmation">Confirm Password</label>
      <input id="password_confirmation" name="password_confirmation" type="password" required aria-required="true" />

      <button type="submit" class="btn">Register</button>
    </form>

    <div class="links" style="margin-top:14px;">
      <a href="{{ route('login') }}">Already have an account? Log In</a>
      <a href="{{ url('/') }}">Back to home</a>
    </div>

    <footer class="small">
      By registering, you agree to our <a href="#" style="color:var(--accent)">Terms</a> and <a href="#" style="color:var(--accent)">Privacy Policy</a>.
    </footer>
  </main>

  <!-- Optional submit UX -->
  <script>
    (function(){
      try {
        const form = document.querySelector('form');
        if (!form) return;
        form.addEventListener('submit', function(){
          const btn = document.querySelector('.btn');
          if (btn) {
            btn.disabled = true;
            btn.textContent = 'Creating account...';
          }
        });
      } catch(e) { /* ignore */ }
    })();
  </script>
</body>
</html>
