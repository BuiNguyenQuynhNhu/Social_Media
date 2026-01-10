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
<body class = 'background'>
    <?= view('templates/AdminNavbar') ?>
</body>
</html>