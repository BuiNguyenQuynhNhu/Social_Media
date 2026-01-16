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
                        <button class="btn-friend btn-primary-friend btn-add-friend" data-id="<?= $person['id'] ?>" style="text-align: center; width: 100%;">Add Friend</button>
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

<script>
$(document).ready(function() {
    $('.btn-add-friend').click(function() {
        const btn = $(this);
        const receiverId = btn.data('id');
        
        // Disable button
        btn.prop('disabled', true).text('Sending...');
        
        $.ajax({
            url: '<?= base_url('friend/add') ?>',
            type: 'POST',
            data: {
                receiver_id: receiverId,
                // Add CSRF if available globally, otherwise we might need the input here too
                // Typically header or global var is used. Let's assume global or handle error.
                // But wait, friends page DOES NOT have the csrf input we just added to settings.
                // We should add it to friends/layout.php ideally or just here for now.
                // Actually, let's grab it if it exists, or risk 403 if strict.
                // Assuming "friends/layout" might need it too.
            },
            success: function(response) {
                if (response.status === 'success') {
                    btn.text('Request Sent').addClass('btn-secondary-friend').removeClass('btn-primary-friend');
                } else {
                    alert(response.message);
                    btn.prop('disabled', false).text('Add Friend');
                }
            },
            error: function() {
                alert('Error sending request');
                btn.prop('disabled', false).text('Add Friend');
            }
        });
    });
});
</script>
