@extends('layouts.app')

@section('title', 'View Post')

@section('content')
<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
<link href="https://fonts.googleapis.com/icon?family=Material+Icons+Outlined" rel="stylesheet">

<meta name="csrf-token" content="{{ csrf_token() }}">

<style>
/* === VARIABLES === */
:root {
    --primary: #494ca2;
    --accent: #CF0F47;
    --accent-2: #FF0B55;
    --card-bg: #ffffff;
    --text-muted: #666;
    --border-color: #ddd;
    --input-bg: #fbfbfb;
    --btn-disabled-bg: #e0e0e0;
    --btn-disabled-color: #999;
    --reply-btn-default: #888;
    --upvote-color: #28a745;
    --downvote-color: #dc3545;
}

.main-content {
    flex: 1;
    overflow-y: auto;
    position: relative;
    background: #f8f9fa;
    padding: 20px 0;
}

/* Back button styling */
.back-button {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    color: var(--accent);
    text-decoration: none;
    font-weight: 600;
    font-size: 14px;
    margin-bottom: 15px;
    transition: color 0.2s;
    /* Center aligns with the card */
    max-width: 550px;
    margin-left: auto;
    margin-right: auto;
    width: 100%;
}
.back-button:hover {
    color: var(--accent-2);
    text-decoration: none;
}
.back-button .material-icons {
    font-size: 18px;
}

/* === POST CARD (EXACT COPY OF NARROW CARD) === */
.post-card {
    background: var(--card-bg);
    border-radius: 12px;
    box-shadow: 0 6px 20px rgba(0,0,0,0.06);
    transition: all 0.25s ease;
    position: relative;
    font-size: 14px;
    
    /* WIDTH SETTINGS */
    max-width: 550px;       
    width: 100%;            
    margin: 0 auto 20px auto; 
}

/* Keep interactive elements above overlay */
.post-header, .post-footer, .dropdown, .footer-action, .vote-container, .comment-container, .comments-section {
    position: relative;
    z-index: 5;
}

.post-content {
    padding: 1rem 1.5rem;
}

.post-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 0.25rem;
}

.report-details {
    font-size: 13px;
    font-weight: 700;
    color: var(--accent);
    line-height: 1.4;
    text-transform: uppercase;
}
.report-details .location {
    font-weight: 500;
    color: #333;
    text-transform: none;
}

.post-body {
    color: #333;
    line-height: 1.4;
    font-size: 14px;
    margin-top: 0.5rem;
    margin-bottom: 0.75rem;
}

/* EXACT IMAGE SIZING FROM CARD */
.post-body img, .post-body video {
    max-width: 220px;
    max-height: 150px;
    width: auto;
    height: auto;
    border-radius: 8px;
    margin-top: 8px;
    box-shadow: 0 3px 8px rgba(0,0,0,0.08);
    display: block;
    object-fit: cover;
}

/* Signature */
.post-signature {
    padding-top: 8px;
    margin-bottom: 0.75rem;
    border-top: 1px solid #f0f0f0;
}
.user-info {
    display: flex;
    align-items: center;
    gap: 6px;
}
.user-info strong {
    font-size: 14px;
    font-weight: 600;
}
.user-info small {
    color: var(--text-muted);
    font-size: 12px;
    margin-left: auto;
}

/* Footer */
.post-footer {
    display: flex;
    align-items: center;
    margin-top: 0.75rem;
    padding-top: 0.5rem;
    border-top: 1px solid #f0f0f0;
    gap: 10px;
}

.footer-action {
    display: flex;
    align-items: center;
    color: var(--text-muted);
    text-decoration: none;
    font-size: 13px;
    font-weight: 500;
    transition: color 0.2s;
    cursor: pointer;
}

/* Comment Container */
.comment-container {
    display: flex;
    align-items: center;
    background: #f0f0f0;
    border-radius: 16px;
    padding: 2px 6px;
}
.comment-container .footer-action {
    padding: 3px 5px;
    color: var(--text-muted);
}
.comment-container .material-icons-outlined {
    margin-right: 3px;
    font-size: 18px;
}

