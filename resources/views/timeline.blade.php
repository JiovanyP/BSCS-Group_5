<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Timeline</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.1.1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://maxcdn.icons8.com/fonts/line-awesome/1.1/css/line-awesome.min.css">

    <style>
        body { margin-top:20px; background:#FFFFFF; color:#000000; }
        .timeline { width:100%; position:relative; padding:1px 0; list-style:none; font-weight:500; }
        .timeline .timeline-item { position:relative; float:left; clear:left; width:50%; margin-bottom:20px; }
        .timeline .timeline-item>.timeline-event { position:relative; float:left; width:100%; }
        .timeline .timeline-item>.timeline-point {
            background:#CF0F47; border-color:#FFDEDE; right:-14px; width:12px; height:12px;
            margin-top:-6px; margin-left:8px; margin-right:8px; position:absolute; z-index:100;
            border-width:3px; border-style:solid; border-radius:100%; line-height:20px;
            text-align:center; box-shadow:0 0 0 5px #f2f3f8;
        }
        .timeline:before { content:""; position:absolute; top:0; left:0; bottom:0; width:50%;
            margin-left:2px; border-right:4px solid #000000; }
        .timeline .timeline-label { position:relative; float:left; clear:left; width:100%;
            margin:20px auto; text-align:center; }
        .timeline .timeline-label .label {
            background-color:#FF0B55; border-radius:35px; color:#fff; display:inline;
            font-size:.85rem; font-weight:600; line-height:1; padding:.65rem 1.4rem;
            text-align:center; vertical-align:baseline; white-space:nowrap;
        }
        .widget { background:#fff; border-radius:6px; border:1px solid #CF0F47;
            margin-bottom:30px; box-shadow:0 2px 10px rgba(0,0,0,.05); }
        .widget-header { background:#fff; padding:.85rem 1.4rem; position:relative; width:100%; }
        .widget-body { padding:1.4rem; }
        .widget-footer { background:#FFDEDE; padding:1rem 1.07rem; position:relative; }
        .meta ul { list-style:none; padding:0; margin:0; display:flex; }
        .meta ul li { margin-right:0.5rem; }
        .meta ul li a { font-size:1.1rem; transition:0.3s; }
        .meta ul li a i.la-heart { color:#FF0B55; }
        .meta ul li a i.la-heart:hover { color:#CF0F47; }
        .meta ul li a i.la-comment { color:#000000; }
        .meta ul li a i.la-comment:hover { color:#CF0F47; }
        .user-image img { width:40px; height:40px; margin-right:10px; border:2px solid #CF0F47; }
        .time-right { float:right; font-size:0.85rem; color:#000000; margin-top:10px; }
        @media screen and (max-width:768px) {
            .timeline .timeline-item { width:100%; margin-bottom:20px; }
            .timeline:before { left:42px; width:0; }
            .timeline .timeline-item>.timeline-point { transform:translateX(-50%); left:42px!important; margin-left:0; }
            .timeline .timeline-label { transform:translateX(-50%); margin:0 0 20px 42px; }
        }
    </style>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>
<div class="container">
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

            {{-- Show Errors --}}
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="timeline timeline-line-solid">
                @php $currentDate = null; @endphp

                @forelse ($posts as $post)
                    @if ($currentDate !== $post->created_at->toDateString())
                        <span class="timeline-label">
                            <span class="label">
                                @if ($post->created_at->isToday())
                                    Today
                                @elseif ($post->created_at->isYesterday())
                                    Yesterday
                                @else
                                    {{ $post->created_at->format('F j, Y') }}
                                @endif
                            </span>
                        </span>
                        @php $currentDate = $post->created_at->toDateString(); @endphp
                    @endif

                    <div class="timeline-item">
                        <div class="timeline-point"></div>
                        <div class="timeline-event">
                            <div class="widget has-shadow">
                                <div class="widget-header d-flex align-items-center">
                                    <div class="user-image">
                                        <img class="rounded-circle" src="{{ $post->user->avatar ?? 'https://bootdey.com/img/Content/avatar/avatar1.png' }}" alt="...">
                                    </div>
                                    <div class="d-flex flex-column mr-auto ml-2">
                                        <div class="title">
                                            <span class="username font-weight-bold">{{ $post->user->name }}</span>
                                        </div>
                                    </div>
                                    <div class="time-right">{{ $post->created_at->diffForHumans() }}</div>
                                </div>

                                <div class="widget-body">
                                    <p>{{ $post->content }}</p>
                                </div>

                                <div class="widget-footer d-flex align-items-center">
                                    <div class="col no-padding d-flex justify-content-start">
                                        <div class="meta">
                                            <ul>
                                                <li>
                                                    <a href="#" class="like-btn" data-id="{{ $post->id }}">
                                                        <i class="la la-heart"></i>
                                                        <span class="numb">{{ $post->likes_count ?? 0 }}</span>
                                                    </a>
                                                </li>
                                                <li>
                                                    <a href="#" class="comment-btn" data-id="{{ $post->id }}">
                                                        <i class="la la-comment"></i>
                                                        <span class="numb">{{ $post->comments_count ?? 0 }}</span>
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>

                                {{-- Comment box --}}
                                <div class="widget-footer">
                                    <div class="input-group">
                                        <input type="text" class="form-control comment-input" placeholder="Write a comment...">
                                        <div class="input-group-append">
                                            <button class="btn btn-sm comment-send" data-id="{{ $post->id }}" style="background-color:#FF0B55; color:#fff; border:none;">Send</button>
                                        </div>
                                    </div>

                                    {{-- Comments list --}}
                                    <div class="comments-list mt-2">
                                        @foreach ($post->comments as $comment)
                                            <div class="comment mb-2" id="comment-{{ $comment->id }}">
                                                <strong>{{ $comment->user->name }}:</strong> {{ $comment->content }}
                                                <a href="#" class="reply-btn ml-2 small text-primary" data-id="{{ $comment->id }}">Reply</a>

                                                {{-- Replies --}}
                                                <div class="replies ml-4 mt-1">
                                                    @foreach ($comment->replies as $reply)
                                                        <div class="reply mb-1" id="comment-{{ $reply->id }}">
                                                            <strong>{{ $reply->user->name }}:</strong> {{ $reply->content }}
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endforeach
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

<script src="https://code.jquery.com/jquery-1.10.2.min.js"></script>
<script>
    $(document).ready(function () {
        // ✅ Setup CSRF token
        $.ajaxSetup({
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
        });

        // ✅ Like button
        $(document).on('click', '.like-btn', function(e){
            e.preventDefault();
            let btn = $(this);
            let postId = btn.data('id');

            $.post(`/posts/${postId}/like`, {}, function(res){
                btn.find('.numb').text(res.likes_count);
                btn.find('i').css('color', res.liked ? '#CF0F47' : '#FF0B55');
            });
        });

        // ✅ Comment send (new comments + replies)
        $(document).on('click', '.comment-send', function(){
            let btn = $(this);
            let postId = btn.data('id');
            let input = btn.closest('.input-group').find('.comment-input');
            let content = input.val();
            let parentId = input.data('parent') || null;

            if(content.trim() === '') return;

            $.post(`/posts/${postId}/comment`, { content: content, parent_id: parentId }, function(res){
                input.val('');
                input.removeAttr('data-parent');

                if(res.parent_id){
                    // Append as reply
                    $(`#comment-${res.parent_id} .replies`).append(
                        `<div class="reply mb-1" id="comment-${res.id}">
                            <strong>${res.user}:</strong> ${res.comment}
                        </div>`
                    );
                } else {
                    // Append as top-level comment
                    btn.closest('.widget-footer').find('.comments-list').append(
                        `<div class="comment mb-2" id="comment-${res.id}">
                            <strong>${res.user}:</strong> ${res.comment}
                            <a href="#" class="reply-btn ml-2 small text-primary" data-id="${res.id}">Reply</a>
                            <div class="replies ml-4 mt-1"></div>
                        </div>`
                    );
                }

                btn.closest('.widget').find('.comment-btn .numb').text(res.comments_count);
            });
        });

        // ✅ Reply button → focus input
        $(document).on('click', '.reply-btn', function(e){
            e.preventDefault();
            let parentId = $(this).data('id');
            let input = $(this).closest('.widget').find('.comment-input');
            input.focus();
            input.attr('data-parent', parentId);
        });
    });
</script>
</body>
</html>
