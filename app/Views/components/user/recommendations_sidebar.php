<!-- Friend Recommendations Sidebar Widget -->
<div class="friend-recommendations-sidebar">
    <div class="sidebar-header">
        <h3>People You May Know</h3>
    </div>
    
    <div class="recommendations-list">
        <?php
        $recommendedFriends = [];
        
        // Ensure session is started
        if (!isset($_SESSION)) {
            session_start();
        }
        
        if (isset($_SESSION['user_id'])) {
            // Use RecommenderController with proper filtering
            $recommenderController = new \App\Controllers\RecommenderController();
            $userId = $_SESSION['user_id'];
            $recommendations = $recommenderController->getFriendRecommendations($userId, 20);
            
            // Get existing friends and requests to filter
            $friendshipModel = new \App\Models\FriendShip();
            $existingFriends = $friendshipModel->listFriend($userId);
            $pendingSent = $friendshipModel->listSend($userId);
            $pendingReceived = $friendshipModel->listReceive($userId);
            
            // Build exclude list
            $excludeIds = [$userId];
            foreach ($existingFriends as $f) $excludeIds[] = $f['id'];
            foreach ($pendingSent as $f) $excludeIds[] = $f['id'];
            foreach ($pendingReceived as $f) $excludeIds[] = $f['id'];
            $excludeIds = array_unique($excludeIds);
            
            // Filter and get user details
            $userModel = new \App\Models\User();
            $profileModel = new \App\Models\Profile();
            
            foreach ($recommendations as $rec) {
                if (in_array($rec['user_id'], $excludeIds)) continue;
                
                $user = $userModel->find($rec['user_id']);
                if ($user) {
                    $profile = $profileModel->find($rec['user_id']);
                    $name = $profile ? ($profile['firstname'] . ' ' . $profile['lastname']) : 'User';
                    
                    $recommendedFriends[] = [
                        'id' => $user['id'],
                        'name' => $name,
                        'avatar' => $user['avatar'] ?? '',
                        'score' => $rec['score']
                    ];
                    
                    if (count($recommendedFriends) >= 5) break;
                }
            }
        }
        
        // Display recommendations
        if (!empty($recommendedFriends)):
            foreach ($recommendedFriends as $friend): ?>
                <div class="recommendation-item" data-user-id="<?= $friend['id'] ?>">
                    <div class="user-info">
                        <a href="/user/profile/<?= $friend['id'] ?>" class="user-avatar">
                            <?php if ($friend['avatar']): ?>
                                <img src="<?= base_url($friend['avatar']) ?>" alt="<?= htmlspecialchars($friend['name']) ?>">
                            <?php else: ?>
                                <div class="avatar-placeholder">
                                    <?= strtoupper(substr($friend['name'], 0, 1)) ?>
                                </div>
                            <?php endif; ?>
                        </a>
                        
                        <div class="user-details">
                            <a href="/user/profile/<?= $friend['id'] ?>" class="user-name">
                                <?= htmlspecialchars($friend['name']) ?>
                            </a>
                        </div>
                    </div>
                    
                    <button class="add-friend-btn" data-user-id="<?= $friend['id'] ?>">
                        Add
                    </button>
                </div>
            <?php endforeach;
        else: ?>
            <div class="no-recommendations">
                <p style="color: #65676b; font-size: 13px; text-align: center;">No new recommendations</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<style>
.friend-recommendations-sidebar {
    background: white;
    border-radius: 8px;
    padding: 12px;
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
    margin-bottom: 16px;
}

.sidebar-header {
    padding-bottom: 12px;
    border-bottom: 1px solid #e4e6eb;
    margin-bottom: 12px;
}

.sidebar-header h3 {
    font-size: 16px;
    font-weight: 600;
    color: #050505;
    margin: 0;
}

.recommendations-list {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.recommendation-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 8px;
    border-radius: 6px;
    transition: background-color 0.2s;
}

.recommendation-item:hover {
    background-color: #f2f3f5;
}

.user-info {
    display: flex;
    align-items: center;
    gap: 10px;
    flex: 1;
    min-width: 0;
}

.user-avatar {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    overflow: hidden;
    display: block;
    flex-shrink: 0;
}

.user-avatar img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.avatar-placeholder {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, #1877f2, #667eea);
    color: white;
    font-size: 14px;
    font-weight: 600;
}

.user-details {
    flex: 1;
    min-width: 0;
    display: flex;
    flex-direction: column;
    gap: 2px;
}

.user-name {
    font-size: 14px;
    font-weight: 500;
    color: #050505;
    text-decoration: none;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.user-name:hover {
    text-decoration: underline;
}

.mutual-friends {
    font-size: 12px;
    color: #65676b;
}

.add-friend-btn {
    flex-shrink: 0;
    padding: 6px 16px;
    border-radius: 6px;
    background: #e4e6eb;
    border: none;
    cursor: pointer;
    font-size: 13px;
    font-weight: 600;
    color: #050505;
    transition: all 0.2s;
}

.add-friend-btn:hover {
    background: #d8dadf;
}

.add-friend-btn.added {
    background: #e4f3e6;
    color: #42b883;
    pointer-events: none;
}

@media (max-width: 1200px) {
    .friend-recommendations-sidebar {
        display: none;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.add-friend-btn').forEach(btn => {
        btn.addEventListener('click', async function() {
            const userId = this.dataset.userId;
            const item = this.closest('.recommendation-item');
            
            try {
                const response = await fetch('/friend/add/' + userId, {
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                
                if (response.ok) {
                    this.textContent = 'Added';
                    this.classList.add('added');
                    
                    setTimeout(() => {
                        item.style.opacity = '0';
                        item.style.transform = 'translateX(20px)';
                        setTimeout(() => item.remove(), 300);
                    }, 1000);
                }
            } catch (error) {
                console.error('Error:', error);
            }
        });
    });
});
</script>
