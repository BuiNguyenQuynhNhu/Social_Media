// =========================================
// POST INTERACTION AND COMMENT SYSTEM
// =========================================

// ----- MEDIA PREVIEW -----
function previewPostFiles(input) {
    const preview = document.getElementById('media-preview-container');
    if (!preview) return;

    preview.innerHTML = '';
    preview.style.display = 'none';

    if (input.files && input.files.length > 0) {
        preview.style.display = 'grid';
        Array.from(input.files).forEach(file => {
            const reader = new FileReader();
            reader.onload = function (e) {
                const div = document.createElement('div');
                div.className = 'preview-item';
                if (file.type.startsWith('video')) {
                    div.innerHTML = `<video src="${e.target.result}" controls></video>`;
                } else {
                    div.innerHTML = `<img src="${e.target.result}">`;
                }
                preview.appendChild(div);
            }
            reader.readAsDataURL(file);
        });
    }
}

// ----- POST REACTIONS -----
function sendReaction(postId, type) {
    if (!BASE_URL) {
        console.error('BASE_URL is not defined');
        return;
    }

    $.post(BASE_URL + '/interaction/react/' + postId, { type: type }, function (res) {
        if (res.status === 'success') {
            updateLikes(postId);
            updateReactionButton(postId, res.action, type);
        }
    }).fail(function () {
        console.error('Failed to send reaction');
    });
}

function updateReactionButton(postId, action, type) {
    const btnText = document.getElementById('reaction-btn-text-' + postId);
    const btn = document.getElementById('btn-react-' + postId);

    if (!btnText || !btn) return;

    if (action === 'unreacted') {
        btnText.innerText = 'Like';
        btn.classList.remove('active');
        btn.style.color = '';
    } else {
        const typeMap = {
            'like': 'Like', 'love': 'Love', 'haha': 'Haha',
            'wow': 'Wow', 'sad': 'Sad', 'angry': 'Angry'
        };
        const colorMap = {
            'like': '#1877f2', 'love': '#e41e3f', 'haha': '#f7b928',
            'wow': '#f7b928', 'sad': '#f7b928', 'angry': '#e41e3f'
        };
        btnText.innerText = typeMap[type] || 'Like';
        btn.classList.add('active');
        btn.style.color = colorMap[type] || '';
    }
}

function updateLikes(postId) {
    if (!BASE_URL) return;

    $.get(BASE_URL + '/interaction/get_likes/' + postId, function (res) {
        const count = res.total || 0;
        $('#likes-count-' + postId).text(count > 0 ? count : '');
    }).fail(function () {
        console.error('Failed to update likes for post ' + postId);
    });
}

// ----- COMMENTS -----
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
    if (!content) return;

    $.post(BASE_URL + '/comment/add/' + postId, { content: content }, function (res) {
        if (res.status === 'success') {
            input.value = '';
            loadComments(postId);
        } else {
            alert(res.message || 'Failed to add comment');
        }
    }).fail(function () {
        alert('Failed to add comment. Please try again.');
    });
}

function loadComments(postId) {
    if (!BASE_URL) return;

    $.get(BASE_URL + '/comment/get/' + postId, function (res) {
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
    const profileUrl = BASE_URL + '/user/profile/' + c.user.id;

    const html = `
        <div class="comment-wrapper" id="comment-${c.id}">
            <div class="comment" data-depth="${c.depth || 0}">
                <a href="${profileUrl}">
                    <img src="${c.user.avatar}" class="avatar-xs">
                </a>
                <div class="comment-content-wrapper">
                    <div class="comment-bubble">
                        <a href="${profileUrl}" class="comment-author">${c.user.name}</a>
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

// ----- REPLIES -----
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

    $.post(BASE_URL + '/comment/add/' + postId, {
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

// ----- COMMENT ACTIONS -----
function likeComment(commentId) {
    if (!BASE_URL) return;

    $.post(BASE_URL + '/comment/like/' + commentId, function (res) {
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

    $.ajax({
        url: BASE_URL + '/comment/delete/' + commentId,
        type: 'DELETE',
        success: function (res) {
            if (res.status === 'success') {
                loadComments(postId);
            } else {
                alert(res.message || 'Failed to delete');
            }
        },
        error: function () {
            alert('Failed to delete comment');
        }
    });
}

// ----- HELPER FUNCTIONS -----
function escapeHtml(text) {
    const map = {
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;'
    };
    return text.replace(/[&<>"']/g, m => map[m]);
}

function formatCommentTime(dateString) {
    const date = new Date(dateString);
    const now = new Date();
    const diffMs = now - date;
    const diffMins = Math.floor(diffMs / 60000);

    if (diffMins < 1) return 'Just now';
    if (diffMins < 60) return diffMins + 'm';

    const diffHours = Math.floor(diffMins / 60);
    if (diffHours < 24) return diffHours + 'h';

    const diffDays = Math.floor(diffHours / 24);
    if (diffDays < 7) return diffDays + 'd';

    return date.toLocaleDateString();
}

// ----- INITIALIZATION -----
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