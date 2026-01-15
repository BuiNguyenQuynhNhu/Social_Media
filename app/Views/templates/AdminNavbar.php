<nav class="navbar">
    <div class="container nav-wrapper">
        <div class="nav-section left">
            <a href="<?= base_url('admin/dashboard') ?>" class="logo">
                <img src="<?= base_url('ICON/logo.png') ?>" alt="Logo">
            </a>
            <span style="font-weight:bold; font-size:18px; margin-left:10px;">Admin Panel</span>
        </div>

        <div class="nav-section right">
            <div class="nav-user">
                <!-- Notifications handled by notifications.js if included, but here we just show Logout -->
                <div class="user-profile">
                    <span style="font-weight:600; margin-right:10px;"><?= session()->get('firstname') ?> (Admin)</span>
                </div>
                <a href="<?= base_url('logout') ?>" class="icon-circle logout" title="Logout">
                    <i data-lucide="log-out"></i>
                </a>
            </div>
        </div>
    </div>
    <!-- Include Notifications JS if we want notifications in Admin panel (usually yes) -->
    <!-- But AdminNavbar usually simpler. Let's include basic scripts if needed in layout, but Navbar handles visual structure -->
    <!-- Note: notifications.js is loaded in dashboard footer or body in UserNavbar. Here we might omit or include. 
         Admin usually doesn't need "friend request" notifications unless they are also a user. 
         Let's keep it simple. -->
</nav>
