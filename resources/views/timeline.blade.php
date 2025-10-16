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
body { background:#fff; color:#000; font-family: Arial, sans-serif; margin:0; padding:0; }

/* Profile Header */
.profile-header { 
    background: #FF0B55; 
    text-align:center; 
    padding:80px 20px 40px; 
    border-radius:0 0 15px 15px; 
    position: relative; 
}
.profile-pic-wrapper { position: relative; display: inline-block; }
.profile-header img { width:120px; height:120px; border-radius:50%; border:4px solid #fff; object-fit: cover; transition: 0.3s ease; }
.camera-overlay { position:absolute; bottom:5px; right:5px; width:35px; height:35px; background:#fff; border:2px solid #fff; display:flex; align-items:center; justify-content:center; color:#FF0B55; opacity:0; transition:0.3s ease; cursor:pointer; border-radius:50%; }
.camera-overlay i { font-size:18px; }
.profile-pic-wrapper:hover .camera-overlay { opacity:1; }
.profile-pic-wrapper:hover img { filter:brightness(0.9); }
.profile-header h3 { margin:0; font-weight:bold; color:white; }

/* Exit Button */
.btn-outline-light {
    position: absolute;
    top: 20px;
    right: 20px;
    border: 2px solid #fff;
    color: #fff;
    background: none;
    border-radius: 6px;
    padding: 6px 14px;
    text-decoration: none;
    font-weight: bold;
    transition: 0.3s ease;
}
.btn-outline-light:hover {
    background: #fff;
    color: #FF0B55;
    border-color: #fff;
}

/* Timeline & Widgets */
.timeline { list-style:none; width:100%; }
.timeline-label { border-bottom:1px solid #CF0F47; padding:10px 0; margin-bottom:15px; color:white; }
.widget { background:#fff; border:1px solid #CF0F47; border-radius:6px; box-shadow:0 2px 10px rgba(0,0,0,.05); margin-bottom:20px; }
.widget-header { padding:.85rem 1.4rem; display:flex; align-items:center; }
.widget-body { padding:1.4rem; }
.widget-footer { background:#FFDEDE; padding:1rem; }
.meta ul { list-style:none; display:flex; padding:0; margin:0; }
.meta ul li { margin-right:.8rem; display:flex; align-items:center; }
.meta ul li a i.la { cursor:pointer; font-size:20px; }
.voted-up i.la-arrow-up { color:#28a745 !important; }
.voted-down i.la-arrow-down { color:#dc3545 !important; }
.comments-section { background:#f9f9f9; border-top:1px solid #eee; display:none; padding:0.5rem 1rem; }
.replies { margin-left:1.5rem; margin-top:0.5rem; }
.comment-input { width:100%; }

/* Media Preview */
#mediaPreview { display:none; text-align:center; }
#mediaPreview img, #mediaPreview video { max-height:300px; border-radius:10px; }
#removePreview { 
    position:absolute; top:-10px; right:-10px; 
    border-radius:50%; width:25px; height:25px; 
    line-height:1; font-weight:bold;
}
</style>
</head>
<body>

{{-- Profile Header --}}
<div class="profile-header">
    <!-- EXIT BUTTON -->
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
        <div class="col-xl-10 col-12">

            {{-- Post Form --}}
            <div class="card mb-4">
                <div class="card-body">
                    <form action="{{ route('timeline.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <textarea name="content" class="form-control mb-2" rows="3" placeholder="What's on your mind?"></textarea>

                        <div class="d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center">
                                <label class="mb-0 mr-3" style="font-size:14px;">Attach</label>
                                <input type="file" id="mediaInput" name="image" accept="image/*,video/*,image/gif" class="form-control-file">
                            </div>
                            <button type="submit" class="btn btn-primary" style="background-color:#FF0B55; border:none;">Post</button>
                        </div>

                        <!-- Media Preview -->
                        <div id="mediaPreview" class="mt-3 position-relative">
                            <button type="button" id="removePreview" class="btn btn-sm btn-danger">×</button>
                            <img id="previewImage" src="#" alt="Preview" class="img-fluid rounded" style="display:none;">
                            <video id="previewVideo" controls class="w-100 rounded" style="display:none;">
                                <source id="previewVideoSource" src="#">
                            </video>
                        </div>
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

                    @php $userVote = $post->userVote(auth()->id()); @endphp

                    <div class="widget" id="post-{{ $post->id }}">
                        <div class="widget-header">
                            <img src="{{ $post->user->avatar_url }}" class="rounded-circle" width="40" height="40" style="object-fit:cover;">
                            <div class="ml-2">
                                <strong>{{ $post->user->name }}</strong>
                                <div class="small text-muted">{{ $post->created_at->diffForHumans() }}</div>
                            </div>

                            @if(auth()->id() === $post->user_id)
                            <div class="ml-auto dropdown">
                                <a href="#" class="text-dark" data-toggle="dropdown"><i class="la la-ellipsis-h"></i></a>
                                <div class="dropdown-menu dropdown-menu-right">
                                    <a class="dropdown-item" href="{{ route('posts.edit', $post->id) }}"><i class="la la-edit"></i> Edit Post</a>
                                    <form action="{{ route('posts.destroy', $post->id) }}" method="POST" class="delete-post-form">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="dropdown-item text-danger delete-post-btn" data-id="{{ $post->id }}"><i class="la la-trash"></i> Delete Post</button>
                                    </form>
                                </div>
                            </div>
                            @endif
                        </div>

                        <div class="widget-body">
                            <p>{{ $post->content }}</p>
                            @if($post->image)
                                @if($post->media_type === 'image' || $post->media_type === 'gif')
                                    <img src="{{ asset('storage/' . $post->image) }}" alt="Post Media" class="img-fluid rounded mt-2">
                                @elseif($post->media_type === 'video')
                                    <video controls class="w-100 rounded mt-2">
                                        <source src="{{ asset('storage/' . $post->image) }}" type="video/mp4">
                                        Your browser does not support the video tag.
                                    </video>
                                @else
                                    <img src="{{ asset('storage/' . $post->image) }}" alt="Post Media" class="img-fluid rounded mt-2">
                                @endif
                            @endif
                        </div>

                        <div class="widget-footer">
                            <div class="meta">
                                <ul>
                                    <li>
                                        <a href="#" class="upvote-btn {{ $userVote==='up'?'voted-up':'' }}" data-id="{{ $post->id }}"><i class="la la-arrow-up"></i></a>
                                        <span id="upvote-count-{{ $post->id }}">{{ $post->upvotes()->count() }}</span>
                                    </li>
                                    <li>
                                        <a href="#" class="downvote-btn {{ $userVote==='down'?'voted-down':'' }}" data-id="{{ $post->id }}"><i class="la la-arrow-down"></i></a>
                                        <span id="downvote-count-{{ $post->id }}">{{ $post->downvotes()->count() }}</span>
                                    </li>
                                    <li>
                                        <a href="#" class="toggle-comments" data-id="{{ $post->id }}">
                                            <i class="la la-comment"></i>
                                            <span id="comment-count-{{ $post->id }}">{{ $post->total_comments_count }}</span>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>

                        {{-- Comments --}}
                        <div class="comments-section" id="comments-section-{{ $post->id }}">
                            <div class="comments-list mb-2">
                                @foreach($post->comments as $comment)
                                    <div class="comment mb-2 d-flex" id="comment-{{ $comment->id }}">
                                        <img src="{{ $comment->user->avatar_url }}" class="rounded-circle mr-2" width="30" height="30" style="object-fit:cover;">
                                        <div>
                                            <strong>{{ $comment->user->name }}</strong>: {{ $comment->content }}
                                            <div class="replies ml-4 mt-1">
                                                @foreach($comment->replies as $reply)
                                                    <div class="comment mb-1 d-flex" id="comment-{{ $reply->id }}">
                                                        <img src="{{ $reply->user->avatar_url }}" class="rounded-circle mr-2" width="25" height="25" style="object-fit:cover;">
                                                        <div><strong>{{ $reply->user->name }}</strong>: {{ $reply->content }}</div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <div class="input-group mb-2">
                                <input type="text" class="form-control comment-input" placeholder="Write a comment...">
                                <div class="input-group-append">
                                    <button class="btn btn-sm btn-danger comment-send" data-id="{{ $post->id }}">Send</button>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <p class="text-center text-muted">No posts yet.</p>
                @endforelse
            </div>
            <div class="d-flex justify-content-center mt-3">{{ $posts->links() }}</div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
$(function(){
    $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });

    // === MEDIA PREVIEW ===
    $('#mediaInput').on('change', function(event) {
        const file = event.target.files[0];
        if (!file) return;
        const fileType = file.type;
        const reader = new FileReader();
        reader.onload = function(e) {
            if (fileType.startsWith('image/')) {
                $('#previewImage').attr('src', e.target.result).show();
                $('#previewVideo').hide();
            } else if (fileType.startsWith('video/')) {
                $('#previewVideoSource').attr('src', e.target.result);
                $('#previewVideo')[0].load();
                $('#previewVideo').show();
                $('#previewImage').hide();
            }
            $('#mediaPreview').show();
        };
        reader.readAsDataURL(file);
    });
    $('#removePreview').on('click', function(){
        $('#mediaPreview').hide();
        $('#previewImage,#previewVideo').hide();
        $('#mediaInput').val('');
    });

    // === DELETE POST ===
    $(document).on('click','.delete-post-btn',function(e){
        e.preventDefault();
        if(!confirm('Are you sure?')) return;
        const postId=$(this).data('id');
        const card=$(this).closest('.widget');
        $.ajax({url:`/posts/${postId}`,type:'DELETE',success:()=>card.remove(),error:()=>alert('Failed to delete post')});
    });

    // === TOGGLE COMMENTS ===
    $(document).on('click','.toggle-comments',function(e){
        e.preventDefault();
        const id=$(this).data('id');
        $(`#comments-section-${id}`).slideToggle('fast');
    });

    // === UPVOTE / DOWNVOTE ===
    $(document).on('click','.upvote-btn,.downvote-btn',function(e){
        e.preventDefault();
        const id=$(this).data('id');
        const vote=$(this).hasClass('upvote-btn')?'up':'down';
        $.post(`/posts/${id}/vote`,{vote:vote},res=>{
            $(`#upvote-count-${id}`).text(res.upvotes_count);
            $(`#downvote-count-${id}`).text(res.downvotes_count);
            $(`.upvote-btn[data-id="${id}"]`).toggleClass('voted-up',res.user_vote==='up');
            $(`.downvote-btn[data-id="${id}"]`).toggleClass('voted-down',res.user_vote==='down');
        }).fail(()=>alert('Failed to vote'));
    });

    // === COMMENTS ===
    $(document).on('click','.comment-send',function(){
        const btn=$(this);
        const id=btn.data('id');
        const input=btn.closest('.input-group').find('.comment-input');
        const content=input.val().trim();
        if(!content) return;
        const data={content:content};
        const parentId=input.data('parent');
        if(parentId) data.parent_id=parentId;
        $.post(`/posts/${id}/comments`,data,res=>{
            const html=`
                <div class="comment mb-2 d-flex" id="comment-${res.id}">
                    <img src="${res.avatar}" class="rounded-circle mr-2" width="30" height="30" style="object-fit:cover;">
                    <div>
                        <strong>${res.user}</strong>: ${res.comment}
                        ${res.parent_id?'':`<a href="#" class="reply-btn ml-2 small text-primary" data-id="${res.id}">Reply</a>`}
                        <div class="replies ml-4 mt-1"></div>
                    </div>
                </div>`;
            if(res.parent_id){
                $(`#comment-${res.parent_id} .replies`).append(html);
            } else {
                $(`#comments-section-${id} .comments-list`).append(html);
            }
            input.val('').removeAttr('data-parent');
            $(`#comment-count-${id}`).text(res.comments_count);
        }).fail(()=>alert('Failed to add comment'));
    });

    // === REPLY ===
    $(document).on('click','.reply-btn',function(e){
        e.preventDefault();
        const parent=$(this).data('id');
        const input=$(this).closest('.comments-section').find('.comment-input');
        input.focus().attr('data-parent',parent);
    });
});
</script>
</body>
</html>
