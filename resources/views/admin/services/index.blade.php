@extends('layouts.admin')

@section('title', 'Services Management')

@section('content')

{{-- Use Material Icons --}}
<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet" />

<div class="edit-wrapper">
    
    {{-- Alert Messages --}}
    @if (session('success'))
        <div class="alert-box success">
            <span class="material-icons">check_circle</span>
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="alert-box error">
            <span class="material-icons">error</span>
            <div>
                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- 1. REGISTER BUSINESS FORM (AUTO-APPROVES FOR ADMINS) --}}
    <div class="post-card create-mode" style="margin-bottom: 40px;">
        <form action="{{ route('admin.services.store') }}" method="POST">
            @csrf
            <div class="post-content">
                <div class="post-header">
                    <div class="report-details-edit" style="width: 100%;">
                        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 15px;">
                            <label class="edit-label">MANUAL BUSINESS REGISTRATION</label>
                            <span class="admin-badge">AUTO-APPROVES</span>
                        </div>
                        
                        <div class="input-row-grid">
                            <div class="field-container">
                                <label class="field-label-small">BUSINESS NAME</label>
                                <input name="business_name" type="text" class="edit-input-underlined" placeholder="e.g. Kabacan Towing" required />
                            </div>
                            <div class="field-container">
                                <label class="field-label-small">SERVICE OFFERED</label>
                                <input name="service_offered" type="text" class="edit-input-underlined" placeholder="e.g. Vehicle Recovery" required />
                            </div>
                        </div>

                        <div class="input-row-grid" style="margin-top: 20px;">
                            <div class="field-container">
                                <label class="field-label-small">CONTACT NUMBER</label>
                                <input name="contact_number" type="text" class="edit-input-underlined" placeholder="09XX XXX XXXX" required />
                            </div>
                            <div class="field-container">
                                <label class="field-label-small">OFFICE ADDRESS</label>
                                <input name="address" type="text" class="edit-input-underlined" placeholder="Brgy. Poblacion, Kabacan" required />
                            </div>
                        </div>
                    </div>
                </div>

                <div class="post-body">
                    <label class="field-label-small" style="margin-top:10px; display:block;">BUSINESS DESCRIPTION</label>
                    <textarea name="business_description" class="edit-textarea" rows="2" placeholder="Briefly describe the business and operations..."></textarea>
                </div>

                <div class="post-footer action-footer" style="justify-content: flex-end; padding-top: 15px;">
                    <button type="reset" class="btn-cancel" style="border:none; background:none; cursor:pointer;">Reset</button>
                    <button type="submit" class="btn-save">Register Service</button>
                </div>
            </div>
        </form>
    </div>

    {{-- 2. PENDING APPROVALS --}}
    <div style="display:flex; align-items:center; gap:8px; margin-bottom:15px; color:#ff9800;">
        <span class="material-icons">pending_actions</span>
        <label class="edit-label" style="font-size:14px; margin:0; color:#ff9800;">PENDING APPROVALS</label>
    </div>

    <div class="services-feed" style="margin-bottom: 40px;">
        @forelse($pendingServices as $service)
            <div class="post-card" style="border-left: 4px solid #ff9800;">
                <div class="post-content">
                    <div class="post-header">
                        <div class="report-details" style="color: #ff9800;">
                            {{ strtoupper($service->services_offered) }} • 
                            <span class="location">{{ $service->address }}</span>
                        </div>
                        
                        <div class="dropdown">
                            <button class="dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false">
                                <span class="material-icons">more_horiz</span>
                            </button>
                            <div class="dropdown-menu dropdown-menu-right">
                                {{-- Approve Form --}}
                                <form action="{{ route('admin.services.approve', $service->id) }}" method="POST" style="margin:0; border-bottom:1px solid #eee;">
                                    @csrf
                                    <button class="dropdown-item" style="color: #28a745; font-weight: 600;" type="submit" onclick="return confirm('Approve this service and make it public?');">
                                        <span class="material-icons" style="font-size: 18px;">check_circle</span> Approve Listing
                                    </button>
                                </form>
                                {{-- Reject/Delete Form --}}
                                <form action="{{ route('admin.services.destroy', $service->id) }}" method="POST" style="margin:0;">
                                    @csrf
                                    @method('DELETE')
                                    <button class="dropdown-item text-danger" type="submit" onclick="return confirm('Reject and delete this application?');">
                                        <span class="material-icons" style="font-size: 18px;">cancel</span> Reject & Delete
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="post-body">
                        <h4 style="font-weight: 700; color: #333; margin: 0 0 5px 0; font-size: 18px;">{{ $service->business_name }}</h4>
                        @if($service->description)
                            <p style="color: var(--text-muted); font-size: 14px; margin-bottom: 10px;">{{ $service->description }}</p>
                        @endif
                        <strong style="color: var(--primary); font-size: 14px;">☎ {{ $service->contact_number }}</strong>
                        
                        <div style="margin-top: 15px; font-size: 12px; color: #888;">
                            Submitted by User ID: {{ $service->user_id ?? 'System' }} | Awaiting Payment
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div style="text-align: center; color: var(--text-muted); font-weight: 500; padding: 20px; background: var(--card-bg); border-radius: 15px; border: 1px dashed #ccc;">
                No pending applications.
            </div>
        @endforelse
    </div>

    {{-- 3. APPROVED SERVICE DIRECTORY --}}
    <div style="display:flex; align-items:center; gap:8px; margin-bottom:15px; color:var(--primary);">
        <span class="material-icons">business_center</span>
        <label class="edit-label" style="font-size:14px; margin:0;">ACTIVE DIRECTORY</label>
    </div>

    <div class="services-feed">
        @forelse($approvedServices as $service)
            <div class="post-card" id="service-{{ $service->id }}" style="border-left: 4px solid #28a745;">
                <div class="post-content">
                    <div class="post-header">
                        <div class="report-details" style="color: #28a745;">
                            {{ strtoupper($service->services_offered) }} • 
                            <span class="location">{{ $service->address }}</span>
                        </div>
                        
                        <div class="dropdown">
                            <button class="dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false">
                                <span class="material-icons">more_horiz</span>
                            </button>
                            <div class="dropdown-menu dropdown-menu-right">
                                <form action="{{ route('admin.services.destroy', $service->id) }}" method="POST" style="margin:0;">
                                    @csrf
                                    @method('DELETE')
                                    <button class="dropdown-item text-danger" type="submit" onclick="return confirm('Remove this service from the active directory?');">
                                        <span class="material-icons" style="font-size: 18px;">delete</span> Remove Business
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="post-body">
                        <h4 style="font-weight: 700; color: #333; margin: 0 0 5px 0; font-size: 18px;">{{ $service->business_name }}</h4>
                        @if($service->description)
                            <p style="color: var(--text-muted); font-size: 14px; margin-bottom: 15px;">{{ $service->description }}</p>
                        @endif

                        <div style="background: #f8f9fa; border: 1px solid var(--border-light); border-radius: 12px; padding: 12px 15px; display: flex; align-items: center; justify-content: space-between; margin-top: 15px;">
                            <div style="display: flex; align-items: center; gap: 10px;">
                                <div style="background: rgba(40, 167, 69, 0.1); color: #28a745; width: 36px; height: 36px; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                                    <span class="material-icons" style="font-size: 18px;">call</span>
                                </div>
                                <div>
                                    <div style="font-size: 11px; font-weight: 700; color: var(--text-muted); letter-spacing: 0.5px;">EMERGENCY / HOTLINE</div>
                                    <strong style="color: #28a745; font-size: 16px;">{{ $service->contact_number }}</strong>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div style="text-align: center; color: var(--text-muted); font-weight: 500; padding: 40px; background: var(--card-bg); border-radius: 20px; border: 1px dashed #ccc;">
                <span class="material-icons" style="font-size: 48px; color: #ddd; display: block; margin-bottom: 10px;">storefront</span>
                No active services in the directory.
            </div>
        @endforelse
    </div>

