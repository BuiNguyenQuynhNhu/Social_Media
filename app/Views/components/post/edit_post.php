<!-- Edit Post Modal -->
<div id="editPostModal" class="modal" style="display: none;">
    <div class="modal-content" style="max-width: 500px;">
        <div class="modal-header">
            <h2>Edit Post</h2>
            <button class="close-btn" onclick="closeEditPostModal()">
                <i data-lucide="x"></i>
            </button>
        </div>

        <form id="editPostForm" action="" method="POST">
            <?= csrf_field() ?>
            <input type="hidden" name="_method" value="POST"> <!-- CodeIgniter uses POST for updates usually, but could be PUT -->
            
            <div class="modal-body">
                <div class="user-micro">
                    <img src="" id="edit-post-avatar" class="avatar-medium" style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover;">
                    <div>
                        <strong id="edit-post-username"></strong>
                        <div class="privacy-selector">
                            <i id="edit-privacy-icon" data-lucide="globe" style="width: 12px; height: 12px;"></i>
                            <select name="privacy" id="edit-post-privacy" onchange="updateEditPrivacyIcon(this)" style="border: none; background: #e4e6eb; padding: 2px 6px; border-radius: 4px; font-size: 13px;">
                                <option value="public" data-icon="globe">Public</option>
                                <option value="friends" data-icon="users">Friends</option>
                                <option value="private" data-icon="lock">Only me</option>
                            </select>
                        </div>
                    </div>
                </div>

                <textarea name="content" id="edit-post-content" class="modal-body textarea" 
                          rows="6" required style="width: 100%; border: none; outline: none; font-size: 24px; resize: none; font-family: inherit; margin-top: 16px;"></textarea>
            </div>

            <div class="modal-footer">
                <button type="submit" class="btn-primary">Save</button>
            </div>
        </form>
    </div>
</div>

<script>
function openEditPostModal(postId, content, privacy, avatar, username) {
    const modal = document.getElementById('editPostModal');
    const form = document.getElementById('editPostForm');
    
    // Set Action URL
    form.action = `<?= base_url('post/update_post/') ?>/${postId}`;
    
    // Set Values
    document.getElementById('edit-post-content').value = content;
    document.getElementById('edit-post-privacy').value = privacy;
    document.getElementById('edit-post-avatar').src = avatar;
    document.getElementById('edit-post-username').textContent = username;
    
    // Update privacy icon
    updateEditPrivacyIcon(document.getElementById('edit-post-privacy'));

    modal.style.display = 'flex';
    lucide.createIcons();
}

function closeEditPostModal() {
    document.getElementById('editPostModal').style.display = 'none';
}

function updateEditPrivacyIcon(select) {
    const icon = document.getElementById('edit-privacy-icon');
    const selectedOption = select.options[select.selectedIndex];
    const iconName = selectedOption.getAttribute('data-icon');
    icon.setAttribute('data-lucide', iconName);
    lucide.createIcons();
}
</script>
