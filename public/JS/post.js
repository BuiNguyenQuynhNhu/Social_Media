// =========================================
// POST MAIN MODULE
// Coordinates all post-related functionality
// Dependencies: reactions.js, comments.js, utils.js
// =========================================

// Initialize on page load
$(document).ready(function () {
    // Load initial likes and comments for all visible posts
    $('.post-card').each(function () {
        const postId = $(this).data('id');
        if (postId) {
            updateLikes(postId);
            loadComments(postId);
        }
    });

    console.log('Post interaction system initialized');
});