<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    
    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        window.BASE_URL = '<?= rtrim(base_url(), '/') ?>';
        const APP_CONFIG = {
            baseURL: window.BASE_URL,
            userId: '<?= session()->get('user_id') ?>',
            csrfTokenName: '<?= csrf_token() ?>',     
            csrfHash: '<?= csrf_hash() ?>'
        };
    </script>
    
    <link rel="stylesheet" href="<?= base_url('CSS/navbar.css') ?>">
    <link rel="stylesheet" href="<?= base_url('CSS/overall.css') ?>">

    <style>
        .navbar { position: sticky; top: 0; z-index: 100; background: #fff; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        .admin-container { max-width: 1200px; margin: 20px auto; padding: 20px; }
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 30px; }
        .stat-card { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); text-align: center; }
        .stat-value { font-size: 24px; font-weight: bold; color: #1877f2; margin-top: 10px; }
        .stat-label { color: #65676b; font-size: 14px; }
    </style>
</head>
<body class='background'>
    <?= view('templates/AdminNavbar') ?>
    
    <div class="admin-container">
        <h1>Admin Dashboard</h1>
        
        <div class="stats-grid">
            <div class="stat-card">
                <i data-lucide="users" size="32" color="#1877f2"></i>
                <div class="stat-value"><?= $stats['users'] ?? 'Users' ?></div>
                <div class="stat-label"><a href="<?= base_url('admin/list_user') ?>">Manage Users</a></div>
            </div>
            <div class="stat-card">
                <i data-lucide="file-text" size="32" color="#1877f2"></i>
                <div class="stat-value"><?= $stats['posts'] ?? 'Posts' ?></div>
                <div class="stat-label"><a href="<?= base_url('admin/list_post') ?>">Manage Posts</a></div>
            </div>
            <div class="stat-card">
                <i data-lucide="users-2" size="32" color="#1877f2"></i>
                <div class="stat-value"><?= $stats['groups'] ?? 'Groups' ?></div>
                <div class="stat-label"><a href="<?= base_url('admin/list_group') ?>">Manage Groups</a></div>
            </div>
            <div class="stat-card">
                <i data-lucide="message-circle" size="32" color="#1877f2"></i>
                <div class="stat-value"><?= $stats['chats'] ?? 'Chats' ?></div>
                <div class="stat-label"><a href="<?= base_url('admin/list_chat') ?>">Manage Chats</a></div>
            </div>
            <div class="stat-card" style="border: 2px solid #dc3545;">
                <i data-lucide="gavel" size="32" color="#dc3545"></i>
                <div class="stat-value" style="color:#dc3545;">Appeals</div>
                <div class="stat-label"><a href="<?= base_url('admin/appeals') ?>" style="color:#dc3545;">Review Ban Appeals</a></div>
            </div>
        </div>
    </div>
    
    <script>lucide.createIcons();</script>
</body>
</html>