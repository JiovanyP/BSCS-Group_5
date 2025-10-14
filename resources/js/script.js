$(document).ready(function () {
    $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });

    // Upvote / Downvote
    $(document).on('click', '.upvote-btn, .downvote-btn', function(e){
        e.preventDefault();
        let postId = $(this).data('id');
        let vote = $(this).hasClass('upvote-btn') ? 'up' : 'down';
        $.post(`/posts/${postId}/vote`, { vote: vote }, function(res){
            $(`#upvote-count-${postId}`).text(res.upvotes_count);
            $(`#downvote-count-${postId}`).text(res.downvotes_count);
            $(`.upvote-btn[data-id="${postId}"]`).toggleClass('voted-up', res.user_vote === 'up');
            $(`.downvote-btn[data-id="${postId}"]`).toggleClass('voted-down', res.user_vote === 'down');
        }).fail(function(){ alert('Failed to vote.'); });
    });

    // Add Comment / Reply
    $(document).on('click', '.comment-send', function(){
        let btn = $(this);
        let postId = btn.data('id');
        let input = btn.closest('.input-group').find('.comment-input');
        let content = input.val().trim();
        if(!content) return;

        let data = { content: content };
        let parentId = input.data('parent');
        if(parentId) data.parent_id = parentId;

        $.post(`/posts/${postId}/comments`, data, function(res){
            let commentHTML = `
                <div class="comment mb-2 d-flex" id="comment-${res.id}">
                    <img src="${res.avatar}" class="rounded-circle mr-2" width="30" height="30">
                    <div>
                        <strong>${res.user}</strong>: ${res.comment}
                        ${res.parent_id ? '' : `<a href="#" class="reply-btn ml-2 small text-primary" data-id="${res.id}">Reply</a>`}
                        <div class="replies ml-4 mt-1"></div>
                    </div>
                </div>`;
            if(res.parent_id){
                $(`#comment-${res.parent_id} .replies`).append(commentHTML);
            } else {
                $(`#comments-section-${postId} .comments-list`).append(commentHTML);
            }
            input.val('').removeAttr('data-parent');
            $(`#comment-count-${postId}`).text(res.comments_count);
        }).fail(function(){ alert('Failed to add comment.'); });
    });

    // Reply button
    $(document).on('click', '.reply-btn', function(e){
        e.preventDefault();
        let parentId = $(this).data('id');
        let input = $(this).closest('.comments-section').find('.comment-input');
        input.focus().attr('data-parent', parentId);
    });

    // Toggle comments
    $(document).on('click', '.toggle-comments', function(e){
        e.preventDefault();
        let postId = $(this).data('id');
        $(`#comments-section-${postId}`).slideToggle('fast');
    });
});
