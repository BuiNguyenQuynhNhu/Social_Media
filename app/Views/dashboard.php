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
<body>

<!-- Navbar -->
<?= view('templates/UserNavbar') ?>


<div class="dashboard-container">
    <!-- Left Sidebar -->
    <?= view('components/sidebar_left') ?>

    <!-- Main Feed -->
    <div class="main-feed">
        <!-- Create Post -->
        <div class="create-post-card">
            <div class="cp-top">
                <img src="<?= $avatarUrl ?>" alt="User" class="avatar-medium">
                <div class="cp-input" onclick="openCreatePostModal()">
                    What's on your mind, <?= $user['firstname'] ?>?
                </div>
            </div>
            <div class="cp-actions">
                <button class="cp-btn" onclick="openCreatePostModal()">
                    <i data-lucide="image" class="text-green"></i>
                    <span>Photo/Video</span>
                </button>
            </div>
        </div>

        <!-- Posts -->
        <div class="posts-feed">
            <?php if (!empty($posts)): ?>
                <?php foreach ($posts as $post): ?>
                    <div class="post-card" data-id="<?= $post['id'] ?>">
                        <div class="post-header">
                            <img src="<?= $post['avatar'] ? base_url($post['avatar']) : base_url('ICON/avatar-default.svg') ?>" alt="User" class="avatar-small">
                            <div class="post-info">
                                <a href="<?= base_url('user/profile/'.$post['id_user']) ?>" class="author-name"><?= esc($post['firstname'] . ' ' . $post['lastname']) ?></a>
                                <span class="timestamp"><?= date('M d, Y', strtotime($post['created_at'])) ?></span>
                            </div>
                            <button class="icon-btn-ghost"><i data-lucide="more-horizontal"></i></button>
                        </div>
                        
                        <div class="post-content">
                            <?= esc($post['content']) ?>
                        </div>

                        <!-- Media Grid -->
                        <?php if (!empty($post['media'])): ?>
                            <?php 
                                $mediaCount = count($post['media']);
                                $displayMedia = array_slice($post['media'], 0, 5);
                                $gridClass = 'grid-' . min($mediaCount, 5);
                            ?>
                            <div class="post-media-grid <?= $gridClass ?>">
                                <?php foreach ($displayMedia as $index => $item): ?>
                                    <div class="media-item <?= ($index === 4 && $mediaCount > 5) ? 'more-overlay' : '' ?>" 
                                         data-more="+<?= $mediaCount - 5 ?>" 
                                         onclick="openLightbox('<?= $post['id'] ?>', <?= $index ?>)">
                                        <?php if (strpos($item['type'], 'video') !== false): ?>
                                            <video src="<?= $item['url'] ?>"></video>
                                        <?php else: ?>
                                            <img src="<?= $item['url'] ?>" alt="Post Image">
                                        <?php endif; ?>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>

                        <div class="post-stats">
                            <div class="stat-item">
                                <i data-lucide="thumbs-up" class="w-4 h-4"></i>
                                <span id="likes-count-<?= $post['id'] ?>">0</span>
                            </div>
                            <div class="stat-item" onclick="toggleComments('<?= $post['id'] ?>')">
                                <span id="comments-count-<?= $post['id'] ?>">0 Comments</span>
                            </div>
                        </div>

                        <div class="post-actions">
                            <div class="reaction-wrapper">
                                <button class="action-btn" id="btn-react-<?= $post['id'] ?>">
                                    <i data-lucide="thumbs-up"></i>
                                    <span id="reaction-btn-text-<?= $post['id'] ?>">Like</span>
                                </button>
                                <div class="reaction-box">
                                    <div class="reaction-icon" onclick="sendReaction('<?= $post['id'] ?>', 'like')">👍</div>
                                    <div class="reaction-icon" onclick="sendReaction('<?= $post['id'] ?>', 'love')">❤️</div>
                                    <div class="reaction-icon" onclick="sendReaction('<?= $post['id'] ?>', 'haha')">😆</div>
                                    <div class="reaction-icon" onclick="sendReaction('<?= $post['id'] ?>', 'wow')">😮</div>
                                    <div class="reaction-icon" onclick="sendReaction('<?= $post['id'] ?>', 'sad')">😢</div>
                                    <div class="reaction-icon" onclick="sendReaction('<?= $post['id'] ?>', 'angry')">😡</div>
                                </div>
                            </div>
                            <button class="action-btn" onclick="toggleComments('<?= $post['id'] ?>')">
                                <i data-lucide="message-square"></i>
                                <span>Comment</span>
                            </button>
                            <button class="action-btn">
                                <i data-lucide="share-2"></i>
                                <span>Share</span>
                            </button>
                        </div>

                        <!-- Comments Section -->
                        <div class="comments-section" id="comments-<?= $post['id'] ?>" style="display: none;">
                            <div class="comment-input-wrapper">
                                <img src="<?= $avatarUrl ?>" class="avatar-xs">
                                <div class="input-box">
                                    <input type="text" placeholder="Write a comment..." onkeydown="if(event.key === 'Enter') addComment(this, '<?= $post['id'] ?>')">
                                    <button onclick="addComment(this.previousElementSibling, '<?= $post['id'] ?>')"><i data-lucide="send"></i></button>
                                </div>
                            </div>
                            <div class="comments-list"></div>
                        </div>
                    </div>
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
    <?= view('components/sidebar_right') ?>

</div>

<!-- Create Post Modal -->
<div id="createPostModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Create Post</h2>
            <button class="close-btn" onclick="closeCreatePostModal()"><i data-lucide="x"></i></button>
        </div>
        <form action="<?= base_url('post/create_post') ?>" method="post" enctype="multipart/form-data">
            <div class="modal-body">
                <div class="user-micro">
                    <img src="<?= $avatarUrl ?>" class="avatar-small">
                    <span><?= $user['firstname'] . ' ' . $user['lastname'] ?></span>
                </div>
                <textarea name="content" placeholder="What's on your mind?"></textarea>
                <div class="media-preview" id="media-preview-area"></div>
                <div class="add-to-post">
                    <span>Add to your post</span>
                    <label class="icon-action text-green">
                        <i data-lucide="image"></i>
                        <input type="file" name="images[]" multiple accept="image/*,video/*" hidden onchange="previewFiles(this)">
                    </label>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn-primary w-full">Post</button>
            </div>
        </form>
    </div>
</div>

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

    function loadComments(postId) {
        $.get('<?= base_url('comment/get/') ?>' + postId, function(res) {
            let list = $('#comments-' + postId + ' .comments-list');
            list.empty();
            if(res.data) {
                res.data.forEach(c => {
                    list.append(`
                        <div class="comment">
                            <img src="${c.user.avatar}" class="avatar-xs">
                            <div class="comment-bubble">
                                <a href="#" class="comment-author">${c.user.name}</a>
                                <p>${c.content}</p>
                            </div>
                        </div>
                    `);
                });
                $('#comments-count-' + postId).text(res.data.length + ' Comments');
            }
        });
    }
    
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
