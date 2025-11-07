@extends('layouts.app')

@section('title', 'Publish Report')

@section('content')
<div class="post-container" role="main" aria-labelledby="createPostTitle">
    <h1 id="createPostTitle"><strong>Publish a Report</strong></h1>
    <div class="subtitle">Create reports with texts, images, or videos</div>

    <form id="postForm" action="{{ route('timeline.store') }}" method="POST" enctype="multipart/form-data" novalidate>
        @csrf

        {{-- Accident Type --}}
        <label for="accident_type">Accident Type (Required)</label>
        <select id="accident_type" name="accident_type" required>
            <option value="" disabled selected>Select Type</option>
            <option value="Fire">Fire</option>
            <option value="Crime">Crime</option>
            <option value="Traffic">Traffic</option>
            <option value="Others">Others</option>
        </select>
        <input id="other_type" type="text" placeholder="Please specify" style="display:none;" />

        {{-- Location --}}
        <label for="location">Location (Required)</label>
        <select id="location" name="location" required>
            <option value="" disabled selected>Select Location</option>
            <option value="Bonifacio">Bonifacio</option>
            <option value="Sinamar 1">Sinamar 1</option>
            <option value="Sinamar 2">Sinamar 2</option>
            <option value="Guiang">Guiang</option>
            <option value="Avenue">Avenue</option>
            <option value="Sunset">Sunset</option>
            <option value="Sunrise">Sunrise</option>
            <option value="Villanueva">Villanueva</option>
            <option value="Abellera">Abellera</option>
            <option value="Miracle">Miracle</option>
            <option value="Others">Others</option>
        </select>
        <input id="other_location" type="text" placeholder="Please specify" style="display:none;" />

        {{-- Hidden fields to save final values --}}
        <input type="hidden" name="final_accident_type" id="final_accident_type">
        <input type="hidden" name="final_location" id="final_location">

        {{-- Report Content --}}
        <label for="content">Your Report (Required)</label>
        <textarea id="content" name="content" rows="4" placeholder="What's up?" required></textarea>

        {{-- Image/Video --}}
        <label for="image">Attach Image/Video (optional)</label>
        <div style="display: flex; gap: 8px; margin-bottom: 12px;">
            <input id="image" name="image" type="file" accept="image/*,video/*,image/gif" style="flex:1;" />
            <button type="button" id="cameraBtn" class="btn-camera">📷</button>
        </div>

        <input type="hidden" id="media_type_input" name="media_type" value=""> 

        <div id="preview-container" style="display:none; margin-bottom:12px;">
            <img id="preview-image" src="" alt="Preview" style="display:none;" />
            <video id="preview-video" controls style="display:none;"></video>
        </div>

        <button id="postBtn" type="submit" class="btn btn-primary" disabled>Post</button>
        <a href="{{ route('timeline') }}" class="btn btn-secondary">Cancel</a>
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

    .post-container form {
        width: 100%;
        margin-top: 6px;
        text-align: left;
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

    .post-container a.btn {
        display: block;
        text-decoration: none;
        box-sizing: border-box;
    }

    .post-container .btn-primary {
        width: 100%;
        background: var(--accent);
        color: #fff;
    }

    .post-container .btn-primary:hover {
        background: var(--accent-2);
    }

    .post-container .btn-secondary {
        margin-top: 10px;
        width: 100%;
        background: #eee;
        color: #444;
    }

    .post-container .btn-secondary:hover {
        background: #ddd;
    }

    .post-container select {
        color: #888;
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

    #other_location {
        width: 100%;
        padding: 12px;
        border-radius: 10px;
        border: 1px solid #ddd;
        margin-bottom: 12px;
        box-sizing: border-box;
        font-size: 14px;
        background: #fbfbfb;
    }

    .post-container footer.small {
        margin-top: 18px;
        color: #888;
        font-size: 12px;
    }
</style>

<script>
(function(){
    const form = document.getElementById('postForm');
    const postBtn = document.getElementById('postBtn');

    const content = document.getElementById('content');
    const accidentType = document.getElementById('accident_type');
    const otherType = document.getElementById('other_type');
    const location = document.getElementById('location');
    const otherLocation = document.getElementById('other_location');

    const finalAccidentType = document.getElementById('final_accident_type');
    const finalLocation = document.getElementById('final_location');

    function toggleButton(){
        const ready = content.value.trim() &&
                      accidentType.value &&
                      location.value &&
                      (accidentType.value !== "Others" || otherType.value.trim()) &&
                      (location.value !== "Others" || otherLocation.value.trim());
        postBtn.disabled = !ready;
    }

    [content, accidentType, otherType, location, otherLocation].forEach(el=>{
        el.addEventListener('input', toggleButton);
        el.addEventListener('change', toggleButton);
    });

    function handleOther(selectEl, inputEl){
        selectEl.addEventListener('change', function(){
            if(this.value==="Others"){
                inputEl.style.display="block";
                inputEl.required = true;
            } else {
                inputEl.style.display="none";
                inputEl.value="";
                inputEl.required = false;
            }
            toggleButton();
        });
    }

    handleOther(accidentType, otherType);
    handleOther(location, otherLocation);

    form.addEventListener('submit', function(){
        // Set hidden inputs for saving
        finalAccidentType.value = (accidentType.value==="Others" ? otherType.value.trim() : accidentType.value);
        finalLocation.value = (location.value==="Others" ? otherLocation.value.trim() : location.value);

        postBtn.disabled = true;
        postBtn.textContent = 'Posting...';
    });

    toggleButton();
})();
</script>
@endsection
