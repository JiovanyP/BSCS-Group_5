{{-- resources/views/admin/users.blade.php --}}
@extends('layouts.admin')

@section('title', 'Manage Users')

@section('content')
@php
    use Illuminate\Support\Str;
    $adminId = optional(Auth::guard('admin')->user())->id;
@endphp

<style>
/* Reuse admin dashboard variables (keeps look consistent) */
:root {
  --bg: #071018;
  --panel: rgba(255,255,255,0.02);
  --muted: #98a0a8;
  --accent: #CF0F47;
  --green: #17b06b;
  --red: #ea4d4d;
  --blue: #1482e8;
  --card-radius: 12px;
  --card-shadow: 0 10px 30px rgba(0,0,0,0.6);
}

.users-wrap { display:block; }
.header-row { display:flex; justify-content:space-between; align-items:center; gap:12px; margin-bottom:18px; }
.header-row h1 { margin:0; font-size:20px; }
.header-row .meta { color:var(--muted); font-size:13px; }

.users-card { background:var(--panel); border-radius:12px; padding:12px; box-shadow:var(--card-shadow); }

/* table */
.users-table { width:100%; border-collapse:collapse; color:#eaf2fa; font-weight:700; }
.users-table th, .users-table td { padding:10px 12px; border-top:1px solid rgba(255,255,255,0.02); text-align:left; vertical-align:middle; }
.users-table th { color:var(--muted); font-weight:800; font-size:13px; }
.avatar { width:40px; height:40px; border-radius:50%; display:inline-flex; align-items:center; justify-content:center; overflow:hidden; background:rgba(255,255,255,0.02); color:var(--muted); margin-right:8px; }
.user-row { transition: background .08s ease; }
.user-row:hover { background: rgba(255,255,255,0.01); }

/* badges */
.badge { display:inline-block; padding:6px 8px; border-radius:8px; font-size:12px; font-weight:800; }
.badge.active { background: rgba(23,176,107,0.12); color:var(--green); }
.badge.suspended { background: rgba(234,77,77,0.08); color:var(--red); }
.badge.banned { background: rgba(255,100,100,0.06); color:var(--red); }
.role-pill { background: rgba(255,255,255,0.02); color:var(--muted); padding:4px 8px; border-radius:8px; font-weight:700; font-size:12px; }

/* action buttons */
.actions { display:flex; gap:8px; justify-content:flex-end; }
.action-btn {
  border: none;
  padding:8px 10px;
  border-radius:8px;
  cursor:pointer;
  background: rgba(255,255,255,0.03);
  color:#fff;
  font-weight:700;
  display:inline-flex;
  gap:8px;
  align-items:center;
}
.action-btn:disabled { opacity:0.5; cursor:not-allowed; }

/* colored actions */
.action-suspend { background: linear-gradient(180deg,var(--blue), #076fb8); }
.action-ban { background: linear-gradient(180deg,var(--red), #c43932); }
.action-restore { background: linear-gradient(180deg,var(--green), #12a85b); }
.action-delete { background: rgba(255,255,255,0.02); color:var(--red); border:1px solid rgba(255,255,255,0.03); }

/* responsive - stack on small screens */
@media (max-width:920px) {
  .users-table thead { display:none; }
  .users-table tr { display:block; margin-bottom:12px; border-radius:10px; background:var(--panel); padding:10px; }
  .users-table td { display:flex; justify-content:space-between; padding:8px 10px; border-top:0; }
  .actions { justify-content:flex-start; margin-top:8px; }
}
</style>

<div class="users-wrap">
  <div class="header-row">
    <div>
      <h1>Manage Users</h1>
      <div class="meta">List users, suspend, ban, restore, or remove accounts</div>
    </div>

    <div style="display:flex; gap:8px; align-items:center;">
      <form method="get" action="{{ route('admin.users') }}" style="display:flex; gap:8px; align-items:center;">
        <input type="search" name="q" placeholder="Search name or email" value="{{ request('q') }}" style="background:transparent; border:1px solid rgba(255,255,255,0.03); padding:8px 10px; border-radius:8px; color:#fff;">
        <select name="status" style="background:transparent; border:1px solid rgba(255,255,255,0.03); padding:8px 10px; border-radius:8px; color:#fff;">
          <option value="">All status</option>
          <option value="active" {{ request('status')=='active' ? 'selected' : '' }}>Active</option>
          <option value="suspended" {{ request('status')=='suspended' ? 'selected' : '' }}>Suspended</option>
          <option value="banned" {{ request('status')=='banned' ? 'selected' : '' }}>Banned</option>
        </select>
        <button type="submit" class="action-btn" style="padding:8px 12px;">Filter</button>
      </form>
    </div>
  </div>

  <div class="users-card" role="region" aria-label="User management">
    @if(empty($users) || ($users instanceof \Illuminate\Support\Collection && $users->isEmpty()))
      <div style="padding:18px; color:var(--muted)">No users found.</div>
    @else
      <table class="users-table" role="table" aria-label="Users table">
        <thead>
          <tr>
            <th style="width:36px"></th>
            <th>Name / Email</th>
            <th style="width:140px">Role</th>
            <th style="width:120px">Status</th>
            <th style="width:110px; text-align:right;">Posts</th>
            <th style="width:190px; text-align:right;">Actions</th>
          </tr>
        </thead>
        <tbody>
          @foreach($users as $user)
            @php
              $status = $user->status ?? 'active';
              $isSelfAdmin = ($adminId && ($adminId === ($user->id ?? null)));
            @endphp
            <tr class="user-row" id="user-row-{{ $user->id }}">
              <td>
                <div class="avatar" aria-hidden="true">
                  @if(!empty($user->avatar))
                    <img src="{{ asset('storage/'.$user->avatar) }}" alt="{{ $user->name }}" style="width:100%; height:100%; object-fit:cover;">
                  @else
                    {{ strtoupper(substr($user->name ?? 'U',0,1)) }}
                  @endif
                </div>
              </td>

              <td>
                <div style="font-weight:800;">{{ $user->name ?? '—' }}</div>
                <div style="color:var(--muted); font-size:13px;">{{ $user->email ?? '—' }}</div>
                <div style="color:var(--muted); font-size:12px; margin-top:6px;">Joined {{ optional($user->created_at)->diffForHumans() }}</div>
              </td>

              <td>
                <span class="role-pill">{{ $user->role ?? 'user' }}</span>
              </td>

              <td>
                @if($status === 'active')
                  <span class="badge active">Active</span>
                @elseif($status === 'suspended')
                  <span class="badge suspended">Suspended</span>
                @elseif($status === 'banned')
                  <span class="badge banned">Banned</span>
                @else
                  <span class="badge" style="background:rgba(255,255,255,0.02)">{{ ucfirst($status) }}</span>
                @endif
              </td>

              <td style="text-align:right;">
                <div>{{ $user->posts_count ?? ($user->posts->count() ?? 0) }}</div>
              </td>

              <td style="text-align:right;">
                <div class="actions" role="group" aria-label="User actions for {{ $user->name }}">
                  {{-- View (non-destructive) --}}
                  <a href="{{ route('admin.users.show', ['user' => $user->id]) ?? '#' }}" class="action-btn" title="View user details">
                    <span class="material-icons" aria-hidden="true">visibility</span> View
                  </a>

                  {{-- Suspend --}}
                  <button
                    class="action-btn action-suspend btn-suspend"
                    title="Suspend user"
                    data-action="{{ route('admin.users.suspend', ['user' => $user->id]) }}"
                    data-user-id="{{ $user->id }}"
                    {{ $isSelfAdmin ? 'disabled' : '' }}
                    aria-disabled="{{ $isSelfAdmin ? 'true' : 'false' }}"
                  >
                    <span class="material-icons" aria-hidden="true">pause_circle</span> Suspend
                  </button>

                  {{-- Ban --}}
                  <button
                    class="action-btn action-ban btn-ban"
                    title="Ban user"
                    data-action="{{ route('admin.users.ban', ['user' => $user->id]) }}"
                    data-user-id="{{ $user->id }}"
                    {{ $isSelfAdmin ? 'disabled' : '' }}
                    aria-disabled="{{ $isSelfAdmin ? 'true' : 'false' }}"
                  >
                    <span class="material-icons" aria-hidden="true">gavel</span> Ban
                  </button>

                  {{-- Restore --}}
                  <button
                    class="action-btn action-restore btn-restore"
                    title="Restore user"
                    data-action="{{ route('admin.users.restore', ['user' => $user->id]) }}"
                    data-user-id="{{ $user->id }}"
                    {{ ($status === 'active' || $isSelfAdmin) ? 'disabled' : '' }}
                    aria-disabled="{{ ($status === 'active' || $isSelfAdmin) ? 'true' : 'false' }}"
                  >
                    <span class="material-icons" aria-hidden="true">autorenew</span> Restore
                  </button>

                  {{-- Delete (destructive) --}}
                  <form method="POST" action="{{ route('admin.users.destroy', ['user' => $user->id]) }}" style="display:inline-block;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="action-btn action-delete" title="Delete user" onclick="return confirm('Delete this user? This action cannot be undone.');" {{ $isSelfAdmin ? 'disabled' : '' }}>
                      <span class="material-icons" aria-hidden="true">delete</span> Delete
                    </button>
                  </form>
                </div>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>

      <div style="margin-top:10px; color:var(--muted);">
        @if(method_exists($users,'links'))
          {{ $users->links() }}
        @endif
      </div>
    @endif
  </div>
</div>

@push('scripts')
<script>
(function(){
  'use strict';

  if (typeof $ === 'undefined') {
    console.error('jQuery required for admin users actions.');
    return;
  }

  // Setup CSRF header for AJAX requests
  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
      'Accept': 'application/json'
    }
  });

  // Generic action helper
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

    $btn.prop('disabled', true).css('opacity',0.6);

    $.ajax({
      url: action,
      type: 'POST',
      dataType: 'json',
      success: function(res) {
        if (res && res.success) {
          if ($btn.hasClass('btn-suspend')) {
            $('#user-row-' + userId + ' .badge').removeClass('active suspended banned').addClass('suspended').text('Suspended');
            $btn.prop('disabled', true);
            $('#user-row-' + userId + ' .btn-restore').prop('disabled', false).css('opacity',1);
          } else if ($btn.hasClass('btn-ban')) {
            $('#user-row-' + userId + ' .badge').removeClass('active suspended banned').addClass('banned').text('Banned');
            $('#user-row-' + userId + ' .btn-suspend').prop('disabled', true);
            $('#user-row-' + userId + ' .btn-restore').prop('disabled', false);
          } else if ($btn.hasClass('btn-restore')) {
            $('#user-row-' + userId + ' .badge').removeClass('suspended banned active').addClass('active').text('Active');
            $('#user-row-' + userId + ' .btn-suspend, #user-row-' + userId + ' .btn-ban').prop('disabled', false).css('opacity',1);
            $btn.prop('disabled', true);
          }
          alert(res.message || 'Action completed.');
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
        $btn.prop('disabled', false).css('opacity',1);
      }
    });
  }

  // Bind actions
  $(document).on('click', '.btn-suspend', function(e){
    e.preventDefault();
    handleAction($(this), { confirmMessage: 'Suspend this user? They will be prevented from logging in.' });
  });

  $(document).on('click', '.btn-ban', function(e){
    e.preventDefault();
    handleAction($(this), { confirmMessage: 'Ban this user? This will permanently disable the account.' });
  });

  $(document).on('click', '.btn-restore', function(e){
    e.preventDefault();
    handleAction($(this), { confirmMessage: 'Restore this user to active status?' });
  });

  // Keyboard accessibility
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
