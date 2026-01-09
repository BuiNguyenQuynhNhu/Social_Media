/* =========================================
   CORE: CSRF & REQUEST HANDLING
   ========================================= */

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

    console.log("Token CSRF lấy được:", tokenValue);

    if (!tokenValue) {

        tokenValue = APP_CONFIG.csrfHash;
    }

    formData.append(APP_CONFIG.csrfTokenName, tokenValue);

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
            alert("Lỗi bảo mật CSRF! Vui lòng F5 lại trang và thử lại.");
            location.reload();
            return { status: 'error' };
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
    removeImage();
}

function previewImage(input) {
    const preview = document.getElementById('image-preview');
    const container = document.getElementById('image-preview-container');
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.src = e.target.result;
            container.style.display = 'block';
        }
        reader.readAsDataURL(input.files[0]);
    }
}

function removeImage() {
    const input = document.getElementById('post-image');
    const container = document.getElementById('image-preview-container');
    const preview = document.getElementById('image-preview');
    if (input) input.value = "";
    if (container) container.style.display = 'none';
    if (preview) preview.src = "#";
}

window.onclick = function(event) {
    const editModal = document.getElementById('edit-modal');
    const postModal = document.getElementById('postModal');
    const reactorModal = document.getElementById('reactorsModal');
    if (event.target == editModal) editModal.style.display = "none";
    if (event.target == postModal) closePostModal();
    if (event.target == reactorModal) reactorModal.style.display = "none";
}


/* =========================================
   FRIENDSHIP 
   ========================================= */
function addFriend(targetId) {
    let data = new FormData();
    data.append('receiver_id', targetId);

    sendRequest(APP_CONFIG.baseURL + '/friend/add', data).then(res => {
        if(res.status === 'success') location.reload();
        else alert(res.message);
    });
}

function acceptFriend(requesterId) {
    let data = new FormData();
    data.append('requester_id', requesterId);

    sendRequest(APP_CONFIG.baseURL + '/friend/accept', data).then(res => {
        if(res.status === 'success') location.reload();
    });
}

function unfriendUser(targetId) {
    if(!confirm("Are you sure you want to unfriend?")) return;
    cancelRequest(targetId);
}

function cancelRequest(targetId) {
    let data = new FormData();
    data.append('target_id', targetId);

    sendRequest(APP_CONFIG.baseURL + '/friend/remove', data).then(res => {
        if(res.status === 'success') location.reload();
    });
}


/* =========================================
   POST INTERACTION 
   ========================================= */
function sendReaction(postId, type) {
    let formData = new FormData();
    formData.append('post_id', postId);
    formData.append('reaction', type);

    sendRequest(APP_CONFIG.baseURL + '/post/react', formData).then(res => {
        if(res.status === 'added' || res.status === 'removed' || res.status === 'updated') {
            location.reload(); 
        }
    });
}

function toggleCommentBox(postId) {
    const box = document.getElementById('comment-section-' + postId);
    if (box.style.display === 'none') {
        box.style.display = 'block';
        loadComments(postId);
    } else {
        box.style.display = 'none';
    }
}

function loadComments(postId) {
    fetch(APP_CONFIG.baseURL + '/post/comments/' + postId)
    .then(res => res.json())
    .then(res => {
        if(res.status === 'success') {
            const list = document.getElementById('comment-list-' + postId);
            list.innerHTML = '';
            res.data.forEach(cmt => appendCommentHTML(list, cmt));
        }
    });
}

function sendComment(postId) {
    const input = document.getElementById('input-comment-' + postId);
    const content = input.value.trim();
    if(!content) return;

    let formData = new FormData();
    formData.append('post_id', postId);
    formData.append('content', content);

    sendRequest(APP_CONFIG.baseURL + '/post/comment/add', formData).then(res => {
        if(res.status === 'success') {
            input.value = '';

            const list = document.getElementById('comment-list-' + postId);
            appendCommentHTML(list, res.comment);

            const countSpan = document.getElementById('comment-count-' + postId);
            if (countSpan) {
                let currentCount = parseInt(countSpan.innerText) || 0;
                countSpan.innerText = (currentCount + 1) + ' comments';
            }
        }
    });
}

function handleEnter(e, postId) {
    if(e.key === 'Enter') sendComment(postId);
}

