<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://unpkg.com/lucide@latest"></script>
    
    <script>
        const APP_CONFIG = {
            baseURL: '<?= base_url() ?>',
            userId: '<?= session()->get('user_id') ?>',
            myAvatar: '<?= $avatarUrl ?>',
            csrfTokenName: '<?= csrf_token() ?>',     
            csrfHash: '<?= csrf_hash() ?>'
        };
    </script>
    <script src="<?= base_url('JS/post.js') ?>"></script>

    <link rel="stylesheet" href="<?= base_url('CSS/navbar.css') ?>">
    <link rel="stylesheet" href="<?= base_url('CSS/overall.css') ?>">
    <link rel="stylesheet" href="<?= base_url('CSS/profile.css') ?>"> 

    <style>
        .dashboard-container {
            display: flex;
            justify-content: space-between;
            max-width: 1200px;
            margin: 20px auto;
            gap: 30px;
            padding: 0 15px;
        }

        .sidebar-left {
            width: 280px;
            position: sticky;
            top: 80px;
            height: fit-content;
            display: none;
        }
        @media (min-width: 900px) { .sidebar-left { display: block; } }

        .menu-item {
            display: flex; align-items: center; gap: 12px;
            padding: 10px; border-radius: 8px;
            text-decoration: none; color: #050505; font-weight: 600;
            transition: 0.2s; margin-bottom: 5px;
        }
        .menu-item:hover { background: #e4e6eb; }
        .menu-img { width: 36px; height: 36px; border-radius: 50%; object-fit: cover; }

        .feed-center {
            flex: 1;
            max-width: 600px;
            margin: 0 auto;
        }

        .sidebar-right {
            width: 280px;
            position: sticky;
            top: 80px;
            height: fit-content;
            display: none;
        }
        @media (min-width: 1100px) { .sidebar-right { display: block; } }

        .contact-title { color: #65676b; font-weight: 600; margin-bottom: 10px; }
        .contact-item {
            display: flex; align-items: center; gap: 12px;
            padding: 8px; border-radius: 8px;
            cursor: pointer; text-decoration: none; color: black;
            transition: 0.2s;
        }
        .contact-item:hover { background: #e4e6eb; }
        .contact-avatar { width: 36px; height: 36px; border-radius: 50%; object-fit: cover; }
        
        .online-dot { 
            width: 10px; height: 10px; background: #31a24c; border-radius: 50%; 
            position: absolute; bottom: 0; right: 0; border: 2px solid white;
        }
    </style>
</head>
<body class="background">

    <?= view('templates/UserNavbar', ['avatarUrl' => $avatarUrl]) ?>

    <div class="dashboard-container">
        
        <div class="sidebar-left">
            <a href="<?= base_url('user/profile') ?>" class="menu-item">
                <img src="<?= $avatarUrl ?>" class="menu-img">
                <span><?= esc($user['firstname'] . ' ' . $user['lastname']) ?></span>
            </a>
            <a href="<?= base_url('friends') ?>" class="menu-item">
                <i data-lucide="users" style="color: #1877f2;"></i>
                <span>Friends</span>
            </a>
            <a href="#" class="menu-item">
                <i data-lucide="bookmark" style="color: #a033ff;"></i>
                <span>Saved</span>
            </a>
            <a href="#" class="menu-item">
                <i data-lucide="clock" style="color: #2abba7;"></i>
                <span>Memories</span>
            </a>
        </div>

        <div class="feed-center">
            
            <div class="fb-card create-post">
                <div class="create-post-trigger" onclick="openPostModal()" style="display: flex; align-items: center; cursor: pointer; padding: 10px;">
                    <div class="trigger-avatar" style="margin-right: 10px;">
                        <img src="<?= $avatarUrl ?>" style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover;">
                    </div>
                    <div class="trigger-input" style="background: #f0f2f5; padding: 10px 15px; border-radius: 20px; flex-grow: 1; color: #65676b;">
                        What's on your mind, <?= esc($user['firstname']) ?>?
                    </div>
                </div>
            </div>

            <div id="feed-container">
                <?php if (!empty($posts)): ?>
                    <?php foreach ($posts as $post): ?>
                        <script>
                            document.addEventListener('DOMContentLoaded', () => {
                                const container = document.getElementById('feed-container');
                                const postData = <?= json_encode($post) ?>;
                                const html = generatePostHTML(postData); 
                                container.insertAdjacentHTML('beforeend', html);
                                lucide.createIcons();
                            });
                        </script>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div style="text-align: center; color: #65676b; margin-top: 30px; background: white; padding: 20px; border-radius: 8px;">
                        <img src="<?= base_url('ICON/no-post.svg') ?>" style="width: 100px; opacity: 0.5; margin-bottom: 10px;">
                        <p>No posts in your News Feed.</p>
                        <p style="font-size: 13px;">Add more friends to see their updates!</p>
                    </div>
                <?php endif; ?>
            </div>

        </div>

        <div class="sidebar-right">
            <div class="contact-title">Contacts</div>
            
            <?php if (!empty($friends)): ?>
                <?php foreach ($friends as $friend): ?>
                    <?php 
                        $fAvatar = $friend['avatar'] ?? 'ICON/avatar-default.svg';
                        $fAvatarUrl = str_starts_with($fAvatar, 'http') ? $fAvatar : base_url($fAvatar);
                    ?>
                    <a href="<?= base_url('chat/with/'.$friend['user_id']) ?>" class="contact-item">
                        <div style="position: relative;">
                            <img src="<?= $fAvatarUrl ?>" class="contact-avatar">
                            <div class="online-dot"></div>
                        </div>
                        <span style="font-weight: 600; font-size: 14px;">
                            <?= esc($friend['firstname'] . ' ' . $friend['lastname']) ?>
                        </span>
                    </a>
                <?php endforeach; ?>
            <?php else: ?>
                <div style="font-size: 13px; color: #65676b;">No friends online.</div>
            <?php endif; ?>
        </div>

    </div>

    <div id="postModal" class="modal-overlay" style="display: none;">
        <div class="modal-content">
            
            <div class="modal-header" style="display: flex; align-items: center; justify-content: space-between; padding: 15px; border-bottom: 1px solid #ddd;">
                <h3 style="flex-grow: 1; text-align: center; margin: 0; font-size: 20px; font-weight: 700;">Create Post</h3>
                <div onclick="closePostModal()" style="cursor: pointer; width: 36px; height: 36px; background: #e4e6eb; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                    <i data-lucide="x"></i>
                </div>
            </div>

            <form id="createPostForm" onsubmit="submitCreatePost(event)" enctype="multipart/form-data">
                <div class="modal-body" style="padding: 15px;">
                    
                    <div class="user-profile-row" style="display: flex; align-items: center; margin-bottom: 15px;">
                        <img src="<?= $avatarUrl ?? base_url('ICON/avatar-default.svg') ?>" alt="User" style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover; margin-right: 10px;">
                        
                        <div>
                            <div style="font-weight: 600; font-size: 15px;">
                                <?= esc($user['firstname'] . ' ' . $user['lastname']) ?>
                            </div>
                            <select name="privacy" style="background: #e4e6eb; border: none; border-radius: 6px; padding: 4px 8px; font-size: 13px; font-weight: 600; margin-top: 4px; cursor: pointer;">
                                <option value="public">🌎 Public</option>
                                <option value="friends">👥 Friends</option>
                                <option value="private">🔒 Only me</option>
                            </select>
                        </div>
                    </div>

                    <textarea name="content" 
                            placeholder="What's on your mind, <?= esc($user['firstname']) ?>?" 
                            style="width: 100%; border: none; outline: none; resize: none; font-size: 24px; min-height: 120px; font-family: inherit; margin-bottom: 10px;"></textarea>

                    <div id="image-preview-container" style="display:none; margin-top:10px; position: relative; border: 1px solid #ddd; border-radius: 8px; padding: 5px;">
                        <img id="image-preview" src="#" alt="Image Preview" style="width: 100%; max-height: 300px; object-fit: contain; border-radius: 4px;">
                        <button type="button" onclick="removeImage()" style="position: absolute; top: 10px; right: 10px; background: white; border: 1px solid #ccc; border-radius: 50%; width: 30px; height: 30px; cursor: pointer; display: flex; align-items: center; justify-content: center;">
                            <i data-lucide="x"></i>
                        </button>
                    </div>
                </div>

                <div class="modal-footer" style="padding: 15px; border-top: 1px solid #ddd; display: flex; justify-content: space-between; align-items: center;">
                    <div class="upload-btn-wrapper">
                        <label for="post-image" style="cursor: pointer; display: flex; align-items: center; gap: 8px; padding: 8px 12px; border-radius: 5px; background-color: #f0f2f5;" title="Photo/Video">
                            <span style="font-size: 24px;">📷</span> 
                        </label>
                        <input type="file" name="image" id="post-image" accept="image/*" style="display: none;" onchange="previewImage(this)">
                    </div>
                    <button type="submit" class="btn btn-primary" style="padding: 8px 30px; font-weight: bold; background-color: #1877f2; color: white; border: none; border-radius: 6px; font-size: 15px; cursor: pointer;">Post</button>
                </div>
            </form>
        </div>
    </div>

    <div id="editPostModal" class="modal-overlay" style="display: none;">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Edit Post</h3>
                <div onclick="document.getElementById('editPostModal').style.display='none'" class="close-btn"><i data-lucide="x"></i></div>
            </div>
            <form id="editPostForm" onsubmit="submitEditPost(event)">
                <div class="modal-body">
                    <textarea name="content" id="edit-content" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; min-height: 100px; resize: none;"></textarea>
                    <select name="privacy" id="edit-privacy" style="margin-top: 10px; padding: 5px; width: 100%;">
                        <option value="public">Public</option>
                        <option value="friends">Friends</option>
                        <option value="private">Only Me</option>
                    </select>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </form>
        </div>
    </div>

    <div id="reactorsModal" class="modal-overlay" style="display: none; align-items: center; justify-content: center; z-index: 2000;">
        <div class="modal-content" style="width: 400px; max-height: 80vh; display: flex; flex-direction: column;">
            <div class="modal-header">
                <h3 style="margin: 0; font-size: 18px;">People who reacted</h3>
                <button onclick="document.getElementById('reactorsModal').style.display='none'" class="close-btn"><i data-lucide="x"></i></button>
            </div>
            <div id="reactors-list" class="modal-body"></div>
        </div>
    </div>

    <script src="<?= base_url('JS/navbar.js') ?>"></script>
    <script> lucide.createIcons(); </script>
</body>
</html>