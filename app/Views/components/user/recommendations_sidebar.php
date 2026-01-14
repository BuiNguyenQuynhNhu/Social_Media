<!-- Friend Recommendations Sidebar Widget - WITH FALLBACK -->
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
            // Try with session user_id first
            $userId = $_SESSION['user_id'];
            $apiUrl = 'http://127.0.0.1:5000/api/recommendations/friends/' . $userId . '?top_k=5';
            
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $apiUrl);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 2);
            
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            
            if ($httpCode === 200 && $response) {
                $data = json_decode($response, true);
                if (isset($data['recommendations'])) {
                    $recs = $data['recommendations'];
                    
                    // If no recommendations, try with user ID 1 from dataset as fallback
                    if (empty($recs) && $userId > 100) {
                        $apiUrl = 'http://127.0.0.1:5000/api/recommendations/friends/1?top_k=5';
                        $ch = curl_init();
                        curl_setopt($ch, CURLOPT_URL, $apiUrl);
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                        curl_setopt($ch, CURLOPT_TIMEOUT, 2);
                        $response = curl_exec($ch);
                        curl_close($ch);
                        
                        if ($response) {
                            $data = json_decode($response, true);
                            $recs = $data['recommendations'] ?? [];
                        }
                    }
                    
                    foreach ($recs as $rec) {
                        $recommendedFriends[] = [
                            'id' => $rec['user_id'],
                            'name' => 'User ' . $rec['user_id'],
                            'mutual_count' => $rec['mutual_friends'] ?? 0,
                            'profile_picture' => '',
                            'score' => $rec['score']
                        ];
                    }
                }
            }
        }
        
        // Display recommendations
        if (!empty($recommendedFriends)):
            foreach ($recommendedFriends as $friend): ?>
                <div class="recommendation-item" data-user-id="<?= $friend['id'] ?>">
                    <div class="user-info">
                        <a href="/user/profile/<?= $friend['id'] ?>" class="user-avatar">
                            <div class="avatar-placeholder">
                                <?= strtoupper(substr($friend['name'], 0, 1)) ?>
                            </div>
                        </a>
                        
                        <div class="user-details">
                            <a href="/user/profile/<?= $friend['id'] ?>" class="user-name">
                                <?= htmlspecialchars($friend['name']) ?>
                            </a>
                            <?php if ($friend['mutual_count'] > 0): ?>
                                <span class="mutual-friends">
                                    <?= $friend['mutual_count'] ?> mutual
                                </span>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <button class="add-friend-btn" data-user-id="<?= $friend['id'] ?>">
                        Add
                    </button>
                </div>
            <?php endforeach;
        endif; ?>
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