</div>

<style>
    :root {
        --primary: #494ca2;
        --accent: #CF0F47;
        --accent-hover: #FF0B55;
        --card-bg: #ffffff;
        --text-muted: #666;
        --border-light: #f0f0f0;
    }

    .edit-wrapper, .post-card, input, textarea, button, .edit-label, .report-details {
        font-family: 'Poppins', sans-serif !important;
    }

    .edit-wrapper {
        width: 100%;
        max-width: 550px; 
        margin: 0 auto;
        padding-bottom: 50px;
    }

    .post-card {
        background: var(--card-bg);
        border-radius: 20px; 
        box-shadow: 0 6px 20px rgba(0,0,0,0.06);
        border: 1px solid #eee;
        margin: 0 auto 15px auto;
        width: 100%;
        position: relative;
        transition: all 0.25s ease;
    }
    .post-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 28px rgba(0,0,0,0.1);
    }
    .post-content { padding: 1.5rem; }

    .post-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 0.5rem;
        position: relative;
        z-index: 5;
    }

    .report-details {
        font-size: 12px;
        font-weight: 700;
        text-transform: uppercase;
    }
    .report-details .location {
        font-weight: 600;
        color: #333;
        text-transform: none;
    }

    .edit-label { font-size: 11px; font-weight: 700; color: var(--text-muted); letter-spacing: 0.8px; }
    .admin-badge { background: var(--primary); color: white; padding: 3px 10px; border-radius: 6px; font-size: 10px; font-weight: 700; }
    .input-row-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; }
    .field-label-small { font-size: 10px; font-weight: 700; color: var(--primary); margin-bottom: 4px; }
    
    .edit-input-underlined {
        width: 100%; border: none; border-bottom: 2px solid #f0f0f0; padding: 8px 0;
        font-size: 14px; font-weight: 600; outline: none; background: transparent; transition: 0.3s;
    }
    .edit-input-underlined:focus { border-bottom-color: var(--accent); }
    
    .edit-textarea {
        width: 100%; border: none; resize: none; font-size: 14px; color: #333;
        padding: 10px 0; outline: none; background: transparent; border-bottom: 2px solid #f0f0f0;
    }

    .btn-save {
        background: var(--accent); color: white; border: none; padding: 10px 25px;
        border-radius: 50px; font-weight: 600; cursor: pointer; transition: 0.3s;
    }
    .btn-save:hover { background: var(--accent-hover); transform: translateY(-2px); }
    .btn-cancel { color: var(--text-muted); font-weight: 600; font-size: 14px; text-decoration: none; margin-right: 15px; }

    .alert-box { padding: 12px 15px; border-radius: 12px; margin-bottom: 20px; display: flex; align-items: center; gap: 10px; font-weight: 500; font-size: 14px; }
    .alert-box.success { background: #e8f5e9; color: #2e7d32; }
    .alert-box.error { background: #ffebee; color: #c62828; }

    .dropdown { position: relative !important; z-index: 1000; }
    .dropdown-toggle { background: transparent !important; border: none !important; padding: 0 !important; cursor: pointer !important; color: var(--text-muted) !important; box-shadow: none !important; }
    .dropdown-toggle::after { display: none !important; }
    .dropdown-menu { border-radius: 8px; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15); border: 1px solid rgba(0, 0, 0, 0.05); padding: 8px 0; font-size: 14px; min-width: 180px; }
    .dropdown-item { display: flex; align-items: center; gap: 8px; padding: 8px 16px; cursor: pointer; font-family: 'Poppins', sans-serif; background: transparent; border: none; width: 100%; text-align: left; }
    .dropdown-item:hover { background: #f8f9fa; }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const toggles = document.querySelectorAll('.dropdown-toggle');
    toggles.forEach(toggle => {
        toggle.addEventListener('click', function(e) {
            e.preventDefault();
            const menu = this.nextElementSibling;
            const isShowing = menu.classList.contains('show');
            
            document.querySelectorAll('.dropdown-menu').forEach(m => m.classList.remove('show'));
            
            if (!isShowing) {
                menu.classList.add('show');
                menu.style.position = 'absolute';
                menu.style.top = '100%';
                menu.style.right = '0';
                menu.style.display = 'block';
            } else {
                menu.style.display = 'none';
            }
        });
    });

    document.addEventListener('click', function(e) {
        if (!e.target.closest('.dropdown')) {
            document.querySelectorAll('.dropdown-menu').forEach(m => {
                m.classList.remove('show');
                m.style.display = 'none';
            });
        }
    });
});
</script>
@endsection