{{-- resources/views/admin/posts/create.blade.php --}}
@extends('layouts.admin')

@section('title', 'Create Admin Post')

@section('content')
<style>
:root {
  --accent: #CF0F47;
  --accent-2: #FF0B55;
  --card-bg: #1A1A1B;
  --muted: #98a0a8;
  --input-bg: #272729;
  --border: #343536;
}

.post-container {
  max-width: 600px;
  margin: 0 auto;
  background: var(--card-bg);
  border-radius: 16px;
  padding: 36px;
  box-shadow: 0 12px 40px rgba(0,0,0,0.4);
}

.post-container h1 {
  margin: 0 0 14px 0;
  color: var(--accent);
  font-size: 24px;
  letter-spacing: 0.2px;
}

.subtitle {
  color: var(--muted);
  margin-bottom: 24px;
  font-size: 13px;
}

.post-container label {
  display: block;
  font-size: 13px;
  color: #D7DADC;
  margin-bottom: 8px;
  font-weight: 600;
}

.post-container textarea,
.post-container input[type="text"],
.post-container input[type="file"],
.post-container select {
  width: 100%;
  padding: 12px;
  border-radius: 10px;
  border: 1px solid var(--border);
  margin-bottom: 16px;
  box-sizing: border-box;
  font-size: 14px;
  background: var(--input-bg);
  color: #D7DADC;
  transition: all 0.2s;
}

.post-container textarea {
  resize: vertical;
  min-height: 120px;
  font-family: inherit;
}

.post-container textarea:focus,
.post-container input:focus,
.post-container select:focus {
  border-color: var(--accent);
  background: #1f1f20;
  box-shadow: 0 0 0 3px rgba(207, 15, 71, 0.15);
  outline: none;
}

.post-container select {
  cursor: pointer;
}

.post-container .btn {
  display: block;
  padding: 12px 16px;
  border-radius: 10px;
  border: 0;
  font-weight: 700;
  cursor: pointer;
  font-size: 15px;
  transition: all 0.25s;
  text-align: center;
  text-decoration: none;
}

.post-container .btn-primary {
  width: 100%;
  background: var(--accent);
  color: #fff;
}

.post-container .btn-primary:hover:not(:disabled) {
  background: var(--accent-2);
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(207, 15, 71, 0.4);
}

.post-container .btn-primary:disabled {
  background: #555;
  cursor: not-allowed;
  opacity: 0.5;
}

.post-container .btn-secondary {
  margin-top: 12px;
  width: 100%;
  background: #343536;
  color: #D7DADC;
}

.post-container .btn-secondary:hover {
  background: #3f4041;
}

#preview-container {
  margin-bottom: 16px;
  border-radius: 8px;
  overflow: hidden;
}

#preview-container img,
#preview-container video {
  max-width: 100%;
  max-height: 300px;
  width: 100%;
  object-fit: contain;
  background: #000;
  border-radius: 8px;
}

.file-input-wrapper {
  display: flex;
  gap: 8px;
  align-items: center;
}

.file-input-wrapper input[type="file"] {
  flex: 1;
  margin-bottom: 0;
}

.admin-badge {
  display: inline-block;
  background: rgba(255, 105, 180, 0.15);
  color: #FF69B4;
  padding: 6px 14px;
  border-radius: 20px;
  font-size: 12px;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  margin-bottom: 20px;
}

.alert {
  padding: 12px 16px;
  border-radius: 8px;
  margin-bottom: 20px;
  font-size: 14px;
}

.alert-danger {
  background: rgba(220, 53, 69, 0.15);
  color: #ff6b6b;
  border: 1px solid rgba(220, 53, 69, 0.3);
}

.alert-success {
  background: rgba(40, 167, 69, 0.15);
  color: #5cb85c;
  border: 1px solid rgba(40, 167, 69, 0.3);
}
</style>

