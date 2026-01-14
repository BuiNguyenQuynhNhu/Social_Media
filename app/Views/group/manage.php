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
    
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        .manage-container {
            max-width: 1000px;
            margin: 20px auto;
            display: flex;
            gap: 20px;
            padding: 0 20px;
        }
        .manage-sidebar {
            width: 250px;
            background: white;
            border-radius: 8px;
            padding: 16px;
            height: fit-content;
            box-shadow: 0 1px 2px rgba(0,0,0,0.1);
        }
        .manage-content {
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 20px;
        }
        .manage-menu-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px;
            border-radius: 6px;
            color: #050505;
            text-decoration: none;
            transition: background 0.2s;
            cursor: pointer;
        }
        .manage-menu-item:hover, .manage-menu-item.active {
            background: #f0f2f5;
        }
        .card {
            background: white;
            border-radius: 8px;
            box-shadow: 0 1px 2px rgba(0,0,0,0.1);
            padding: 20px;
        }
        .card h2 { margin-top: 0; font-size: 20px; }
        
        .request-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 12px 0;
            border-bottom: 1px solid #f0f2f5;
        }
        .request-user { display: flex; align-items: center; gap: 12px; }
        .request-actions { display: flex; gap: 8px; }
        .btn-approve { background: #e7f3ff; color: #1877f2; border: none; padding: 6px 12px; border-radius: 6px; cursor: pointer; font-weight: 600; }
        .btn-decline { background: #f0f2f5; color: #050505; border: none; padding: 6px 12px; border-radius: 6px; cursor: pointer; font-weight: 600; }
    </style>
</head>
<body class="background">

<?= view('components/user/navbar', ['avatarUrl' => $avatarUrl]) ?>

<div class="manage-container">
    <div class="manage-sidebar">
        <h3 style="margin-top: 0; margin-bottom: 16px;">Manager Tools</h3>
        <a href="#overview" class="manage-menu-item active">
            <i data-lucide="bar-chart-2"></i> Overview
        </a>
        <a href="#member_requests" class="manage-menu-item">
            <i data-lucide="user-plus"></i> Member Requests
        </a>
        <a href="#pending_posts" class="manage-menu-item">
            <i data-lucide="file-text"></i> Pending Posts
        </a>
        <a href="<?= base_url('group/' . $group['id'] . '/settings') ?>" class="manage-menu-item">
            <i data-lucide="settings"></i> Group Settings
        </a>
        <hr style="border: 0; border-top: 1px solid #e4e6eb; margin: 16px 0;">
        <a href="<?= base_url('group/' . $group['id']) ?>" class="manage-menu-item">
            <i data-lucide="arrow-left"></i> Back to Group
        </a>
    </div>

    <div class="manage-content">
        <!-- Dashboard Overview -->
        <div class="card" id="overview">
            <h2>Overview</h2>
            <div style="display: flex; gap: 20px;">
                <div style="flex: 1; padding: 16px; background: #fafafa; border-radius: 8px; text-align: center;">
                    <div style="font-size: 24px; font-weight: 700;"><?= $group['member_count'] ?></div>
                    <div style="color: #65676b;">Members</div>
                </div>
                 <!-- Placeholder stats -->
                 <div style="flex: 1; padding: 16px; background: #fafafa; border-radius: 8px; text-align: center;">
                    <div style="font-size: 24px; font-weight: 700;">0</div>
                    <div style="color: #65676b;">New Posts Today</div>
                </div>
            </div>
        </div>

        <!-- Pending Posts -->
        <div class="card" id="pending_posts">
            <h2>Pending Posts</h2>
            <?php if (empty($pending_posts)): ?>
                <p style="color: #65676b; font-style: italic;">No pending posts.</p>
            <?php else: ?>
                <div class="pending-list" style="display:flex; flex-direction:column; gap:12px;">
                    <?php foreach ($pending_posts as $post): ?>
                        <div class="request-item" id="pending-post-<?= $post['id'] ?>">
                            <div class="request-user">
                                <img src="<?= (isset($post['avatar']) && strpos($post['avatar'], 'http') === 0) ? $post['avatar'] : ($post['avatar'] ? base_url($post['avatar']) : base_url('ICON/avatar-default.svg')) ?>" class="avatar-small" style="width:40px; height:40px; border-radius:50%;">
                                <div>
                                    <div style="font-weight:600;"><?= esc($post['firstname'] . ' ' . $post['lastname']) ?></div>
                                    <div style="font-size:13px; color:#65676b;"><?= date('M d, H:i', strtotime($post['created_at'])) ?></div>
                                    <p style="margin:4px 0 0 0;"><?= esc(substr($post['content'], 0, 100)) . (strlen($post['content'])>100 ? '...' : '') ?></p>
                                </div>
                            </div>
                            <div class="request-actions">
                                <button class="btn-approve" onclick="approvePost('<?= $post['id'] ?>')">Approve</button>
                                <button class="btn-decline" onclick="rejectPost('<?= $post['id'] ?>')">Decline</button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

    const GROUP_ID = '<?= $group['id'] ?>';
    const BASE_URL = '<?= base_url() ?>';

    $(document).ready(function() {
        // Handle hash navigation
        function loadTabFromHash() {
            const hash = window.location.hash || '#overview';
            $('.manage-menu-item').removeClass('active');
            $(`a[href="${hash}"]`).addClass('active');
            
            $('.card').hide(); // Hide all sections
            $(hash).show(); // Show active section
        }

        loadTabFromHash();
        $(window).on('hashchange', loadTabFromHash);
    });

    function approvePost(postId) {
        $.post(BASE_URL + '/group/approve_post/' + postId, function(res){
            if(res.status === 'success') {
                $(`#pending-post-${postId}`).fadeOut();
            }
        });
    }

    function rejectPost(postId) {
        if(!confirm('Reject and delete this post?')) return;
        $.post(BASE_URL + '/group/reject_post/' + postId, function(res){
            if(res.status === 'success') {
                $(`#pending-post-${postId}`).fadeOut();
            }
        });
    }

</body>
</html>
