{{-- resources/views/admin/users.blade.php --}}
@extends('layouts.admin')

@section('title', 'Manage Users')

@section('content')
@php
    use Illuminate\Support\Str;
@endphp

<style>
:root {
  --bg-primary: #0B1416;
  --bg-secondary: #1A1A1B;
  --bg-hover: #272729;
  --bg-dropdown: #202022;
  --border: #343536;
  --text-primary: #D7DADC;
  --text-secondary: #818384;
  --accent-blue: #0079D3;
  --accent-red: #FF4500;
  --accent-green: #46D160;
  --card-radius: 6px;
}

body {
  background: var(--bg-primary);
  color: var(--text-primary);
}

.users-container {
  max-width: 1400px;
  margin: 0 auto;
  padding: 20px;
}

/* Header Section */
.page-header {
  margin-bottom: 20px;
}

.page-header h1 {
  font-size: 24px;
  font-weight: 500;
  margin: 0 0 4px 0;
  color: var(--text-primary);
}

.page-header .subtitle {
  color: var(--text-secondary);
  font-size: 13px;
}

/* Filter Bar */
.filter-bar {
  display: flex;
  gap: 10px;
  align-items: center;
  margin-bottom: 12px;
  flex-wrap: wrap;
}

.filter-bar input[type="search"],
.filter-bar select {
  background: var(--bg-secondary);
  border: 1px solid var(--border);
  border-radius: 4px;
  padding: 6px 12px;
  color: var(--text-primary);
  font-size: 13px;
  outline: none;
  height: 36px;
}

.filter-bar input[type="search"] {
  flex: 1;
  min-width: 200px;
  max-width: 400px;
}

.filter-btn {
  background: var(--accent-blue);
  color: white;
  border: none;
  border-radius: 4px;
  padding: 0 20px;
  height: 36px;
  font-size: 13px;
  font-weight: 600;
  cursor: pointer;
  transition: background 0.2s;
}

.filter-btn:hover {
  background: #0068B8;
}

/* User Cards */
.users-list {
  display: flex;
  flex-direction: column;
  gap: 6px;
}

.user-card {
  background: var(--bg-secondary);
  border: 1px solid var(--border);
  border-radius: var(--card-radius);
  padding: 10px 14px;
  transition: background 0.2s;
  display: flex;
  align-items: center;
  gap: 14px;
  position: relative;
  z-index: 1; /* Default stack level */
}

/* Lift active row above others so dropdown isn't clipped */
.user-card.row-active {
  z-index: 100 !important;
  border-color: var(--text-secondary);
  background: var(--bg-hover);
}

.user-card:hover {
  background: var(--bg-hover);
  border-color: var(--text-secondary);
}

