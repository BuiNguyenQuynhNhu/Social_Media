<div class="sidebar right-sidebar">
    <!-- Friend Recommendations (AI-Powered) -->
    <?php include __DIR__ . '/recommendations_sidebar.php'; ?>
    
    <div class="sidebar-section">
        <div class="section-header">
            <h3>Recent Chats</h3>
            <div class="actions">
                <i data-lucide="search"></i>
                <i data-lucide="more-horizontal"></i>
            </div>
        </div>
        
        <div class="contact-list">
            <?php if(!empty($recentChats)): ?>
                <?php foreach($recentChats as $chat): ?>
                <a href="<?= base_url('chat') ?>" class="contact-item">
                    <div class="avatar-wrapper">
                        <img src="<?= $chat['avatar'] ?>" alt="Chat" style="width: 36px; height: 36px; border-radius: 50%; object-fit: cover;">
                        <!-- Status indicator could go here -->
                    </div>
                    <span><?= esc($chat['name']) ?></span>
                </a>
                <?php endforeach; ?>
            <?php elseif(isset($friends) && !empty($friends)): ?>
                <!-- Fallback to friends list if no recent chats -->
                <?php foreach($friends as $friend): ?>
                <a href="<?= base_url('message') ?>" class="contact-item">
                    <div class="avatar-wrapper">
                        <img src="<?= $friend['avatar'] ? base_url($friend['avatar']) : base_url('ICON/avatar-default.svg') ?>" alt="User">
                    </div>
                    <span><?= $friend['firstname'] . ' ' . $friend['lastname'] ?></span>
                </a>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="text-muted" style="padding: 0 8px;">No chats yet.</p>
            <?php endif; ?>
        </div>
    </div>
</div>
