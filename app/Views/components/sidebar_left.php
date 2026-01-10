<div class="sidebar left-sidebar">
    <div class="sidebar-menu">
        <a href="<?= base_url('user/profile/'.$user_id) ?>" class="menu-item">
            <img src="<?= $avatarUrl ?>" alt="Profile" class="avatar-small">
            <span><?= $user['firstname'] . ' ' . $user['lastname'] ?></span>
        </a>
        <a href="<?= base_url('friend') ?>" class="menu-item">
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
        <!-- Dynamic Group List will go here -->
        <a href="#" class="menu-item group-item">
            <img src="<?= base_url('ICON/groups.svg') ?>" class="group-icon">
            <span>CodeIgniter Developers</span>
        </a>
        <a href="<?= base_url('group/create') ?>" class="menu-item create-group">
            <div class="icon-circle"><i data-lucide="plus"></i></div>
            <span>Create New Group</span>
        </a>
    </div>
</div>
