<nav class="navbar">
    <div class="nav-wrapper">
        <div class="nav-section left">
            <a href="<?= base_url('admin/dashboard') ?>" class="logo">
                <img src="<?= base_url('ICON/logo.png') ?>" alt="Logo">
            </a>
            <form class="search-form">
                <div class="search-container">
                    <button type="submit" class="search-btn"><i data-lucide="search"></i></button>
                    <input class="input" placeholder="Search system..." type="text">
                    <button class="reset" type="reset"><i data-lucide="x"></i></button>
                </div>
            </form>
        </div>

        <div class="nav-section center">
            <div class="nav-menu">
                <a href="<?= base_url('admin/users') ?>" class="nav-item <?= ($title == 'Users') ? 'active' : '' ?>" title="List Users">
                    <i data-lucide="users"></i>
                </a>
                <a href="<?= base_url('admin/groups') ?>" class="nav-item <?= ($title == 'Groups') ? 'active' : '' ?>" title="List Groups">
                    <i data-lucide="layers"></i>
                </a>
                <a href="<?= base_url('admin/chats') ?>" class="nav-item <?= ($title == 'Chats') ? 'active' : '' ?>" title="List Chats">
                    <i data-lucide="message-square"></i>
                </a>
                <a href="<?= base_url('admin/notifications') ?>" class="nav-item <?= ($title == 'Notifications') ? 'active' : '' ?>" title="Send Email/Noti">
                    <i data-lucide="mail"></i>
                </a>
                <span class="nav-line"></span>
            </div>
        </div>

        <div class="nav-section right">
            <div class="nav-user">
                <div class="user-profile">
                    <?php 
                        $email = $adminData['email'] ?? 'Admin';
                        $username = explode('@', $email)[0]; 
                    ?>
                    
                    <span class="admin-name"><?= esc($username) ?></span>

                    <a href="<?= base_url('admin/profile') ?>" class="avatar-link">
                        <img src="<?= $adminData['avatar'] ?? base_url('ICON/avatar-default.svg') ?>" class="avatar">
                    </a>
                    <a href="<?= base_url('logout') ?>" class="icon-circle logout" title="Logout">
                        <i data-lucide="log-out"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</nav>