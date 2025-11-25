@extends('layouts.app')

@section('title', 'Profile')

@section('content')

<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
<link href="https://fonts.googleapis.com/icon?family=Material+Icons+Outlined" rel="stylesheet">

<meta name="csrf-token" content="{{ csrf_token() }}">

<!-- Load jQuery FIRST, before any other scripts -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- Popper (required for Bootstrap dropdowns) -->
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
<!-- Bootstrap 4 -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.min.js"></script>

<style>
/* === CRITICAL: FORCE CIRCULAR AVATARS FIRST === */
.avatar-wrapper img,
.user-info img,
.comment img,
.post-signature img,
.post-card img[alt*="Avatar"],
img[src*="avatar"],
.avatar-circle,
.user-avatar {
    border-radius: 50% !important;
    object-fit: cover !important;
}

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
    padding: 0;
}

/* === PROFILE HEADER === */
.profile-header {
    background: #fff;
    text-align: center;
    padding: 60px 20px 50px;
    color: #000;
    position: relative;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
}

.profile-header h3 {
    color: #000;
    margin-top: 20px;
    font-size: 2rem;
    font-weight: 700;
    letter-spacing: 3px;
    text-transform: uppercase;
}

.profile-header p {
    color: #333;
}

/* === Avatar Wrapper + Camera Icon Overlay === */
.avatar-wrapper {
    position: relative;
    display: inline-block;
    margin-bottom: 20px;
}
.avatar-wrapper img {
    width: 160px !important;
    height: 160px !important;
    border-radius: 50% !important;
    object-fit: cover !important;
    border: 6px solid #fff;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
    display: block !important;
}

.camera-icon {
    position: absolute;
    bottom: 8px;
    right: 8px;
    background-color: #000;
    border-radius: 50%;
    width: 44px;
    height: 44px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    color: white;
    transition: background 0.2s, transform 0.12s;
    border: 3px solid #fff;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.3);
}
.camera-icon:hover {
    background-color: #222;
    transform: scale(1.05);
}
.camera-icon .material-icons {
    font-size: 20px;
}

/* === ALERT === */
.alert {
    border-radius: 12px;
    animation: fadeIn 0.3s ease-in;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(-10px); }
    to { opacity: 1; transform: translateY(0); }
}

/* === FIXED MODAL CLOSE BUTTON - NO WHITE BACKGROUND === */
.modal-header .close {
    background: transparent !important;
    border: none !important;
    opacity: 0.5;
    padding: 0 !important;
    margin: -1rem -1rem -1rem auto !important;
    font-size: 1.5rem;
    font-weight: 700;
    line-height: 1;
    color: #000 !important;
    text-shadow: none !important;
    transition: opacity 0.2s ease;
    box-shadow: none !important;
    width: auto !important;
    height: auto !important;
}

.modal-header .close:hover,
.modal-header .close:focus {
    opacity: 0.75;
    background: transparent !important;
    outline: none !important;
    box-shadow: none !important;
}

/* Remove any button styling from close button */
button.close {
    background: transparent !important;
    border: 0 !important;
    padding: 0 !important;
}

/* Ensure modal backdrop works properly */
.modal-backdrop {
    background-color: rgba(0, 0, 0, 0.5);
}

/* Modal styling improvements */
.modal-content {
    border-radius: 12px;
    border: none;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
}

.modal-header {
    border-bottom: 1px solid #e9ecef;
    padding: 1.25rem 1.5rem;
    background: #fff;
    border-radius: 12px 12px 0 0;
}

.modal-body {
    padding: 1.5rem;
}

.modal-footer {
    border-top: 1px solid #e9ecef;
    padding: 1rem 1.5rem;
    background: #f8f9fa;
    border-radius: 0 0 12px 12px;
}

/* Delete modal specific styles */
#deleteModal .modal-header {
    background-color: #dc3545;
    color: white;
}

#deleteModal .modal-header .close {
    color: white !important;
    opacity: 1;
    text-shadow: none;
}

#deleteModal .modal-footer {
    display: flex;
    justify-content: flex-end;
    gap: 10px;
}

#deleteModal .btn-secondary {
    background: #6c757d;
    border: none;
    padding: 8px 20px;
    border-radius: 8px;
    font-weight: 600;
    transition: all 0.2s ease;
}

#deleteModal .btn-secondary:hover {
    background: #5a6268;
}

