// =========================================
// COMMENTS MODULE
// Handles comment display, loading, and rendering
// =========================================

function toggleComments(postId) {
    const section = $('#comments-' + postId);
    if (section.is(':visible')) {
        section.slideUp(200);
    } else {
        section.slideDown(200);
        loadComments(postId);
    }
}

function addComment(input, postId) {
    const content = input.value.trim();
    const fileInput = document.getElementById('comment-file-' + postId);
    const file = fileInput ? fileInput.files[0] : null;

    if (!content && !file) return;

    const formData = new FormData();
    formData.append('content', content);
    if (file) {
        formData.append('media', file);
    }

    $.ajax({
        url: window.BASE_URL + '/comment/add/' + postId,
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function (res) {
            if (res.status === 'success') {
                input.value = '';
                if (fileInput) fileInput.value = ''; // Clear file input
                clearCommentMedia(postId); // Clear preview
                loadComments(postId);
            } else {
                alert(res.message || 'Failed to add comment');
            }
        },
        error: function () {
            alert('Failed to add comment. Please try again.');
        }
    });
}

function loadComments(postId) {
    if (!window.BASE_URL) return;

    $.get(window.BASE_URL + '/comment/get/' + postId, function (res) {
        const container = $('#comments-' + postId + ' .comments-list');
        container.empty();

        if (res.status === 'success' && res.data && res.data.length > 0) {
            $('#comments-count-' + postId).text(res.data.length + ' Comment' + (res.data.length !== 1 ? 's' : ''));

            // Build comment tree
            const map = {};
            const roots = [];

            res.data.forEach(c => {
                c.children = [];
                map[c.id] = c;
            });

            res.data.forEach(c => {
                if (c.parent_id && map[c.parent_id]) {
                    map[c.parent_id].children.push(c);
                } else {
                    roots.push(c);
                }
            });

            roots.forEach(c => renderComment(c, container));
            lucide.createIcons();
        } else {
            $('#comments-count-' + postId).text('0 Comments');
        }
    }).fail(function () {
        console.error('Failed to load comments for post ' + postId);
    });
}

function renderComment(c, container) {
    const timeText = c.created_at ? formatCommentTime(c.created_at) : 'Just now';
    const likeActive = c.is_liked ? 'style="color:#1877f2; font-weight:600;"' : '';
    const likeText = c.is_liked ? 'Liked' : 'Like';
    const deleteBtn = c.can_delete ? `<span onclick="deleteComment('${c.id}', '${c.post_id}')">Delete</span>` : '';
    const profileUrl = window.BASE_URL + '/user/profile/' + c.user.id;

    const html = `
        <div class="comment-wrapper" id="comment-${c.id}">
            <div class="comment" data-depth="${c.depth || 0}">
                <a href="${profileUrl}">
                    <img src="${c.user.avatar}" class="avatar-xs">
                </a>
                <div class="comment-content-wrapper">
                    <div class="comment-bubble">
                        <a href="${profileUrl}" class="comment-author">${c.user.name}</a>
                        ${c.media ? `<div class="comment-media-container" style="margin-top:4px;">
                            ${c.media.type === 'video'
                ? `<video src="${c.media.url}" controls></video>`
                : `<img src="${c.media.url}" alt="Comment Media">`}
                        </div>` : ''}
                        <p>${escapeHtml(c.content)}</p>
                    </div>
                    <div class="comment-actions">
                        <span>${timeText}</span>
                        <span onclick="likeComment('${c.id}')" ${likeActive} id="like-btn-${c.id}">
                            ${likeText} ${c.likes_count > 0 ? '(' + c.likes_count + ')' : ''}
                        </span>
                        <span onclick="toggleReply('${c.id}')">Reply</span>
                        ${deleteBtn}
                    </div>
                    <div class="reply-box" id="reply-box-${c.id}">
                        <div class="comment-input-wrapper">
                            <input type="text" id="reply-input-${c.id}" placeholder="Write a reply..." 
                                   onkeydown="if(event.key==='Enter') submitReply('${c.post_id}', '${c.id}')">
                            <button onclick="submitReply('${c.post_id}', '${c.id}')" 
                                    style="color:#1877f2; border:none; background:none; cursor:pointer; padding:6px; border-radius:50%;">
                                <i data-lucide="send" style="width:16px; height:16px;"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="replies-container"></div>
        </div>
    `;

    const el = $(html);
    container.append(el);

    // Render children
    if (c.children && c.children.length > 0) {
        const repliesContainer = el.find('.replies-container');
        c.children.forEach(child => renderComment(child, repliesContainer));
    }
}

