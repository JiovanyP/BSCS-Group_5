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
    body { 
      background:#fff; 
      color:#000; 
      font-family: Arial, sans-serif; 
      margin:0;
      padding:0;
    }

    /* Header */
    .profile-header {
      background: #FFDEDE;
      color:#000;
      text-align:center;
      padding:80px 20px 40px;
      border-radius:0 0 15px 15px;
      position: relative;
    }

    .profile-pic-wrapper {
      position: relative;
      display: inline-block;
    }

    .profile-header img {
      width:120px; 
      height:120px; 
      border-radius:50%;
      border:4px solid #FF0B55; 
      margin-bottom:15px;
      object-fit: cover;
      transition: 0.3s ease;
    }

    .profile-header h3 { 
      margin:0; 
      font-weight:bold; 
    }

    /* FB-style Camera icon overlay (hidden until hover) */
    .camera-overlay {
      position: absolute;
      bottom: 5px;
      right: 5px;
      width: 35px;
      height: 35px;
      background-color: #FF0B55;
      border: 2px solid #fff;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      color: #fff;
      opacity: 0;
      transition: opacity 0.3s ease;
      cursor: pointer;
    }

    .camera-overlay i {
      font-size: 18px;
    }

    .profile-pic-wrapper:hover .camera-overlay {
      opacity: 1;
    }

    .profile-pic-wrapper:hover img {
      filter: brightness(0.9);
    }

    /* Buttons + Timeline styling */
    .profile-nav .btn { 
      margin:0 5px; 
      background-color:#CF0F47; 
      border:none; 
      color:#fff; 
      font-weight:600; 
    }

    .profile-nav .btn:hover { 
      background-color:#FF0B55; 
    }

    .timeline { list-style:none; width:100%; }

    .timeline-label .label { 
      background:#FF0B55; 
      border-radius:35px; 
      color:#fff; 
      padding:.65rem 1.4rem; 
      font-weight:600; 
    }

    .widget { 
      background:#fff; 
      border:1px solid #CF0F47; 
      border-radius:6px; 
      box-shadow:0 2px 10px rgba(0,0,0,.05); 
      margin-bottom:20px; 
    }

    .widget-header { 
      padding:.85rem 1.4rem; 
      display:flex; 
      align-items:center; 
    }

    .widget-body { padding:1.4rem; }

    .widget-footer { background:#FFDEDE; padding:1rem; }

    .meta ul { list-style:none; display:flex; padding:0; margin:0; }
    .meta ul li { margin-right:.8rem; display:flex; align-items:center; }
    .meta ul li a i.la { cursor:pointer; font-size:20px; }

    .voted-up i.la-arrow-up { color:#28a745 !important; }
    .voted-down i.la-arrow-down { color:#dc3545 !important; }

    .comments-section { background:#f9f9f9; border-top:1px solid #eee; display:none; }
  </style>
</head>
<body>

<div class="profile-header">
  <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PATCH')
    <div class="profile-pic-wrapper">
      {{-- Profile Picture --}}
      <img src="{{ Auth::user()->avatar ?? 'https://bootdey.com/img/Content/avatar/avatar1.png' }}" 
           alt="Avatar" id="avatarPreview">

      {{-- FB-style Camera Overlay --}}
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
          <form action="{{ route('timeline.store') }}" method="POST">
            @csrf
            <textarea name="content" class="form-control mb-2" rows="3" placeholder="What's on your mind?" required></textarea>
            <button type="submit" class="btn btn-primary" style="background-color:#FF0B55; border:none;">Post</button>
          </form>
        </div>
      </div>

      {{-- Error Messages --}}
      @if ($errors->any())
        <div class="alert alert-danger">
          <ul class="mb-0">
            @foreach ($errors->all() as $error)
              <li>{{ $error }}</li>
            @endforeach
          </ul>
        </div>
      @endif

      {{-- Posts --}}
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

          <div class="widget">
            <div class="widget-header">
              <img src="{{ $post->user->avatar ?? 'https://bootdey.com/img/Content/avatar/avatar1.png' }}" class="rounded-circle" width="40" height="40">
              <div class="ml-2">
                <strong>{{ $post->user->name }}</strong>
                <div class="small text-muted">{{ $post->created_at->diffForHumans() }}</div>
              </div>

              @if (auth()->id() === $post->user_id)
              <div class="ml-auto dropdown">
                <a href="#" class="text-dark" data-toggle="dropdown"><i class="la la-ellipsis-h"></i></a>
                <div class="dropdown-menu dropdown-menu-right">
                  <a class="dropdown-item" href="{{ route('posts.edit', $post->id) }}"><i class="la la-edit"></i> Edit Post</a>
                  <form action="{{ route('posts.destroy', $post->id) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="dropdown-item text-danger"><i class="la la-trash"></i> Delete Post</button>
                  </form>
                </div>
              </div>
              @endif
            </div>

            <div class="widget-body">
              <p>{{ $post->content }}</p>
            </div>

            <div class="widget-footer">
              <div class="meta">
                <ul>
                  <li>
                    <a class="upvote-btn {{ $userVote === 'up' ? 'voted-up' : '' }}" data-id="{{ $post->id }}">
                      <i class="la la-arrow-up"></i>
                    </a>
                    <span id="upvote-count-{{ $post->id }}">{{ $post->upvotes()->count() }}</span>
                  </li>
                  <li>
                    <a class="downvote-btn {{ $userVote === 'down' ? 'voted-down' : '' }}" data-id="{{ $post->id }}">
                      <i class="la la-arrow-down"></i>
                    </a>
                    <span id="downvote-count-{{ $post->id }}">{{ $post->downvotes()->count() }}</span>
                  </li>
                  <li>
                    <a class="toggle-comments" data-id="{{ $post->id }}">
                      <i class="la la-comment"></i>
                      <span id="comment-count-{{ $post->id }}">{{ $post->total_comments_count }}</span>
                    </a>
                  </li>
                </ul>
              </div>
            </div>

            <div class="comments-section p-3" id="comments-section-{{ $post->id }}">
              <div class="comments-list mb-2">
                @foreach ($post->comments as $comment)
                  <div class="comment mb-2 d-flex">
                    <img src="{{ $comment->user->avatar ?? 'https://bootdey.com/img/Content/avatar/avatar1.png' }}" class="rounded-circle mr-2" width="30" height="30">
                    <div><strong>{{ $comment->user->name }}</strong>: {{ $comment->content }}</div>
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
        @empty
          <p class="text-center text-muted">No posts yet.</p>
        @endforelse
      </div>
    </div>
  </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
$(function(){
  $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });

  $('.toggle-comments').on('click', function(e){
    e.preventDefault();
    $('#comments-section-' + $(this).data('id')).slideToggle('fast');
  });

  $('.upvote-btn').on('click', function(e){
    e.preventDefault();
    const id = $(this).data('id');
    $.post(`/posts/${id}/upvote`, {}, function(res){
      $(`#upvote-count-${id}`).text(res.upvotes_count);
      $(`#downvote-count-${id}`).text(res.downvotes_count);
      $(`.upvote-btn[data-id="${id}"]`).toggleClass('voted-up', res.user_vote === 'up');
      $(`.downvote-btn[data-id="${id}"]`).toggleClass('voted-down', res.user_vote === 'down');
    });
  });

  $('.downvote-btn').on('click', function(e){
    e.preventDefault();
    const id = $(this).data('id');
    $.post(`/posts/${id}/downvote`, {}, function(res){
      $(`#upvote-count-${id}`).text(res.upvotes_count);
      $(`#downvote-count-${id}`).text(res.downvotes_count);
      $(`.upvote-btn[data-id="${id}"]`).toggleClass('voted-up', res.user_vote === 'up');
      $(`.downvote-btn[data-id="${id}"]`).toggleClass('voted-down', res.user_vote === 'down');
    });
  });

  $('.comment-send').on('click', function(){
    const id = $(this).data('id');
    const input = $(this).closest('.input-group').find('.comment-input');
    const content = input.val().trim();
    if (!content) return;

    $.post(`/posts/${id}/comment`, { content }, function(res){
      const commentHTML = `
        <div class="comment mb-2 d-flex">
          <img src="${res.avatar}" class="rounded-circle mr-2" width="30" height="30">
          <div><strong>${res.user}</strong>: ${res.comment}</div>
        </div>`;
      $(`#comments-section-${id} .comments-list`).append(commentHTML);
      input.val('');
      $(`#comment-count-${id}`).text(res.comments_count);
    });
  });
});
</script>

</body>
</html>
