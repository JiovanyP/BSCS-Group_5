@extends('layouts.app')

@section('title', 'Publish Report')

@section('content')
<div class="post-container" role="main" aria-labelledby="createPostTitle">
    <h1 id="createPostTitle"><strong>Publish a Report</strong></h1>
    <div class="subtitle">Create reports with texts, images, or videos</div>

    <form id="postForm" action="{{ route('timeline.store') }}" method="POST" enctype="multipart/form-data" novalidate>
    @csrf
        <label for="accident_type">Accident Type (Required)</label>
        <select id="accident_type" name="accident_type" required>
            <option value="" disabled selected>Select Type</option>
            <option value="Fire">Fire</option>
            <option value="Crime">Crime</option>
            <option value="Traffic">Traffic</option>
            <option value="Others">Others</option>
        </select>

        <input id="other_type" name="other_type" type="text" placeholder="Please specify" style="display:none;" />

        <label for="content">Your Report (Required)</label>
        <textarea id="content" name="content" rows="4" placeholder="What's up?" required></textarea>

        <label for="location">Location (Required)</label>
        <input id="location" name="location" type="text" placeholder="Enter your location" required />

        <label for="image">Attach Image/Video (optional)</label>
        <div style="display: flex; gap: 8px; margin-bottom: 12px;">
            <input id="image" name="image" type="file" accept="image/*,video/*,image/gif" style="flex: 1; margin-bottom: 0;" />
            <button type="button" id="cameraBtn" class="btn-camera" style="width: auto; padding: 12px 16px; background: #eee; color: #444; margin: 0;">📷</button>
        </div>
        
        {{-- MODIFIED: Added hidden field to store media type --}}
        <input type="hidden" id="media_type_input" name="media_type" value=""> 

        <div id="preview-container" style="display:none; margin-bottom:12px;">
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

        <button id="postBtn" type="submit" class="btn btn-primary" disabled>Post</button>
        <a href="{{ route('timeline') }}" class="btn btn-secondary">Cancel</a>
    </form>

    <footer class="small">
        By posting, you agree to follow our
        <a href="#" style="color:var(--accent)">Community Guidelines</a>.
    </footer>
</div>

