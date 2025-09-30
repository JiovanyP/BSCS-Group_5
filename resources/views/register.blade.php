<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Register Page">
    <meta name="author" content="">
    <title>Register</title>

    <!-- Custom fonts -->
    <link href="{{ asset('assets/vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        body {
            margin: 0;
            height: 100vh;
            background: linear-gradient(135deg, #000000, #CF0F47, #FF0B55);
            background-size: 400% 400%;
            display: flex;
            justify-content: center;
            align-items: center;
            font-family: 'Poppins', sans-serif;
        }

        .register-container {
            background: #ffffff;
            border-radius: 20px;
            padding: 40px;
            width: 380px;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.3);
            text-align: center;
        }

        .register-container h2 {
            color: #CF0F47;
            margin-bottom: 20px;
            font-weight: 600;
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

        .input-field:focus {
            border-color: #CF0F47;
            background: #fff;
            box-shadow: 0 0 0 3px rgba(207, 15, 71, 0.1);
        }

        .register-btn {
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

        .register-btn:hover {
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

        .error-message {
            color: red;
            margin-bottom: 15px;
            padding: 10px;
            background: #ffeeee;
            border-radius: 5px;
            text-align: left;
        }

        .error-message ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }
    </style>
</head>

<body>
    <div class="register-container">
        <h2>Create an Account</h2>

        {{-- Error messages --}}
        @if ($errors->any())
            <div class="error-message">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('register.post') }}" method="POST">
            @csrf

            <!-- Name -->
            <input type="text" id="name" name="name" class="input-field" placeholder="Full Name"
                value="{{ old('name') }}" required>

            <!-- Email -->
            <input type="email" id="email" name="email" class="input-field" placeholder="Email Address"
                value="{{ old('email') }}" required>

            <!-- Location -->
            <input type="text" id="location" name="location" class="input-field" placeholder="Location"
                value="{{ old('location') }}" required>

            <!-- Password -->
            <input type="password" id="password" name="password" class="input-field" placeholder="Password" required>

            <!-- Confirm Password -->
            <input type="password" id="password_confirmation" name="password_confirmation" class="input-field"
                placeholder="Confirm Password" required>

            <!-- Submit Button -->
            <button type="submit" class="register-btn">Register</button>
        </form>

        <!-- Login Link -->
        <a href="{{ route('login') }}" class="link">Already have an account? Log In</a>
    </div>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</body>

</html>
