<div class="sidebar right-sidebar">
    <!-- Friend Recommendations (AI-Powered) -->
    <?php include __DIR__ . '/recommendations_sidebar.php'; ?>
    
    <div class="sidebar-section">
        <div class="section-header">
            <h3>Messages</h3>
            <div class="actions">
                <i data-lucide="search"></i>
                <i data-lucide="more-horizontal"></i>
            </div>
        </div>
        
        <div class="contact-list">
            <?php 
            // Debug
            if (defined('CI_DEBUG') && CI_DEBUG) {
                echo '<!-- recentChats count: ' . (is_array($recentChats) ? count($recentChats) : 'not array') . ' -->';
            }
            ?>
            <?php if(!empty($recentChats) && is_array($recentChats)): ?>
                <?php foreach(array_slice($recentChats, 0, 5) as $chat): ?>
                    <?php if(isset($chat['friend_id']) && isset($chat['name']) && isset($chat['avatar'])): ?>
                    <a href="<?= base_url('message#' . $chat['friend_id']) ?>" class="contact-item">
                        <div class="avatar-wrapper">
                            <img src="<?= $chat['avatar'] ?>" alt="Chat" style="width: 36px; height: 36px; border-radius: 50%; object-fit: cover;">
                        </div>
                        <div style="flex: 1; min-width: 0;">
                            <div style="font-weight: 600; font-size: 14px;"><?= esc($chat['name']) ?></div>
                            <div style="font-size: 12px; color: #65676b; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                <?php 
                                $preview = $chat['last_message'] ?? 'Start a conversation';
                                echo esc(substr($preview, 0, 30)) . (strlen($preview) > 30 ? '...' : '');
                                ?>
                            </div>
                        </div>
                    </a>
                    <?php endif; ?>
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