function appendCommentHTML(container, data) {
    const name = data.firstname + ' ' + data.lastname;
    const avatar = data.avatar ? APP_CONFIG.baseURL + '/' + data.avatar : APP_CONFIG.baseURL + '/ICON/avatar-default.svg';
    const html = `
        <div style="display:flex; gap:10px; align-items:flex-start;">
            <img src="${avatar}" style="width:32px; height:32px; border-radius:50%; object-fit: cover;">
            <div style="background:white; padding:8px 12px; border-radius:15px;">
                <div style="font-weight:bold; font-size:13px;">${name}</div>
                <div style="font-size:14px;">${data.content}</div>
            </div>
        </div>
    `;
    container.insertAdjacentHTML('beforeend', html);
}
function submitCreatePost(event) {
    event.preventDefault();

    const form = document.getElementById('createPostForm');
    const formData = new FormData(form);


    sendRequest(APP_CONFIG.baseURL + '/post/create', formData).then(res => {
        if(res.status === 'success') {
            location.reload(); 
        } else {
            alert(res.message || 'Error creating post');
            if(res.errors) console.log(res.errors);
        }
    });
}

/* =========================================
   SHOW REACTORS 
   ========================================= */
function showReactors(postId) {
    const modal = document.getElementById('reactorsModal');
    const list = document.getElementById('reactors-list');
    
    if(modal) modal.style.display = 'flex';
    if(list) list.innerHTML = '<div style="text-align:center; padding:20px;">Loading...</div>';

    fetch(APP_CONFIG.baseURL + '/post/reactors/' + postId)
    .then(res => res.json())
    .then(res => {
        if(res.status === 'success') {
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
                const isSelf = (user.user_id == APP_CONFIG.userId);

                const html = `
                    <div style="display: flex; align-items: center; justify-content: space-between; padding: 10px; border-radius: 8px; transition: 0.2s;" onmouseover="this.style.background='#f0f2f5'" onmouseout="this.style.background='white'">
                        <a href="${APP_CONFIG.baseURL}/user/profile/${user.user_id}" style="display: flex; align-items: center; gap: 10px; text-decoration: none; color: black;">
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

function deleteComment(commentId) {
    if(!confirm('Delete this comment?')) return;
    
    let formData = new FormData();
    formData.append('comment_id', commentId);
    
    sendRequest(APP_CONFIG.baseURL + '/post/comment/delete', formData).then(res => {
        if(res.status === 'success') {
            const el = document.getElementById('comment-item-' + commentId);
            if(el) el.remove();

        } else {
            alert('Error: ' + (res.message || 'Cannot delete'));
        }
    });
}

window.addEventListener('click', function(e) {
    if (!e.target.closest('.post-menu')) {
        document.querySelectorAll('.menu-dropdown').forEach(el => el.style.display = 'none');
    }
});

function timeAgo(dateString) {
    if (!dateString) return 'Just now';

    let t = dateString.split(/[- :]/);

    let date = new Date(t[0], t[1]-1, t[2], t[3], t[4], t[5]);
    
    let now = new Date();
    let seconds = Math.floor((now - date) / 1000);

    if (seconds < 0) return "Just now";

    let interval = seconds / 31536000;
    if (interval > 1) return Math.floor(interval) + " years ago";
    
    interval = seconds / 2592000;
    if (interval > 1) return Math.floor(interval) + " months ago";
    
    interval = seconds / 86400;
    if (interval > 1) return Math.floor(interval) + " days ago";
    
    interval = seconds / 3600;
    if (interval > 1) return Math.floor(interval) + " hours ago";
    
    interval = seconds / 60;
    if (interval > 1) return Math.floor(interval) + " minutes ago";
    
    return "Just now";
}

function appendCommentHTML(container, data) {
    const name = data.firstname + ' ' + data.lastname;
    const avatar = getImageUrl(data.avatar);

    const isOwner = (data.user_id == APP_CONFIG.userId);
    const timeDisplay = timeAgo(data.created_at);

    let actionBtns = '';
    if (isOwner) {
        const safeContent = data.content.replace(/'/g, "\\'");
        
        actionBtns = `
            <span id="btn-edit-cmt-${data.id}" onclick="enableEditComment(${data.id}, '${safeContent}')" 
                  style="font-size:11px; color:#65676b; cursor:pointer; margin-left:8px; font-weight:600;">Edit</span>
            
            <span onclick="deleteComment(${data.id})" 
                  style="font-size:11px; color:#dc3545; cursor:pointer; margin-left:8px; font-weight:600;">Delete</span>
        `;
    }

    const html = `
        <div id="comment-item-${data.id}" style="display:flex; gap:10px; align-items:flex-start; margin-bottom: 10px;">
            <img src="${avatar}" style="width:32px; height:32px; border-radius:50%; object-fit: cover;">
            <div style="flex:1;">
                <div style="background:white; padding:8px 12px; border-radius:15px; display:inline-block;">
                    <div style="font-weight:bold; font-size:13px;">${name}</div>
                    
                    <div id="comment-content-${data.id}" style="font-size:14px;">${data.content}</div>
                </div>
                <div style="margin-left:5px; margin-top:2px;">
                    <span style="font-size:11px; color:#65676b;">${timeDisplay}</span>
                    ${actionBtns}
                </div>
            </div>
        </div>
    `;
    container.insertAdjacentHTML('beforeend', html);
}

function previewAvatar(input) {
        const preview = document.getElementById('edit-avatar-preview');
        
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            
            reader.onload = function(e) {
                preview.src = e.target.result;
            }

            reader.readAsDataURL(input.files[0]);
        }
    }

function enableEditComment(commentId, oldContent) {
    const contentDiv = document.getElementById(`comment-content-${commentId}`);

    if (contentDiv.querySelector('input')) return;

    contentDiv.setAttribute('data-original', oldContent);

    contentDiv.innerHTML = `
        <div style="display:flex; gap:5px; margin-top:5px;">
            <input type="text" id="input-edit-${commentId}" value="${oldContent}" 
                   style="width:100%; padding:5px; border-radius:5px; border:1px solid #ddd;"
                   onkeydown="if(event.key==='Enter') submitEditComment(${commentId})">
            <button onclick="submitEditComment(${commentId})" class="btn-primary" style="font-size:11px; padding:0 8px;">Save</button>
            <button onclick="cancelEditComment(${commentId})" class="btn-secondary" style="font-size:11px; padding:0 8px;">Cancel</button>
        </div>
    `;

    document.getElementById(`input-edit-${commentId}`).focus();
}

function cancelEditComment(commentId) {
    const contentDiv = document.getElementById(`comment-content-${commentId}`);
    const original = contentDiv.getAttribute('data-original');
    contentDiv.innerHTML = original;
}

function submitEditComment(commentId) {
    const input = document.getElementById(`input-edit-${commentId}`);
    const newContent = input.value.trim();
    
    if (!newContent) {
        alert("Content cannot be empty");
        return;
    }

    let formData = new FormData();
    formData.append('comment_id', commentId);
    formData.append('content', newContent);

    sendRequest(APP_CONFIG.baseURL + '/post/comment/update', formData).then(res => {
        if (res.status === 'success') {
            const contentDiv = document.getElementById(`comment-content-${commentId}`);
            contentDiv.innerHTML = newContent;

            const editBtn = document.getElementById(`btn-edit-cmt-${commentId}`);
            if(editBtn) {

                editBtn.setAttribute('onclick', `enableEditComment(${commentId}, '${newContent.replace(/'/g, "\\'")}')`);
            }
        } else {
            alert(res.message || 'Error updating comment');
        }
    });
}

