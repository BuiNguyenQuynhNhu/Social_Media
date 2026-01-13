<div class="post-card" data-id="<?= $post['id'] ?>">
    <div class="post-header">
        <div class="header-left" style="display:flex; gap:10px; align-items:center; flex:1;">
        <a href="<?= base_url('user/profile/'.$post['id_user']) ?>">
            <img src="<?= (isset($post['avatar']) && strpos($post['avatar'], 'http') === 0) ? $post['avatar'] : ($post['avatar'] ? base_url($post['avatar']) : base_url('ICON/avatar-default.svg')) ?>" class="avatar-medium">
        </a>
            <div class="post-info">
                <a href="<?= base_url('user/profile/'.$post['id_user']) ?>" class="author-name"><?= esc($post['firstname'] . ' ' . $post['lastname']) ?></a>
                <span class="timestamp"><?= date('M d, Y', strtotime($post['created_at'])) ?></span>
            </div>
        </div>
        <?php if (session()->get('user_id') == $post['id_user'] && (isset($showDelete) && $showDelete)): ?>
            <form action="<?= base_url('post/delete_post/' . $post['id']) ?>" method="post" onsubmit="return confirm('Do you really want to delete this post?')">
                <?= csrf_field() ?>
                <input type="hidden" name="_method" value="DELETE">
                <button type="submit" class="icon-btn-ghost"><i data-lucide="trash-2"></i></button>
            </form>
        <?php endif; ?>
    </div>
    
    <div class="post-content">
        <?= esc($post['content']) ?>
    </div>

    <!-- Media Grid -->
    <?php if (!empty($post['media'])): ?>
        <?php 
            $mediaCount = count($post['media']);
            $displayMedia = array_slice($post['media'], 0, 5);
            $gridClass = 'grid-' . min($mediaCount, 5);
        ?>
        <div class="post-media-grid <?= $gridClass ?>">
            <?php foreach ($displayMedia as $index => $item): ?>
                <div class="media-item <?= ($index === 4 && $mediaCount > 5) ? 'more-overlay' : '' ?>" 
                        data-more="+<?= $mediaCount - 5 ?>" 
                        onclick="openLightbox('<?= $post['id'] ?>', <?= $index ?>)">
                    <?php if (strpos($item['type'], 'video') !== false): ?>
                        <video src="<?= $item['url'] ?>"></video>
                    <?php else: ?>
                        <img src="<?= $item['url'] ?>" alt="Post Image">
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <div class="post-stats">
        <div class="stat-item">
            <i data-lucide="thumbs-up" class="w-4 h-4"></i>
            <span id="likes-count-<?= $post['id'] ?>">0</span>
        </div>
        <div class="stat-item" onclick="toggleComments('<?= $post['id'] ?>')">
            <span id="comments-count-<?= $post['id'] ?>">0 Comments</span>
        </div>
    </div>

    <div class="post-actions">
        <div class="reaction-wrapper">
            <button class="action-btn" id="btn-react-<?= $post['id'] ?>">
                <i data-lucide="thumbs-up"></i>
                <span id="reaction-btn-text-<?= $post['id'] ?>">Like</span>
            </button>
            <div class="reaction-box">
                <div class="reaction-icon" onclick="sendReaction('<?= $post['id'] ?>', 'like')">👍</div>
                <div class="reaction-icon" onclick="sendReaction('<?= $post['id'] ?>', 'love')">❤️</div>
                <div class="reaction-icon" onclick="sendReaction('<?= $post['id'] ?>', 'haha')">😆</div>
                <div class="reaction-icon" onclick="sendReaction('<?= $post['id'] ?>', 'wow')">😮</div>
                <div class="reaction-icon" onclick="sendReaction('<?= $post['id'] ?>', 'sad')">😢</div>
                <div class="reaction-icon" onclick="sendReaction('<?= $post['id'] ?>', 'angry')">😡</div>
            </div>
        </div>
        <button class="action-btn" onclick="toggleComments('<?= $post['id'] ?>')">
            <i data-lucide="message-square"></i>
            <span>Comment</span>
        </button>
        <button class="action-btn">
            <i data-lucide="share-2"></i>
            <span>Share</span>
        </button>
    </div>


    <!-- Comments Section -->
    <?= view('components/post/comment_section', ['post' => $post, 'avatarUrl' => $avatarUrl]) ?>
</div>
