<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://unpkg.com/lucide@latest"></script>
    <link rel="stylesheet" href="<?= base_url('CSS/navbar.css') ?>">
</head>

<body class="background">

<nav class="navbar">
    <div class="container">

        <!-- LOGO -->
        <a href="<?= base_url('/') ?>" class="logo">
            <img src="<?= base_url('ICON/logo.png') ?>" alt="Logo">
        </a>

        <!-- NAV ICONS -->
        <div class="nav-menu">
                <a class="nav-item active"><i data-lucide="home"></i></a>
                <a class="nav-item"><i data-lucide="users"></i></a>
                <a class="nav-item"><i data-lucide="file-text"></i></a>
                <a class="nav-item"><i data-lucide="message-circle"></i></a>
                <span class="nav-line"></span>
        </div>

        <!-- USER -->
        <div class="nav-user">
            <div class="notification">
                <i data-lucide="bell"></i>
                <span class="dot"></span>
            </div>

            <!-- <img src="<?= base_url('avatar/default.png') ?>" class="avatar"> -->

            <span class="username">
                <!-- <?= session()->get('username') ?? 'Guest' ?> -->
            </span>
        </div>

    </div>
</nav>

<script src="<?= base_url('JS/navbar.js') ?>"></script>
</body>
</html>
