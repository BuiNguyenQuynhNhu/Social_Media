<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="base_url" content="<?= rtrim(base_url(), '/') ?>">
    <meta name="user_id" content="<?= $user_id ?>">
    <meta name="group_id" content="<?= $group['id'] ?>">
    <title>Settings - <?= esc($group['name']) ?> | Social Media</title>
    
    <link rel="stylesheet" href="<?= base_url('CSS/navbar.css') ?>">
    <link rel="stylesheet" href="<?= base_url('CSS/overall.css') ?>">
    <link rel="stylesheet" href="<?= base_url('CSS/group/group_layout.css') ?>">
    
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <link rel="stylesheet" href="<?= base_url('CSS/group/group_manage.css') ?>">
</head>
<body class="background">

<?= view('components/user/navbar', ['avatarUrl' => $avatarUrl]) ?>

<div class="settings-container">
    <div class="settings-header">
        <h1>Group Settings</h1>
        <a href="<?= base_url('group/' . $group['id']) ?>" class="back-link"><i data-lucide="arrow-left"></i> Back to Group</a>
    </div>
    
    <div class="settings-body">
        <!-- Basic Info -->
        <div class="form-section">
            <label>Name</label>
            <input type="text" id="group-name-input" class="form-input" value="<?= esc($group['name']) ?>">
        </div>
        <div class="form-section">
            <label>Description</label>
            <textarea id="group-description-input" class="form-input" rows="3"><?= esc($group['description'] ?? '') ?></textarea>
        </div>
        
        <!-- Group Images -->
        <div class="form-section">
            <label>Group Avatar</label>
            <input type="file" id="group-avatar-input" class="form-input" accept="image/*">
            <?php if (!empty($group['avatar'])): ?>
                <img src="<?= $group['avatar'] ?>" alt="Current avatar" style="max-width: 100px; margin-top: 8px; border-radius: 8px;">
            <?php endif; ?>
        </div>
        
        <div class="form-section">
            <label>Cover Photo</label>
            <input type="file" id="group-cover-input" class="form-input" accept="image/*">
            <?php if (!empty($group['cover_photo'])): ?>
                <img src="<?= $group['cover_photo'] ?>" alt="Current cover" style="max-width: 200px; margin-top: 8px; border-radius: 8px;">
            <?php endif; ?>
        </div>
        
        <div class="form-section">
            <button class="btn-save" onclick="updateGroupInfo()">Save Changes</button>
        </div>
        
        <hr style="border: 0; border-top: 1px solid #e4e6eb; margin: 24px 0;">

        <!-- Post Requirements -->
        <div class="form-section">
            <label>Post Approval</label>
            <div class="settings-row">
                <div>
                    <strong>Require Post Approval</strong>
                    <div style="font-size: 13px; color: #65676b;">Admin must approve posts before they are visible.</div>
                </div>
                <label class="switch" style="position: relative; display: inline-block; width: 40px; height: 24px;">
                    <input type="checkbox" id="post-approval-toggle" <?= (!empty($group['post_requirement']) && $group['post_requirement'] == 1) ? 'checked' : '' ?> onchange="updatePostRequirements()">
                    <span class="slider round" style="position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; background-color: #ccc; transition: .4s; border-radius: 34px;"></span>
                    <style>
                        .switch input { opacity: 0; width: 0; height: 0; }
                        .slider:before { position: absolute; content: ""; height: 16px; width: 16px; left: 4px; bottom: 4px; background-color: white; transition: .4s; border-radius: 50%; }
                        input:checked + .slider { background-color: #1877f2; }
                        input:checked + .slider:before { transform: translateX(16px); }
                    </style>
                </label>
            </div>
        </div>

        <hr style="border: 0; border-top: 1px solid #e4e6eb; margin: 24px 0;">

        <!-- Danger Zone -->
        <h3 style="color: #dc3545;">Danger Zone</h3>
        <button class="btn-save" style="background: white; color: #dc3545; border: 1px solid #dc3545;" onclick="deleteGroup()">Delete Group</button>
    </div>
</div>


<script>
    const BASE_URL = '<?= rtrim(base_url(), '/') ?>';
    const GROUP_ID = '<?= $group['id'] ?>';
    lucide.createIcons();
</script>
<script src="<?= base_url('JS/group/settings.js') ?>"></script>

</body>
</html>
