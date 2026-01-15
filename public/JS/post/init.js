// Initialize Post Data
document.addEventListener('DOMContentLoaded', function () {
    // Global CSRF Setup for jQuery
    // We use beforeSend to fetch the latest token from the cookie immediately before the request
    if (window.jQuery && window.APP_CONFIG) {
        $.ajaxSetup({
            beforeSend: function (xhr, settings) {
                if (!/^(GET|HEAD|OPTIONS|TRACE)$/i.test(settings.type) && !this.crossDomain) {
                    // Try to get token from cookie (most up-to-date), fallback to config
                    // Standard CI4 cookie name is 'csrf_cookie_name', or we check what might be in usage
                    const token = getCookie('csrf_cookie_name') || window.APP_CONFIG.csrfHash;
                    xhr.setRequestHeader('X-CSRF-TOKEN', token);
                }
            }
        });
    }

    const posts = document.querySelectorAll('.post-card');
    posts.forEach(post => {
        const postId = post.getAttribute('data-id');
        if (postId) {
            // Auto-load likes and reaction status
            if (typeof updateLikes === 'function') {
                updateLikes(postId);
            }
            // Auto-load comments count (and potentially comments)
            if (typeof loadComments === 'function') {
                loadComments(postId);
            }
        }
    });
});
