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
                    <span class="privacy-badge" id="group-privacy">
                        <i data-lucide="<?= $group['privacy'] === 'public' ? 'globe' : 'lock' ?>"></i>
                        <?= ucfirst($group['privacy']) ?> Group
                    </span>
                    <span class="member-count" id="group-member-count">
                        <i data-lucide="users"></i>
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
                    <button class="btn-primary" onclick="openGroupSettings()">
                        <i data-lucide="settings"></i>
                        Settings
                    </button>
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
        <button class="tab-btn active" onclick="switchTab('feed')" data-tab="feed">
            <i data-lucide="newspaper"></i>
            Feed
        </button>
        <button class="tab-btn" onclick="switchTab('members')" data-tab="members">
            <i data-lucide="users"></i>
            Members
        </button>
        <button class="tab-btn" onclick="switchTab('about')" data-tab="about">
            <i data-lucide="info"></i>
            About
        </button>
    </div>
</div>
