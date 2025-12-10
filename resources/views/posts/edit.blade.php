@extends('layouts.app')

@section('title', 'Edit Report')

@section('content')

{{-- Import the fonts used in your post card --}}
<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
<link href="https://fonts.googleapis.com/icon?family=Material+Icons+Outlined" rel="stylesheet">

<div class="edit-wrapper">
    <div class="post-card edit-mode">
        
        {{-- Main Update Form --}}
        <form id="update-post-form" action="{{ route('posts.update', $post) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
            {{-- 
                PHP LOGIC: 
                Determine if the saved data is a standard option or a custom "Others" input.
            --}}
            @php
                $stdTypes = ['Fire', 'Crime', 'Traffic'];
                $stdLocs  = ['Bonifacio','Sinamar 1','Sinamar 2','Guiang','Avenue','Sunset','Sunrise','Villanueva','Abellera','Miracle'];

                // If the saved value is NOT in the standard list, it is custom
                $isCustomType = !in_array($post->accident_type, $stdTypes);
                $isCustomLoc  = !in_array($post->location, $stdLocs);
            @endphp

            <div class="post-content">
                <div class="post-header">
                    <div class="report-details-edit">
                        <label class="edit-label">INCIDENT TYPE</label>
                        <div class="input-row">
                            {{-- Accident Type Select --}}
                            <select id="accident_type" name="accident_type" class="edit-select required-field" required>
                                {{-- Standard Options --}}
                                <option value="Fire" {{ $post->accident_type == 'Fire' ? 'selected' : '' }}>FIRE</option>
                                <option value="Crime" {{ $post->accident_type == 'Crime' ? 'selected' : '' }}>CRIME</option>
                                <option value="Traffic" {{ $post->accident_type == 'Traffic' ? 'selected' : '' }}>TRAFFIC</option>
                                
                                {{-- Force selection of 'Others' if the DB has a custom value --}}
                                <option value="Others" {{ $isCustomType ? 'selected' : '' }}>OTHERS</option>
                            </select>
                            
                            <span class="separator">•</span>
                            
                            {{-- Location Select --}}
                            <select id="location" name="location" class="edit-select location-select required-field" required>
                                @foreach($stdLocs as $loc)
                                    <option value="{{ $loc }}" {{ $post->location == $loc ? 'selected' : '' }}>{{ $loc }}</option>
                                @endforeach
                                {{-- Force selection of 'Others' if the DB has a custom value --}}
                                <option value="Others" {{ $isCustomLoc ? 'selected' : '' }}>Others</option>
                            </select>
                        </div>

                        {{-- Hidden "Other" Inputs --}}
                        {{-- Logic: If custom, put the DB value in the text box and show it immediately --}}
                        <input id="other_type" name="other_type" type="text" class="edit-input-underlined" placeholder="Specify Incident..." 
                               value="{{ $isCustomType ? $post->accident_type : '' }}" 
                               style="{{ $isCustomType ? 'display:block;' : 'display:none;' }}" />

                        <input id="other_location" name="other_location" type="text" class="edit-input-underlined" placeholder="Specify Location..." 
                               value="{{ $isCustomLoc ? $post->location : '' }}" 
                               style="{{ $isCustomLoc ? 'display:block;' : 'display:none;' }}" />
                    </div>

                    {{-- Top Right: Delete Option --}}
                    <div class="dropdown">
                        <button type="button" class="btn-icon text-danger" onclick="if(confirm('Delete this post? Cannot be undone.')) document.getElementById('delete-post-form').submit();">
                            <span class="material-icons">delete</span>
                        </button>
                    </div>
                </div>

                <div class="post-body">
                    <textarea id="content" name="content" class="edit-textarea required-field" rows="3" placeholder="What happened?" required>{{ old('content', $post->content) }}</textarea>

                    <div class="media-edit-area">
                        {{-- Hidden File Input --}}
                        <input id="image" name="image" type="file" accept="image/*,video/*,image/gif" style="display: none;" />
                        <input type="hidden" id="media_type_input" name="media_type" value="{{ $post->media_type ?? '' }}">

                        {{-- Toolbar --}}
                        <div class="media-toolbar">
                            <button type="button" class="media-btn" onclick="document.getElementById('image').click()">
                                <span class="material-icons">image</span> Change Media
                            </button>
                            <button type="button" id="cameraBtn" class="media-btn">
                                <span class="material-icons">photo_camera</span> Camera
                            </button>
                            @if($post->image)
                                <span class="current-badge">Has Current Media</span>
                            @endif
                        </div>

                        {{-- Previews --}}
                        <div id="preview-container" class="preview-box">
                            
                            {{-- Current Media (Initially Visible) --}}
                            <div id="current-media-wrapper" style="{{ $post->image ? '' : 'display:none;' }}">
                                @if($post->image)
                                    @if($post->media_type === 'video')
                                        <video controls class="preview-media">
                                            <source src="{{ asset('storage/' . $post->image) }}" type="video/{{ pathinfo($post->image, PATHINFO_EXTENSION) }}">
                                        </video>
                                    @else
                                        <img src="{{ asset('storage/' . $post->image) }}" class="preview-media" alt="Current Image">
                                    @endif
                                @endif
                            </div>

                            {{-- New Previews (JS) --}}
                            <img id="preview-image" class="preview-media" style="display:none;" />
                            <video id="preview-video" class="preview-media" controls style="display:none;"></video>
                            
                            {{-- Camera Stream --}}
                            <video id="camera-preview" autoplay playsinline class="preview-media" style="display:none;"></video>
                            <canvas id="camera-canvas" style="display:none;"></canvas>
                            
                            {{-- Camera Controls --}}
                            <div id="camera-controls" class="camera-actions" style="display:none;">
                                <button type="button" id="captureBtn" class="btn btn-sm btn-primary">Capture</button>
                                <button type="button" id="cancelCameraBtn" class="btn btn-sm btn-secondary">Cancel</button>
                            </div>

                            <button type="button" id="removePreviewBtn" class="remove-media-btn" style="display:none;">
                                <span class="material-icons" style="font-size: 16px;">close</span> Remove New Media
                            </button>
                        </div>
                    </div>
                </div>

                <div class="post-signature">
                    <div class="user-info">
                        <img src="{{ $post->user->avatar_url ?? asset('images/avatar.png') }}" width="28" height="28" class="rounded-circle">
                        <strong>{{ $post->user->name ?? 'Unknown User' }}</strong>
                        <small>Editing now</small>
                    </div>
                </div>

                <div class="post-footer action-footer">
                    <button type="button" class="btn-cancel" onclick="window.history.back()">Cancel</button>
                    <button type="submit" class="btn-save">Update Post</button>
                </div>
            </div>
        </form>
    </div>

    {{-- Hidden Delete Form --}}
    <form id="delete-post-form" action="{{ route('posts.destroy', $post) }}" method="POST" style="display: none;">
        @csrf
        @method('DELETE')
    </form>
