<div class="group-about" id="group-about-content">
    <div class="card-container about-card">
        <div class="about-header">
            <h2>About this group</h2>
        </div>

        <div class="about-description">
            <p><?= nl2br(esc($group['description'] ?? 'No description available.')) ?></p>
        </div>

        <hr class="divider">

        <div class="about-details">
            <div class="detail-item">
                <i data-lucide="<?= $group['privacy'] === 'public' ? 'globe' : 'lock' ?>"></i>
                <div>
                    <strong><?= ucfirst($group['privacy']) ?></strong>
                    <p>
                        <?= $group['privacy'] === 'public' 
                            ? "Anyone can see who's in the group and what they post." 
                            : "Only members can see who's in the group and what they post." ?>
                    </p>
                </div>
            </div>

            <div class="detail-item">
                <i data-lucide="eye"></i>
                <div>
                    <strong>Visible</strong>
                    <p>Anyone can find this group.</p>
                </div>
            </div>

            <div class="detail-item">
                <i data-lucide="clock"></i>
                <div>
                    <strong>History</strong>
                    <p>Group created on <?= date('F j, Y', strtotime($group['created_at'])) ?></p>
                </div>
            </div>
        </div>
    </div>
</div>
