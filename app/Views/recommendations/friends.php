<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $data['title'] ?? 'Friend Recommendations' ?></title>
    <link rel="stylesheet" href="/public/css/style.css">
    <link rel="stylesheet" href="/public/css/recommendations.css">
</head>
<body>
    <?php include_once __DIR__ . '/../components/navbar.php'; ?>
    
    <div class="container">
        <div class="recommendations-page">
            <div class="page-header">
                <h1>
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                        <path d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    People You May Know
                </h1>
                <p class="subtitle">Based on your network and interests</p>
            </div>

            <?php if (empty($data['recommendations'])): ?>
                <div class="empty-state">
                    <svg width="64" height="64" viewBox="0 0 24 24" fill="none">
                        <path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2M9 11a4 4 0 100-8 4 4 0 000 8zM23 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    <h3>No Recommendations Available</h3>
                    <p>We'll suggest new connections as you use the platform more.</p>
                </div>
            <?php else: ?>
                <div class="recommendations-grid">
                    <?php foreach ($data['recommendations'] as $user): ?>
                        <div class="recommendation-card">
                            <div class="card-header">
                                <a href="/profile/<?= $user['id'] ?>" class="user-avatar">
                                    <?php if (!empty($user['profile_picture'])): ?>
                                        <img src="<?= htmlspecialchars($user['profile_picture']) ?>" alt="<?= htmlspecialchars($user['first_name']) ?>">
                                    <?php else: ?>
                                        <div class="avatar-placeholder">
                                            <?= strtoupper(substr($user['first_name'], 0, 1)) ?>
                                        </div>
                                    <?php endif; ?>
                                </a>
                            </div>

                            <div class="card-body">
                                <a href="/profile/<?= $user['id'] ?>" class="user-name">
                                    <?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?>
                                </a>
                                
                                <div class="recommendation-score">
                                    <div class="score-bar">
                                        <div class="score-fill" style="width: <?= ($user['score'] * 100) ?>%"></div>
                                    </div>
                                    <span class="score-text"><?= number_format($user['score'] * 100, 0) ?>% match</span>
                                </div>

                                <p class="recommendation-reason">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                                        <path d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                    <?= htmlspecialchars($user['reason']) ?>
                                </p>

                                <?php if (!empty($user['mutual_friends'])): ?>
                                    <p class="mutual-friends">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                                            <path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2M9 11a4 4 0 100-8 4 4 0 000 8zM23 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75" stroke="currentColor" stroke-width="2"/>
                                        </svg>
                                        <?= $user['mutual_friends'] ?> mutual friends
                                    </p>
                                <?php endif; ?>
                            </div>

                            <div class="card-footer">
                                <button class="btn btn-primary add-friend-btn" data-user-id="<?= $user['id'] ?>">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
                                        <path d="M16 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2M12.5 11a4 4 0 100-8 4 4 0 000 8zM19 8v6M22 11h-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                    Add Friend
                                </button>
                                <button class="btn btn-secondary remove-btn" data-user-id="<?= $user['id'] ?>">
                                    Remove
                                </button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <?php include_once __DIR__ . '/../components/footer.php'; ?>

    <script>
        // Add friend button handler
        document.querySelectorAll('.add-friend-btn').forEach(btn => {
            btn.addEventListener('click', async function() {
                const userId = this.dataset.userId;
                const card = this.closest('.recommendation-card');
                
                try {
                    const response = await fetch('/friendship/send-request', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({ friend_id: userId })
                    });
                    
                    const data = await response.json();
                    
                    if (data.success) {
                        this.innerHTML = '<svg width="20" height="20" viewBox="0 0 24 24" fill="none"><path d="M5 13l4 4L19 7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg> Request Sent';
                        this.disabled = true;
                        this.classList.add('btn-success');
                    } else {
                        alert(data.message || 'Failed to send friend request');
                    }
                } catch (error) {
                    console.error('Error:', error);
                    alert('An error occurred. Please try again.');
                }
            });
        });

        // Remove recommendation button handler
        document.querySelectorAll('.remove-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const card = this.closest('.recommendation-card');
                card.style.opacity = '0';
                card.style.transform = 'scale(0.9)';
                setTimeout(() => {
                    card.remove();
                    
                    // Check if no recommendations left
                    if (document.querySelectorAll('.recommendation-card').length === 0) {
                        location.reload();
                    }
                }, 300);
            });
        });
    </script>
</body>
</html>
