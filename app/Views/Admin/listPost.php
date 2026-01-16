<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Posts | Admin</title>
    
    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="<?= base_url('CSS/admin.css') ?>">
    <script>
        window.BASE_URL = '<?= rtrim(base_url(), '/') ?>';
        window.APP_CONFIG = {
            baseURL: window.BASE_URL,
            userId: '<?= session()->get('user_id') ?>',
            csrfTokenName: '<?= csrf_token() ?>',     
            csrfHash: '<?= csrf_hash() ?>'
        };
    </script>
</head>
<body>

<div class="admin-layout">
    <?= view('components/admin/sidebar') ?>

    <main class="admin-main">
        <div class="page-header">
            <h1 class="page-title">Manage Posts</h1>
        </div>
        
        <div class="admin-card table-container">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Author ID</th>
                        <th>Content</th>
                        <th>Created At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($posts)): ?>
                        <?php foreach ($posts as $post): ?>
                        <tr id="post-row-<?= $post['id'] ?>">
                            <td style="color:#65676b; font-weight:500;">
                                <?= esc($post['id_user']) ?>
                            </td>
                            <td title="<?= esc($post['content']) ?>">
                                <?= esc(substr($post['content'], 0, 80)) . (strlen($post['content'])>80?'...':'') ?>
                            </td>
                            <td><?= date('M d, Y H:i', strtotime($post['created_at'])) ?></td>
                            <td>
                                <button data-id="<?= $post['id'] ?>" class="btn btn-danger btn-delete" title="Delete Post">
                                    <i data-lucide="trash-2" style="width:16px;height:16px;"></i> Delete
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="4" style="text-align:center; padding: 40px; color:#65676b;">No posts found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>
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
                url: window.APP_CONFIG.baseURL + '/admin/delete_post/' + postId,
                type: 'POST',
                data: { [window.APP_CONFIG.csrfTokenName]: window.APP_CONFIG.csrfHash },
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
