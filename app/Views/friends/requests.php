<div class="friends-section-header">
    <h2>Friend Requests</h2>
    <a href="<?= base_url('friend/sent') ?>" style="text-decoration: none; font-size: 14px; color: #1877f2;">View Sent Requests</a>
</div>

<div class="friends-grid">
    <?php if (!empty($requests)): ?>
        <?php foreach ($requests as $request): ?>
            <div class="friend-card">
                <?php 
                $avatar = $request['avatar'];
                if (!str_starts_with($avatar, 'http')) $avatar = base_url($avatar ?: 'ICON/avatar-default.svg');
                ?>
                <img src="<?= $avatar ?>" class="friend-card-img" alt="Avatar">
                <div class="friend-card-body">
                    <a href="<?= base_url('user/profile/'.$request['id']) ?>" class="friend-card-name">
                        <?= esc($request['firstname'] . ' ' . $request['lastname']) ?>
                    </a>
                    
                    <div class="friend-card-actions">
                        <a href="<?= base_url('friend/accept/'.$request['id_friend_ship']) ?>" class="btn-friend btn-confirm" style="text-align: center; text-decoration: none;">Confirm</a>
                        <a href="<?= base_url('friend/remove/'.$request['id_friend_ship']) ?>" class="btn-friend btn-delete" style="text-align: center; text-decoration: none;">Delete</a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div style="grid-column: 1/-1; text-align: center; padding: 40px; color: #65676b;">
            <p>No new friend requests.</p>
        </div>
    <?php endif; ?>
</div>
