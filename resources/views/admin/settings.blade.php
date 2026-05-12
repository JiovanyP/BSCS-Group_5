{{-- resources/views/admin/settings.blade.php --}}
@extends('layouts.admin')

@section('title', 'Admin Settings')

@section('content')
@php
  $lastLogin = $lastLogin ?? ($admin->last_login_at ?? null);
@endphp

<style>
/* === THEME SYNC === */
:root {
  --bg-card: #ffffff;
  --accent: #CF0F47;
  --accent-hover: #FF0B55;
  --text-main: #111315;
  --text-muted: #666666;
  --border-light: #eeeeee;
  --input-bg: #f8f9fa;
}

/* Force Poppins */
.settings-container, 
.settings-card, 
.form-input, 
.btn, 
.field-value {
  font-family: 'Poppins', sans-serif !important;
}

/* Settings container */
.settings-container {
  max-width: 800px;
  margin: 0 auto;
  padding-top: 10px;
}

/* Page header */
.settings-header {
  margin-bottom: 30px;
}

.settings-header h1 {
  font-size: 24px;
  font-weight: 700;
  margin: 0;
  color: var(--text-main);
}

.settings-header .subtitle {
  color: var(--text-muted);
  font-size: 14px;
}

/* Alert Success */
.alert-success {
  background: #e8f5e9;
  color: #2e7d32;
  border-radius: 12px;
  padding: 16px 20px;
  font-weight: 600;
  margin-bottom: 24px;
  display: flex;
  align-items: center;
  gap: 12px;
  border: 1px solid #c8e6c9;
}

/* Settings cards */
.settings-cards {
  display: flex;
  flex-direction: column;
  gap: 24px;
}

.settings-card {
  background: var(--bg-card);
  border: 1px solid var(--border-light);
  border-radius: 16px;
  padding: 28px;
  box-shadow: 0 4px 12px rgba(0,0,0,0.03);
  transition: all 0.3s ease;
}

.settings-card:hover {
  box-shadow: 0 8px 24px rgba(0,0,0,0.06);
}

.card-title {
  font-size: 17px;
  font-weight: 700;
  color: var(--text-main);
  margin-bottom: 24px;
  display: flex;
  align-items: center;
  gap: 10px;
}

.card-title .material-icons {
  font-size: 22px;
  color: var(--accent);
}

/* Info fields */
.info-field {
  margin-bottom: 18px;
  padding-bottom: 14px;
  border-bottom: 1px solid #f8f9fa;
}

.info-field:last-child {
  margin-bottom: 0;
  padding-bottom: 0;
  border-bottom: none;
}

.field-label {
  font-size: 11px;
  color: var(--text-muted);
  text-transform: uppercase;
  letter-spacing: 0.8px;
  font-weight: 700;
  margin-bottom: 4px;
}

.field-value {
  font-size: 15px;
  color: var(--text-main);
  font-weight: 500;
}

/* Form elements */
.form-group {
  margin-bottom: 20px;
}

.form-label {
  font-size: 13px;
  color: var(--text-main);
  font-weight: 600;
  margin-bottom: 8px;
  display: block;
}

.form-input {
  width: 100%;
  padding: 12px 16px;
  border-radius: 10px;
  border: 1px solid #ddd;
  background: var(--input-bg);
  color: var(--text-main);
  font-size: 14px;
  transition: all 0.2s;
  box-sizing: border-box;
}

.form-input:focus {
  outline: none;
  border-color: var(--accent);
  background: #fff;
  box-shadow: 0 0 0 4px rgba(207, 15, 71, 0.1);
}

/* Radio buttons */
.radio-group {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 12px;
  margin-top: 12px;
}

.radio-option {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 14px;
  background: #f8f9fa;
  border: 1px solid #eee;
  border-radius: 10px;
  cursor: pointer;
  transition: all 0.2s;
}

.radio-option:has(input:checked) {
  border-color: var(--accent);
  background: #fff;
}

.radio-option input[type="radio"] {
  width: 18px;
  height: 18px;
  accent-color: var(--accent);
}

.radio-option label {
  cursor: pointer;
  color: var(--text-main);
  font-size: 14px;
  font-weight: 600;
  flex: 1;
}

