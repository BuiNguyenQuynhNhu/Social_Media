<?= $this->extend('group/manage/layout') ?>

<?= $this->section('content') ?>
<div class="card">
    <div class="card-header">
        <h2>Overview</h2>
    </div>
    <div class="card-body">
        <div class="stats-grid">
            <a href="<?= base_url('group/' . $group['id'] . '/manage/members') ?>" class="stat-box" style="text-decoration:none; color:inherit; display:block;">
                <div class="stat-value"><?= $group['member_count'] ?></div>
                <div class="stat-label">Total Members</div>
            </a>
            <a href="<?= base_url('group/' . $group['id'] . '/manage/posts') ?>" class="stat-box" style="text-decoration:none; color:inherit; display:block;">
                <div class="stat-value"><?= $group['pending_posts_count'] ?? 0 ?></div>
                <div class="stat-label">Pending Posts</div>
            </a>
             <div class="stat-box">
                <div class="stat-value">0</div>
                <div class="stat-label">Pending Requests</div>
            </div>
        </div>
    </div>
</div>

<!-- Recent Activity Placeholder -->
<div class="card" style="margin-top: 20px;">
    <div class="card-header">
        <h2>Recent Activity</h2>
    </div>
    <div class="card-body">
        <p class="text-muted">No recent activity to show.</p>
    </div>
</div>
<?= $this->endSection() ?>
