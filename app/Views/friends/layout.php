<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Friends' ?> | Social Media</title>
    
    <link rel="stylesheet" href="<?= base_url('CSS/overall.css') ?>">
    <link rel="stylesheet" href="<?= base_url('CSS/friends/friends_layout.css') ?>">
    <link rel="stylesheet" href="<?= base_url('CSS/navbar.css') ?>">
    
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body class="background">

<?= view('components/user/navbar', ['avatarUrl' => $avatarUrl]) ?>

<div class="friends-page-container">
    <!-- Sidebar -->
    <div class="friends-sidebar">
        <div class="friends-sidebar-header">
            <h1>Friends</h1>
        </div>
        
        <nav class="friends-nav">
            <a href="<?= base_url('friends/requests') ?>" class="friends-nav-item">
                <div class="friends-nav-icon">
                    <i data-lucide="user-plus"></i>
                </div>
                <span>Friend Requests</span>
            </a>
            
            <a href="<?= base_url('friends/suggestions') ?>" class="friends-nav-item">
                <div class="friends-nav-icon">
                    <i data-lucide="user-check"></i>
                </div>
                <span>Suggestions</span>
            </a>
            
            <a href="<?= base_url('friends/list') ?>" class="friends-nav-item">
                <div class="friends-nav-icon">
                    <i data-lucide="users"></i>
                </div>
                <span>All Friends</span>
            </a>
        </nav>
    </div>

    <!-- Main Content -->
    <div class="friends-content">
        <?= $content ?>
    </div>
</div>

<script>
    lucide.createIcons();
    
    // Auto-hide alerts
    setTimeout(function() {
        $('.alert').fadeOut('slow');
    }, 3000);
</script>
<script src="<?= base_url('JS/navbar.js') ?>?v=<?= time() ?>"></script>


</body>
</html>
