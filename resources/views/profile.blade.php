@extends('layouts.app')

@section('title', 'Profile')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">

<div class="post-container" role="main" aria-labelledby="profileTitle">
    <h1 id="profileTitle"><strong>My Profile</strong></h1>
    <div class="subtitle">Manage your posts and profile information</div>

    {{-- Success Alert --}}
    @if (session('success'))
        <div class="success-message">
            {{ session('success') }}
        </div>
        <script>
            setTimeout(() => document.querySelector('.success-message').style.display = 'none', 3000);
        </script>
    @endif

    {{-- Profile Header --}}
    <div class="profile-header">
        <form id="avatarForm" action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
            @csrf @method('PATCH')
            <div class="avatar-wrapper">
                <img id="avatarPreview" src="{{ Auth::user()->avatar_url }}" alt="Avatar">
                <label class="camera-icon" for="avatarInput" title="Change avatar">
                    <i class="la la-camera"></i>
                </label>
                <input type="file" id="avatarInput" name="avatar" accept="image/*" style="display:none;">
            </div>
        </form>
        <h2>{{ strtoupper(Auth::user()->name) }}</h2>
    </div>

    {{-- Create Post --}}
    <div class="post-form-card">
        <form action="{{ route('timeline.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <textarea name="content" class="form-control" rows="3" placeholder="Create a post..."></textarea>
            <div class="form-actions">
                <input type="file" name="image" accept="image/*,video/*,image/gif">
                <button type="submit" class="btn btn-primary">Post</button>
            </div>
        </form>
    </div>

    {{-- Timeline --}}
    <div class="timeline-section">
        @php $currentDate = null; @endphp
        @forelse ($posts as $post)
            @if ($currentDate !== $post->created_at->toDateString())
                <div class="timeline-label">
                    {{ $post->created_at->isToday() ? 'Today' : ($post->created_at->isYesterday() ? 'Yesterday' : $post->created_at->format('F j, Y')) }}
                </div>
                @php $currentDate = $post->created_at->toDateString(); @endphp
            @endif

            @php $userVote = $post->userVote(auth()->id()); @endphp
            <div class="post-card" id="post-{{ $post->id }}">
                <div class="vote-column">
                    <a href="#" class="upvote-btn {{ $userVote==='up'?'voted-up':'' }}" data-id="{{ $post->id }}">
                        <i class="la la-arrow-up"></i>
                    </a>
                    <div class="vote-count" id="upvote-count-{{ $post->id }}">
                        {{ $post->upvotes()->count() - $post->downvotes()->count() }}
                    </div>
                    <a href="#" class="downvote-btn {{ $userVote==='down'?'voted-down':'' }}" data-id="{{ $post->id }}">
                        <i class="la la-arrow-down"></i>
                    </a>
                </div>

                <div class="post-content">
                    <div class="post-header">
                        <div class="user-info">
                            <img src="{{ $post->user->avatar_url }}" width="38" height="38" class="user-avatar">
                            <div class="user-details">
                                <strong>{{ $post->user->name }}</strong>
                                <span class="post-time">{{ $post->created_at->diffForHumans() }}</span>
                            </div>
                        </div>
                        <div class="post-actions">
                            <div class="dropdown">
                                <a href="#" class="text-muted" data-toggle="dropdown">
                                    <i class="la la-ellipsis-h"></i>
                                </a>
                                <div class="dropdown-menu dropdown-menu-right">
                                    @if(auth()->id() === $post->user_id)
                                        <a class="dropdown-item" href="{{ route('posts.edit', $post->id) }}">
                                            <i class="la la-edit"></i> Edit
                                        </a>
                                        <button class="dropdown-item text-danger delete-post-btn" data-id="{{ $post->id }}" data-toggle="modal" data-target="#deleteModal">
                                            <i class="la la-trash"></i> Delete
                                        </button>
                                    @else
                                        <button class="dropdown-item text-warning report-post-btn" data-id="{{ $post->id }}" data-toggle="modal" data-target="#reportModal">
                                            <i class="la la-flag"></i> Report
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="post-body">
                        <p>{{ $post->content }}</p>
                        @if($post->image)
                            @if($post->media_type === 'image' || $post->media_type === 'gif')
                                <img src="{{ asset('storage/' . $post->image) }}" alt="Post Image">
                            @elseif($post->media_type === 'video')
                                <video controls>
                                    <source src="{{ asset('storage/' . $post->image) }}" type="video/mp4">
                                </video>
                            @endif
                        @endif
                    </div>

                    <div class="post-footer">
                        <a href="#" class="toggle-comments" data-id="{{ $post->id }}">
                            <i class="la la-comment"></i> 
                            <span id="comment-count-{{ $post->id }}">{{ $post->total_comments_count }}</span> Comments
                        </a>
                    </div>

                    {{-- Comments --}}
                    <div class="comments-section" id="comments-section-{{ $post->id }}">
                        <div class="comments-list">
                            @foreach($post->comments as $comment)
                                <div class="comment" id="comment-{{ $comment->id }}">
                                    <img src="{{ $comment->user->avatar_url }}" width="28" height="28" class="comment-avatar">
                                    <div class="comment-content">
                                        <div class="comment-text">
                                            <strong>{{ $comment->user->name }}</strong> {{ $comment->content }}
                                        </div>
                                        <a href="#" class="reply-btn" data-id="{{ $comment->id }}">Reply</a>
                                        <div class="replies">
                                            @foreach($comment->replies as $reply)
                                                <div class="comment reply" id="comment-{{ $reply->id }}">
                                                    <img src="{{ $reply->user->avatar_url }}" width="25" height="25" class="comment-avatar">
                                                    <div class="comment-text">
                                                        <strong>{{ $reply->user->name }}</strong> {{ $reply->content }}
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div class="comment-input-group">
                            <input type="text" class="comment-input" placeholder="Add a comment...">
                            <button class="btn btn-primary comment-send" data-id="{{ $post->id }}">Send</button>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="empty-state">
                <i class="la la-file-text"></i>
                <h3>No Posts Yet</h3>
                <p>Create your first post to get started!</p>
            </div>
        @endforelse

        {{-- Pagination --}}
        @if($posts->hasPages())
        <div class="pagination-container">
            <div class="pagination">
                {{-- Previous Page Link --}}
                @if($posts->onFirstPage())
                    <span class="pagination-btn disabled">Previous</span>
                @else
                    <a href="{{ $posts->previousPageUrl() }}" class="pagination-btn">Previous</a>
                @endif

                {{-- Pagination Elements --}}
                @foreach($posts->getUrlRange(1, $posts->lastPage()) as $page => $url)
                    @if($page == $posts->currentPage())
                        <span class="pagination-btn active">{{ $page }}</span>
                    @else
                        <a href="{{ $url }}" class="pagination-btn">{{ $page }}</a>
                    @endif
                @endforeach

                {{-- Next Page Link --}}
                @if($posts->hasMorePages())
                    <a href="{{ $posts->nextPageUrl() }}" class="pagination-btn">Next</a>
                @else
                    <span class="pagination-btn disabled">Next</span>
                @endif
            </div>
        </div>
        @endif
    </div>
