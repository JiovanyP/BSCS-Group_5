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

/* === TIMELINE LABEL === */
.timeline-label {
    background: #fff;
    color: var(--accent);
    font-weight: 700;
    display: inline-block;
    padding: 6px 14px;
    border-radius: 20px;
    box-shadow: 0 2px 6px rgba(0,0,0,0.08);
}

/* === LOCATION TAGS === */
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
        <div class="col-xl-8 mx-auto posts-container">

            {{-- ✅ Success Alert --}}
            @if (session('success'))
                <div class="alert alert-success text-center" id="successAlert">
                    {{ session('success') }}
                </div>
                <script>
                    setTimeout(() => document.getElementById('successAlert').style.display = 'none', 3000);
                </script>
            @endif

            {{-- ✅ LOCATION FILTER TAGS --}}
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

            {{-- ✅ TIMELINE POSTS (using partial) --}}
            @php $currentDate = null; @endphp
            @forelse ($posts as $post)
                @if ($currentDate !== $post->created_at->toDateString())
                    <div class="timeline-label text-center font-weight-bold my-3">
                        {{ $post->created_at->isToday() ? 'Today' : ($post->created_at->isYesterday() ? 'Yesterday' : $post->created_at->format('F j, Y')) }}
                    </div>
                    @php $currentDate = $post->created_at->toDateString(); @endphp
                @endif

                {{-- ✅ Reusable Post Partial --}}
                @include('partials.post', ['post' => $post])
            @empty
                <p class="text-center text-muted">No reports yet.</p>
            @endforelse

            {{-- ✅ Pagination --}}
            <div class="d-flex justify-content-center mt-4">{{ $posts->links() }}</div>
        </div>
    </div>
</div>

{{-- ✅ Shared Delete & Report Modals --}}
@include('partials.delete-report-modals')

{{-- === jQuery + Bootstrap JS === --}}
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
$(function() {
    $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });
    let currentPostId = null;

    // Prevent scroll-to-top on placeholder links
    $(document).on('click', 'a[href="#"]', function(e) {
        if (!$(this).attr('data-toggle') && !$(this).attr('data-target')) e.preventDefault();
    });

    // Location filter
    $(document).on('click', '.location-tag', function() {
        const selected = $(this).data('location');
        $('.location-tag').removeClass('active');
        $(this).addClass('active');

        if (selected === 'all') {
            $('.post-card').fadeIn(250);
        } else {
            $('.post-card').hide().filter(function() {
                const loc = $(this).find('.location').text().trim();
                return loc === selected;
            }).fadeIn(250);
        }

        $('html, body').animate({ scrollTop: $('.posts-container').offset().top - 80 }, 500, 'swing');
    });

    // Toggle comments
    $(document).on('click', '.toggle-comments', function(e) {
        e.preventDefault();
        e.stopPropagation();
        const id = $(this).data('id');
        $(`#comments-section-${id}`).slideToggle(200);
    });

    // Dynamic Send Button Logic
    $(document).on('input', '.comment-input', function() {
        const postId = $(this).attr('id').replace('comment-input-', '');
        const sendBtn = $(`#comment-send-${postId}`);
        sendBtn.prop('disabled', $(this).val().trim() === '');
    });

    $(document).on('input', '.reply-input', function() {
        const sendBtn = $(this).closest('.reply-input-group').find('.reply-send');
        sendBtn.prop('disabled', $(this).val().trim() === '');
    });

    // Add Comment
    $(document).on('click', '.comment-send:not(:disabled)', function() {
        const btn = $(this);
        const id = btn.data('id');
        const input = $(`#comment-input-${id}`);
        const content = input.val().trim();
        if (!content) return;

        btn.prop('disabled', true);
        btn.text('Sending...');

        $.post(`/posts/${id}/comments`, {content: content}, res => {
            const html = `<div class="comment" id="comment-${res.id}">
                <img src="${res.avatar}" width="28" height="28" class="rounded-circle">
                <div style="flex: 1;">
                    <div><strong>${res.user}</strong> ${res.comment}</div>
                    <a href="#" class="reply-btn small" data-id="${res.id}">Reply</a>
                    <div class="replies"></div>
                </div>
            </div>`;
            
            if ($(`#comments-section-${id} .comments-list`).length === 0) {
                $(`#comments-section-${id}`).prepend('<div class="comments-header">Comments</div><div class="comments-list mb-3"></div>');
            }
            
            $(`#comments-section-${id} .comments-list`).append(html);
            const count = parseInt($(`#comment-count-${id}`).text()) + 1;
            $(`#comment-count-${id}`).text(count);
            input.val('');
            btn.text('Send');
        });
    });

    // Reply Button
    $(document).on('click', '.reply-btn', function(e) {
        e.preventDefault();
        const commentId = $(this).data('id');
        const repliesDiv = $(`#comment-${commentId} .replies`);

        if (repliesDiv.find('.reply-input-group').length === 0) {
            const replyInputId = `reply-input-${commentId}`;
            const replySendId = `reply-send-${commentId}`;

            const replyInput = `<div class="reply-input-group">
                <input type="text" class="form-control reply-input" id="${replyInputId}" placeholder="Write a reply...">
                <button class="reply-send" data-comment-id="${commentId}" id="${replySendId}" disabled>Send</button>
            </div>`;
            repliesDiv.append(replyInput);
            $(`#${replyInputId}`).trigger('input').focus();
        }
    });

    // Send Reply
    $(document).on('click', '.reply-send:not(:disabled)', function() {
        const btn = $(this);
        const commentId = btn.data('comment-id');
        const input = $(`#reply-input-${commentId}`);
        const content = input.val().trim();
        if (!content) return;

        btn.prop('disabled', true);
        btn.text('Sending...');

        $.post(`/comments/${commentId}/reply`, {content: content}, res => {
            const html = `<div class="comment" id="comment-${res.id}">
                <img src="${res.avatar}" width="25" height="25" class="rounded-circle">
                <div><strong>${res.user}</strong> ${res.content}</div>
            </div>`;
            $(`#comment-${commentId} .replies`).prepend(html);
            
            const postId = $(`#comment-${commentId}`).closest('.post-content').find('.toggle-comments').data('id');
            const countSpan = $(`#comment-count-${postId}`);
            countSpan.text(parseInt(countSpan.text()) + 1);

            input.closest('.reply-input-group').remove();
        });
    });

    // ===== DELETE POST =====
    let postIdToDelete = null;

    $(document).on('click', '.delete-post-btn', function () {
        postIdToDelete = $(this).data('id');
        $('#deleteForm').attr('action', `/posts/${postIdToDelete}`);
        $('#deleteModal').modal('show');
    });

    $('#deleteModal').on('hidden.bs.modal', function () {
        $('#deleteForm').attr('action', '');
        postIdToDelete = null;
    });

    // Report Post
    $(document).on('click', '.report-post-btn', function() { 
        currentPostId = $(this).data('id'); 
    });
    
    $('#confirmReportBtn').click(function() {
        const reason = $('input[name="reason"]:checked').val();
        if (!reason) { 
            alert('Please select a reason'); 
            return; 
        }
        $.post(`/posts/${currentPostId}/report`, {reason: reason}, () => {
            $('#reportModal').modal('hide');
            alert('Thank you for your report.');
        });
    });
});
</script>

{{-- ✅ Include Reddit-style Voting Logic --}}
@include('partials.voting-logic')

@endsection