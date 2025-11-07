{{-- resources/views/admin/users.blade.php --}}
@extends('layouts.admin')

@section('title', 'Manage Users')

@section('content')
@php
    use Illuminate\Support\Str;
    $adminId = optional(Auth::guard('admin')->user())->id;
@endphp

<style>
:root {
  --bg-primary: #0B1416;
  --bg-secondary: #1A1A1B;
  --bg-hover: #272729;
  --border: #343536;
  --text-primary: #D7DADC;
  --text-secondary: #818384;
  --accent-blue: #0079D3;
  --accent-red: #FF4500;
  --accent-green: #46D160;
  --card-radius: 8px;
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
  margin-bottom: 24px;
}

.page-header h1 {
  font-size: 28px;
  font-weight: 500;
  margin: 0 0 8px 0;
  color: var(--text-primary);
}

.page-header .subtitle {
  color: var(--text-secondary);
  font-size: 14px;
  line-height: 1.5;
}

/* Filter Bar */
.filter-bar {
  display: flex;
  gap: 12px;
  align-items: center;
  margin-bottom: 16px;
  flex-wrap: wrap;
}

.filter-bar input[type="search"],
.filter-bar select {
  background: var(--bg-secondary);
  border: 1px solid var(--border);
  border-radius: 20px;
  padding: 8px 16px;
  color: var(--text-primary);
  font-size: 14px;
  outline: none;
  transition: all 0.2s;
}

.filter-bar input[type="search"] {
  flex: 1;
  min-width: 200px;
  max-width: 400px;
}

.filter-bar input[type="search"]:focus,
.filter-bar select:focus {
  border-color: var(--accent-blue);
  background: var(--bg-hover);
}

.filter-bar select {
  padding-right: 32px;
  cursor: pointer;
}

.filter-btn {
  background: var(--accent-blue);
  color: white;
  border: none;
  border-radius: 20px;
  padding: 8px 24px;
  font-size: 14px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s;
}

.filter-btn:hover {
  background: #0068B8;
}