#deleteModal .btn-danger {
    background: var(--accent);
    border: none;
    padding: 8px 20px;
    border-radius: 8px;
    font-weight: 600;
    transition: all 0.2s ease;
}

#deleteModal .btn-danger:hover {
    background: var(--accent-2);
}

/* Report modal specific styles */
#reportModal .modal-header {
    background: #ffc107;
    color: #000;
}

#reportModal .modal-header .close {
    color: #000 !important;
}

#reportModal .form-check {
    padding: 12px;
    border-radius: 8px;
    transition: background 0.2s ease;
}

#reportModal .form-check:hover {
    background: #f8f9fa;
}

#reportModal .form-check-input {
    margin-top: 0.3em;
}

#reportModal .form-check-label {
    cursor: pointer;
    font-weight: 500;
}

#reportModal .modal-footer {
    display: flex;
    justify-content: flex-end;
    gap: 10px;
}

#reportModal .btn-secondary {
    background: #6c757d;
    border: none;
    padding: 8px 20px;
    border-radius: 8px;
    font-weight: 600;
}

#reportModal .btn-warning {
    background: #ffc107;
    border: none;
    padding: 8px 20px;
    border-radius: 8px;
    font-weight: 600;
    color: #000;
}

/* Ensure containers don't clip dropdowns */
.posts-container,
.main-content,
.container,
.post-card,
.row {
    overflow: visible !important;
}

/* Timeline label */
.timeline-label {
    background: #fff;
    color: var(--accent);
    font-weight: 700;
    display: inline-block;
    padding: 6px 14px;
    border-radius: 20px;
    box-shadow: 0 2px 6px rgba(0,0,0,0.08);
}
</style>

<div class="main-content">
    <div class="profile-header">
        {{-- Avatar Form --}}
        <form id="avatarForm" action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PATCH')
            <div class="avatar-wrapper">
                <img id="avatarPreview" src="{{ Auth::user()->avatar_url }}" alt="Avatar">
                <label class="camera-icon" for="avatarInput" title="Change avatar">
                    <span class="material-icons">camera_alt</span>
                </label>
                <input type="file" id="avatarInput" name="avatar" accept="image/*" style="display:none;">
            </div>
        </form>

        <h4 style="font-weight: bold;">{{ strtoupper(Auth::user()->name) }}</h4>
        <p style="margin-top: 8px; font-size: 15px; opacity: 0.95;">
            {{ Auth::user()->email }}
        </p>
        @if(Auth::user()->address)
        <p style="margin-top: 4px; font-size: 14px; opacity: 0.9;">
            <span class="material-icons-outlined" style="font-size: 16px; vertical-align: middle;">location_on</span>
            {{ Auth::user()->address }}
        </p>
        @endif
    </div>

    <div class="container mt-4">
        <div class="col-xl-8 mx-auto posts-container">

            {{-- Unified Success Alert --}}
            <div id="profileSuccessContainer">
                @if (session('success'))
                    <div class="alert alert-success text-center" id="successAlert">
                        {{ session('success') }}
                    </div>
                    <script>
                        setTimeout(() => document.getElementById('successAlert').style.display = 'none', 3000);
                    </script>
                @endif
            </div>

            {{-- Timeline --}}
            @php $currentDate = null; @endphp
            @forelse ($posts as $post)
                @if ($currentDate !== $post->created_at->toDateString())
                    <div class="timeline-label text-center font-weight-bold my-3">
                        {{ $post->created_at->isToday() ? 'Today' : ($post->created_at->isYesterday() ? 'Yesterday' : $post->created_at->format('F j, Y')) }}
                    </div>
                    @php $currentDate = $post->created_at->toDateString(); @endphp
                @endif
                @include('partials.post', ['post' => $post])
            @empty
                <p class="text-center text-muted">No posts yet.</p>
            @endforelse

            @if($posts->hasPages())
                <div class="d-flex justify-content-center mt-4">{{ $posts->links() }}</div>
            @endif
        </div>
    </div>

</div>

{{-- CRITICAL: Include modal partials --}}
@include('partials.delete-report-modals')
@include('partials.edit-modal')

<script>
// ===== COMPLETE FIXED JAVASCRIPT FOR PROFILE PAGE =====

