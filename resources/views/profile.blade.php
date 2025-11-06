@extends('layouts.app')

@section('title', 'Profile')

@section('content')
<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
<link href="https://fonts.googleapis.com/icon?family=Material+Icons+Outlined" rel="stylesheet">

<meta name="csrf-token" content="{{ csrf_token() }}">

<style>
/* === VARIABLES & BASE STYLES === */
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
    --reply-btn-default: #888;
    --upvote-color: #28a745;
    --downvote-color: #dc3545;
}

.main-content {
    flex: 1;
    overflow-y: auto;
    position: relative;
    background: #f8f9fa;
    padding: 0;
}

/* === PROFILE HEADER === */
.profile-header {
    background: #fff;
    text-align: center;
    padding: 60px 20px 50px;
    color: #000;
    position: relative;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
}

.profile-header h3 {
    color: #000;
    margin-top: 20px;
    font-size: 2rem;
    font-weight: 700;
    letter-spacing: 3px;
    text-transform: uppercase;
}

.profile-header p {
    color: #333;
}

/* === Avatar Wrapper + Camera Icon Overlay === */
.avatar-wrapper {
    position: relative;
    display: inline-block;
    margin-bottom: 20px;
}
.avatar-wrapper img {
    width: 160px;
    height: 160px;
    border-radius: 50%;
    border: 6px solid #fff;
    object-fit: cover;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
}

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
.camera-icon:hover { 
    background-color: #222; 
    transform: scale(1.05); 
}
.camera-icon .material-icons { 
    font-size: 20px; 
}

/* === TIMELINE LABEL === */
.timeline-label {
    font-size: 0.9rem;
    letter-spacing: 0.5px;
    background: #fff;
    color: var(--accent);
    font-weight: 700;
    display: inline-block;
    padding: 6px 14px;
    border-radius: 20px;
    box-shadow: 0 2px 6px rgba(0,0,0,0.08);
}

/* === MODAL STYLES === */
.modal-header.delete-header { 
    background-color: #dc3545; 
    color: white; 
}
.modal-header.report-header { 
    background-color: #d1ecf1; 
}

.report-reason-list label {
    display: block;
    margin-bottom: 8px;
    font-weight: 400;
    color: #333;
}

#confirmReportBtn {
    background-color: var(--accent) !important;
    color: #fff !important;
    border: none !important;
    font-weight: 700;
    transition: background-color 0.25s;
}
#confirmReportBtn:hover {
    background-color: var(--accent-2) !important;
}

.modal-footer .btn-secondary {
    background-color: #f0f0f0 !important;
    color: #666 !important;
    border: 1px solid #ddd !important;
    font-weight: 500;
}
.modal-footer .btn-secondary:hover {
    background-color: #e9e9e9 !important;
}

/* === ALERT === */
.alert {
    border-radius: 12px;
    animation: fadeIn 0.3s ease-in;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(-10px); }
    to { opacity: 1; transform: translateY(0); }
}
</style>

<div class="main-content">
    <div class="profile-header">
        <form id="avatarForm" action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
            @csrf @method('PATCH')
            <div class="avatar-wrapper">
                <img id="avatarPreview" src="{{ Auth::user()->avatar_url }}" alt="Avatar">
                <label class="camera-icon" for="avatarInput" title="Change avatar">
                    <span class="material-icons">camera_alt</span>
                </label>
                <input type="file" id="avatarInput" name="avatar" accept="image/*" style="display:none;">
            </div>
        </form>
        <h4 style="font-weight: bold;">{{ strtoupper(Auth::user()->name) }}</h4>
        <p style="margin-top: 8px; font-size: 15px; opacity: 0.95;">
            {{ Auth::user()->email }}
        </p>
        @if(Auth::user()->address)
        <p style="margin-top: 4px; font-size: 14px; opacity: 0.9;">
            <span class="material-icons-outlined" style="font-size: 16px; vertical-align: middle;">location_on</span>
            {{ Auth::user()->address }}
        </p>
        @endif
    </div>

    <div class="container mt-4">
        <div class="col-xl-8 mx-auto posts-container">

            {{-- Success Alert --}}
            @if (session('success'))
                <div class="alert alert-success text-center" id="successAlert">
                    {{ session('success') }}
                </div>
                <script>
                    setTimeout(() => document.getElementById('successAlert').style.display = 'none', 3000);
                </script>
            @endif

            {{-- Timeline with Date Labels --}}
            @php $currentDate = null; @endphp
            @forelse ($posts as $post)
                @if ($currentDate !== $post->created_at->toDateString())
                    <div class="timeline-label text-center font-weight-bold my-3">
                        {{ $post->created_at->isToday() ? 'Today' : ($post->created_at->isYesterday() ? 'Yesterday' : $post->created_at->format('F j, Y')) }}
                    </div>
                    @php $currentDate = $post->created_at->toDateString(); @endphp
                @endif

                {{-- Reusable Post Partial --}}
                @include('partials.post', ['post' => $post])
            @empty
                <p class="text-center text-muted">No posts yet.</p>
            @endforelse

            {{-- Pagination --}}
            <div class="d-flex justify-content-center mt-4">{{ $posts->links() }}</div>
        </div>
    </div>
</div>

{{-- Shared Delete & Report Modals --}}
@include('partials.delete-report-modals')

