<div class="fb-card create-post-trigger" onclick="document.getElementById('post-modal').style.display='flex'">
    <img src="<?= $avatarUrl ?>" class="small-avatar">
    <div class="search-style-input">What's on your mind?</div>
</div>

<div id="post-modal" class="modal-overlay" style="display: none;">
    <div class="fb-card modal-content post-modal-card">
    <div class="section-header">
        <h3>Create Post</h3>
        <button class="icon-circle mini" onclick="document.getElementById('post-modal').style.display='none'">
            <i data-lucide="x"></i>
        </button>
    </div>

    <form action="<?= base_url('post/create_post') ?>" method="POST" enctype="multipart/form-data">
        <?= csrf_field() ?>
        <div class="post-modal-body">
    <div class="user-meta">
        <img src="<?= $avatarUrl ?>" class="small-avatar">
        <div class="meta-info">
            <span class="user-name"><?= esc($user['firstname'] . ' ' . $user['lastname']) ?></span>
            <div class="privacy-selector">
                <i id="privacy-icon" data-lucide="globe" size="12"></i> 
                <select name="privacy" onchange="updatePrivacyIcon(this)">
                    <option value="public" data-icon="globe">Public</option>
                    <option value="friends" data-icon="users">Friends</option>
                    <option value="private" data-icon="lock">Only me</option>
                </select>
            </div>
        </div>
    </div>

    <textarea name="content" class="post-textarea" rows="6" 
              placeholder="What's on your mind, <?= esc($user['firstname']) ?>?" required></textarea>
    
    <div id="media-preview-container" class="preview-media-grid" style="display: none;"></div>
</div><div class="action-icons">
                    <label for="file-input" style="cursor: pointer;">
                        <i data-lucide="image" class="text-green"></i>
                    </label>
                    <input type="file" id="file-input" name="images[]" 
                           accept="image/*,video/*" multiple hidden 
                           onchange="previewPostFiles(this)">
                </div>

        <button type="submit" class="btn btn-blue btn-full mt-10">Post</button>
    </form>
</div>
</div>