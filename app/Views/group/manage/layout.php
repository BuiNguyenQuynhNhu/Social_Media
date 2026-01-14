<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="base_url" content="<?= rtrim(base_url(), '/') ?>">
    <meta name="user_id" content="<?= $user_id ?>">
    <meta name="group_id" content="<?= $group['id'] ?>">
    <title>Manage - <?= esc($group['name']) ?> | Social Media</title>
    
    <link rel="stylesheet" href="<?= base_url('CSS/navbar.css') ?>">
    <link rel="stylesheet" href="<?= base_url('CSS/overall.css') ?>">
    <link rel="stylesheet" href="<?= base_url('CSS/group/group_layout.css') ?>">
    <link rel="stylesheet" href="<?= base_url('CSS/group/group_manage.css') ?>">
    
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body class="background">

<?= view('components/user/navbar', ['avatarUrl' => $avatarUrl]) ?>

<div class="manage-container">
    <div class="manage-sidebar">
        <div class="sidebar-header">
            <h3>Manager Tools</h3>
            <div class="group-preview">
                <img src="<?= (isset($group['avatar']) && strpos($group['avatar'], 'http') === 0) ? $group['avatar'] : ($group['avatar'] ? base_url($group['avatar']) : base_url('ICON/group-default.png')) ?>" class="avatar-small">
                <span><?= esc($group['name']) ?></span>
            </div>
        </div>
        
        <nav class="sidebar-nav">
            <a href="<?= base_url('group/' . $group['id'] . '/manage/overview') ?>" class="nav-item <?= $active_page === 'overview' ? 'active' : '' ?>">
                <i data-lucide="bar-chart-2"></i> Overview
            </a>
            <a href="<?= base_url('group/' . $group['id'] . '/manage/members') ?>" class="nav-item <?= $active_page === 'members' ? 'active' : '' ?>">
                <i data-lucide="users"></i> Member Requests
            </a>
            <a href="<?= base_url('group/' . $group['id'] . '/manage/posts') ?>" class="nav-item <?= $active_page === 'posts' ? 'active' : '' ?>">
                <i data-lucide="file-text"></i> Pending Posts
            </a>
             <a href="<?= base_url('group/' . $group['id'] . '/manage/requirements') ?>" class="nav-item <?= $active_page === 'requirements' ? 'active' : '' ?>">
                <i data-lucide="clipboard-list"></i> Participation Questions
            </a>
            <a href="<?= base_url('group/' . $group['id'] . '/settings') ?>" class="nav-item">
                <i data-lucide="settings"></i> Group Settings
            </a>
        </nav>
        
        <div class="sidebar-footer">
            <a href="<?= base_url('group/' . $group['id']) ?>" class="nav-item back-btn">
                <i data-lucide="arrow-left"></i> Back to Group
            </a>
        </div>
    </div>

    <div class="manage-content">
        <?= $this->renderSection('content') ?>
    </div>
</div>

<script>
    lucide.createIcons();
    const GROUP_ID = '<?= $group['id'] ?>';
    const BASE_URL = '<?= base_url() ?>';
</script>
<?= $this->renderSection('scripts') ?>
</body>
</html>
