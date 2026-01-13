<!-- Create Post Trigger -->
<div class="create-post-card" onclick="openCreatePostModal()">
    <img src="<?= $avatarUrl ?>" class="avatar-medium" style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover;">
    <div class="create-post-input">What's on your mind, <?= esc($user['firstname']) ?>?</div>
    <div class="create-post-icons">
        <i data-lucide="image" style="color: #45bd62;"></i>
        <i data-lucide="smile" style="color: #f7b928;"></i>
    </div>
</div>

<!-- Create Post Modal -->
<div id="createPostModal" class="modal" style="display: none;">
    <div class="modal-content" style="max-width: 500px;">
        <div class="modal-header">
            <h2>Create Post</h2>
            <button class="close-btn" onclick="closeCreatePostModal()">
                <i data-lucide="x"></i>
            </button>
        </div>

        <form action="<?= base_url('post/create_post') ?>" method="POST" enctype="multipart/form-data">
            <?= csrf_field() ?>
            <input type="hidden" name="group_id" id="modal-group-id" value="">
            
            <div class="modal-body">
                <div class="user-micro">
                    <img src="<?= $avatarUrl ?>" class="avatar-medium" style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover;">
                    <div>
                        <strong><?= esc($user['firstname'] . ' ' . $user['lastname']) ?></strong>
                        <div class="privacy-selector">
                            <i id="privacy-icon" data-lucide="globe" style="width: 12px; height: 12px;"></i>
                            <select name="privacy" onchange="updatePrivacyIcon(this)" style="border: none; background: #e4e6eb; padding: 2px 6px; border-radius: 4px; font-size: 13px;">
                                <option value="public" data-icon="globe">Public</option>
                                <option value="friends" data-icon="users">Friends</option>
                                <option value="private" data-icon="lock">Only me</option>
                            </select>
                        </div>
                    </div>
                </div>

                <textarea name="content" class="modal-body textarea" placeholder="What's on your mind, <?= esc($user['firstname']) ?>?" 
                          rows="6" required style="width: 100%; border: none; outline: none; font-size: 24px; resize: none; font-family: inherit;"></textarea>
                
                <div id="media-preview-container" style="display: none; grid-template-columns: repeat(auto-fill, minmax(150px, 1fr)); gap: 8px; margin-top: 12px;"></div>

                <div class="add-to-post">
                    <span style="font-weight: 600;">Add to your post</span>
                    <div style="display: flex; gap: 8px;">
                        <label for="post-file-input" style="cursor: pointer; padding: 8px; border-radius: 50%; transition: background 0.2s;">
                            <i data-lucide="image" style="color: #45bd62; width: 24px; height: 24px;"></i>
                        </label>
                        <input type="file" id="post-file-input" name="images[]" accept="image/*,video/*" multiple hidden onchange="previewPostFiles(this)">
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="submit" class="btn-primary">Post</button>
            </div>
        </form>
    </div>
</div>

<script>
function openCreatePostModal() {
    document.getElementById('createPostModal').style.display = 'flex';
    lucide.createIcons();
}

function closeCreatePostModal() {
    document.getElementById('createPostModal').style.display = 'none';
    document.getElementById('media-preview-area').innerHTML = '';
}

function updatePrivacyIcon(select) {
    const icon = document.getElementById('privacy-icon');
    const selectedOption = select.options[select.selectedIndex];
    const iconName = selectedOption.getAttribute('data-icon');
    icon.setAttribute('data-lucide', iconName);
    lucide.createIcons();
}
</script>

<style>
.create-post-card {
    background: white;
    border-radius: 8px;
    padding: 12px 16px;
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
    display: flex;
    align-items: center;
    gap: 12px;
    cursor: pointer;
    margin-bottom: 16px;
}

.create-post-input {
    flex: 1;
    background: #f0f2f5;
    padding: 10px 12px;
    border-radius: 50px;
    color: #65676b;
}

.create-post-input:hover {
    background: #e4e6eb;
}

.create-post-icons {
    display: flex;
    gap: 8px;
}

.privacy-selector {
    display: flex;
    align-items: center;
    gap: 4px;
    margin-top: 4px;
    font-size: 13px;
    color: #65676b;
}
</style>