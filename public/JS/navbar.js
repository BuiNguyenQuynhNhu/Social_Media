lucide.createIcons();

const navMenu = document.querySelector('.nav-menu');
const navItems = document.querySelectorAll('.nav-item');
const line = document.querySelector('.nav-line');
const searchInput = document.querySelector('.search-form .input');
const searchForm = document.querySelector('.search-form');

function moveLine(target) {
    if (!target) return;
    const rect = target.getBoundingClientRect();
    const menuRect = navMenu.getBoundingClientRect();

    if(line) {
        line.style.width = rect.width + 'px';
        line.style.left = (rect.left - menuRect.left) + 'px';
    }
}

const activeItem = document.querySelector('.nav-item.active');
if (activeItem) {
    setTimeout(() => moveLine(activeItem), 100);
}

navItems.forEach(item => {
    item.addEventListener('click', () => {
        navItems.forEach(i => i.classList.remove('active'));
        item.classList.add('active');
        moveLine(item);
    });
});

window.addEventListener('resize', () => {
    const active = document.querySelector('.nav-item.active');
    if (active) moveLine(active);
});


/* =========================================
   2. SEARCH LOGIC
   ========================================= */
let searchTimeout = null;

function liveSearch(keyword) {
    const dropdown = document.getElementById('search-dropdown');
    
    if (!keyword || keyword.trim().length === 0) {
        if(dropdown) dropdown.style.display = 'none';
        return;
    }

    clearTimeout(searchTimeout);

    searchTimeout = setTimeout(function() {
        if(typeof APP_CONFIG === 'undefined') return;

        fetch(APP_CONFIG.baseURL + '/search/live?q=' + encodeURIComponent(keyword)) 
            .then(response => response.json())
            .then(data => {
                let html = '';
                
                if (data.length > 0) {
                    data.forEach(user => {
                        const avatarSrc = (user.avatar && user.avatar.startsWith('http')) 
                                          ? user.avatar 
                                          : APP_CONFIG.baseURL + '/' + (user.avatar || 'ICON/avatar-default.svg');

                        html += `
                            <a href="${APP_CONFIG.baseURL}/user/profile/${user.id}" class="search-item">
                                <img src="${avatarSrc}" class="search-avatar">
                                <div class="search-info">
                                    <span class="search-name">${user.firstname} ${user.lastname}</span>
                                    <span class="search-sub">User</span>
                                </div>
                            </a>
                        `;
                    });
                } else {
                    html = '<div style="padding:10px; text-align:center; color:#65676b;">No results found</div>';
                }

                if(dropdown) {
                    dropdown.innerHTML = html;
                    dropdown.style.display = 'block';
                }
            })
            .catch(err => console.error(err));
    }, 300);
}

function hideSearch() {
    const dropdown = document.getElementById('search-dropdown');
    if(dropdown) dropdown.style.display = 'none';
}


/* =========================================
   3. NOTIFICATION LOGIC 
   ========================================= */

