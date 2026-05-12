@extends('layouts.app')

@section('title', 'Profile')

@section('content')

<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
<link href="https://fonts.googleapis.com/icon?family=Material+Icons+Outlined" rel="stylesheet">

<meta name="csrf-token" content="{{ csrf_token() }}">

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.min.js"></script>

<style>
/* === CRITICAL: FORCE CIRCULAR AVATARS FIRST === */
/* specific to profile header only to avoid messing up post images */
.profile-header .avatar-wrapper img, 
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

            {{-- Timeline Loop --}}
            @php $currentDate = null; @endphp
            @forelse ($posts as $post)
                @if ($currentDate !== $post->created_at->toDateString())
                    <div class="timeline-label text-center font-weight-bold my-3">
                        {{ $post->created_at->isToday() ? 'Today' : ($post->created_at->isYesterday() ? 'Yesterday' : $post->created_at->format('F j, Y')) }}
                    </div>
                    @php $currentDate = $post->created_at->toDateString(); @endphp
                @endif

                {{-- FIX: Added 'singlePost' => false so images render correctly --}}
                @include('partials.post', ['post' => $post, 'singlePost' => false])
                
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

{{-- FIX: Load external Interactions JS (Votes, Comments, Deletes) --}}
<script src="{{ asset('js/post-interactions.js') }}"></script>

<script>
$(document).ready(function() {
    // ===== GLOBAL AJAX SETUP =====
    $.ajaxSetup({
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
    });

    // ===== AVATAR UPLOAD (Specific to Profile) =====
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
                        // Update any other instances of this user's avatar on the page
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

    // ===== EDIT POST (Specific to Profile if not in shared JS) =====
    // We keep this here because it wasn't explicitly shown in the Timeline file, 
    // ensuring the "Edit" button on your profile still works.
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
                
                // Update post content in DOM
                $(`#post-${postId} .post-body p`).text(response.content);

                // Handle image updates in DOM
                const bodyContainer = $(`#post-${postId} .post-body`);
                if (response.image) {
                    const imgHtml = `<img src="/storage/${response.image}" alt="Post image" class="post-image">`;
                    if (bodyContainer.find('img').length) {
                        bodyContainer.find('img').attr('src', `/storage/${response.image}`);
                    } else {
                        bodyContainer.append(imgHtml);
                    }
                } else if (response.image_removed) {
                    bodyContainer.find('img').remove();
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