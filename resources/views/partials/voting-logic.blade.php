<!-- resources/views/partials/voting-logic.blade.php -->
<script>
// Reddit-style Voting Logic
$(document).on('click', '.upvote-btn, .downvote-btn', function(e) {
    e.preventDefault();
    e.stopPropagation();
    
    const btn = $(this);
    const postId = btn.data('id');
    const isUpvote = btn.hasClass('upvote-btn');
    const upvoteBtn = $(`.upvote-btn[data-id="${postId}"]`);
    const downvoteBtn = $(`.downvote-btn[data-id="${postId}"]`);
    const voteCountEl = $(`#upvote-count-${postId}`);
    
    // Get current state
    const wasUpvoted = upvoteBtn.hasClass('voted-up');
    const wasDownvoted = downvoteBtn.hasClass('voted-down');
    const currentCount = parseInt(voteCountEl.text());
    
    let newCount = currentCount;
    let newVoteState = null;
    
    // Reddit-style voting logic
    if (isUpvote) {
        if (wasUpvoted) {
            // Clicking upvote again = remove upvote
            newCount = currentCount - 1;
            newVoteState = null;
        } else if (wasDownvoted) {
            // Switch from downvote to upvote (+2)
            newCount = currentCount + 2;
            newVoteState = 'up';
        } else {
            // New upvote
            newCount = currentCount + 1;
            newVoteState = 'up';
        }
    } else {
        if (wasDownvoted) {
            // Clicking downvote again = remove downvote
            newCount = currentCount + 1;
            newVoteState = null;
        } else if (wasUpvoted) {
            // Switch from upvote to downvote (-2)
            newCount = currentCount - 2;
            newVoteState = 'down';
        } else {
            // New downvote
            newCount = currentCount - 1;
            newVoteState = 'down';
        }
    }
    
    // Update UI optimistically
    voteCountEl.text(newCount);
    upvoteBtn.removeClass('voted-up');
    downvoteBtn.removeClass('voted-down');
    
    if (newVoteState === 'up') {
        upvoteBtn.addClass('voted-up');
    } else if (newVoteState === 'down') {
        downvoteBtn.addClass('voted-down');
    }
    
    // Send to server
    const vote = isUpvote ? 'up' : 'down';

    $.post(`/posts/${postId}/vote`, {
        vote: vote,
        _token: $('meta[name="csrf-token"]').attr('content')
    })
        .done(function(res) {
            // Sync with server response
            const serverCount = (res.upvotes_count || 0) - (res.downvotes_count || 0);
            voteCountEl.text(serverCount);
            
            upvoteBtn.removeClass('voted-up');
            downvoteBtn.removeClass('voted-down');
            
            if (res.user_vote === 'up') {
                upvoteBtn.addClass('voted-up');
            } else if (res.user_vote === 'down') {
                downvoteBtn.addClass('voted-down');
            }
        })
        .fail(function(xhr) {
            console.error('Vote failed:', xhr.responseText);
            // Revert UI on error
            voteCountEl.text(currentCount);
            upvoteBtn.toggleClass('voted-up', wasUpvoted);
            downvoteBtn.toggleClass('voted-down', wasDownvoted);
            alert('Failed to register vote. Please try again.');
        });
});
</script>