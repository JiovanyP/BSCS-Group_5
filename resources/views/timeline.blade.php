@extends('layouts.app')

@section('title', 'Timeline')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
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

.main-content { flex: 1; overflow-y: auto; position: relative; }

/* === Profile Header === */
.profile-header {
    background: #FF0B55;
    text-align: center;
    padding: 60px 20px 50px;
    color: white;
    position: relative;
}

.profile-header h3 {
    margin-top: 20px;
    font-size: 2rem;
    font-weight: 700;
    letter-spacing: 3px;
    text-transform: uppercase;
}

/* === Avatar Wrapper + Camera Icon Overlay === */
.avatar-wrapper {
    position: relative;
    display: inline-block;
}
.avatar-wrapper img {
    width: 160px;
    height: 160px;
    border-radius: 50%;
    border: 6px solid #fff;
    object-fit: cover;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
}

/* Camera icon overlay (small circle at bottom-right) */
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
.camera-icon:hover { background-color: #222; transform: scale(1.05); }
.camera-icon i { font-size: 20px; }

/* === Exit Button (top-right) === */
.exit-btn {
    position: absolute;
    right: 35px;
    background: rgba(0, 0, 0, 0.3);
    border: 1px solid rgba(255, 255, 255, 0.9);
    color: white;
    font-size: 1rem;
    cursor: pointer;
    z-index: 30;
    padding: 10px 16px;
    border-radius: 10px;
    transition: all 0.2s;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
}
.exit-btn:hover { 
    background: rgba(0, 0, 0, 0.5);
    border-color: white;
    transform: translateX(3px);
}
.exit-btn:focus { outline: none; }
.exit-btn i { 
    font-size: 1rem;
    font-weight: bold;
}

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

<div class="main-content">
    <!-- Exit button to go back to dashboard -->
    <button class="exit-btn" onclick="window.location.href='{{ route('dashboard') }}'" title="Back to dashboard">
        <i class="la la-arrow-right"></i>
    </button>

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

<!-- Load jQuery and Bootstrap JS -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>

<!-- Weather API Script -->
<script>
// Kabacan coords: 7.1067° N, 124.8294° E
async function fetchWeather() {
    try {
        const res = await fetch("https://api.open-meteo.com/v1/forecast?latitude=7.1067&longitude=124.8294&current_weather=true");
        const data = await res.json();
        const weather = data.current_weather;
        const container = document.getElementById("weather-info");

        if (weather) {
            const temp = weather.temperature;
            const wind = weather.windspeed;
            const code = weather.weathercode;

            // Emoji icons for cuteness
            let icon = "☁️";
            if (code === 0) icon = "☀️"; // clear
            else if ([1,2].includes(code)) icon = "🌤";
            else if ([3,45,48].includes(code)) icon = "☁️";
            else if ([51,61,80].includes(code)) icon = "🌧";
            else if ([71,85].includes(code)) icon = "❄️";
            else if ([95,96,99].includes(code)) icon = "⛈";

            container.innerHTML = `
                <p style="font-size: 32px;">${icon}</p>
                <p>🌡️ ${temp}°C</p>
                <p>💨 ${wind} km/h</p>
            `;
        } else {
            container.innerHTML = "<p>Weather data unavailable.</p>";
        }
    } catch (err) {
        document.getElementById("weather-info").innerHTML = "<p>Failed to load weather.</p>";
    }
}

// Only run weather fetch if weather-info element exists (i.e., in sidebar)
if (document.getElementById("weather-info")) {
    fetchWeather();
    // Optional auto-refresh every 10 minutes
    setInterval(fetchWeather, 600000);
}
</script>

<!-- Timeline Scripts -->
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

<!-- Avatar preview + auto-submit script -->
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
@endsection