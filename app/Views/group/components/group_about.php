<div class="group-about" id="group-about-content" style="display: none;">
    <div class="about-section">
        <h2>About This Group</h2>
        <p id="group-description"><?= esc($group['description'] ?? 'No description available.') ?></p>
    </div>
    <div class="about-section">
        <h3>Privacy</h3>
        <p>
            <i data-lucide="<?= $group['privacy'] === 'public' ? 'globe' : 'lock' ?>"></i>
            <?= ucfirst($group['privacy']) ?> group
        </p>
    </div>
    <div class="about-section">
        <h3>Created</h3>
        <p><?= date('F j, Y', strtotime($group['created_at'])) ?></p>
    </div>
</div>
