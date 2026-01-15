<div class="friends-section-header">
    <h2>All Friends</h2>
</div>

<div class="friends-grid">
    <?php if (!empty($friends)): ?>
        <?php foreach ($friends as $friend): ?>
            <div class="friend-card">
                <?php 
                $avatar = $friend['avatar'];
                if (!str_starts_with($avatar, 'http')) $avatar = base_url($avatar ?: 'ICON/avatar-default.svg');
                ?>
                <img src="<?= $avatar ?>" class="friend-card-img" alt="Avatar">
                <div class="friend-card-body">
                    <a href="<?= base_url('user/profile/'.$friend['id']) ?>" class="friend-card-name">
                        <?= esc($friend['firstname'] . ' ' . $friend['lastname']) ?>
                    </a>
                    
                    <div class="friend-card-actions">
                        <a href="<?= base_url('user/profile/'.$friend['id']) ?>" class="btn-friend btn-primary-friend" style="text-align: center; text-decoration: none;">View Profile</a>
                        <a href="<?= base_url('friend/remove/'.$friend['id_friend_ship']) ?>" 
                           class="btn-friend btn-delete" 
                           style="text-align: center; text-decoration: none;"
                           onclick="return confirm('Are you sure you want to unfriend?')">Unfriend</a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div style="grid-column: 1/-1; text-align: center; padding: 40px; color: #65676b;">
            <p>No friends to show.</p>
        </div>
    <?php endif; ?>
</div>
