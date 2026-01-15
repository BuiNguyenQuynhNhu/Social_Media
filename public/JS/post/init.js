// Initialize Post Data
document.addEventListener('DOMContentLoaded', function () {
    const posts = document.querySelectorAll('.post-card');
    posts.forEach(post => {
        const postId = post.getAttribute('data-id');
        if (postId) {
            // Auto-load likes and reaction status
            if (typeof updateLikes === 'function') {
                updateLikes(postId);
            }
            // Auto-load comments count (and potentially comments if we wanted, but count is sufficient)
            // loadComments fetches comments and updates count text.
            // If we just want count, we might need a specific/lighter API, but loadComments works.
            if (typeof loadComments === 'function') {
                loadComments(postId);
            }
        }
    });
});
