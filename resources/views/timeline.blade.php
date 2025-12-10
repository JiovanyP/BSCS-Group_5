@extends('layouts.app')

@section('title', 'Timeline')

@section('content')
<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
<link href="https://fonts.googleapis.com/icon?family=Material+Icons+Outlined" rel="stylesheet">

<meta name="csrf-token" content="{{ csrf_token() }}">

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.min.js"></script>

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
}

.main-content {
    flex: 1;
    overflow-y: auto;
    position: relative;
    background: #f8f9fa;
    padding: 20px 0;
}

.timeline-label {
    position: sticky;
    top: 10; /* distance from top */
    z-index: 10;
    background: #fff;
    color: var(--accent);
    font-weight: 700;
    display: inline-block;
    padding: 6px 14px;
    border-radius: 20px;
    box-shadow: 0 2px 6px rgba(0,0,0,0.08);
    text-align: center;
    margin: 10px auto; /* center horizontally */
}

.location-tag {
    border-radius: 20px;
    padding: 6px 14px;
    font-weight: 500;
    transition: all 0.2s;
}
.location-tag:hover {
    background-color: var(--accent);
    color: #fff;
}
.location-tag.active {
    background-color: var(--primary);
    color: #fff;
}

.no-posts-message {
    display: none;
    text-align: center;
    color: var(--text-muted);
    font-weight: 500;
    margin-top: 30px;
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

            {{-- Success Alert Container (for AJAX) --}}
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

            {{-- POSTS --}}
            @php $currentDate = null; @endphp
            @forelse($posts as $post)
                @if($currentDate !== $post->created_at->toDateString())
                    <div class="timeline-label text-center font-weight-bold my-3">
                        {{ $post->created_at->isToday() ? 'Today' : ($post->created_at->isYesterday() ? 'Yesterday' : $post->created_at->format('F j, Y')) }}
                    </div>
                    @php $currentDate = $post->created_at->toDateString(); @endphp
                @endif

                @include('partials.post', ['post' => $post, 'singlePost' => false])
            @empty
                <p class="text-center text-muted">No reports yet.</p>
            @endforelse

            <div class="no-posts-message">No posts found for this location.</div>

            <div class="d-flex justify-content-center mt-4">{{ $posts->links() }}</div>
        </div>
    </div>
</div>

{{-- Modals --}}
@include('partials.delete-report-modals')

{{-- External JS for Post Interactions --}}
<script src="{{ asset('js/post-interactions.js') }}"></script>

<script>
// Setup CSRF Token for ALL AJAX calls
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

$(function() {
    // ===== TIMELINE FILTERING & LABEL LOGIC =====
    
    function updateTimelineLabels() {
        // Hide all labels initially
        $('.timeline-label').hide();
        // Show labels that precede at least one visible post
        $('.timeline-label').each(function() {
            const $label = $(this);
            const $nextLabel = $label.nextAll('.timeline-label').first();
            const $postsBetween = $label.nextUntil($nextLabel, '.post-card');
            if ($postsBetween.filter(':visible').length > 0) $label.show();
        });
    }

    function updateEmptyMessage() {
        const visiblePosts = $('.post-card:visible').length;
        $('.no-posts-message').toggle(visiblePosts === 0);
    }

    $(document).on('click', '.location-tag', function() {
        const selected = $(this).data('location');
        $('.location-tag').removeClass('active');
        $(this).addClass('active');

        if(selected === 'all') $('.post-card').fadeIn(250);
        else $('.post-card').hide().filter(function() {
            return $(this).find('.location').text().trim() === selected;
        }).fadeIn(250);

        updateTimelineLabels();
        updateEmptyMessage();

        // Smooth scroll to top of posts container after filtering
        $('html, body').animate({
            scrollTop: $('.posts-container').offset().top - 80
        }, 500);
    });

    // Initial call to set labels correctly on load
    updateTimelineLabels();

    // ===== PREVENT ANCHOR SCROLL =====
    // This is helpful if any partials use an anchor link for dropdowns/modals
    $(document).on('click', 'a[href="#"]', function(e) {
        if (!$(this).attr('data-toggle') && !$(this).attr('data-target')) {
            e.preventDefault();
        }
    });

    // ===== DELETE POST LOGIC (AJAX) =====
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
                    updateTimelineLabels(); // Update labels after deletion
                    updateEmptyMessage();
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

    // ===== REPORT POST LOGIC (AJAX) =====
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
});
</script>
@endsection