<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Timeline</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://maxcdn.icons8.com/fonts/line-awesome/1.1/css/line-awesome.min.css">

<style>
/* === Base === */
body {
    background-color: #f5f7f8;
    color: #1a1a1a;
    font-family: "Inter", Arial, sans-serif;
    margin: 0;
}

/* === Profile Header === */
.profile-header {
    background: #FF0B55;
    text-align: center;
    padding: 80px 20px 40px;
    border-radius: 0 0 20px 20px;
    color: white;
    position: relative;
    box-shadow: 0 3px 10px rgba(0,0,0,0.1);
}
.profile-header h3 {
    font-weight: 700;
    letter-spacing: 1px;
}
.profile-pic-wrapper { position: relative; display: inline-block; }
.profile-header img {
    width: 120px; height: 120px;
    border-radius: 50%; border: 4px solid #fff;
    object-fit: cover; transition: 0.3s ease;
}
.camera-overlay {
    position: absolute; bottom: 5px; right: 5px;
    width: 35px; height: 35px;
    background: #fff; color: #FF0B55;
    display: flex; align-items: center; justify-content: center;
    border-radius: 50%; opacity: 0; transition: 0.3s;
    cursor: pointer; border: 2px solid #fff;
}
.profile-pic-wrapper:hover img { filter: brightness(0.9); }
.profile-pic-wrapper:hover .camera-overlay { opacity: 1; }

/* Exit button */
.btn-outline-light {
    position: absolute; top: 20px; right: 20px;
    border: 2px solid #fff; color: #fff;
    background: none; border-radius: 6px;
    padding: 6px 14px; text-decoration: none;
    font-weight: bold; transition: 0.3s ease;
}
.btn-outline-light:hover {
    background: #fff;
    color: #FF0B55;
}

/* === Post Form === */
.card.post-form {
    border: none;
    border-radius: 12px;
    box-shadow: 0 3px 12px rgba(0,0,0,0.05);
}
.card.post-form textarea {
    border: none;
    resize: none;
    font-size: 15px;
}
.card.post-form textarea:focus {
    outline: none;
    box-shadow: none;
}

