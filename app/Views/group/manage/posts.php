<?= $this->extend('group/manage/layout') ?>

<?= $this->section('content') ?>
<div class="card">
    <div class="card-header">
        <h2>Pending Posts</h2>
        <span class="badge"><?= count($pending_posts) ?></span>
    </div>
    <div class="card-body">
        <?php if (empty($pending_posts)): ?>
            <div class="empty-state">
                <i data-lucide="file-check" class="icon-large"></i>
                <p>No posts waiting for approval.</p>
            </div>
        <?php else: ?>
            <div class="pending-posts-list">
                <?php foreach ($pending_posts as $post): ?>
                    <div class="pending-post-item" id="pending-post-<?= $post['id'] ?>">
                        <div class="post-preview-header">
                            <div class="user-info">
                                <img src="<?= (isset($post['avatar']) && strpos($post['avatar'], 'http') === 0) ? $post['avatar'] : ($post['avatar'] ? base_url($post['avatar']) : base_url('ICON/avatar-default.svg')) ?>" class="avatar-sm">
                                <div>
                                    <div class="user-name"><?= esc($post['firstname'] . ' ' . $post['lastname']) ?></div>
                                    <div class="post-time"><?= date('M d, H:i', strtotime($post['created_at'])) ?></div>
                                </div>
                            </div>
                        </div>
                        <div class="post-preview-content">
                            <p><?= esc($post['content']) ?></p>
                            <?php if (!empty($post['media'])): ?>
                                <div class="media-preview-badge">
                                    <i data-lucide="image"></i> Contains Media
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="post-preview-actions">
                            <button class="btn-approve" onclick="approvePost('<?= $post['id'] ?>')">Approve</button>
                            <button class="btn-reject" onclick="rejectPost('<?= $post['id'] ?>')">Decline</button>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    function getCsrfToken() {
        return window.APP_CONFIG ? window.APP_CONFIG.csrfHash : '';
    }

    function approvePost(postId) {
        $.ajax({
            url: BASE_URL + '/group/approve_post/' + postId,
            type: 'POST',
            headers: { 'X-CSRF-TOKEN': getCsrfToken() },
            success: function(res) {
                if(res.status === 'success') {
                    $(`#pending-post-${postId}`).fadeOut();
                }
            }
        });
    }

    function rejectPost(postId) {
        if(!confirm('Reject and delete this post?')) return;
        $.ajax({
            url: BASE_URL + '/group/reject_post/' + postId,
            type: 'POST',
            headers: { 'X-CSRF-TOKEN': getCsrfToken() },
            success: function(res) {
                if(res.status === 'success') {
                    $(`#pending-post-${postId}`).fadeOut();
                }
            }
        });
    }
</script>
<?= $this->endSection() ?>
