@section('content')
<div class="error-page" style="text-align: center; margin-top: 100px; font-family: 'Poppins', sans-serif;">
    <h1 style="font-size: 5rem; font-weight: bold; color: #ff4d4f;">404</h1>
    <h2 style="font-size: 1.5rem; color: #666;">Oops! The page you’re looking for doesn’t exist.</h2>
    <p style="margin-top: 20px;">
        {{-- Hardcoded URLs prevent the page from crashing when routes are broken --}}
        You can go back to the <a href="/timeline" style="color: #494ca2; text-decoration: underline;">Timeline</a> 
        or <a href="/dashboard" style="color: #494ca2; text-decoration: underline;">Dashboard</a>.
    </p>
</div>
@endsection