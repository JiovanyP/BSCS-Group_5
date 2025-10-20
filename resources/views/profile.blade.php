<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Profile</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://maxcdn.icons8.com/fonts/line-awesome/1.1/css/line-awesome.min.css">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
<style>
/* === Variables === */
:root {
    --primary: #494ca2;
    --accent: #CF0F47;
    --accent-hover: #FF0B55;
    --sidebar-bg: #ffffff;
    --white: #ffffff;
    --black: #000000;
    --text-muted: #666;
}

/* === Base === */
body {
    background-color: #f5f7f8;
    color: #1a1a1a;
    font-family: 'Poppins', Arial, sans-serif;
    margin: 0;
    display: flex;
    min-height: 100vh;
}

/* === Sidebar === */
.sidebar {
    min-width: 270px;
    max-width: 270px;
    background: var(--sidebar-bg);
    color: var(--text-muted);
    position: sticky;
    top: 0;
    height: 100vh;
    padding: 20px;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    border-right: 1px solid #eee;
    box-shadow: 0 0 20px rgba(0, 0, 0, 0.02);
}

.sidebar h1 {
    margin-bottom: 20px;
    font-weight: 700;
    font-size: 1.5rem;
}

.sidebar h3 {
    margin-bottom: 20px;
    font-weight: 300;
    font-size: 0.85rem;
    line-height: 1.6;
}

.sidebar .logo {
    color: var(--accent);
    text-decoration: none;
}

.sidebar ul.components {
    list-style: none;
    padding: 0;
}

.sidebar ul li a {
    padding: 10px 0;
    display: flex;
    align-items: center;
    color: var(--text-muted);
    border-bottom: 1px solid rgba(0, 0, 0, 0.05);
    text-decoration: none;
    transition: 0.3s all ease;
}

.sidebar ul li a:hover { color: var(--black); }
.sidebar ul li.active > a { color: var(--accent); font-weight: 600; }

/* === Main Content === */
.main-content { flex: 1; overflow-y: auto; position: relative; }

/* === Profile Header === */
.profile-header {
    background: #FF0B55;
    text-align: center;
    padding: 80px 20px 40px;
    color: white;
    position: relative;
}

/* === Avatar Wrapper + Camera Icon Overlay === */
.avatar-wrapper {
    position: relative;
    display: inline-block;
}
.avatar-wrapper img {
    width: 120px;
    height: 120px;
    border-radius: 50%;
    border: 4px solid #fff;
    object-fit: cover;
}

/* Camera icon overlay (small circle at bottom-right) */
.camera-icon {
    position: absolute;
    bottom: 6px;
    right: 6px;
    background-color: rgba(0, 0, 0, 0.6);
    border-radius: 50%;
    width: 36px;
    height: 36px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    color: white;
    transition: background 0.2s, transform 0.12s;
}
.camera-icon:hover { background-color: rgba(0, 0, 0, 0.8); transform: translateY(-2px); }

/* === Exit Button (top-right) === */
.exit-btn {
    position: absolute;
    top: 18px;
    right: 22px;
    background: transparent;
    border: none;
    color: white;
    font-size: 1.6rem;
    cursor: pointer;
    z-index: 30;
    padding: 6px;
    border-radius: 6px;
    transition: transform 0.12s, background 0.12s;
}
.exit-btn:hover { transform: scale(1.05); background: rgba(255,255,255,0.12); }

/* === Post Form === */
.card.post-form {
    border: none;
    border-radius: 12px;
    box-shadow: 0 3px 12px rgba(0,0,0,0.05);
}
.card.post-form textarea { border: none; resize: none; }

/* === Post Card === */
.post-card {
    display: flex;
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    margin-bottom: 20px;
    transition: 0.2s ease;
}
.post-card:hover { box-shadow: 0 4px 14px rgba(0,0,0,0.08); }

