<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    
    <link rel="icon" href="<?= base_url('ICON/logo.png') ?>">
    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="<?= base_url('CSS/admin.css') ?>">
</head>
<body>

<div class="admin-layout">
    <!-- Sidebar -->
    <?= view('components/admin/sidebar') ?>

    <!-- Main Content -->
    <main class="admin-main">
        <div class="page-header">
            <h1 class="page-title">Dashboard Overview</h1>
            <div style="display:flex; align-items:center; gap:12px;">
                <span style="font-weight:600; color:#65676b;"><?= session()->get('firstname') ?> (Admin)</span>
                <img src="<?= base_url('ICON/avatar-default.svg') ?>" style="width:40px;height:40px;border-radius:50%;">
            </div>
        </div>

        <!-- Stats -->
        <div class="stats-grid">
            <a href="<?= base_url('admin/list_user') ?>" style="text-decoration:none;">
                <div class="stat-card">
                    <div class="stat-icon">
                        <i data-lucide="users"></i>
                    </div>
                    <div class="stat-info">
                        <h3><?= $stats['users'] ?? 0 ?></h3>
                        <span>Total Users</span>
                    </div>
                </div>
            </a>

            <a href="<?= base_url('admin/list_post') ?>" style="text-decoration:none;">
                <div class="stat-card">
                    <div class="stat-icon">
                        <i data-lucide="file-text"></i>
                    </div>
                    <div class="stat-info">
                        <h3><?= $stats['posts'] ?? 0 ?></h3>
                        <span>Total Posts</span>
                    </div>
                </div>
            </a>

            <a href="<?= base_url('admin/list_group') ?>" style="text-decoration:none;">
                <div class="stat-card">
                    <div class="stat-icon">
                        <i data-lucide="users-2"></i>
                    </div>
                    <div class="stat-info">
                        <h3><?= $stats['groups'] ?? 0 ?></h3>
                        <span>Groups</span>
                    </div>
                </div>
            </a>

            <a href="<?= base_url('admin/appeals') ?>" style="text-decoration:none;">
                <div class="stat-card">
                    <div class="stat-icon danger">
                        <i data-lucide="gavel"></i>
                    </div>
                    <div class="stat-info">
                        <h3>Appeals</h3>
                        <span>Review Bans</span>
                    </div>
                </div>
            </a>
        </div>

        <!-- Recent Activity or Other Widgets could go here -->
        <div class="admin-card">
            <h3 style="margin-top:0;">System Status</h3>
            <p style="color:#65676b;">System is running smoothly. Notifications and Appeal system are active.</p>
        </div>

    </main>
</div>

<script>
    lucide.createIcons();
</script>
</body>
</html>