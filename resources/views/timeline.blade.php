@extends('layouts.app')

@section('title', 'Timeline')

@push('styles')
<style>
:root {
  --accent: #CF0F47;
  --accent-2: #FF0B55;
  --card-bg: #ffffff;
  --muted: #666;
  --bg-light: #FFDEDE;
}

body { 
  background: #FF0B55;
  color: #000; 
  font-family: "Helvetica Neue", Arial, sans-serif;
  margin: 0;
  padding: 0;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
}

/* Profile Header */
.profile-header { 
  background: var(--bg-light);
  text-align: center;
  padding: 80px 20px 40px;
  border-radius: 0 0 16px 16px;
  position: relative;
  box-shadow: 0 4px 20px rgba(0,0,0,0.1);
}

.profile-pic-wrapper { 
  position: relative;
  display: inline-block;
}

.profile-header img { 
  width: 120px;
  height: 120px;
  border-radius: 50%;
  border: 4px solid var(--accent-2);
  object-fit: cover;
  transition: all 0.3s ease;
  box-shadow: 0 4px 15px rgba(207, 15, 71, 0.3);
}

.camera-overlay { 
  position: absolute;
  bottom: 5px;
  right: 5px;
  width: 35px;
  height: 35px;
  background: var(--accent-2);
  border: 2px solid #fff;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #fff;
  opacity: 0;
  transition: 0.3s ease;
  cursor: pointer;
  box-shadow: 0 2px 8px rgba(0,0,0,0.2);
}

.camera-overlay i { 
  font-size: 18px;
}

.profile-pic-wrapper:hover .camera-overlay { 
  opacity: 1;
}

.profile-pic-wrapper:hover img { 
  filter: brightness(0.9);
  transform: scale(1.02);
}

.profile-header h3 { 
  margin: 12px 0 0 0;
  font-weight: 700;
  font-size: 24px;
  letter-spacing: 0.3px;
  color: var(--accent);
}

/* Container */
.container {
  max-width: 680px;
  margin: 0 auto;
  padding: 24px 20px;
}

/* Post Form Card */
.post-card {
  background: var(--card-bg);
  border-radius: 16px;
  padding: 24px;
  box-shadow: 0 8px 30px rgba(0,0,0,0.15);
  margin-bottom: 24px;
}

.post-card textarea {
  width: 100%;
  padding: 12px;
  border-radius: 10px;
  border: 1px solid #ddd;
  margin-bottom: 12px;
  box-sizing: border-box;
  font-size: 14px;
  background: #fbfbfb;
  font-family: "Helvetica Neue", Arial, sans-serif;
  resize: vertical;
  min-height: 80px;
}

.post-card textarea:focus {
  outline: none;
  border-color: var(--accent-2);
  background: #fff;
}

.btn-post {
  display: inline-block;
  padding: 12px 24px;
  border-radius: 10px;
  border: 0;
  background: var(--accent);
  color: #fff;
  font-weight: 700;
  cursor: pointer;
  font-size: 15px;
  transition: background 0.2s ease-in-out;
}

.btn-post:hover {
  background: var(--accent-2);
}

/* Timeline */
.timeline { 
  list-style: none;
  width: 100%;
  padding: 0;
  margin: 0;
}

.timeline-label { 
  text-align: center;
  padding: 12px 0;
  margin: 24px 0 18px 0;
  position: relative;
}

.timeline-label .label {
  background: var(--card-bg);
  padding: 8px 20px;
  border-radius: 20px;
  font-weight: 600;
  font-size: 13px;
  color: var(--accent);
  box-shadow: 0 2px 10px rgba(0,0,0,0.1);
  display: inline-block;
}

/* Post Widget */
.widget { 
  background: var(--card-bg);
  border-radius: 16px;
  box-shadow: 0 8px 30px rgba(0,0,0,0.15);
  margin-bottom: 20px;
  overflow: hidden;
  transition: transform 0.2s ease;
}

.widget:hover {
  transform: translateY(-2px);
  box-shadow: 0 12px 40px rgba(0,0,0,0.2);
}

.widget-header { 
  padding: 18px 20px;
  display: flex;
  align-items: center;
  border-bottom: 1px solid #f5f5f5;
}

