@extends('layouts.app')

@section('title', 'Page Not Found')

@section('content')
<div class="error-page" style="text-align: center; margin-top: 100px;">
    <h1 style="font-size: 5rem; font-weight: bold; color: #ff4d4f;">404</h1>
    <h2 style="font-size: 1.5rem; color: #666;">Oops! The page you’re looking for doesn’t exist.</h2>
    <p style="margin-top: 20px;">You can go back to the <a href="{{ route('timeline') }}" style="color: #494ca2; text-decoration: underline;">Timeline</a> or <a href="{{ route('dashboard') }}" style="color: #494ca2; text-decoration: underline;">Dashboard</a>.</p>
</div>
@endsection
