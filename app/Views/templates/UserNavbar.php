<nav class="navbar">
    <div class="container nav-wrapper">
        
        <div class="nav-section left">
            <a href="<?= base_url('dashboard') ?>" class="logo">
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
                <a href="<?= base_url('dashboard') ?>" class="nav-item <?= (uri_string() == 'dashboard' || uri_string() == '') ? 'active' : '' ?>">
                    <i data-lucide="home"></i>
                </a>

                <a href="<?= base_url('friends') ?>" class="nav-item <?= (uri_string() == 'friends') ? 'active' : '' ?>">
                    <i data-lucide="users"></i>
                </a>

                <a href="#" class="nav-item">
                    <i data-lucide="file-text"></i>
                </a>
                <a href="#" class="nav-item">
                    <i data-lucide="message-circle"></i>
                </a>
                
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
                    <a href="<?= base_url('user/profile') ?>" class="avatar-link" title="Your Profile">
                        <img src="<?= $avatarUrl ?? base_url('ICON/avatar-default.svg') ?>" class="search-avatar" style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover;">
                    </a>
                </div>

                <a href="<?= base_url('logout') ?>" class="icon-circle logout" title="Logout">
                    <i data-lucide="log-out"></i>
                </a>
            </div>
        </div>
    </div>
</nav>

<script>
    let timeout = null;

    function liveSearch(keyword) {
        const dropdown = document.getElementById('search-dropdown');
        
        if (keyword.length == 0) {
            dropdown.style.display = 'none';
            return;
        }

        clearTimeout(timeout);

        timeout = setTimeout(function() {
            fetch('<?= base_url("search/live") ?>?q=' + keyword) 
                .then(response => response.json())
                .then(data => {
                    let html = '';
                    
                    if (data.length > 0) {
                        data.forEach(user => {
                            const avatarSrc = (user.avatar && user.avatar.startsWith('http')) 
                                              ? user.avatar 
                                              : '<?= base_url() ?>' + (user.avatar || 'ICON/avatar-default.svg');

                            html += `
                                <a href="<?= base_url('user/profile/') ?>${user.id}" class="search-item">
                                    <img src="${avatarSrc}" class="search-avatar">
                                    <div class="search-info">
                                        <span class="search-name">${user.firstname} ${user.lastname}</span>
                                        <span class="search-sub">User</span>
                                    </div>
                                </a>
                            `;

                        });
                    }

                    dropdown.innerHTML = html;
                    dropdown.style.display = 'block';
                });
        }, 300);
    }

    document.addEventListener('click', function(e) {
        const container = document.querySelector('.search-container');
        if (!container.contains(e.target)) {
            document.getElementById('search-dropdown').style.display = 'none';
        }
    });
    
    function hideSearch() {
        document.getElementById('search-dropdown').style.display = 'none';
    }
</script>