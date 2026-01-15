<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Search Results</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
        .search-results-page {
            padding: 20px;
            max-width: 800px;
            margin: 0 auto;
            margin-top: 20px;
        }
        .results-header {
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 1px solid #ddd;
        }
        .results-section h3 {
            font-size: 1.2rem;
            color: #65676b;
            margin-bottom: 15px;
        }
        .result-card {
            background: #fff;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 10px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 1px 2px rgba(0,0,0,0.1);
        }
        .card-left {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        .result-avatar {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            object-fit: cover;
        }
        .result-name {
            display: block;
            font-weight: 600;
            font-size: 1.1rem;
            color: #050505;
            text-decoration: none;
        }
        .result-name:hover {
            text-decoration: underline;
        }
        .badge-privacy {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 0.8rem;
            margin-top: 5px;
        }
        .badge-privacy.public { background: #e4e6eb; color: #050505; }
        .badge-privacy.private { background: #f0f2f5; color: #65676b; box-shadow: inset 0 0 0 1px #ced0d4; }
        
        .badge-friend { color: #31a24c; font-weight: 500; font-size: 0.9rem; }
        .badge-pending { color: #65676b; font-weight: 500; font-size: 0.9rem; }
        .badge-incoming { color: #1877f2; font-weight: 500; font-size: 0.9rem; }
    </style>
</head>
<body class="background">

    <?= view('components/user/navbar', ['avatarUrl' => $avatarUrl ?? base_url('ICON/avatar-default.svg')]) ?>

    <div class="search-results-page">
        <div class="results-header">
            <h2>Search Results for "<?= esc($results['query']) ?>"</h2>
        </div>

        <div class="results-section">
            <h3>People</h3>
            <?php if (empty($results['users'])): ?>
                <p class="text-muted">No people found.</p>
            <?php else: ?>
                <div class="results-grid">
                    <?php foreach ($results['users'] as $user): ?>
                        <div class="result-card user-card">
                            <div class="card-left">
                                <a href="<?= $user['url'] ?>">
                                    <img src="<?= $user['avatar'] ?>" alt="<?= esc($user['name']) ?>" class="result-avatar">
                                </a>
                                <div class="result-info">
                                    <a href="<?= $user['url'] ?>" class="result-name"><?= esc($user['name']) ?></a>
                                    <?php if (isset($user['status']) && $user['status'] !== 'self'): ?>
                                        <div class="friend-status">
                                            <?php if ($user['status'] === 'friend'): ?>
                                                <span class="badge-friend"><i data-lucide="check"></i> Friend</span>
                                            <?php elseif ($user['status'] === 'pending'): ?>
                                                <span class="badge-pending"><i data-lucide="clock"></i> Request Sent</span>
                                            <?php elseif ($user['status'] === 'incoming'): ?>
                                                <span class="badge-incoming">Request Received</span>
                                            <?php else: ?>
                                                <button onclick="sendFriendRequest(event, '<?= $user['id'] ?>')" class="btn-primary btn-sm" style="padding: 6px 12px; background: #1877f2; color: #fff; border: none; border-radius: 6px; cursor: pointer; font-weight: 600;">Add Friend</button>
                                            <?php endif; ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <div class="results-section mt-4">
            <h3>Groups</h3>
            <?php if (empty($results['groups'])): ?>
                <p class="text-muted">No groups found.</p>
            <?php else: ?>
                <div class="results-grid">
                    <?php foreach ($results['groups'] as $group): ?>
                        <div class="result-card group-card">
                            <div class="card-left">
                                <a href="<?= $group['url'] ?>">
                                    <img src="<?= $group['avatar'] ?>" alt="<?= esc($group['name']) ?>" class="result-avatar">
                                </a>
                                <div class="result-info">
                                    <a href="<?= $group['url'] ?>" class="result-name"><?= esc($group['name']) ?></a>
                                    <span class="badge-privacy <?= $group['privacy'] ?>"><?= ucfirst($group['privacy']) ?></span>
                                </div>
                            </div>
                            <div class="card-right">
                                <a href="<?= $group['url'] ?>" class="btn-secondary btn-sm" style="padding: 6px 12px; background: #e4e6eb; color: #000; text-decoration: none; border-radius: 6px; font-weight: 600;">View Group</a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>


</body>
</html>
