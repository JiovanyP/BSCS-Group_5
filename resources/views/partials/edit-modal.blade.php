{{-- Edit Profile Modal --}}

<div class="modal fade" id="editProfileModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-body" style="padding: 0;">
        <div class="edit-profile-container">
          <h1><strong>Edit Personal Info</strong></h1>
          <div class="subtitle">Update your personal information</div>

          {{-- Success alert --}}
          @if (session('success'))
            <div class="alert alert-success" style="font-size: 14px; margin-bottom: 16px;">
              {{ session('success') }}
            </div>
          @endif

          {{-- Display validation errors --}}
          @if ($errors->any())
            <div class="alert alert-danger" style="font-size: 14px; margin-bottom: 16px;">
              <ul style="margin-bottom: 0;">
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

            <label for="editPhone">Phone Number</label>
            <input type="tel" id="editPhone" name="phone" value="{{ old('phone', Auth::user()->phone ?? '') }}" placeholder="Enter your phone number">

            <label for="editLocation">Location</label>
            <input type="text" id="editLocation" name="location" value="{{ old('location', Auth::user()->location ?? '') }}" placeholder="Enter your location">

            <label for="editBio">Bio</label>
            <textarea id="editBio" name="bio" rows="3" placeholder="Tell us about yourself">{{ old('bio', Auth::user()->bio ?? '') }}</textarea>

            <label for="editAvatar">Profile Picture</label>
            <input type="file" id="editAvatar" name="avatar" accept="image/*">
            <small class="form-text text-muted">Leave empty to keep current avatar. Max 2MB, JPEG/PNG/GIF only.</small>

            <button type="submit" class="btn btn-primary">Save Changes</button>
            <button type="button" class="btn btn-secondary" onclick="$('#editProfileModal').modal('hide')">Cancel</button>
          </form>

          <footer class="small">
            Your information is kept private and secure.
          </footer>
        </div>
      </div>
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

  .edit-profile-container form {
      width: 100%;
      margin-top: 6px;
      text-align: left;
  }

  .edit-profile-container label {
      display: block;
      font-size: 13px;
      color: #444;
      margin-bottom: 6px;
  }

  .edit-profile-container textarea,
  .edit-profile-container input[type="text"],
  .edit-profile-container input[type="email"],
  .edit-profile-container input[type="password"],
  .edit-profile-container input[type="tel"],
  .edit-profile-container input[type="file"] {
      width: 100%;
      padding: 12px;
      border-radius: 10px;
      border: 1px solid #ddd;
      margin-bottom: 12px;
      box-sizing: border-box;
      font-size: 14px;
      background: #fbfbfb;
  }

  .edit-profile-container textarea {
      resize: none;
      min-height: 80px;
  }

  .edit-profile-container textarea:focus,
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
      text-align: center;
      text-decoration: none;
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
</style>

<script>
$(document).ready(function() {
    // Ensure modal is properly cleaned up when hidden
    $('#editProfileModal').on('hidden.bs.modal', function () {
        $('.modal-backdrop').remove();
        $('body').removeClass('modal-open');
        $('body').css('padding-right', '');
    });

    // Close modal after successful submission
    $('#editProfileModal form').on('submit', function() {
        $('#editProfileModal').modal('hide');
    });
});
</script>
