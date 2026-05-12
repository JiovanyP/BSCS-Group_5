{{-- resources/views/admin/posts/create.blade.php --}}
@extends('layouts.admin')

@section('title', 'Create Announcement')

@section('content')

{{-- Use Material Symbols to match your new sidebar --}}
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet" />

<div class="edit-wrapper">
    
    {{-- Admin Alert Messages --}}
    @if (session('success'))
        <div class="alert-box success">
            <span class="material-symbols-outlined">check_circle</span>
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="alert-box error">
            <span class="material-symbols-outlined">error</span>
            <div>
                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        </div>
    @endif

    <div class="post-card create-mode">
        
        {{-- Main Create Form --}}
        <form id="create-post-form" action="{{ route('admin.posts.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            {{-- Hidden fields to save final combined values --}}
            <input type="hidden" name="final_accident_type" id="final_accident_type">
            <input type="hidden" name="final_location" id="final_location">

            <div class="post-content">
                <div class="post-header">
                    <div class="report-details-edit">
                        <div style="display:flex; justify-content:space-between; align-items:center;">
                            <label class="edit-label">NEW ANNOUNCEMENT</label>
                            <span class="admin-badge">ADMIN MODE</span>
                        </div>
                        
                        <div class="input-row">
                            {{-- Accident Type Select --}}
                            <select id="accident_type" name="accident_type" class="edit-select required-field" required>
                                <option value="" disabled selected>SELECT TYPE</option>
                                <option value="Fire" {{ old('accident_type') == 'Fire' ? 'selected' : '' }}>FIRE</option>
                                <option value="Crime" {{ old('accident_type') == 'Crime' ? 'selected' : '' }}>CRIME</option>
                                <option value="Traffic" {{ old('accident_type') == 'Traffic' ? 'selected' : '' }}>TRAFFIC</option>
                                <option value="Others" {{ old('accident_type') == 'Others' ? 'selected' : '' }}>OTHERS</option>
                            </select>
                            
                            <span class="separator">•</span>
                            
                            {{-- Location Select --}}
                            <select id="location" name="location" class="edit-select location-select required-field" required>
                                <option value="" disabled selected>Select Location</option>
                                @php
                                    $locations = ['Bonifacio','Sinamar 1','Sinamar 2','Guiang','Avenue','Sunset','Sunrise','Villanueva','Abellera','Miracle','Others'];
                                @endphp
                                @foreach($locations as $loc)
                                    <option value="{{ $loc }}" {{ old('location') == $loc ? 'selected' : '' }}>{{ $loc }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Hidden "Other" Inputs --}}
                        <input id="other_type" name="other_type" type="text" class="edit-input-underlined" placeholder="Specify Incident..." 
                               value="{{ old('other_type') }}" style="display:none;" />

                        <input id="other_location" name="other_location" type="text" class="edit-input-underlined" placeholder="Specify Location..." 
                               value="{{ old('other_location') }}" style="display:none;" />
                    </div>
                </div>

                <div class="post-body">
                    {{-- Text Content --}}
                    <textarea id="content" name="content" class="edit-textarea required-field" rows="3" placeholder="What's the announcement?" required>{{ old('content') }}</textarea>

                    <div class="media-edit-area">
                        {{-- Hidden File Input --}}
                        <input id="image" name="image" type="file" accept="image/*,video/*,image/gif" style="display: none;" />
                        <input type="hidden" id="media_type_input" name="media_type" value=""> 

                        {{-- Toolbar --}}
                        <div class="media-toolbar">
                            <button type="button" class="media-btn" onclick="document.getElementById('image').click()">
                                <span class="material-symbols-outlined">image</span> Add Media
                            </button>
                            <button type="button" id="cameraBtn" class="media-btn">
                                <span class="material-symbols-outlined">photo_camera</span> Camera
                            </button>
                        </div>

                        {{-- Previews --}}
                        <div id="preview-container" class="preview-box" style="display:none;">
                            <img id="preview-image" class="preview-media" style="display:none;" alt="Preview" />
                            <video id="preview-video" class="preview-media" controls style="display:none;"></video>
                            
                            {{-- Camera Stream --}}
                            <video id="camera-preview" autoplay playsinline class="preview-media" style="display:none;"></video>
                            <canvas id="camera-canvas" style="display:none;"></canvas>
                            
                            {{-- Camera Controls --}}
                            <div id="camera-controls" class="camera-actions" style="display:none;">
                                <button type="button" id="captureBtn" class="btn-action primary">Capture</button>
                                <button type="button" id="cancelCameraBtn" class="btn-action secondary">Cancel</button>
                            </div>

                            <button type="button" id="removePreviewBtn" class="remove-media-btn" style="display:none;">
                                <span class="material-symbols-outlined" style="font-size: 16px;">close</span> Remove Media
                            </button>
                        </div>
                    </div>
                </div>

                <div class="post-signature">
                    <div class="user-info">
                        @php
                            $user = Auth::guard('admin')->user();
                            $avatar = $user->avatar_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($user->name ?? 'A');
                        @endphp
                        <img src="{{ $avatar }}" width="28" height="28" class="rounded-circle">
                        <strong>{{ $user->name ?? 'Administrator' }}</strong>
                        <small>Official Post</small>
                    </div>
                </div>

                <div class="post-footer action-footer">
                    <a href="{{ route('admin.posts.create') }}" class="btn-cancel">Cancel</a>
                    <button type="submit" id="postBtn" class="btn-save" disabled>Publish</button>
                </div>
            </div>
        </form>
    </div>
