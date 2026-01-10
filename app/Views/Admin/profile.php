<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <script src="https://unpkg.com/lucide@latest"></script>
    
    <link rel="stylesheet" href="<?= base_url('CSS/navbar.css') ?>">
    <link rel="stylesheet" href="<?= base_url('CSS/overall.css') ?>">

    <style>
        .navbar {
            position: sticky; top: 0; z-index: 100; background: #fff; box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .profile-container { margin-top: 20px; }
    </style>
</head>

<body class="background">

    <?= view('templates/AdminNavbar', ['avatarUrl' => $avatarUrl]) ?>

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
                        <span>Amount of user noti: <strong></strong></span>
                    </div>
                    
                    <div class="intro-item">
                        <i data-lucide="cake"></i>
                        <span>Amount of group noti <strong></strong></span>
                    </div>
                    <div class="intro-item">
                        <i data-lucide="cake"></i>
                        <span>Amount of group chat noti <strong></strong></span>
                    </div>
                    <button class="btn btn-full-gray" onclick="document.getElementById('edit-modal').style.display='flex'">Edit Details</button>
                </div>
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