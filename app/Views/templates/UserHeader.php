<?php 
    $isOwnProfile = (session()->get('user_id') == $user['id']); 
?>
<div class="profile-header-card">
    <div class="cover-photo" style="background-image: url('<?= $coverUrl ?>'); background-size: cover; background-position: center;">
        <?php if($isOwnProfile): ?>
            <button class="edit-cover-btn" onclick="openEditModal('cover-section')">
                <i data-lucide="camera"></i> Edit Cover Photo
            </button>
        <?php endif; ?>
    </div>

    <div class="header-content">
        <div class="user-info-main">
            <img src="<?= $avatarUrl ?>" class="main-avatar">
            <div class="name-details">
                <h1><?= esc($user['firstname'] . ' ' . $user['lastname']) ?></h1>
                <div class="profile-nav">
                    <div class="nav-tab active" onclick="switchTab('posts', this)">
                        <span><?= count($posts ?? []) ?> Posts</span>
                        <div class="nav-underline"></div>
                    </div>
                    <a href="<?= base_url('user/update_profile/'.$user['id']) ?>" class="nav-tab" style="text-decoration: none;">
                        <span><?= count($friends ?? []) ?> Friends</span>
                        <div class="nav-underline"></div>
                    </a>
                </div>
            </div>
        </div>
        
        <div class="header-actions">
            <?php if($isOwnProfile): ?>
                <button class="btn btn-blue"><i data-lucide="plus"></i> Add Post</button>
                <button class="btn btn-gray" onclick="openEditModal('all')">
                    <i data-lucide="edit-2"></i> Edit Profile
                </button>
            <?php endif; ?>
        </div>
    </div>
</div>

<div id="edit-modal" class="modal-overlay" style="display: none;">
    <div class="modal-content fb-card">
        <div class="section-header">
            <h3>Edit Profile</h3>
            <button class="icon-circle mini" onclick="document.getElementById('edit-modal').style.display='none'">
                <i data-lucide="x"></i>
            </button>
        </div>
        
        <form action="<?= base_url('user/update_profile/'.$user['id']) ?>" method="POST" class="modal-body" enctype="multipart/form-data">
            <?= csrf_field() ?>
            <input type="hidden" name="_method" value="PUT">

            <div class="edit-section" id="avatar-section">
                <div class="section-header">
                    <h4>Profile Picture</h4>
                    <label for="avatar-upload" class="btn-text">Edit</label>
                    <input type="file" id="avatar-upload" name="avatar" hidden onchange="previewImage(this, 'avatar-preview')">
                </div>
                <div class="preview-center">
                    <img src="<?= $avatarUrl ?>" class="main-avatar large" id="avatar-preview">
                </div>
            </div>

            <div class="edit-section" id="cover-section">
                <div class="section-header">
                    <h4>Cover Photo</h4>
                    <label for="cover-upload" class="btn-text">Edit</label>
                    <input type="file" id="cover-upload" name="cover" hidden onchange="previewImage(this, 'cover-preview')">
                </div>
                <div class="cover-preview-box">
                    <img src="<?= $coverUrl ?>" class="preview-cover" id="cover-preview">
                </div>
            </div>

            <div class="edit-grid">
                <div class="form-group">
                    <label>First name</label>
                    <input type="text" name="firstname" value="<?= esc($user['firstname']) ?>" class="fb-input">
                </div>
                <div class="form-group">
                    <label>Last name</label>
                    <input type="text" name="lastname" value="<?= esc($user['lastname']) ?>" class="fb-input">
                </div>
                <div class="form-group">
                    <label>Gender</label>
                    <select name="gender" class="fb-input">
                        <option value="Male" <?= $user['gender'] == 'Male' ? 'selected' : '' ?>>Male</option>
                        <option value="Female" <?= $user['gender'] == 'Female' ? 'selected' : '' ?>>Female</option>
                        <option value="Other" <?= $user['gender'] == 'Other' ? 'selected' : '' ?>>Other</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Date of birth</label>
                    <input type="date" name="birth" value="<?= esc($user['birth']) ?>" class="fb-input">
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-blue btn-full">Save Changes</button>
            </div>
        </form>
    </div>
</div>
    <script src="<?= base_url('JS/navbar.js') ?>"></script>
    <script>
        lucide.createIcons();
        
        function switchTab(tabName, element) {
            document.querySelectorAll('.nav-tab').forEach(tab => tab.classList.remove('active'));
            element.classList.add('active');

            document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
            document.getElementById(tabName + '-container').classList.add('active');
        }

        window.onclick = function(e) {
            if (e.target.className === 'modal-overlay') e.target.style.display = 'none';
        }

        function openEditModal(mode) {
        const modal = document.getElementById('edit-modal');
        const coverSection = document.getElementById('cover-section');
        const avatarSection = document.getElementById('avatar-section');
        const infoSection = document.querySelector('.edit-grid');
        const modalTitle = modal.querySelector('h3');

        modal.style.display = 'flex';

        if (mode === 'cover-section') {
            modalTitle.innerText = "Update Cover Photo";
            coverSection.style.display = 'block';
            avatarSection.style.display = 'none';
            infoSection.style.display = 'none';
        } else {
            modalTitle.innerText = "Edit Profile";
            coverSection.style.display = 'none';
            avatarSection.style.display = 'block';
            infoSection.style.display = 'grid';
        }
    }

    function closeModal() {
        document.getElementById('edit-modal').style.display = 'none';
    }

        function previewImage(input, previewId) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById(previewId).src = e.target.result;
                }
                reader.readAsDataURL(input.files[0]);
            }
        }

        window.onclick = function(e) {
            const modal = document.getElementById('edit-modal');
            if (e.target === modal) closeModal();
        }

        function switchTab(tabName, element) {
            document.querySelectorAll('.nav-tab').forEach(tab => tab.classList.remove('active'));
            element.classList.add('active');

            document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
            const target = document.getElementById(tabName + '-container');
            if(target) target.classList.add('active');
        }
        </script>
</body>
</html>