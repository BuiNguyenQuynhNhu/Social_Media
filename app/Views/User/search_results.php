<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Search Results</title>
    <script src="https://unpkg.com/lucide@latest"></script>
    <link rel="stylesheet" href="<?= base_url('CSS/navbar.css') ?>">
    <link rel="stylesheet" href="<?= base_url('CSS/overall.css') ?>">
    
    <style>
        .search-container-page { max-width: 800px; margin: 20px auto; padding: 0 15px; }
        .result-title { font-size: 20px; font-weight: 700; margin-bottom: 20px; }
        
        /* List Style (Hiển thị dạng danh sách dọc) */
        .user-list-item {
            display: flex;
            align-items: center;
            background: white;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 10px;
            box-shadow: 0 1px 2px rgba(0,0,0,0.1);
            transition: 0.2s;
        }
        .user-list-item:hover { background: #f2f2f2; }
        
        .result-avatar {
            width: 60px; height: 60px;
            border-radius: 50%; object-fit: cover;
            margin-right: 15px;
        }
        
        .result-info { flex: 1; }
        .result-name { font-weight: 600; font-size: 16px; color: #050505; text-decoration: none; }
        .result-email { font-size: 13px; color: #65676b; }
        
        .btn-view {
            padding: 8px 20px;
            background: #e4e6eb;
            color: black;
            border-radius: 6px;
            text-decoration: none;
            font-weight: 600;
            font-size: 14px;
        }
        .btn-view:hover { background: #d8dadf; }
    </style>
</head>
<body class="background">

    <?= view('templates/UserNavbar', ['avatarUrl' => $avatarUrl, 'keyword' => $keyword]) ?>

    <div class="search-container-page">
        <div class="result-title">
            Search results for "<?= esc($keyword) ?>"
        </div>

        <?php if (!empty($users)): ?>
            <?php foreach ($users as $u): ?>
                <div class="user-list-item">
                    <a href="<?= base_url('user/profile/'.$u['id']) ?>">
                        <img src="<?= (str_starts_with($u['avatar'] ?? '', 'http')) ? $u['avatar'] : base_url($u['avatar'] ?? 'ICON/avatar-default.svg') ?>" class="result-avatar">
                    </a>
                    
                    <div class="result-info">
                        <a href="<?= base_url('user/profile/'.$u['id']) ?>" class="result-name">
                            <?= esc($u['firstname'] . ' ' . $u['lastname']) ?>
                        </a>
                        <div class="result-email"><?= esc($u['email']) ?></div>
                    </div>

                    <a href="<?= base_url('user/profile/'.$u['id']) ?>" class="btn-view">
                        View Profile
                    </a>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div style="text-align: center; color: #65676b; margin-top: 50px;">
                <i data-lucide="search-x" style="width: 48px; height: 48px; margin-bottom: 10px;"></i>
                <p>No users found matching "<?= esc($keyword) ?>"</p>
            </div>
        <?php endif; ?>
    </div>

    <script src="<?= base_url('JS/navbar.js') ?>"></script>
    <script> lucide.createIcons(); </script>
</body>
</html>