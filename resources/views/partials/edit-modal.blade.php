@extends('layouts.app')

@section('title', 'Edit Profile')

@section('content')
<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
<link href="https://fonts.googleapis.com/icon?family=Material+Icons+Outlined" rel="stylesheet">

<div class="main-content py-4">
    <div class="container">
        <div class="edit-profile-container">

            <h1><strong>Edit Personal Info</strong></h1>
            <div class="subtitle">Update your personal information</div>

            {{-- Success alert --}}
            @if (session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            {{-- Display validation errors --}}
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" novalidate>
                @csrf
                @method('PATCH')

                <label for="editName">Full Name (Required)</label>
                <input type="text" id="editName" name="name" value="{{ old('name', Auth::user()->name) }}" required>

                <label for="editEmail">Email (Required)</label>
                <input type="email" id="editEmail" name="email" value="{{ old('email', Auth::user()->email) }}" required>

                <label for="editPassword">New Password</label>
                <input type="password" id="editPassword" name="password" placeholder="Leave empty to keep current password">

                <label for="editPasswordConfirmation">Confirm New Password</label>
                <input type="password" id="editPasswordConfirmation" name="password_confirmation" placeholder="Confirm new password">
                <small class="form-text text-muted">Leave both password fields empty to keep your current password.</small>

                <label for="editLocation">Location</label>
                <input type="text" id="editLocation" name="location" value="{{ old('location', Auth::user()->location ?? '') }}" placeholder="Enter your location">

                <label for="editAvatar">Profile Picture</label>
                <input type="file" id="editAvatar" name="avatar" accept="image/*">
                <small class="form-text text-muted">Leave empty to keep current avatar. Max 2MB, JPEG/PNG/GIF only.</small>

                {{-- Image Preview --}}
                <div id="avatarPreviewContainer" style="margin: 30px; display:flex; justify-content:center;">
                    <img id="avatarPreview" 
                        src="{{ Auth::user()->avatar_url ?? asset('images/avatar.png') }}" 
                        alt="Avatar Preview" 
                        style="width:120px; height:120px; object-fit:cover; border-radius:50%; border:2px solid #ddd; box-shadow:0 4px 12px rgba(0,0,0,0.1); transition:0.3s;">
                </div>

                <button type="submit" class="btn btn-primary">Save Changes</button>
                <a href="{{ route('timeline') }}" class="btn btn-secondary">Cancel</a>
            </form>

            <footer class="small">
                Your information is kept private and secure.
            </footer>
        </div>
    </div>
</div>

<style>
:root {
    --accent: #CF0F47;
    --accent-2: #FF0B55;
    --card-bg: #ffffff;
    --muted: #666;
}

.main-content {
    background-color: #fafafa;
    min-height: 80vh;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 40px 0;
}

.edit-profile-container {
    width: 460px;
    max-width: calc(100% - 40px);
    background: var(--card-bg);
    border-radius: 16px;
    padding: 36px;
    box-shadow: 0 12px 40px rgba(0,0,0,0.1);
    margin: 0 auto;
}

.edit-profile-container h1 {
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

.edit-profile-container label {
    display: block;
    font-size: 13px;
    color: #444;
    margin-bottom: 6px;
}

.edit-profile-container input[type="text"],
.edit-profile-container input[type="email"],
.edit-profile-container input[type="password"],
.edit-profile-container input[type="file"] {
    width: 100%;
    padding: 12px;
    border-radius: 10px;
    border: 1px solid #ddd;
    margin-bottom: 12px;
    font-size: 14px;
    background: #fbfbfb;
    box-sizing: border-box;
}

.edit-profile-container input:focus {
    border-color: var(--accent);
    background: #fff;
    box-shadow: 0 0 0 3px rgba(207, 15, 71, 0.1);
    outline: none;
}

.edit-profile-container .btn {
    display: block;
    padding: 12px 14px;
    border-radius: 10px;
    border: 0;
    font-weight: 700;
    cursor: pointer;
    font-size: 15px;
    transition: 0.25s;
    margin-bottom: 10px;
}

.edit-profile-container .btn-primary {
    width: 100%;
    background: var(--accent);
    color: #fff;
}

.edit-profile-container .btn-primary:hover {
    background: var(--accent-2);
}

.edit-profile-container .btn-secondary {
    width: 100%;
    background: #eee;
    color: #444;
    text-align: center;
    display: inline-block;
}

.edit-profile-container .btn-secondary:hover {
    background: #ddd;
}

.edit-profile-container footer.small {
    margin-top: 18px;
    color: #888;
    font-size: 12px;
    text-align: center;
}

.form-text {
    font-size: 12px;
    color: #888;
    margin-top: -8px;
    margin-bottom: 12px;
    display: block;
}

.alert-success {
    background-color: #e8f5e9;
    color: #2e7d32;
    border: 1px solid #a5d6a7;
    border-radius: 8px;
    padding: 10px 14px;
}

.alert-danger {
    background-color: #fcebea;
    color: #b71c1c;
    border: 1px solid #f5c6cb;
    border-radius: 8px;
    padding: 10px 14px;
    margin-bottom: 12px;
}
</style>

<script>
document.getElementById('editAvatar').addEventListener('change', function(event) {
    const preview = document.getElementById('avatarPreview');
    const file = event.target.files[0];

    if(file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.src = e.target.result;
        }
        reader.readAsDataURL(file);
    } else {
        // Reset to original avatar if no file selected
        preview.src = "{{ Auth::user()->avatar_url ?? asset('images/avatar.png') }}";
    }
});
</script>

@endsection