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
.profile-header { background:#FF0B55; text-align:center; padding:80px 20px 40px; border-radius:0 0 15px 15px; position:relative; }
.profile-pic-wrapper { position: relative; display: inline-block; }
.profile-header img { width:120px; height:120px; border-radius:50%; border:4px solid #fff; object-fit: cover; transition:0.3s ease; }
.camera-overlay { position:absolute; bottom:5px; right:5px; width:35px; height:35px; background:#fff; border:2px solid #fff; display:flex; align-items:center; justify-content:center; color:#FF0B55; opacity:0; transition:0.3s ease; cursor:pointer; border-radius:50%; }
.camera-overlay i { font-size:18px; }
.profile-pic-wrapper:hover .camera-overlay { opacity:1; }
.profile-pic-wrapper:hover img { filter:brightness(0.9); }
.profile-header h3 { margin:0; font-weight:bold; color:white; }

/* Exit Button */
.btn-outline-light { position:absolute; top:20px; right:20px; border:2px solid #fff; color:#fff; background:none; border-radius:6px; padding:6px 14px; text-decoration:none; font-weight:bold; transition:0.3s ease; }
.btn-outline-light:hover { background:#fff; color:#FF0B55; border-color:#fff; }

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
#removePreview { position:absolute; top:-10px; right:-10px; border-radius:50%; width:25px; height:25px; line-height:1; font-weight:bold; }
</style>
</head>
<body>

{{-- Profile Header --}}
<div class="profile-header">
    <a href="{{ route('dashboard') }}" class="btn btn-outline-light"><i class="la la-sign-out"></i> Exit</a>

    <form id="avatarForm" action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
        @csrf @method('PATCH')
        <div class="profile-pic-wrapper">
            <img src="{{ Auth::user()->avatar_url }}" alt="Avatar" id="avatarPreview">
            <div class="camera-overlay" onclick="document.getElementById('avatarInput').click();"><i class="la la-camera"></i></div>
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
                                    <a class="dropdown-item edit-post-btn" href="#" data-id="{{ $post->id }}"><i class="la la-edit"></i> Edit Post</a>
                                    <form action="{{ route('posts.destroy', $post->id) }}" method="POST" class="delete-post-form">
                                        @csrf @method('DELETE')
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
                                    <img src="{{ asset('storage/' . $post->image) }}" class="img-fluid rounded mt-2">
                                @elseif($post->media_type === 'video')
                                    <video controls class="w-100 rounded mt-2"><source src="{{ asset('storage/' . $post->image) }}" type="video/mp4"></video>
                                @endif
                            @endif
                        </div>

                        <div class="widget-footer">
                            <div class="meta">
                                <ul>
                                    <li><a href="#" class="upvote-btn {{ $userVote==='up'?'voted-up':'' }}" data-id="{{ $post->id }}"><i class="la la-arrow-up"></i></a><span id="upvote-count-{{ $post->id }}">{{ $post->upvotes()->count() }}</span></li>
                                    <li><a href="#" class="downvote-btn {{ $userVote==='down'?'voted-down':'' }}" data-id="{{ $post->id }}"><i class="la la-arrow-down"></i></a><span id="downvote-count-{{ $post->id }}">{{ $post->downvotes()->count() }}</span></li>
                                    <li><a href="#" class="toggle-comments" data-id="{{ $post->id }}"><i class="la la-comment"></i><span id="comment-count-{{ $post->id }}">{{ $post->total_comments_count }}</span></a></li>
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
                                <div class="input-group-append"><button class="btn btn-sm btn-danger comment-send" data-id="{{ $post->id }}">Send</button></div>
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

<!-- JS Section -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
$(function(){
    $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });

    // === EDIT POST (Open modal & update via AJAX) ===
    $(document).on('click', '.edit-post-btn', function(e){
        e.preventDefault();
        const postId = $(this).data('id');
        const currentContent = $(`#post-${postId} .widget-body p`).text().trim();
        $('#editPostContent').val(currentContent);
        $('#editPostForm').attr('data-id', postId);
        $('#editPostModal').modal('show');
    });

    $('#editPostForm').on('submit', function(e){
        e.preventDefault();
        const postId = $(this).attr('data-id');
        const formData = $(this).serialize();
        $.ajax({
            url: `/posts/${postId}`,
            type: 'PATCH',
            data: formData,
            success: function(res) {
                if (res.success) {
                    $(`#post-${postId} .widget-body p`).text(res.content);
                    $('#editPostModal').modal('hide');
                }
            },
            error: function() {
                alert('Error updating post.');
            }
        });
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

    // === VOTES ===
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
});
</script>

<!-- Edit Post Modal -->
<div class="modal fade" id="editPostModal" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <form id="editPostForm" method="POST">
        @csrf @method('PATCH')
        <div class="modal-header">
          <h5 class="modal-title">Edit Post</h5>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
        <div class="modal-body">
          <textarea name="content" id="editPostContent" class="form-control" rows="4"></textarea>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary" style="background-color:#FF0B55; border:none;">Save Changes</button>
        </div>
      </form>
    </div>
  </div>
</div>
</body>
</html>
