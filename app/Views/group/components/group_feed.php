<div class="group-feed" id="group-feed-content">
    <?php if ($is_member): ?>
        <!-- Create Post Section -->
        <div class="group-create-post">
            <img src="<?= $avatarUrl ?>" class="avatar-medium" alt="Your avatar">
            <button class="create-post-btn" onclick="openGroupPostModal()">
                What's on your mind?
            </button>
        </div>
    <?php endif; ?>
    
    <!-- Posts Feed -->
    <div class="group-posts" id="group-posts">
        <?php if (!empty($posts)): ?>
            <?php foreach ($posts as $post): ?>
                <?= view('components/user/post_item', ['post' => $post, 'avatarUrl' => $avatarUrl]) ?>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="empty-state">
                <i data-lucide="newspaper" style="width: 60px; height: 60px; color: #65676b;"></i>
                <p>No posts yet. <?= $is_member ? 'Be the first to post!' : 'Join the group to see posts.' ?></p>
            </div>
        <?php endif; ?>
    </div>
</div>
