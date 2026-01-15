
function getCookie(name) {
    if (!name) return null;
    let match = document.cookie.match(new RegExp('(^| )' + name + '=([^;]+)'));
    if (match) return decodeURIComponent(match[2]);
    return null;
}

async function sendRequest(url, formData = null) {
    if (!formData) {
        formData = new FormData();
    }

    const COOKIE_NAME = 'csrf_cookie_name';

    let tokenValue = getCookie(COOKIE_NAME);

    // console.log("Token CSRF lấy được:", tokenValue);

    if (!tokenValue && window.APP_CONFIG) {
        tokenValue = window.APP_CONFIG.csrfHash;
    }

    if (window.APP_CONFIG) {
        formData.append(window.APP_CONFIG.csrfTokenName, tokenValue);
    }

    try {
        const response = await fetch(url, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': tokenValue
            },
            body: formData
        });

        if (response.status === 403) {
            alert("Security Error (CSRF). Please refresh the page.");
            // location.reload();
            return { status: 'error', message: 'CSRF Error' };
        }

        return await response.json();

    } catch (error) {
        console.error("Request Error:", error);
        return { status: 'error' };
    }
}


/* =========================================
   MODAL & POST IMAGE
   ========================================= */
function openPostModal() {
    const modal = document.getElementById("postModal");
    if (modal) modal.style.display = "flex";
}

function closePostModal() {
    const modal = document.getElementById("postModal");
    if (modal) modal.style.display = "none";
}

function previewImage(input) {
    const container = document.getElementById("image-preview-container");
    const preview = document.getElementById("image-preview");

    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function (e) {
            preview.src = e.target.result;
            container.style.display = "block";
        }
        reader.readAsDataURL(input.files[0]);
    }
}

function removeImage() {
    document.getElementById("post-image").value = "";
    document.getElementById("image-preview-container").style.display = "none";
    document.getElementById("image-preview").src = "#";
}

/* =========================================
   FRIEND ACTIONS (Should normally be in friend.js or similar, but used here)
   ========================================= */
function addFriend(id) {
    let data = new FormData();
    data.append('target_id', id);

    sendRequest(window.BASE_URL + '/friend/add', data).then(res => {
        if (res.status === 'success') location.reload();
    });
}

function acceptFriend(id) {
    let data = new FormData();
    data.append('target_id', id);
    sendRequest(window.BASE_URL + '/friend/accept', data).then(res => {
        if (res.status === 'success') location.reload();
    });
}

function removeFriend(id) {
    if (!confirm('Are you sure?')) return;
    let data = new FormData();
    data.append('target_id', id);
    sendRequest(window.BASE_URL + '/friend/remove', data).then(res => {
        if (res.status === 'success') location.reload();
    });
}


/* =========================================
   POST INTERACTION 
   ========================================= */
function sendReaction(postId, type) {
    let formData = new FormData();
    formData.append('post_id', postId);
    formData.append('reaction', type);

    // Use window.BASE_URL
    sendRequest(window.BASE_URL + '/post/react', formData).then(res => {
        if (res.status === 'added' || res.status === 'removed' || res.status === 'updated') {
            updateLikes(postId); // Update UI without reload
            // If My Reaction changes, reload or update button?
            // Ideally update UI directly. For now reload is simple.
            location.reload();
        }
    });
}

/* =========================================
   COMMENT ACTIONS (Aligned with comment_section.php)
   ========================================= */

function toggleComments(postId) {
    const box = document.getElementById('comments-' + postId);
    if (!box) return; // Fallback for old ID?

    if (box.style.display === 'none') {
        box.style.display = 'block';
        loadComments(postId);
    } else {
        box.style.display = 'none';
    }
}

// Alias for old calls if any
const toggleCommentBox = toggleComments;

function previewCommentMedia(input, postId) {
    const previewBox = document.getElementById('comment-preview-' + postId);
    if (input.files && input.files[0]) {
        const file = input.files[0];
        const reader = new FileReader();

        reader.onload = function (e) {
            previewBox.style.display = 'block';
            if (file.type.startsWith('image/')) {
                previewBox.innerHTML = `<img src="${e.target.result}" style="max-height:100px; border-radius:8px;">
                                        <button onclick="clearCommentMedia('${postId}')" style="position:absolute; margin-left:-20px; background:red; color:white; border:none; border-radius:50%; width:20px; height:20px; cursor:pointer;">&times;</button>`;
            } else {
                previewBox.innerHTML = `<div style="background:#eee; padding:5px; border-radius:4px;">${file.name}</div>`;
            }
        };
        reader.readAsDataURL(file);
    } else {
        previewBox.style.display = 'none';
    }
}

