@extends('layouts.app')

@section('title', 'Edit Report')

@section('content')

<div class="post-container" role="main" aria-labelledby="editPostTitle">
    <h1 id="editPostTitle"><strong>Edit Report</strong></h1>
    <div class="subtitle">Modify your report details or replace the attached media</div>

    <form id="update-post-form" action="{{ route('posts.update', $post) }}" method="POST" enctype="multipart/form-data" novalidate>
        @csrf
        @method('PUT')

        <label for="accident_type">Accident Type (Required)</label>
        <select id="accident_type" name="accident_type" required>
            <option value="" disabled>Select Type</option>
            <option value="Fire" {{ old('accident_type', $post->accident_type) == 'Fire' ? 'selected' : '' }}>Fire</option>
            <option value="Crime" {{ old('accident_type', $post->accident_type) == 'Crime' ? 'selected' : '' }}>Crime</option>
            <option value="Traffic" {{ old('accident_type', $post->accident_type) == 'Traffic' ? 'selected' : '' }}>Traffic</option>
            <option value="Others" {{ old('accident_type', $post->accident_type) == 'Others' ? 'selected' : '' }}>Others</option>
        </select>

        <input id="other_type" name="other_type" type="text" placeholder="Please specify"
               value="{{ old('other_type', $post->other_type) }}"
               style="{{ old('accident_type', $post->accident_type) == 'Others' ? '' : 'display:none;' }}" />

        <label for="content">Your Report (Required)</label>
        <textarea id="content" name="content" rows="4" required>{{ old('content', $post->content) }}</textarea>

        <label for="location">Location (Required)</label>
        <select id="location" name="location" required>
            <option value="" disabled>Select Location</option>
            @php
                $locations = ['Bonifacio','Sinamar 1','Sinamar 2','Guiang','Avenue','Sunset','Sunrise','Villanueva','Abellera','Miracle','Others'];
            @endphp
            @foreach($locations as $loc)
                <option value="{{ $loc }}" {{ old('location', $post->location) == $loc ? 'selected' : '' }}>{{ $loc }}</option>
            @endforeach
        </select>

        <input id="other_location" name="other_location" type="text" placeholder="Please specify"
               value="{{ old('other_location', $post->other_location) }}"
               style="{{ old('location', $post->location) == 'Others' ? '' : 'display:none;' }}" />

        <label for="image">Replace Image/Video (optional)</label>
        <div style="display: flex; gap: 8px; margin-bottom: 12px;">
            <input id="image" name="image" type="file" accept="image/*,video/*,image/gif" style="flex: 1; margin-bottom: 0;" />
            <button type="button" id="cameraBtn" class="btn-camera" style="width: auto; padding: 12px 16px; background: #eee; color: #444; margin: 0;">📷</button>
        </div>

        <input type="hidden" id="media_type_input" name="media_type" value="{{ $post->media_type ?? '' }}">

        <div id="preview-container" style="margin-bottom:12px; {{ $post->image ? '' : 'display:none;' }}">
            @if($post->image)
                <label>Current Media</label>
                @if($post->media_type === 'video')
                    <video controls style="max-width:100%; max-height:200px; border-radius:8px;">
                        <source src="{{ asset('storage/' . $post->image) }}" type="video/{{ pathinfo($post->image, PATHINFO_EXTENSION) }}">
                    </video>
                @else
                    <img src="{{ asset('storage/' . $post->image) }}" alt="Current Image" style="max-width:100%; max-height:200px; border-radius:8px; object-fit:cover;">
                @endif
            @endif
            <img id="preview-image" src="" alt="Preview" style="display:none;" />
            <video id="preview-video" controls style="display:none;"></video>
            <video id="camera-preview" autoplay playsinline style="display:none; max-width: 100%; max-height: 200px; margin-top: 10px; border-radius: 8px;"></video>
            <canvas id="camera-canvas" style="display:none;"></canvas>
            <div id="camera-controls" style="display:none; margin-top: 10px;">
                <button type="button" id="captureBtn" class="camera-btn capture">Capture</button>
                <button type="button" id="cancelCameraBtn" class="camera-btn">Cancel</button>
            </div>
            <button type="button" id="removePreviewBtn">Remove</button>
        </div>

        <div class="btn-group">
            <button type="submit" class="btn btn-primary">Update Post</button>
            <button type="button" class="btn btn-secondary" onclick="window.history.back()">Cancel</button>
        </div>
    </form>

    <form id="delete-post-form" action="{{ route('posts.destroy', $post) }}" method="POST"
          onsubmit="return confirm('Delete this post? This cannot be undone.');" style="margin-top: 16px;">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-danger">Delete Post</button>
    </form>