/* Avatar */
.user-avatar {
  width: 36px;
  height: 36px;
  border-radius: 50%;
  background: linear-gradient(135deg, #FF4500, #FF8717);
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  font-weight: 700;
  font-size: 14px;
  flex-shrink: 0;
  overflow: hidden;
}

.user-avatar img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

/* User Info */
.user-info {
  flex: 1;
  min-width: 0;
  display: flex;
  flex-direction: column;
  justify-content: center;
}

.user-name-row {
  display: flex;
  align-items: center;
  gap: 8px;
  margin-bottom: 2px;
}

.user-name {
  font-size: 14px;
  font-weight: 600;
  color: var(--text-primary);
}

.user-email {
  font-size: 12px;
  color: var(--text-secondary);
}

/* Badges */
.role-badge {
  background: rgba(255, 255, 255, 0.1);
  padding: 1px 6px;
  border-radius: 4px;
  font-size: 10px;
  font-weight: 600;
  text-transform: uppercase;
  color: var(--text-secondary);
}

.status-badge {
  display: inline-flex;
  align-items: center;
  gap: 5px;
  padding: 2px 8px;
  border-radius: 12px;
  font-size: 11px;
  font-weight: 600;
  white-space: nowrap;
  margin-right: 8px;
}

.status-badge.active { color: var(--accent-green); background: rgba(70, 209, 96, 0.1); }
.status-badge.suspended { color: #FF9F0A; background: rgba(255, 159, 10, 0.1); }
.status-badge.banned { color: var(--accent-red); background: rgba(255, 69, 0, 0.1); }

.status-badge::before {
  content: '';
  width: 6px;
  height: 6px;
  border-radius: 50%;
  background: currentColor;
}

/* Stats */
.user-stats {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 0 12px;
  border-right: 1px solid var(--border);
  margin-right: 8px;
}

.stat-value {
  font-size: 13px;
  font-weight: 700;
  color: var(--text-primary);
}

.stat-label {
  font-size: 10px;
  color: var(--text-secondary);
  text-transform: uppercase;
  margin-left: 4px;
}

/* --- Action Menu & Dropdown --- */
.action-menu-container {
  position: relative;
}

.menu-trigger-btn {
  background: transparent;
  border: 1px solid transparent;
  color: var(--text-secondary);
  width: 32px;
  height: 32px;
  border-radius: 4px;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: all 0.2s;
  position: relative;
  z-index: 20;
}

.menu-trigger-btn:hover, .menu-trigger-btn.active {
  background: var(--bg-hover);
  color: var(--text-primary);
  border-color: var(--border);
}

.dropdown-menu {
  position: absolute;
  top: 100%;
  right: 0;   /* Anchors menu to the right edge */
  left: auto; /* Prevents overflow off-screen */
  margin-top: 4px;
  background: var(--bg-dropdown);
  border: 1px solid var(--border);
  border-radius: 6px;
  box-shadow: 0 4px 20px rgba(0,0,0,0.5);
  width: 160px;
  z-index: 200;
  display: none;
  overflow: hidden;
  padding: 4px 0;
}

.dropdown-menu.show {
  display: block;
}

.dropdown-item {
  display: flex;
  align-items: center;
  gap: 10px;
  width: 100%;
  padding: 8px 12px;
  background: transparent;
  border: none;
  color: var(--text-primary);
  font-size: 13px;
  text-align: left;
  cursor: pointer;
  transition: background 0.15s;
  text-decoration: none;
}

.dropdown-item:hover:not(:disabled) {
  background: var(--bg-hover);
}

.dropdown-item:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.dropdown-item .material-icons {
  font-size: 18px;
  color: var(--text-secondary);
}

.dropdown-item:hover .material-icons {
  color: var(--text-primary);
}

.dropdown-item.text-red:hover { background: rgba(255, 69, 0, 0.1); color: var(--accent-red); }
.dropdown-item.text-red .material-icons { color: var(--accent-red); }

.dropdown-divider {
  height: 1px;
  background: var(--border);
  margin: 4px 0;
}

/* Pagination */
.pagination-wrapper {
  margin-top: 20px;
  text-align: center;
}

/* Responsive */
@media (max-width: 768px) {
  .user-stats { display: none; }
  .user-info { flex-direction: column; align-items: flex-start; }
  .user-name-row { flex-wrap: wrap; }
}
</style>

<div class="users-container">
  <div class="page-header">
    <h1>Manage Users</h1>
    <div class="subtitle">Moderate user accounts</div>
  </div>

  <form method="get" action="{{ route('admin.users.index') }}">
    <div class="filter-bar">
      <input 
        type="search" 
        name="q" 
        placeholder="Search..." 
        value="{{ request('q') }}"
      >
      
      <select name="status">
        <option value="">All Status</option>
        <option value="active" {{ request('status')=='active' ? 'selected' : '' }}>Active</option>
        <option value="suspended" {{ request('status')=='suspended' ? 'selected' : '' }}>Suspended</option>
        <option value="banned" {{ request('status')=='banned' ? 'selected' : '' }}>Banned</option>
      </select>
      
      <button type="submit" class="filter-btn">Filter</button>
    </div>
  </form>

  @if(empty($users) || ($users instanceof \Illuminate\Support\Collection && $users->isEmpty()))
    <div class="empty-state" style="text-align:center; padding: 40px; color: var(--text-secondary);">
      <span class="material-icons" style="font-size: 48px; opacity:0.3;">people_outline</span>
      <div>No users found</div>
    </div>
  @else
    <div class="users-list">
      @foreach($users as $user)
        @php
            // 1. Get status directly from User model
            $status = $user->status ?? 'active';
            
            // 2. Safe ID comparison for "Self" check (Loose comparison == handles string/int mismatch)
            $currentAdminId = Auth::guard('admin')->id();
            $isSelf = ($currentAdminId && $currentAdminId == $user->id);

            // 3. Define boolean states for cleaner template logic
            $isSuspended = ($status === 'suspended');
            $isBanned    = ($status === 'banned');
            $isActive    = ($status === 'active');
        @endphp
        
        <div class="user-card" id="user-row-{{ $user->id }}" data-user-id="{{ $user->id }}">
          
          {{-- Avatar --}}
          <div class="user-avatar">
            @if(!empty($user->avatar))
                {{-- Use avatar_url accessor if available, else standard asset path --}}
                <img src="{{ $user->avatar_url ?? asset('storage/'.$user->avatar) }}" alt="{{ $user->name }}">
            @else
                {{ strtoupper(substr($user->name ?? 'U',0,1)) }}
            @endif
          </div>

          {{-- Info --}}
          <div class="user-info">
            <div class="user-name-row">
              <span class="user-name">{{ $user->name ?? '—' }}</span>
              <span class="role-badge">{{ $user->role ?? 'user' }}</span>
              <span class="status-badge {{ $status }}">{{ ucfirst($status) }}</span>
            </div>
            <div class="user-email">{{ $user->email ?? '—' }}</div>
          </div>

          {{-- Stats --}}
          <div class="user-stats">
            <span class="stat-value">{{ $user->posts_count ?? 0 }}</span>
            <span class="stat-label">Posts</span>
          </div>

          {{-- Action Dropdown --}}
          <div class="action-menu-container">
            <button type="button" class="menu-trigger-btn" onclick="toggleDropdown('dropdown-{{ $user->id }}', this, event)">
              <span class="material-icons">more_vert</span>
            </button>

            <div id="dropdown-{{ $user->id }}" class="dropdown-menu">
              
              {{-- View Profile --}}
              <a href="{{ route('admin.users.show', ['user' => $user->id]) }}" class="dropdown-item">
                <span class="material-icons">visibility</span> View Profile
              </a>

              <div class="dropdown-divider"></div>

              {{-- Suspend --}}
              <button
                type="button"
                class="dropdown-item js-action-btn btn-suspend"
                data-url="{{ route('admin.users.suspend', ['user' => $user->id]) }}"
                data-action="suspend"
                data-confirm="Are you sure you want to suspend this user?"
                {{ ($isSelf || $isSuspended || $isBanned) ? 'disabled' : '' }}
                title="{{ $isSelf ? 'Cannot suspend self' : ($isSuspended ? 'Already suspended' : '') }}"
              >
                <span class="material-icons">pause_circle</span> Suspend User
              </button>

              {{-- Ban --}}
              <button
                type="button"
                class="dropdown-item js-action-btn btn-ban"
                data-url="{{ route('admin.users.ban', ['user' => $user->id]) }}"
                data-action="ban"
                data-confirm="Are you sure you want to permanently BAN this user?"
                {{ ($isSelf || $isBanned) ? 'disabled' : '' }}
                title="{{ $isSelf ? 'Cannot ban self' : ($isBanned ? 'Already banned' : '') }}"
              >
                <span class="material-icons">block</span> Ban User
              </button>

              {{-- Restore --}}
              <button
                type="button"
                class="dropdown-item js-action-btn btn-restore"
                data-url="{{ route('admin.users.restore', ['user' => $user->id]) }}"
                data-action="restore"
                data-confirm="Restore this user to active status?"
                {{ ($isSelf || $isActive) ? 'disabled' : '' }}
                title="{{ $isActive ? 'Already active' : '' }}"
              >
                <span class="material-icons">refresh</span> Restore User
              </button>

              <div class="dropdown-divider"></div>

              {{-- Delete --}}
              <form 
                method="POST" 
                action="{{ route('admin.users.destroy', ['user' => $user->id]) }}" 
                style="margin: 0;"
              >
                @csrf
                @method('DELETE')
                <button 
                  type="submit" 
                  class="dropdown-item text-red" 
                  onclick="return confirm('Delete this user? This action cannot be undone.');" 
                  {{ $isSelf ? 'disabled' : '' }}
                  title="{{ $isSelf ? 'Cannot delete self' : '' }}"
                >
                  <span class="material-icons">delete_outline</span> Delete Account
                </button>
              </form>

            </div>
          </div>
        </div>
      @endforeach
    </div>

    <div class="pagination-wrapper">
      @if(method_exists($users,'links'))
        {{ $users->links() }}
      @endif
    </div>
  @endif
</div>

{{-- Inline script to ensure functionality without external deps --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
    
    // --- 1. Dropdown Toggle Logic ---
    window.toggleDropdown = function(id, btn, e) {
        if(e) e.stopPropagation();
        
        // Close others
        document.querySelectorAll('.dropdown-menu.show').forEach(m => {
            if(m.id !== id) m.classList.remove('show');
        });
        document.querySelectorAll('.menu-trigger-btn.active').forEach(b => {
            if(b !== btn) b.classList.remove('active');
        });
        document.querySelectorAll('.user-card.row-active').forEach(r => {
             r.classList.remove('row-active');
        });

        // Toggle current
        const menu = document.getElementById(id);
        const row = btn.closest('.user-card');
        
        if (menu.classList.contains('show')) {
            menu.classList.remove('show');
            btn.classList.remove('active');
            if(row) row.classList.remove('row-active');
        } else {
            menu.classList.add('show');
            btn.classList.add('active');
            if(row) row.classList.add('row-active');
        }
    };

    // Close on click outside
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.action-menu-container')) {
            document.querySelectorAll('.dropdown-menu.show').forEach(m => m.classList.remove('show'));
            document.querySelectorAll('.menu-trigger-btn.active').forEach(b => b.classList.remove('active'));
            document.querySelectorAll('.user-card.row-active').forEach(r => r.classList.remove('row-active'));
        }
    });

    // --- 2. AJAX Action Logic (Vanilla JS) ---
    document.body.addEventListener('click', function(e) {
        // Find the closest button with class 'js-action-btn'
        const btn = e.target.closest('.js-action-btn');
        if (!btn) return;

        // Prevent default behavior
        e.preventDefault();

        // 1. Check Confirmation
        const confirmMsg = btn.dataset.confirm || 'Are you sure?';
        if (!confirm(confirmMsg)) return;

        // 2. Prepare Data
        const url = btn.dataset.url;
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

        if (!url || !csrfToken) {
            alert('Error: Missing URL or CSRF Token');
            return;
        }

        // 3. UI Loading State
        const originalContent = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<span class="material-icons" style="animation: spin 1s linear infinite;">refresh</span> Processing...';

        // 4. Send Request
        fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify({})
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Success: Reload page to reflect changes cleanly
                window.location.reload(); 
            } else {
                alert(data.message || 'Action failed');
                // Reset button on failure
                btn.disabled = false;
                btn.innerHTML = originalContent;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An unexpected error occurred.');
            btn.disabled = false;
            btn.innerHTML = originalContent;
        });
    });

    // Add spinner style dynamically
    const style = document.createElement('style');
    style.innerHTML = `
        @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
    `;
    document.head.appendChild(style);

});
</script>

@endsection