/* === Reddit-like Post Card === */
.post-card {
    display: flex;
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    margin-bottom: 20px;
    transition: 0.2s ease;
}
.post-card:hover {
    box-shadow: 0 4px 14px rgba(0,0,0,0.08);
}
.vote-column {
    width: 60px;
    text-align: center;
    padding: 1rem 0.5rem;
    border-right: 1px solid #eee;
    background: #fafafa;
    border-top-left-radius: 12px;
    border-bottom-left-radius: 12px;
}
.vote-column i.la {
    font-size: 22px;
    cursor: pointer;
    display: block;
    margin: 6px auto;
    color: #888;
}
.vote-column .voted-up i.la-arrow-up { color: #28a745; }
.vote-column .voted-down i.la-arrow-down { color: #dc3545; }
.vote-count { font-weight: bold; color: #333; }

.post-content {
    flex: 1;
    padding: 1rem 1.5rem;
}
.post-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
}
.post-header .user-info {
    display: flex;
    align-items: center;
}
.post-header .user-info img {
    width: 38px; height: 38px;
    border-radius: 50%; object-fit: cover;
}
.post-header .user-info strong {
    margin-left: 10px;
    font-size: 15px;
}
.post-body {
    margin-top: 0.7rem;
}
.post-body img, .post-body video {
    border-radius: 10px;
    margin-top: 10px;
    max-width: 100%;
}

/* === Post Footer === */
.post-footer {
    border-top: 1px solid #eee;
    padding-top: 8px;
    display: flex;
    gap: 20px;
    font-size: 14px;
}
.post-footer a {
    color: #555;
    text-decoration: none;
}
.post-footer a:hover { color: #FF0B55; }

/* === Comments === */
.comments-section {
    display: none;
    background: #f8f9fa;
    border-top: 1px solid #eee;
    padding: 1rem 1.5rem;
    border-radius: 0 0 12px 12px;
}
.comment { margin-bottom: 8px; }
.comment img {
    width: 28px; height: 28px;
    border-radius: 50%;
    margin-right: 6px;
}
.comment-input { width: 100%; font-size: 14px; }

/* === Misc === */
.timeline-label {
    text-align: center;
    font-weight: bold;
    color: #555;
    margin: 25px 0 10px;
    position: relative;
}
.timeline-label::before, .timeline-label::after {
    content: "";
    position: absolute;
    top: 50%;
    width: 40%;
    height: 1px;
    background: #ccc;
}
.timeline-label::before { left: 0; }
.timeline-label::after { right: 0; }
</style>
</head>
<body>

{{-- PROFILE HEADER --}}
<div class="profile-header">
    <a href="{{ route('dashboard') }}" class="btn btn-outline-light">
        <i class="la la-sign-out"></i> Exit
    </a>

    <form id="avatarForm" action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PATCH')
        <div class="profile-pic-wrapper">
            <img src="{{ Auth::user()->avatar_url }}" alt="Avatar" id="avatarPreview">
            <div class="camera-overlay" onclick="document.getElementById('avatarInput').click();">
                <i class="la la-camera"></i>
            </div>
        </div>
        <input type="file" id="avatarInput" name="avatar" accept="image/*" style="display:none;" onchange="this.form.submit()">
    </form>
    <h3>{{ strtoupper(Auth::user()->name) }}</h3>
</div>

<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-xl-8 col-lg-10 col-12">

            {{-- Post Form --}}
            <div class="card post-form mb-4">
                <div class="card-body">
                    <form action="{{ route('timeline.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <textarea name="content" class="form-control mb-2" rows="3" placeholder="Create a post..."></textarea>

                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <input type="file" id="mediaInput" name="image" accept="image/*,video/*,image/gif">
                            </div>
                            <button type="submit" class="btn btn-danger" style="background:#FF0B55; border:none;">Post</button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Timeline --}}
            @php $currentDate = null; @endphp
            @forelse ($posts as $post)
                @if ($currentDate !== $post->created_at->toDateString())
                    <div class="timeline-label">
                        {{ $post->created_at->isToday() ? 'Today' : ($post->created_at->isYesterday() ? 'Yesterday' : $post->created_at->format('F j, Y')) }}
                    </div>
                    @php $currentDate = $post->created_at->toDateString(); @endphp
                @endif

                @php $userVote = $post->userVote(auth()->id()); @endphp

                <div class="post-card" id="post-{{ $post->id }}">
                    {{-- VOTE COLUMN --}}
                    <div class="vote-column">
                        <a href="#" class="upvote-btn {{ $userVote==='up'?'voted-up':'' }}" data-id="{{ $post->id }}"><i class="la la-arrow-up"></i></a>
                        <div class="vote-count" id="upvote-count-{{ $post->id }}">{{ $post->upvotes()->count() - $post->downvotes()->count() }}</div>
                        <a href="#" class="downvote-btn {{ $userVote==='down'?'voted-down':'' }}" data-id="{{ $post->id }}"><i class="la la-arrow-down"></i></a>
                    </div>

                    {{-- CONTENT COLUMN --}}
                    <div class="post-content">
                        <div class="post-header">
                            <div class="user-info">
                                <img src="{{ $post->user->avatar_url }}">
                                <strong>{{ $post->user->name }}</strong>
                                <small class="text-muted ml-2">{{ $post->created_at->diffForHumans() }}</small>
                            </div>
                            @if(auth()->id() === $post->user_id)
                            <div class="dropdown">
                                <a href="#" class="text-muted" data-toggle="dropdown"><i class="la la-ellipsis-h"></i></a>
                                <div class="dropdown-menu dropdown-menu-right">
                                    <a class="dropdown-item" href="{{ route('posts.edit', $post->id) }}"><i class="la la-edit"></i> Edit</a>
                                    <form action="{{ route('posts.destroy', $post->id) }}" method="POST" class="delete-post-form">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="dropdown-item text-danger delete-post-btn" data-id="{{ $post->id }}"><i class="la la-trash"></i> Delete</button>
                                    </form>
                                </div>
                            </div>
                            @endif
                        </div>

                        <div class="post-body">
                            <p>{{ $post->content }}</p>
                            @if($post->image)
                                @if($post->media_type === 'image' || $post->media_type === 'gif')
                                    <img src="{{ asset('storage/' . $post->image) }}" alt="Post Media">
                                @elseif($post->media_type === 'video')
                                    <video controls>
                                        <source src="{{ asset('storage/' . $post->image) }}" type="video/mp4">
                                    </video>
                                @endif
                            @endif
                        </div>

                        <div class="post-footer mt-2">
                            <a href="#" class="toggle-comments" data-id="{{ $post->id }}"><i class="la la-comment"></i> {{ $post->total_comments_count }} Comments</a>
                        </div>

                        {{-- Comments --}}
                        <div class="comments-section" id="comments-section-{{ $post->id }}">
                            <div class="comments-list mb-2">
                                @foreach($post->comments as $comment)
                                    <div class="comment d-flex align-items-start">
                                        <img src="{{ $comment->user->avatar_url }}">
                                        <div>
                                            <strong>{{ $comment->user->name }}</strong> {{ $comment->content }}
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <div class="input-group mt-2">
                                <input type="text" class="form-control comment-input" placeholder="Add a comment...">
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

            <div class="d-flex justify-content-center mt-3">{{ $posts->links() }}</div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
$(function(){
    $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });

    $(document).on('click','.toggle-comments',function(e){
        e.preventDefault();
        const id=$(this).data('id');
        $(`#comments-section-${id}`).slideToggle('fast');
    });

    $(document).on('click','.upvote-btn,.downvote-btn',function(e){
        e.preventDefault();
        const id=$(this).data('id');
        const vote=$(this).hasClass('upvote-btn')?'up':'down';
        $.post(`/posts/${id}/vote`,{vote:vote},res=>{
            $(`#upvote-count-${id}`).text(res.upvotes_count - res.downvotes_count);
            $(`.upvote-btn[data-id="${id}"]`).toggleClass('voted-up',res.user_vote==='up');
            $(`.downvote-btn[data-id="${id}"]`).toggleClass('voted-down',res.user_vote==='down');
        }).fail(()=>alert('Failed to vote'));
    });

    $(document).on('click','.comment-send',function(){
        const btn=$(this);
        const id=btn.data('id');
        const input=btn.closest('.input-group').find('.comment-input');
        const content=input.val().trim();
        if(!content) return;
        $.post(`/posts/${id}/comments`,{content:content},res=>{
            const html=`
                <div class="comment d-flex align-items-start">
                    <img src="${res.avatar}">
                    <div><strong>${res.user}</strong> ${res.comment}</div>
                </div>`;
            $(`#comments-section-${id} .comments-list`).append(html);
            input.val('');
        }).fail(()=>alert('Failed to comment'));
    });
});
</script>
</body>
</html>