</div>

{{-- Delete Modal --}}
<div class="modal fade" id="deleteModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header delete-header">
        <h5 class="modal-title"><i class="la la-exclamation-circle"></i> Delete Post</h5>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">Are you sure you want to delete this post?</div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Delete</button>
      </div>
    </div>
  </div>
</div>

{{-- Report Modal --}}
<div class="modal fade" id="reportModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header report-header">
        <h5 class="modal-title"><i class="la la-flag"></i> Report Post</h5>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">
        <p>Select a reason for reporting:</p>
        <form id="reportForm">
          <label><input type="radio" name="reason" value="spam"> It's spam</label>
          <label><input type="radio" name="reason" value="violence"> Violence or threats</label>
          <label><input type="radio" name="reason" value="hate_speech"> Hate speech</label>
          <label><input type="radio" name="reason" value="misinformation"> Misinformation</label>
          <label><input type="radio" name="reason" value="other"> Other</label>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-info" id="confirmReportBtn">Report</button>
      </div>
    </div>
  </div>
</div>

<style>
    :root {
        --accent: #CF0F47;
        --accent-2: #FF0B55;
        --card-bg: #ffffff;
        --muted: #666;
    }

    .post-container {
        max-width: 800px;
        background: var(--card-bg);
        border-radius: 16px;
        padding: 40px;
        box-shadow: 0 12px 40px rgba(0,0,0,0.1);
        margin: 0 auto;
    }

    .post-container h1 {
        margin: 0 0 16px 0;
        color: var(--accent);
        font-size: 28px;
        letter-spacing: 0.2px;
        text-align: center;
    }

    .subtitle {
        color: var(--muted);
        margin-bottom: 24px;
        font-size: 15px;
        text-align: center;
    }

    /* Profile Header */
    .profile-header {
        text-align: center;
        margin-bottom: 35px;
        padding: 0;
    }

    .avatar-wrapper {
        position: relative;
        display: inline-block;
        margin-bottom: 20px;
    }

    .avatar-wrapper img {
        width: 140px;
        height: 140px;
        border-radius: 50%;
        border: 4px solid #fff;
        object-fit: cover;
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
    }

    .camera-icon {
        position: absolute;
        bottom: 10px;
        right: 10px;
        background-color: var(--accent);
        border-radius: 50%;
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        color: white;
        transition: all 0.3s;
        border: 3px solid #fff;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.3);
    }

    .camera-icon:hover {
        background-color: var(--accent-2);
        transform: scale(1.05);
    }

    .camera-icon i {
        font-size: 18px;
    }

    .profile-header h2 {
        margin: 0;
        color: #333;
        font-size: 22px;
        font-weight: 700;
        letter-spacing: 1px;
    }

    /* Post Form Card */
    .post-form-card {
        background: #fff;
        border-radius: 12px;
        padding: 25px;
        margin-bottom: 30px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        border: 1px solid #eee;
    }

    .post-form-card textarea {
        width: 100%;
        padding: 15px;
        border-radius: 10px;
        border: 1px solid #ddd;
        margin-bottom: 15px;
        box-sizing: border-box;
        font-size: 15px;
        background: #fbfbfb;
        resize: none;
        min-height: 90px;
    }

    .post-form-card textarea:focus {
        border-color: var(--accent);
        background: #fff;
        box-shadow: 0 0 0 3px rgba(207, 15, 71, 0.1);
        outline: none;
    }

    .form-actions {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 15px;
    }

    .form-actions input[type="file"] {
        flex: 1;
        padding: 10px;
        border: 1px solid #ddd;
        border-radius: 8px;
        background: #fbfbfb;
    }

    /* Buttons */
    .btn {
        display: inline-block;
        padding: 12px 24px;
        border-radius: 10px;
        border: 0;
        font-weight: 700;
        cursor: pointer;
        font-size: 15px;
        transition: 0.25s;
        text-align: center;
        text-decoration: none;
        min-width: 100px;
    }

    .btn-primary {
        background: var(--accent);
        color: #fff;
    }

    .btn-primary:hover {
        background: var(--accent-2);
    }

    .btn-secondary {
        background: #eee;
        color: #444;
    }

    .btn-secondary:hover {
        background: #ddd;
    }

    /* Post Card */
    .post-card {
        background: #fff;
        border-radius: 12px;
        padding: 0;
        margin-bottom: 25px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        border: 1px solid #eee;
        display: flex;
    }

    .vote-column {
        width: 70px;
        text-align: center;
        padding: 1.2rem 0.5rem;
        border-right: 1px solid #eee;
        background: #fafafa;
        border-radius: 12px 0 0 12px;
    }

    .vote-column i.la {
        font-size: 22px;
        cursor: pointer;
        margin: 8px auto;
        color: #888;
        display: block;
    }

    .vote-column .voted-up i.la-arrow-up { color: #28a745; }
    .vote-column .voted-down i.la-arrow-down { color: #dc3545; }
    .vote-count { font-weight: bold; font-size: 18px; margin: 10px 0; }

    .post-content {
        flex: 1;
        padding: 1.5rem;
    }

    .post-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 15px;
    }

    .user-info {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .user-avatar {
        border-radius: 50%;
        object-fit: cover;
    }

    .user-details {
        display: flex;
        flex-direction: column;
        gap: 4px;
    }

    .user-details strong {
        color: #333;
        font-size: 16px;
    }

    .post-time {
        color: var(--muted);
        font-size: 13px;
    }

    .post-body {
        margin-bottom: 18px;
    }

    .post-body p {
        margin: 0 0 15px 0;
        line-height: 1.6;
        color: #333;
        font-size: 15px;
    }

    .post-body img, .post-body video {
        max-width: 100%;
        border-radius: 10px;
        margin-top: 12px;
    }

    .post-footer {
        border-top: 1px solid #f0f0f0;
        padding-top: 15px;
    }

    .toggle-comments {
        color: var(--accent);
        text-decoration: none;
        font-size: 15px;
        font-weight: 500;
        padding: 8px 0;
        display: inline-block;
    }

    .toggle-comments:hover {
        color: var(--accent-2);
    }

    /* Comments */
    .comments-section {
        display: none;
        margin-top: 18px;
        padding-top: 18px;
        border-top: 1px solid #f0f0f0;
    }

    .comments-list {
        margin-bottom: 18px;
    }

    .comment {
        display: flex;
        gap: 12px;
        margin-bottom: 15px;
        padding-bottom: 15px;
        border-bottom: 1px solid #f8f8f8;
    }

    .comment:last-child {
        border-bottom: none;
        margin-bottom: 0;
    }

    .comment.reply {
        margin-left: 25px;
        margin-bottom: 10px;
        padding-bottom: 10px;
    }

    .comment-avatar {
        border-radius: 50%;
        object-fit: cover;
        flex-shrink: 0;
    }

    .comment-content {
        flex: 1;
    }

    .comment-text {
        margin-bottom: 6px;
        line-height: 1.5;
        font-size: 14px;
    }

    .reply-btn {
        font-size: 13px;
        color: var(--accent);
        text-decoration: none;
        font-weight: 500;
    }

    .reply-btn:hover {
        color: var(--accent-2);
    }

    .replies {
        margin-top: 10px;
    }

    .comment-input-group {
        display: flex;
        gap: 10px;
    }

    .comment-input {
        flex: 1;
        padding: 12px 15px;
        border: 1px solid #ddd;
        border-radius: 8px;
        font-size: 14px;
    }

    .comment-input:focus {
        border-color: var(--accent);
        outline: none;
        box-shadow: 0 0 0 2px rgba(207, 15, 71, 0.1);
    }

    /* Timeline */
    .timeline-label {
        text-align: center;
        color: var(--muted);
        font-weight: 600;
        font-size: 15px;
        margin: 30px 0 20px 0;
        padding: 10px 0;
        border-bottom: 1px solid #eee;
    }

    /* Success Message */
    .success-message {
        background: rgba(76, 175, 80, 0.1);
        color: #4CAF50;
        padding: 18px;
        border-radius: 8px;
        margin-bottom: 25px;
        border-left: 4px solid #4CAF50;
        font-size: 15px;
    }

    /* Empty State */
    .empty-state {
        text-align: center;
        padding: 70px 25px;
        color: #666;
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        margin: 25px 0;
        border: 1px solid #eee;
    }

    .empty-state i {
        font-size: 70px;
        color: #ccc;
        margin-bottom: 25px;
    }

    .empty-state h3 {
        font-size: 22px;
        margin-bottom: 15px;
        color: #666;
    }

    .empty-state p {
        font-size: 15px;
        color: #999;
        line-height: 1.6;
    }

    /* Pagination */
    .pagination-container {
        display: flex;
        justify-content: center;
        margin-top: 35px;
    }

    .pagination {
        display: flex;
        gap: 8px;
        align-items: center;
    }

    .pagination-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 10px 16px;
        background: #fff;
        border: 1px solid #e0e0e0;
        border-radius: 8px;
        color: #666;
        text-decoration: none;
        font-size: 14px;
        font-weight: 500;
        transition: all 0.3s ease;
        min-width: 44px;
        height: 40px;
    }

    .pagination-btn:hover {
        background: var(--accent);
        border-color: var(--accent);
        color: #fff;
        transform: translateY(-1px);
    }

    .pagination-btn.active {
        background: var(--accent);
        border-color: var(--accent);
        color: #fff;
    }

    .pagination-btn.disabled {
        background: #f8f9fa;
        border-color: #e0e0e0;
        color: #999;
        cursor: not-allowed;
        transform: none;
    }

    .pagination-btn.disabled:hover {
        background: #f8f9fa;
        border-color: #e0e0e0;
        color: #999;
        transform: none;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .post-container {
            padding: 30px 25px;
        }

        .post-header {
            flex-direction: column;
            gap: 12px;
        }

        .post-actions {
            align-self: flex-end;
        }

        .form-actions {
            flex-direction: column;
            gap: 12px;
        }

        .form-actions input[type="file"] {
            margin-right: 0;
            margin-bottom: 12px;
            width: 100%;
        }

        .pagination {
            flex-wrap: wrap;
            justify-content: center;
        }
    }

    @media (max-width: 480px) {
        .post-card {
            flex-direction: column;
        }

        .vote-column {
            width: 100%;
            border-right: none;
            border-bottom: 1px solid #eee;
            border-radius: 12px 12px 0 0;
            padding: 12px;
        }

        .vote-column i.la {
            display: inline-block;
            margin: 0 12px;
        }

        .vote-count {
            display: inline-block;
            margin: 0 12px;
        }

        .pagination-btn {
            padding: 8px 12px;
            font-size: 13px;
            min-width: 40px;
            height: 36px;
        }
    }
</style>

<script>
$(function(){
  $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });
  let currentPostId = null;
  let currentCommentId = null;

  $(document).on('click','.toggle-comments',function(e){
    e.preventDefault();
    const id=$(this).data('id');
    $(`#comments-section-${id}`).slideToggle('fast');
  });

  $(document).on('click','.upvote-btn,.downvote-btn',function(e){
    e.preventDefault();
    const id=$(this).data('id');
    const vote=$(this).hasClass('upvote-btn')?'up':'down';
    $.post(`/posts/${id}/vote`,{vote:vote},res=>{
      $(`#upvote-count-${id}`).text(res.upvotes_count - res.downvotes_count);
      $(`.upvote-btn[data-id="${id}"]`).toggleClass('voted-up',res.user_vote==='up');
      $(`.downvote-btn[data-id="${id}"]`).toggleClass('voted-down',res.user_vote==='down');
    });
  });

  $(document).on('click','.comment-send',function(){
    const btn=$(this);
    const id=btn.data('id');
    const input=btn.closest('.comment-input-group').find('.comment-input');
    const content=input.val().trim();
    if(!content) return;
    $.post(`/posts/${id}/comments`,{content:content},res=>{
      const html=`<div class="comment" id="comment-${res.id}">
          <img src="${res.avatar}" width="28" height="28" class="comment-avatar">
          <div class="comment-content">
              <div class="comment-text"><strong>${res.user}</strong> ${res.comment}</div>
              <a href="#" class="reply-btn" data-id="${res.id}">Reply</a>
              <div class="replies"></div>
          </div>
      </div>`;
      $(`#comments-section-${id} .comments-list`).append(html);
      const count = parseInt($(`#comment-count-${id}`).text()) + 1;
      $(`#comment-count-${id}`).text(count);
      input.val('');
    });
  });

  $(document).on('click','.reply-btn',function(e){
    e.preventDefault();
    const commentId=$(this).data('id');
    const repliesDiv=$(`#comment-${commentId} .replies`);
    
    if(repliesDiv.find('.comment-input-group').length === 0){
      const replyInput=`<div class="comment-input-group mt-2">
          <input type="text" class="comment-input reply-input" placeholder="Write a reply...">
          <button class="btn btn-primary reply-send" data-comment-id="${commentId}">Send</button>
      </div>`;
      repliesDiv.append(replyInput);
    }
  });

  $(document).on('click','.reply-send',function(){
    const btn=$(this);
    const commentId=btn.data('comment-id');
    const input=btn.closest('.comment-input-group').find('.reply-input');
    const content=input.val().trim();
    if(!content) return;
    
    $.post(`/comments/${commentId}/reply`,{content:content},res=>{
      const html=`<div class="comment reply" id="comment-${res.id}">
          <img src="${res.avatar}" width="25" height="25" class="comment-avatar">
          <div class="comment-text"><strong>${res.user}</strong> ${res.comment}</div>
      </div>`;
      $(`#comment-${commentId} .replies`).prepend(html);
      input.closest('.comment-input-group').remove();
    });
  });

  $(document).on('click','.delete-post-btn',function(){ currentPostId=$(this).data('id'); });
  $('#confirmDeleteBtn').click(function(){
    $.ajax({ url:`/posts/${currentPostId}`, type:'POST', data:{_method:'DELETE'},
      success:()=>{ $(`#post-${currentPostId}`).fadeOut(300,()=>$(this).remove()); $('#deleteModal').modal('hide'); }
    });
  });

  $(document).on('click','.report-post-btn',function(){ currentPostId=$(this).data('id'); });
  $('#confirmReportBtn').click(function(){
    const reason=$('input[name="reason"]:checked').val();
    if(!reason){ alert('Please select a reason'); return; }
    $.post(`/posts/${currentPostId}/report`,{reason:reason},()=>{
      $('#reportModal').modal('hide');
      alert('Thank you for your report.');
    });
  });
});
</script>
@endsection