<div class="post-container">
  <div class="admin-badge">
    ⭐ Posting as Admin
  </div>

  <h1><strong>Create Admin Post</strong></h1>
  <div class="subtitle">Share important updates and announcements</div>

  @if (session('success'))
    <div class="alert alert-success">
      {{ session('success') }}
    </div>
  @endif

  @if ($errors->any())
    <div class="alert alert-danger">
      <ul style="margin: 0; padding-left: 20px;">
        @foreach ($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <form id="adminPostForm" action="{{ route('admin.posts.store') }}" method="POST" enctype="multipart/form-data">
    @csrf

    {{-- Accident Type --}}
    <label for="accident_type">Incident Type <span style="color:var(--accent);">*</span></label>
    <select id="accident_type" name="accident_type" required>
      <option value="" disabled selected>Select Type</option>
      <option value="Fire" {{ old('accident_type') == 'Fire' ? 'selected' : '' }}>Fire</option>
      <option value="Crime" {{ old('accident_type') == 'Crime' ? 'selected' : '' }}>Crime</option>
      <option value="Traffic" {{ old('accident_type') == 'Traffic' ? 'selected' : '' }}>Traffic</option>
      <option value="Others" {{ old('accident_type') == 'Others' ? 'selected' : '' }}>Others</option>
    </select>
    <input id="other_type" name="other_type" type="text" placeholder="Please specify" value="{{ old('other_type') }}" style="display:none;" />

    {{-- Location --}}
    <label for="location">Location <span style="color:var(--accent);">*</span></label>
    <select id="location" name="location" required>
      <option value="" disabled selected>Select Location</option>
      <option value="Bonifacio" {{ old('location') == 'Bonifacio' ? 'selected' : '' }}>Bonifacio</option>
      <option value="Sinamar 1" {{ old('location') == 'Sinamar 1' ? 'selected' : '' }}>Sinamar 1</option>
      <option value="Sinamar 2" {{ old('location') == 'Sinamar 2' ? 'selected' : '' }}>Sinamar 2</option>
      <option value="Guiang" {{ old('location') == 'Guiang' ? 'selected' : '' }}>Guiang</option>
      <option value="Avenue" {{ old('location') == 'Avenue' ? 'selected' : '' }}>Avenue</option>
      <option value="Sunset" {{ old('location') == 'Sunset' ? 'selected' : '' }}>Sunset</option>
      <option value="Sunrise" {{ old('location') == 'Sunrise' ? 'selected' : '' }}>Sunrise</option>
      <option value="Villanueva" {{ old('location') == 'Villanueva' ? 'selected' : '' }}>Villanueva</option>
      <option value="Abellera" {{ old('location') == 'Abellera' ? 'selected' : '' }}>Abellera</option>
      <option value="Miracle" {{ old('location') == 'Miracle' ? 'selected' : '' }}>Miracle</option>
      <option value="Others" {{ old('location') == 'Others' ? 'selected' : '' }}>Others</option>
    </select>
    <input id="other_location" name="other_location" type="text" placeholder="Please specify" value="{{ old('other_location') }}" style="display:none;" />

    {{-- Content --}}
    <label for="content">Post Content <span style="color:var(--accent);">*</span></label>
    <textarea id="content" name="content" rows="6" placeholder="Write your announcement or update..." required>{{ old('content') }}</textarea>

    {{-- Image/Video --}}
    <label for="image">Attach Image/Video (Optional)</label>
    <div class="file-input-wrapper">
      <input id="image" name="image" type="file" accept="image/*,video/*,image/gif" />
    </div>

    <div id="preview-container" style="display:none;">
      <img id="preview-image" src="" alt="Preview" style="display:none;" />
      <video id="preview-video" controls style="display:none;"></video>
    </div>

    <button id="postBtn" type="submit" class="btn btn-primary">
      Publish Post
    </button>
    <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">Cancel</a>
  </form>
</div>

<script>
(function(){
  const form = document.getElementById('adminPostForm');
  const postBtn = document.getElementById('postBtn');
  const content = document.getElementById('content');
  const accidentType = document.getElementById('accident_type');
  const otherType = document.getElementById('other_type');
  const location = document.getElementById('location');
  const otherLocation = document.getElementById('other_location');
  const imageInput = document.getElementById('image');
  const previewContainer = document.getElementById('preview-container');
  const previewImage = document.getElementById('preview-image');
  const previewVideo = document.getElementById('preview-video');

  // Check if "Others" was selected on page load (for old values)
  if (accidentType.value === "Others") {
    otherType.style.display = "block";
    otherType.required = true;
  }
  if (location.value === "Others") {
    otherLocation.style.display = "block";
    otherLocation.required = true;
  }

  // Toggle submit button
  function toggleButton(){
    const contentValid = content.value.trim().length > 0;
    const typeValid = accidentType.value && (accidentType.value !== "Others" || otherType.value.trim());
    const locationValid = location.value && (location.value !== "Others" || otherLocation.value.trim());
    
    postBtn.disabled = !(contentValid && typeValid && locationValid);
  }

  // Listen to all inputs
  [content, accidentType, otherType, location, otherLocation].forEach(el => {
    el.addEventListener('input', toggleButton);
    el.addEventListener('change', toggleButton);
  });

  // Handle "Others" option
  function handleOther(selectEl, inputEl){
    selectEl.addEventListener('change', function(){
      if(this.value === "Others"){
        inputEl.style.display = "block";
        inputEl.required = true;
      } else {
        inputEl.style.display = "none";
        inputEl.value = "";
        inputEl.required = false;
      }
      toggleButton();
    });
  }

  handleOther(accidentType, otherType);
  handleOther(location, otherLocation);

  // Image preview
  imageInput.addEventListener('change', function(e){
    const file = e.target.files[0];
    if (!file) {
      previewContainer.style.display = 'none';
      return;
    }

    const reader = new FileReader();
    reader.onload = function(event){
      previewContainer.style.display = 'block';
      
      if (file.type.startsWith('image/')) {
        previewImage.src = event.target.result;
        previewImage.style.display = 'block';
        previewVideo.style.display = 'none';
      } else if (file.type.startsWith('video/')) {
        previewVideo.src = event.target.result;
        previewVideo.style.display = 'block';
        previewImage.style.display = 'none';
      }
    };
    reader.readAsDataURL(file);
  });

  // Form submit - show loading state
  form.addEventListener('submit', function(e){
    // Don't prevent default - let form submit normally
    postBtn.disabled = true;
    postBtn.textContent = 'Publishing...';
  });

  // Initial check
  toggleButton();
})();
</script>
@endsection