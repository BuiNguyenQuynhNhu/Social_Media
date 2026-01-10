<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Post | Social Media</title>
    <link rel="stylesheet" href="<?= base_url('CSS/dashboard.css') ?>">
    <link href="https://fonts.googleapis.com/css2?family=Segoe+UI:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body class="background">

<!-- Navbar -->
    <!-- Navbar -->
    <?= view('components/user/navbar', ['avatarUrl' => $avatarUrl]) ?>


    <div class="dashboard-container">
        <!-- Left Sidebar -->
        <?= view('components/user/sidebar_left') ?>

        <!-- Main Feed -->
        <div class="main-feed">
            <!-- Create Post -->
            <?= view('components/user/create_post', ['avatarUrl' => $avatarUrl, 'user' => $user]) ?>

            <!-- Posts -->
            <div class="posts-feed">
                <?php if (!empty($posts)): ?>
                    <?php foreach ($posts as $post): ?>
                        <?= view('components/user/post_item', ['post' => $post, 'avatarUrl' => $avatarUrl]) ?>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="empty-state">
                        <i data-lucide="newspaper"></i>
                        <p>No posts yet. Be the first to post!</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Right Sidebar -->
        <?= view('components/user/sidebar_right') ?>

    </div>

    <!-- Create Post Modal is already included via user/create_post component -->

<script>
    // Initialize Lucide Icons
    lucide.createIcons();

    function openCreatePostModal() {
        document.getElementById('createPostModal').style.display = 'flex';
    }
    function closeCreatePostModal() {
        document.getElementById('createPostModal').style.display = 'none';
    }

    // Media Preview Logic
    function previewFiles(input) {
        const preview = document.getElementById('media-preview-area');
        preview.innerHTML = '';
        if (input.files) {
            Array.from(input.files).forEach(file => {
                const reader = new FileReader();
                reader.onload = function(e) {
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

    // Interaction JS
    function sendReaction(postId, type) {
        $.post('<?= base_url('interaction/react/') ?>' + postId, {type: type}, function(res) {
            updateLikes(postId);
            const btnText = document.getElementById('reaction-btn-text-' + postId);
            const btn = document.getElementById('btn-react-' + postId);
            
            if(res.action === 'unreacted') {
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
        });
    }

    function updateLikes(postId) {
        $.get('<?= base_url('interaction/get_likes/') ?>' + postId, function(res) {
            let text = res.total;
            $('#likes-count-' + postId).text(text || '');
        });
    }

    function toggleComments(postId) {
        let section = $('#comments-' + postId);
        if (section.is(':visible')) {
            section.hide();
        } else {
            section.show();
        }
    }

    function addComment(input, postId) {
        let content = input.value;
        if(!content.trim()) return;
        
        $.post('<?= base_url('comment/add/') ?>' + postId, {content: content}, function(res) {
            if(res.status === 'success') {
                input.value = '';
                loadComments(postId);
            }
        });
    }

    // --- COMMENT SYSTEM ---
    function loadComments(postId) {
        $.get('<?= base_url('comment/get/') ?>' + postId, function(res) {
            let container = $('#comments-' + postId + ' .comments-list');
            container.empty();
            if(res.data) {
                $('#comments-count-' + postId).text(res.data.length + ' Comments');
                
                // Build Tree
                let map = {}, roots = [];
                res.data.forEach(c => {
                    c.children = [];
                    map[c.id] = c;
                });
                res.data.forEach(c => {
                    if(c.parent_id && map[c.parent_id]) {
                        map[c.parent_id].children.push(c);
                    } else {
                        roots.push(c);
                    }
                });

                roots.forEach(c => {
                    renderComment(c, container);
                });
                lucide.createIcons();
            }
        });
    }

    function renderComment(c, container) {
        let timeText = c.created_at ? new Date(c.created_at).toLocaleString() : 'Just now';
        let likeActive = c.is_liked ? 'style="color:#1877f2; font-weight:bold;"' : '';
        let likeText = c.is_liked ? 'Liked' : 'Like';
        let deleteBtn = c.can_delete ? `<span onclick="deleteComment('${c.id}', '${c.post_id}')">Delete</span>` : '';
        
        let html = `
            <div class="comment-wrapper" id="comment-${c.id}">
                <div class="comment" data-depth="${c.depth}">
                    <img src="${c.user.avatar}" class="avatar-xs">
                    <div class="comment-content-wrapper">
                        <div class="comment-bubble">
                            <a href="<?= base_url('user/profile/') ?>${c.user.id}" class="comment-author">${c.user.name}</a>
                            <p>${c.content}</p>
                        </div>
                        <div class="comment-actions">
                            <span>${timeText}</span>
                            <span onclick="likeComment('${c.id}')" ${likeActive} id="like-btn-${c.id}">${likeText} (${c.likes_count})</span>
                            <span onclick="toggleReply('${c.id}')">Reply</span>
                            ${deleteBtn}
                        </div>
                        <div class="reply-box" id="reply-box-${c.id}">
                             <div class="reply-input-wrapper">
                                <input type="text" id="reply-input-${c.id}" placeholder="Write a reply...">
                                <button onclick="submitReply('${c.post_id}', '${c.id}')" style="color:#1877f2; border:none; background:none; cursor:pointer;"><i data-lucide="send"></i></button>
                             </div>
                        </div>
                    </div>
                </div>
                <div class="replies-container" style="display:flex; flex-direction:column; gap:0;">
                </div>
            </div>
        `;
        
        let el = $(html);
        container.append(el);
        
        // Render Children
        let repliesContainer = el.find('.replies-container');
        if(c.children && c.children.length > 0) {
            c.children.forEach(child => {
                renderComment(child, repliesContainer);
            });
        }
    }

    function toggleReply(id) {
        $('#reply-box-' + id).toggle();
        $('#reply-input-' + id).focus();
    }

    function submitReply(postId, parentId) {
        let content = $('#reply-input-' + parentId).val();
        if(!content) return;
        
        $.post('<?= base_url('comment/add/') ?>' + postId, {content: content, parent_id: parentId}, function(res) {
            if(res.status === 'success') {
                loadComments(postId);
            } else {
                alert(res.message);
            }
        });
    }

    function likeComment(id) {
        $.post('<?= base_url('comment/like/') ?>' + id, function(res) {
            if(res.status === 'success') {
                let btn = $('#like-btn-' + id);
                if(res.action === 'liked') {
                    btn.css('color', '#1877f2').css('font-weight', 'bold');
                    btn.text('Liked (' + res.count + ')');
                } else {
                    btn.css('color', '').css('font-weight', 'normal');
                    btn.text('Like (' + res.count + ')');
                }
            }
        });
    }

    function deleteComment(id, postId) {
        if(!confirm('Delete this comment?')) return;
        $.ajax({
            url: '<?= base_url('comment/delete/') ?>' + id,
            type: 'DELETE',
            success: function(res) {
                if(res.status === 'success') {
                    loadComments(postId);
                } else {
                    alert('Failed to delete');
                }
            }
        });
    }

    // Main create post handler (no change needed but ensuring safety)
    
    // Initial Load
    $(document).ready(function() {
        // Load initial likes stats for all posts
        $('.post-card').each(function() {
            let pid = $(this).data('id');
            updateLikes(pid);
            loadComments(pid); // Auto-load comments
        });
    });
</script>

</body>
</html>
