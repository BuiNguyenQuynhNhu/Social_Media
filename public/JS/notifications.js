// Notification System
let notificationInterval;
let notificationDropdown = null;

function toggleNotifications() {
    if (!notificationDropdown) {
        notificationDropdown = document.getElementById('notif-dropdown');
        if (!notificationDropdown) {
            createNotificationDropdown();
        }
    }

    const dropdown = document.getElementById('notif-dropdown');
    if (dropdown.style.display === 'none' || dropdown.style.display === '') {
        loadNotifications();
        dropdown.style.display = 'block';
    } else {
        dropdown.style.display = 'none';
    }
}

function createNotificationDropdown() {
    const notifContainer = document.querySelector('.notification');
    if (!notifContainer) return;

    const dropdown = document.createElement('div');
    dropdown.id = 'notif-dropdown';
    dropdown.className = 'notif-dropdown';
    dropdown.style.display = 'none';

    notifContainer.appendChild(dropdown);
}

function loadNotifications() {
    fetch(`${BASE_URL}/notifications/`)
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                renderNotifications(data.data);
                updateNotificationDot(data.data);
            }
        })
        .catch(error => {
            console.error('Error loading notifications:', error);
        });
}

function renderNotifications(notifications) {
    const dropdown = document.getElementById('notif-dropdown');
    if (!dropdown) return;

    let html = '<div class="notif-header">Notifications</div>';

    if (notifications.length === 0) {
        html += '<div class="notif-empty">No notifications yet</div>';
    } else {
        notifications.forEach(notif => {
            const unreadClass = notif.status === 'unread' ? 'unread' : '';
            const contextData = JSON.parse(notif.context || '{}');

            let message = getNotificationMessage(contextData.type, notif.sender_name, contextData);
            let link = getNotificationLink(contextData);

            html += `
                <a href="${link}" class="notif-item ${unreadClass}" onclick="markAsRead('${notif.id}')">
                    <img src="${notif.sender_avatar}" class="notif-avatar" alt="${notif.sender_name}">
                    <div class="notif-content">
                        <div>${message}</div>
                        <span class="notif-time">${notif.time_ago}</span>
                    </div>
                </a>
            `;
        });

        html += '<div class="notif-footer"><button onclick="markAllRead()" class="mark-all-btn">Mark all as read</button></div>';
    }

    dropdown.innerHTML = html;
}

function getNotificationMessage(type, senderName, context) {
    const messages = {
        'friend_request': `<strong>${senderName}</strong> sent you a friend request`,
        'friend_accept': `<strong>${senderName}</strong> accepted your friend request`,
        'group_invite': `<strong>${senderName}</strong> invited you to join <strong>${context.group_name || 'a group'}</strong>`,
        'group_accept': `<strong>${senderName}</strong> accepted your request to join <strong>${context.group_name || 'the group'}</strong>`,
        'member_approved': `<strong>${senderName}</strong> approved your request to join <strong>${context.group_name || 'the group'}</strong>`,
        'post_approved': `Your post in <strong>${context.group_name || 'the group'}</strong> was approved`,
        'new_join_request': `<strong>${senderName}</strong> wants to join <strong>${context.group_name || 'your group'}</strong>`
    };

    return messages[type] || `New notification from <strong>${senderName}</strong>`;
}

function getNotificationLink(context) {
    const type = context.type;

    if (type === 'friend_request' || type === 'friend_accept') {
        return `${BASE_URL}/friends`;
    }

    if (type.includes('group') && context.group_id) {
        if (type === 'new_join_request') {
            return `${BASE_URL}/group/${context.group_id}/manage/members`;
        }
        return `${BASE_URL}/group/${context.group_id}`;
    }

    return `${BASE_URL}/user/dashboard`;
}

function markAsRead(notifId) {
    fetch(`${BASE_URL}/notifications/read/${notifId}`, {
        method: 'POST'
    })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                checkUnreadNotifications();
            }
        })
        .catch(error => console.error('Error marking notification as read:', error));
}

function markAllRead() {
    fetch(`${BASE_URL}/notifications/read-all`, {
        method: 'POST'
    })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                loadNotifications();
                updateNotificationDot([]);
            }
        })
        .catch(error => console.error('Error marking all as read:', error));
}

function updateNotificationDot(notifications) {
    const dot = document.getElementById('notif-dot');
    if (!dot) return;

    const unreadCount = notifications.filter(n => n.status === 'unread').length;

    if (unreadCount > 0) {
        dot.style.display = 'block';
        dot.textContent = unreadCount > 9 ? '9+' : unreadCount;
    } else {
        dot.style.display = 'none';
    }
}

function checkUnreadNotifications() {
    fetch(`${BASE_URL}/notifications/`)
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                updateNotificationDot(data.data);
            }
        })
        .catch(error => console.error('Error checking notifications:', error));
}

// Poll for new notifications every 30 seconds
function startNotificationPolling() {
    checkUnreadNotifications(); // Check immediately
    notificationInterval = setInterval(checkUnreadNotifications, 30000);
}

// Close dropdown when clicking outside
document.addEventListener('click', function (event) {
    const notifContainer = document.querySelector('.notification');
    const dropdown = document.getElementById('notif-dropdown');

    if (dropdown && notifContainer && !notifContainer.contains(event.target)) {
        dropdown.style.display = 'none';
    }
});

// Start polling when page loads
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', startNotificationPolling);
} else {
    startNotificationPolling();
}