function loadNotifications() {
    if(typeof APP_CONFIG === 'undefined') return;

    fetch(APP_CONFIG.baseURL + '/notifications/get')
    .then(res => res.json())
    .then(res => {
        if(res.status === 'success') {
            const countSpan = document.getElementById('notif-count');
            const list = document.getElementById('notif-list');
            
            // Xử lý chấm đỏ
            if(res.unread > 0 && countSpan) {
                countSpan.innerText = res.unread;
                countSpan.style.display = 'block';
            } else if (countSpan) {
                countSpan.style.display = 'none';
            }

            // Xử lý chấm đỏ Tab Friend
            const friendBadge = document.getElementById('friend-badge');
            if (friendBadge) {
                if (res.pending_friends > 0) {
                    friendBadge.innerText = res.pending_friends; 
                    friendBadge.style.display = 'block';
                } else {
                    friendBadge.style.display = 'none';
                }
            }

            // Render danh sách
            let html = '';
            if(res.data.length === 0) {
                html = '<div style="padding:20px; text-align:center; color:#65676b;">No notifications yet</div>';
            } else {
                res.data.forEach(n => {
                    let text = '';
                    let link = '#';
                    const fname = n.firstname || 'Someone';
                    const lname = n.lastname || '';
                    const name = `<strong>${fname} ${lname}</strong>`;
                    
                    if(n.type === 'friend_request') {
                        text = `${name} sent you a friend request.`;
                        link = APP_CONFIG.baseURL + '/friends';
                    } else if (n.type === 'friend_accept') {
                        text = `${name} accepted your friend request.`;
                        link = APP_CONFIG.baseURL + '/user/profile/' + n.actor_id;
                    } else if (n.type === 'post_like') {
                        text = `${name} liked your post.`;
                        link = APP_CONFIG.baseURL + '/user/profile/' + n.user_id + '#post-' + n.reference_id;
                    } else if (n.type === 'post_comment') {
                        text = `${name} commented on your post.`;
                        link = APP_CONFIG.baseURL + '/user/profile/' + n.user_id + '#post-' + n.reference_id;
                    }

                    let avatar = n.avatar || 'ICON/avatar-default.svg';
                    if(!avatar.startsWith('http')) avatar = APP_CONFIG.baseURL + '/' + avatar;

                    const unreadClass = (n.is_read == 0) ? 'unread' : '';

                    // QUAN TRỌNG: Đã thêm dấu nháy đơn bao quanh ${n.id}
                    html += `
                        <div onclick="handleNotifClick(event, '${n.id}', '${link}')" class="notif-item ${unreadClass}" style="cursor:pointer;">
                            <img src="${avatar}" class="notif-avatar">
                            <div class="notif-content">
                                <div>${text}</div>
                                <span class="notif-time">${timeAgo(n.created_at)}</span>
                            </div>
                            ${n.is_read == 0 ? '<div style="width:10px; height:10px; background:#1877f2; border-radius:50%;"></div>' : ''}
                        </div>
                    `;
                });
            }
            if(list) list.innerHTML = html;
        }
    });
}

function handleNotifClick(e, notifId, link) {
    e.preventDefault(); 

    // Gửi kèm CSRF token để tránh lỗi 403
    let headers = { 'X-Requested-With': 'XMLHttpRequest' };
    if (APP_CONFIG.csrfTokenName && APP_CONFIG.csrfHash) {
        headers['X-CSRF-TOKEN'] = APP_CONFIG.csrfHash;
    }

    fetch(APP_CONFIG.baseURL + '/notifications/read-item/' + notifId, { 
        method: 'POST',
        headers: headers
    }).then(() => {
        window.location.href = link;
    }).catch(() => {
        // Nếu lỗi mạng vẫn chuyển trang
        window.location.href = link;
    });
}

function toggleNotifications() {
    const dropdown = document.getElementById('notif-dropdown');
    if (!dropdown) return;

    const isClosed = dropdown.style.display === 'none' || dropdown.style.display === '';
    
    const searchDD = document.getElementById('search-dropdown');
    if(searchDD) searchDD.style.display = 'none';

    if (isClosed) {
        dropdown.style.display = 'block';
        const countSpan = document.getElementById('notif-count');
        if(countSpan) countSpan.style.display = 'none';
    } else {
        dropdown.style.display = 'none';
    }
}

function timeAgo(dateString) {
    if (!dateString) return 'Just now';
    let t = dateString.split(/[- :]/);
    let date = new Date(t[0], t[1]-1, t[2], t[3], t[4], t[5]);
    let now = new Date();
    let seconds = Math.floor((now - date) / 1000);
    if (seconds < 60) return "Just now";
    let minutes = Math.floor(seconds / 60);
    if (minutes < 60) return minutes + "m ago";
    let hours = Math.floor(minutes / 60);
    if (hours < 24) return hours + "h ago";
    return Math.floor(hours / 24) + "d ago";
}

document.addEventListener('DOMContentLoaded', () => {
    loadNotifications();
    setInterval(loadNotifications, 10000);
});

document.addEventListener('click', function(e) {
    const notif = document.querySelector('.notification');
    const dropdown = document.getElementById('notif-dropdown');
    
    if (notif && dropdown && !notif.contains(e.target)) {
        dropdown.style.display = 'none';
    }

    const searchContainer = document.querySelector('.search-container');
    if (searchContainer && !searchContainer.contains(e.target)) {
        hideSearch();
    }
});

