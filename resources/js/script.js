$(document).ready(function() {
    // JavaScript functionality for like and comment buttons
    $('.like-btn').on('click', function() {
        var postId = $(this).data('id');
        $.ajax({
            type: 'POST',
            url: '/like',
            data: {id: postId},
            success: function(data) {
                console.log(data);
            }
        });
    });

    $('.comment-btn').on('click', function() {
        var postId = $(this).data('id');
        $.ajax({
            type: 'POST',
            url: '/comment',
            data: {id: postId},
            success: function(data) {
                console.log(data);
            }
        });
    });
});