function clearCommentMedia(postId) {
    document.getElementById('comment-file-' + postId).value = '';
    document.getElementById('comment-preview-' + postId).style.display = 'none';
    document.getElementById('comment-preview-' + postId).innerHTML = '';
}

function addComment(inputElement, postId) {
    // If called from button, inputElement might be null or we find it by ID
    const input = document.getElementById('comment-input-' + postId);
    const content = input.value.trim();
    const fileInput = document.getElementById('comment-file-' + postId);
    const parentInput = document.getElementById('comment-parent-' + postId);

    if (!content && (!fileInput.files || fileInput.files.length === 0)) return;

    let formData = new FormData();
    formData.append('content', content);
    formData.append('post_id', postId); // For backend safety

    if (parentInput && parentInput.value) {
        formData.append('parent_id', parentInput.value);
    }

    if (fileInput.files.length > 0) {
        formData.append('media', fileInput.files[0]);
    }

    sendRequest(window.BASE_URL + '/comment/add/' + postId, formData).then(res => {
        if (res.status === 'success') {
            input.value = '';
            clearCommentMedia(postId);
            if (parentInput) parentInput.value = ''; // Reset parent
            input.placeholder = "Write a comment..."; // Reset placeholder

            // Append comment
            const list = document.querySelector(`#comments-${postId} .comments-list`);
            if (list) {
                appendCommentHTML(list, res.data);
                // Scroll to bottom?
            }

            // Update counts if visible
            const countSpan = document.getElementById('comments-count-' + postId); // matches post_item.php
            if (countSpan) {
                let current = parseInt(countSpan.innerText) || 0;
                countSpan.innerText = (current + 1) + ' Comments';
            }
        } else {
            alert(res.message || 'Error adding comment');
        }
    });
}

function replyToComment(commentId, authorName) {
    // Find the closest post container to get postId
    const row = document.getElementById('comment-row-' + commentId);
    if (!row) return;

    const section = row.closest('.comments-section');
    if (!section) return;

    const input = section.querySelector('input[type="text"]');
    const parentInput = section.querySelector('input[type="hidden"]'); // The one we added

    if (input && parentInput) {
        parentInput.value = commentId;
        input.placeholder = `Replying to ${authorName}...`;
        input.focus();
    }
}

function likeComment(commentId, btn) {
    sendRequest(window.BASE_URL + '/comment/like/' + commentId, {}, 'POST').then(res => {
        if (res.status === 'success') {
            const isLiked = (res.action === 'liked'); // Check backend response structure, assuming action or status
            // Toggle local style
            if (isLiked) {
                btn.style.color = '#1877f2';
                btn.style.fontWeight = 'bold';
            } else {
                btn.style.color = '#65676b';
            }

            const countSpan = document.getElementById('like-count-' + commentId);
            if (countSpan) {
                if (res.likes_count > 0 || res.count > 0) {
                    const cnt = res.likes_count || res.count;
                    countSpan.innerHTML = ` <i class="fa fa-thumbs-up" style="color:#1877f2"></i> ${cnt}`;
                } else {
                    countSpan.innerHTML = '';
                }
            }
        }
    });
}

function loadComments(postId) {
    const list = document.querySelector(`#comments-${postId} .comments-list`);
    if (!list) return;

    fetch(window.BASE_URL + '/comment/get/' + postId)
        .then(res => res.json())
        .then(res => {
            if (res.status === 'success') {
                list.innerHTML = '';
                res.data.forEach(comment => {
                    appendCommentHTML(list, comment);
                });
            }
        });
}