/* User Cards */
.users-list {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.user-card {
  background: var(--bg-secondary);
  border: 1px solid var(--border);
  border-radius: var(--card-radius);
  padding: 16px;
  transition: all 0.2s;
  display: flex;
  align-items: center;
  gap: 16px;
}

.user-card:hover {
  background: var(--bg-hover);
  border-color: var(--text-secondary);
}

/* Avatar */
.user-avatar {
  width: 48px;
  height: 48px;
  border-radius: 50%;
  background: linear-gradient(135deg, #FF4500, #FF8717);
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  font-weight: 700;
  font-size: 18px;
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
}

.user-name {
  font-size: 14px;
  font-weight: 600;
  color: var(--text-primary);
  margin: 0 0 4px 0;
  display: flex;
  align-items: center;
  gap: 8px;
}

.user-email {
  font-size: 12px;
  color: var(--text-secondary);
  margin: 0 0 4px 0;
}

.user-meta {
  font-size: 12px;
  color: var(--text-secondary);
  display: flex;
  gap: 12px;
  flex-wrap: wrap;
}

/* Badges & Pills */
.role-badge {
  display: inline-block;
  background: rgba(255, 255, 255, 0.1);
  padding: 2px 8px;
  border-radius: 12px;
  font-size: 11px;
  font-weight: 600;
  text-transform: uppercase;
  color: var(--text-secondary);
}

.status-badge {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  padding: 4px 12px;
  border-radius: 16px;
  font-size: 12px;
  font-weight: 600;
  white-space: nowrap;
}

.status-badge.active {
  background: rgba(70, 209, 96, 0.15);
  color: var(--accent-green);
}

.status-badge.suspended {
  background: rgba(255, 159, 10, 0.15);
  color: #FF9F0A;
}

.status-badge.banned {
  background: rgba(255, 69, 0, 0.15);
  color: var(--accent-red);
}

.status-badge::before {
  content: '';
  width: 8px;
  height: 8px;
  border-radius: 50%;
  background: currentColor;
}

/* Stats */
.user-stats {
  display: flex;
  align-items: center;
  gap: 16px;
  padding: 0 16px;
}

.stat-item {
  text-align: center;
  min-width: 60px;
}

.stat-value {
  display: block;
  font-size: 16px;
  font-weight: 700;
  color: var(--text-primary);
}

.stat-label {
  display: block;
  font-size: 11px;
  color: var(--text-secondary);
  text-transform: uppercase;
  margin-top: 2px;
}

/* Actions */
.user-actions {
  display: flex;
  gap: 8px;
  flex-wrap: wrap;
  align-items: center;
}

.action-btn {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  padding: 6px 12px;
  border-radius: 20px;
  font-size: 13px;
  font-weight: 600;
  border: 1px solid var(--border);
  background: transparent;
  color: var(--text-primary);
  cursor: pointer;
  transition: all 0.2s;
  white-space: nowrap;
}

.action-btn:hover:not(:disabled) {
  background: var(--bg-hover);
  border-color: var(--text-primary);
}

.action-btn:disabled {
  opacity: 0.4;
  cursor: not-allowed;
}

.action-btn.view {
  color: var(--accent-blue);
  border-color: var(--accent-blue);
}

.action-btn.view:hover:not(:disabled) {
  background: rgba(0, 121, 211, 0.1);
}

.action-btn.suspend {
  color: #FF9F0A;
  border-color: #FF9F0A;
}

.action-btn.suspend:hover:not(:disabled) {
  background: rgba(255, 159, 10, 0.1);
}

.action-btn.ban {
  color: var(--accent-red);
  border-color: var(--accent-red);
}

.action-btn.ban:hover:not(:disabled) {
  background: rgba(255, 69, 0, 0.1);
}

.action-btn.restore {
  color: var(--accent-green);
  border-color: var(--accent-green);
}

.action-btn.restore:hover:not(:disabled) {
  background: rgba(70, 209, 96, 0.1);
}

.action-btn.delete {
  color: var(--accent-red);
}

.action-btn .material-icons {
  font-size: 16px;
}

/* Empty State */
.empty-state {
  text-align: center;
  padding: 60px 20px;
  color: var(--text-secondary);
}

.empty-state .material-icons {
  font-size: 64px;
  opacity: 0.3;
  margin-bottom: 16px;
}

/* Pagination */
.pagination-wrapper {
  margin-top: 24px;
  padding: 16px;
  text-align: center;
}

/* Responsive Design */
@media (max-width: 1024px) {
  .user-stats {
    display: none;
  }
  
  .user-actions {
    flex-direction: column;
    align-items: stretch;
  }
  
  .action-btn {
    justify-content: center;
    width: 100%;
  }
}

@media (max-width: 768px) {
  .users-container {
    padding: 12px;
  }
  
  .page-header h1 {
    font-size: 22px;
  }
  
  .filter-bar {
    flex-direction: column;
    align-items: stretch;
  }
  
  .filter-bar input[type="search"],
  .filter-bar select {
    max-width: 100%;
    width: 100%;
  }
  
  .user-card {
    flex-direction: column;
    align-items: flex-start;
    padding: 12px;
  }
  
  .user-info {
    width: 100%;
  }
  
  .user-actions {
    width: 100%;
    margin-top: 8px;
  }
  
  .user-meta {
    font-size: 11px;
  }
}

@media (max-width: 480px) {
  .user-avatar {
    width: 40px;
    height: 40px;
    font-size: 16px;
  }
  
  .action-btn {
    padding: 8px 12px;
    font-size: 12px;
  }
  
  .action-btn .material-icons {
    font-size: 14px;
  }
}
</style>

<div class="users-container">
  <div class="page-header">
    <h1>Manage Users</h1>
    <div class="subtitle">View and moderate user accounts across your platform</div>
  </div>

  <form method="get" action="{{ route('admin.users.index') }}">
    <div class="filter-bar">
      <input 
        type="search" 
        name="q" 
        placeholder="Search users..." 
        value="{{ request('q') }}"
        aria-label="Search users by name or email"
      >
      
      <select name="status" aria-label="Filter by status">
        <option value="">All Status</option>
        <option value="active" {{ request('status')=='active' ? 'selected' : '' }}>Active</option>
        <option value="suspended" {{ request('status')=='suspended' ? 'selected' : '' }}>Suspended</option>
        <option value="banned" {{ request('status')=='banned' ? 'selected' : '' }}>Banned</option>
      </select>
      
      <button type="submit" class="filter-btn">Filter</button>
    </div>
  </form>

  @if(empty($users) || ($users instanceof \Illuminate\Support\Collection && $users->isEmpty()))
    <div class="empty-state">
      <span class="material-icons">people_outline</span>
      <div>No users found</div>
    </div>
  @else
    <div class="users-list" role="list" aria-label="Users list">
      @foreach($users as $user)
        @php
          $status = $user->status ?? 'active';
          $isSelfAdmin = ($adminId && ($adminId === ($user->id ?? null)));
        @endphp
        
        <div class="user-card" id="user-row-{{ $user->id }}" role="listitem">
          <div class="user-avatar" aria-hidden="true">
            @if(!empty($user->avatar))
              <img src="{{ asset('storage/'.$user->avatar) }}" alt="{{ $user->name }}">
            @else
              {{ strtoupper(substr($user->name ?? 'U',0,1)) }}
            @endif
          </div>

          <div class="user-info">
            <div class="user-name">
              {{ $user->name ?? '—' }}
              <span class="role-badge">{{ $user->role ?? 'user' }}</span>
            </div>
            <div class="user-email">{{ $user->email ?? '—' }}</div>
            <div class="user-meta">
              <span>Joined {{ optional($user->created_at)->diffForHumans() }}</span>
              <span>•</span>
              <span>{{ $user->posts_count ?? ($user->posts->count() ?? 0) }} posts</span>
            </div>
          </div>

          <div class="user-stats">
            <div class="stat-item">
              <span class="stat-value">{{ $user->posts_count ?? ($user->posts->count() ?? 0) }}</span>
              <span class="stat-label">Posts</span>
            </div>
          </div>

          <div class="status-badge {{ $status }}">
            {{ ucfirst($status) }}
          </div>

          <div class="user-actions" role="group" aria-label="Actions for {{ $user->name }}">
            <a 
              href="{{ route('admin.users.show', ['user' => $user->id]) ?? '#' }}" 
              class="action-btn view"
              title="View user"
            >
              <span class="material-icons">visibility</span>
              <span>View</span>
            </a>

            <button
              class="action-btn suspend btn-suspend"
              title="Suspend user"
              data-action="{{ route('admin.users.suspend', ['user' => $user->id]) }}"
              data-user-id="{{ $user->id }}"
              {{ $isSelfAdmin ? 'disabled' : '' }}
            >
              <span class="material-icons">pause_circle</span>
              <span>Suspend</span>
            </button>

            <button
              class="action-btn ban btn-ban"
              title="Ban user"
              data-action="{{ route('admin.users.ban', ['user' => $user->id]) }}"
              data-user-id="{{ $user->id }}"
              {{ $isSelfAdmin ? 'disabled' : '' }}
            >
              <span class="material-icons">block</span>
              <span>Ban</span>
            </button>

            <button
              class="action-btn restore btn-restore"
              title="Restore user"
              data-action="{{ route('admin.users.restore', ['user' => $user->id]) }}"
              data-user-id="{{ $user->id }}"
              {{ ($status === 'active' || $isSelfAdmin) ? 'disabled' : '' }}
            >
              <span class="material-icons">refresh</span>
              <span>Restore</span>
            </button>

            <form 
              method="POST" 
              action="{{ route('admin.users.destroy', ['user' => $user->id]) }}" 
              style="display:inline-block; margin: 0;"
            >
              @csrf
              @method('DELETE')
              <button 
                type="submit" 
                class="action-btn delete" 
                title="Delete user" 
                onclick="return confirm('Delete this user? This action cannot be undone.');" 
                {{ $isSelfAdmin ? 'disabled' : '' }}
              >
                <span class="material-icons">delete_outline</span>
                <span>Delete</span>
              </button>
            </form>
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

@push('scripts')
<script>
(function(){
  'use strict';

  if (typeof $ === 'undefined') {
    console.error('jQuery required for admin users actions.');
    return;
  }

  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
      'Accept': 'application/json'
    }
  });

  function handleAction($btn, opts) {
    opts = opts || {};
    const action = $btn.data('action');
    const userId = $btn.data('user-id');

    if (!action) {
      console.error('No action URL found on button', $btn);
      return;
    }

    const defaultConfirm = opts.confirmMessage || 'Are you sure you want to perform this action?';
    if (!confirm(defaultConfirm)) return;

    $btn.prop('disabled', true).css('opacity', 0.6);

    $.ajax({
      url: action,
      type: 'POST',
      dataType: 'json',
      success: function(res) {
        if (res && res.success) {
          const $statusBadge = $('#user-row-' + userId + ' .status-badge');
          
          if ($btn.hasClass('btn-suspend')) {
            $statusBadge.removeClass('active banned').addClass('suspended').text('Suspended');
            $btn.prop('disabled', true);
            $('#user-row-' + userId + ' .btn-restore').prop('disabled', false).css('opacity', 1);
          } else if ($btn.hasClass('btn-ban')) {
            $statusBadge.removeClass('active suspended').addClass('banned').text('Banned');
            $('#user-row-' + userId + ' .btn-suspend').prop('disabled', true);
            $('#user-row-' + userId + ' .btn-restore').prop('disabled', false).css('opacity', 1);
          } else if ($btn.hasClass('btn-restore')) {
            $statusBadge.removeClass('suspended banned').addClass('active').text('Active');
            $('#user-row-' + userId + ' .btn-suspend, #user-row-' + userId + ' .btn-ban').prop('disabled', false).css('opacity', 1);
            $btn.prop('disabled', true);
          }
          
          alert(res.message || 'Action completed successfully.');
        } else {
          alert(res && res.message ? res.message : 'Unexpected response from server.');
        }
      },
      error: function(xhr) {
        console.error('Action failed', xhr.responseText || xhr.statusText);
        var msg = 'Action failed. ';
        try {
          var json = JSON.parse(xhr.responseText);
          if (json && json.message) msg += json.message;
        } catch(err){}
        alert(msg);
      },
      complete: function() {
        $btn.prop('disabled', false).css('opacity', 1);
      }
    });
  }

  $(document).on('click', '.btn-suspend', function(e){
    e.preventDefault();
    handleAction($(this), { confirmMessage: 'Suspend this user? They will be prevented from logging in.' });
  });

  $(document).on('click', '.btn-ban', function(e){
    e.preventDefault();
    handleAction($(this), { confirmMessage: 'Ban this user permanently?' });
  });

  $(document).on('click', '.btn-restore', function(e){
    e.preventDefault();
    handleAction($(this), { confirmMessage: 'Restore this user to active status?' });
  });

  $(document).on('keydown', '.action-btn', function(e){
    if (e.key === 'Enter' || e.key === ' ') {
      e.preventDefault();
      $(this).trigger('click');
    }
  });

})();
</script>
@endpush

@endsection