.widget-header img {
  border: 2px solid var(--accent-2);
}

.widget-header .ml-2 {
  margin-left: 12px;
  flex: 1;
}

.widget-header strong {
  font-size: 15px;
  color: #111;
}

.widget-header .small {
  font-size: 12px;
  color: var(--muted);
  margin-top: 2px;
}

.widget-header .ml-auto {
  margin-left: auto;
}

.widget-header .dropdown a {
  color: var(--muted);
  font-size: 20px;
  text-decoration: none;
}

.widget-body { 
  padding: 20px;
}

.widget-body p {
  margin: 0;
  line-height: 1.6;
  font-size: 14px;
  color: #222;
}

.widget-footer { 
  background: var(--bg-light);
  padding: 12px 20px;
}

.meta ul { 
  list-style: none;
  display: flex;
  padding: 0;
  margin: 0;
  gap: 20px;
}

.meta ul li { 
  display: flex;
  align-items: center;
  gap: 6px;
  font-size: 14px;
  font-weight: 600;
}

.meta ul li a { 
  text-decoration: none;
  color: var(--muted);
  transition: color 0.2s;
}

.meta ul li a:hover {
  color: var(--accent);
}

.meta ul li a i.la { 
  cursor: pointer;
  font-size: 20px;
}

.voted-up i.la-arrow-up { 
  color: #28a745 !important;
}

.voted-down i.la-arrow-down { 
  color: #dc3545 !important;
}

.meta ul li span {
  color: var(--muted);
  font-size: 13px;
}

/* Comments Section */
.comments-section { 
  background: #f9f9f9;
  border-top: 1px solid #eee;
  display: none;
  padding: 16px 20px;
}

.comments-list {
  margin-bottom: 12px;
}

.comment {
  margin-bottom: 12px;
}

.comment img {
  border: 1px solid #ddd;
}

.comment strong {
  font-size: 13px;
  color: #111;
}

.comment div {
  font-size: 13px;
  line-height: 1.5;
  color: #333;
}

.replies { 
  margin-left: 1.5rem;
  margin-top: 0.5rem;
}

.reply-btn {
  font-size: 12px;
  text-decoration: none;
}

.comment-input { 
  width: 100%;
  padding: 10px 12px;
  border-radius: 8px;
  border: 1px solid #ddd;
  font-size: 13px;
  font-family: "Helvetica Neue", Arial, sans-serif;
}

.comment-input:focus {
  outline: none;
  border-color: var(--accent-2);
}

.input-group {
  display: flex;
  gap: 8px;
}

.input-group-append .btn-sm {
  padding: 10px 16px;
  border-radius: 8px;
  background: var(--accent);
  color: #fff;
  border: none;
  font-weight: 600;
  font-size: 13px;
  cursor: pointer;
  transition: background 0.2s;
}

.input-group-append .btn-sm:hover {
  background: var(--accent-2);
}

/* Dropdown Menu */
.dropdown-menu {
  border-radius: 10px;
  box-shadow: 0 4px 20px rgba(0,0,0,0.15);
  border: 1px solid #eee;
  padding: 8px 0;
}

.dropdown-item {
  padding: 10px 16px;
  font-size: 14px;
  color: #333;
  transition: background 0.2s;
}

.dropdown-item:hover {
  background: #f9f9f9;
  color: var(--accent);
}

.dropdown-item.text-danger:hover {
  background: #fff0f0;
  color: #dc3545;
}

/* Empty State */
.text-center.text-muted {
  padding: 40px 20px;
  color: #fff;
  font-size: 15px;
}

/* Responsive */
@media (max-width: 768px) {
  .profile-header {
    padding: 60px 20px 30px;
  }
  
  .profile-header img {
    width: 100px;
    height: 100px;
  }
  
  .container {
    padding: 16px 12px;
  }
  
  .post-card, .widget {
    border-radius: 12px;
  }
}
</style>
@endpush

@section('content')

{{-- Profile Header --}}
<div class="profile-header">
    <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" id="avatarForm">
        @csrf
        @method('PATCH')
        <div class="profile-pic-wrapper">
            <img src="{{ Auth::user()->avatar ?? 'https://bootdey.com/img/Content/avatar/avatar1.png' }}" 
                 alt="Avatar" id="avatarPreview">
            <div class="camera-overlay" onclick="document.getElementById('avatarInput').click();">
                <i class="la la-camera"></i>
            </div>
        </div>
        <input type="file" id="avatarInput" name="avatar" accept="image/*" style="display:none;">
    </form>
    <h3>{{ strtoupper(Auth::user()->name) }}</h3>