</div>

<style>
    :root {
        --accent: #CF0F47;
        --accent-2: #FF0B55;
        --card-bg: #ffffff;
        --muted: #666;
    }

    .post-container {
        width: 460px;
        max-width: calc(100% - 40px);
        background: var(--card-bg);
        border-radius: 16px;
        padding: 36px;
        box-shadow: 0 12px 40px rgba(0,0,0,0.1);
        margin: 0 auto;
    }

    .post-container h1 {
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

    .post-container label {
        display: block;
        font-size: 13px;
        color: #444;
        margin-bottom: 6px;
    }

    .post-container textarea,
    .post-container input[type="text"],
    .post-container input[type="file"],
    .post-container select {
        width: 100%;
        padding: 12px;
        border-radius: 10px;
        border: 1px solid #ddd;
        margin-bottom: 12px;
        box-sizing: border-box;
        font-size: 14px;
        background: #fbfbfb;
    }

    .post-container textarea {
        resize: none;
        min-height: 100px;
    }

    .post-container textarea:focus,
    .post-container input:focus,
    .post-container select:focus {
        border-color: var(--accent);
        background: #fff;
        box-shadow: 0 0 0 3px rgba(207, 15, 71, 0.1);
        outline: none;
    }

    .btn-group {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .post-container .btn {
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
    }

    .post-container .btn-primary {
        background: var(--accent);
        color: #fff;
    }

    .post-container .btn-primary:hover {
        background: var(--accent-2);
    }

    .post-container .btn-secondary {
        background: #eee;
        color: #444;
    }

    .post-container .btn-secondary:hover {
        background: #ddd;
    }

    .post-container .btn-danger {
        width: 100%;
        background: #e74c3c;
        color: #fff;
        border: none;
    }

    .post-container .btn-danger:hover {
        background: #c0392b;
    }

    #preview-container img,
    #preview-container video {
        max-width: 100%;
        max-height: 200px;
        margin-top: 10px;
        border-radius: 8px;
        object-fit: cover;
    }

    #removePreviewBtn,
    .camera-btn {
        display: inline-block;
        margin-top: 8px;
        padding: 6px 12px;
        background: #eee;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        font-size: 13px;
        color: #444;
    }

    #removePreviewBtn:hover,
    .camera-btn:hover {
        background: #ddd;
    }

    .camera-btn.capture {
        background: var(--accent);
        color: #fff;
        margin-right: 8px;
    }

    .camera-btn.capture:hover {
        background: var(--accent-2);
    }

    .btn-camera {
        border: none;
        border-radius: 6px;
        cursor: pointer;
    }
</style>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const accidentType = document.getElementById("accident_type");
    const otherType = document.getElementById("other_type");
    const location = document.getElementById("location");
    const otherLocation = document.getElementById("other_location");

    function toggleOtherType() {
        if (accidentType.value === "Others") {
            otherType.style.display = "block";
        } else {
            otherType.style.display = "none";
            otherType.value = "";
        }
    }

    function toggleOtherLocation() {
        if (location.value === "Others") {
            otherLocation.style.display = "block";
        } else {
            otherLocation.style.display = "none";
            otherLocation.value = "";
        }
    }

    // Run once on load
    toggleOtherType();
    toggleOtherLocation();

    // Listen for changes
    accidentType.addEventListener("change", toggleOtherType);
    location.addEventListener("change", toggleOtherLocation);
});
</script>

@endsection
