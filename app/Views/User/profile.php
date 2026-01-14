<?php 
    $isOwnProfile = (session()->get('user_id') == $user['id']); 
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
</head>

<body class="background">
    <?= view('components/user/navbar', ['avatarUrl' => $avatarUrl]) ?>
    <div class="profile-container">
        <?= view('components/user/header') ?>

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

                    <?php foreach ($posts as $post): ?>
                        <?= view('components/post/post_item', ['post' => $post, 'avatarUrl' => $avatarUrl, 'showDelete' => true, 'allowManage' => true]) ?>
                    <?php endforeach; ?>
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
        </div>
    </div>
    <script>
        const BASE_URL = "<?= rtrim(base_url(), '/') ?>";
        lucide.createIcons();
    </script>
    <script src="<?= base_url('JS/post/utils.js') ?>"></script>
    <script src="<?= base_url('JS/post/reactions.js') ?>"></script>
    <script src="<?= base_url('JS/post/comments.js') ?>"></script>
    <script src="<?= base_url('JS/post.js') ?>"></script>
</body>
</html>