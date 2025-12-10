{{-- resources/views/admin/posts/index.blade.php --}}
@extends('layouts.admin')

@section('title', 'Admin Posts')

@section('content')
<style>
:root {
  --accent: #CF0F47;
  --card: #1A1A1B;
  --border: #343536;
  --text-light: #D7DADC;
  --muted: #8a8f94;
}

.page-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 25px;
}

.table-container {
  background: var(--card);
  padding: 25px;
  border-radius: 14px;
  box-shadow: 0 8px 30px rgba(0,0,0,0.4);
}

table {
  width: 100%;
  border-collapse: collapse;
}

table th {
  padding: 14px;
  color: var(--accent);
  text-align: left;
  border-bottom: 1px solid var(--border);
  font-size: 14px;
}

table td {
  padding: 14px;
  color: var(--text-light);
  border-bottom: 1px solid var(--border);
}

.badge-admin {
  display: inline-block;
  padding: 4px 10px;
  background: rgba(255, 105, 180, 0.15);
  color: #FF69B4;
  border-radius: 8px;
  font-size: 12px;
  font-weight: 700;
}

.btn-delete {
  color: #ff6b6b;
  border: 1px solid #ff6b6b;
  padding: 6px 12px;
  text-decoration: none;
  border-radius: 6px;
  font-size: 13px;
  transition: 0.25s;
}

.btn-delete:hover {
  background: rgba(255,70,70,0.2);
}

.empty-state {
  text-align: center;
  color: var(--muted);
  padding: 40px 0;
  font-size: 14px;
}
</style>

<div class="page-header">
  <h1 style="color:var(--accent);">Your Admin Posts</h1>
  <a href="{{ route('admin.posts.create') }}" 
     class="btn btn-primary"
     style="background:var(--accent); border:none; border-radius:8px; padding:10px 16px; font-weight:700;">
     + Create Post
  </a>
</div>

<div class="table-container">

@if ($posts->count() === 0)
  <div class="empty-state">No posts created yet.</div>
@else
<table>
  <thead>
    <tr>
      <th>Type</th>
      <th>Location</th>
      <th>Content</th>
      <th>Media</th>
      <th>Created</th>
      <th>Action</th>
    </tr>
  </thead>

  <tbody>
  @foreach ($posts as $post)
    <tr>
      <td>{{ $post->accident_type }}</td>

      <td>{{ $post->location }}</td>

      <td style="max-width:300px;">
        {{ Str::limit($post->content, 100) }}
      </td>

      <td>
        @if ($post->media_type)
          @if (str_starts_with($post->media_type, 'image'))
            <img src="{{ $post->image_url }}" width="70" style="border-radius:6px;">
          @else
            <span style="color:#6ab0ff;">Video</span>
          @endif
        @else
          —
        @endif
      </td>

      <td>{{ $post->created_at->diffForHumans() }}</td>

      <td>
        <button class="btn-delete" onclick="openDeleteModal({{ $post->id }})">
          Delete
        </button>
      </td>
    </tr>
  @endforeach
  </tbody>
</table>

<div style="margin-top:20px;">
  {{ $posts->links() }}
</div>
@endif

</div>

{{-- DELETE CONFIRM MODAL --}}
<div id="deleteModal" 
     style="display:none; position:fixed; top:0; left:0; width:100%; height:100%;
            background:rgba(0,0,0,0.7); justify-content:center; align-items:center;">
  
  <div style="background:#1A1A1B; padding:25px; border-radius:10px; width:300px; text-align:center;">
    <h3 style="color:#ff6b6b; margin-bottom:10px;">Delete Post?</h3>
    <p style="color:#D7DADC; font-size:14px; margin-bottom:20px;">
      This action cannot be undone.
    </p>

    <form id="deleteForm" method="POST">
      @csrf
      @method('DELETE')

      <button type="submit"
              style="background:#ff4d4d; border:none; padding:10px 14px; color:white; font-weight:700; border-radius:6px; width:100%; margin-bottom:8px;">
        Yes, Delete
      </button>
    </form>

    <button onclick="closeDeleteModal()"
            style="background:#444; border:none; padding:10px 14px; color:white; border-radius:6px; width:100%; font-weight:700;">
      Cancel
    </button>
  </div>
</div>

<script>
function openDeleteModal(id) {
  let modal = document.getElementById('deleteModal');
  let form = document.getElementById('deleteForm');

  form.action = "/admin/posts/" + id;

  modal.style.display = "flex";
}

function closeDeleteModal() {
  document.getElementById('deleteModal').style.display = "none";
}
</script>

@endsection
