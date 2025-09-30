<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Report Accident</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .urgency-option input[type="radio"]:checked + label {
            border-color: #3b82f6;
            background-color: #eff6ff;
        }
        .camera-preview {
            display: none;
            width: 100%;
            max-width: 400px;
            border-radius: 8px;
            overflow: hidden;
            margin-top: 10px;
        }
    </style>
</head>
<body class="bg-gray-100 min-h-screen">
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-2xl mx-auto bg-white rounded-lg shadow-md overflow-hidden">
            <div class="bg-blue-600 py-4 px-6">
                <h1 class="text-2xl font-bold text-white">Report an Accident</h1>
                <p class="text-blue-100">Please provide details about the accident</p>
            </div>

            <div class="p-6">
                @if(session('success'))
                    <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6" role="alert">
                        <p class="font-bold">Success!</p>
                        <p>{{ session('success') }}</p>
                        <div class="mt-4">
                            <a href="{{ route('accidents.create') }}" class="inline-block bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                                Report Another Accident
                            </a>
                        </div>
                    </div>
                @endif

                @if($errors->any())
                    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6" role="alert">
                        <p class="font-bold">Please fix the following errors:</p>
                        <ul class="list-disc list-inside mt-2">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('accidents.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    
                    <div class="mb-6">
                        <label for="full_name" class="block text-gray-700 font-medium mb-2">Full Name (Optional)</label>
                        <input type="text" id="full_name" name="full_name" value="{{ old('full_name') }}" 
                               class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                               placeholder="Your full name">
                    </div>

                    <div class="mb-6">
                        <label for="location" class="block text-gray-700 font-medium mb-2">Location <span class="text-red-500">*</span></label>
                        <input type="text" id="location" name="location" value="{{ old('location') }}" required
                               class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                               placeholder="Building name, room number, etc.">
                    </div>

                    <div class="mb-6">
                        <label for="accident_type" class="block text-gray-700 font-medium mb-2">Accident Type <span class="text-red-500">*</span></label>
                        <select id="accident_type" name="accident_type" required
                                class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">Select an accident type</option>
                            <option value="slip_trip" {{ old('accident_type') == 'slip_trip' ? 'selected' : '' }}>Slip or Trip</option>
                            <option value="fall" {{ old('accident_type') == 'fall' ? 'selected' : '' }}>Fall</option>
                            <option value="vehicle" {{ old('accident_type') == 'vehicle' ? 'selected' : '' }}>Vehicle Accident</option>
                            <option value="equipment" {{ old('accident_type') == 'equipment' ? 'selected' : '' }}>Equipment Mishap</option>
                            <option value="other" {{ old('accident_type') == 'other' ? 'selected' : '' }}>Other</option>
                        </select>
                    </div>

                    <div class="mb-6">
                        <label for="description" class="block text-gray-700 font-medium mb-2">Description of Accident <span class="text-red-500">*</span></label>
                        <textarea id="description" name="description" rows="5" required
                                  class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                  placeholder="Please provide details about the accident">{{ old('description') }}</textarea>
                    </div>

                    <div class="mb-6">
                        <label class="block text-gray-700 font-medium mb-2">Upload Photo <span class="text-red-500">*</span></label>
                        <div class="flex flex-col space-y-4">
                            <div>
                                <input type="file" id="photo" name="photo" accept="image/*" class="hidden" required>
                                <label for="photo" class="cursor-pointer bg-blue-500 hover:bg-blue-600 text-white py-2 px-4 rounded-lg inline-flex items-center">
                                    <i class="fas fa-camera mr-2"></i> Choose Photo
                                </label>
                                <span id="file-name" class="ml-3 text-gray-600">No file chosen</span>
                            </div>
                            
                            <div id="camera-controls" class="mt-4">
                                <button type="button" id="start-camera" class="bg-green-500 hover:bg-green-600 text-white py-2 px-4 rounded-lg inline-flex items-center">
                                    <i class="fas fa-video mr-2"></i> Use Camera
                                </button>
                                
                                <div class="mt-4 space-y-4">
                                    <video id="video" class="camera-preview" autoplay playsinline></video>
                                    <canvas id="canvas" class="camera-preview"></canvas>
                                    
                                    <div id="capture-buttons" class="hidden space-x-4">
                                        <button type="button" id="capture-btn" class="bg-blue-500 hover:bg-blue-600 text-white py-2 px-4 rounded-lg">
                                            Capture Photo
                                        </button>
                                        <button type="button" id="retake-btn" class="bg-gray-500 hover:bg-gray-600 text-white py-2 px-4 rounded-lg">
                                            Retake
                                        </button>
                                    </div>
                                </div>
                                
                                <input type="hidden" id="photo-data" name="photo_data">
                            </div>
                        </div>
                    </div>

                    <div class="mb-6">
                        <label class="block text-gray-700 font-medium mb-2">Urgency Level <span class="text-red-500">*</span></label>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 urgency-selector">
                            <div class="urgency-option">
                                <input type="radio" id="low" name="urgency" value="low" class="hidden" 
                                       {{ old('urgency', 'low') == 'low' ? 'checked' : '' }} required>
                                <label for="low" class="block p-4 border-2 rounded-lg cursor-pointer text-center">
                                    <div class="text-2xl mb-2">🟢</div>
                                    <div class="font-bold">Low</div>
                                    <div class="text-sm text-gray-600 mt-1">Regular maintenance, no disruption to activities</div>
                                </label>
                            </div>
                            
                            <div class="urgency-option">
                                <input type="radio" id="medium" name="urgency" value="medium" class="hidden"
                                       {{ old('urgency') == 'medium' ? 'checked' : '' }}>
                                <label for="medium" class="block p-4 border-2 rounded-lg cursor-pointer text-center">
                                    <div class="text-2xl mb-2">🟠</div>
                                    <div class="font-bold">Medium</div>
                                    <div class="text-sm text-gray-600 mt-1">Requires attention soon, minor disruption</div>
                                </label>
                            </div>
                            
                            <div class="urgency-option">
                                <input type="radio" id="high" name="urgency" value="high" class="hidden"
                                       {{ old('urgency') == 'high' ? 'checked' : '' }}>
                                <label for="high" class="block p-4 border-2 rounded-lg cursor-pointer text-center">
                                    <div class="text-2xl mb-2">🔴</div>
                                    <div class="font-bold">High - Urgent</div>
                                    <div class="text-sm text-gray-600 mt-1">Immediate attention needed, major disruption</div>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="mt-8">
                        <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-4 rounded-lg focus:outline-none focus:shadow-outline">
                            Submit Report
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // File input display
        document.getElementById('photo').addEventListener('change', function(e) {
            const fileName = e.target.files[0] ? e.target.files[0].name : 'No file chosen';
            document.getElementById('file-name').textContent = fileName;
        });

        // Camera functionality
        const startCameraBtn = document.getElementById('start-camera');
        const video = document.getElementById('video');
        const canvas = document.getElementById('canvas');
        const captureBtn = document.getElementById('capture-btn');
        const retakeBtn = document.getElementById('retake-btn');
        const captureButtons = document.getElementById('capture-buttons');
        const photoData = document.getElementById('photo-data');
        let stream = null;

        startCameraBtn.addEventListener('click', async function() {
            try {
                stream = await navigator.mediaDevices.getUserMedia({ video: true, audio: false });
                video.srcObject = stream;
                video.classList.remove('camera-preview');
                video.classList.add('block');
                captureButtons.classList.remove('hidden');
                startCameraBtn.classList.add('hidden');
            } catch (err) {
                console.error("Error accessing the camera:", err);
                alert("Unable to access camera. Please check permissions and try again.");
            }
        });

        captureBtn.addEventListener('click', function() {
            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;
            canvas.getContext('2d').drawImage(video, 0, 0, canvas.width, canvas.height);
            
            // Stop video stream
            stream.getTracks().forEach(track => track.stop());
            
            // Show canvas, hide video
            video.classList.add('camera-preview');
            canvas.classList.remove('camera-preview');
            canvas.classList.add('block');
            
            // Convert to data URL and set as hidden input value
            const imageData = canvas.toDataURL('image/png');
            photoData.value = imageData;
            
            // Hide capture buttons
            captureButtons.classList.add('hidden');
            
            // Create a file from data URL and set it to the file input
            const dataURLtoBlob = (dataURL) => {
                const byteString = atob(dataURL.split(',')[1]);
                const mimeString = dataURL.split(',')[0].split(':')[1].split(';')[0];
                const ab = new ArrayBuffer(byteString.length);
                const ia = new Uint8Array(ab);
                for (let i = 0; i < byteString.length; i++) {
                    ia[i] = byteString.charCodeAt(i);
                }
                return new Blob([ab], { type: mimeString });
            };
            
            const blob = dataURLtoBlob(imageData);
            const file = new File([blob], 'camera-capture.png', { type: 'image/png' });
            const dataTransfer = new DataTransfer();
            dataTransfer.items.add(file);
            document.getElementById('photo').files = dataTransfer.files;
            document.getElementById('file-name').textContent = 'camera-capture.png';
        });

        retakeBtn.addEventListener('click', async function() {
            // Clear canvas
            canvas.getContext('2d').clearRect(0, 0, canvas.width, canvas.height);
            canvas.classList.add('camera-preview');
            
            // Restart camera
            try {
                stream = await navigator.mediaDevices.getUserMedia({ video: true, audio: false });
                video.srcObject = stream;
                video.classList.remove('camera-preview');
                video.classList.add('block');
            } catch (err) {
                console.error("Error accessing the camera:", err);
                alert("Unable to access camera. Please check permissions and try again.");
            }
        });

        // Form validation
        document.querySelector('form').addEventListener('submit', function(e) {
            const photoInput = document.getElementById('photo');
            if (!photoInput.files || photoInput.files.length === 0) {
                e.preventDefault();
                alert('Please upload a photo of the accident.');
                return false;
            }
        });
    </script>
</body>
</html>