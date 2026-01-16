<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ban Appeals | Admin</title>
    
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
        .appeal-card { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); margin-bottom: 15px; }
        .appeal-header { display: flex; align-items: center; gap: 12px; margin-bottom: 12px; }
        .appeal-avatar { width: 48px; height: 48px; border-radius: 50%; object-fit: cover; }
        .appeal-user-info h3 { margin: 0; font-size: 16px; }
        .appeal-user-info span { font-size: 13px; color: #65676b; }
        .appeal-reason { background: #f0f2f5; padding: 12px; border-radius: 8px; margin-bottom: 15px; }
        .appeal-reason label { font-weight: 600; font-size: 13px; color: #65676b; display: block; margin-bottom: 4px; }
        .appeal-actions { display: flex; gap: 10px; }
        .action-btn { padding: 8px 16px; border-radius: 6px; text-decoration: none; font-size: 14px; font-weight: 600; cursor: pointer; border: none; }
        .btn-accept { background: #42b72a; color: white; }
        .btn-reject { background: #dc3545; color: white; }
        .btn-back { background: #e4e6eb; color: #050505; }
        .empty-state { text-align: center; padding: 40px; color: #65676b; }
        .user-status { padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: 600; }
        .status-banned { background: #f8d7da; color: #721c24; }
        .status-active { background: #d4edda; color: #155724; }
    </style>
</head>
<body class='background'>
    <?= view('templates/AdminNavbar') ?>
    
    <div class="admin-container">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
            <h1>Ban Appeals</h1>
            <a href="<?= base_url('admin/dashboard') ?>" class="action-btn btn-back">Back to Dashboard</a>
        </div>
        
        <?php if (empty($appeals)): ?>
            <div class="empty-state">
                <i data-lucide="inbox" style="width:64px;height:64px;margin-bottom:16px;"></i>
                <h3>No Pending Appeals</h3>
                <p>There are no ban appeals waiting for review.</p>
            </div>
        <?php else: ?>
            <?php foreach ($appeals as $appeal): ?>
                <?php $context = json_decode($appeal['context'], true); ?>
                <div class="appeal-card" id="appeal-<?= $appeal['id'] ?>">
                    <div class="appeal-header">
                        <img src="<?= (isset($appeal['avatar']) && strpos($appeal['avatar'], 'http') === 0) ? $appeal['avatar'] : base_url($appeal['avatar'] ?? 'ICON/avatar-default.svg') ?>" class="appeal-avatar">
                        <div class="appeal-user-info">
                            <h3><?= esc($appeal['firstname'] . ' ' . $appeal['lastname']) ?></h3>
                            <span>Submitted <?= date('M d, Y H:i', strtotime($appeal['created_at'])) ?></span>
                        </div>
                        <span class="user-status status-banned">Suspended</span>
                    </div>
                    
                    <div class="appeal-reason">
                        <label>Appeal Reason</label>
                        <p><?= esc($context['appeal_reason'] ?? 'No reason provided') ?></p>
                    </div>
                    
                    <div class="appeal-actions">
                        <button onclick="acceptAppeal('<?= $appeal['requester_id'] ?>', '<?= $appeal['id'] ?>')" class="action-btn btn-accept">
                            <i data-lucide="check" style="width:16px;height:16px;margin-right:4px;"></i> Accept & Unban
                        </button>
                        <button onclick="rejectAppeal('<?= $appeal['requester_id'] ?>', '<?= $appeal['id'] ?>')" class="action-btn btn-reject">
                            <i data-lucide="x" style="width:16px;height:16px;margin-right:4px;"></i> Reject
                        </button>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
    
    <script>
    lucide.createIcons();
    
    function acceptAppeal(userId, appealId) {
        if (!confirm('Accept this appeal and unban the user?')) return;
        $.ajax({
            url: BASE_URL + '/admin/accept_appeal/' + userId,
            type: 'POST',
            data: { csrf_test_name: APP_CONFIG.csrfHash },
            success: function(res) {
                if (res.status === 'success') {
                    $('#appeal-' + appealId).fadeOut();
                    alert(res.message);
                } else {
                    alert(res.message || 'Failed');
                }
            }
        });
    }
    
    function rejectAppeal(userId, appealId) {
        if (!confirm('Reject this appeal?')) return;
        $.ajax({
            url: BASE_URL + '/admin/reject_appeal/' + userId,
            type: 'POST',
            data: { csrf_test_name: APP_CONFIG.csrfHash },
            success: function(res) {
                if (res.status === 'success') {
                    $('#appeal-' + appealId).fadeOut();
                    alert(res.message);
                } else {
                    alert(res.message || 'Failed');
                }
            }
        });
    }
    </script>
</body>
</html>
