$(document).ready(function () {
    // ✅ Setup CSRF token for all AJAX
    $.ajaxSetup({
        headers: { "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content") },
    });

    // ✅ Like button
    $(document).on("click", ".like-btn", function (e) {
        e.preventDefault();
        let btn = $(this);
        let postId = btn.data("id");

        $.post(`/posts/${postId}/like`, {}, function (res) {
            btn.find(".numb").text(res.likes_count);
            if (res.liked) {
                btn.find("i").css("color", "#CF0F47");
            } else {
                btn.find("i").css("color", "#FF0B55");
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

        $.post(`/posts/${postId}/comment`, { content: content, parent_id: parentId }, function (res) {
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
                btn.closest(".widget-footer").find(".comments-list").append(
                    `<div class="comment mb-2" id="comment-${res.id}">
                        <strong>${res.user}:</strong> ${res.comment}
                        <a href="#" class="reply-btn ml-2 small text-primary" data-id="${res.id}">Reply</a>
                        <div class="replies ml-4 mt-1"></div>
                    </div>`
                );
            }

            // ✅ Update comment count
            btn.closest(".widget").find(".comment-btn .numb").text(res.comments_count);
        });
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
