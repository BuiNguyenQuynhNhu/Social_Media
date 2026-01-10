<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<style>
    .friend-container {
        max-width: 900px;
        margin: 20px auto;
        display: flex;
        gap: 20px;
    }
    .friend-sidebar {
        width: 250px;
        background: #fff;
        border-radius: 8px;
        padding: 15px;
        box-shadow: 0 1px 2px rgba(0,0,0,0.1);
        height: fit-content;
    }
    .friend-content {
        flex: 1;
        background: #fff;
        border-radius: 8px;
        padding: 20px;
        box-shadow: 0 1px 2px rgba(0,0,0,0.1);
    }
    .friend-menu-title {
        font-size: 20px;
        font-weight: 700;
        margin-bottom: 15px;
    }
    .friend-menu-item {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 10px;
        border-radius: 8px;
        color: #050505;
        text-decoration: none;
        font-weight: 600;
        margin-bottom: 5px;
    }
    .friend-menu-item:hover {
        background: #f0f2f5;
    }
    .friend-menu-item.active {
        background: #e7f3ff;
        color: #1877f2;
    }
    .friend-list {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
        gap: 15px;
    }
    .friend-card {
        border: 1px solid #ddd;
        border-radius: 8px;
        overflow: hidden;
        display: flex;
        flex-direction: column;
    }
    .friend-img {
        width: 100%;
        height: 180px;
        object-fit: cover;
    }
    .friend-info {
        padding: 10px;
        display: flex;
        flex-direction: column;
        gap: 5px;
    }
    .friend-name {
        font-weight: 600;
        font-size: 16px;
        text-decoration: none;
        color: #050505;
    }
    .friend-actions {
        margin-top: auto;
        display: flex;
        flex-direction: column;
        gap: 5px;
    }
    .btn-friend {
        width: 100%;
        padding: 8px;
        border-radius: 6px;
        border: none;
        font-weight: 600;
        cursor: pointer;
        font-size: 14px;
    }
    .btn-blue { background: #e7f3ff; color: #1877f2; }
    .btn-blue:hover { background: #dbe7f2; }
    .btn-gray { background: #e4e6eb; color: #050505; }
    .btn-gray:hover { background: #d8dadf; }
</style>

<?= view('components/user/navbar', ['avatarUrl' => $avatarUrl]) ?>

<div class="friend-container">
    <div class="friend-sidebar">
        <h2 class="friend-menu-title">Friends</h2>
        <a href="<?= base_url('friend') ?>" class="friend-menu-item <?= $activeTab == 'all' ? 'active' : '' ?>">
            <i data-lucide="users"></i> All Friends
        </a>
        <a href="<?= base_url('friend/requests') ?>" class="friend-menu-item <?= $activeTab == 'received' ? 'active' : '' ?>">
            <i data-lucide="user-plus"></i> Friend Requests
        </a>
        <a href="<?= base_url('friend/sent') ?>" class="friend-menu-item <?= $activeTab == 'sent' ? 'active' : '' ?>">
            <i data-lucide="send"></i> Sent Requests
        </a>
    </div>

    <div class="friend-content">
        <h3><?= esc($title) ?></h3>
        <div class="friend-list">
            <?php if (!empty($list)): ?>
                <?php foreach ($list as $user): ?>
                    <div class="friend-card">
                        <?php 
                        $avatar = $user['avatar'];
                        if (!str_starts_with($avatar, 'http')) $avatar = base_url($avatar ?: 'ICON/avatar-default.svg');
                        ?>
                        <img src="<?= $avatar ?>" class="friend-img" alt="Avatar">
                        <div class="friend-info">
                            <a href="<?= base_url('user/profile/'.$user['id']) ?>" class="friend-name">
                                <?= esc($user['firstname'] . ' ' . $user['lastname']) ?>
                            </a>
                            
                            <div class="friend-actions">
                                <?php if($activeTab == 'received'): ?>
                                    <a href="<?= base_url('friend/accept/'.$user['id_friend_ship']) ?>" class="btn-friend btn-blue">Confirm</a>
                                    <a href="<?= base_url('friend/remove/'.$user['id_friend_ship']) ?>" class="btn-friend btn-gray">Delete</a>
                                <?php elseif($activeTab == 'sent'): ?>
                                    <a href="<?= base_url('friend/remove/'.$user['id_friend_ship']) ?>" class="btn-friend btn-gray">Cancel Request</a>
                                <?php else: ?>
                                    <a href="<?= base_url('friend/remove/'.$user['id_friend_ship']) ?>" class="btn-friend btn-gray">Unfriend</a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No friends found.</p>
            <?php endif; ?>
        </div>
    </div>
</div>
<?= $this->endSection() ?>