</div>

<div class="container">
    {{-- Post Form --}}
    <div class="post-card">
        <form action="{{ route('timeline.store') }}" method="POST">
            @csrf
            <textarea name="content" placeholder="What's on your mind?" required></textarea>
            <button type="submit" class="btn-post">Post</button>
        </form>
    </div>

    {{-- Timeline --}}
    <div class="timeline">
        @php $currentDate = null; @endphp
        @forelse ($posts as $post)
            {{-- Day Separator --}}
            @if ($currentDate !== $post->created_at->toDateString())
                <div class="timeline-label">
                    <span class="label">
                        {{ $post->created_at->isToday() ? 'Today' : ($post->created_at->isYesterday() ? 'Yesterday' : $post->created_at->format('F j, Y')) }}
                    </span>
                </div>
                @php $currentDate = $post->created_at->toDateString(); @endphp
            @endif

            @php $userVote = $post->userVote(auth()->id()); @endphp

            {{-- Post Widget --}}
            <div class="widget" id="post-{{ $post->id }}">
                <div class="widget-header">
                    <img src="{{ $post->user->avatar ?? 'https://bootdey.com/img/Content/avatar/avatar1.png' }}" class="rounded-circle" width="40" height="40">
                    <div class="ml-2">
                        <strong>{{ $post->user->name }}</strong>
                        <div class="small text-muted">{{ $post->created_at->diffForHumans() }}</div>
                    </div>

                    @if(auth()->id() === $post->user_id)
                    <div class="ml-auto dropdown">
                        <a href="#" class="text-dark" data-toggle="dropdown"><i class="la la-ellipsis-h"></i></a>
                        <div class="dropdown-menu dropdown-menu-right">
                            <a class="dropdown-item" href="{{ route('posts.edit', $post->id) }}"><i class="la la-edit"></i> Edit Post</a>
                            <form action="{{ route('posts.destroy', $post->id) }}" method="POST" class="delete-post-form">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="dropdown-item text-danger delete-post-btn" data-id="{{ $post->id }}"><i class="la la-trash"></i> Delete Post</button>
                            </form>
                        </div>
                    </div>
                    @endif
                </div>

                <div class="widget-body">
                    <p>{{ $post->content }}</p>
                </div>

                <div class="widget-footer">
                    <div class="meta">
                        <ul>
                            <li>
                                <a href="#" class="upvote-btn {{ $userVote==='up'?'voted-up':'' }}" data-id="{{ $post->id }}"><i class="la la-arrow-up"></i></a>
                                <span id="upvote-count-{{ $post->id }}">{{ $post->upvotes()->count() }}</span>
                            </li>
                            <li>
                                <a href="#" class="downvote-btn {{ $userVote==='down'?'voted-down':'' }}" data-id="{{ $post->id }}"><i class="la la-arrow-down"></i></a>
                                <span id="downvote-count-{{ $post->id }}">{{ $post->downvotes()->count() }}</span>
                            </li>
                            <li>
                                <a href="#" class="toggle-comments" data-id="{{ $post->id }}">
                                    <i class="la la-comment"></i>
                                    <span id="comment-count-{{ $post->id }}">{{ $post->total_comments_count }}</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>

                {{-- Comments --}}
                <div class="comments-section" id="comments-section-{{ $post->id }}">
                    <div class="comments-list mb-2">
                        @foreach($post->comments as $comment)
                            <div class="comment mb-2 d-flex" id="comment-{{ $comment->id }}">
                                <img src="{{ $comment->user->avatar ?? 'https://bootdey.com/img/Content/avatar/avatar1.png' }}" class="rounded-circle mr-2" width="30" height="30">
                                <div>
                                    <strong>{{ $comment->user->name }}</strong>: {{ $comment->content }}
                                    <a href="#" class="reply-btn ml-2 small text-primary" data-id="{{ $comment->id }}">Reply</a>
                                    <div class="replies ml-4 mt-1">
                                        @foreach($comment->replies as $reply)
                                            <div class="comment mb-1 d-flex" id="comment-{{ $reply->id }}">
                                                <img src="{{ $reply->user->avatar ?? 'https://bootdey.com/img/Content/avatar/avatar1.png' }}" class="rounded-circle mr-2" width="25" height="25">
                                                <div><strong>{{ $reply->user->name }}</strong>: {{ $reply->content }}</div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="input-group mb-2">
                        <input type="text" class="form-control comment-input" placeholder="Write a comment...">
                        <div class="input-group-append">
                            <button class="btn btn-sm btn-danger comment-send" data-id="{{ $post->id }}">Send</button>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <p class="text-center text-muted">No posts yet. Start sharing your thoughts!</p>
        @endforelse
    </div>
