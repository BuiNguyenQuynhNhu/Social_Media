<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Groups | Admin</title>
    
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
            <h1 class="page-title">Manage Groups</h1>
        </div>
        
        <div class="admin-card table-container">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Privacy</th>
                        <th>Members</th>
                        <th>Created At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($groups)): ?>
                        <?php foreach ($groups as $group): ?>
                        <tr id="group-row-<?= $group['id'] ?>">
                            <td>
                                <div class="user-cell">
                                    <div style="width:40px;height:40px;background:#e7f3ff;border-radius:8px;display:flex;align-items:center;justify-content:center;color:#1877f2;">
                                        <i data-lucide="users-2" style="width:20px;height:20px;"></i>
                                    </div>
                                    <span style="font-weight:600;"><?= esc($group['name']) ?></span>
                                </div>
                            </td>
                            <td>
                                <?php if ($group['privacy'] == 'public'): ?>
                                    <span class="badge badge-active">Public</span>
                                <?php else: ?>
                                    <span class="badge badge-banned" style="background:#f0f2f5; color:#65676b;">Private</span>
                                <?php endif; ?>
                            </td>
                            <td><?= $group['member_count'] ?? '-' ?></td>
                            <td><?= date('M d, Y', strtotime($group['created_at'])) ?></td>
                            <td>
                                <div style="display:flex; gap:6px;">
                                    <a href="<?= base_url('group/'.$group['id']) ?>" class="btn btn-primary" target="_blank" title="View Group">
                                        <i data-lucide="external-link" style="width:16px;height:16px;"></i>
                                    </a>
                                    <button data-id="<?= $group['id'] ?>" class="btn btn-danger btn-delete" title="Delete Group">
                                        <i data-lucide="trash-2" style="width:16px;height:16px;"></i> Delete
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="5" style="text-align:center; padding: 40px; color:#65676b;">No groups found.</td></tr>
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
            const groupId = $(this).data('id');
            
            if (!confirm('Are you sure you want to delete this group? The owner will be notified.')) return;
            
            $.ajax({
                url: window.APP_CONFIG.baseURL + '/admin/delete_group/' + groupId,
                type: 'POST',
                data: { [window.APP_CONFIG.csrfTokenName]: window.APP_CONFIG.csrfHash },
                success: function(res) {
                    if (res.status === 'success') {
                        $('#group-row-' + groupId).fadeOut();
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
