/**
 * Post Interactions Script
 * Handles comments, replies, and voting for post cards
 * 
 * Usage: Include this file after jQuery and call initializePostInteractions()
 * File location: public/js/post-interactions.js
 */

function initializePostInteractions() {
    // Prevent multiple initializations
    if (window.postInteractionsInitialized) {
        return;
    }
    window.postInteractionsInitialized = true;

    // ===== TOGGLE COMMENTS =====
    $(document).on('click', '.toggle-comments', function(e) {
        e.preventDefault();
        e.stopPropagation();
        const id = $(this).data('id');
        $(`#comments-section-${id}`).slideToggle(200);
    });

    // ===== DYNAMIC SEND BUTTON ENABLE/DISABLE =====
    $(document).on('input', '.comment-input', function() {
        const postId = $(this).attr('id').replace('comment-input-', '');
        const sendBtn = $(`#comment-send-${postId}`);
        sendBtn.prop('disabled', $(this).val().trim() === '');
    });

    $(document).on('input', '.reply-input', function() {
        const sendBtn = $(this).closest('.reply-input-group').find('.reply-send');
        sendBtn.prop('disabled', $(this).val().trim() === '');
    });

    // ===== ADD COMMENT =====
    $(document).on('click', '.comment-send', function() {
        const btn = $(this);
        if (btn.prop('disabled')) return;
        
        const id = btn.data('id');
        const input = $(`#comment-input-${id}`);
        const content = input.val().trim();
        if (!content) return;

        btn.prop('disabled', true).text('Sending...');

        $.post(`/posts/${id}/comments`, { content: content })
            .done(function(res) {
                const html = `<div class="comment" id="comment-${res.id}">
                    <img src="${res.avatar}" width="28" height="28" class="rounded-circle">
                    <div style="flex: 1;">
                        <div><strong>${res.user}</strong> ${res.comment}</div>
                        <a href="#" class="reply-btn small" data-id="${res.id}">Reply</a>
                        <div class="replies"></div>
                    </div>
                </div>`;

                if ($(`#comments-section-${id} .comments-list`).length === 0) {
                    $(`#comments-section-${id}`).prepend(
                        '<div class="comments-header">Comments</div><div class="comments-list mb-3"></div>'
                    );
                }

                $(`#comments-section-${id} .comments-list`).append(html);
                const countEl = $(`#comment-count-${id}`);
                const count = parseInt(countEl.text() || '0') + 1;
                countEl.text(count);
                input.val('');
                btn.prop('disabled', false).text('Send');
            })
            .fail(function(xhr) {
                console.error('Comment failed:', xhr.responseText);
                alert('Failed to add comment. Please try again.');
                btn.prop('disabled', false).text('Send');
            });
    });

    // ===== REPLY BUTTON =====
    $(document).on('click', '.reply-btn', function(e) {
        e.preventDefault();
        const commentId = $(this).data('id');
        const repliesDiv = $(`#comment-${commentId} .replies`);
        if (repliesDiv.find('.reply-input-group').length === 0) {
            const replyInputId = `reply-input-${commentId}`;
            const replySendId = `reply-send-${commentId}`;
            const replyInput = `<div class="reply-input-group">
                <input type="text" class="form-control reply-input" id="${replyInputId}" placeholder="Write a reply...">
                <button class="reply-send btn btn-sm btn-primary" data-comment-id="${commentId}" id="${replySendId}" disabled>Send</button>
            </div>`;
            repliesDiv.append(replyInput);
            $(`#${replyInputId}`).trigger('input').focus();
        }
    });

    // ===== SEND REPLY =====
    $(document).on('click', '.reply-send', function() {
        const btn = $(this);
        if (btn.prop('disabled')) return;
        
        const commentId = btn.data('comment-id');
        const input = $(`#reply-input-${commentId}`);
        const content = input.val().trim();
        if (!content) return;

        btn.prop('disabled', true).text('Sending...');

        $.post(`/comments/${commentId}/reply`, { content: content })
            .done(function(res) {
                const html = `<div class="comment" id="comment-${res.id}">
                    <img src="${res.avatar}" width="25" height="25" class="rounded-circle">
                    <div><strong>${res.user}</strong> ${res.content}</div>
                </div>`;
                $(`#comment-${commentId} .replies`).prepend(html);
                const postId = $(`#comment-${commentId}`).closest('.post-content').find('.toggle-comments').data('id');
                const countSpan = $(`#comment-count-${postId}`);
                countSpan.text(parseInt(countSpan.text() || '0') + 1);
                input.closest('.reply-input-group').remove();
            })
            .fail(function(xhr) {
                console.error('Reply failed:', xhr.responseText);
                alert('Failed to send reply. Try again.');
                btn.prop('disabled', false).text('Send');
            });
    });

    // ===== VOTING LOGIC =====
    $(document).on('click', '.upvote-btn, .downvote-btn', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        const btn = $(this);
        const postId = btn.data('id');
        const isUpvote = btn.hasClass('upvote-btn');
        const upvoteBtn = $(`.upvote-btn[data-id="${postId}"]`);
        const downvoteBtn = $(`.downvote-btn[data-id="${postId}"]`);
        const voteCountEl = $(`#upvote-count-${postId}`);
        const wasUpvoted = upvoteBtn.hasClass('voted-up');
        const wasDownvoted = downvoteBtn.hasClass('voted-down');
        const currentCount = parseInt(voteCountEl.text());
        let newCount = currentCount;
        let newVoteState = null;

        // Calculate new vote state
        if (isUpvote) {
            if (wasUpvoted) { 
                // Removing upvote
                newCount = currentCount - 1; 
                newVoteState = null; 
            } else if (wasDownvoted) { 
                // Changing from downvote to upvote
                newCount = currentCount + 2; 
                newVoteState = 'up'; 
            } else { 
                // Adding new upvote
                newCount = currentCount + 1; 
                newVoteState = 'up'; 
            }
        } else {
            if (wasDownvoted) { 
                // Removing downvote
                newCount = currentCount + 1; 
                newVoteState = null; 
            } else if (wasUpvoted) { 
                // Changing from upvote to downvote
                newCount = currentCount - 2; 
                newVoteState = 'down'; 
            } else { 
                // Adding new downvote
                newCount = currentCount - 1; 
                newVoteState = 'down'; 
            }
        }

        // Optimistic UI update
        voteCountEl.text(newCount);
        upvoteBtn.removeClass('voted-up');
        downvoteBtn.removeClass('voted-down');
        if (newVoteState === 'up') upvoteBtn.addClass('voted-up');
        else if (newVoteState === 'down') downvoteBtn.addClass('voted-down');

        // Send to server
        const vote = isUpvote ? 'up' : 'down';
        $.post(`/posts/${postId}/vote`, { vote: vote })
            .done(function(res) {
                // Update with server response
                const serverCount = (res.upvotes_count || 0) - (res.downvotes_count || 0);
                voteCountEl.text(serverCount);
                upvoteBtn.removeClass('voted-up');
                downvoteBtn.removeClass('voted-down');
                if (res.user_vote === 'up') upvoteBtn.addClass('voted-up');
                else if (res.user_vote === 'down') downvoteBtn.addClass('voted-down');
            })
            .fail(function(xhr) {
                console.error('Vote failed:', xhr.responseText);
                // Rollback on failure
                voteCountEl.text(currentCount);
                upvoteBtn.toggleClass('voted-up', wasUpvoted);
                downvoteBtn.toggleClass('voted-down', wasDownvoted);
                if (xhr.responseJSON && xhr.responseJSON.error) {
                    alert(xhr.responseJSON.error);
                } else {
                    alert('Failed to register vote. Please try again.');
                }
            });
    });
}

// Auto-initialize if jQuery is already loaded
if (typeof jQuery !== 'undefined') {
    $(function() {
        initializePostInteractions();
    });
}