function appendCommentHTML(container, data) {
    const name = data.user.name || (data.firstname + ' ' + data.lastname);
    const avatar = data.user.avatar || (data.avatar ? window.BASE_URL + '/' + data.avatar : window.BASE_URL + '/ICON/avatar-default.svg');

    // Safely get media URL using helper
    let mediaEl = '';
    if (data.media) {
        // data.media might be object or string
        const urlRaw = data.media.url || data.media;
        const url = getImageUrl(urlRaw);

        if (data.media.type && data.media.type.includes('video')) {
            mediaEl = `<video src="${url}" controls style="max-width:200px; max-height:200px; margin-top:5px; border-radius:8px;"></video>`;
        } else {
            mediaEl = `<img src="${url}" style="max-width:200px; height:auto; margin-top:5px; border-radius:8px;">`;
        }
    } else if (data.media_url) {
        // Fallback for flat structure
        mediaEl = `<img src="${getImageUrl(data.media_url)}" style="max-width:200px; height:auto; margin-top:5px; border-radius:8px;">`;
    }

    // Format Date safely
    let timeString = 'Just now';
    if (data.created_at) {
        try {
            const date = new Date(data.created_at);
            const now = new Date();
            const diff = (now - date) / 1000; // seconds

            if (diff < 60) timeString = 'Just now';
            else if (diff < 3600) timeString = Math.floor(diff / 60) + 'm';
            else if (diff < 86400) timeString = Math.floor(diff / 3600) + 'h';
            else timeString = date.toLocaleDateString();
        } catch (e) { }
    }

    const isLiked = data.is_liked ? 'color:#1877f2; font-weight:bold;' : '';
    const likesCount = data.likes_count > 0 ? data.likes_count : '';
    const depth = data.depth || 0;

    const html = `
         <div class="comment" data-depth="${depth}" id="comment-row-${data.id}">
            <img src="${avatar}" class="avatar-xs">
            <div style="flex:1;">
                <div class="comment-bubble">
                    <a href="${window.BASE_URL}/user/profile/${data.user.id}" class="comment-author">${name}</a>
                    <p>${data.content}</p>
                    ${mediaEl}
                </div>
                
                <div class="comment-actions">
                     <span style="font-weight:700; ${isLiked}" onclick="likeComment('${data.id}', this)" class="action-btn">Like</span>
                    <span style="font-weight:700;" onclick="replyToComment('${data.id}', '${name}')" class="action-btn">Reply</span>
                    <span style="font-weight:normal;" class="time-text">${timeString}</span>
                    ${likesCount ? `<span id="like-count-${data.id}" style="display:flex; align-items:center; gap:2px; margin-left:auto;"> <i class="fa fa-thumbs-up" style="color:#1877f2"></i> ${likesCount}</span>` : `<span id="like-count-${data.id}" style="margin-left:auto;"></span>`}
                </div>
            </div>
        </div>
    `;
    container.insertAdjacentHTML('beforeend', html);
}

function submitCreatePost(event) {
    event.preventDefault();

    const form = document.getElementById('createPostForm');
    const formData = new FormData(form);

    sendRequest(window.BASE_URL + '/post/create', formData).then(res => {
        if (res.status === 'success') {
            location.reload();
        } else {
            alert(res.message || 'Error creating post');
            if (res.errors) console.log(res.errors);
        }
    });
}

/* =========================================
   SHOW REACTORS 
   ========================================= */
function showReactors(postId) {
    const modal = document.getElementById('reactorsModal');
    const list = document.getElementById('reactors-list');

    if (modal) modal.style.display = 'flex';
    if (list) list.innerHTML = '<div style="text-align:center; padding:20px;">Loading...</div>';

    fetch(window.BASE_URL + '/post/reactors/' + postId)
        .then(res => res.json())
        .then(res => {
            if (res.status === 'success') {
                list.innerHTML = '';
                if (res.data.length === 0) {
                    list.innerHTML = '<div style="text-align:center; padding:20px;">No reactions yet.</div>';
                    return;
                }
                const iconMap = { 'like': '👍', 'love': '❤️', 'haha': '😆', 'wow': '😮', 'sad': '😢', 'angry': '😡' };

                res.data.forEach(user => {
                    const name = user.firstname + ' ' + user.lastname;
                    const avatar = getImageUrl(user.avatar);
                    const icon = iconMap[user.reaction] || '👍';
                    const isSelf = (user.user_id == window.APP_CONFIG.userId);

                    const html = `
                    <div style="display: flex; align-items: center; justify-content: space-between; padding: 10px; border-radius: 8px; transition: 0.2s;" onmouseover="this.style.background='#f0f2f5'" onmouseout="this.style.background='white'">
                        <a href="${window.BASE_URL}/user/profile/${user.user_id}" style="display: flex; align-items: center; gap: 10px; text-decoration: none; color: black;">
                            <div style="position: relative;">
                                <img src="${avatar}" style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover;">
                                <div style="position: absolute; bottom: -2px; right: -2px; font-size: 14px; background: white; border-radius: 50%;">${icon}</div>
                            </div>
                            <span style="font-weight: 600; font-size: 15px;">${name}</span>
                        </a>
                        ${!isSelf ? `<button class="btn btn-secondary" onclick="addFriend('${user.user_id}')" style="padding: 6px 12px; font-size: 13px; border: none; background: #e4e6eb; border-radius: 6px; cursor: pointer; font-weight: 600;">Add Friend</button>` : ''}
                    </div>
                `;
                    list.insertAdjacentHTML('beforeend', html);
                });
            }
        });
}