</div>

<style>
    /* === CORE VARIABLES === */
    :root {
        --primary: #494ca2;
        --accent: #CF0F47;
        --accent-2: #FF0B55;
        --card-bg: #ffffff;
        --text-muted: #666;
        --border-color: #ddd;
        --input-bg: #fbfbfb;
    }

    .edit-wrapper {
        width: 100%;
        max-width: 500px;
        margin: 20px auto;
        padding: 0 10px;
    }

    /* === POST CARD STYLES === */
    .post-card {
        background: var(--card-bg);
        border-radius: 20px;
        box-shadow: 0 12px 40px rgba(0,0,0,0.1);
        transition: all 0.25s ease;
        position: relative;
        font-size: 14px;
        overflow: hidden;
    }

    .post-content {
        padding: 1.5rem;
    }

    .post-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 1rem;
    }

    /* === EDITING SPECIFIC STYLES === */
    .report-details-edit {
        flex: 1;
        display: flex;
        flex-direction: column;
        gap: 4px;
    }

    .edit-label {
        font-size: 10px;
        font-weight: 700;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .input-row {
        display: flex;
        align-items: center;
        gap: 5px;
    }

    /* Seamless Selects */
    .edit-select {
        border: none;
        background: transparent;
        font-weight: 700;
        color: var(--accent);
        font-size: 14px;
        cursor: pointer;
        padding: 4px 0;
        outline: none;
        border-bottom: 2px solid transparent;
        transition: border-color 0.2s;
    }
    
    .edit-select:focus {
        border-bottom-color: var(--accent);
    }

    .location-select {
        color: #333;
        font-weight: 600;
    }

    .separator {
        color: #ccc;
    }

    /* Underlined inputs for "Others" */
    .edit-input-underlined {
        width: 100%;
        border: none;
        border-bottom: 1px solid #ddd;
        padding: 5px 0;
        font-size: 13px;
        outline: none;
        background: transparent;
        color: #444;
        margin-top: 5px;
    }
    .edit-input-underlined:focus {
        border-bottom-color: var(--accent);
    }

    /* Seamless Textarea */
    .edit-textarea {
        width: 100%;
        border: none;
        resize: vertical;
        font-family: inherit;
        font-size: 15px;
        line-height: 1.5;
        color: #333;
        padding: 0;
        outline: none;
        min-height: 100px;
        background: transparent;
    }
    .edit-textarea::placeholder {
        color: #aaa;
    }

    /* === MEDIA AREA === */
    .media-edit-area {
        margin-top: 15px;
        border-top: 1px dashed #eee;
        padding-top: 15px;
    }

    .media-toolbar {
        display: flex;
        gap: 10px;
        align-items: center;
        margin-bottom: 10px;
    }

    .media-btn {
        display: flex;
        align-items: center;
        gap: 5px;
        background: #f0f2f5;
        border: none;
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        color: #555;
        cursor: pointer;
        transition: background 0.2s;
    }
    .media-btn:hover {
        background: #e4e6eb;
        color: #333;
    }
    .media-btn .material-icons {
        font-size: 16px;
    }

    .current-badge {
        font-size: 11px;
        color: #28a745;
        background: rgba(40, 167, 69, 0.1);
        padding: 4px 8px;
        border-radius: 4px;
        margin-left: auto;
    }

    .preview-box {
        position: relative;
    }

    .preview-media {
        max-width: 100%;
        max-height: 250px;
        border-radius: 12px;
        object-fit: cover;
        box-shadow: 0 4px 12px rgba(0,0,0,0.08);
        display: block;
    }

    .remove-media-btn {
        position: absolute;
        top: 10px;
        right: 10px;
        background: rgba(0,0,0,0.6);
        color: white;
        border: none;
        border-radius: 20px;
        padding: 4px 10px;
        font-size: 11px;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 4px;
    }

    .camera-actions {
        display: flex;
        gap: 10px;
        margin-top: 10px;
        justify-content: center;
    }

    /* === FOOTER & BUTTONS === */
    .post-signature {
        padding-top: 10px;
        margin-bottom: 1rem;
        border-top: 1px solid #f0f0f0;
    }
    
    .user-info {
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .user-info strong {
        font-size: 14px;
        font-weight: 600;
    }
    .user-info small {
        color: var(--text-muted);
        font-size: 12px;
        margin-left: auto;
    }

    .action-footer {
        display: flex;
        gap: 12px;
        border-top: 1px solid #f0f0f0;
        padding-top: 15px;
        justify-content: flex-end;
    }

    .btn-save {
        background: var(--accent);
        color: white;
        border: none;
        padding: 10px 24px;
        border-radius: 20px;
        font-weight: 700;
        font-size: 14px;
        cursor: pointer;
        transition: background 0.2s;
    }
    .btn-save:hover {
        background: var(--accent-2);
        box-shadow: 0 4px 12px rgba(207, 15, 71, 0.3);
    }

    .btn-cancel {
        background: transparent;
        color: #666;
        border: none;
        padding: 10px 16px;
        font-weight: 600;
        font-size: 14px;
        cursor: pointer;
    }
    .btn-cancel:hover {
        color: #333;
        text-decoration: underline;
    }

    .btn-icon {
        background: transparent;
        border: none;
        cursor: pointer;
        padding: 4px;
        border-radius: 50%;
        transition: background 0.2s;
    }
    .btn-icon:hover {
        background: #ffebee;
    }
</style>

<script>
document.addEventListener("DOMContentLoaded", function() {
    // 1. Handling "Other" Inputs
    const accidentType = document.getElementById("accident_type");
    const otherType = document.getElementById("other_type");
    const location = document.getElementById("location");
    const otherLocation = document.getElementById("other_location");

    function toggleOtherType() {
        if (accidentType.value === "Others") {
            otherType.style.display = "block";
            otherType.focus();
        } else {
            otherType.style.display = "none";
            otherType.value = "";
        }
    }

    function toggleOtherLocation() {
        if (location.value === "Others") {
            otherLocation.style.display = "block";
            otherLocation.focus();
        } else {
            otherLocation.style.display = "none";
            otherLocation.value = "";
        }
    }

    // Listen for user changes
    accidentType.addEventListener("change", toggleOtherType);
    location.addEventListener("change", toggleOtherLocation);

    // Note: We do NOT call toggleOtherType/Location immediately on load here.
    // Why? Because PHP at the top of the file has already handled the initial 
    // display state/value of the inputs based on the database.


    // 2. Handling Media (Files & Camera)
    const imageInput = document.getElementById('image');
    const previewContainer = document.getElementById('preview-container');
    const currentMediaWrapper = document.getElementById('current-media-wrapper');
    const previewImage = document.getElementById('preview-image');
    const previewVideo = document.getElementById('preview-video');
    const removePreviewBtn = document.getElementById('removePreviewBtn');
    
    // Camera Elements
    const cameraBtn = document.getElementById('cameraBtn');
    const cameraPreview = document.getElementById('camera-preview');
    const cameraCanvas = document.getElementById('camera-canvas');
    const cameraControls = document.getElementById('camera-controls');
    const captureBtn = document.getElementById('captureBtn');
    const cancelCameraBtn = document.getElementById('cancelCameraBtn');
    let stream = null;

    // -- File Selection Logic --
    imageInput.addEventListener('change', function(event) {
        const file = event.target.files[0];
        if (file) {
            const fileType = file.type;
            const reader = new FileReader();

            // Hide current media, show removal button
            if(currentMediaWrapper) currentMediaWrapper.style.display = 'none';
            removePreviewBtn.style.display = 'flex';

            reader.onload = function(e) {
                if (fileType.startsWith('image/')) {
                    previewImage.src = e.target.result;
                    previewImage.style.display = 'block';
                    previewVideo.style.display = 'none';
                } else if (fileType.startsWith('video/')) {
                    previewVideo.src = e.target.result;
                    previewVideo.style.display = 'block';
                    previewImage.style.display = 'none';
                }
            }
            reader.readAsDataURL(file);
        }
    });

    // -- Remove New Preview Logic --
    removePreviewBtn.addEventListener('click', function() {
        imageInput.value = ''; // Reset input
        previewImage.style.display = 'none';
        previewVideo.style.display = 'none';
        previewImage.src = '';
        previewVideo.src = '';
        removePreviewBtn.style.display = 'none';
        
        // Restore original media view if exists
        if(currentMediaWrapper) currentMediaWrapper.style.display = 'block';
    });

    // -- Camera Logic --
    cameraBtn.addEventListener('click', async () => {
        try {
            // Hide everything else
            if(currentMediaWrapper) currentMediaWrapper.style.display = 'none';
            previewImage.style.display = 'none';
            previewVideo.style.display = 'none';

            stream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: 'environment' } });
            cameraPreview.srcObject = stream;
            cameraPreview.style.display = 'block';
            cameraControls.style.display = 'flex';
        } catch (err) {
            alert("Camera access denied or unavailable.");
        }
    });

    captureBtn.addEventListener('click', () => {
        const context = cameraCanvas.getContext('2d');
        cameraCanvas.width = cameraPreview.videoWidth;
        cameraCanvas.height = cameraPreview.videoHeight;
        context.drawImage(cameraPreview, 0, 0, cameraCanvas.width, cameraCanvas.height);

        cameraCanvas.toBlob(blob => {
            const file = new File([blob], "camera_capture.jpg", { type: "image/jpeg" });
            const container = new DataTransfer();
            container.items.add(file);
            imageInput.files = container.files;

            // Trigger change event to update preview using the file logic above
            imageInput.dispatchEvent(new Event('change'));

            stopCamera();
        }, 'image/jpeg');
    });

    cancelCameraBtn.addEventListener('click', () => {
        stopCamera();
        // Restore previous state
        if (!imageInput.files.length && currentMediaWrapper) {
            currentMediaWrapper.style.display = 'block';
        } else if (imageInput.files.length) {
             imageInput.dispatchEvent(new Event('change'));
        }
    });

    function stopCamera() {
        if (stream) {
            stream.getTracks().forEach(track => track.stop());
        }
        cameraPreview.style.display = 'none';
        cameraControls.style.display = 'none';
    }
});
</script>

@endsection