$(document).ready(function() {
    // ===== GLOBAL AJAX SETUP =====
    $.ajaxSetup({
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
    });

    // ===== AVATAR UPLOAD =====
    $('#avatarInput').on('change', function() {
        if (this.files && this.files[0]) {
            const reader = new FileReader();
            reader.onload = e => $('#avatarPreview').attr('src', e.target.result);
            reader.readAsDataURL(this.files[0]);

            const formData = new FormData($('#avatarForm')[0]);
            formData.append('_method', 'PATCH');

            $.ajax({
                url: $('#avatarForm').attr('action'),
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    $('#profileSuccessContainer').html(`
                        <div class="alert alert-success text-center" id="successAlert">
                            Profile picture updated successfully!
                        </div>
                    `);
                    setTimeout(() => $('#successAlert').fadeOut(500, function() { $(this).remove(); }), 3000);

                    if (response.avatar) {
                        $('#avatarPreview').attr('src', response.avatar);
                        const userId = response.user_id || {{ Auth::id() }};
                        $(`.user-avatar-${userId}`).attr('src', response.avatar);
                    }
                },
                error: function(xhr) {
                    console.error(xhr.responseText);
                    alert('Failed to update avatar.');
                }
            });
        }
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

                $('#profileSuccessContainer').html(`
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
            alert('Please select a reason for reporting.');
            return;
        }

        const submitBtn = $('#reportSubmitBtn');
        submitBtn.prop('disabled', true).text('Submitting...');

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

                $('#profileSuccessContainer').html(`
                    <div class="alert alert-success text-center" id="successAlert">
                        Post reported successfully!
                    </div>
                `);
                setTimeout(() => $('#successAlert').fadeOut(500, function() {
                    $(this).remove();
                }), 3000);

                submitBtn.prop('disabled', false).text('Submit Report');
            },
            error: function(xhr) {
                console.error('Report failed:', xhr.responseText);
                $('#reportModal').modal('hide');

                if (xhr.status === 409) {
                    alert('You have already reported this post.');
                } else {
                    alert('Failed to report post. Please try again.');
                }

                submitBtn.prop('disabled', false).text('Submit Report');
            }
        });
    });

    // ===== EDIT POST =====
    $(document).on('click', '.edit-btn', function(e) {
        e.preventDefault();
        e.stopPropagation();
        const postId = $(this).data('id');

        $.get(`/posts/${postId}/edit`, function(response) {
            $('#editPostId').val(postId);
            $('#editContent').val(response.content);

            if (response.image) {
                $('#currentImage').attr('src', `/storage/${response.image}`).show();
                $('#removeImageCheckbox').parent().show();
            } else {
                $('#currentImage').hide();
                $('#removeImageCheckbox').parent().hide();
            }

            $('#editModal').modal('show');
        }).fail(function(xhr) {
            console.error('Failed to load post:', xhr.responseText);
            alert('Failed to load post data.');
        });
    });

    $(document).on('submit', '#editPostForm', function(e) {
        e.preventDefault();

        const postId = $('#editPostId').val();
        const formData = new FormData(this);
        formData.append('_method', 'PUT');

        const submitBtn = $(this).find('button[type="submit"]');
        submitBtn.prop('disabled', true).text('Updating...');

        $.ajax({
            url: `/posts/${postId}`,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                $('#editModal').modal('hide');

                $(`#post-${postId} .post-body p`).text(response.content);

                if (response.image) {
                    const imgHtml = `<img src="/storage/${response.image}" alt="Post image" class="post-image">`;
                    if ($(`#post-${postId} .post-body img`).length) {
                        $(`#post-${postId} .post-body img`).attr('src', `/storage/${response.image}`);
                    } else {
                        $(`#post-${postId} .post-body`).append(imgHtml);
                    }
                } else if (response.image_removed) {
                    $(`#post-${postId} .post-body img`).remove();
                }

                $('#profileSuccessContainer').html(`
                    <div class="alert alert-success text-center" id="successAlert">
                        Post updated successfully!
                    </div>
                `);
                setTimeout(() => $('#successAlert').fadeOut(500, function() {
                    $(this).remove();
                }), 3000);

                submitBtn.prop('disabled', false).text('Update Post');
            },
            error: function(xhr) {
                console.error('Update failed:', xhr.responseText);
                alert('Failed to update post. Please try again.');
                submitBtn.prop('disabled', false).text('Update Post');
            }
        });
    });
});
</script>

@endsection