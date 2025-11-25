@extends('layouts.app')

@section('title', 'Timeline')

@section('content')
<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
<link href="https://fonts.googleapis.com/icon?family=Material+Icons+Outlined" rel="stylesheet">
<meta name="csrf-token" content="{{ csrf_token() }}">

<!-- Load jQuery FIRST, before any other scripts -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- Popper (required for Bootstrap dropdowns) -->
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.min.js"></script>

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

/* simple post-card selector used by filter */
.post-card {
    display: block;
}

/* Ensure containers don't clip dropdowns */
.posts-container,
.main-content,
.container {
    overflow: visible !important;
}
</style>

<div class="main-content">
    <div class="container mt-4">
        <div class="col-xl-8 mx-auto posts-container">

            {{-- Success Alert Container --}}
            <div id="timelineSuccessContainer"></div>

            {{-- Success Alert from Server --}}
            @if (session('success'))
                <div class="alert alert-success text-center" id="successAlert">
                    {{ session('success') }}
                </div>
                <script>
                    setTimeout(() => {
                        $('#successAlert').fadeOut(500, function() { $(this).remove(); });
                    }, 3000);
                </script>
            @endif

            {{-- LOCATION FILTER TAGS --}}
            @php
                $uniqueLocations = $posts->pluck('location')->filter()->unique()->values();
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

            {{-- TIMELINE POSTS (grouped by date) --}}
            @php $currentDate = null; @endphp
            @forelse ($posts as $post)
                @if ($currentDate !== $post->created_at->toDateString())
                    <div class="timeline-label text-center font-weight-bold my-3">
                        {{ $post->created_at->isToday() ? 'Today' : ($post->created_at->isYesterday() ? 'Yesterday' : $post->created_at->format('F j, Y')) }}
                    </div>
                    @php $currentDate = $post->created_at->toDateString(); @endphp
                @endif

                {{-- Reusable Post Partial --}}
                @include('partials.post', ['post' => $post])
            @empty
                <p class="text-center text-muted">No reports yet.</p>
            @endforelse

            {{-- PAGINATION --}}
            <div class="d-flex justify-content-center mt-4">{{ $posts->links() }}</div>
        </div>
    </div>
</div>

{{-- Modals --}}
@include('partials.delete-report-modals')

