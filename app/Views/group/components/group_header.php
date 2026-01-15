<div class="group-header">
    <div class="group-cover" id="group-cover" style="background-image: url('<?= $group['cover_photo'] ?? base_url('ICON/background-default.jpg') ?>');">
        <!-- Cover Photo -->
    </div>
    
    <div class="group-info-bar">
        <div class="group-main-info">
            <img src="<?= $group['avatar'] ?? base_url('ICON/group-default.png') ?>" class="group-avatar" alt="Group avatar" id="group-avatar">
            <div class="group-title-section">
                <h1 id="group-name"><?= esc($group['name']) ?></h1>
                <div class="group-meta">
                    <span class="privacy-badge">
                        <i data-lucide="<?= $group['privacy'] === 'public' ? 'globe' : 'lock' ?>"></i>
                        <?= ucfirst($group['privacy']) ?> Group
                    </span>
                    <span class="meta-separator">•</span>
                    <span class="member-count">
                        <?= $group['member_count'] ?? 0 ?> members
                    </span>
                </div>
            </div>
        </div>
        
        <div class="group-actions">
            <?php if ($is_member): ?>
                <button class="btn-secondary" onclick="leaveGroup()">
                    <i data-lucide="user-minus"></i>
                    Leave Group
                </button>
                <?php if ($is_admin): ?>
                    <a href="<?= base_url("group/{$group['id']}/manage") ?>" class="btn-primary" style="text-decoration: none; display: inline-flex; align-items: center; gap: 8px;">
                        <i data-lucide="settings"></i>
                        Manage
                    </a>
                <?php endif; ?>
            <?php else: ?>
                <button class="btn-primary" onclick="joinGroup()">
                    <i data-lucide="user-plus"></i>
                    Join Group
                </button>
            <?php endif; ?>
        </div>
    </div>
    
    <div class="group-nav-tabs">
        <a href="<?= base_url("group/{$group['id']}/feed") ?>" class="group-nav-tab <?= ($active_tab ?? '') === 'feed' ? 'active' : '' ?>">
            <i data-lucide="newspaper"></i>
            Feed
        </a>
        <a href="<?= base_url("group/{$group['id']}/members") ?>" class="group-nav-tab <?= ($active_tab ?? '') === 'members' ? 'active' : '' ?>">
            <i data-lucide="users"></i>
            Members
        </a>
        <a href="<?= base_url("group/{$group['id']}/about") ?>" class="group-nav-tab <?= ($active_tab ?? '') === 'about' ? 'active' : '' ?>">
            <i data-lucide="info"></i>
            About
        </a>
    </div>
</div>
