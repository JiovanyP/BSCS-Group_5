<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login</title>
  <style>
    body {
      margin: 0;
      height: 100vh;
      background: linear-gradient(135deg, #000000, #CF0F47, #FF0B55);
      background-size: 400% 400%;
      display: flex;
      justify-content: center;
      align-items: center;
      font-family: Arial, sans-serif;
    }

    .login-container {
      background: #ffffff; /* White card */
      border-radius: 20px;
      padding: 40px;
      width: 380px;
      box-shadow: 0 8px 30px rgba(0, 0, 0, 0.3);
      text-align: center; /* Center text and inline elements */
    }

    .login-container h2 {
      color: #CF0F47;
      margin-bottom: 20px;
    }

    .social-btn {
      width: 90%; /* narrower for clean centered look */
      padding: 12px;
      border: 1px solid #ccc;
      border-radius: 12px;
      font-weight: bold;
      cursor: pointer;
      margin: 10px auto;
      background: white;
      color: #333;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 10px;
    }

    .divider {
      display: flex;
      align-items: center;
      text-align: center;
      margin: 20px 0;
      color: #666;
    }

    .divider::before, .divider::after {
      content: "";
      flex: 1;
      border-bottom: 1px solid #ccc;
    }
    .divider:not(:empty)::before {
      margin-right: 0.75em;
    }
    .divider:not(:empty)::after {
      margin-left: 0.75em;
    }

    .input-field {
      width: 90%;
      padding: 12px;
      margin: 10px auto;
      border: 1px solid #ccc;
      border-radius: 12px;
      outline: none;
      background: #f9f9f9;
      color: #333;
      display: block;
    }

    .login-btn {
      width: 90%;
      padding: 12px;
      border: none;
      border-radius: 12px;
      background: #CF0F47;
      color: white;
      font-weight: bold;
      cursor: pointer;
      transition: 0.3s;
      margin: 10px auto;
      display: block;
    }

    .login-btn:hover {
      background: #FF0B55;
    }

    .link {
      display: block;
      margin-top: 10px;
      font-size: 14px;
      color: #CF0F47;
      text-decoration: none;
    }

    .link:hover {
      text-decoration: underline;
    }
  </style>
</head>
<body>
  <div class="login-container">
    <h2>Log In</h2>

    <!-- Google login -->
    <a href="{{ route('google.login') }}" class="social-btn">🔴 Continue with Google</a>

    <div class="divider">OR</div>

    <form method="POST" action="{{ route('login.post') }}">
    @csrf
    
    <!-- Error messages -->
    @if($errors->any())
        <div style="color: red; margin-bottom: 15px; font-size: 14px; background: #ffe6e6; padding: 10px; border-radius: 8px; border: 1px solid red;">
            {{ $errors->first() }}
        </div>
    @endif
    
    <input type="text" name="email" placeholder="Email or Username" class="input-field" 
           value="{{ old('email') }}" required>
    <input type="password" name="password" placeholder="Password" class="input-field" required>
    <button type="submit" class="login-btn">Log In</button>
</form>

    <!-- Links -->
    <a href="#" class="link">Forgot password?</a>
    <a href="{{ route('register') }}" class="link">New here? Sign Up</a>
  </div>
</body>
</html>
