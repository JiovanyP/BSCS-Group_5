<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Reset Password</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <style>
    :root {
      --accent: #CF0F47;
      --accent-2: #FF0B55;
      --card-bg: #ffffff;
      --muted: #666;
    }

    html, body {
      height: 100%;
      margin: 0;
      background-color: #fff;
      font-family: "Helvetica Neue", Arial, sans-serif;
      -webkit-font-smoothing: antialiased;
      -moz-osx-font-smoothing: grayscale;
    }

    body {
      display: flex;
      align-items: center;
      justify-content: center;
      min-height: 100vh;
    }

    .reset-container {
      width: 420px;
      max-width: calc(100% - 40px);
      background: var(--card-bg);
      border-radius: 16px;
      padding: 36px;
      box-shadow: 0 12px 40px rgba(0, 0, 0, 0.25);
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

    form {
      width: 100%;
      margin-top: 6px;
      text-align: left;
    }

    label {
      display: block;
      font-size: 13px;
      color: #444;
      margin-bottom: 6px;
      font-weight: 500;
    }

    input[type="email"],
    input[type="password"] {
      width: 100%;
      padding: 12px 12px;
      border-radius: 10px;
      border: 1px solid #ddd;
      margin-bottom: 12px;
      box-sizing: border-box;
      font-size: 14px;
      background: #fbfbfb;
    }

    .btn {
      display: inline-block;
      width: 100%;
      padding: 12px 14px;
      border-radius: 10px;
      border: 0;
      background: var(--accent);
      color: #fff;
      font-weight: 700;
      cursor: pointer;
      font-size: 15px;
      transition: background 0.2s ease-in-out;
    }

    .btn:hover {
      background: var(--accent-2);
    }

    .error-box {
      background: #fff0f0;
      border: 1px solid #ffd0d0;
      color: #a00000;
      padding: 10px 12px;
      border-radius: 8px;
      margin-bottom: 12px;
      font-size: 13px;
      text-align: left;
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
  <div class="reset-container">
    <h1>Reset Password</h1>
    <p class="subtitle">Enter your new password below.</p>

    @if ($errors->any())
      <div class="error-box">
        @foreach ($errors->all() as $error)
          <div>{{ $error }}</div>
        @endforeach
      </div>
    @endif

    <form method="POST" action="{{ route('password.update') }}">
      @csrf
      <input type="hidden" name="token" value="{{ $token }}">

      <div>
        <label for="email">Email</label>
        <input type="email" name="email" id="email" required>
      </div>

      <div>
        <label for="password">New Password</label>
        <input type="password" name="password" id="password" required>
      </div>

      <div>
        <label for="password_confirmation">Confirm Password</label>
        <input type="password" name="password_confirmation" id="password_confirmation" required>
      </div>

      <button type="submit" class="btn">Reset Password</button>
    </form>
  </div>
</body>
</html>
