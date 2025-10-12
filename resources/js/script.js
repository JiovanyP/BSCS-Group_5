$(document).ready(function () {
    // ✅ Setup CSRF token for all AJAX requests
    $.ajaxSetup({
        headers: { "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content") },
    });

    // ✅ Delete Post
    $(document).on("click", ".delete-post-btn", function (e) {
        e.preventDefault();
        if (!confirm("Are you sure you want to delete this post?")) return;

        let postId = $(this).data("id");
        let card = $(this).closest(".timeline-item");

        $.ajax({
            url: `/posts/${postId}`,
            type: "DELETE",
            success: function () {
                card.remove(); // ✅ Remove post from timeline without reload
            },
            error: function () {
                alert("Something went wrong while deleting the post.");
            },
        });
    });

    // ✅ Toggle Comments (show/hide)
    $(document).on("click", ".toggle-comments", function (e) {
        e.preventDefault();
        let postId = $(this).data("id");
        $(`#comments-section-${postId}`).slideToggle("fast"); // ✅ Smooth show/hide
    });

    // ✅ Upvote
    $(document).on("click", ".upvote-btn", function (e) {
        e.preventDefault();
        let postId = $(this).data("id");

        $.post(`/posts/${postId}/vote`, { vote: "upvote" }, function (res) {
            // Update counts
            $(`#upvote-count-${postId}`).text(res.upvotes_count);
            $(`#downvote-count-${postId}`).text(res.downvotes_count);

            // Highlight active button
            $(`.upvote-btn[data-id="${postId}"]`).toggleClass("voted-up", res.user_vote === "up");
            $(`.downvote-btn[data-id="${postId}"]`).removeClass("voted-down");
        }).fail(function () {
            alert("Failed to upvote. Please try again.");
        });
    });

    // ✅ Downvote
    $(document).on("click", ".downvote-btn", function (e) {
        e.preventDefault();
        let postId = $(this).data("id");

        $.post(`/posts/${postId}/vote`, { vote: "downvote" }, function (res) {
            // Update counts
            $(`#upvote-count-${postId}`).text(res.upvotes_count);
            $(`#downvote-count-${postId}`).text(res.downvotes_count);

            // Highlight active button
            $(`.downvote-btn[data-id="${postId}"]`).toggleClass("voted-down", res.user_vote === "down");
            $(`.upvote-btn[data-id="${postId}"]`).removeClass("voted-up");
        }).fail(function () {
            alert("Failed to downvote. Please try again.");
        });
    });

    // ✅ Add Comment / Reply
    $(document).on("click", ".comment-send", function () {
        let btn = $(this);
        let postId = btn.data("id");
        let input = btn.closest(".input-group").find(".comment-input");
        let content = input.val().trim();
        let parentId = input.data("parent") || null;

        if (content === "") return;

        $.post(`/posts/${postId}/comments`, { content: content, parent_id: parentId }, function (res) {
            input.val("");
            input.removeAttr("data-parent");

            if (res.parent_id) {
                // ✅ Append reply
                $(`#comment-${res.parent_id} .replies`).append(`
                    <div class="reply mb-1 d-flex" id="comment-${res.id}">
                        <img src="${res.avatar}" class="rounded-circle mr-2" style="width:30px;height:30px;">
                        <div><strong>${res.user}:</strong> ${res.comment}</div>
                    </div>
                `);
            } else {
                // ✅ Append top-level comment
                btn.closest(".comments-section").find(".comments-list").append(`
                    <div class="comment mb-2 d-flex" id="comment-${res.id}">
                        <img src="${res.avatar}" class="rounded-circle mr-2" style="width:35px;height:35px;">
                        <div>
                            <strong>${res.user}:</strong> ${res.comment}
                            <a href="#" class="reply-btn ml-2 small text-primary" data-id="${res.id}">Reply</a>
                            <div class="replies ml-4 mt-1"></div>
                        </div>
                    </div>
                `);
            }

            // ✅ Update comment count
            btn.closest(".widget").find(".toggle-comments span").text(res.comments_count);
        }).fail(function () {
            alert("Failed to add comment. Please try again.");
        });
    });

    // ✅ Reply button → focuses input for reply
    $(document).on("click", ".reply-btn", function (e) {
        e.preventDefault();
        let parentId = $(this).data("id");
        let input = $(this).closest(".widget").find(".comment-input");
        input.focus();
        input.attr("data-parent", parentId);
    });
});