/* =========================================
   EDIT POST & DELETE COMMENT & MENU
   ========================================= */

function togglePostMenu(postId) {
    document.querySelectorAll('.menu-dropdown').forEach(el => el.style.display = 'none');

    const menu = document.getElementById('menu-' + postId);
    if (menu) menu.style.display = (menu.style.display === 'block') ? 'none' : 'block';
}

function openEditPostModal(postId, content, privacy) {
    const modal = document.getElementById('editPostModal');
    const form = document.getElementById('editPostForm');

    document.getElementById('edit-content').value = content;
    document.getElementById('edit-privacy').value = privacy;

    form.setAttribute('data-post-id', postId);

    togglePostMenu(postId);
    modal.style.display = 'flex';
}

function submitEditPost(event) {
    event.preventDefault();

    const form = document.getElementById('editPostForm');
    const postId = form.getAttribute('data-post-id');

    if (!postId) {
        alert("Error: No post ID found.");
        return;
    }

    const formData = new FormData(form);

    sendRequest(window.BASE_URL + '/post/update/' + postId, formData).then(res => {
        if (res.status === 'success') {
            location.reload();
        } else {
            alert(res.message || 'Error updating post');
        }
    });
}


function getImageUrl(path) {
    if (!path) return window.BASE_URL + '/ICON/avatar-default.svg';
    if (path.startsWith('http')) return path;
    return window.BASE_URL + '/' + path;
}

function updateLikes(postId) {
    // Optional: Fetch new counts and update DOM
}