/* Buttons */
.btn-primary {
  width: 100%;
  padding: 12px 24px;
  border-radius: 50px;
  font-size: 14px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
  border: none;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  margin-top: 20px;
  background: var(--accent);
  color: white;
}

.btn-primary:hover {
  background: var(--accent-hover);
  transform: translateY(-2px);
  box-shadow: 0 6px 20px rgba(207, 15, 71, 0.25);
}

/* Error messages */
.error-message {
  color: var(--accent);
  font-size: 12px;
  margin-top: 6px;
  font-weight: 500;
}
</style>

<div class="settings-container">
  <!-- Header -->
  <div class="settings-header">
    <h1>Account Settings</h1>
    <div class="subtitle">Update your administrator credentials and preferences</div>
  </div>

  <!-- Success Alert -->
  @if(session('success'))
    <div class="alert-success">
      <span class="material-icons">check_circle</span>
      {{ session('success') }}
    </div>
  @endif

  <!-- Settings Cards -->
  <div class="settings-cards">
    <!-- Account Information -->
    <div class="settings-card">
      <div class="card-title">
        <span class="material-icons">manage_accounts</span>
        Account Profile
      </div>

      <div class="info-field">
        <div class="field-label">Full Name</div>
        <div class="field-value">{{ $admin->name ?? '—' }}</div>
      </div>

      <div class="info-field">
        <div class="field-label">Email Address</div>
        <div class="field-value">{{ $admin->email ?? '—' }}</div>
      </div>

      <div class="info-field">
        <div class="field-label">Last Session</div>
        <div class="field-value">
          @if($lastLogin)
            {{ \Carbon\Carbon::parse($lastLogin)->format('M d, Y — h:i A') }}
          @else
            No recorded login
          @endif
        </div>
      </div>
    </div>

    <!-- Preferences -->
    <div class="settings-card">
      <div class="card-title">
        <span class="material-icons">palette</span>
        Display Preferences
      </div>

      <form action="{{ route('admin.settings.updateTheme') }}" method="POST">
        @csrf
        <div class="form-label">Active Theme</div>
        
        <div class="radio-group">
          <label class="radio-option" for="dark-mode">
            <input 
              type="radio" 
              id="dark-mode" 
              name="theme" 
              value="dark" 
              {{ ($settings['theme'] ?? 'dark') === 'dark' ? 'checked' : '' }}
            >
            Dark Mode
          </label>
          
          <label class="radio-option" for="light-mode">
            <input 
              type="radio" 
              id="light-mode" 
              name="theme" 
              value="light" 
              {{ ($settings['theme'] ?? 'dark') === 'light' ? 'checked' : '' }}
            >
            Light Mode
          </label>
        </div>

        <button type="submit" class="btn-primary">
          <span class="material-icons" style="font-size: 18px;">save</span>
          Save Theme Preference
        </button>
      </form>
    </div>

    <!-- Change Password -->
    <div class="settings-card">
      <div class="card-title">
        <span class="material-icons">lock_reset</span>
        Security Update
      </div>

      <form action="{{ route('admin.settings.updatePassword') }}" method="POST">
        @csrf
        
        <div class="form-group">
          <label for="current_password" class="form-label">Current Password</label>
          <input 
            type="password" 
            id="current_password" 
            name="current_password" 
            class="form-input" 
            placeholder="Required to authorize changes"
            required
          >
          @error('current_password')
            <div class="error-message">{{ $message }}</div>
          @enderror
        </div>

        <div class="form-group">
          <label for="new_password" class="form-label">New Password</label>
          <input 
            type="password" 
            id="new_password" 
            name="new_password" 
            class="form-input" 
            placeholder="Minimum 8 characters"
            required
          >
          @error('new_password')
            <div class="error-message">{{ $message }}</div>
          @enderror
        </div>

        <div class="form-group">
          <label for="new_password_confirmation" class="form-label">Confirm New Password</label>
          <input 
            type="password" 
            id="new_password_confirmation" 
            name="new_password_confirmation" 
            class="form-input" 
            placeholder="Repeat new password"
            required
          >
        </div>

        <button type="submit" class="btn-primary">
          <span class="material-icons" style="font-size: 18px;">security</span>
          Update Security Credentials
        </button>
      </form>
    </div>
  </div>
</div>
@endsection