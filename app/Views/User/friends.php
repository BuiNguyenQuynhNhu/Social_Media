<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Friends</title>
    <script src="https://unpkg.com/lucide@latest"></script>
    <script>
        const APP_CONFIG = {
            baseURL: '<?= base_url() ?>',
            userId: '<?= session()->get('user_id') ?>',
            csrfTokenName: '<?= csrf_token() ?>',     
            csrfHash: '<?= csrf_hash() ?>'
        };
    </script>
    <link rel="stylesheet" href="<?= base_url('CSS/navbar.css') ?>">
    <link rel="stylesheet" href="<?= base_url('CSS/overall.css') ?>">
    
    <style>
        .friends-container { max-width: 1000px; margin: 20px auto; padding: 0 15px; }
        .section-title { font-size: 20px; font-weight: 700; margin-bottom: 15px; display: flex; justify-content: space-between; align-items: center; }

        .friends-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 15px;
            margin-bottom: 40px;
        }

        .friend-card {
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 1px 2px rgba(0,0,0,0.1);
            border: 1px solid #ddd;
            display: flex;
            flex-direction: column;
        }

        .card-img {
            width: 100%;
            aspect-ratio: 1/1; 
            object-fit: cover;
            background: #f0f2f5;
            cursor: pointer;
        }

        .card-body { padding: 12px; display: flex; flex-direction: column; gap: 8px; flex: 1; }
        
        .card-name {
            font-weight: 600;
            font-size: 17px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .card-name a { text-decoration: none; color: inherit; }
        .card-name a:hover { text-decoration: underline; }

        .btn-full { width: 100%; border: none; padding: 8px; border-radius: 6px; font-weight: 600; cursor: pointer; }
        .btn-confirm { background: #1877f2; color: white; }
        .btn-delete { background: #e4e6eb; color: black; }
        .btn-delete:hover { background: #d8dadf; }
        
        .empty-state {
            text-align: center; color: #65676b; padding: 40px; background: white; border-radius: 8px; box-shadow: 0 1px 2px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body class="background">

    <?= view('templates/UserNavbar', ['avatarUrl' => $avatarUrl]) ?>

    <div class="friends-container">
        
        <div class="section-title">
            Friend Requests
            <?php if(count($requests) > 0): ?>
                <span style="color: #de3c3c; font-size: 16px;"><?= count($requests) ?> requests</span>
            <?php endif; ?>
        </div>

        <?php if (!empty($requests)): ?>
            <div class="friends-grid">
                <?php foreach ($requests as $req): ?>
                    <div class="friend-card">
                        <a href="<?= base_url('user/profile/'.$req['user_id']) ?>">
                            <img src="<?= (str_starts_with($req['avatar'] ?? '', 'http')) ? $req['avatar'] : base_url($req['avatar'] ?? 'ICON/avatar-default.svg') ?>" class="card-img">
                        </a>
                        <div class="card-body">
                            <div class="card-name">
                                <a href="<?= base_url('user/profile/'.$req['user_id']) ?>">
                                    <?= esc($req['firstname'] . ' ' . $req['lastname']) ?>
                                </a>
                            </div>
                            <button class="btn-full btn-confirm" onclick="acceptFriend('<?= $req['user_id'] ?>')">Confirm</button>
                            <button class="btn-full btn-delete" onclick="cancelRequest('<?= $req['user_id'] ?>')">Delete</button>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="empty-state">No new friend requests.</div>
        <?php endif; ?>


        <hr style="margin: 30px 0; border: 0; border-top: 1px solid #ddd;">
        
        <div class="section-title">All Friends</div>

        <?php if (!empty($friends)): ?>
            <div class="friends-grid">
                <?php foreach ($friends as $friend): ?>
                    <div class="friend-card">
                        <a href="<?= base_url('user/profile/'.$friend['user_id']) ?>">
                            <img src="<?= (str_starts_with($friend['avatar'] ?? '', 'http')) ? $friend['avatar'] : base_url($friend['avatar'] ?? 'ICON/avatar-default.svg') ?>" class="card-img">
                        </a>
                        <div class="card-body">
                            <div class="card-name">
                                <a href="<?= base_url('user/profile/'.$friend['user_id']) ?>">
                                    <?= esc($friend['firstname'] . ' ' . $friend['lastname']) ?>
                                </a>
                            </div>
                            <a href="<?= base_url('user/profile/'.$friend['user_id']) ?>" class="btn-full btn-delete" style="text-align:center; text-decoration:none;">View Profile</a>
                            <button class="btn-full btn-delete" onclick="unfriendUser('<?= $friend['user_id'] ?>')">Unfriend</button>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="empty-state">You don't have any friends yet. Add some!</div>
        <?php endif; ?>

    </div>

    <script src="<?= base_url('JS/navbar.js') ?>"></script>
    <script> lucide.createIcons(); </script>

    <script>
        async function sendFriendAction(url, dataObj) {
            let formData = new FormData();

            for (let key in dataObj) {
                formData.append(key, dataObj[key]);
            }

            if (APP_CONFIG.csrfTokenName && APP_CONFIG.csrfHash) {
                formData.append(APP_CONFIG.csrfTokenName, APP_CONFIG.csrfHash);
            }

            try {
                let response = await fetch(url, {
                    method: 'POST',
                    headers: { 'X-Requested-With': 'XMLHttpRequest' },
                    body: formData
                });

                if (response.status === 403) {
                    alert("Security token expired. Please reload the page.");
                    location.reload();
                    return;
                }

                let result = await response.json();
                
                if (result.status === 'success') {
                    location.reload(); 
                } else {
                    alert(result.message || 'Action failed');
                }
            } catch (error) {
                console.error("Error:", error);
            }
        }

        function acceptFriend(requesterId) {
            sendFriendAction(APP_CONFIG.baseURL + '/friend/accept', { 
                'requester_id': requesterId 
            });
        }

        function cancelRequest(targetId) {
            sendFriendAction(APP_CONFIG.baseURL + '/friend/remove', { 
                'target_id': targetId 
            });
        }

        function unfriendUser(targetId) {
            if(confirm("Are you sure you want to unfriend this person?")) {
                cancelRequest(targetId); 
            }
        }
    </script>
</body>
</html>