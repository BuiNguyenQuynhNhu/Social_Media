<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="base_url" content="<?= rtrim(base_url(), '/') ?>">
    <meta name="user_id" content="<?= $user_id ?>">
    <meta name="group_id" content="<?= $group['id'] ?? '' ?>">
    <title><?= isset($group) ? esc($group['name']) : 'Groups' ?> | Social Media</title>
    
    <!-- Stylesheets -->
    <link rel="stylesheet" href="<?= base_url('CSS/navbar.css') ?>">
    <link rel="stylesheet" href="<?= base_url('CSS/overall.css') ?>">
    <link rel="stylesheet" href="<?= base_url('CSS/group/group_layout.css') ?>">
    <link rel="stylesheet" href="<?= base_url('CSS/group/group_common.css') ?>">
    <link rel="stylesheet" href="<?= base_url('CSS/group/group_feed.css') ?>">
    <link rel="stylesheet" href="<?= base_url('CSS/group/group_members.css') ?>">
    <link rel="stylesheet" href="<?= base_url('CSS/group/group_about.css') ?>">
    <link rel="stylesheet" href="<?= base_url('CSS/post/post.css') ?>">
    <link rel="stylesheet" href="<?= base_url('CSS/post/comment.css') ?>">
    
    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body class="background">

<?= view('components/user/navbar', ['avatarUrl' => $avatarUrl]) ?>

<div class="group-page-container">
    <!-- Group Header (Shared) -->
    <?= view('group/components/group_header', [
        'group' => $group,
        'is_member' => $is_member ?? false,
        'is_admin' => $is_admin ?? false,
        'active_tab' => $active_tab ?? 'feed'
    ]) ?>
    
    <div class="group-content-area">
        <!-- Specific Page Content Injected Here -->
        <?= $this->renderSection('content') ?>
    </div>
    
    <!-- Group Settings Modal (Admin Only) -->
    <?php if ($is_admin ?? false): ?>
        <?= view('group/components/group_settings', ['group' => $group]) ?>
    <?php endif; ?>
</div>

<!-- JavaScript -->
<script src="<?= base_url('JS/post/utils.js') ?>"></script>
<script src="<?= base_url('JS/post/reactions.js') ?>"></script>
<script src="<?= base_url('JS/post/comments.js') ?>"></script>
<script src="<?= base_url('JS/post.js') ?>"></script>
<script src="<?= base_url('JS/group/list.js') ?>"></script>
<script src="<?= base_url('JS/group/members.js') ?>"></script>
<script src="<?= base_url('JS/group/actions.js') ?>"></script>
<script src="<?= base_url('JS/group/settings.js') ?>"></script>
<script src="<?= base_url('JS/group.js') ?>"></script>

</body>
</html>
