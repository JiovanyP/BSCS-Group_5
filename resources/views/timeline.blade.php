@extends('layouts.app')

@section('title', 'Timeline')

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
    /* Custom voting colors */
    --upvote-color: #28a745;
    --downvote-color: #dc3545;
}

.main-content {
    flex: 1;
    overflow-y: auto;
    position: relative;
    background: #f8f9fa;
    padding: 20px 0;
}

/* === POST CARD === */
.post-card {
    background: var(--card-bg);
    border-radius: 16px;
    box-shadow: 0 12px 40px rgba(0,0,0,0.1);
    margin-bottom: 20px;
    transition: all 0.25s ease;
}

.post-content {
    padding: 1.5rem 2rem;
}

/* Post Header and Report Details */
.post-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 0.5rem;
}

.report-details {
    font-size: 15px;
    font-weight: 700;
    color: var(--accent);
    line-height: 1.4;
    text-transform: uppercase;
}
.report-details .location {
    font-weight: 500;
    color: #333;
    text-transform: none;
}

.post-body {
    color: #333;
    line-height: 1.6;
    font-size: 15px;
    margin-top: 0.5rem;
    margin-bottom: 1rem;
}
.post-body img, .post-body video {
    max-width: 100%;
    border-radius: 10px;
    margin-top: 12px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

/* === Post Signature Spacing === */
.post-signature {
    padding-top: 10px;
    margin-bottom: 1rem;
    border-top: 1px solid #f0f0f0;
}
.user-info {
    display: flex;
    align-items: center;
    gap: 8px; /* Standard spacing between elements */
}
.user-info strong {
    font-size: 15px;
    font-weight: 600;
}
.user-info small {
    color: var(--text-muted);
    font-size: 13px;
    margin-left: auto; /* Pushes the time to the far right */
}

/* === POST FOOTER (Voting and Comments) === */
.post-footer {
    display: flex;
    align-items: center;
    margin-top: 1rem;
    padding-top: 0.75rem;
    border-top: 1px solid #f0f0f0;
    gap: 15px; /* Spacing between comment pill and vote pill */
}

.footer-action {
    display: flex;
    align-items: center;
    color: var(--text-muted);
    text-decoration: none;
    font-size: 14px;
    font-weight: 500;
    transition: color 0.2s;
    cursor: pointer;
}

.comment-container {
    display: flex;
    align-items: center;
    background: #f0f0f0;
    border-radius: 18px;
    padding: 2px 8px;
    transition: background 0.2s;
}
.comment-container:hover {
    background: #e9e9e9;
}
.comment-container .footer-action {
    padding: 4px 6px;
    color: var(--text-muted);
}
.comment-container .footer-action:hover {
    color: var(--accent); /* Pink when hovered/clicked */
}
.comment-container .material-icons-outlined {
    margin-right: 4px;
    font-size: 20px;
}

.vote-container {
    display: flex;
    align-items: center;
    margin-left: auto; /* Pushes the vote counter to the right */
    background: #f0f0f0;
    border-radius: 18px;
    padding: 2px;
}

.upvote-btn, .downvote-btn {
    padding: 4px 8px;
}
.upvote-btn:hover { color: var(--upvote-color); }
.downvote-btn:hover { color: var(--downvote-color); }

.voted-up { color: var(--upvote-color) !important; }
.voted-down { color: var(--downvote-color) !important; }

/* === Comments & Replies Styles === */
.comments-section {
    display: none;
    background: #f8f9fa;
    border-top: 1px solid #eee;
    padding: 1.5rem 2rem 0;
    border-radius: 0 0 16px 16px;
    margin: 0 -2rem -1.5rem -2rem;
    overflow: hidden;
}

.comment {
    display: flex;
    align-items: flex-start;
    margin-bottom: 0.5rem;
    gap: 8px; 
}
.comment img {
    flex-shrink: 0;
}
.comment strong {
    font-weight: 600;
    margin-right: 4px;
}

.replies .comment {
    display: flex;
    align-items: flex-start;
    gap: 6px; 
    margin-left: 20px; 
    margin-top: 4px;
}

.comments-section > .input-group {
    margin-bottom: 1.5rem;
    position: relative;
    height: 44px;
}

.comment-input, .reply-input {
    width: 100%;
    padding: 12px 60px 12px 16px !important;
    border-radius: 22px !important;
    border: 1px solid var(--border-color);
    font-size: 14px;
    background: var(--input-bg);
    transition: border-color 0.2s, box-shadow 0.2s;
    position: absolute;
    top: 0;
    left: 0;
    height: 100%;
}

.comment-input:focus, .reply-input:focus {
    border-color: var(--accent) !important;
    background: #fff;
    box-shadow: 0 0 0 3px rgba(207, 15, 71, 0.15) !important;
    outline: none;
}

.comment-send, .reply-send {
    position: absolute;
    right: 5px;
    top: 50%;
    transform: translateY(-50%);
    z-index: 10;
    background: var(--btn-disabled-bg) !important;
    color: var(--btn-disabled-color) !important;
    border: none;
    font-weight: 700;
    transition: background 0.25s, color 0.25s;
    padding: 4px 12px;
    font-size: 14px;
    border-radius: 18px !important;
    height: 34px;
    line-height: 1.8;
}

.comment-send:not(:disabled), .reply-send:not(:disabled) {
    background: var(--accent) !important;
    color: #fff !important;
}
.comment-send:not(:disabled):hover, .reply-send:not(:disabled):hover {
    background: var(--accent-2) !important;
}

.reply-btn {
    font-size: 0.875rem;
    cursor: pointer;
    color: var(--reply-btn-default) !important;
    font-weight: 500;
    text-decoration: none !important;
    transition: color 0.2s;
    display: inline-block;
}
.reply-btn:hover, .reply-btn:focus {
    color: var(--accent) !important;
}

.reply-input-group {
    position: relative;
    height: 44px;
    margin-top: 0.75rem;
}

/* Dropdown menu items for Edit/Delete/Report */
.dropdown-item {
    display: flex;
    align-items: center;
    gap: 8px; /* Spacing between icon and text */
}
.dropdown-item .material-icons {
    font-size: 18px;
}

/* === REPORT MODAL SPECIFIC STYLES === */

/* Ensure radio buttons stack */
.report-reason-list label {
    display: block; /* Makes each label take up the full width */
    margin-bottom: 8px; /* Adds space between options */
    font-weight: 400; /* Standard weight */
    color: #333; /* Standard text color */
}

/* Style for Report Button (Pink) */
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

/* Style for Cancel Button (Grey) */
.modal-footer .btn-secondary {
    background-color: #f0f0f0 !important;
    color: #666 !important;
    border: 1px solid #ddd !important;
    font-weight: 500;
}
.modal-footer .btn-secondary:hover {
    background-color: #e9e9e9 !important;
}

</style>

<div class="main-content">
    <div class="container mt-4">
        <div class="col-xl-8 mx-auto">

            {{-- Success Alert (omitted for brevity) --}}
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

                    <div class="post-content">
                        <div class="post-header">
                            <div class="report-details">
                                {{ strtoupper($post->accident_type) }} • <span class="location">{{ $post->location }}</span>
                                @if($post->other_type) <small class="text-muted">({{ $post->other_type }})</small> @endif
                            </div>

                            <div class="dropdown">
                                <a href="#" class="text-muted" data-toggle="dropdown"><span class="material-icons">more_horiz</span></a>
                                <div class="dropdown-menu dropdown-menu-right">
                                    {{-- Edit/Delete/Report Buttons as before --}}
                                    @if(auth()->id() === $post->user_id)
                                        <a class="dropdown-item cute-edit-btn" href="{{ route('posts.edit', $post->id) }}">
                                            <span class="material-icons">edit</span> Edit
                                        </a>
                                        <button class="dropdown-item cute-delete-btn delete-post-btn" data-id="{{ $post->id }}" data-toggle="modal" data-target="#deleteModal">
                                            <span class="material-icons">delete</span> Delete
                                        </button>
                                    @else
                                        <button class="dropdown-item report-post-btn" data-id="{{ $post->id }}" data-toggle="modal" data-target="#reportModal">
                                            <span class="material-icons">flag</span> Report
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

                        {{-- Post Signature --}}
                        <div class="post-signature">
                            <div class="user-info">
                                <img src="{{ $post->user->avatar_url }}" width="32" height="32" class="rounded-circle">
                                <strong>{{ $post->user->name }}</strong>
                                <small>{{ $post->created_at->diffForHumans() }}</small>
                            </div>
                        </div>
                        {{-- END Post Signature --}}

                        {{-- Post Footer with Comment Pill --}}
                        <div class="post-footer">
                            <div class="comment-container">
                                <a href="#" class="toggle-comments footer-action" data-id="{{ $post->id }}">
                                    <span class="material-icons-outlined">chat_bubble_outline</span>
                                    <span id="comment-count-{{ $post->id }}">{{ $post->total_comments_count }}</span>
                                </a>
                            </div>
                            
                            <div class="vote-container">
                                <a href="#" class="upvote-btn footer-action {{ $userVote==='up'?'voted-up':'' }}" data-id="{{ $post->id }}">
                                    <span class="material-icons">arrow_upward</span>
                                </a>
                                <div class="vote-count" id="upvote-count-{{ $post->id }}">
                                    {{ $post->upvotes()->count() - $post->downvotes()->count() }}
                                </div>
                                <a href="#" class="downvote-btn footer-action {{ $userVote==='down'?'voted-down':'' }}" data-id="{{ $post->id }}">
                                    <span class="material-icons">arrow_downward</span>
                                </span>
                                </a>
                            </div>
                        </div>

                        {{-- Comments --}}
                        <div class="comments-section" id="comments-section-{{ $post->id }}">
                            <div class="comments-list mb-3">
                                @foreach($post->comments as $comment)
                                    <div class="comment" id="comment-{{ $comment->id }}">
                                        <img src="{{ $comment->user->avatar_url }}" width="28" height="28" class="rounded-circle">
                                        <div style="flex: 1;">
                                            <div><strong>{{ $comment->user->name }}</strong> {{ $comment->content }}</div>
                                            <a href="#" class="reply-btn small" data-id="{{ $comment->id }}">Reply</a>
                                            <div class="replies">
                                                @foreach($comment->replies as $reply)
                                                    <div class="comment" id="comment-{{ $reply->id }}">
                                                        <img src="{{ $reply->user->avatar_url }}" width="25" height="25" class="rounded-circle">
                                                        <div><strong>{{ $reply->user->name }}</strong> {{ $reply->content }}</div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <div class="input-group">
                                <input type="text" class="form-control comment-input" id="comment-input-{{ $post->id }}" placeholder="Add a comment...">
                                <button class="comment-send" data-id="{{ $post->id }}" id="comment-send-{{ $post->id }}" disabled>Send</button>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <p class="text-center text-muted">No reports yet.</p>
            @endforelse

            <div class="d-flex justify-content-center mt-4">{{ $posts->links() }}</div>
        </div>
    </div>
</div>

{{-- Delete Modal (No changes requested) --}}
<div class="modal fade" id="deleteModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header delete-header">
        <h5 class="modal-title"><span class="material-icons">warning</span> Delete Report</h5>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">Are you sure you want to delete this report?</div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Delete</button>
      </div>
    </div>
  </div>
</div>

{{-- MODIFIED REPORT MODAL --}}
<div class="modal fade" id="reportModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header report-header">
        {{-- Removed the 'x' close button here --}}
        <h5 class="modal-title"><span class="material-icons">flag</span> Report Post</h5>
      </div>
      <div class="modal-body">
        <p>Select a reason for reporting:</p>
        <form id="reportForm" class="report-reason-list">
          {{-- Applied 'display: block' styling via .report-reason-list for stacking --}}
          <label><input type="radio" name="reason" value="spam"> It's spam</label>
          <label><input type="radio" name="reason" value="violence"> Violence or threats</label>
          <label><input type="radio" name="reason" value="hate_speech"> Hate speech</label>
          <label><input type="radio" name="reason" value="misinformation"> Misinformation</label>
          <label><input type="radio" name="reason" value="other"> Other</label>
        </form>
      </div>
      <div class="modal-footer">
        {{-- Cancel button is now grey --}}
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
        {{-- Report button is now pink --}}
        <button type="button" class="btn" id="confirmReportBtn">Report</button>
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

  // --- Dynamic Send Button Logic ---
  $(document).on('input', '.comment-input', function() {
    const postId = $(this).attr('id').replace('comment-input-', '');
    const sendBtn = $(`#comment-send-${postId}`);
    sendBtn.prop('disabled', $(this).val().trim() === '');
  });

  $(document).on('input', '.reply-input', function() {
    const sendBtn = $(this).closest('.reply-input-group').find('.reply-send');
    sendBtn.prop('disabled', $(this).val().trim() === '');
  });
  // ---------------------------------

  $(document).on('click','.toggle-comments',function(e){
    e.preventDefault();
    const id=$(this).data('id');
    $(`#comments-section-${id}`).slideToggle('fast', function() {
        const input = $(`#comment-input-${id}`);
        const sendBtn = $(`#comment-send-${id}`);
        sendBtn.prop('disabled', input.val().trim() === '');
    });
  });

    $(document).on('click','.upvote-btn,.downvote-btn',function(e){
        e.preventDefault();
        const btn = $(this);
        const id = btn.data('id');
        const vote = btn.hasClass('upvote-btn') ? 'up' : 'down';
        
        $.post(`/posts/${id}/vote`, {vote: vote}, res => {
            // Update vote count (net score)
            const netScore = res.upvotes_count - res.downvotes_count;
            $(`#upvote-count-${id}`).text(netScore);
            
            // Remove both vote classes first
            $(`.upvote-btn[data-id="${id}"]`).removeClass('voted-up');
            $(`.downvote-btn[data-id="${id}"]`).removeClass('voted-down');
            
            // Apply the appropriate class based on user's current vote
            if (res.user_vote === 'up') {
                $(`.upvote-btn[data-id="${id}"]`).addClass('voted-up');
            } else if (res.user_vote === 'down') {
                $(`.downvote-btn[data-id="${id}"]`).addClass('voted-down');
            }
            // If user_vote is null, no classes are added (vote was undone)
        }).fail(function(xhr) {
            console.error('Vote failed:', xhr.responseText);
            alert('Failed to register vote. Please try again.');
        });
    });
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
      $(`#${replyInputId}`).trigger('input');
    }
  });

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