<style>
    /* STYLES REMAINING UNCHANGED */
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
        const location = document.getElementById('location');
        const accidentType = document.getElementById('accident_type');
        const otherType = document.getElementById('other_type');
        const imageInput = document.getElementById('image');
        const mediaTypeInput = document.getElementById('media_type_input'); // MODIFIED: Get new hidden input
        const previewContainer = document.getElementById('preview-container');
        const previewImage = document.getElementById('preview-image');
        const previewVideo = document.getElementById('preview-video');
        const removeBtn = document.getElementById('removePreviewBtn');
        const cameraBtn = document.getElementById('cameraBtn');
        const cameraPreview = document.getElementById('camera-preview');
        const cameraCanvas = document.getElementById('camera-canvas');
        const cameraControls = document.getElementById('camera-controls');
        const captureBtn = document.getElementById('captureBtn');
        const cancelCameraBtn = document.getElementById('cancelCameraBtn');

        let cameraStream = null;

        function toggleButton() {
            if (
                content.value.trim() &&
                location.value.trim() &&
                accidentType.value.trim() &&
                (accidentType.value !== "Others" || otherType.value.trim())
            ) {
                postBtn.disabled = false;
            } else {
                postBtn.disabled = true;
            }
        }

        [content, location, accidentType, otherType].forEach(el => {
            el.addEventListener('input', toggleButton);
            el.addEventListener('change', toggleButton);
        });

        accidentType.addEventListener('change', function() {
            if (this.value === "") {
                this.style.color = "#888";
                otherType.style.display = "none";
            } else {
                this.style.color = "#000";
                if (this.value === "Others") {
                    otherType.style.display = "block";
                } else {
                    otherType.style.display = "none";
                    otherType.value = "";
                }
            }
            toggleButton();
        });

        accidentType.style.color = "#888";

        imageInput.addEventListener('change', function() {
            const file = this.files[0];
            if (file) {
                const url = URL.createObjectURL(file);
                previewContainer.style.display = "block";
                removeBtn.style.display = "inline-block";
                cameraControls.style.display = "none";
                
                // MODIFIED: Set media_type_input value based on file type
                if (file.type.startsWith("image/")) {
                    mediaTypeInput.value = file.type.endsWith('/gif') ? 'gif' : 'image';
                    previewImage.src = url;
                    previewImage.style.display = "block";
                    previewVideo.style.display = "none";
                    cameraPreview.style.display = "none";
                } else if (file.type.startsWith("video/")) {
                    mediaTypeInput.value = 'video';
                    previewVideo.src = url;
                    previewVideo.style.display = "block";
                    previewImage.style.display = "none";
                    cameraPreview.style.display = "none";
                } else {
                    // Fallback or unsupported file type
                    mediaTypeInput.value = '';
                    previewContainer.style.display = "none";
                }

                window.scrollTo({ top: 0, behavior: 'smooth' });
            } else {
                previewContainer.style.display = "none";
                mediaTypeInput.value = ''; // MODIFIED: Clear on no file
            }
        });

        cameraBtn.addEventListener('click', async function() {
            try {
                if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
                    alert('Camera access is not supported in your browser. Please use a modern browser like Chrome, Firefox, or Safari.');
                    return;
                }

                cameraStream = await navigator.mediaDevices.getUserMedia({
                    video: true,
                    audio: false
                });

                cameraPreview.srcObject = cameraStream;
                cameraPreview.style.display = "block";
                cameraControls.style.display = "block";
                previewContainer.style.display = "block";
                previewImage.style.display = "none";
                previewVideo.style.display = "none";
                removeBtn.style.display = "none";
                mediaTypeInput.value = ''; // MODIFIED: Clear media type when camera is live
                imageInput.value = ""; // MODIFIED: Clear file input when camera is live

                window.scrollTo({ top: 0, behavior: 'smooth' });
            } catch (err) {
                console.error('Camera error:', err);
                let errorMsg = 'Unable to access camera. ';
                if (err.name === 'NotAllowedError' || err.name === 'PermissionDeniedError') {
                    errorMsg += 'Please allow camera access in your browser settings.';
                } else if (err.name === 'NotFoundError') {
                    errorMsg += 'No camera found on your device.';
                } else {
                    errorMsg += err.message;
                }
                alert(errorMsg);
            }
        });

        captureBtn.addEventListener('click', function() {
            const context = cameraCanvas.getContext('2d');
            cameraCanvas.width = cameraPreview.videoWidth;
            cameraCanvas.height = cameraPreview.videoHeight;
            context.drawImage(cameraPreview, 0, 0, cameraCanvas.width, cameraCanvas.height); // MODIFIED: Added width/height for safety

            cameraCanvas.toBlob(function(blob) {
                const file = new File([blob], "camera-capture.jpg", { type: "image/jpeg" });
                const dataTransfer = new DataTransfer();
                dataTransfer.items.add(file);
                imageInput.files = dataTransfer.files;
                
                // MODIFIED: Set media type to 'image' after capture
                mediaTypeInput.value = 'image';

                if (cameraStream) {
                    cameraStream.getTracks().forEach(track => track.stop());
                    cameraStream = null;
                }

                previewImage.src = URL.createObjectURL(blob);
                previewImage.style.display = "block";
                cameraPreview.style.display = "none";
                cameraControls.style.display = "none";
                removeBtn.style.display = "inline-block";
            }, 'image/jpeg', 0.95);
        });

        cancelCameraBtn.addEventListener('click', function() {
            if (cameraStream) {
                cameraStream.getTracks().forEach(track => track.stop());
                cameraStream = null;
            }
            cameraPreview.style.display = "none";
            cameraControls.style.display = "none";
            previewContainer.style.display = "none";
            mediaTypeInput.value = ''; // MODIFIED: Clear media type on cancel
        });

        removeBtn.addEventListener('click', function() {
            imageInput.value = "";
            mediaTypeInput.value = ""; // MODIFIED: Clear media type
            previewImage.src = "";
            previewVideo.src = "";
            previewImage.style.display = "none";
            previewVideo.style.display = "none";
            previewContainer.style.display = "none";

            if (cameraStream) {
                cameraStream.getTracks().forEach(track => track.stop());
                cameraStream = null;
            }
        });

        form.addEventListener('submit', function(){
            if (cameraStream) {
                cameraStream.getTracks().forEach(track => track.stop());
                cameraStream = null;
            }
            postBtn.disabled = true;
            postBtn.textContent = 'Posting...';
        });

        toggleButton();
    })();
</script>
@endsection