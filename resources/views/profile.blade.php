@extends('layouts.app')

@section('title', 'Profile')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<style>
/* === Variables === */
:root {
    --primary: #494ca2;
    --accent: #CF0F47;
    --accent-hover: #FF0B55;
    --card-bg: #ffffff;
    --text-muted: #666;
}

.main-content { 
    flex: 1; 
    overflow-y: auto; 
    position: relative;
    background: #f8f9fa;
    padding: 20px 0;
}

/* === Post Card === */
.post-card {
    display: flex;
    background: var(--card-bg);
    border-radius: 16px;
    box-shadow: 0 12px 40px rgba(0,0,0,0.1);
    margin-bottom: 20px;
    transition: all 0.25s ease;
}

.post-card:hover { 
    box-shadow: 0 16px 50px rgba(0,0,0,0.15);
    transform: translateY(-2px);
}

.vote-column {
    width: 60px;
    text-align: center;
    padding: 1rem 0.5rem;
    border-right: 1px solid #eee;
    background: #fbfbfb;
    border-radius: 16px 0 0 16px;
}

.vote-column i.la {
    font-size: 22px;
    cursor: pointer;
    margin: 6px auto;
    color: #888;
    transition: color 0.2s;
}

.vote-column .voted-up i.la-arrow-up { color: #28a745; }
.vote-column .voted-down i.la-arrow-down { color: var(--accent); }
.vote-count { font-weight: bold; color: #333; }

.post-content { flex: 1; padding: 1.5rem 2rem; }
.post-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; }
.post-body { 
    color: #333;
    line-height: 1.6;
    font-size: 15px;
}
.post-body img, .post-body video { 
    max-width: 100%; 
    border-radius: 10px; 
    margin-top: 12px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

.post-footer {
    margin-top: 1rem;
    padding-top: 0.75rem;
    border-top: 1px solid #f0f0f0;
}

.post-footer a {
    color: var(--text-muted);
    text-decoration: none;
    font-size: 14px;
    transition: color 0.2s;
}

.post-footer a:hover {
    color: var(--accent);
}

/* === Comments === */
.comments-section {
    display: none;
    background: #f8f9fa;
    border-top: 1px solid #eee;
    padding: 1.5rem 2rem;
    border-radius: 0 0 16px 16px;
    margin: 0 -2rem -1.5rem -2rem;
}

.comment {
    padding: 10px 0;
    border-bottom: 1px solid #f0f0f0;
}

.comment:last-child {
    border-bottom: none;
}

.comment strong {
    color: #333;
    font-weight: 600;
}

.replies {
    margin-left: 2rem;
    margin-top: 0.75rem;
}

.reply-btn {
    font-size: 0.875rem;
    cursor: pointer;
    color: var(--accent) !important;
    font-weight: 500;
}

.reply-btn:hover {
    color: var(--accent-hover) !important;
}

.comment-input {
    border-radius: 10px;
    border: 1px solid #ddd;
    padding: 10px 14px;
    font-size: 14px;
    background: #fff;
}

.comment-input:focus {
    border-color: var(--accent);
    box-shadow: 0 0 0 3px rgba(207, 15, 71, 0.1);
    outline: none;
}

.comment-send {
    background: var(--accent) !important;
    border: none;
    border-radius: 0 10px 10px 0;
    padding: 10px 20px;
    font-weight: 600;
    transition: background 0.25s;
}

.comment-send:hover {
    background: var(--accent-hover) !important;
}

.reply-send {
    background: var(--accent) !important;
    border: none;
    font-weight: 600;
}

.reply-send:hover {
    background: var(--accent-hover) !important;
}

/* === Alert === */
.alert {
    border-radius: 10px;
    animation: fadeIn 0.3s ease-in;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    border: none;
    font-weight: 500;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(-10px); }
    to { opacity: 1; transform: translateY(0); }
}

/* === Modal === */
.modal-content {
    border-radius: 16px;
    border: none;
    box-shadow: 0 20px 60px rgba(0,0,0,0.3);
}

.modal-header.delete-header { 
    background-color: var(--accent); 
    color: white;
    border: none;
    border-radius: 16px 16px 0 0;
    padding: 1.25rem 1.5rem;
}

.modal-header.delete-header .close {
    background: transparent;
    border: none;
    color: white;
    opacity: 0.8;
    font-size: 1.5rem;
    padding: 0;
    margin: 0;
}

.modal-header.delete-header .close:hover {
    opacity: 1;
}

.modal-header.report-header { 
    background-color: #d1ecf1;
    border: none;
    border-radius: 16px 16px 0 0;
    padding: 1.25rem 1.5rem;
}

.modal-body {
    padding: 1.5rem;
    font-size: 15px;
}

.modal-footer {
    border: none;
    padding: 1rem 1.5rem;
}

.modal-footer .btn {
    border-radius: 10px;
    padding: 10px 24px;
    font-weight: 600;
    transition: all 0.25s;
}

.modal-footer .btn-secondary {
    background: #eee;
    color: #444;
    border: none;
}

.modal-footer .btn-secondary:hover {
    background: #ddd;
}

.modal-footer .btn-danger {
    background: var(--accent);
    border: none;
}

.modal-footer .btn-danger:hover {
    background: var(--accent-hover);
}

.modal-footer .btn-info {
    background: var(--primary);
    border: none;
}

.modal-footer .btn-info:hover {
    background: #3a3d82;
}

/* === Timeline Label === */
.timeline-label {
    font-size: 0.9rem;
    letter-spacing: 0.5px;
    color: var(--text-muted);
    font-weight: 600;
}

/* === Dropdown Buttons === */
.dropdown-menu {
    border: 1px solid #e5e7eb;
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
    padding: 0.5rem 0;
    min-width: 160px;
    border-radius: 12px;
}

.cute-edit-btn,
.cute-delete-btn,
.report-post-btn {
    color: #6b7280 !important;
    font-weight: 500;
    transition: all 0.2s ease;
    padding: 10px 16px;
    border-radius: 0;
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 0.9rem;
    border: none;
    width: 100%;
    text-align: left;
}

.cute-edit-btn:hover {
    background: #f3f4f6 !important;
    color: var(--primary) !important;
}

.cute-delete-btn {
    color: var(--accent) !important;
}

.cute-delete-btn:hover {
    background: #fef2f2 !important;
    color: var(--accent) !important;
}

.report-post-btn {
    color: #f59e0b !important;
}

.report-post-btn:hover {
    background: #fffbeb !important;
    color: #f59e0b !important;
}

/* === User Info === */
.user-info img {
    border: 2px solid #f0f0f0;
}

.user-info strong {
    color: #333;
    font-size: 15px;
}

.user-info small {
    font-size: 13px;
}

/* === Empty State === */
.text-center.text-muted {
    padding: 60px 20px;
    font-size: 16px;
    color: var(--text-muted);
}

/* === Report Form === */
#reportForm label {
    display: block;
    padding: 10px 12px;
    margin: 6px 0;
    border-radius: 8px;
    cursor: pointer;
    transition: background 0.2s;
}