function toggleReply(commentId) {
    const replyBox = $('#reply-box-' + commentId);
    replyBox.slideToggle(200);
    $('#reply-input-' + commentId).focus();
}

function submitReply(postId, parentId) {
    const input = $('#reply-input-' + parentId);
    const content = input.val().trim();

    if (!content) {
        input.focus();
        return;
    }

    $.post(window.BASE_URL + '/comment/add/' + postId, {
        content: content,
        parent_id: parentId
    }, function (res) {
        if (res.status === 'success') {
            input.val('');
            loadComments(postId);
        } else {
            alert(res.message || 'Failed to add reply');
        }
    }).fail(function () {
        alert('Failed to add reply. Please try again.');
    });
}

function likeComment(commentId) {
    if (!window.BASE_URL) return;

    $.post(window.BASE_URL + '/comment/like/' + commentId, function (res) {
        if (res.status === 'success') {
            const btn = $('#like-btn-' + commentId);
            if (res.action === 'liked') {
                btn.css({ 'color': '#1877f2', 'font-weight': '600' });
                btn.html('Liked ' + (res.count > 0 ? '(' + res.count + ')' : ''));
            } else {
                btn.css({ 'color': '', 'font-weight': '' });
                btn.html('Like ' + (res.count > 0 ? '(' + res.count + ')' : ''));
            }
        }
    }).fail(function () {
        console.error('Failed to like comment');
    });
}

function deleteComment(commentId, postId) {
    if (!confirm('Delete this comment?')) return;

    // Use jQuery AJAX which has global CSRF setup from init.js
    $.ajax({
        url: window.BASE_URL + '/comment/delete/' + commentId,
        type: 'DELETE',
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        },
        success: function (res) {
            if (res.status === 'success') {
                loadComments(postId);
            } else {
                alert(res.message || 'Failed to delete');
            }
        },
        error: function (xhr) {
            console.error('Delete error:', xhr);
            alert('Failed to delete comment. Status: ' + xhr.status);
        }
    });
}

function previewCommentMedia(input, postId) {
    const previewContainer = $(`#comment-preview-${postId}`);

    if (input.files && input.files[0]) {
        const file = input.files[0];
        const reader = new FileReader();

        reader.onload = function (e) {
            let html = '';
            if (file.type.startsWith('video')) {
                html = `<div style="position:relative; display:inline-block;">
                            <video src="${e.target.result}" style="max-height:150px; border-radius:8px; display:block;" controls></video>
                            <button onclick="clearCommentMedia('${postId}')" style="position:absolute; top:-8px; right:-8px; background:#fff; color:#050505; border:1px solid #ddd; border-radius:50%; width:24px; height:24px; cursor:pointer; display:flex; align-items:center; justify-content:center; box-shadow:0 1px 2px rgba(0,0,0,0.1);">&times;</button>
                        </div>`;
            } else {
                html = `<div style="position:relative; display:inline-block;">
                            <img src="${e.target.result}" style="max-height:150px; border-radius:8px; display:block;" alt="Preview">
                            <button onclick="clearCommentMedia('${postId}')" style="position:absolute; top:-8px; right:-8px; background:#fff; color:#050505; border:1px solid #ddd; border-radius:50%; width:24px; height:24px; cursor:pointer; display:flex; align-items:center; justify-content:center; box-shadow:0 1px 2px rgba(0,0,0,0.1);">&times;</button>
                        </div>`;
            }
            previewContainer.html(html).slideDown(200);
        };

        reader.onerror = function () {
            console.error('Error reading file for preview');
        };

        reader.readAsDataURL(file);
    } else {
        previewContainer.hide().empty();
    }
}

function clearCommentMedia(postId) {
    const input = document.getElementById(`comment-file-${postId}`);
    if (input) input.value = '';
    $(`#comment-preview-${postId}`).slideUp(200, function () {
        $(this).empty();
    });
}
