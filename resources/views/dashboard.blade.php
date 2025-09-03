<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-4">
    <h1>Welcome, {{ Auth::user()->name }} 🎉</h1>
    <p>You are logged in successfully.</p>
    <a href="{{ route('timeline') }}" class="btn btn-primary">Go to Timeline</a>
</div>
</body>
</html>