{{-- Edit Profile Modal --}}
@include('partials.edit-modal')

{{-- jQuery + Bootstrap JS --}}
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
$(function(){
  $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });
  let currentPostId = null;

  // Prevent scroll-to-top on placeholder links
  $(document).on('click', 'a[href="#"]', function(e) {
    if (!$(this).attr('data-toggle') && !$(this).attr('data-target')) e.preventDefault();
  });

  // Dynamic Send Button Logic
  $(document).on('input', '.comment-input', function() {
    const postId = $(this).attr('id').replace('comment-input-', '');
    const sendBtn = $(`#comment-send-${postId}`);
    sendBtn.prop('disabled', $(this).val().trim() === '');
  });

  $(document).on('input', '.reply-input', function() {
    const sendBtn = $(this).closest('.reply-input-group').find('.reply-send');
    sendBtn.prop('disabled', $(this).val().trim() === '');
  });

  // Toggle comments
  $(document).on('click','.toggle-comments',function(e){
    e.preventDefault();
    e.stopPropagation();
    const id=$(this).data('id');
    $(`#comments-section-${id}`).slideToggle('fast', function() {
        const input = $(`#comment-input-${id}`);
        const sendBtn = $(`#comment-send-${id}`);
        sendBtn.prop('disabled', input.val().trim() === '');
    });
  });

  // Voting
  $(document).on('click','.upvote-btn,.downvote-btn',function(e){
    e.preventDefault();
    e.stopPropagation();
    const btn = $(this);
    const id = btn.data('id');
    const vote = btn.hasClass('upvote-btn') ? 'up' : 'down';
    
    $.post(`/posts/${id}/vote`, {vote: vote}, res => {
      const netScore = res.upvotes_count - res.downvotes_count;
      $(`#upvote-count-${id}`).text(netScore);
      
      $(`.upvote-btn[data-id="${id}"]`).removeClass('voted-up');
      $(`.downvote-btn[data-id="${id}"]`).removeClass('voted-down');
      
      if (res.user_vote === 'up') {
        $(`.upvote-btn[data-id="${id}"]`).addClass('voted-up');
      } else if (res.user_vote === 'down') {
        $(`.downvote-btn[data-id="${id}"]`).addClass('voted-down');
      }
    }).fail(function(xhr) {
      console.error('Vote failed:', xhr.responseText);
      alert('Failed to register vote. Please try again.');
    });
  });

  // Add comment
  $(document).on('click','.comment-send:not(:disabled)',function(){
    const btn=$(this);
    const id=btn.data('id');
    const input=$(`#comment-input-${id}`);
    const content=input.val().trim();
    if(!content) return;

    btn.prop('disabled', true);
    btn.text('Sending...');

    $.post(`/posts/${id}/comments`,{content:content},res=>{
      const html=`<div class="comment" id="comment-${res.id}">
          <img src="${res.avatar}" width="28" height="28" class="rounded-circle">
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
      btn.text('Send');
    });
  });

  // Reply button
  $(document).on('click','.reply-btn',function(e){
    e.preventDefault();
    const commentId=$(this).data('id');
    const repliesDiv=$(`#comment-${commentId} .replies`);

    if(repliesDiv.find('.reply-input-group').length === 0){
      const replyInputId = `reply-input-${commentId}`;
      const replySendId = `reply-send-${commentId}`;

      const replyInput=`<div class="reply-input-group">
          <input type="text" class="form-control reply-input" id="${replyInputId}" placeholder="Write a reply...">
          <button class="reply-send" data-comment-id="${commentId}" id="${replySendId}" disabled>Send</button>
      </div>`;
      repliesDiv.append(replyInput);
      $(`#${replyInputId}`).trigger('input').focus();
    }
  });

  // Send reply
  $(document).on('click','.reply-send:not(:disabled)',function(){
    const btn=$(this);
    const commentId=btn.data('comment-id');
    const input=$(`#reply-input-${commentId}`);
    const content=input.val().trim();
    if(!content) return;

    btn.prop('disabled', true);
    btn.text('Sending...');

    $.post(`/comments/${commentId}/reply`,{content:content},res=>{
      const html=`<div class="comment" id="comment-${res.id}">
          <img src="${res.avatar}" width="25" height="25" class="rounded-circle">
          <div><strong>${res.user}</strong> ${res.content}</div>
      </div>`;
      $(`#comment-${commentId} .replies`).prepend(html);
      const postId = $(`#comment-${commentId}`).closest('.post-content').find('.toggle-comments').data('id');
      const countSpan = $(`#comment-count-${postId}`);
      countSpan.text(parseInt(countSpan.text()) + 1);

      input.closest('.reply-input-group').remove();
    });
  });

  // Delete post
  $(document).on('click','.delete-post-btn',function(){ currentPostId=$(this).data('id'); });
  $('#confirmDeleteBtn').click(function(){
    $.ajax({ url:`/posts/${currentPostId}`, type:'POST', data:{_method:'DELETE'},
      success:()=>{ $(`#post-${currentPostId}`).fadeOut(300,()=>$(this).remove()); $('#deleteModal').modal('hide'); }
    });
  });

  // Report post
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
            $('#avatarForm').submit();
        }
    });
});
</script>

{{-- ✅ Include Reddit-style Voting Logic --}}
@include('partials.voting-logic')

@endsection