function loadPage(page, renderPosts = true) {
    const targetId = APP_CONFIG.targetId;

    fetch(`${APP_CONFIG.baseURL}/post/get_posts_paging?user_id=${targetId}&page=${page}`)
    .then(res => res.json())
    .then(res => {
        if(res.status === 'success') {

            if (renderPosts) {
                const container = document.getElementById('feed-container');
                container.innerHTML = ''; 
                
                if (res.data.length === 0) {
                    container.innerHTML = '<div class="fb-card" style="padding:20px;text-align:center">No posts found.</div>';
                } else {
                    res.data.forEach(post => {
                        const html = generatePostHTML(post);
                        container.insertAdjacentHTML('beforeend', html);
                    });
                    lucide.createIcons();
                }

                document.querySelector('.profile-body').scrollIntoView({ behavior: 'smooth' });
            }

            renderPaginationControls(res.currentPage, res.totalPages);
        }
    })
    .catch(err => console.error(err));
}

function renderPaginationControls(current, total) {
    const container = document.getElementById('pagination-controls');
    if (!container) return;
    
    container.innerHTML = '';

    if (total <= 1) return; 

    // Nút Previous (<)
    if (current > 1) {
        container.innerHTML += `<button onclick="loadPage(${current - 1})" class="btn-page"><i data-lucide="chevron-left" width="20"></i></button>`;
    }

    // Các nút số
    for (let i = 1; i <= total; i++) {
        if (i === 1 || i === total || (i >= current - 1 && i <= current + 1)) {
            const activeClass = (i === current) ? 'active' : '';
            container.innerHTML += `<button onclick="loadPage(${i})" class="btn-page ${activeClass}">${i}</button>`;
        } else if (i === current - 2 || i === current + 2) {
            container.innerHTML += `<span class="pagination-ellipsis">...</span>`;
        }
    }

    // Nút Next (>)
    if (current < total) {
        container.innerHTML += `<button onclick="loadPage(${current + 1})" class="btn-page"><i data-lucide="chevron-right" width="20"></i></button>`;
    }
    
    lucide.createIcons();
}

