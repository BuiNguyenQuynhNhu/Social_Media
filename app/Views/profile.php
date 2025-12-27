<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile</title>
    
    <script src="https://unpkg.com/lucide@latest"></script>
    
    <link rel="stylesheet" href="<?= base_url('CSS/navbar.css') ?>">
    <link rel="stylesheet" href="<?= base_url('CSS/profile.css') ?>">

    <style>
        .profile-container {
            min-height: calc(100vh - 80px);
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }
    </style>
</head>

<body class="background">

    <nav class="navbar">
        <div class="container">
            <a href="<?= base_url('/') ?>" class="logo">
                <img src="<?= base_url('ICON/logo.png') ?>" alt="Logo">
            </a>

            <div class="nav-menu">
                <a href="<?= base_url('dashboard') ?>" class="nav-item"><i data-lucide="home"></i></a>
                <a href="#" class="nav-item active"><i data-lucide="users"></i></a>
                <a href="#" class="nav-item"><i data-lucide="file-text"></i></a>
                <a href="#" class="nav-item"><i data-lucide="message-circle"></i></a>
                <span class="nav-line"></span>
            </div>

            <div class="nav-user">
                <div class="notification">
                    <i data-lucide="bell"></i>
                    <span class="dot"></span>
                </div>
                <?php $avatarUrl = $user['avatar'] && $user['avatar'] != 'default.png' 
                                    ? base_url('avatar/' . $user['avatar']) 
                                    : base_url('avatar/default.png'); ?>
                <img src="<?= $avatarUrl ?>" class="avatar">
                
                <span class="username">
                    <?= esc($user['firstname']) ?>
                </span>
                
                <a href="<?= base_url('logout') ?>" style="margin-left:10px; color: red;">
                    <i data-lucide="log-out" width="20"></i>
                </a>
            </div>
        </div>
    </nav>


    <div class="profile-container"> <div class="profile-card">
            
            <div class="profile-header" style="background-image: url('<?= base_url('ICON/cover-placeholder.jpg') ?>'); background-color: #333;">
                <div class="main-profile">
                    <div class="profile-image" style="background-image: url('<?= $avatarUrl ?>');"></div>
                    
                    <div class="profile-names">
                        <h1 class="username"><?= esc($user['firstname'] . ' ' . $user['lastname']) ?></h1>
                        <small class="page-title"><?= esc($user['email']) ?></small>
                    </div>
                </div>
            </div>

            <div class="profile-body">
                
                <div class="profile-actions">
                    <button class="follow">Follow</button> <button class="message" onclick="document.getElementById('edit-modal').style.display='block'">Edit Profile</button>
                    
                    <div class="bio">
                        <div class="bio-header">
                            <i data-lucide="info"></i>
                            <span>Bio</span>
                        </div>
                        <p class="bio-text">
                            <?= !empty($user['bio']) ? esc($user['bio']) : 'Chưa có giới thiệu bản thân.' ?>
                        </p>
                    </div>
                </div>

                <div class="account-info">
                    
                    <div class="data">
                        <div class="important-data">
                            <div class="data-item">
                                <span class="value">0</span>
                                <span class="label">Posts</span>
                            </div>
                            <div class="data-item">
                                <span class="value">0</span>
                                <span class="label">Followers</span>
                            </div>
                            <div class="data-item">
                                <span class="value">0</span>
                                <span class="label">Following</span>
                            </div>
                        </div>
                        
                        <div class="other-data">
                            <div class="data-item">
                                <span class="label">Gender</span>
                                <span class="value"><?= esc($user['gender']) ?></span>
                            </div>
                            <div class="data-item">
                                <span class="label">Birth</span>
                                <span class="value"><?= date('d M Y', strtotime($user['birth'])) ?></span>
                            </div>
                        </div>
                    </div>

                    <div class="social-media">
                        <span>Socials:</span>
                        <a href="#" class="media-link"><i data-lucide="facebook"></i></a>
                        <a href="#" class="media-link"><i data-lucide="twitter"></i></a>
                        <a href="#" class="media-link"><i data-lucide="instagram"></i></a>
                    </div>

                    <div class="last-post">
                        <div class="post-cover" style="background-image: url('<?= base_url('ICON/post-placeholder.jpg') ?>'); background-color: #555;">
                            <span class="last-badge">New</span>
                        </div>
                        <h3 class="post-title">My latest activity</h3>
                        <button class="post-CTA">View</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="edit-modal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.8); z-index:999;">
        <div style="background:white; width: 400px; margin: 100px auto; padding: 20px; border-radius: 10px;">
            <h2>Update Profile</h2>
            <form action="<?= base_url('user/update-info') ?>" method="post">
                <input type="text" name="firstname" value="<?= esc($user['firstname']) ?>" placeholder="Firstname" style="width:100%; margin-bottom:10px; padding:8px;">
                <input type="text" name="lastname" value="<?= esc($user['lastname']) ?>" placeholder="Lastname" style="width:100%; margin-bottom:10px; padding:8px;">
                <textarea name="bio" placeholder="Bio..." style="width:100%; margin-bottom:10px; padding:8px;"><?= esc($user['bio'] ?? '') ?></textarea>
                
                <div style="display: flex; justify-content: space-between;">
                    <button type="button" onclick="document.getElementById('edit-modal').style.display='none'" style="background:grey; color:white; border:none; padding:8px 15px; cursor:pointer;">Cancel</button>
                    <button type="submit" style="background:#4f46e5; color:white; border:none; padding:8px 15px; cursor:pointer;">Save</button>
                </div>
            </form>
            
            <hr style="margin: 20px 0;">
            
            <form action="<?= base_url('user/delete-account') ?>" method="post" onsubmit="return confirm('Bạn chắc chắn muốn xóa tài khoản?');">
                <input type="password" name="password" placeholder="Nhập mật khẩu để xóa" required style="width:100%; margin-bottom:10px; padding:8px;">
                <button type="submit" style="background:red; color:white; border:none; padding:8px; width:100%; cursor:pointer;">DELETE ACCOUNT</button>
            </form>
        </div>
    </div>

    <script src="<?= base_url('JS/navbar.js') ?>"></script>
    <script>
        lucide.createIcons();
    </script>
</body>
</html>