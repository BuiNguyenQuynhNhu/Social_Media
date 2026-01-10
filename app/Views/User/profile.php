<?php 
    $isOwnProfile = (session()->get('user_id') == $user['id']); 
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($user['firstname'] . ' ' . $user['lastname']) ?> | Profile</title>
    
    <script src="https://unpkg.com/lucide@latest"></script>
    <link rel="stylesheet" href="<?= base_url('CSS/navbar.css') ?>">
    <link rel="stylesheet" href="<?= base_url('CSS/User/profile.css') ?>">
    <link rel="stylesheet" href="<?= base_url('CSS/overall.css') ?>">
    <link rel="stylesheet" href="<?= base_url('CSS/post/post.css') ?>">
</head>

<body class="background">
    <?= view('templates/UserNavbar', ['avatarUrl' => $avatarUrl]) ?>
    <div class="profile-container">
        <?= view('templates/UserHeader') ?>

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
                        <?= view('templates/post') ?>
                    <?php endif; ?>

                    <?php foreach ($posts as $post): ?>
                        <div class="fb-card post-card">
                            <div class="post-header">
                                <div class="header-left">
                                    <img src="<?= (isset($post['avatar']) && strpos($post['avatar'], 'http') === 0) ? $post['avatar'] : ($post['avatar'] ? base_url($post['avatar']) : base_url('ICON/avatar-default.svg')) ?>" class="small-avatar">
                                    <div class="post-info">
                                        <span class="author"><?= esc($post['firstname'] . ' ' . $post['lastname']) ?></span>
                                        <span class="time"><?= date('M d, Y', strtotime($post['created_at'])) ?></span>
                                    </div>
                                </div>

                                <?php if (session()->get('user_id') == $post['id_user']): ?>
                                <form action="<?= base_url('post/delete_post/' . $post['id']) ?>" method="post"
                                    onsubmit="return confirm('Do you really want to delete this post?')">

                                    <?= csrf_field() ?>
                                    <input type="hidden" name="_method" value="DELETE">

                                    <button type="submit" class="delete-icon">
                                        <i data-lucide="trash-2"></i>
                                    </button>
                                </form>

                                <?php endif; ?>
                            </div>

                            <div class="post-content">
                                <p><?= esc($post['content']) ?></p>
                                
                                <div class="media-grid">
                                    <?php foreach ($post['media'] as $media): ?>
                                        <?php if ($media['type'] === 'video'): ?>
                                            <video src="<?= $media['url'] ?>" controls class="post-media-item"></video>
                                        <?php else: ?>
                                            <img src="<?= $media['url'] ?>" class="post-media-item">
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
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
    <script src="<?= base_url('JS/post.js') ?>"></script>
</body>
</html>