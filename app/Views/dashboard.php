<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Post | Social Media</title>
    <link rel="stylesheet" href="<?= base_url('CSS/navbar.css') ?>">
    <link rel="stylesheet" href="<?= base_url('CSS/dashboard.css') ?>">
    <link rel="stylesheet" href="<?= base_url('CSS/post/post.css') ?>">
    <link rel="stylesheet" href="<?= base_url('CSS/post/comment.css') ?>">
    <link rel="stylesheet" href="<?= base_url('CSS/post/interaction.css') ?>">
    <link href="https://fonts.googleapis.com/css2?family=Segoe+UI:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body class="background">

<!-- Navbar -->
    <!-- Navbar -->
    <?= view('components/user/navbar', ['avatarUrl' => $avatarUrl]) ?>


    <div class="dashboard-container">
        <!-- Left Sidebar -->
        <?= view('components/user/sidebar_left') ?>

        <!-- Main Feed -->
        <div class="main-feed">
            <!-- Create Post -->
            <?= view('components/post/create_post', ['avatarUrl' => $avatarUrl, 'user' => $user]) ?>

            <!-- Posts -->
            <div class="posts-feed">
                <?php if (!empty($posts)): ?>
                    <?php foreach ($posts as $post): ?>
                        <?= view('components/post/post_item', ['post' => $post, 'avatarUrl' => $avatarUrl, 'context' => 'dashboard']) ?>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="empty-state">
                        <i data-lucide="newspaper"></i>
                        <p>No posts yet. Be the first to post!</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Right Sidebar -->
        <?= view('components/user/sidebar_right') ?>

    </div>

    <!-- Create Post Modal is already included via user/create_post component -->

    <script src="<?= base_url('JS/post/init.js') ?>"></script>
    <script>
        window.BASE_URL = "<?= rtrim(base_url(), '/') ?>";
        window.APP_CONFIG = {
            baseURL: window.BASE_URL,
            userId: '<?= session()->get('user_id') ?>',
            csrfTokenName: '<?= csrf_token() ?>',     
            csrfHash: '<?= csrf_hash() ?>'
        };
        // Initialize Lucide Icons
        if(window.lucide) lucide.createIcons();
    
        function openCreatePostModal() {
            document.getElementById('createPostModal').style.display = 'flex';
        }
        function closeCreatePostModal() {
            document.getElementById('createPostModal').style.display = 'none';
        }
    </script>
    <script src="<?= base_url('JS/navbar.js') ?>"></script>
    <script src="<?= base_url('JS/post/utils.js') ?>"></script>
    <script src="<?= base_url('JS/post/reactions.js') ?>"></script>
    <script src="<?= base_url('JS/post/comments.js') ?>"></script>
    <script src="<?= base_url('JS/post.js') ?>"></script>
    
    <!-- Lightbox Component -->
    <?= view('components/post/lightbox') ?>
<?= view('components/post/edit_post') ?>
</body>
</html>
