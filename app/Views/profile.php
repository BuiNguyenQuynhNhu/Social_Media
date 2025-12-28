<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($user['firstname'] . ' ' . $user['lastname']) ?>'s Profile</title>
    
    <script src="https://unpkg.com/lucide@latest"></script>
    
    <link rel="stylesheet" href="<?= base_url('CSS/navbar.css') ?>">
    <link rel="stylesheet" href="<?= base_url('CSS/profile.css') ?>">
    <link rel="stylesheet" href="<?= base_url('CSS/overall.css') ?>">

    <style>
        .navbar {
            position: sticky; top: 0; z-index: 100; background: #fff; box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .profile-container { margin-top: 20px; }
    </style>
</head>

<body class="background">

    <?= view('templates/navbar', ['avatarUrl' => $avatarUrl]) ?>

    <div class="profile-container">
        
        <div class="fb-card profile-header">
            </div>

        <div class="profile-body">
            
            <div class="left-column">
                <?php if(session()->getFlashdata('success')): ?>
                    <div style="background: #d4edda; color: #155724; padding: 10px; border-radius: 8px; margin-bottom: 10px;">
                        <?= session()->getFlashdata('success') ?>
                    </div>
                <?php endif; ?>

                <div class="fb-card intro-section">
                    <h3>Intro</h3>
                    
                    <div class="intro-item">
                        <i data-lucide="user"></i>
                        <span>Gender: <strong><?= esc($user['gender'] ?? 'Not set') ?></strong></span>
                    </div>
                    
                    <div class="intro-item">
                        <i data-lucide="cake"></i>
                        <span>Birthday: <strong><?= !empty($user['birth']) ? date('d F Y', strtotime($user['birth'])) : 'Not set' ?></strong></span>
                    </div>

                    <button class="btn btn-full-gray" onclick="document.getElementById('edit-modal').style.display='flex'">Edit Details</button>
                </div>
            </div>

            <div class="right-column">
                <div class="fb-card create-post">
                     <div style="padding: 15px; color: #65676b; background: #f0f2f5; border-radius: 20px; cursor: pointer;">
                        What's on your mind, <?= esc($user['firstname']) ?>?
                     </div>
                </div>
                
                <div class="fb-card post-card">
                    <div class="post-header">
                        <img src="<?= $avatarUrl ?>" class="small-avatar">
                        <div class="post-info">
                            <span class="author"><?= esc($user['firstname'] . ' ' . $user['lastname']) ?></span>
                            <span class="time">Just now <i data-lucide="globe" width="12"></i></span>
                        </div>
                    </div>
                    <div class="post-content">
                        <p>Hello world! Welcome to my profile.</p>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <div id="edit-modal" class="modal-overlay" style="display: none;">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Edit Profile</h2>
                <button onclick="document.getElementById('edit-modal').style.display='none'" class="close-btn"><i data-lucide="x"></i></button>
            </div>
            
            <form action="<?= base_url('user/update_profile') ?>" method="put" class="edit-form">
                <div style="display: flex; gap: 10px;">
                    <div class="form-group" style="flex: 1;">
                        <label>First Name</label>
                        <input type="text" name="firstname" value="<?= esc($user['firstname']) ?>" required>
                    </div>
                    <div class="form-group" style="flex: 1;">
                        <label>Last Name</label>
                        <input type="text" name="lastname" value="<?= esc($user['lastname']) ?>" required>
                    </div>
                </div>

                <div style="display: flex; gap: 10px; margin-top: 10px;">
                    <div class="form-group" style="flex: 1;">
                        <label>Birthday</label>
                        <input type="date" name="birth" value="<?= esc($user['birth']) ?>" required>
                    </div>
                    <div class="form-group" style="flex: 1;">
                        <label>Gender</label>
                        <select name="gender" style="width: 100%; padding: 10px; border-radius: 6px; border: 1px solid #ccc;">
                            <option value="Male" <?= ($user['gender'] == 'Male') ? 'selected' : '' ?>>Male</option>
                            <option value="Female" <?= ($user['gender'] == 'Female') ? 'selected' : '' ?>>Female</option>
                            <option value="Other" <?= ($user['gender'] == 'Other') ? 'selected' : '' ?>>Other</option>
                        </select>
                    </div>
                </div>

                <div class="form-actions" style="margin-top: 20px;">
                    <button type="submit" class="btn btn-primary" style="width: 100%" method= 'put'>Save Changes</button>
                </div>
            </form>
            
            <div class="danger-zone" style="margin-top: 30px; border-top: 1px solid #ddd; padding-top: 20px;">
                <h4 style="color: red; margin-bottom: 10px;">Danger Zone</h4>
                <form action="<?= base_url('user/delete_account') ?>" method="delete" onsubmit="return confirm('Are you sure you want to delete your account? This cannot be undone.');">
                    <div style="display: flex; gap: 10px;">
                        <input type="password" name="password" placeholder="Enter password to confirm" required style="flex: 1; padding: 8px;">
                        <button type="submit" class="btn btn-danger" style="background: red; color: white; border: none; padding: 8px 15px; border-radius: 5px;">Delete</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="<?= base_url('JS/navbar.js') ?>"></script>
    <script> 
        lucide.createIcons(); 
        
        window.onclick = function(event) {
            var modal = document.getElementById('edit-modal');
            if (event.target == modal) {
                modal.style.display = "none";
            }
        }
    </script>
</body>
</html>