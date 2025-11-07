@extends('layouts.admin')

@section('content')
@php
  $lastLogin = $lastLogin ?? ($admin->last_login_at ?? null);
@endphp

<style>
:root {
  --bg-primary: #0B1416;
  --bg-secondary: #1A1A1B;
  --bg-hover: #272729;
  --border: #343536;
  --text-primary: #D7DADC;
  --text-secondary: #818384;
  --accent-red: #FF0558;
  --accent-green: #46D160;
  --card-radius: 8px;
}

body {
  background: var(--bg-primary);
  color: var(--text-primary);
}

/* Settings container - centered */
.settings-container {
  max-width: 800px;
  margin: 0 auto;
  padding: 20px;
}

/* Page header */
.settings-header {
  text-align: center;
  margin-bottom: 32px;
}

.settings-header h1 {
  font-size: 28px;
  font-weight: 500;
  margin: 0 0 8px 0;
  color: var(--text-primary);
}

.settings-header .subtitle {
  color: var(--text-secondary);
  font-size: 14px;
}

/* Alert */
.alert-success {
  background: linear-gradient(135deg, #46D160, #3CB54A);
  color: white;
  border-radius: var(--card-radius);
  padding: 16px 20px;
  font-weight: 600;
  box-shadow: 0 8px 24px rgba(70, 209, 96, 0.3);
  margin-bottom: 24px;
  display: flex;
  align-items: center;
  gap: 12px;
}

.alert-success::before {
  content: '✓';
  display: flex;
  align-items: center;
  justify-content: center;
  width: 24px;
  height: 24px;
  border-radius: 50%;
  background: rgba(255, 255, 255, 0.2);
  font-weight: 700;
}

/* Settings cards */
.settings-cards {
  display: flex;
  flex-direction: column;
  gap: 20px;
}

.settings-card {
  background: var(--bg-secondary);
  border: 1px solid var(--border);
  border-left: 3px solid var(--accent-red);
  border-radius: var(--card-radius);
  padding: 24px;
  transition: all 0.2s;
}

.settings-card:hover {
  background: var(--bg-hover);
  border-color: var(--text-secondary);
  transform: translateY(-2px);
  box-shadow: 0 8px 24px rgba(255, 5, 88, 0.2);
}

.card-title {
  font-size: 18px;
  font-weight: 600;
  color: var(--text-primary);
  margin-bottom: 20px;
  display: flex;
  align-items: center;
  gap: 8px;
}

.card-title .material-icons {
  font-size: 20px;
  color: var(--accent-red);
}

/* Info fields */
.info-field {
  margin-bottom: 16px;
}

.info-field:last-child {
  margin-bottom: 0;
}

.field-label {
  font-size: 12px;
  color: var(--text-secondary);
  text-transform: uppercase;
  letter-spacing: 0.5px;
  font-weight: 600;
  margin-bottom: 6px;
}

.field-value {
  font-size: 15px;
  color: var(--text-primary);
  font-weight: 500;
}

/* Form elements */
.form-group {
  margin-bottom: 20px;
}

.form-group:last-of-type {
  margin-bottom: 0;
}

.form-label {
  font-size: 13px;
  color: var(--text-secondary);
  font-weight: 600;
  margin-bottom: 8px;
  display: block;
}

.form-input {
  width: 100%;
  padding: 12px 16px;
  border-radius: var(--card-radius);
  border: 1px solid var(--border);
  background: var(--bg-primary);
  color: var(--text-primary);
  font-size: 14px;
  transition: all 0.2s;
  box-sizing: border-box;
}

.form-input:focus {
  outline: none;
  border-color: var(--accent-red);
  box-shadow: 0 0 0 3px rgba(255, 5, 88, 0.1);
}

.form-input::placeholder {
  color: var(--text-secondary);
  opacity: 0.5;
}

/* Radio buttons */
.radio-group {
  display: flex;
  flex-direction: column;
  gap: 12px;
  margin-top: 12px;
}

.radio-option {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 12px 16px;
  background: rgba(255, 255, 255, 0.03);
  border-radius: var(--card-radius);
  cursor: pointer;
  transition: all 0.2s;
}

.radio-option:hover {
  background: rgba(255, 255, 255, 0.05);
}

.radio-option input[type="radio"] {
  width: 18px;
  height: 18px;
  cursor: pointer;
  accent-color: var(--accent-red);
}

.radio-option label {
  cursor: pointer;
  color: var(--text-primary);
  font-size: 14px;
  font-weight: 500;
  flex: 1;
}

/* Buttons */
.btn {
  width: 100%;
  padding: 12px 24px;
  border-radius: var(--card-radius);
  font-size: 14px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s;
  border: none;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  margin-top: 20px;
}

.btn-primary {
  background: linear-gradient(135deg, var(--accent-red), #D10447);
  color: white;
}

.btn-primary:hover {
  background: linear-gradient(135deg, #FF1E6B, var(--accent-red));
  box-shadow: 0 8px 24px rgba(255, 5, 88, 0.4);
  transform: translateY(-2px);
}

.btn-primary:active {
  transform: translateY(0);
}

/* Error messages */
.error-message {
  color: #FF6B6B;
  font-size: 12px;
  margin-top: 6px;
  display: flex;
  align-items: center;
  gap: 6px;
}

.error-message::before {
  content: '⚠';
  font-size: 14px;
}

/* Responsive */
@media (max-width: 768px) {
  .settings-container {
    padding: 16px;
  }

  .settings-header h1 {
    font-size: 24px;
  }

  .settings-card {
    padding: 20px;
  }

  .card-title {
    font-size: 16px;
  }
}

@media (max-width: 480px) {
  .settings-container {
    padding: 12px;
  }

  .settings-card {
    padding: 16px;
  }

  .settings-header h1 {
    font-size: 22px;
  }
}
</style>

<div class="settings-container">
  <!-- Header -->
  <div class="settings-header">
    <h1>Settings</h1>
    <div class="subtitle">Manage your admin account and preferences</div>
  </div>

  <!-- Success Alert -->
  @if(session('success'))
    <div class="alert-success">
      {{ session('success') }}
    </div>
  @endif

  <!-- Settings Cards -->
  <div class="settings-cards">
    <!-- Account Information -->
    <div class="settings-card">
      <div class="card-title">
        <span class="material-icons">account_circle</span>
        Account Information
      </div>

      <div class="info-field">
        <div class="field-label">Name</div>
        <div class="field-value">{{ $admin->name ?? '—' }}</div>
      </div>

      <div class="info-field">
        <div class="field-label">Email</div>
        <div class="field-value">{{ $admin->email ?? '—' }}</div>
      </div>

      <div class="info-field">
        <div class="field-label">Last Login</div>
        <div class="field-value">
          @if($lastLogin)
            {{ \Carbon\Carbon::parse($lastLogin)->format('M d, Y h:i A') }}
          @else
            —
          @endif
        </div>
      </div>
    </div>

    <!-- Preferences -->
    <div class="settings-card">
      <div class="card-title">
        <span class="material-icons">palette</span>
        Preferences
      </div>

      <form action="{{ route('admin.settings.updateTheme') }}" method="POST">
        @csrf
        <div class="form-label">Theme</div>
        
        <div class="radio-group">
          <div class="radio-option">
            <input 
              type="radio" 
              id="dark-mode" 
              name="theme" 
              value="dark" 
              {{ ($settings['theme'] ?? 'dark') === 'dark' ? 'checked' : '' }}
            >
            <label for="dark-mode">Dark Mode</label>
          </div>
          
          <div class="radio-option">
            <input 
              type="radio" 
              id="light-mode" 
              name="theme" 
              value="light" 
              {{ ($settings['theme'] ?? 'dark') === 'light' ? 'checked' : '' }}
            >
            <label for="light-mode">Light Mode</label>
          </div>
        </div>

        <button type="submit" class="btn btn-primary">
          <span class="material-icons" style="font-size: 18px;">check</span>
          Save Preference
        </button>
      </form>
    </div>

    <!-- Change Password -->
    <div class="settings-card">
      <div class="card-title">
        <span class="material-icons">lock</span>
        Change Password
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
            placeholder="Enter your current password"
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
            placeholder="Enter your new password"
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
            placeholder="Confirm your new password"
            required
          >
        </div>

        <button type="submit" class="btn btn-primary">
          <span class="material-icons" style="font-size: 18px;">vpn_key</span>
          Update Password
        </button>
      </form>
    </div>
  </div>
</div>
@endsection