/* Votes */
.vote-container {
    display: flex;
    align-items: center;
    margin-left: auto;
    background: #f0f0f0;
    border-radius: 16px;
    padding: 2px;
}
.upvote-btn, .downvote-btn { padding: 3px 6px; }
.upvote-btn:hover { color: var(--upvote-color); }
.downvote-btn:hover { color: var(--downvote-color); }
.voted-up { color: var(--upvote-color) !important; }
.voted-down { color: var(--downvote-color) !important; }

/* Comments section (VISIBLE BY DEFAULT FOR VIEW PAGE) */
.comments-section {
    display: block; /* Forced visible for View Page */
    background: #f8f9fa;
    border-top: 1px solid #eee;
    padding: 1rem 1.5rem;
    border-radius: 0 0 12px 12px;
    margin: 0 -1.5rem -1rem -1.5rem;
    overflow: hidden;
}

.comments-header {
    font-size: 14px;
    font-weight: 600;
    color: #333;
    margin-bottom: 0.75rem;
}

.comment {
    display: flex;
    align-items: flex-start;
    margin-bottom: 0.4rem;
    gap: 6px;
}
.comment img { border-radius: 50%; flex-shrink: 0; width: 24px; height: 24px; }
.comment strong { font-weight: 600; margin-right: 3px; }

.replies .comment {
    margin-left: 18px;
    margin-top: 3px;
    gap: 4px;
}

.comments-section > .input-group {
    margin-bottom: 1rem;
    position: relative;
    height: 36px;
}

.comment-input, .reply-input {
    width: 100%;
    padding: 8px 50px 8px 12px !important;
    border-radius: 18px !important;
    border: 1px solid var(--border-color);
    font-size: 13px;
    background: var(--input-bg);
    transition: border-color 0.2s, box-shadow 0.2s;
    position: absolute;
    top: 0; left: 0;
    height: 100%;
}
.comment-input:focus, .reply-input:focus {
    border-color: var(--accent) !important;
    background: #fff;
    box-shadow: 0 0 0 2px rgba(207, 15, 71, 0.15) !important;
    outline: none;
}

.comment-send, .reply-send {
    position: absolute;
    right: 5px;
    top: 50%;
    transform: translateY(-50%);
    z-index: 10;
    background: var(--btn-disabled-bg) !important;
    color: var(--btn-disabled-color) !important;
    border: none;
    font-weight: 700;
    transition: background 0.25s, color 0.25s;
    padding: 3px 10px;
    font-size: 13px;
    border-radius: 16px !important;
    height: 30px;
    line-height: 1.6;
}

.comment-send:not(:disabled), .reply-send:not(:disabled) {
    background: var(--accent) !important;
    color: #fff !important;
}
.comment-send:not(:disabled):hover, .reply-send:not(:disabled):hover {
    background: var(--accent-2) !important;
}

.reply-btn {
    font-size: 0.8rem;
    cursor: pointer;
    color: var(--reply-btn-default) !important;
    font-weight: 500;
    text-decoration: none !important;
    transition: color 0.2s;
}
.reply-btn:hover, .reply-btn:focus {
    color: var(--accent) !important;
}

.reply-input-group {
    position: relative;
    height: 36px;
    margin-top: 0.5rem;
}

/* DROPDOWNS & MODALS */
.dropdown, .dropdown-menu, .modal {
    position: relative;
    z-index: 20 !important;
}
</style>

