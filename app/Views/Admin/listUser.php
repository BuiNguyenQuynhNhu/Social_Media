<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users | Admin</title>
    
    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="<?= base_url('CSS/admin.css') ?>">
    <script>
        window.BASE_URL = '<?= rtrim(base_url(), '/') ?>';
        window.APP_CONFIG = {
            baseURL: window.BASE_URL,
            userId: '<?= session()->get('user_id') ?>',
            csrfName: '<?= csrf_token() ?>',     
            csrfHash: '<?= csrf_hash() ?>'
        };
    </script>
</head>
<body>

<div class="admin-layout">
    <?= view('components/admin/sidebar') ?>

    <main class="admin-main">
        <div class="page-header">
            <h1 class="page-title">Manage Users</h1>
            <div class="admin-actions">
                <!-- Add search or other top actions here -->
            </div>
        </div>
        
        <div class="admin-card table-container">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>User</th>
                        <th>Email</th>
                        <th>Created</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($users)): ?>
                        <?php foreach ($users as $user): ?>
                        <tr id="user-row-<?= $user['id'] ?>">
                            <td>
                                <div class="user-cell">
                                    <img src="<?= (isset($user['avatar']) && strpos($user['avatar'], 'http') === 0) ? $user['avatar'] : base_url($user['avatar'] ?? 'ICON/avatar-default.svg') ?>" class="avatar-sm">
                                    <span style="font-weight:600;"><?= esc($user['firstname'] . ' ' . $user['lastname']) ?></span>
                                </div>
                            </td>
                            <td><?= esc($user['email']) ?></td>
                            <td><?= date('M d, Y', strtotime($user['created_at'])) ?></td>
                            <td>
                                <?php if (($user['status'] ?? 'active') == 'banned'): ?>
                                    <span class="badge badge-banned">Banned</span>
                                <?php else: ?>
                                    <span class="badge badge-active">Active</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div style="display:flex; gap:6px;">
                                    <a href="<?= base_url('user/profile/'.$user['id']) ?>" class="btn btn-primary" target="_blank" title="View Profile">
                                        <i data-lucide="external-link" style="width:16px;height:16px;"></i>
                                    </a>
                                    
                                    <button data-id="<?= $user['id'] ?>" class="btn btn-warning btn-warn" title="Warn User" style="background:#fff3cd; color:#856404;">
                                        <i data-lucide="alert-triangle" style="width:16px;height:16px;"></i>
                                    </button>

                                    <?php if (($user['status'] ?? 'active') !== 'banned'): ?>
                                        <button data-id="<?= $user['id'] ?>" class="btn btn-danger btn-ban" title="Ban User">
                                            <i data-lucide="ban" style="width:16px;height:16px;"></i>
                                        </button>
                                    <?php else: ?>
                                        <button data-id="<?= $user['id'] ?>" class="btn btn-success btn-unban" title="Unban User">
                                            <i data-lucide="check" style="width:16px;height:16px;"></i>
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="5" style="text-align:center; padding: 40px; color:#65676b;">No users found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>
</div>

<script>
    lucide.createIcons();

    $(document).ready(function() {
        // Warn User
        $(document).on('click', '.btn-warn', function(e) {
            e.preventDefault();
            const userId = $(this).data('id');
            const reason = prompt('Enter warning reason:');
            if (!reason) return;

            $.ajax({
                url: APP_CONFIG.baseURL + '/admin/warn_user/' + userId,
                type: 'POST',
                data: { 
                    [APP_CONFIG.csrfName]: APP_CONFIG.csrfHash, 
                    reason: reason 
                },
                success: function(res) {
                    alert(res.message);
                }
            });
        });

        // Ban User
        $(document).on('click', '.btn-ban', function(e) {
            e.preventDefault();
            const userId = $(this).data('id');
            if (!confirm('Ban this user for 7 days?')) return;

            $.ajax({
                url: APP_CONFIG.baseURL + '/admin/ban_user/' + userId,
                type: 'POST',
                data: { [APP_CONFIG.csrfName]: APP_CONFIG.csrfHash },
                success: function(res) {
                    if (res.status === 'success') location.reload();
                    else alert(res.message);
                }
            });
        });

        // Unban User
        $(document).on('click', '.btn-unban', function(e) {
            e.preventDefault();
            const userId = $(this).data('id');
            if (!confirm('Unban this user?')) return;

            $.ajax({
                url: APP_CONFIG.baseURL + '/admin/unban_user/' + userId,
                type: 'POST',
                data: { [APP_CONFIG.csrfName]: APP_CONFIG.csrfHash },
                success: function(res) {
                    if (res.status === 'success') location.reload();
                    else alert(res.message);
                }
            });
        });
    });
</script>
</body>
</html>
