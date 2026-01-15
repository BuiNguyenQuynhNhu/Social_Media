<nav class="navbar">
    <div class="container nav-wrapper">
        
        <div class="nav-section left">
            <a href="<?= base_url('user/dashboard') ?>" class="logo">
                <img src="<?= base_url('ICON/logo.png') ?>" alt="Logo">
            </a>
            <form class="search-form" action="<?= base_url('search') ?>" method="get" autocomplete="off">
                <div class="search-container" style="position: relative;"> <button type="submit" class="search-btn"><i data-lucide="search"></i></button>
                    
                    <input class="input" 
                        id="search-input"
                        name="q" 
                        placeholder="Search Facebook..." 
                        type="text" 
                        value="<?= esc($keyword ?? '') ?>"
                        onkeyup="liveSearch(this.value)">
                        
                    <button class="reset" type="reset" onclick="hideSearch()">
                        <i data-lucide="x"></i>
                    </button>

                    <div id="search-dropdown" class="search-dropdown" style="display: none;">
                        </div>
                </div>
            </form>
        </div>

        <div class="nav-section center">
            <div class="nav-menu">
                <?php 
                    $currentUri = uri_string(); 
                ?>
                <a href="<?= base_url('user/dashboard') ?>" class="nav-item <?= ($currentUri == '' || $currentUri == '/' || strpos($currentUri, 'dashboard') !== false) ? 'active' : '' ?>" title="Home">
                    <i data-lucide="home"></i>
                </a>

                <a href="<?= base_url('friends') ?>" class="nav-item <?= (strpos($currentUri, 'friend') !== false) ? 'active' : '' ?>" style="position: relative;" title="Friends">
                    <i data-lucide="users"></i>
                    <span id="friend-badge" class="nav-badge"></span>
                </a>
                
                <a href="<?= base_url('group') ?>" class="nav-item <?= (strpos($currentUri, 'group') !== false) ? 'active' : '' ?>" title="Groups">
                    <i data-lucide="users-2"></i>
                </a>
                
                <a href="<?= base_url('message') ?>" class="nav-item <?= (strpos($currentUri, 'message') !== false || strpos($currentUri, 'chat') !== false) ? 'active' : '' ?>" title="Messages">
                    <i data-lucide="message-square"></i>
                </a>
                
                <span class="nav-line"></span>
            </div>
        </div>

        <div class="nav-section right">
            <div class="nav-user">
                <div class="icon-circle notification" title="Notifications" onclick="toggleNotifications()" style="position: relative;">
                    <i data-lucide="bell"></i>
                    <span id="notif-count" class="nav-badge" style="display: none; right: -5px; top: -5px;"></span>
                </div>

                <div class="user-profile">
                    <a href="<?= base_url('user/profile/') . session()->get('user_id')?>" class="avatar-link" title="Your Profile">
                        <img src="<?= $navbarAvatarUrl ?? $avatarUrl ?? base_url('ICON/avatar-default.svg') ?>" class="search-avatar" style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover;">
                    </a>
                </div>

                <a href="<?= base_url('logout') ?>" class="icon-circle logout" title="Logout">
                    <i data-lucide="log-out"></i>
                </a>
            </div>
        </div>
    </div>
    
    <script src="<?= base_url('JS/notifications.js') ?>"></script>
    <script src="<?= base_url('JS/search.js') ?>"></script>
    <script src="<?= base_url('JS/navbar.js') ?>"></script>
</nav>