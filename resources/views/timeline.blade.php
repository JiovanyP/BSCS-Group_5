<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Timeline</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.1.1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://maxcdn.icons8.com/fonts/line-awesome/1.1/css/line-awesome.min.css">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <style>
        body { margin-top:20px; background:#FFFFFF; color:#000000; font-family: Arial, sans-serif; }
        .profile-header { background: #000000; color:#fff; text-align:center; padding:40px 20px; border-radius:0 0 15px 15px; }
        .profile-header img { width:120px; height:120px; border-radius:50%; border:4px solid #FF0B55; margin-bottom:15px; }
        .profile-header h3 { margin:0; font-weight:bold; }
        .profile-nav { margin-top:20px; }
        .profile-nav .btn { margin:0 5px; background-color:#CF0F47; border:none; color:#fff; font-weight:600; }
        .profile-nav .btn:hover { background-color:#FF0B55; }

        .timeline { width:100%; position:relative; list-style:none; }
        .timeline .timeline-item { margin-bottom:20px; }
        .timeline:before { display:none; }

        .timeline-label .label { background:#FF0B55; border-radius:35px; color:#fff; padding:.65rem 1.4rem; font-weight:600; }

        .widget { background:#fff; border:1px solid #CF0F47; border-radius:6px; box-shadow:0 2px 10px rgba(0,0,0,.05); }
        .widget-header { padding:.85rem 1.4rem; display:flex; align-items:center; }
        .widget-body { padding:1.4rem; }
        .widget-footer { background:#FFDEDE; padding:1rem; }

        .meta ul { list-style:none; display:flex; padding:0; margin:0; }
        .meta ul li { margin-right:.5rem; display:flex; align-items:center; }
        .meta ul li a i.la { cursor:pointer; font-size:20px; }

        .voted-up i.la-arrow-up { color:#28a745 !important; }
        .voted-down i.la-arrow-down { color:#dc3545 !important; }

        .comments-section { background:#f9f9f9; border-top:1px solid #eee; }
    </style>
</head>
<body>

<div class="profile-header">
    <img src="{{ Auth::user()->avatar ?? 'https://bootdey.com/img/Content/avatar/avatar1.png' }}" alt="Avatar">
    <h3>{{ Auth::user()->name }}</h3>
    <div class="profile-nav">
        <a href="{{ route('timeline') }}" class="btn">Timeline</a>
        <a href="{{ url('/about') }}" class="btn">About</a>
    </div>
</div>

<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-xl-10 col-12">

            {{-- Post Form --}}
            <div class="card mb-4">
                <div class="card-body">
                    <form action="{{ route('timeline.store') }}" method="POST">
                        @csrf
                        <textarea name="content" class="form-control mb-2" rows="3" placeholder="What's on your mind?" required></textarea>
                        <button type="submit" class="btn btn-primary" style="background-color:#FF0B55; border:none;">Post</button>
                    </form>
                </div>
            </div>

            {{-- Timeline --}}
            <div class="timeline">
                @php $currentDate = null; @endphp
                @forelse ($posts as $post)
                    @if ($currentDate !== $post->created_at->toDateString())
                        <div class="timeline-label text-center mb-3">
                            <span class="label">
                                {{ $post->created_at->isToday() ? 'Today' : ($post->created_at->isYesterday() ? 'Yesterday' : $post->created_at->format('F j, Y')) }}
                            </span>
                        </div>
                        @php $currentDate = $post->created_at->toDateString(); @endphp
                    @endif

                    <div class="timeline-item">
                        <div class="widget">
                            <div class="widget-header">
                                <img src="{{ $post->user->avatar ?? 'https://bootdey.com/img/Content/avatar/avatar1.png' }}" class="rounded-circle" width="40" height="40">
                                <div class="ml-2">
                                    <strong>{{ $post->user->name }}</strong>
                                    <div class="small text-muted">{{ $post->created_at->diffForHumans() }}</div>
                                </div>
                            </div>
                            <div class="widget-body">
                                <p>{{ $post->content }}</p>
                            </div>
                            <div class="widget-footer">
                                <div class="meta">
                                    <ul>
                                        <li>
                                            <a class="upvote-btn {{ $post->userVote === 'up' ? 'voted-up' : '' }}" data-id="{{ $post->id }}">
                                                <i class="la la-arrow-up"></i>
                                            </a>
                                            <span id="upvote-count-{{ $post->id }}">{{ $post->upvotes_count ?? 0 }}</span>
                                        </li>
                                        <li>
                                            <a class="downvote-btn {{ $post->userVote === 'down' ? 'voted-down' : '' }}" data-id="{{ $post->id }}">
                                                <i class="la la-arrow-down"></i>
                                            </a>
                                            <span id="downvote-count-{{ $post->id }}">{{ $post->downvotes_count ?? 0 }}</span>
                                        </li>
                                        <li>
                                            <a class="toggle-comments" data-id="{{ $post->id }}">
                                                <i class="la la-comment"></i>
                                                <span>{{ $post->comments_count ?? 0 }}</span>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>

                            {{-- Comments --}}
                            <div class="comments-section p-3" id="comments-section-{{ $post->id }}" style="display:none;">
                                <div class="comments-list mb-2">
                                    @foreach ($post->comments as $comment)
                                        <div class="comment mb-2 d-flex">
                                            <img src="{{ $comment->user->avatar ?? 'https://bootdey.com/img/Content/avatar/avatar2.png' }}" class="rounded-circle mr-2" width="30" height="30">
                                            <div>
                                                <strong>{{ $comment->user->name }}</strong>: {{ $comment->content }}
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                <div class="input-group">
                                    <input type="text" class="form-control comment-input" placeholder="Write a comment...">
                                    <div class="input-group-append">
                                        <button class="btn btn-sm btn-danger comment-send" data-id="{{ $post->id }}">Send</button>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                @empty
                    <p class="text-center text-muted">No posts yet.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });

// Toggle comments
$(document).on('click', '.toggle-comments', function(e){
    e.preventDefault();
    let postId = $(this).data('id');
    $('#comments-section-' + postId).slideToggle('fast');
});

// Upvote
$(document).on('click', '.upvote-btn', function(e){
    e.preventDefault();
    let postId = $(this).data('id');
    $.post(`/posts/${postId}/vote`, { vote: 'upvote' }, function(data){
        $(`#upvote-count-${postId}`).text(data.upvotes_count);
        $(`#downvote-count-${postId}`).text(data.downvotes_count);
        $(`.upvote-btn[data-id="${postId}"]`).toggleClass('voted-up', data.user_vote === 'up');
        $(`.downvote-btn[data-id="${postId}"]`).removeClass('voted-down');
    });
});

// Downvote
$(document).on('click', '.downvote-btn', function(e){
    e.preventDefault();
    let postId = $(this).data('id');
    $.post(`/posts/${postId}/vote`, { vote: 'downvote' }, function(data){
        $(`#upvote-count-${postId}`).text(data.upvotes_count);
        $(`#downvote-count-${postId}`).text(data.downvotes_count);
        $(`.downvote-btn[data-id="${postId}"]`).toggleClass('voted-down', data.user_vote === 'down');
        $(`.upvote-btn[data-id="${postId}"]`).removeClass('voted-up');
    });
});

// Add Comment
$(document).on('click', '.comment-send', function(e){
    e.preventDefault();
    let postId = $(this).data('id');
    let input = $(this).closest('.input-group').find('.comment-input');
    let content = input.val();
    if(!content) return;
    $.post(`/posts/${postId}/comments`, { content: content }, function(data){
        let newComment = `<div class="comment mb-2 d-flex">
            <img src="${data.avatar}" class="rounded-circle mr-2" width="30" height="30">
            <div><strong>${data.user}</strong>: ${data.content}</div>
        </div>`;
        $(`#comments-section-${postId} .comments-list`).append(newComment);
        input.val('');
    });
});
</script>

</body>
</html>