</div>

<style>
    /* === VARIABLES (Synced with Layout) === */
    :root {
        --primary: #494ca2;
        --accent: #CF0F47;
        --accent-hover: #FF0B55;
        --card-bg: #ffffff;
        --text-muted: #666;
        --border-light: #f0f0f0;
    }

    /* === RESET TO POPPINS === */
    .edit-wrapper,
    .post-card,
    input,
    select,
    textarea,
    button,
    .admin-badge,
    .user-info strong {
        font-family: 'Poppins', sans-serif !important;
    }

    .edit-wrapper {
        width: 100%;
        max-width: 550px;
        margin: 0 auto;
        padding-top: 10px;
    }

    /* === POST CARD === */
    .post-card {
        background: var(--card-bg);
        border-radius: 20px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.05);
        border: 1px solid #eee;
        overflow: hidden;
    }

    .post-content { padding: 1.5rem; }

    /* Header & Badge */
    .edit-label {
        font-size: 11px;
        font-weight: 700;
        color: var(--text-muted);
        letter-spacing: 0.8px;
    }

    .admin-badge {
        background: var(--primary);
        color: white;
        padding: 3px 10px;
        border-radius: 6px;
        font-size: 10px;
        font-weight: 700;
    }

    /* Inputs */
    .input-row { display: flex; align-items: center; gap: 8px; margin-top: 5px; }

    .edit-select {
        border: none;
        background: transparent;
        font-weight: 700;
        color: var(--accent);
        font-size: 15px;
        cursor: pointer;
        padding: 5px 0;
        outline: none;
        border-bottom: 2px solid transparent;
        transition: 0.3s;
    }
    
    .edit-select:focus { border-bottom-color: var(--accent); }
    .location-select { color: #333; font-weight: 600; }
    .separator { color: #ccc; }

    .edit-input-underlined {
        width: 100%;
        border: none;
        border-bottom: 1px solid #ddd;
        padding: 8px 0;
        font-size: 14px;
        outline: none;
        background: transparent;
        margin-top: 10px;
    }

    .edit-textarea {
        width: 100%;
        border: none;
        resize: none;
        font-size: 16px;
        line-height: 1.6;
        color: #333;
        padding: 15px 0;
        outline: none;
        min-height: 120px;
        background: transparent;
    }

    /* Media Area */
    .media-edit-area {
        margin-top: 10px;
        border-top: 1px solid var(--border-light);
        padding-top: 15px;
    }

    .media-toolbar { display: flex; gap: 12px; }

    .media-btn {
        display: flex;
        align-items: center;
        gap: 6px;
        background: #f8f9fa;
        border: 1px solid #eee;
        padding: 8px 16px;
        border-radius: 50px;
        font-size: 13px;
        font-weight: 600;
        color: var(--text-muted);
        cursor: pointer;
        transition: 0.2s;
    }

    .media-btn:hover {
        background: #fff;
        border-color: var(--accent);
        color: var(--accent);
    }

    .preview-media {
        width: 100%;
        border-radius: 15px;
        margin-top: 10px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }

    /* Signature & Footer */
    .post-signature {
        padding-top: 15px;
        margin-top: 15px;
        border-top: 1px solid var(--border-light);
    }

    .user-info { display: flex; align-items: center; gap: 10px; }
    .user-info strong { font-size: 14px; color: #333; }
    .user-info small { color: var(--text-muted); margin-left: auto; font-size: 12px; }
    .rounded-circle { border-radius: 50%; object-fit: cover; }

    .action-footer {
        display: flex;
        gap: 15px;
        justify-content: flex-end;
        padding-top: 20px;
    }

    .btn-save {
        background: var(--accent);
        color: white;
        border: none;
        padding: 10px 30px;
        border-radius: 50px;
        font-weight: 600;
        cursor: pointer;
        transition: 0.3s;
    }

    .btn-save:hover:not(:disabled) {
        background: var(--accent-hover);
        transform: translateY(-2px);
    }

    .btn-save:disabled { background: #ccc; cursor: not-allowed; }

    .btn-cancel {
        color: var(--text-muted);
        text-decoration: none;
        font-weight: 600;
        padding: 10px;
    }

    /* Alerts */
    .alert-box {
        padding: 15px;
        border-radius: 12px;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 12px;
        font-weight: 500;
    }
    .alert-box.success { background: #e8f5e9; color: #2e7d32; }
    .alert-box.error { background: #ffebee; color: #c62828; }

    /* Camera Actions */
    .btn-action {
        padding: 6px 15px;
        border-radius: 8px;
        border: none;
        font-weight: 600;
        cursor: pointer;
    }
    .btn-action.primary { background: var(--primary); color: white; }
    .btn-action.secondary { background: #eee; }
</style>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const form = document.getElementById('create-post-form');
    const postBtn = document.getElementById('postBtn');
    const content = document.getElementById('content');
    const accidentType = document.getElementById('accident_type');
    const otherType = document.getElementById('other_type');
    const location = document.getElementById('location');
    const otherLocation = document.getElementById('other_location');
    const finalAccidentType = document.getElementById('final_accident_type');
    const finalLocation = document.getElementById('final_location');

    function toggleOtherType() {
        otherType.style.display = (accidentType.value === "Others") ? "block" : "none";
        otherType.required = (accidentType.value === "Others");
        checkValidity();
    }

    function toggleOtherLocation() {
        otherLocation.style.display = (location.value === "Others") ? "block" : "none";
        otherLocation.required = (location.value === "Others");
        checkValidity();
    }

    function checkValidity() {
        const typeValid = accidentType.value && (accidentType.value !== "Others" || otherType.value.trim());
        const locValid = location.value && (location.value !== "Others" || otherLocation.value.trim());
        const contentValid = content.value.trim().length > 0;
        postBtn.disabled = !(typeValid && locValid && contentValid);
    }

    accidentType.addEventListener("change", toggleOtherType);
    location.addEventListener("change", toggleOtherLocation);
    [content, otherType, otherLocation].forEach(el => el.addEventListener('input', checkValidity));

    form.addEventListener('submit', function() {
        finalAccidentType.value = (accidentType.value === "Others" ? otherType.value.trim() : accidentType.value);
        finalLocation.value = (location.value === "Others" ? otherLocation.value.trim() : location.value);
        postBtn.disabled = true;
        postBtn.innerHTML = 'Publishing...';
    });

    // Media Logic
    const imageInput = document.getElementById('image');
    const previewContainer = document.getElementById('preview-container');
    const previewImage = document.getElementById('preview-image');
    const previewVideo = document.getElementById('preview-video');
    const removePreviewBtn = document.getElementById('removePreviewBtn');
    const cameraBtn = document.getElementById('cameraBtn');
    const cameraPreview = document.getElementById('camera-preview');
    const cameraCanvas = document.getElementById('camera-canvas');
    const cameraControls = document.getElementById('camera-controls');
    const captureBtn = document.getElementById('captureBtn');
    const cancelCameraBtn = document.getElementById('cancelCameraBtn');
    let stream = null;

    imageInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            previewContainer.style.display = 'block';
            removePreviewBtn.style.display = 'flex';
            reader.onload = function(e) {
                if (file.type.startsWith('image/')) {
                    previewImage.src = e.target.result;
                    previewImage.style.display = 'block';
                    previewVideo.style.display = 'none';
                } else {
                    previewVideo.src = e.target.result;
                    previewVideo.style.display = 'block';
                    previewImage.style.display = 'none';
                }
            };
            reader.readAsDataURL(file);
        }
    });

    removePreviewBtn.addEventListener('click', () => {
        imageInput.value = ''; 
        previewContainer.style.display = 'none';
    });

    cameraBtn.addEventListener('click', async () => {
        try {
            previewContainer.style.display = 'block';
            stream = await navigator.mediaDevices.getUserMedia({ video: true });
            cameraPreview.srcObject = stream;
            cameraPreview.style.display = 'block';
            cameraControls.style.display = 'flex';
        } catch (err) { alert("Camera error."); }
    });

    captureBtn.addEventListener('click', () => {
        cameraCanvas.width = cameraPreview.videoWidth;
        cameraCanvas.height = cameraPreview.videoHeight;
        cameraCanvas.getContext('2d').drawImage(cameraPreview, 0, 0);
        cameraCanvas.toBlob(blob => {
            const file = new File([blob], "capture.jpg", { type: "image/jpeg" });
            const dt = new DataTransfer();
            dt.items.add(file);
            imageInput.files = dt.files;
            imageInput.dispatchEvent(new Event('change'));
            stopCamera();
        }, 'image/jpeg');
    });

    function stopCamera() {
        if (stream) stream.getTracks().forEach(t => t.stop());
        cameraPreview.style.display = 'none';
        cameraControls.style.display = 'none';
    }

    cancelCameraBtn.addEventListener('click', stopCamera);
});
</script>
@endsection