.vote-column {
    width: 60px;
    text-align: center;
    padding: 1rem 0.5rem;
    border-right: 1px solid #eee;
    background: #fafafa;
    border-radius: 12px 0 0 12px;
}
.vote-column i.la {
    font-size: 22px;
    cursor: pointer;
    margin: 6px auto;
    color: #888;
}
.vote-column .voted-up i.la-arrow-up { color: #28a745; }
.vote-column .voted-down i.la-arrow-down { color: #dc3545; }
.vote-count { font-weight: bold; }

.post-content { flex: 1; padding: 1rem 1.5rem; }
.post-header { display: flex; justify-content: space-between; align-items: center; }
.post-body img, .post-body video { max-width: 100%; border-radius: 10px; margin-top: 10px; }

/* === Comments === */
.comments-section {
    display: none;
    background: #f8f9fa;
    border-top: 1px solid #eee;
    padding: 1rem 1.5rem;
    border-radius: 0 0 12px 12px;
}

.comment {
    padding: 8px 0;
    border-bottom: 1px solid #f0f0f0;
}

.comment:last-child {
    border-bottom: none;
}

.replies {
    margin-left: 2rem;
    margin-top: 0.5rem;
}

.reply-btn {
    font-size: 0.875rem;
    cursor: pointer;
}

/* === Alert === */
.alert {
    border-radius: 8px;
    animation: fadeIn 0.3s ease-in;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(-10px); }
    to { opacity: 1; transform: translateY(0); }
}

/* === Modal === */
.modal-header.delete-header { background-color: #dc3545; color: white; }
.modal-header.report-header { background-color: #d1ecf1; }

/* === Timeline Label === */
.timeline-label {
    font-size: 0.9rem;
    letter-spacing: 0.5px;
}
</style>
</head>
<body>

<div class="sidebar">
    <div>
        <h1><a href="#" class="logo">Publ.</a></h1>
        <h3>Be part of keeping our community safe. Publish your report with Publ.</h3>
        <ul class="components">
            <li><a href="{{ route('timeline') }}"><i class="la la-home me-2"></i>Home</a></li>
            <li><a href="{{ route('posts.create') }}"><i class="la la-plus-circle me-2"></i>Create Post</a></li>
            <li><a href="{{ route('accidents.create') }}"><i class="la la-exclamation-triangle me-2"></i>Notifications</a></li>
            <li class="active"><a href="{{ route('profile') }}"><i class="la la-user me-2"></i>Profile</a></li>
        </ul>
    </div>
    <form action="{{ route('logout') }}" method="POST">@csrf
        <button type="submit" class="btn btn-danger w-100"><i class="la la-sign-out me-2"></i>Logout</button>
    </form>
</div>

<div class="main-content">
    <div class="profile-header">
        <form id="avatarForm" action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
            @csrf @method('PATCH')
            <!-- Avatar wrapper with camera overlay -->
            <div class="avatar-wrapper">
                <img id="avatarPreview" src="{{ Auth::user()->avatar_url }}" alt="Avatar">
                <!-- label acts as button for hidden file input -->
                <label class="camera-icon" for="avatarInput" title="Change avatar"><i class="la la-camera"></i></label>
                <input type="file" id="avatarInput" name="avatar" accept="image/*" style="display:none;">
            </div>
        </form>
        <h3>{{ strtoupper(Auth::user()->name) }}</h3>
    </div>

    <div class="container mt-4">
        <div class="col-xl-8 mx-auto">

            {{-- Success Alert --}}
            @if (session('success'))
                <div class="alert alert-success text-center" id="successAlert">
                    {{ session('success') }}
                </div>
                <script>
                    setTimeout(() => document.getElementById('successAlert').style.display = 'none', 3000);
                </script>
            @endif

            {{-- Create Post --}}
            <div class="card post-form mb-4">
                <div class="card-body">
                    <form action="{{ route('timeline.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <textarea name="content" class="form-control mb-2" rows="3" placeholder="Create a post..."></textarea>
                        <div class="d-flex justify-content-between align-items-center">
                            <input type="file" name="image" accept="image/*,video/*,image/gif">
                            <button type="submit" class="btn btn-danger" style="background:#FF0B55;">Post</button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Timeline --}}
            @php $currentDate = null; @endphp
            @forelse ($posts as $post)
                @if ($currentDate !== $post->created_at->toDateString())
                    <div class="timeline-label text-center font-weight-bold text-muted my-3">
                        {{ $post->created_at->isToday() ? 'Today' : ($post->created_at->isYesterday() ? 'Yesterday' : $post->created_at->format('F j, Y')) }}
                    </div>
                    @php $currentDate = $post->created_at->toDateString(); @endphp
                @endif

                @php $userVote = $post->userVote(auth()->id()); @endphp
                <div class="post-card" id="post-{{ $post->id }}">
                    <div class="vote-column">
                        <a href="#" class="upvote-btn {{ $userVote==='up'?'voted-up':'' }}" data-id="{{ $post->id }}"><i class="la la-arrow-up"></i></a>
                        <div class="vote-count" id="upvote-count-{{ $post->id }}">{{ $post->upvotes()->count() - $post->downvotes()->count() }}</div>
                        <a href="#" class="downvote-btn {{ $userVote==='down'?'voted-down':'' }}" data-id="{{ $post->id }}"><i class="la la-arrow-down"></i></a>
                    </div>

                    <div class="post-content">
                        <div class="post-header">
                            <div class="user-info d-flex align-items-center">
                                <img src="{{ $post->user->avatar_url }}" width="38" height="38" class="rounded-circle mr-2">
                                <strong>{{ $post->user->name }}</strong>
                                <small class="text-muted ml-2">{{ $post->created_at->diffForHumans() }}</small>
                            </div>
                            <div class="dropdown">
                                <a href="#" class="text-muted" data-toggle="dropdown"><i class="la la-ellipsis-h"></i></a>
                                <div class="dropdown-menu dropdown-menu-right">
                                    @if(auth()->id() === $post->user_id)
                                        <a class="dropdown-item" href="{{ route('posts.edit', $post->id) }}"><i class="la la-edit"></i> Edit</a>
                                        <button class="dropdown-item text-danger delete-post-btn" data-id="{{ $post->id }}" data-toggle="modal" data-target="#deleteModal"><i class="la la-trash"></i> Delete</button>
                                    @else
                                        <button class="dropdown-item text-warning report-post-btn" data-id="{{ $post->id }}" data-toggle="modal" data-target="#reportModal"><i class="la la-flag"></i> Report</button>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="post-body">
                            <p>{{ $post->content }}</p>
                            @if($post->image)
                                @if($post->media_type === 'image' || $post->media_type === 'gif')
                                    <img src="{{ asset('storage/' . $post->image) }}" alt="Post Image">
                                @elseif($post->media_type === 'video')
                                    <video controls><source src="{{ asset('storage/' . $post->image) }}" type="video/mp4"></video>
                                @endif
                            @endif
                        </div>

                        <div class="post-footer mt-2">
                            <a href="#" class="toggle-comments" data-id="{{ $post->id }}"><i class="la la-comment"></i> <span id="comment-count-{{ $post->id }}">{{ $post->total_comments_count }}</span> Comments</a>
                        </div>

                        {{-- Comments --}}
                        <div class="comments-section" id="comments-section-{{ $post->id }}">
                            <div class="comments-list mb-2">
                                @foreach($post->comments as $comment)
                                    <div class="comment d-flex align-items-start mb-2" id="comment-{{ $comment->id }}">
                                        <img src="{{ $comment->user->avatar_url }}" width="28" height="28" class="rounded-circle mr-2">
                                        <div>
                                            <div><strong>{{ $comment->user->name }}</strong> {{ $comment->content }}</div>
                                            <a href="#" class="reply-btn small text-primary" data-id="{{ $comment->id }}">Reply</a>
                                            <div class="replies">
                                                @foreach($comment->replies as $reply)
                                                    <div class="comment d-flex align-items-start mb-1" id="comment-{{ $reply->id }}">
                                                        <img src="{{ $reply->user->avatar_url }}" width="25" height="25" class="rounded-circle mr-2">
                                                        <div><strong>{{ $reply->user->name }}</strong> {{ $reply->content }}</div>
                                                    </div>
                                                @endforeach
                                            </div>
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

{{-- Delete Modal --}}
<div class="modal fade" id="deleteModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header delete-header">
        <h5 class="modal-title"><i class="la la-exclamation-circle"></i> Delete Post</h5>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">Are you sure you want to delete this post?</div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Delete</button>
      </div>
    </div>
  </div>
</div>

{{-- Report Modal --}}
<div class="modal fade" id="reportModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header report-header">
        <h5 class="modal-title"><i class="la la-flag"></i> Report Post</h5>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">
        <p>Select a reason for reporting:</p>
        <form id="reportForm">
          <label><input type="radio" name="reason" value="spam"> It's spam</label><br>
          <label><input type="radio" name="reason" value="violence"> Violence or threats</label><br>
          <label><input type="radio" name="reason" value="hate_speech"> Hate speech</label><br>
          <label><input type="radio" name="reason" value="misinformation"> Misinformation</label><br>
          <label><input type="radio" name="reason" value="other"> Other</label>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-info" id="confirmReportBtn">Report</button>
      </div>
    </div>
  </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
$(function(){
  $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });
  let currentPostId = null;
  let currentCommentId = null;

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
    });
  });

  $(document).on('click','.comment-send',function(){
    const btn=$(this);
    const id=btn.data('id');
    const input=btn.closest('.input-group').find('.comment-input');
    const content=input.val().trim();
    if(!content) return;
    $.post(`/posts/${id}/comments`,{content:content},res=>{
      const html=`<div class="comment d-flex align-items-start mb-2" id="comment-${res.id}">
          <img src="${res.avatar}" width="28" height="28" class="rounded-circle mr-2">
          <div>
              <div><strong>${res.user}</strong> ${res.comment}</div>
              <a href="#" class="reply-btn small text-primary" data-id="${res.id}">Reply</a>
              <div class="replies"></div>
          </div>
      </div>`;
      $(`#comments-section-${id} .comments-list`).append(html);
      const count = parseInt($(`#comment-count-${id}`).text()) + 1;
      $(`#comment-count-${id}`).text(count);
      input.val('');
    });
  });

  $(document).on('click','.reply-btn',function(e){
    e.preventDefault();
    const commentId=$(this).data('id');
    const repliesDiv=$(`#comment-${commentId} .replies`);
    
    if(repliesDiv.find('.reply-input-group').length === 0){
      const replyInput=`<div class="input-group input-group-sm mt-2 reply-input-group">
          <input type="text" class="form-control reply-input" placeholder="Write a reply...">
          <div class="input-group-append">
              <button class="btn btn-sm btn-danger reply-send" data-comment-id="${commentId}">Send</button>
          </div>
      </div>`;
      repliesDiv.append(replyInput);
    }
  });

  $(document).on('click','.reply-send',function(){
    const btn=$(this);
    const commentId=btn.data('comment-id');
    const input=btn.closest('.input-group').find('.reply-input');
    const content=input.val().trim();
    if(!content) return;
    
    $.post(`/comments/${commentId}/reply`,{content:content},res=>{
      const html=`<div class="comment d-flex align-items-start mb-1" id="comment-${res.id}">
          <img src="${res.avatar}" width="25" height="25" class="rounded-circle mr-2">
          <div><strong>${res.user}</strong> ${res.comment}</div>
      </div>`;
      $(`#comment-${commentId} .replies`).prepend(html);
      input.closest('.reply-input-group').remove();
    });
  });

  $(document).on('click','.delete-post-btn',function(){ currentPostId=$(this).data('id'); });
  $('#confirmDeleteBtn').click(function(){
    $.ajax({ url:`/posts/${currentPostId}`, type:'POST', data:{_method:'DELETE'},
      success:()=>{ $(`#post-${currentPostId}`).fadeOut(300,()=>$(this).remove()); $('#deleteModal').modal('hide'); }
    });
  });

  $(document).on('click','.report-post-btn',function(){ currentPostId=$(this).data('id'); });
  $('#confirmReportBtn').click(function(){
    const reason=$('input[name="reason"]:checked').val();
    if(!reason){ alert('Please select a reason'); return; }
    $.post(`/posts/${currentPostId}/report`,{reason:reason},()=>{
      $('#reportModal').modal('hide');
      alert('Thank you for your report.');
    });
  });
});
</script>

<!-- Avatar preview + auto-submit script (keeps behavior minimal) -->
<script>
$(document).ready(function() {
    $('#avatarInput').on('change', function() {
        if (this.files && this.files[0]) {
            const reader = new FileReader();
            reader.onload = e => $('#avatarPreview').attr('src', e.target.result);
            reader.readAsDataURL(this.files[0]);
            // auto-submit form to update avatar
            $('#avatarForm').submit();
        }
    });
});
</script>

</body>
</html>
