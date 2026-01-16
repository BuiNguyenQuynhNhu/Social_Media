<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ban Appeals | Admin</title>
    
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
            <h1 class="page-title">Ban Appeals</h1>
        </div>
        
        <?php if (empty($appeals)): ?>
            <div class="admin-card empty-state" style="text-align:center; padding:60px;">
                <div style="background:#e7f3ff; width:80px; height:80px; border-radius:50%; display:flex; align-items:center; justify-content:center; margin:0 auto 20px;">
                    <i data-lucide="inbox" style="width:40px;height:40px;color:#1877f2;"></i>
                </div>
                <h3 style="margin-bottom:10px;">No Pending Appeals</h3>
                <p style="color:#65676b;">There are no ban appeals waiting for review at this time.</p>
            </div>
        <?php else: ?>
            <div class="appeals-grid" style="display:flex; flex-direction:column; gap:20px;">
                <?php foreach ($appeals as $appeal): ?>
                    <?php $context = json_decode($appeal['context'], true); ?>
                    <div class="admin-card" id="appeal-<?= $appeal['id'] ?>">
                        <div style="display:flex; align-items:flex-start; justify-content:space-between; margin-bottom:16px;">
                            <div style="display:flex; gap:12px;">
                                <img src="<?= (isset($appeal['avatar']) && strpos($appeal['avatar'], 'http') === 0) ? $appeal['avatar'] : base_url($appeal['avatar'] ?? 'ICON/avatar-default.svg') ?>" style="width:48px;height:48px;border-radius:50%;object-fit:cover;">
                                <div>
                                    <h3 style="margin:0; font-size:16px; font-weight:700;"><?= esc($appeal['firstname'] . ' ' . $appeal['lastname']) ?></h3>
                                    <span style="color:#65676b; font-size:13px;">Submitted <?= date('M d, Y H:i', strtotime($appeal['created_at'])) ?></span>
                                </div>
                            </div>
                            <span class="badge badge-banned">Suspended</span>
                        </div>
                        
                        <div style="background:#f0f2f5; padding:16px; border-radius:8px; margin-bottom:20px;">
                            <label style="font-weight:600; font-size:12px; color:#65676b; display:block; margin-bottom:6px; text-transform:uppercase;">Appeal Reason</label>
                            <p style="margin:0; font-size:15px; line-height:1.5;"><?= esc($context['appeal_reason'] ?? 'No reason provided') ?></p>
                        </div>
                        
                        <div style="display:flex; gap:12px; justify-content:flex-end;">
                            <button onclick="rejectAppeal('<?= $appeal['requester_id'] ?>', '<?= $appeal['id'] ?>')" class="btn btn-danger" style="background:#e4e6eb; color:black;">
                                <i data-lucide="x" style="width:16px;height:16px;"></i> Reject
                            </button>
                            <button onclick="acceptAppeal('<?= $appeal['requester_id'] ?>', '<?= $appeal['id'] ?>')" class="btn btn-success">
                                <i data-lucide="check" style="width:16px;height:16px;"></i> Accept & Unban
                            </button>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </main>
</div>

<script>
    lucide.createIcons();
    
    function acceptAppeal(userId, appealId) {
        if (!confirm('Accept this appeal and unban the user?')) return;
        $.ajax({
            url: window.APP_CONFIG.baseURL + '/admin/accept_appeal/' + userId,
            type: 'POST',
            data: { [window.APP_CONFIG.csrfName]: window.APP_CONFIG.csrfHash },
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
            url: window.APP_CONFIG.baseURL + '/admin/reject_appeal/' + userId,
            type: 'POST',
            data: { [window.APP_CONFIG.csrfName]: window.APP_CONFIG.csrfHash },
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
