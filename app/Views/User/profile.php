<?php 
    $isOwnProfile = (session()->get('user_id') == $user['id']); 
    $coverUrl = $coverUrl ?? base_url('ICON/background-default.jpg');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($user['firstname'] . ' ' . $user['lastname']) ?> | Profile</title>
    
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <link rel="stylesheet" href="<?= base_url('CSS/navbar.css') ?>">
    <link rel="stylesheet" href="<?= base_url('CSS/User/profile.css') ?>">
    <link rel="stylesheet" href="<?= base_url('CSS/overall.css') ?>">
    <link rel="stylesheet" href="<?= base_url('CSS/post/post.css') ?>">
    <link rel="stylesheet" href="<?= base_url('CSS/post/comment.css') ?>">
    <link rel="stylesheet" href="<?= base_url('CSS/post/interaction.css') ?>">
    <link rel="stylesheet" href="<?= base_url('CSS/dashboard.css') ?>">
    <script>
        window.BASE_URL = '<?= rtrim(base_url(), '/') ?>';
        window.APP_CONFIG = {
            baseURL: window.BASE_URL,
            userId: '<?= session()->get('user_id') ?>',
            csrfTokenName: '<?= csrf_token() ?>',     
            csrfHash: '<?= csrf_hash() ?>'
        };
    </script>
</head>

<body class="background">
    <?= view('components/user/navbar', ['avatarUrl' => $navbarAvatarUrl ?? $avatarUrl]) ?>
    <div class="profile-container">
        <!-- Pass necessary data to header -->
        <?= view('components/user/header', [
            'user' => $user, 
            'friends' => $friends ?? [], 
            'posts' => $posts ?? [], 
            'avatarUrl' => $avatarUrl, 
            'coverUrl' => $coverUrl
        ]) ?>

        <div class="profile-body-layout">
            <div class="left-column">
                <div class="fb-card intro-section">
                    <h3>Intro</h3>
                    <div class="intro-item"><i data-lucide="user"></i><span>Gender: <strong><?= esc($user['gender'] ?? 'Not set') ?></strong></span></div>
                    <div class="intro-item"><i data-lucide="cake"></i><span>Birthday: <strong><?= !empty($user['birth']) ? date('d F Y', strtotime($user['birth'])) : 'Not set' ?></strong></span></div>
                </div>
            </div>

            <div class="right-column">

                <div id="posts-container" class="tab-content active">
                    <?php if($isOwnProfile): ?>
                        <?= view('components/post/create_post', ['user' => $user, 'avatarUrl' => $avatarUrl]) ?>
                    <?php endif; ?>

                    <?php if(!empty($posts)): ?>
                        <?php foreach ($posts as $post): ?>
                            <?= view('components/post/post_item', ['post' => $post, 'avatarUrl' => $avatarUrl, 'context' => 'profile']) ?>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="fb-card" style="padding:20px; text-align:center; color:#65676b;">No posts yet.</div>
                    <?php endif; ?>
                </div>

                <div id="friends-container" class="tab-content">
                    <div class="friends-grid">
                        <?php if(!empty($friends)): foreach($friends as $f): ?>
                            <div class="fb-card friend-card">
                                <div class="card-content">
                                    <img src="<?= $f['avatar'] ? base_url($f['avatar']) : base_url('ICON/avatar-default.svg') ?>" class="card-avatar">
                                    <span class="friend-name"><?= esc($f['firstname'].' '.$f['lastname']) ?></span>
                                </div>
                            </div>
                        <?php endforeach; endif; ?>
                    </div>
                </div>
            </div>

            <div id="reactors-list" style="overflow-y: auto; padding: 10px; flex: 1;"></div>
        </div>
    </div>

    <!-- Modals -->
    <div id="editPostModal" class="modal-overlay" style="display: none;">
        <div class="modal-content">
            <div class="modal-header" style="padding: 15px; border-bottom: 1px solid #ddd; display: flex; justify-content: space-between;">
                <h3 style="margin:0">Edit Post</h3>
                <div onclick="document.getElementById('editPostModal').style.display='none'" style="cursor:pointer"><i data-lucide="x"></i></div>
            </div>
            <form id="editPostForm" onsubmit="submitEditPost(event)">
                <?= csrf_field() ?>
                <div class="modal-body" style="padding: 15px;">
                    <textarea name="content" id="edit-content" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; min-height: 100px; resize: none;"></textarea>
                    <select name="privacy" id="edit-privacy" style="margin-top: 10px; padding: 5px; width: 100%;">
                        <option value="public">Public</option>
                        <option value="friends">Friends</option>
                        <option value="private">Only Me</option>
                    </select>
                </div>
                <div class="modal-footer" style="padding: 15px; text-align: right; border-top: 1px solid #ddd;">
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Reactors Modal -->
    <div id="reactorsModal" class="modal-overlay" style="display: none; align-items: center; justify-content: center; z-index: 2000;">
        <div class="modal-content" style="width: 400px; max-height: 80vh; display: flex; flex-direction: column;">
             <div class="modal-header">
                <h3 style="margin: 0; font-size: 18px;">People who reacted</h3>
                 <button onclick="document.getElementById('reactorsModal').style.display='none'" class="close-btn"><i data-lucide="x"></i></button>
            </div>
            <div id="reactors-list-modal" class="modal-body" style="overflow-y: auto;"></div>
             <!-- Note: id reactors-list duplicated in main layout? Fixed above to reactors-list-modal or similar if conflict -->
        </div>
    </div>

    <script>
        lucide.createIcons();
    </script>
    <!-- Use standard post.js which handles reactions/comments/modals -->
    <script src="<?= base_url('JS/post.js') ?>"></script>
</body>
</html>