<div class="main-content">
    <div class="container mt-4">
        
        <a href="{{ url()->previous() }}" class="back-button">
            <span class="material-icons">arrow_back</span>
            Back
        </a>

        @if (session('success'))
            <div class="alert alert-success text-center" id="successAlert" style="max-width: 550px; margin: 0 auto 15px auto;">
                {{ session('success') }}
            </div>
            <script>
                setTimeout(() => document.getElementById('successAlert').style.display = 'none', 3000);
            </script>
        @endif

        @php $userVote = $post->userVote(auth()->id()); @endphp
        
        <div class="post-card" id="post-{{ $post->id }}">
            <div class="post-content">
                <div class="post-header">
                    <div class="report-details">
                        {{ strtoupper($post->accident_type) }} • <span class="location">{{ $post->location }}</span>
                        @if($post->other_type) <small class="text-muted">({{ $post->other_type }})</small> @endif
                    </div>

                    <div class="dropdown">
                        <a href="#" class="text-muted" data-toggle="dropdown"><span class="material-icons">more_horiz</span></a>
                        <div class="dropdown-menu dropdown-menu-right">
                            @if(auth()->id() === $post->user_id)
                                <a class="dropdown-item" href="{{ route('posts.edit', $post->id) }}">
                                    <span class="material-icons">edit</span> Edit
                                </a>
                                <button class="dropdown-item delete-post-btn" data-id="{{ $post->id }}" data-bs-toggle="modal" data-bs-target="#deleteModal">
                                    <span class="material-icons">delete</span> Delete
                                </button>
                            @else
                                <button class="dropdown-item report-post-btn" data-id="{{ $post->id }}" data-bs-toggle="modal" data-bs-target="#reportModal">
                                    <span class="material-icons">flag</span> Report
                                </button>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="post-body">
                    <p>{{ $post->content }}</p>
                    @if($post->image)
                        @if($post->media_type === 'image' || $post->media_type === 'gif')
                            <img src="{{ asset('storage/' . $post->image) }}" alt="Post Image">
                        @elseif($post->media_type === 'video')
                            <video controls><source src="{{ asset('storage/' . $post->image) }}" type="video/mp4"></video>
                        @endif
                    @endif
                </div>

                <div class="post-signature">
                    <div class="user-info">
                        <img src="{{ $post->user->avatar_url }}" width="28" height="28" class="rounded-circle">
                        <strong>{{ $post->user->name }}</strong>
                        
                        <small>{{ $post->created_at->format('M j, Y g:i A') }}</small>
                    </div>
                </div>

                <div class="post-footer">
                    <div class="comment-container">
                        <div class="footer-action">
                            <span class="material-icons-outlined">chat_bubble_outline</span>
                            <span id="comment-count-{{ $post->id }}">{{ $post->total_comments_count }}</span>
                        </div>
                    </div>
                    
                    <div class="vote-container">
                        <a href="#" class="upvote-btn footer-action {{ $userVote==='up'?'voted-up':'' }}" data-id="{{ $post->id }}">
                            <span class="material-icons">arrow_upward</span>
                        </a>
                        <div class="vote-count" id="upvote-count-{{ $post->id }}">
                            {{ $post->upvotes()->count() - $post->downvotes()->count() }}
                        </div>
                        <a href="#" class="downvote-btn footer-action {{ $userVote==='down'?'voted-down':'' }}" data-id="{{ $post->id }}">
                            <span class="material-icons">arrow_downward</span>
                        </a>
                    </div>
                </div>

                <div class="comments-section" id="comments-section-{{ $post->id }}">
                    <div class="input-group">
                        <input type="text" class="form-control comment-input" id="comment-input-{{ $post->id }}" placeholder="Add a comment...">
                        <button class="comment-send" data-id="{{ $post->id }}" id="comment-send-{{ $post->id }}" disabled>Send</button>
                    </div>

                    @if($post->comments->count() > 0)
                        <div class="comments-header">Comments</div>
                        <div class="comments-list mb-3">
                            @foreach($post->comments as $comment)
                                <div class="comment" id="comment-{{ $comment->id }}">
                                    <img src="{{ $comment->user->avatar_url }}" width="24" height="24" class="rounded-circle">
                                    <div style="flex: 1;">
                                        <div><strong>{{ $comment->user->name }}</strong> {{ $comment->content }}</div>
                                        <a href="#" class="reply-btn small" data-id="{{ $comment->id }}">Reply</a>
                                        <div class="replies">
                                            @foreach($comment->replies as $reply)
                                                <div class="comment" id="comment-{{ $reply->id }}">
                                                    <img src="{{ $reply->user->avatar_url }}" width="22" height="22" class="rounded-circle">
                                                    <div><strong>{{ $reply->user->name }}</strong> {{ $reply->content }}</div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Delete Modal --}}
