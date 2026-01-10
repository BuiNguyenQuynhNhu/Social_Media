
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
</head>

<body class="background">
    <?= view('templates/UserNavbar', ['avatarUrl' => $avatarUrl]) ?>

    <div class="profile-container">
        <div class="profile-header-card">
            <div class="cover-photo" style="background-image: url('<?= $coverUrl ?>'); background-size: cover; background-position: center;">
                <?php if($isOwnProfile): ?>
                    <button class="edit-cover-btn"><i data-lucide="camera"></i> Edit Cover Photo</button>
                <?php endif; ?>
            </div>

            <div class="header-content">
                <div class="user-info-main">
                    <img src="<?= $avatarUrl ?>" class="main-avatar">
                    <div class="name-details">
                        <h1><?= esc($user['firstname'] . ' ' . $user['lastname']) ?></h1>
                        <span><?= count($posts ?? []) ?> Posts</span>
                        <span><?= count($posts ?? []) ?> Friends</span>
                    </div>
                </div>
                
                <div class="header-actions">
                    <?php if($isOwnProfile): ?>
                        <button class="btn btn-blue"><i data-lucide="plus"></i> Add Post</button>
                        <button class="btn btn-gray" onclick="document.getElementById('edit-modal').style.display='flex'">
                            <i data-lucide="edit-2"></i> Edit Profile
                        </button>
                    <?php else: ?>
                        <?php if (isset($relationship)): ?>
                            <?php if ($relationship['status'] === 'accepted'): ?>
                                <a href="<?= base_url('friends/remove/' . $relationship['id']) ?>" class="btn btn-gray">
                                    <i data-lucide="user-minus"></i> Unfriend
                                </a>
                            <?php elseif ($relationship['status'] === 'pending' && $relationship['requester_id'] == session()->get('user_id')): ?>
                                <a href="<?= base_url('friends/remove/' . $relationship['id']) ?>" class="btn btn-gray">
                                    <i data-lucide="x"></i> Cancel Request
                                </a>
                            <?php elseif ($relationship['status'] === 'pending' && $relationship['receiver_id'] == session()->get('user_id')): ?>
                                <a href="<?= base_url('friends/accept/' . $relationship['id']) ?>" class="btn btn-blue">
                                    <i data-lucide="user-check"></i> Accept Request
                                </a>
                            <?php endif; ?>
                        <?php else: ?>
                            <a href="<?= base_url('friends/add/' . $user['id']) ?>" class="btn btn-blue">
                                <i data-lucide="user-plus"></i> Add Friend
                            </a>
                        <?php endif; ?>
                        
                        <button class="btn btn-gray"><i data-lucide="message-circle"></i> Message</button>
                    <?php endif; ?>
                </div>
                
            </div>
        </div>
    </div>
    <div class="fb-card friends-header">
        <div class="header-top">
            <h2><?= esc($title) ?></h2>
            <div class="header-tools">
                <div class="search-box">
                    <i data-lucide="search"></i>
                    <input type="text" id="friendSearchInput" placeholder="Search in this list..." onkeyup="filterFriends()">
                </div>
                
                <nav class="friends-nav">
                    <a href="<?= base_url('friends') ?>" class="<?= $activeTab == 'all' ? 'active' : '' ?>">All Friends</a>
                    <a href="<?= base_url('friends/requests') ?>" class="<?= $activeTab == 'received' ? 'active' : '' ?>">Friend Requests</a>
                    <a href="<?= base_url('friends/sent') ?>" class="<?= $activeTab == 'sent' ? 'active' : '' ?>">Sent Requests</a>
                </nav>
            </div>
        </div>
    </div>

    <div class="friends-grid" id="friendsGrid">
        <?php if (!empty($list)): ?>
            <?php foreach ($list as $friend): ?>
            <div class="fb-card friend-card">
                <div class="card-content">
                    <img src="<?= $friend['avatar'] ? base_url($friend['avatar']) : base_url('ICON/avatar-default.svg') ?>" class="card-avatar">
                    <div class="card-info">
                        <?php 
                            $friendId = ($friend['receiver_id'] == session()->get('user_id')) ? $friend['requester_id'] : $friend['receiver_id']; 
                        ?>
                        <a href="<?= base_url('profile/' . $friendId) ?>" class="friend-name">
                            <?= esc($friend['firstname'] . ' ' . $friend['lastname']) ?>
                        </a>
                    </div>
                </div>
                <div class="dropdown">
                    <button class="icon-circle mini dropdown-toggle"><i data-lucide="more-horizontal"></i></button>
                    <div class="dropdown-menu">
                        <?php if ($activeTab == 'all'): ?>
                            <a href="<?= base_url('chat/' . $friendId) ?>"><i data-lucide="message-circle"></i> Message</a>
                            <a href="<?= base_url('friends/remove/' . $friend['id']) ?>" class="unfriend-text"><i data-lucide="user-minus"></i> Unfriend</a>
                        <?php elseif ($activeTab == 'received'): ?>
                            <a href="<?= base_url('friends/accept/' . $friend['id']) ?>"><i data-lucide="check"></i> Accept</a>
                            <a href="<?= base_url('friends/remove/' . $friend['id']) ?>" class="unfriend-text"><i data-lucide="x"></i> Decline</a>
                        <?php else: ?>
                            <a href="<?= base_url('friends/remove/' . $friend['id']) ?>" class="unfriend-text"><i data-lucide="trash-2"></i> Cancel</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="no-results">No <?= strtolower($title) ?> found.</p>
        <?php endif; ?>
    </div>
    <script src="<?= base_url('JS/navbar.js') ?>"></script>
    <script>lucide.createIcons();</script>
</body>
</html>