</div>
@endsection

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function(){
    $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });

    // Avatar upload live
    $('#avatarInput').on('change', function(){
        let formData = new FormData($('#avatarForm')[0]);
        $.ajax({
            url: $('#avatarForm').attr('action'),
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function(res){
                $('#avatarPreview').attr('src', res.avatar_url);
            },
            error: function(){ alert('Avatar updated successfully'); }
        });
    });

    // Delete Post
    $(document).on('click', '.delete-post-btn', function(e){
        e.preventDefault();
        if(!confirm('Are you sure?')) return;
        let postId = $(this).data('id');
        let card = $(this).closest('.widget');
        $.ajax({
            url: `/posts/${postId}`,
            type: 'DELETE',
            success: function(){ card.remove(); },
            error: function(){ alert('Failed to delete post'); }
        });
    });

    // Toggle Comments
    $(document).on('click', '.toggle-comments', function(e){
        e.preventDefault();
        let postId = $(this).data('id');
        $(`#comments-section-${postId}`).slideToggle('fast');
    });

    // Upvote / Downvote
    $(document).on('click', '.upvote-btn, .downvote-btn', function(e){
        e.preventDefault();
        let postId = $(this).data('id');
        let vote = $(this).hasClass('upvote-btn')?'up':'down';
        $.post(`/posts/${postId}/vote`, { vote: vote }, function(res){
            $(`#upvote-count-${postId}`).text(res.upvotes_count);
            $(`#downvote-count-${postId}`).text(res.downvotes_count);
            $(`.upvote-btn[data-id="${postId}"]`).toggleClass('voted-up', res.user_vote==='up');
            $(`.downvote-btn[data-id="${postId}"]`).toggleClass('voted-down', res.user_vote==='down');
        }).fail(function(){ alert('Failed to vote'); });
    });

    // Add Comment / Reply
    $(document).on('click', '.comment-send', function(){
        let btn = $(this);
        let postId = btn.data('id');
        let input = btn.closest('.input-group').find('.comment-input');
        let content = input.val().trim();
        if(!content) return;

        let data = { content: content };
        let parentId = input.data('parent');
        if(parentId) data.parent_id = parentId;

        $.post(`/posts/${postId}/comment`, data, function(res){
            let commentHTML = `
                <div class="comment mb-2 d-flex" id="comment-${res.id}">
                    <img src="${res.avatar}" class="rounded-circle mr-2" width="30" height="30">
                    <div>
                        <strong>${res.user}</strong>: ${res.comment}
                        ${res.parent_id?'':`<a href="#" class="reply-btn ml-2 small text-primary" data-id="${res.id}">Reply</a>`}
                        <div class="replies ml-4 mt-1"></div>
                    </div>
                </div>`;
            if(res.parent_id){
                $(`#comment-${res.parent_id} .replies`).append(commentHTML);
            } else {
                $(`#comments-section-${postId} .comments-list`).append(commentHTML);
            }
            input.val('').removeAttr('data-parent');
            $(`#comment-count-${postId}`).text(res.comments_count);
        }).fail(function(){ alert('Failed to add comment'); });
    });

    // Reply button
    $(document).on('click', '.reply-btn', function(e){
        e.preventDefault();
        let parentId = $(this).data('id');
        let input = $(this).closest('.comments-section').find('.comment-input');
        input.focus().attr('data-parent', parentId).attr('placeholder', 'Write a reply...');
    });
});
</script>
@endpush