#reportForm label:hover {
    background: #f8f9fa;
}

#reportForm input[type="radio"] {
    margin-right: 10px;
}
</style>

<div class="main-content">
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

            {{-- Timeline --}}
            @php $currentDate = null; @endphp
            @forelse ($posts as $post)
                @if ($currentDate !== $post->created_at->toDateString())
                    <div class="timeline-label text-center font-weight-bold my-3">
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
                                <small class="text-muted ml-3">{{ $post->created_at->diffForHumans() }}</small>
                            </div>
                            <div class="dropdown">
                                <a href="#" class="text-muted" data-toggle="dropdown"><i class="la la-ellipsis-h"></i></a>
                                <div class="dropdown-menu dropdown-menu-right">
                                    @if(auth()->id() === $post->user_id)
                                        <a class="dropdown-item cute-edit-btn" href="{{ route('posts.edit', $post->id) }}">
                                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                                            </svg>
                                            Edit
                                        </a>
                                        <button class="dropdown-item cute-delete-btn delete-post-btn" data-id="{{ $post->id }}" data-toggle="modal" data-target="#deleteModal">
                                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <polyline points="3 6 5 6 21 6"></polyline>
                                                <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                                <line x1="10" y1="11" x2="10" y2="17"></line>
                                                <line x1="14" y1="11" x2="14" y2="17"></line>
                                            </svg>
                                            Delete
                                        </button>
                                    @else
                                        <button class="dropdown-item report-post-btn" data-id="{{ $post->id }}" data-toggle="modal" data-target="#reportModal">
                                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M4 15s1-1 4-1 5 2 8 2 4-1 4-1V3s-1 1-4 1-5-2-8-2-4 1-4 1z"></path>
                                                <line x1="4" y1="22" x2="4" y2="15"></line>
                                            </svg>
                                            Report
                                        </button>
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

                        <div class="post-footer">
                            <a href="#" class="toggle-comments" data-id="{{ $post->id }}"><i class="la la-comment"></i> <span id="comment-count-{{ $post->id }}">{{ $post->total_comments_count }}</span> Comments</a>
                        </div>

                        {{-- Comments --}}
                        <div class="comments-section" id="comments-section-{{ $post->id }}">
                            <div class="comments-list mb-3">
                                @foreach($post->comments as $comment)
                                    <div class="comment d-flex align-items-start mb-2" id="comment-{{ $comment->id }}">
                                        <img src="{{ $comment->user->avatar_url }}" width="28" height="28" class="rounded-circle mr-2">
                                        <div style="flex: 1;">
                                            <div><strong>{{ $comment->user->name }}</strong> {{ $comment->content }}</div>
                                            <a href="#" class="reply-btn small" data-id="{{ $comment->id }}">Reply</a>
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
                            <div class="input-group">
                                <input type="text" class="form-control comment-input" placeholder="Add a comment...">
                                <div class="input-group-append">
                                    <button class="btn btn-sm comment-send" data-id="{{ $post->id }}">Send</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <p class="text-center text-muted">No posts yet.</p>
            @endforelse

            <div class="d-flex justify-content-center mt-4">{{ $posts->links() }}</div>
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
          <label><input type="radio" name="reason" value="spam"> It's spam</label>
          <label><input type="radio" name="reason" value="violence"> Violence or threats</label>
          <label><input type="radio" name="reason" value="hate_speech"> Hate speech</label>
          <label><input type="radio" name="reason" value="misinformation"> Misinformation</label>
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
          <div style="flex: 1;">
              <div><strong>${res.user}</strong> ${res.comment}</div>
              <a href="#" class="reply-btn small" data-id="${res.id}">Reply</a>
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
          <input type="text" class="form-control reply-input" placeholder="Write a reply..." style="border-radius: 10px 0 0 10px; border: 1px solid #ddd;">
          <div class="input-group-append">
              <button class="btn btn-sm reply-send" data-comment-id="${commentId}" style="border-radius: 0 10px 10px 0;">Send</button>
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
@endsection