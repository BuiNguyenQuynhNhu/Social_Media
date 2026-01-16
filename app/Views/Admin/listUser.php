<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users | Admin</title>
    
    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        window.BASE_URL = '<?= rtrim(base_url(), '/') ?>';
        window.APP_CONFIG = {
            baseURL: window.BASE_URL,
            userId: '<?= session()->get('user_id') ?>',
            csrfName: '<?= csrf_token() ?>',     
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
        .btn-warn { background: #fff3cd; color: #856404; }
        .btn-ban { background: #f8d7da; color: #721c24; }
        .btn-unban { background: #d4edda; color: #155724; }
        .status-banned { color: #dc3545; font-weight: 600; }
        .status-active { color: #28a745; }
    </style>
</head>
<body class='background'>
    <?= view('templates/AdminNavbar') ?>
    
    <div class="admin-container">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
            <h1>Manage Users</h1>
            <a href="<?= base_url('admin/dashboard') ?>" class="action-btn" style="background:#ddd; color:#000;">Back to Dashboard</a>
        </div>
        
        <table>
            <thead>
                <tr>
                    <th>Avatar</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($users)): ?>
                    <?php foreach ($users as $user): ?>
                    <tr id="user-row-<?= $user['id'] ?>">
                        <td><img src="<?= (isset($user['avatar']) && strpos($user['avatar'], 'http') === 0) ? $user['avatar'] : base_url($user['avatar'] ?? 'ICON/avatar-default.svg') ?>" style="width:40px;height:40px;border-radius:50%;object-fit:cover;"></td>
                        <td><?= esc($user['firstname'] . ' ' . $user['lastname']) ?></td>
                        <td><?= esc($user['email']) ?></td>
                        <td><span class="status-<?= ($user['status'] ?? 'active') == 'banned' ? 'banned' : 'active' ?>"><?= ucfirst($user['status'] ?? 'Active') ?></span></td>
                        <td>
                            <a href="<?= base_url('user/profile/'.$user['id']) ?>" class="action-btn btn-view" target="_blank">View</a>
                            <button data-id="<?= $user['id'] ?>" class="action-btn btn-warn">Warn</button>
                            <?php if (($user['status'] ?? 'active') !== 'banned'): ?>
                                <button data-id="<?= $user['id'] ?>" class="action-btn btn-ban">Ban</button>
                            <?php else: ?>
                                <button data-id="<?= $user['id'] ?>" class="action-btn btn-unban">Unban</button>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="5" style="text-align:center;">No users found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    
    <script>
    // Initialize Lucide icons
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
                    if (res.status === 'success') {
                        alert(res.message);
                    } else {
                        alert(res.message || 'Failed');
                    }
                },
                error: function(xhr, status, error) {
                    alert('Error: ' + (xhr.responseText || error));
                }
            });
        });

        // Ban User
        $(document).on('click', '.btn-ban', function(e) {
            e.preventDefault();
            const userId = $(this).data('id');
            
            if (!confirm('Are you sure you want to ban this user for 7 days?')) return;

            $.ajax({
                url: APP_CONFIG.baseURL + '/admin/ban_user/' + userId,
                type: 'POST',
                data: { [APP_CONFIG.csrfName]: APP_CONFIG.csrfHash },
                success: function(res) {
                    if (res.status === 'success') {
                        alert(res.message);
                        location.reload();
                    } else {
                        alert(res.message || 'Failed');
                    }
                },
                error: function(xhr, status, error) {
                    alert('Error: ' + (xhr.responseText || error));
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
                    if (res.status === 'success') {
                        alert(res.message);
                        location.reload();
                    } else {
                        alert(res.message || 'Failed');
                    }
                },
                error: function(xhr, status, error) {
                    alert('Error: ' + (xhr.responseText || error));
                }
            });
        });
    });
    </script>
</body>
</html>
