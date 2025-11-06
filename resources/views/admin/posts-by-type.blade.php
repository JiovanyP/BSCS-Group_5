{{-- resources/views/admin/posts-by-type.blade.php --}}
@extends('layouts.admin')

@section('title', 'Posts by Accident Type: ' . ucfirst($type))

@section('content')
<style>
/* Reuse admin dashboard variables for consistency */
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

/* Posts list layout */
.posts-list { display:block; }
.posts-header { display:flex; justify-content:space-between; align-items:flex-start; gap:12px; margin-bottom:18px; }
.posts-title h1 { margin:0; font-size:20px; }
.posts-meta { color:var(--muted); margin-top:6px; font-size:13px; }

.posts-grid { display:grid; gap:12px; }
.post-card { background:var(--panel); border-radius:12px; padding:14px; box-shadow:var(--card-shadow); }
.post-card h3 { margin:0 0 8px 0; color:#eaf2fa; font-size:16px; }
.post-card .post-meta { color:var(--muted); font-size:13px; margin-bottom:8px; }
.post-card .post-content { color:#eaf2fa; margin-bottom:8px; }
.post-card .post-stats { display:flex; gap:12px; font-size:13px; color:var(--muted); }

/* Pagination */
.pagination { display:flex; justify-content:center; margin-top:20px; }
.pagination a { color:#eaf2fa; padding:8px 12px; margin:0 4px; background:var(--panel); border-radius:6px; text-decoration:none; }
.pagination a:hover { background:var(--accent); }
.pagination .active { background:var(--accent); }

/* Responsive */
@media (max-width:720px) {
  .posts-grid { grid-template-columns: 1fr; }
}
</style>

<div class="posts-list">
  <div class="posts-header">
    <div class="posts-title">
      <h1>Posts by Accident Type: {{ ucfirst($type) }}</h1>
      <div class="posts-meta">Showing posts related to {{ $type }} accidents</div>
    </div>
    <a href="{{ route('analytics') }}" class="btn btn-secondary">← Back to Analytics</a>
  </div>

  <div class="posts-grid">
    @forelse($posts as $post)
      <div class="post-card">
        <h3>{{ $post->title }}</h3>
        <div class="post-meta">
          By {{ $post->user->name }} • {{ $post->created_at->format('M j, Y') }}
          @if($post->location)
            • {{ $post->location }}
          @endif
        </div>
        <div class="post-content">
          {{ Str::limit($post->content, 200) }}
        </div>
        <div class="post-stats">
          <span>👍 {{ $post->likes_count ?? 0 }}</span>
          <span>💬 {{ $post->comments_count ?? 0 }}</span>
          @if($post->media_type)
            <span>📎 {{ ucfirst($post->media_type) }}</span>
          @endif
        </div>
      </div>
    @empty
      <div class="post-card">
        <h3>No posts found</h3>
        <div class="post-meta">There are no posts for this accident type yet.</div>
      </div>
    @endforelse
  </div>

  @if($posts->hasPages())
    <div class="pagination">
      {{ $posts->links() }}
    </div>
  @endif
</div>
@endsection
