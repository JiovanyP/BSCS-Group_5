@extends('layouts.app')

@section('title', 'Timeline')

@section('content')
<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
<link href="https://fonts.googleapis.com/icon?family=Material+Icons+Outlined" rel="stylesheet">

<meta name="csrf-token" content="{{ csrf_token() }}">
<style>
/* === VARIABLES & BASE STYLES === */
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

/* === POST CARD === */
.post-card {
    background: var(--card-bg);
    border-radius: 16px;
    box-shadow: 0 12px 40px rgba(0,0,0,0.1);
    margin-bottom: 20px;
    transition: all 0.25s ease;
}

.post-content {
    padding: 1.5rem 2rem;
}

.post-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 0.5rem;
}

.report-details {
    font-size: 15px;
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
    line-height: 1.6;
    font-size: 15px;
    margin-top: 0.5rem;
    margin-bottom: 1rem;
}
.post-body img, .post-body video {
    max-width: 100%;
    border-radius: 10px;
    margin-top: 12px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

.post-signature {
    padding-top: 10px;
    margin-bottom: 1rem;
    border-top: 1px solid #f0f0f0;
}
.user-info {
    display: flex;
    align-items: center;
    gap: 8px;
}
.user-info strong {
    font-size: 15px;
    font-weight: 600;
}
.user-info small {
    color: var(--text-muted);
    font-size: 13px;
    margin-left: auto;
}

.post-footer {
    display: flex;
    align-items: center;
    margin-top: 1rem;
    padding-top: 0.75rem;
    border-top: 1px solid #f0f0f0;
    gap: 15px;
}

.footer-action {
    display: flex;
    align-items: center;
    color: var(--text-muted);
    text-decoration: none;
    font-size: 14px;
    font-weight: 500;
    transition: color 0.2s;
    cursor: pointer;
}

.comment-container {
    display: flex;
    align-items: center;
    background: #f0f0f0;
    border-radius: 18px;
    padding: 2px 8px;
    transition: background 0.2s;
}
.comment-container:hover {
    background: #e9e9e9;
}
.comment-container .footer-action {
    padding: 4px 6px;
    color: var(--text-muted);
}
.comment-container .footer-action:hover {
    color: var(--accent);
}
.comment-container .material-icons-outlined {
    margin-right: 4px;
    font-size: 20px;
}

.vote-container {
    display: flex;
    align-items: center;
    margin-left: auto;
    background: #f0f0f0;
    border-radius: 18px;
    padding: 2px;
}

.upvote-btn, .downvote-btn {
    padding: 4px 8px;
}
.upvote-btn:hover { color: var(--upvote-color); }
.downvote-btn:hover { color: var(--downvote-color); }

.voted-up { color: var(--upvote-color) !important; }
.voted-down { color: var(--downvote-color) !important; }

.comments-section {
    display: none;
    background: #f8f9fa;
    border-top: 1px solid #eee;
    padding: 1.5rem 2rem 0;
    border-radius: 0 0 16px 16px;
    margin: 0 -2rem -1.5rem -2rem;
    overflow: hidden;
}

.comment {
    display: flex;
    align-items: flex-start;
    margin-bottom: 0.5rem;
    gap: 8px;
}
.comment img { flex-shrink: 0; }
.comment strong { font-weight: 600; margin-right: 4px; }

.replies .comment {
    display: flex;
    align-items: flex-start;
    gap: 6px;
    margin-left: 20px;
    margin-top: 4px;
}

.comment-input, .reply-input {
    width: 100%;
    padding: 12px 60px 12px 16px !important;
    border-radius: 22px !important;
    border: 1px solid var(--border-color);
    font-size: 14px;
    background: var(--input-bg);
    transition: border-color 0.2s, box-shadow 0.2s;
}

.comment-input:focus, .reply-input:focus {
    border-color: var(--accent) !important;
    background: #fff;
    box-shadow: 0 0 0 3px rgba(207, 15, 71, 0.15) !important;
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
    padding: 4px 12px;
    font-size: 14px;
    border-radius: 18px !important;
    height: 34px;
    line-height: 1.8;
}

.comment-send:not(:disabled), .reply-send:not(:disabled) {
    background: var(--accent) !important;
    color: #fff !important;
}
.comment-send:not(:disabled):hover, .reply-send:not(:disabled):hover {
    background: var(--accent-2) !important;
}

.reply-btn {
    font-size: 0.875rem;
    cursor: pointer;
    color: var(--reply-btn-default) !important;
    font-weight: 500;
    text-decoration: none !important;
    transition: color 0.2s;
    display: inline-block;
}
.reply-btn:hover, .reply-btn:focus { color: var(--accent) !important; }

.dropdown-item { display: flex; align-items: center; gap: 8px; }
.dropdown-item .material-icons { font-size: 18px; }

.report-reason-list label { display: block; margin-bottom: 8px; font-weight: 400; color: #333; }

#confirmReportBtn {
    background-color: var(--accent) !important;
    color: #fff !important;
    border: none !important;
    font-weight: 700;
    transition: background-color 0.25s;
}
#confirmReportBtn:hover { background-color: var(--accent-2) !important; }

.modal-footer .btn-secondary {
    background-color: #f0f0f0 !important;
    color: #666 !important;
    border: 1px solid #ddd !important;
    font-weight: 500;
}
.modal-footer .btn-secondary:hover { background-color: #e9e9e9 !important; }

/* === LOCATION TAG FILTER === */
.location-tag {
    border-radius: 20px;
    padding: 6px 14px;
    font-weight: 500;
    transition: all 0.2s;
}
.location-tag:hover {
    background-color: var(--accent);
    color: #fff;
    border-color: var(--accent);
}
.location-tag.active {
    background-color: var(--primary);
    color: #fff;
    border-color: var(--primary);
}
</style>

<div class="main-content">
    <div class="container mt-4">
        <div class="col-xl-8 mx-auto">

            {{-- Success Alert --}}
            @if (session('success'))
                <div class="alert alert-success text-center" id="successAlert">
                    {{ session('success') }}
                </div>
                <script>
                    setTimeout(() => document.getElementById('successAlert').style.display = 'none', 3000);
                </script>
            @endif

            {{-- === LOCATION FILTER TAGS === --}}
            @php
                $uniqueLocations = $posts->pluck('location')->unique()->filter()->values();
            @endphp

            @if($uniqueLocations->count() > 0)
                <div class="mb-4 text-center">
                    <div class="d-inline-flex flex-wrap justify-content-center">
                        <button class="btn btn-sm btn-outline-primary mx-1 location-tag active" data-location="all">All</button>
                        @foreach($uniqueLocations as $location)
                            <button class="btn btn-sm btn-outline-primary mx-1 location-tag" data-location="{{ $location }}">
                                {{ $location }}
                            </button>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- === TIMELINE === --}}
            @php $currentDate = null; @endphp
            @forelse ($posts as $post)
                @if ($currentDate !== $post->created_at->toDateString())
                    <div class="timeline-label text-center font-weight-bold my-3">
                        {{ $post->created_at->isToday() ? 'Today' : ($post->created_at->isYesterday() ? 'Yesterday' : $post->created_at->format('F j, Y')) }}
                    </div>
                    @php $currentDate = $post->created_at->toDateString(); @endphp
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
                                        <a class="dropdown-item cute-edit-btn" href="{{ route('posts.edit', $post->id) }}">
                                            <span class="material-icons">edit</span> Edit
                                        </a>
                                        <button class="dropdown-item cute-delete-btn delete-post-btn" data-id="{{ $post->id }}" data-toggle="modal" data-target="#deleteModal">
                                            <span class="material-icons">delete</span> Delete
                                        </button>
                                    @else
                                        <button class="dropdown-item report-post-btn" data-id="{{ $post->id }}" data-toggle="modal" data-target="#reportModal">
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
                                <img src="{{ $post->user->avatar_url }}" width="32" height="32" class="rounded-circle">
                                <strong>{{ $post->user->name }}</strong>
                                <small>{{ $post->created_at->diffForHumans() }}</small>
                            </div>
                        </div>

                        <div class="post-footer">
                            <div class="comment-container">
                                <a href="#" class="toggle-comments footer-action" data-id="{{ $post->id }}">
                                    <span class="material-icons-outlined">chat_bubble_outline</span>
                                    <span id="comment-count-{{ $post->id }}">{{ $post->total_comments_count }}</span>
                                </a>
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

                        {{-- Comments Section --}}
                        <div class="comments-section" id="comments-section-{{ $post->id }}">
                            <div class="comments-list mb-3">
                                @foreach($post->comments as $comment)
                                    <div class="comment" id="comment-{{ $comment->id }}">
                                        <img src="{{ $comment->user->avatar_url }}" width="28" height="28" class="rounded-circle">
                                        <div style="flex: 1;">
                                            <div><strong>{{ $comment->user->name }}</strong> {{ $comment->content }}</div>
                                            <a href="#" class="reply-btn small" data-id="{{ $comment->id }}">Reply</a>
                                            <div class="replies">
                                                @foreach($comment->replies as $reply)
                                                    <div class="comment" id="comment-{{ $reply->id }}">
                                                        <img src="{{ $reply->user->avatar_url }}" width="25" height="25" class="rounded-circle">
                                                        <div><strong>{{ $reply->user->name }}</strong> {{ $reply->content }}</div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <div class="input-group">
                                <input type="text" class="form-control comment-input" id="comment-input-{{ $post->id }}" placeholder="Add a comment...">
                                <button class="comment-send" data-id="{{ $post->id }}" id="comment-send-{{ $post->id }}" disabled>Send</button>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <p class="text-center text-muted">No reports yet.</p>
            @endforelse

            <div class="d-flex justify-content-center mt-4">{{ $posts->links() }}</div>
        </div>
    </div>
</div>

{{-- DELETE & REPORT MODALS (unchanged) --}}
<div class="modal fade" id="deleteModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header delete-header">
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

<div class="modal fade" id="reportModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header report-header">
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
        <button type="button" class="btn" id="confirmReportBtn">Submit</button>
      </div>
    </div>
  </div>
</div>

{{-- === jQuery + Location Filter Script === --}}
{{-- === jQuery + Location Filter with Smooth Scroll === --}}
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(function() {
    $(document).on('click', '.location-tag', function() {
        const selected = $(this).data('location');
        $('.location-tag').removeClass('active');
        $(this).addClass('active');

        // --- Filter logic ---
        if (selected === 'all') {
            $('.post-card').fadeIn(250);
        } else {
            $('.post-card').hide().filter(function() {
                const loc = $(this).find('.location').text().trim();
                return loc === selected;
            }).fadeIn(250);
        }

        // --- Smooth scroll up to posts section ---
        const target = $('.posts-container').offset()?.top || 0;
        $('html, body').animate({ scrollTop: target - 80 }, 500, 'swing'); 
        // 80px offset so the section isn’t hidden under a fixed navbar
    });
});
</script>

@endsection
