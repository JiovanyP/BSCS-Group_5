$(document).ready(function () {
    // ✅ Setup CSRF token for all AJAX
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

    // ✅ Toggle Comments (hide/show)
    $(document).on("click", ".toggle-comments", function (e) {
        e.preventDefault();
        let postId = $(this).data("id");
        $(`#comments-section-${postId}`).slideToggle(); // ✅ Smooth show/hide
    });

    // ✅ Upvote
    $(document).on("click", ".upvote-btn", function (e) {
        e.preventDefault();
        let postId = $(this).data("id");

        $.post(`/posts/${postId}/upvote`, {}, function (res) {
            // Update counts
            $(`#upvote-count-${postId}`).text(res.upvotes_count);
            $(`#downvote-count-${postId}`).text(res.downvotes_count);

            // Highlight active button
            if (res.status === "upvoted") {
                $(`.upvote-btn[data-id="${postId}"] i`).css("color", "#28a745");
                $(`.downvote-btn[data-id="${postId}"] i`).css("color", "#000");
            } else {
                $(`.upvote-btn[data-id="${postId}"] i`).css("color", "#000");
            }
        });
    });

    // ✅ Downvote
    $(document).on("click", ".downvote-btn", function (e) {
        e.preventDefault();
        let postId = $(this).data("id");

        $.post(`/posts/${postId}/downvote`, {}, function (res) {
            // Update counts
            $(`#upvote-count-${postId}`).text(res.upvotes_count);
            $(`#downvote-count-${postId}`).text(res.downvotes_count);

            // Highlight active button
            if (res.status === "downvoted") {
                $(`.downvote-btn[data-id="${postId}"] i`).css("color", "#dc3545");
                $(`.upvote-btn[data-id="${postId}"] i`).css("color", "#000");
            } else {
                $(`.downvote-btn[data-id="${postId}"] i`).css("color", "#000");
            }
        });
    });

    // ✅ Comment/Reply send
    $(document).on("click", ".comment-send", function () {
        let btn = $(this);
        let postId = btn.data("id");
        let input = btn.closest(".input-group").find(".comment-input");
        let content = input.val();
        let parentId = input.data("parent") || null;

        if (content.trim() === "") return;

        $.post(
            `/posts/${postId}/comment`,
            { content: content, parent_id: parentId },
            function (res) {
                input.val("");
                input.removeAttr("data-parent");

                if (res.parent_id) {
                    // ✅ Append reply
                    $(`#comment-${res.parent_id} .replies`).append(
                        `<div class="reply mb-1" id="comment-${res.id}">
                            <strong>${res.user}:</strong> ${res.comment}
                        </div>`
                    );
                } else {
                    // ✅ Append top-level comment
                    btn.closest(".comments-section").find(".comments-list").append(
                        `<div class="comment mb-2" id="comment-${res.id}">
                            <strong>${res.user}:</strong> ${res.comment}
                            <a href="#" class="reply-btn ml-2 small text-primary" data-id="${res.id}">Reply</a>
                            <div class="replies ml-4 mt-1"></div>
                        </div>`
                    );
                }

                // ✅ Update comment count
                btn.closest(".widget")
                    .find(".comment-btn .numb")
                    .text(res.comments_count);
            }
        );
    });

    // ✅ Reply button → focuses input
    $(document).on("click", ".reply-btn", function (e) {
        e.preventDefault();
        let parentId = $(this).data("id");
        let input = $(this).closest(".widget").find(".comment-input");
        input.focus();
        input.attr("data-parent", parentId);
    });
});
