<div class="sidebar left-sidebar">
    <div class="sidebar-menu">
        <a href="<?= base_url('user/profile/'.$user_id) ?>" class="menu-item">
            <img src="<?= $avatarUrl ?>" alt="Profile" class="avatar-small" style="width: 36px !important; height: 36px !important; border-radius: 50%; object-fit: cover;">
            <span><?= $user['firstname'] . ' ' . $user['lastname'] ?></span>
        </a>
        <a href="<?= base_url('friends') ?>" class="menu-item">
            <i data-lucide="users"></i>
            <span>Friends</span>
        </a>
        <a href="<?= base_url('group') ?>" class="menu-item">
            <i data-lucide="users-round"></i>
            <span>Groups</span>
        </a>
        <a href="<?= base_url('saved') ?>" class="menu-item">
            <i data-lucide="bookmark"></i>
            <span>Saved</span>
        </a>
    </div>

    <div class="sidebar-divider"></div>

    <div class="sidebar-section">
        <h3>Your Groups</h3>
        <?php if (!empty($myGroups)): ?>
            <?php foreach ($myGroups as $group): ?>
                <a href="<?= base_url('group/' . $group['id']) ?>" class="menu-item group-item">
                    <img src="<?= $group['avatar'] ?>" class="group-icon" style="width: 36px; height: 36px; border-radius: 8px; object-fit: cover;">
                    <span><?= esc($group['name']) ?></span>
                </a>
            <?php endforeach; ?>
        <?php else: ?>
            <p style="padding: 0 8px; color: #65676b; font-size: 14px;">No groups yet</p>
        <?php endif; ?>
        <a href="<?= base_url('group/create-view') ?>" class="menu-item create-group">
            <div class="icon-circle"><i data-lucide="plus"></i></div>
            <span>Create New Group</span>
        </a>
    </div>
</div>