function generatePostHTML(post) {
    // This function is defined in dashboard or post.js. 
    // Implementing it here ensures consistency if called dynamically.
    // ... (Implementation same as previously seen or updated as needed)
    // For brevity, using the version from dashboard or similar.
    // Ensure window.BASE_URL and window.APP_CONFIG used.

    const avatar = getImageUrl(post.avatar);
    const media = post.media_url ? `<img src="${getImageUrl(post.media_url)}" style="width:100%; margin-top:10px; border-radius:8px;">` : '';
    const isOwner = (post.user_id == window.APP_CONFIG.userId);

    let privacyIcon = '<i data-lucide="lock" width="12"></i>';
    if (post.privacy === 'public') privacyIcon = '<i data-lucide="globe" width="12"></i>';
    else if (post.privacy === 'friends') privacyIcon = '<i data-lucide="users" width="12"></i>';

    let menuHtml = '';
    let ownerActions = '';

    if (isOwner) {
        menuHtml = `
        <div class="post-menu" style="position: relative;">
            <button onclick="togglePostMenu(${post.id})" style="background: none; border: none; cursor: pointer; padding: 5px; color: #65676b;"><i data-lucide="more-horizontal"></i></button>
            <div id="menu-${post.id}" class="menu-dropdown" style="display: none; position: absolute; right: 0; top: 30px; background: white; box-shadow: 0 2px 8px rgba(0,0,0,0.2); border-radius: 8px; width: 150px; z-index: 100;">
                <a href="javascript:void(0)" onclick="openEditPostModal(${post.id}, '${post.content.replace(/'/g, "\\'")}', '${post.privacy}')" style="display: flex; align-items: center; gap: 10px; padding: 10px;">
                    <i data-lucide="edit-2" width="16"></i> Edit Post
                </a>
            </div>
        </div>`;

        ownerActions = `
            <a href="${window.BASE_URL}/post/delete/${post.id}" onclick="return confirm('Do you really want to delete this post?')" 
               style="background:none; border:none; cursor:pointer; color:#65676b; padding:5px; display:flex; align-items:center;" title="Delete Post">
                <i data-lucide="trash-2" width="18"></i>
            </a>
            <div style="width:1px; height:20px; background:#ddd; margin:0 5px;"></div>
        `;
    }

    const rightSide = `
        <div style="display:flex; align-items:center;">
            ${ownerActions}
            ${menuHtml}
        </div>
    `;

    const myReact = post.my_reaction;
    const btnClass = myReact ? 'btn-reacted-' + myReact : '';
    const btnText = myReact ? myReact.charAt(0).toUpperCase() + myReact.slice(1) : 'Like';

    const iconMap = {
        'love': 'heart', 'haha': 'smile', 'wow': 'alert-circle', 'sad': 'frown', 'angry': 'frown'
    };
    const btnIconName = iconMap[myReact] || 'thumbs-up';

    return `
    <div class="fb-card post-card" id="post-${post.id}" data-id="${post.id}">
        <div class="post-header" style="display: flex; justify-content: space-between; align-items: flex-start;">
            <div style="display: flex; gap: 10px;">
                <img src="${avatar}" class="small-avatar">
                <div class="post-info">
                    <span class="author">${post.firstname} ${post.lastname}</span>
                    <span class="time">${post.time_time ?? ''} ${privacyIcon}</span>
                </div>
            </div>
            ${rightSide}
        </div>

        <div class="post-content">
            <p>${post.content}</p>
            ${media}
        </div>

        <div class="post-stats">
            <div class="stats-likes" style="cursor: pointer;" onclick="showReactors(${post.id})">
                ${post.like_count > 0 ? `<span style="color: #65676b;">${post.like_count} likes</span>` : ''}
            </div>
            <div class="stats-comments" onclick="toggleCommentBox(${post.id})" style="cursor: pointer;">
                <span id="comment-count-${post.id}">${post.comment_count} comments</span>
            </div>
        </div>

        <div class="post-actions" style="display: flex;">
            <div class="reaction-wrapper">
                <div class="reaction-popup">
                    <span class="reaction-icon" onclick="sendReaction(${post.id}, 'like')">👍</span>
                    <span class="reaction-icon" onclick="sendReaction(${post.id}, 'love')">❤️</span>
                    <span class="reaction-icon" onclick="sendReaction(${post.id}, 'haha')">😆</span>
                    <span class="reaction-icon" onclick="sendReaction(${post.id}, 'wow')">😮</span>
                    <span class="reaction-icon" onclick="sendReaction(${post.id}, 'sad')">😢</span>
                    <span class="reaction-icon" onclick="sendReaction(${post.id}, 'angry')">😡</span>
                </div>
                <button class="action-btn ${btnClass}" onclick="sendReaction(${post.id}, '${myReact || 'like'}')" 
                        style="width: 100%; display: flex; justify-content: center; gap: 5px;">
                    <i data-lucide="${btnIconName}"></i> <span>${btnText}</span>
                </button>
            </div>

            <button class="action-btn" onclick="toggleCommentBox(${post.id})" style="flex:1; display:flex; justify-content:center; gap:5px;">
                <i data-lucide="message-circle"></i> Comment
            </button>
            <button class="action-btn" style="flex:1; display:flex; justify-content:center; gap:5px;">
                <i data-lucide="share-2"></i> Share
            </button>
        </div>

        <div id="comment-section-${post.id}" style="display:none; background-color: #f0f2f5; padding: 10px; border-radius: 0 0 8px 8px;">
            <div id="comment-list-${post.id}" style="display: flex; flex-direction: column; gap: 10px; margin-bottom: 10px;"></div>
            
            <div style="display:flex; gap:10px;">
                <img src="${getImageUrl(window.APP_CONFIG.myAvatar, 'avatar')}" style="width:32px; height:32px; border-radius:50%; object-fit:cover;"> 
                <div style="flex:1; position:relative;">
                    <input type="text" id="input-comment-${post.id}" placeholder="Write a comment..." 
                           onkeypress="handleEnter(event, ${post.id})" 
                           style="width:100%; padding:8px 35px 8px 15px; border-radius:20px; border:1px solid #ddd; outline: none;">
                    <button onclick="sendComment(${post.id})" style="position:absolute; right:10px; top:50%; transform: translateY(-50%); border:none; background:none; cursor:pointer; color:#1877f2;">
                        <i data-lucide="send" width="16"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
    `;
}

$(document).ready(function () {
    // Load initial likes and comments for all visible posts?
    // Actually typically handled when clicking?
    // Kept blank or logic as needed
});
