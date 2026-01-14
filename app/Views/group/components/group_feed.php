
<div class="group-feed">
    <?php if ($is_member): ?>
         <?= view('components/post/create_post', ['avatarUrl' => $avatarUrl, 'user' => $user, 'groupId' => $group['id']]) ?>
    <?php endif; ?>
    
    <!-- Posts Feed -->
    <?php if (!empty($posts)): ?>
        <?php foreach ($posts as $post): ?>
            <?= view('components/post/post_item', ['post' => $post, 'avatarUrl' => $avatarUrl, 'context' => 'group_feed']) ?>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="empty-state">
            <i data-lucide="newspaper" style="width: 60px; height: 60px; color: #65676b;"></i>
            <p>No posts yet. <?= $is_member ? 'Be the first to post!' : 'Join the group to see posts.' ?></p>
        </div>
    <?php endif; ?>
</div>


