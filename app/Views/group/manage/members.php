<?= $this->extend('group/manage/layout') ?>

<?= $this->section('content') ?>
<div class="card">
    <div class="card-header">
        <h2>Member Requests</h2>
    </div>
    <div class="card-body">
        <?php if (empty($requests)): ?>
            <div class="empty-state">
                <i data-lucide="users" class="icon-large"></i>
                <p>No pending member requests.</p>
            </div>
        <?php else: ?>
            <div class="requests-list">
                <?php foreach ($requests as $r): ?>
                <div class="member-item" style="display: flex; align-items: center; justify-content: space-between; padding: 12px; border-bottom: 1px solid #eee;">
                    <div style="display: flex; align-items: center; gap: 12px;">
                        <img src="<?= (isset($r['avatar']) && strpos($r['avatar'], 'http') === 0) ? $r['avatar'] : ($r['avatar'] ? base_url($r['avatar']) : base_url('ICON/avatar-default.svg')) ?>" class="avatar-small">
                        <div>
                            <div style="font-weight: 600;"><?= esc($r['firstname'] . ' ' . $r['lastname']) ?></div>
                            <div style="font-size: 13px; color: #65676b;">Requesting to join</div>
                        </div>
                    </div>
                    <div class="actions" style="display: flex; gap: 8px;">
                        <button onclick="approveMember('<?= $r['user_id'] ?>')" class="btn-primary" style="padding: 6px 12px; font-size: 13px;">Approve</button>
                        <button onclick="rejectMember('<?= $r['user_id'] ?>')" class="btn-secondary" style="padding: 6px 12px; font-size: 13px;">Reject</button>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<div class="card" style="margin-top: 20px;">
    <div class="card-header">
        <h2>Members</h2>
        <div class="search-box">
            <i data-lucide="search"></i>
            <input type="text" placeholder="Find a member...">
        </div>
    </div>
    <div class="card-body">
          <div class="members-list">
             <?php foreach ($members as $m): ?>
                <div class="member-item" style="display: flex; align-items: center; justify-content: space-between; padding: 12px; border-bottom: 1px solid #eee;">
                    <div style="display: flex; align-items: center; gap: 12px;">
                        <img src="<?= (isset($m['avatar']) && strpos($m['avatar'], 'http') === 0) ? $m['avatar'] : ($m['avatar'] ? base_url($m['avatar']) : base_url('ICON/avatar-default.svg')) ?>" class="avatar-small">
                        <div>
                            <div style="font-weight: 600;"><?= esc($m['firstname'] . ' ' . $m['lastname']) ?></div>
                            <div style="font-size: 13px; color: #65676b;"><?= ucfirst($m['role']) ?></div>
                        </div>
                    </div>
                    <?php if ($m['user_id'] != $user_id): // Cannot kick/promote self ?>
                        <div class="actions" style="display: flex; gap: 8px;">
                            <?php if ($m['role'] !== 'admin'): ?>
                                <button onclick="promoteMember('<?= $m['user_id'] ?>')" class="btn-secondary" style="padding: 6px 12px; font-size: 13px;">Make Admin</button>
                            <?php endif; ?>
                            <button onclick="kickMember('<?= $m['user_id'] ?>')" class="btn-secondary" style="padding: 6px 12px; font-size: 13px; color: #dc3545; border-color: #dc3545;">Remove</button>
                        </div>
                    <?php endif; ?>
                </div>
             <?php endforeach; ?>
             <?php if (empty($members)): ?>
                <p class="text-muted">No members found.</p>
             <?php endif; ?>
          </div>
    </div>
</div>

<script>
// Get CSRF token
function getCsrfToken() {
    return window.APP_CONFIG ? window.APP_CONFIG.csrfHash : '';
}

function approveMember(userId) {
    $.ajax({
        url: BASE_URL + '/group/' + GROUP_ID + '/approve_member/' + userId,
        type: 'POST',
        headers: { 'X-CSRF-TOKEN': getCsrfToken() },
        success: function(res) {
            if (res.status === 'success') location.reload();
            else alert(res.message || 'Failed');
        }
    });
}

function rejectMember(userId) {
    if (!confirm('Reject this request?')) return;
    $.ajax({
        url: BASE_URL + '/group/' + GROUP_ID + '/reject_member/' + userId,
        type: 'POST',
        headers: { 'X-CSRF-TOKEN': getCsrfToken() },
        success: function(res) {
            if (res.status === 'success') location.reload();
            else alert(res.message || 'Failed');
        }
    });
}

function kickMember(userId) {
    if (!confirm('Remove this member from the group?')) return;
    $.ajax({
        url: BASE_URL + '/group/' + GROUP_ID + '/kick/' + userId,
        type: 'POST',
        headers: { 'X-CSRF-TOKEN': getCsrfToken() },
        success: function(res) {
            if (res.status === 'success') location.reload();
            else alert(res.message || 'Failed');
        }
    });
}

function promoteMember(userId) {
    if (!confirm('Promote this member to Admin?')) return;
    $.ajax({
        url: BASE_URL + '/group/' + GROUP_ID + '/promote/' + userId,
        type: 'POST',
        headers: { 'X-CSRF-TOKEN': getCsrfToken() },
        success: function(res) {
             if (res.status === 'success') location.reload();
             else alert(res.message || 'Failed');
        }
    });
}
</script>
<?= $this->endSection() ?>
