<nav class="navbar">
    <div class="container">

        <a href="<?= base_url('dashboard') ?>" class="logo">
            <img src="<?= base_url('ICON/logo.png') ?>" alt="Logo">
        </a>

        <div class="nav-menu">
            <a href="<?= base_url('dashboard') ?>" class="nav-item"><i data-lucide="home"></i></a>
            <a href="#" class="nav-item"><i data-lucide="users"></i></a>
            <a href="#" class="nav-item"><i data-lucide="file-text"></i></a>
            <a href="#" class="nav-item"><i data-lucide="message-circle"></i></a>
            <span class="nav-line"></span>
        </div>

        <div class="nav-user">
            <div class="notification">
                <i data-lucide="bell"></i>
                <span class="dot"></span>
            </div>

            <div class="user-dropdown" style="display: flex; align-items: center; gap: 10px; cursor: pointer;">
                <img src="<?= isset($avatarUrl) ? $avatarUrl : base_url('ICON/avatar-default.svg') ?>" class="avatar" style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover;">
                
                <a href="<?= base_url('logout') ?>" class="btn-logout" title="Logout">
                    <i data-lucide="log-out" style="color: #65676b;"></i>
                </a>
            </div>
        </div>

    </div>
</nav>