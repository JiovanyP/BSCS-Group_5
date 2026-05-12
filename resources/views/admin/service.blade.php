{{-- resources/views/admin/service.blade.php --}}
@extends('layouts.admin')

@section('title', 'Service Advertisements')

@section('content')
<div class="service-wrapper">

    {{-- 1. CREATION SECTION --}}
    <div class="section-container">
        <div class="post-card create-mode">
            <form action="{{ route('admin.services.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="post-content">
                    <div class="post-header">
                        <div class="report-details-edit">
                            <label class="edit-label">PROMOTE A BUSINESS</label>
                            <h2 class="form-main-title">Create Service Ad</h2>
                        </div>
                    </div>

                    <div class="form-grid">
                        <div class="form-group">
                            <label class="form-label">Business Name</label>
                            <input type="text" name="business_name" class="form-input" placeholder="e.g. Kabacan Auto Repair" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Contact Number</label>
                            <input type="text" name="contact_number" class="form-input" placeholder="0912 345 6789" required>
                        </div>
                    </div>

                    <div class="form-group mt-3">
                        <label class="form-label">Business Address</label>
                        <input type="text" name="address" class="form-input" placeholder="St. Address, Barangay, City" required>
                    </div>

                    <div class="form-group mt-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-textarea" rows="2" placeholder="Tell the community about this business..." required></textarea>
                    </div>

                    <div class="form-group mt-3">
                        <label class="form-label">Services Offered</label>
                        <textarea name="services_offered" class="form-textarea" rows="2" placeholder="e.g. Oil Change, Engine Repair, Car Wash" required></textarea>
                    </div>

                    <div class="media-edit-area">
                        <input type="file" id="logo-input" name="logo" hidden accept="image/*">
                        <div class="media-toolbar">
                            <button type="button" class="media-btn" onclick="document.getElementById('logo-input').click()">
                                <span class="material-icons">add_a_photo</span> Add Business Logo
                            </button>
                            <span id="file-name-display" class="file-name">No file selected</span>
                        </div>
                    </div>

                    <div class="post-footer action-footer mt-4">
                        <button type="submit" class="btn-save">Publish Advertisement</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <hr class="section-divider">

    {{-- 2. LIST SECTION --}}
    <div class="section-container mt-5">
        <h3 class="list-title">Active Services</h3>
        <div class="services-list">
            @forelse($services as $service)
                <div class="service-item-card">
                    <div class="service-logo">
                        @if($service->logo_url)
                            <img src="{{ $service->logo_url }}" alt="logo">
                        @else
                            <div class="logo-placeholder">{{ substr($service->business_name, 0, 1) }}</div>
                        @endif
                    </div>
                    <div class="service-info">
                        <h4>{{ $service->business_name }}</h4>
                        <p class="service-desc">{{ Str::limit($service->description, 100) }}</p>
                        <div class="service-meta">
                            <span><span class="material-icons">phone</span> {{ $service->contact_number }}</span>
                            <span><span class="material-icons">location_on</span> {{ $service->address }}</span>
                        </div>
                    </div>
                    <div class="service-actions">
                        <form action="{{ route('admin.services.destroy', $service->id) }}" method="POST" onsubmit="return confirm('Remove this service?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="delete-btn"><span class="material-icons">delete</span></button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="empty-state">No service advertisements published yet.</div>
            @endforelse
        </div>
    </div>
</div>

<style>
    .service-wrapper { max-width: 800px; margin: 0 auto; padding-bottom: 50px; }
    
    /* Form Styles */
    .form-main-title { font-weight: 700; font-size: 20px; color: var(--black); margin: 0; }
    .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-top: 15px; }
    
    .form-group .form-label { font-size: 12px; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 5px; }
    .form-input, .form-textarea {
        width: 100%; padding: 12px; border-radius: 10px; border: 1px solid #eee;
        background: #fcfcfc; font-family: 'Poppins', sans-serif; font-size: 14px; transition: 0.3s;
    }
    .form-input:focus, .form-textarea:focus { border-color: var(--accent); outline: none; background: #fff; }
    .form-textarea { resize: none; }

    .file-name { font-size: 12px; color: var(--text-muted); }

    .section-divider { border: 0; height: 1px; background: linear-gradient(to right, transparent, #ddd, transparent); margin: 40px 0; }

    /* List Styles */
    .list-title { font-weight: 700; margin-bottom: 20px; color: var(--black); }
    .service-item-card {
        background: white; border-radius: 15px; padding: 15px; display: flex; gap: 15px;
        border: 1px solid #eee; margin-bottom: 15px; align-items: center; transition: 0.3s;
    }
    .service-item-card:hover { transform: translateX(5px); border-color: var(--accent); }

    .service-logo img, .logo-placeholder {
        width: 60px; height: 60px; border-radius: 12px; object-fit: cover;
    }
    .logo-placeholder {
        background: var(--light-pink); color: var(--accent);
        display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 24px;
    }

    .service-info { flex: 1; }
    .service-info h4 { margin: 0; font-size: 16px; font-weight: 700; color: #111; }
    .service-desc { font-size: 13px; color: var(--text-muted); margin: 4px 0; }
    .service-meta { display: flex; gap: 15px; font-size: 11px; font-weight: 600; color: var(--primary); }
    .service-meta span { display: flex; align-items: center; gap: 4px; }
    .service-meta .material-icons { font-size: 14px; }

    .delete-btn { background: none; border: none; color: #ccc; cursor: pointer; transition: 0.2s; }
    .delete-btn:hover { color: var(--accent); }

    .empty-state { text-align: center; padding: 40px; color: #bbb; font-weight: 500; }
</style>

<script>
    document.getElementById('logo-input').onchange = function() {
        document.getElementById('file-name-display').innerText = this.files[0].name;
    };
</script>
@endsection