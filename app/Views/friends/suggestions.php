<div class="friends-section-header">
    <h2>People You May Know</h2>
</div>

<div class="friends-grid">
    <?php if (!empty($suggestions)): ?>
        <?php foreach ($suggestions as $person): ?>
            <div class="friend-card">
                <?php 
                $avatar = $person['avatar'];
                if (!str_starts_with($avatar, 'http')) $avatar = base_url($avatar ?: 'ICON/avatar-default.svg');
                ?>
                <img src="<?= $avatar ?>" class="friend-card-img" alt="Avatar">
                <div class="friend-card-body">
                    <a href="<?= base_url('user/profile/'.$person['id']) ?>" class="friend-card-name">
                        <?= esc($person['firstname'] . ' ' . $person['lastname']) ?>
                    </a>
                    
                    <div class="friend-card-actions">
                        <a href="<?= base_url('friend/add/'.$person['id']) ?>" class="btn-friend btn-primary-friend" style="text-align: center; text-decoration: none;">Add Friend</a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div style="grid-column: 1/-1; text-align: center; padding: 40px; color: #65676b;">
            <p>No suggestions available right now.</p>
        </div>
    <?php endif; ?>
</div>
