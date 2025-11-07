$(function () {
    $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });

    // Avatar upload
    $('#avatarPreview').click(() => $('#avatarInput').click());
    $('#avatarInput').change(function () { if (this.files.length) $('#avatarForm').submit(); });

    // Remove avatar
    $('#removeAvatarBtn').click(function () {
        if (confirm('Are you sure?')) {
            $.post('{{ route("profile.remove") }}', {}, () => location.reload());
        }
    });

    let currentPostId = null;

    // Upvote / Downvote
    $(document).on('click', '.upvote-btn,.downvote-btn', function (e) {
        e.preventDefault();
        let id = $(this).data('id');
        let vote = $(this).hasClass('upvote-btn') ? 'up' : 'down';
        $.post(`/posts/${id}/vote`, { vote: vote }, res => {
            $(`#upvote-count-${id}`).text(res.upvotes_count - res.downvotes_count);
            $(`.upvote-btn[data-id="${id}"]`).toggleClass('voted-up', res.user_vote === 'up');
            $(`.downvote-btn[data-id="${id}"]`).toggleClass('voted-down', res.user_vote === 'down');
        });
    });

    // Toggle comments
    $(document).on('click', '.toggle-comments', function (e) {
        e.preventDefault();
        let id = $(this).data('id');
        $(`#comments-section-${id}`).slideToggle();
    });

    // Send comment
    $(document).on('click', '.comment-send', function () {
        let btn = $(this);
        let id = btn.data('id');
        let input = btn.closest('.input-group').find('.comment-input');
        let content = input.val().trim();
        if (!content) return;

        $.post(`/posts/${id}/comments`, { content: content }, res => {
            let html = `<div class="d-flex mb-2" id="comment-${res.id}">
                <img src="${res.avatar}" width="30" height="30" class="rounded-circle mr-2">
                <div><strong>${res.user}</strong> ${res.comment} <a href="#" class="reply-btn small text-primary" data-id="${res.id}">Reply</a>
                    <div class="replies ml-3 mt-1"></div>
                </div>
            </div>`;
            $(`#comments-section-${id} .comments-list`).append(html);
            input.val('');
            // Update comment count
            $(`#comment-count-${id}`).text(res.comments_count);
        });
    });

    // Reply button
    $(document).on('click', '.reply-btn', function (e) {
        e.preventDefault();
        let id = $(this).data('id');
        let replies = $(`#comment-${id} .replies`);
        if (!replies.find('.reply-input-group').length) {
            replies.append(`<div class="input-group input-group-sm reply-input-group mt-1">
                <input type="text" class="form-control reply-input" placeholder="Write a reply...">
                <div class="input-group-append"><button class="btn btn-sm btn-primary reply-send" data-comment-id="${id}">Send</button></div>
            </div>`);
        }
    });

    // Send reply
    $(document).on('click', '.reply-send', function () {
        let btn = $(this);
        let id = btn.data('comment-id');
        let input = btn.closest('.input-group').find('.reply-input');
        let content = input.val().trim();
        if (!content) return;

        $.post(`/comments/${id}/reply`, { content: content }, res => {
            let html = `<div class="d-flex mb-1" id="comment-${res.id}">
                <img src="${res.avatar}" width="25" height="25" class="rounded-circle mr-2">
                <div><strong>${res.user}</strong> ${res.comment}</div>
            </div>`;
            $(`#comment-${id} .replies`).prepend(html);
            input.closest('.reply-input-group').remove();
            // Update comment count
            let postId = $(`#comment-${id}`).closest('.comments-section').attr('id').split('-').pop();
            $(`#comment-count-${postId}`).text(res.comments_count);
        });
    });

    // Delete post
    $(document).on('click', '.delete-post-btn', function () { currentPostId = $(this).data('id'); });
    $('#confirmDeleteBtn').click(function () {
        $.ajax({
            url: `/posts/${currentPostId}`, type: 'POST', data: { _method: 'DELETE' },
            success: () => { $(`#post-${currentPostId}`).fadeOut(() => $(this).remove()); $('#deleteModal').modal('hide'); }
        });
    });
});
