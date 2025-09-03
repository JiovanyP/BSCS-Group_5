<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Register Page">
    <meta name="author" content="">
    <title>Register</title>

    <!-- Custom fonts for this template-->
    <link href="{{ asset('assets/vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: #f0f4f8;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }

        .register-container {
            display: flex;
            width: 500px;
            max-width: 90%;
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .register-left {
            flex: 1;
            padding: 2.5rem 2rem;
            background: #f8fafc;
        }

        .register-right {
            flex: 1;
            background: linear-gradient(135deg, #4facfe 0%, #00b4db 50%, #0083b0 100%);
            padding: 4rem 3rem;
            color: white;
            display: flex;
            flex-direction: column;
            justify-content: center;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .register-right::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
            transform: rotate(45deg);
        }

        .register-right h2 {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 1.5rem;
            position: relative;
            z-index: 2;
        }

        .register-right p {
            font-size: 1.1rem;
            opacity: 0.9;
            line-height: 1.6;
            position: relative;
            z-index: 2;
        }

        .form-title {
            font-size: 1.5rem;
            font-weight: 600;
            color: #2c5282;
            margin-bottom: 0.5rem;
            text-align: center;
        }

        .form-subtitle {
            color: #718096;
            margin-bottom: 2rem;
            font-size: 0.9rem;
            text-align: center;
        }

        .form-group {
            margin-bottom: 1.2rem;
            text-align: left;
        }

        .form-group label {
            font-weight: 500;
            color: #4a5568;
            margin-bottom: 0.5rem;
            display: block;
            font-size: 0.9rem;
        }

        .form-control-modern {
            width: 100%;
            padding: 0.7rem 1rem;
            border: 2px solid #e2e8f0;
            border-radius: 10px;
            font-size: 0.9rem;
            transition: all 0.3s ease;
            background: #f7fafc;
            color: #2d3748;
        }

        .form-control-modern:focus {
            outline: none;
            border-color: #4facfe;
            background: white;
            box-shadow: 0 0 0 3px rgba(79, 172, 254, 0.1);
        }

        .form-control-modern::placeholder {
            color: #a0aec0;
        }

        .btn-register {
            width: 100%;
            padding: 0.8rem;
            background: linear-gradient(135deg, #4facfe 0%, #00b4db 100%);
            border: none;
            border-radius: 10px;
            color: white;
            font-weight: 600;
            font-size: 1rem;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 0.8rem;
            text-transform: none;
        }

        .btn-register:hover {
            transform: translateY(-2px);
            box-shadow: 0 15px 35px rgba(79, 172, 254, 0.4);
        }

        .login-link {
            text-align: center;
            margin-top: 1.5rem;
            color: #718096;
            font-size: 0.9rem;
        }

        .login-link a {
            color: #4facfe;
            text-decoration: none;
            font-weight: 600;
        }

        .login-link a:hover {
            text-decoration: underline;
        }

        @media (max-width: 768px) {
            .register-container {
                flex-direction: column;
                width: 95%;
                max-width: 400px;
            }
            
            .register-left {
                padding: 2.5rem 2rem;
            }

            .register-right {
                padding: 2.5rem 2rem;
                order: -1;
            }

            .register-right h2 {
                font-size: 2rem;
            }
        }
    </style>
</head>

<body>
    <div class="register-container">
        <!-- Left Panel - Form -->
        <div class="register-left">
            <h3 class="form-title">Create an Account</h3>

            {{-- Error messages --}}
            @if ($errors->any())
                <div style="color: red; margin-bottom: 1rem;">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="/register" method="POST">
                @csrf

                <!-- Show validation errors -->
                @if($errors->any())
                    <div style="color: red; margin-bottom: 15px; padding: 10px; background: #ffeeee; border-radius: 5px;">
                        <ul style="list-style: none;">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- Name -->
                <div class="form-group">
                    <label for="name">Full Name</label>
                    <input type="text" id="name" name="name" class="form-control-modern" placeholder="Full Name" value="{{ old('name') }}" required>
                </div>

                <!-- Email -->
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email" class="form-control-modern" placeholder="Email Address" value="{{ old('email') }}" required>
                </div>

                <!-- Location -->
                <div class="form-group">
                    <label for="location">Location</label>
                    <input type="text" id="location" name="location" class="form-control-modern" placeholder="Location" value="{{ old('location') }}" required>
                </div>

                <!-- Password -->
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" class="form-control-modern" placeholder="Password" required>
                </div>

                <!-- Confirm Password -->
                <div class="form-group">
                    <label for="password_confirmation">Confirm Password</label>
                    <input type="password" id="password_confirmation" name="password_confirmation" class="form-control-modern" placeholder="Confirm Password" required>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="btn-register">Register</button>
            </form>

            <!-- Login Link -->
            <div class="login-link">
                Already have an account? <a href="{{ route('login') }}">Login</a>
            </div>
        </div>
    </div>

    <!-- Bootstrap core JavaScript-->
    <script src="{{ asset('assets/vendor/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>

    <!-- Core plugin JavaScript-->
    <script src="{{ asset('assets/vendor/jquery-easing/jquery.easing.min.js') }}"></script>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Success message
            @if(session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: '{{ session('success') }}',
                    confirmButtonColor: '#4facfe',
                    confirmButtonText: 'Back to Login',
                    showCancelButton: true,
                    cancelButtonText: 'Stay Here',
                    cancelButtonColor: '#a0aec0',
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = "{{ route('login') }}";
                    }
                });
            @endif

            // Error messages
            @if($errors->any())
                Swal.fire({
                    icon: 'error',
                    title: 'Registration Failed',
                    html: `@foreach($errors->all() as $error)<p>{{ $error }}</p>@endforeach`,
                    confirmButtonColor: '#4facfe',
                    confirmButtonText: 'OK'
                });
            @endif

            // Button loading state
            const form = document.querySelector('form');
            const submitBtn = form.querySelector('.btn-register');
            
            form.addEventListener('submit', function() {
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Registering...';
                submitBtn.disabled = true;
            });
        });
    </script>
</body>
</html>