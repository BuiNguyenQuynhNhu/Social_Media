<!-- Add this to your sidebar (e.g., app/Views/components/user/sidebar_right.php) -->

<div class="recommendations-widget">
    <div class="widget-header">
        <h3>People You May Know</h3>
        <span class="ai-badge" title="AI-Powered Recommendations">✨ AI</span>
    </div>
    
    <?php if (!empty($friend_suggestions)): ?>
        <div class="suggestions-list">
            <?php foreach ($friend_suggestions as $suggestion): ?>
                <div class="suggestion-item" data-user-id="<?= esc($suggestion['id']) ?>">
                    <div class="user-avatar">
                        <img src="<?= esc($suggestion['avatar']) ?>" 
                             alt="<?= esc($suggestion['username']) ?>">
                    </div>
                    <div class="user-info">
                        <div class="user-name">
                            <a href="/profile/<?= esc($suggestion['username']) ?>">
                                <strong><?= esc($suggestion['fullname']) ?></strong>
                            </a>
                        </div>
                        <div class="ai-score" title="AI Confidence Score">
                            <small>Match: <?= $suggestion['ai_score'] ?>%</small>
                        </div>
                        <div class="reason">
                            <small><?= esc($suggestion['reason']) ?></small>
                        </div>
                    </div>
                    <div class="actions">
                        <button class="btn-add-friend" onclick="addFriend('<?= esc($suggestion['id']) ?>')">
                            <i class="fas fa-user-plus"></i> Add Friend
                        </button>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="no-suggestions">
            <p>Check back later for personalized friend recommendations!</p>
        </div>
    <?php endif; ?>
</div>

<style>
.recommendations-widget {
    background: white;
    border-radius: 8px;
    padding: 16px;
    margin-bottom: 20px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.widget-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 16px;
}

.widget-header h3 {
    margin: 0;
    font-size: 16px;
    font-weight: 600;
}

.ai-badge {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 4px 8px;
    border-radius: 12px;
    font-size: 11px;
    font-weight: 600;
}

.suggestions-list {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.suggestion-item {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 8px;
    border-radius: 8px;
    transition: background 0.2s;
}

.suggestion-item:hover {
    background: #f5f5f5;
}

.user-avatar img {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    object-fit: cover;
}

.user-info {
    flex: 1;
}

.user-name a {
    color: #333;
    text-decoration: none;
}

.user-name a:hover {
    color: #667eea;
}

.ai-score {
    color: #667eea;
    font-weight: 500;
}

.reason {
    color: #666;
    font-size: 12px;
}

.btn-add-friend {
    background: #667eea;
    color: white;
    border: none;
    padding: 8px 12px;
    border-radius: 6px;
    cursor: pointer;
    font-size: 13px;
    transition: background 0.2s;
}

.btn-add-friend:hover {
    background: #5568d3;
}

.no-suggestions {
    text-align: center;
    padding: 20px;
    color: #999;
}
</style>

<script>
function addFriend(userId) {
    // Your existing add friend logic
    fetch('/api/friend-request/send', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ user_id: userId })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Friend request sent!');
            // Optionally remove the suggestion from UI
            document.querySelector(`[data-user-id="${userId}"]`).remove();
        }
    });
}
</script>
