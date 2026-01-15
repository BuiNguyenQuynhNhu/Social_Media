<div class="post-card" data-id="<?= $post['id'] ?>">
    <div class="post-header">
        <div class="header-left" style="display:flex; gap:10px; align-items:center; flex:1;">
        <a href="<?= base_url('user/profile/'.$post['id_user']) ?>">
            <?php $pAvatar = trim($post['avatar'] ?? ''); ?>
            <img src="<?= (strpos($pAvatar, 'http') === 0) ? $pAvatar : ($pAvatar ? base_url($pAvatar) : base_url('ICON/avatar-default.svg')) ?>" class="avatar-medium">
        </a>
            <div class="post-info">
                <a href="<?= base_url('user/profile/'.$post['id_user']) ?>" class="author-name"><?= esc($post['firstname'] . ' ' . $post['lastname']) ?></a>
                <?php if (!empty($post['group_name'])): ?>
                    <i data-lucide="chevron-right" style="width:16px; height:16px; margin:0 2px; color:#65676b;"></i>
                    <a href="<?= base_url('group/' . $post['group_id']) ?>" class="author-name"><?= esc($post['group_name']) ?></a>
                <?php endif; ?>
                <span class="timestamp"><?= date('M d, Y', strtotime($post['created_at'])) ?></span>
            </div>
        </div>
        <?php 
            $currentUserId = session()->get('user_id');
            $isPostOwner = $currentUserId == $post['id_user'];
            
            // STRICT Context-based delete rules
            $context = $context ?? 'feed'; // dashboard, profile, group_feed, group_manage
            
            $canDelete = false;
            
            // Rule 1: In PROFILE context, only owner can delete
            if ($context === 'profile' && $isPostOwner) {
                $canDelete = true;
            }
            
            // Rule 2: In GROUP contexts, only admins can delete
            if (($context === 'group_feed' || $context === 'group_manage') && isset($isAdmin) && $isAdmin) {
                $canDelete = true;
            }
            
            // Dashboard and other contexts: NO delete option
        ?>
        
        <div class="post-menu-container" style="position: relative;">
            <?php if ($canDelete): ?>
            <button onclick="togglePostMenu('<?= $post['id'] ?>')" class="menu-trigger icon-btn-ghost">
                <i data-lucide="more-horizontal"></i>
            </button>
            <div id="menu-<?= $post['id'] ?>" class="menu-dropdown" style="display: none; position: absolute; right: 0; top: 100%; background: white; box-shadow: 0 4px 12px rgba(0,0,0,0.15); border-radius: 8px; width: 200px; z-index: 100; padding: 5px;">
                
                <!-- Edit functionality removed as per user request -->

                <form action="<?= base_url('post/delete_post/' . $post['id']) ?>" method="post" onsubmit="return confirm('Are you sure you want to delete this post?');">
                    <?= csrf_field() ?>
                    <button type="submit" class="menu-item" style="width: 100%; display: flex; align-items: center; gap: 10px; padding: 10px; background: none; border: none; cursor: pointer; text-align: left; font-size: 14px; border-radius: 5px; transition: background 0.2s; color: #e41e3f;">
                        <i data-lucide="trash-2" style="width: 18px;"></i> Delete Post
                    </button>
                </form>
            </div>
            <?php endif; ?>
        </div>
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
            <button class="action-btn" id="btn-react-<?= $post['id'] ?>" onclick="sendReaction('<?= $post['id'] ?>', 'like')">
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
