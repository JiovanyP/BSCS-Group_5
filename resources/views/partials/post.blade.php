<!-- resources/views/partials/post.blade.php -->
<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
<link href="https://fonts.googleapis.com/icon?family=Material+Icons+Outlined" rel="stylesheet">

<style>
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

/* === POST CARD === */
.post-card {
    background: var(--card-bg);
    border-radius: 12px;
    box-shadow: 0 6px 20px rgba(0,0,0,0.06);
    margin-bottom: 15px;
    transition: all 0.25s ease;
    position: relative;
    font-size: 14px;
}
.post-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 28px rgba(0,0,0,0.1);
}

/* Make entire card clickable */
.post-card-link {
    position: absolute;
    top: 0; left: 0; right: 0; bottom: 0;
    z-index: 0;
    text-decoration: none;
    pointer-events: none;
}

/* Keep interactive elements above overlay */
.post-header, .post-footer, .dropdown, .footer-action, .vote-container, .comment-container, .comments-section {
    position: relative;
    z-index: 5;
    pointer-events: auto;
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

/* Comment */
.comment-container {
    display: flex;
    align-items: center;
    background: #f0f0f0;
    border-radius: 16px;
    padding: 2px 6px;
    transition: background 0.2s;
}
.comment-container:hover { background: #e9e9e9; }

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

/* Comments section */
.comments-section {
    display: none;
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

.dropdown-item {
    display: flex;
    align-items: center;
    gap: 6px;
}
.dropdown-item .material-icons { font-size: 16px; }
</style>

@php $userVote = $post->userVote(auth()->id()); @endphp

<div class="post-card" id="post-{{ $post->id }}">
    <a href="{{ route('posts.view', $post->id) }}" class="post-card-link"></a>

    <div class="post-content">
        <!-- HEADER -->
        <div class="post-header">
            <div class="report-details">
                {{ strtoupper($post->accident_type ?? 'Incident') }} • 
                <span class="location">{{ $post->location ?? 'Unknown' }}</span>
                @if($post->other_type)
                    <small class="text-muted">({{ $post->other_type }})</small>
                @endif
            </div>
            <div class="dropdown">
                <a href="#" class="text-muted" data-bs-toggle="dropdown">
                    <span class="material-icons">more_horiz</span>
                </a>
                <div class="dropdown-menu dropdown-menu-end">
                    @if(auth()->id() === $post->user_id)
                        <a class="dropdown-item" href="{{ route('posts.edit', $post->id) }}">
                            <span class="material-icons">edit</span> Edit
                        </a>
                        <button class="dropdown-item text-danger delete-post-btn"
                                data-id="{{ $post->id }}"
                                data-toggle="modal"
                                data-target="#deleteModal">
                            <span class="material-icons">delete</span> Delete
                        </button>
                    @else
                        <button class="dropdown-item report-post-btn"
                                data-id="{{ $post->id }}"
                                data-toggle="modal"
                                data-target="#reportModal">
                            <span class="material-icons">flag</span> Report
                        </button>
                    @endif
                </div>
            </div>
        </div>

        <!-- BODY -->
        <div class="post-body">
            @if(!empty($post->content))
                <p>{{ $post->content }}</p>
            @endif
            @if(!empty($post->image_url))
                @if($post->media_type === 'image' || $post->media_type === 'gif')
                    <img src="{{ $post->image_url }}" alt="Post image">
                @elseif($post->media_type === 'video')
                    <video controls>
                        <source src="{{ $post->image_url }}" type="video/mp4">
                    </video>
                @endif
            @endif
        </div>

        <!-- SIGNATURE -->
        <div class="post-signature">
            <div class="user-info">
                <img src="{{ $post->user->avatar_url ?? asset('images/avatar.png') }}" width="28" height="28" class="rounded-circle">
                <strong>{{ $post->user->name ?? 'Unknown User' }}</strong>
                <small>{{ $post->created_at->diffForHumans() }}</small>
            </div>
        </div>

        <!-- FOOTER -->
        <div class="post-footer">
            <div class="comment-container">
                <a href="#" class="toggle-comments footer-action" data-id="{{ $post->id }}">
                    <span class="material-icons-outlined">chat_bubble_outline</span>
                    <span id="comment-count-{{ $post->id }}">{{ $post->total_comments_count }}</span>
                </a>
            </div>

            <div class="vote-container">
                <a href="#" class="upvote-btn footer-action {{ $userVote === 'up' ? 'voted-up' : '' }}" data-id="{{ $post->id }}">
                    <span class="material-icons">arrow_upward</span>
                </a>
                <div class="vote-count" id="upvote-count-{{ $post->id }}">
                    {{ $post->upvotes()->count() - $post->downvotes()->count() }}
                </div>
                <a href="#" class="downvote-btn footer-action {{ $userVote === 'down' ? 'voted-down' : '' }}" data-id="{{ $post->id }}">
                    <span class="material-icons">arrow_downward</span>
                </a>
            </div>
        </div>

        <!-- COMMENTS -->
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
                            <img src="{{ $comment->user->avatar_url ?? asset('images/avatar.png') }}" width="24" height="24" class="rounded-circle">
                            <div style="flex: 1;">
                                <div><strong>{{ $comment->user->name }}</strong> {{ $comment->content }}</div>
                                <a href="#" class="reply-btn small" data-id="{{ $comment->id }}">Reply</a>
                                <div class="replies">
                                    @foreach($comment->replies as $reply)
                                        <div class="comment" id="comment-{{ $reply->id }}">
                                            <img src="{{ $reply->user->avatar_url ?? asset('images/avatar.png') }}" width="22" height="22" class="rounded-circle">
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

<script>
document.addEventListener('DOMContentLoaded', () => {
    // Toggle comment section
    document.querySelectorAll('.toggle-comments').forEach(btn => {
        btn.addEventListener('click', e => {
            e.preventDefault();
            e.stopPropagation();
            const id = btn.dataset.id;
            const section = document.getElementById(`comments-section-${id}`);
            section.style.display = (section.style.display === 'block') ? 'none' : 'block';
        });
    });

    // Enable send button only when input not empty
    document.querySelectorAll('.comment-input').forEach(input => {
        input.addEventListener('input', e => {
            const id = e.target.id.split('-').pop();
            const send = document.getElementById(`comment-send-${id}`);
            send.disabled = e.target.value.trim() === '';
        });
    });

    // Prevent clicks on other interactive elements from triggering card link
    document.querySelectorAll('.footer-action:not(.upvote-btn):not(.downvote-btn), .dropdown-item, .comment-send, .reply-btn').forEach(el => {
        el.addEventListener('click', e => {
            e.stopPropagation();
            e.preventDefault();
        });
    });

    // Reply button logic
    document.addEventListener('click', e => {
        if (e.target.closest('.reply-btn')) {
            e.preventDefault();
            e.stopPropagation();
            const btn = e.target.closest('.reply-btn');
            const commentId = btn.dataset.id;

            if (btn.nextElementSibling?.classList.contains('reply-input-group')) return;

            const replyGroup = document.createElement('div');
            replyGroup.className = 'reply-input-group';
            replyGroup.innerHTML = `
                <input type="text" class="form-control reply-input" placeholder="Write a reply...">
                <button class="reply-send" disabled>Send</button>
            `;

            btn.insertAdjacentElement('afterend', replyGroup);

            const input = replyGroup.querySelector('.reply-input');
            const sendBtn = replyGroup.querySelector('.reply-send');

            input.addEventListener('input', () => { sendBtn.disabled = input.value.trim() === ''; });

            sendBtn.addEventListener('click', e => {
                e.stopPropagation();
                const value = input.value.trim();
                if (!value) return;

                const repliesContainer = btn.parentElement.querySelector('.replies');
                const newReply = document.createElement('div');
                newReply.className = 'comment';
                newReply.innerHTML = `
                    <img src="/images/avatar.png" width="22" height="22" class="rounded-circle">
                    <div><strong>You</strong> ${value}</div>
                `;
                repliesContainer.appendChild(newReply);

                input.value = '';
                sendBtn.disabled = true;
                replyGroup.remove();
            });
        }
    });
});
</script>