<div class="modal fade" id="deleteModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><span class="material-icons">warning</span> Delete Report</h5>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">Are you sure you want to delete this report?</div>
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
      <div class="modal-header">
        <h5 class="modal-title"><span class="material-icons">flag</span> Report Post</h5>
      </div>
      <div class="modal-body">
        <p>Select a reason for reporting:</p>
        <form id="reportForm" class="report-reason-list">
          <label><input type="radio" name="reason" value="spam"> It's spam</label>
          <label><input type="radio" name="reason" value="violence"> Violence or threats</label>
          <label><input type="radio" name="reason" value="hate_speech"> Hate speech</label>
          <label><input type="radio" name="reason" value="misinformation"> Misinformation</label>
          <label><input type="radio" name="reason" value="other"> Other</label>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
        <button type="button" class="btn" id="confirmReportBtn">Report</button>
      </div>
    </div>
  </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
$(function(){
    $.ajaxSetup({
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
    });

    $(document).on('input', '.comment-input', function() {
        const btn = $(this).siblings('.comment-send');
        btn.prop('disabled', $(this).val().trim() === '');
    });

    $(document).on('click', '.comment-send:not(:disabled)', function(){
        const postId = $(this).data('id');
        const input = $(`#comment-input-${postId}`);
        const content = input.val().trim();

        if (!content) return;

        const button = $(this);
        button.prop('disabled', true).text('Sending...');

        $.ajax({
            url: `/posts/${postId}/comments`,
            method: 'POST',
            data: { content },
            success: function(response) {
                input.val('');
                button.text('Send').prop('disabled', true);

                const countEl = $(`#comment-count-${postId}`);
                const newCount = parseInt(countEl.text()) + 1;
                countEl.text(newCount);

                const commentsSection = $(`#comments-section-${postId}`);
                if (commentsSection.find('.comments-header').length === 0) {
                    commentsSection.prepend('<div class="comments-header">Comments</div>');
                }

                let commentsList = commentsSection.find('.comments-list');
                if (commentsList.length === 0) {
                    commentsList = $('<div class="comments-list mb-3"></div>');
                    commentsSection.append(commentsList);
                }

                const newComment = `
                    <div class="comment" id="comment-${response.id}">
                        <img src="${response.user.avatar_url}" width="24" height="24" class="rounded-circle">
                        <div style="flex:1;">
                            <div><strong>${response.user.name}</strong> ${response.content}</div>
                            <a href="#" class="reply-btn small" data-id="${response.id}">Reply</a>
                            <div class="replies"></div>
                        </div>
                    </div>
                `;
                commentsList.prepend(newComment);
            },
            error: function(xhr) {
                console.error(xhr.responseText);
                button.text('Send').prop('disabled', false);
            }
        });
    });
});

$(document).on('click', '.reply-btn', function(e) {
    e.preventDefault(); 
    const commentId = $(this).data('id');
    const repliesContainer = $(this).siblings('.replies');

    if (repliesContainer.find('.reply-input-group').length > 0) {
        repliesContainer.find('.reply-input-group').remove();
        return;
    }

    const replyBox = `
        <div class="reply-input-group">
            <input type="text" class="form-control reply-input" id="reply-input-${commentId}" placeholder="Write a reply...">
            <button class="reply-send" data-id="${commentId}" disabled>Reply</button>
        </div>
    `;
    repliesContainer.append(replyBox);
});

$(document).on('input', '.reply-input', function() {
    const btn = $(this).siblings('.reply-send');
    btn.prop('disabled', $(this).val().trim() === '');
});

$(document).on('click', '.reply-send:not(:disabled)', function() {
    const commentId = $(this).data('id');
    const input = $(`#reply-input-${commentId}`);
    const content = input.val().trim();
    const button = $(this);

    if (!content) return;

    button.prop('disabled', true).text('Sending...');

    $.ajax({
        url: `/comments/${commentId}/reply`,
        method: 'POST',
        data: { content },
        success: function(response) {
            input.closest('.reply-input-group').remove();
            const repliesContainer = $(`#comment-${commentId}`).find('.replies');
            const newReply = `
                <div class="comment" id="comment-${response.id}">
                    <img src="${response.user.avatar_url}" width="22" height="22" class="rounded-circle">
                    <div><strong>${response.user.name}</strong> ${response.content}</div>
                </div>
            `;
            repliesContainer.append(newReply);
        },
        error: function(xhr) {
            console.error(xhr.responseText);
            button.text('Reply').prop('disabled', false);
        }
    });
});
</script>

@include('partials.voting-logic')

@endsection