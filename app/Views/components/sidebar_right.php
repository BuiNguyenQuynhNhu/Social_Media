<div class="sidebar right-sidebar">
    <div class="sidebar-section">
        <div class="section-header">
            <h3>Contacts</h3>
            <div class="actions">
                <i data-lucide="search"></i>
                <i data-lucide="more-horizontal"></i>
            </div>
        </div>
        
        <div class="contact-list">
            <!-- Example Contacts (Dynamic later) -->
            <?php if(isset($friends) && !empty($friends)): ?>
                <?php foreach($friends as $friend): ?>
                <a href="#" class="contact-item" onclick="openChat('<?= $friend['id'] ?>')">
                    <div class="avatar-wrapper">
                        <img src="<?= $friend['avatar'] ? base_url($friend['avatar']) : base_url('ICON/avatar-default.svg') ?>" alt="User">
                        <?php if(isset($friend['is_online']) && $friend['is_online']): ?>
                            <div class="online-status"></div>
                        <?php endif; ?>
                    </div>
                    <span><?= $friend['firstname'] . ' ' . $friend['lastname'] ?></span>
                </a>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="text-muted">No contacts yet.</p>
            <?php endif; ?>
        </div>
    </div>
</div>
