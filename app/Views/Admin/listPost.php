<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Posts | Admin</title>
    
    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        window.BASE_URL = '<?= rtrim(base_url(), '/') ?>';
        window.APP_CONFIG = {
            baseURL: window.BASE_URL,
            userId: '<?= session()->get('user_id') ?>',
            csrfTokenName: '<?= csrf_token() ?>',     
            csrfHash: '<?= csrf_hash() ?>'
        };
    </script>
    
    <link rel="stylesheet" href="<?= base_url('CSS/navbar.css') ?>">
    <link rel="stylesheet" href="<?= base_url('CSS/overall.css') ?>">
    
    <style>
        .admin-container { max-width: 1200px; margin: 20px auto; padding: 20px; }
        table { width: 100%; border-collapse: collapse; background: white; border-radius: 8px; overflow: hidden; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
        th, td { padding: 12px 15px; text-align: left; border-bottom: 1px solid #eee; }
        th { background: #f0f2f5; font-weight: 600; }
        tr:hover { background: #f9f9f9; }
        .action-btn { padding: 5px 10px; border-radius: 4px; text-decoration: none; font-size: 13px; font-weight: 500; cursor: pointer; border: none; margin-right: 4px; }
        .btn-view { background: #e7f3ff; color: #1877f2; }
        .btn-delete { background: #f8d7da; color: #721c24; }
    </style>
</head>
<body class='background'>
    <?= view('templates/AdminNavbar') ?>
    
    <div class="admin-container">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
            <h1>Manage Posts</h1>
            <a href="<?= base_url('admin/dashboard') ?>" class="action-btn" style="background:#ddd; color:#000;">Back to Dashboard</a>
        </div>
        
        <table>
            <thead>
                <tr>
                    <th>Author</th>
                    <th>Content</th>
                    <th>Created At</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($posts)): ?>
                    <?php foreach ($posts as $post): ?>
                    <tr id="post-row-<?= $post['id'] ?>">
                        <td><?= esc($post['id_user']) ?></td>
                        <td><?= esc(substr($post['content'], 0, 80)) . (strlen($post['content'])>80?'...':'') ?></td>
                        <td><?= date('M d, Y H:i', strtotime($post['created_at'])) ?></td>
                        <td>
                            <button data-id="<?= $post['id'] ?>" class="action-btn btn-delete">Delete</button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="4" style="text-align:center;">No posts found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    
    <script>
    lucide.createIcons();
    
    $(document).ready(function() {
        // Event delegation for Delete button
        $(document).on('click', '.btn-delete', function(e) {
            e.preventDefault();
            const postId = $(this).data('id');
            
            if (!confirm('Are you sure you want to delete this post? The user will be notified.')) return;
            
            $.ajax({
                url: BASE_URL + '/admin/delete_post/' + postId,
                type: 'POST',
                data: { [APP_CONFIG.csrfTokenName]: APP_CONFIG.csrfHash },
                success: function(res) {
                    if (res.status === 'success') {
                        $('#post-row-' + postId).fadeOut();
                        alert(res.message);
                    } else {
                        alert(res.message || 'Failed');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Delete error:', error);
                    alert('Error: ' + (xhr.responseText || error));
                }
            });
        });
    });
    </script>
</body>
</html>
