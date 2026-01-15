<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Friends | Social Media</title>
    
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <link rel="stylesheet" href="<?= base_url('CSS/navbar.css') ?>">
    <link rel="stylesheet" href="<?= base_url('CSS/User/profile.css') ?>"> <!-- Use Profile CSS for layout -->
    <link rel="stylesheet" href="<?= base_url('CSS/overall.css') ?>">
    <link rel="stylesheet" href="<?= base_url('CSS/dashboard.css') ?>">
    <link rel="stylesheet" href="<?= base_url('CSS/post/post.css') ?>">
    <link rel="stylesheet" href="<?= base_url('CSS/post/comment.css') ?>">
    <link rel="stylesheet" href="<?= base_url('CSS/post/interaction.css') ?>">
    <link rel="stylesheet" href="<?= base_url('CSS/Friend/ProfileFriend.css') ?>">
        
</head>
<body class="background">

<?= view('components/user/navbar', ['avatarUrl' => $navbarAvatarUrl ?? $avatarUrl]) ?>

<div class="profile-container">
    <?= view('components/user/header') ?>

    <div class="profile-body-layout">
        <div class="left-column">
            <div class="fb-card intro-section">
                <h3>Intro</h3>
                <div class="intro-item"><i data-lucide="user"></i><span>Gender: <strong><?= esc($user['gender'] ?? 'Not set') ?></strong></span></div>
                <div class="intro-item"><i data-lucide="cake"></i><span>Birthday: <strong><?= !empty($user['birth']) ? date('d F Y', strtotime($user['birth'])) : 'Not set' ?></strong></span></div>
            </div>
            <!-- Friend Menu -->
            <div class="menu-card">
                <h3 class="menu-title">Friends</h3>
                <a href="<?= base_url('user/list_friend/'.$targetId) ?>" class="menu-link <?= $activeTab == 'all' ? 'active' : '' ?>">
                    <i data-lucide="users"></i> All Friends
                </a>
                
                <?php if ($targetId == $user_id): ?>
                    <a href="<?= base_url('user/list_receive/'.$user_id) ?>" class="menu-link <?= $activeTab == 'received' ? 'active' : '' ?>">
                        <i data-lucide="user-plus"></i> Friend Requests
                    </a>
                    <a href="<?= base_url('user/list_request/'.$user_id) ?>" class="menu-link <?= $activeTab == 'sent' ? 'active' : '' ?>">
                        <i data-lucide="send"></i> Sent Requests
                    </a>
                <?php endif; ?>
            </div>

            <!-- Intro Section (Optional, mimics profile) -->
        
        </div>

        <div class="right-column">
            <h3 style="margin-bottom:15px; font-size:20px; font-weight:700;"><?= esc($title) ?></h3>
            
            <div class="friend-list">
                <?php if (!empty($list)): ?>
                    <?php foreach ($list as $friendItem): ?>
                        <div class="friend-card">
                            <?php 
                            $avatar = $friendItem['avatar'];
                            if (!str_starts_with($avatar, 'http')) $avatar = base_url($avatar ?: 'ICON/avatar-default.svg');
                            $friendId = $friendItem['id']; 
                            ?>
                            <img src="<?= $avatar ?>" class="friend-img" alt="Avatar">
                            <div class="friend-info">
                                <a href="<?= base_url('user/profile/'.$friendId) ?>" class="friend-name">
                                    <?= esc($friendItem['firstname'] . ' ' . $friendItem['lastname']) ?>
                                </a>
                                
                                <div style="font-size:12px; color:#65676b; margin-top:2px;">
                                    <?php if($activeTab == 'received'): ?>
                                        Request sent: <?= isset($friendItem['created_at']) ? date('M d, Y', strtotime($friendItem['created_at'])) : 'Unknown' ?>
                                    <?php elseif($activeTab == 'sent'): ?>
                                        Request sent: <?= isset($friendItem['created_at']) ? date('M d, Y', strtotime($friendItem['created_at'])) : 'Unknown' ?>
                                    <?php else: ?>
                                        <!-- Friends Since: <?= isset($friendItem['updated_at']) ? date('M d, Y', strtotime($friendItem['updated_at'])) : '' ?> -->
                                    <?php endif; ?>
                                </div>
                                
                                <?php if ($targetId == $user_id): ?>
                                    <div class="friend-actions">
                                        <?php if($activeTab == 'received'): ?>
                                            <a href="<?= base_url('friend/accept/'.$friendItem['id_friend_ship']) ?>" class="btn-friend btn-blue">Confirm</a>
                                            <a href="<?= base_url('friend/remove/'.$friendItem['id_friend_ship']) ?>" class="btn-friend btn-gray">Delete</a>
                                        <?php elseif($activeTab == 'sent'): ?>
                                            <a href="<?= base_url('friend/remove/'.$friendItem['id_friend_ship']) ?>" class="btn-friend btn-gray">Cancel Request</a>
                                        <?php else: ?>
                                            <a href="<?= base_url('friend/remove/'.$friendItem['id_friend_ship']) ?>" 
                                               class="btn-friend btn-gray"
                                               onclick="return confirm('Are you sure you want to unfriend this person?')">Unfriend</a>
                                        <?php endif; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div style="grid-column: 1/-1; text-align: center; padding: 40px; color: #65676b;">
                        <i data-lucide="users" size="48" style="margin-bottom: 10px; opacity: 0.5;"></i>
                        <p>No friends found.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
    const BASE_URL = "<?= base_url() ?>";
    lucide.createIcons();
    <?php if(session()->getFlashdata('success')): ?>
        alert('<?= session()->getFlashdata('success') ?>');
    <?php endif; ?>
    <?php if(session()->getFlashdata('error')): ?>
        alert('<?= session()->getFlashdata('error') ?>');
    <?php endif; ?>
</script>
</body>
</html>
