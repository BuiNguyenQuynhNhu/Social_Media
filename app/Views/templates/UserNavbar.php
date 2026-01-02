<nav class="navbar">
    <div class="container nav-wrapper">
        <div class="nav-section left">
            <a href="<?= base_url('dashboard') ?>" class="logo">
                <img src="<?= base_url('ICON/logo.png') ?>" alt="Logo">
            </a>
            <form class="search-form">
                <div class="search-container">
                    <button type="submit" class="search-btn"><i data-lucide="search"></i></button>
                    <input class="input" placeholder="Search..." type="text">
                    <button class="reset" type="reset"><i data-lucide="x"></i></button>
                </div>
            </form>
        </div>

        <div class="nav-section center">
            <div class="nav-menu">
                <a href="<?= base_url('dashboard') ?>" class="nav-item active"><i data-lucide="home"></i></a>
                <a href="#" class="nav-item"><i data-lucide="users"></i></a>
                <a href="#" class="nav-item"><i data-lucide="file-text"></i></a>
                <a href="#" class="nav-item"><i data-lucide="message-circle"></i></a>
                <span class="nav-line"></span>
            </div>
        </div>

        <div class="nav-section right">
            <div class="nav-user">
                <div class="icon-circle notification" title="Notifications">
                    <i data-lucide="bell"></i>
                    <span class="dot"></span>
                </div>

                <div class="user-profile">
                    <a href="<?= base_url('user/profile') ?>" class="avatar-link">
                        <img src="<?= $avatarUrl ?? base_url('ICON/avatar-default.svg') ?>" class="avatar" alt="Profile">
                    </a>
                    <a href="<?= base_url('logout') ?>" class="icon-circle logout" title="Logout">
                        <i data-lucide="log-out"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</nav>