{{-- ===== CONSOLIDATED JAVASCRIPT ===== --}}
<script>
$(function() {
    // ===== GLOBAL AJAX SETUP =====
    $.ajaxSetup({ 
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } 
    });

    // ===== PREVENT ANCHOR SCROLL =====
    $(document).on('click', 'a[href="#"]', function(e) {
        if (!$(this).attr('data-toggle') && !$(this).attr('data-target')) {
            e.preventDefault();
        }
    });

    // ===== LOCATION FILTER =====
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

        $('html, body').animate({ 
            scrollTop: $('.posts-container').offset().top - 80 
        }, 500);
    });

    // ===== TOGGLE COMMENTS =====
    $(document).on('click', '.toggle-comments', function(e) {
        e.preventDefault();
        e.stopPropagation();
        const id = $(this).data('id');
        $(`#comments-section-${id}`).slideToggle(200);
    });

    // ===== DYNAMIC SEND BUTTON ENABLE/DISABLE =====
    $(document).on('input', '.comment-input', function() {
        const postId = $(this).attr('id').replace('comment-input-', '');
        const sendBtn = $(`#comment-send-${postId}`);
        sendBtn.prop('disabled', $(this).val().trim() === '');
    });

    $(document).on('input', '.reply-input', function() {
        const sendBtn = $(this).closest('.reply-input-group').find('.reply-send');
        sendBtn.prop('disabled', $(this).val().trim() === '');
    });

    // ===== ADD COMMENT =====
    $(document).on('click', '.comment-send', function() {
        const btn = $(this);
        if (btn.prop('disabled')) return;
        
        const id = btn.data('id');
        const input = $(`#comment-input-${id}`);
        const content = input.val().trim();
        
        if (!content) return;

        btn.prop('disabled', true).text('Sending...');

        $.post(`/posts/${id}/comments`, { content: content })
            .done(function(res) {
                const html = `<div class="comment" id="comment-${res.id}">
                    <img src="${res.avatar}" class="avatar-circle" alt="Avatar">
                    <div style="flex: 1;">
                        <div><strong>${res.user}</strong> ${res.comment}</div>
                        <a href="#" class="reply-btn small" data-id="${res.id}">Reply</a>
                        <div class="replies"></div>
                    </div>
                </div>`;

                if ($(`#comments-section-${id} .comments-list`).length === 0) {
                    $(`#comments-section-${id}`).prepend(
                        '<div class="comments-header">Comments</div><div class="comments-list mb-3"></div>'
                    );
                }

                $(`#comments-section-${id} .comments-list`).append(html);
                
                const countEl = $(`#comment-count-${id}`);
                const count = parseInt(countEl.text() || '0') + 1;
                countEl.text(count);
                
                input.val('');
                btn.prop('disabled', false).text('Send');
            })
            .fail(function(xhr) {
                console.error('Comment failed:', xhr.responseText);
                alert('Failed to add comment. Please try again.');
                btn.prop('disabled', false).text('Send');
            });
    });

    // ===== REPLY BUTTON =====
    $(document).on('click', '.reply-btn', function(e) {
        e.preventDefault();
        const commentId = $(this).data('id');
        const repliesDiv = $(`#comment-${commentId} .replies`);

        if (repliesDiv.find('.reply-input-group').length === 0) {
            const replyInputId = `reply-input-${commentId}`;
            const replySendId = `reply-send-${commentId}`;

            const replyInput = `<div class="reply-input-group">
                <input type="text" class="form-control reply-input" id="${replyInputId}" placeholder="Write a reply...">
                <button class="reply-send btn btn-sm btn-primary" data-comment-id="${commentId}" id="${replySendId}" disabled>Send</button>
            </div>`;
            
            repliesDiv.append(replyInput);
            $(`#${replyInputId}`).trigger('input').focus();
        }
    });

    // ===== SEND REPLY =====
    $(document).on('click', '.reply-send', function() {
        const btn = $(this);
        if (btn.prop('disabled')) return;
        
        const commentId = btn.data('comment-id');
        const input = $(`#reply-input-${commentId}`);
        const content = input.val().trim();
        
        if (!content) return;

        btn.prop('disabled', true).text('Sending...');

        $.post(`/comments/${commentId}/reply`, { content: content })
            .done(function(res) {
                const html = `<div class="comment" id="comment-${res.id}">
                    <img src="${res.avatar}" class="avatar-circle" alt="Avatar">
                    <div><strong>${res.user}</strong> ${res.content}</div>
                </div>`;
                
                $(`#comment-${commentId} .replies`).prepend(html);

                const postId = $(`#comment-${commentId}`).closest('.post-content').find('.toggle-comments').data('id');
                const countSpan = $(`#comment-count-${postId}`);
                countSpan.text(parseInt(countSpan.text() || '0') + 1);

                input.closest('.reply-input-group').remove();
            })
            .fail(function(xhr) {
                console.error('Reply failed:', xhr.responseText);
                alert('Failed to send reply. Try again.');
                btn.prop('disabled', false).text('Send');
            });
    });

    // ===== DELETE POST =====
    $(document).on('click', '.delete-post-btn', function(e) {
        e.preventDefault();
        e.stopPropagation();
        const postId = $(this).data('id');
        
        $('#deleteForm').attr('action', `/posts/${postId}`);
        $('#deleteModal').modal('show');
    });

    $(document).on('submit', '#deleteForm', function(e) {
        e.preventDefault();

        const form = $(this);
        const action = form.attr('action');
        const postId = action.split('/').pop();

        $.ajax({
            url: action,
            type: 'POST',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
                _method: 'DELETE'
            },
            success: function(response) {
                $('#deleteModal').modal('hide');

                $(`#post-${postId}`).fadeOut(300, function() {
                    $(this).remove();
                });

                $('#timelineSuccessContainer').html(`
                    <div class="alert alert-success text-center" id="successAlert">
                        Post deleted successfully!
                    </div>
                `);
                setTimeout(() => $('#successAlert').fadeOut(500, function() {
                    $(this).remove();
                }), 3000);
            },
            error: function(xhr) {
                console.error('Delete failed:', xhr.responseText);
                $('#deleteModal').modal('hide');
                alert('Failed to delete post. Please try again.');
            }
        });
    });

    // ===== REPORT POST =====
    $(document).on('click', '.report-post-btn', function(e) {
        e.preventDefault();
        e.stopPropagation();
        const postId = $(this).data('id');

        $('#reportForm').attr('action', `/posts/${postId}/report`);
        $('input[name="reason"]').prop('checked', false);
        $('#reportModal').modal('show');
    });

    $(document).on('submit', '#reportForm', function(e) {
        e.preventDefault();

        const form = $(this);
        const action = form.attr('action');
        const reason = $('input[name="reason"]:checked').val();

        if (!reason) {
            alert('Please select a reason for the report.');
            return;
        }

        const btn = $('#reportSubmitBtn');
        btn.prop('disabled', true).text('Reporting...');

        $.ajax({
            url: action,
            type: 'POST',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
                reason: reason
            },
            success: function(response) {
                $('#reportModal').modal('hide');
                $('input[name="reason"]').prop('checked', false);
                
                $('#timelineSuccessContainer').html(`
                    <div class="alert alert-success text-center" id="successAlert">
                        ${response.message || 'Post reported successfully!'}
                    </div>
                `);
                setTimeout(() => $('#successAlert').fadeOut(500, function() {
                    $(this).remove();
                }), 3000);
                
                btn.prop('disabled', false).text('Submit Report');
            },
            error: function(xhr) {
                console.error('Report failed:', xhr.responseText);
                $('#reportModal').modal('hide');

                if (xhr.status === 409) {
                    alert('You have already reported this post.');
                } else if (xhr.responseJSON && xhr.responseJSON.message) {
                    alert(xhr.responseJSON.message);
                } else {
                    alert('Failed to report post. Please try again.');
                }

                btn.prop('disabled', false).text('Submit Report');
            }
        });
    });

    // ===== VOTING LOGIC (Reddit-style) =====
    $(document).on('click', '.upvote-btn, .downvote-btn', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        const btn = $(this);
        const postId = btn.data('id');
        const isUpvote = btn.hasClass('upvote-btn');
        const upvoteBtn = $(`.upvote-btn[data-id="${postId}"]`);
        const downvoteBtn = $(`.downvote-btn[data-id="${postId}"]`);
        const voteCountEl = $(`#upvote-count-${postId}`);
        
        const wasUpvoted = upvoteBtn.hasClass('voted-up');
        const wasDownvoted = downvoteBtn.hasClass('voted-down');
        const currentCount = parseInt(voteCountEl.text());
        
        let newCount = currentCount;
        let newVoteState = null;
        
        if (isUpvote) {
            if (wasUpvoted) {
                newCount = currentCount - 1;
                newVoteState = null;
            } else if (wasDownvoted) {
                newCount = currentCount + 2;
                newVoteState = 'up';
            } else {
                newCount = currentCount + 1;
                newVoteState = 'up';
            }
        } else {
            if (wasDownvoted) {
                newCount = currentCount + 1;
                newVoteState = null;
            } else if (wasUpvoted) {
                newCount = currentCount - 2;
                newVoteState = 'down';
            } else {
                newCount = currentCount - 1;
                newVoteState = 'down';
            }
        }
        
        voteCountEl.text(newCount);
        upvoteBtn.removeClass('voted-up');
        downvoteBtn.removeClass('voted-down');
        
        if (newVoteState === 'up') {
            upvoteBtn.addClass('voted-up');
        } else if (newVoteState === 'down') {
            downvoteBtn.addClass('voted-down');
        }
        
        const vote = isUpvote ? 'up' : 'down';
        
        $.post(`/posts/${postId}/vote`, { vote: vote })
            .done(function(res) {
                const serverCount = (res.upvotes_count || 0) - (res.downvotes_count || 0);
                voteCountEl.text(serverCount);

                upvoteBtn.removeClass('voted-up');
                downvoteBtn.removeClass('voted-down');

                if (res.user_vote === 'up') {
                    upvoteBtn.addClass('voted-up');
                } else if (res.user_vote === 'down') {
                    downvoteBtn.addClass('voted-down');
                }
            })
            .fail(function(xhr) {
                console.error('Vote failed:', xhr.responseText);
                voteCountEl.text(currentCount);
                upvoteBtn.toggleClass('voted-up', wasUpvoted);
                downvoteBtn.toggleClass('voted-down', wasDownvoted);
                
                if (xhr.responseJSON && xhr.responseJSON.error) {
                    alert(xhr.responseJSON.error);
                } else {
                    alert('Failed to register vote. Please try again.');
                }
            });
    });
});
</script>

@endsection