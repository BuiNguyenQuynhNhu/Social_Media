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
                <a href="<?= base_url('user/dashboard') ?>" class="nav-item <?= (strpos(uri_string(), 'dashboard') !== false || uri_string() == '/' || uri_string() == '') ? 'active' : '' ?>" title="Home">
                    <i data-lucide="home"></i>
                </a>

                <a href="<?= base_url('friend/requests') ?>" class="nav-item <?= (strpos(uri_string(), 'friend/requests') !== false) ? 'active' : '' ?>" style="position: relative;" title="Friend Requests">
                    <i data-lucide="user-plus"></i>
                    <span id="friend-badge" class="nav-badge"></span>
                </a>
                
                <a href="<?= base_url('group') ?>" class="nav-item <?= (strpos(uri_string(), 'group') !== false) ? 'active' : '' ?>" title="Groups">
                    <i data-lucide="users-2"></i>
                </a>
                
                <a href="<?= base_url('chat') ?>" class="nav-item <?= (strpos(uri_string(), 'chat') !== false) ? 'active' : '' ?>" title="Chats">
                    <i data-lucide="message-square"></i>
                </a>
                
                <span class="nav-line"></span>
            </div>
        </div>

        <div class="nav-section right">
            <div class="nav-user">
                <div class="icon-circle notification" title="Notifications" onclick="toggleNotifications()" style="position: relative;">
                    <i data-lucide="bell"></i>
                    <span id="notif-dot" class="notif-dot" style="display: none;"></span>
                </div>

                <div class="user-profile">
                    <a href="<?= base_url('user/profile/') . session()->get('user_id')?>" class="avatar-link" title="Your Profile">
                        <img src="<?= $avatarUrl ?>" class="search-avatar" style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover;">
                    </a>
                </div>

                <a href="<?= base_url('logout') ?>" class="icon-circle logout" title="Logout">
                    <i data-lucide="log-out"></i>
                </a>
            </div>
        </div>
    </div>
</nav>