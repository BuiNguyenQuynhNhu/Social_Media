<?= view('components/user/navbar', ['avatarUrl' => $avatarUrl]) ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Search Results | Social Media</title>
    <link rel="stylesheet" href="<?= base_url('CSS/overall.css') ?>">
    <link rel="stylesheet" href="<?= base_url('CSS/navbar.css') ?>">
    <link rel="stylesheet" href="<?= base_url('CSS/dashboard.css') ?>">
    <link rel="stylesheet" href="<?= base_url('CSS/post/post.css') ?>">
    <link rel="stylesheet" href="<?= base_url('CSS/post/comment.css') ?>">
    <link href="https://fonts.googleapis.com/css2?family=Segoe+UI:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        .filter-item {
            display: flex;
            align-items: center;
            padding: 12px 10px;
            cursor: pointer;
            border-radius: 8px;
            font-weight: 500;
            font-size: 15px;
            color: #050505;
            transition: background 0.2s;
            margin-bottom: 2px;
        }
        .filter-item:hover {
            background: #f0f2f5;
        }
        .filter-item.active {
            background: #e7f3ff;
            color: #1877f2;
        }
        .filter-item i {
            margin-right: 8px;
        }
        .result-section {
            background: white;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 16px;
            box-shadow: 0 1px 2px rgba(0,0,0,0.1);
        }
        .section-title {
            font-size: 20px;
            font-weight: 700;
            margin-bottom: 16px;
            color: #050505;
        }
        .user-result, .group-result {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 8px;
            border-radius: 8px;
            transition: 0.2s;
            margin-bottom: 4px;
        }
        .user-result:hover, .group-result:hover {
            background: #f0f2f5;
        }
        .info h3 {
            font-size: 15px;
            margin: 0;
            font-weight: 600;
        }
        .info span {
            font-size: 13px;
            color: #65676b;
        }
    </style>
</head>
<body class="background">

<?= view('components/user/navbar', ['avatarUrl' => $avatarUrl]) ?>

<div class="dashboard-container" style="padding-top: 20px;">
    <div class="search-sidebar" style="position:sticky; top:80px; height:fit-content; background:white; border-radius:8px; padding:8px; width:280px; box-shadow:0 1px 2px rgba(0,0,0,0.1); margin-right:20px;">
        <div style="padding: 10px; font-weight:700; font-size:20px; margin-bottom:10px; color:#050505;">Search Filters</div>
        <div class="filter-item active" onclick="showSection('all')">
            <i data-lucide="layout-grid"></i> All
        </div>
        <div class="filter-item" onclick="showSection('users')">
            <i data-lucide="users"></i> People
        </div>
        <div class="filter-item" onclick="showSection('groups')">
            <i data-lucide="users"></i> Groups
        </div>
        <div class="filter-item" onclick="showSection('posts')">
            <i data-lucide="file-text"></i> Posts
        </div>
    </div>

    <div class="search-main" style="flex:1; max-width:700px;">
        <?php if (empty($results['users']) && empty($results['groups']) && empty($results['posts'])): ?>
            <div class="result-section" style="text-align:center; padding:40px;">
                <p>No results found for "<?= esc($results['query']) ?>"</p>
            </div>
        <?php else: ?>
            
            <!-- People -->
            <?php if (!empty($results['users'])): ?>
            <div id="sec-users" class="result-section">
                <div class="section-title">People</div>
                <?php foreach ($results['users'] as $u): ?>
                    <div class="user-result">
                        <a href="<?= $u['url'] ?>">
                            <img src="<?= $u['avatar'] ?>" style="width:60px; height:60px; border-radius:50%; object-fit:cover;">
                        </a>
                        <div class="info" style="flex:1;">
                            <a href="<?= $u['url'] ?>" style="text-decoration:none; color:inherit;">
                                <h3><?= $u['name'] ?></h3>
                            </a>
                            <span><?= $u['status'] === 'friend' ? 'Friend' : 'User' ?></span>
                        </div>
                        <a href="<?= $u['url'] ?>" class="btn-primary" style="padding:6px 12px; font-size:14px; text-decoration:none;">View Profile</a>
                    </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>

            <!-- Groups -->
            <?php if (!empty($results['groups'])): ?>
            <div id="sec-groups" class="result-section">
                <div class="section-title">Groups</div>
                <?php foreach ($results['groups'] as $g): ?>
                    <div class="group-result">
                        <a href="<?= $g['url'] ?>">
                            <img src="<?= $g['avatar'] ?>" style="width:60px; height:60px; border-radius:8px; object-fit:cover;">
                        </a>
                        <div class="info" style="flex:1;">
                            <a href="<?= $g['url'] ?>" style="text-decoration:none; color:inherit;">
                                <h3><?= $g['name'] ?></h3>
                            </a>
                            <span><?= ucfirst($g['privacy']) ?> Group</span>
                        </div>
                        <a href="<?= $g['url'] ?>" class="btn-primary" style="padding:6px 12px; font-size:14px; text-decoration:none;">View Group</a>
                    </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>

            <!-- Posts -->
            <?php if (!empty($results['posts'])): ?>
            <div id="sec-posts">
                <div class="section-title" style="margin-left:10px;">Posts</div>
                <?php foreach ($results['posts'] as $post): ?>
                    <?= view('components/post/post_item', ['post' => $post, 'avatarUrl' => $avatarUrl]) ?>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>

        <?php endif; ?>
    </div>
</div>

<script>
    function showSection(type) {
        document.querySelectorAll('.filter-item').forEach(el => el.classList.remove('active'));
        event.target.classList.add('active');

        const sections = ['users', 'groups', 'posts'];
        
        if (type === 'all') {
            sections.forEach(s => {
                const el = document.getElementById('sec-' + s);
                if (el) el.style.display = 'block';
            });
        } else {
            sections.forEach(s => {
                const el = document.getElementById('sec-' + s);
                if (el) el.style.display = (s === type) ? 'block' : 'none';
            });
        }
    }
    
    // Config for posts
    window.BASE_URL = "<?= rtrim(base_url(), '/') ?>";
    window.APP_CONFIG = {
        baseURL: window.BASE_URL,
        userId: '<?= $user_id ?>',
        csrfTokenName: '<?= csrf_token() ?>',     
        csrfHash: '<?= csrf_hash() ?>'
    };
    if(window.lucide) lucide.createIcons();
</script>
<script src="<?= base_url('JS/post/utils.js') ?>"></script>
<script src="<?= base_url('JS/post/reactions.js') ?>"></script>
<script src="<?= base_url('JS/post/comments.js') ?>"></script>
<script src="<?= base_url('JS/post.js') ?>"></script>
<script src="<?= base_url('JS/post/init.js') ?>"></script>

</body>
</html>