function getImageUrl(path) {
    if (!path) return APP_CONFIG.baseURL + '/ICON/avatar-default.svg'; 
    if (path.startsWith('http')) return path; 
    return APP_CONFIG.baseURL + '/' + path; 
}

function generatePostHTML(post) {
    const avatar = getImageUrl(post.avatar);
    const media = post.media_url ? `<img src="${getImageUrl(post.media_url)}" style="width:100%; margin-top:10px; border-radius:8px;">` : '';
    const isOwner = (post.user_id == APP_CONFIG.userId);

    let privacyIcon = '<i data-lucide="lock" width="12"></i>';
    if(post.privacy === 'public') privacyIcon = '<i data-lucide="globe" width="12"></i>';
    else if(post.privacy === 'friends') privacyIcon = '<i data-lucide="users" width="12"></i>';

    let menuHtml = '';
    if(isOwner) {
        menuHtml = `
        <div class="post-menu" style="position: relative;">
            <button onclick="togglePostMenu(${post.id})" style="background: none; border: none; cursor: pointer; padding: 5px; color: #65676b;"><i data-lucide="more-horizontal"></i></button>
            <div id="menu-${post.id}" class="menu-dropdown" style="display: none; position: absolute; right: 0; top: 30px; background: white; box-shadow: 0 2px 8px rgba(0,0,0,0.2); border-radius: 8px; width: 150px; z-index: 100;">
                <a href="javascript:void(0)" onclick="openEditPostModal(${post.id}, '${post.content.replace(/'/g, "\\'")}', '${post.privacy}')" style="display: flex; align-items: center; gap: 10px; padding: 10px;">
                    <i data-lucide="edit-2" width="16"></i> Edit Post
                </a>
                <a href="${APP_CONFIG.baseURL}/post/delete/${post.id}" onclick="return confirm('Delete?')" style="display: flex; align-items: center; gap: 10px; padding: 10px; color: red;">
                    <i data-lucide="trash-2" width="16"></i> Delete Post
                </a>
            </div>
        </div>`;
    }

    const myReact = post.my_reaction;
    const btnClass = myReact ? 'btn-reacted-' + myReact : '';
    const btnText = myReact ? myReact.charAt(0).toUpperCase() + myReact.slice(1) : 'Like';

    const iconMap = {
        'love': 'heart', 'haha': 'smile', 'wow': 'alert-circle', 'sad': 'frown', 'angry': 'frown'
    };
    const btnIconName = iconMap[myReact] || 'thumbs-up';

    return `
    <div class="fb-card post-card" id="post-${post.id}">
        <div class="post-header" style="display: flex; justify-content: space-between; align-items: flex-start;">
            <div style="display: flex; gap: 10px;">
                <img src="${avatar}" class="small-avatar">
                <div class="post-info">
                    <span class="author">${post.firstname} ${post.lastname}</span>
                    <span class="time">${timeAgo(post.created_at)} ${privacyIcon}</span>
                </div>
            </div>
            ${menuHtml}
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
                <img src="${getImageUrl(APP_CONFIG.myAvatar, 'avatar')}" style="width:32px; height:32px; border-radius:50%; object-fit:cover;"> 
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


function submitEditPost(event) {
    event.preventDefault();

    const form = document.getElementById('editPostForm');
    const postId = form.getAttribute('data-post-id'); 
    
    if (!postId) {
        alert("Error: No post ID found.");
        return;
    }

    const formData = new FormData(form);

    sendRequest(APP_CONFIG.baseURL + '/post/update/' + postId, formData).then(res => {
        if(res.status === 'success') {
            location.reload();
        } else {
            alert(res.message || 'Error updating post');
        }
    });
}