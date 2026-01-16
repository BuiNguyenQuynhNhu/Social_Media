
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
    const baseUrl = window.BASE_URL;
    if (!baseUrl) return;

    fetch(`${baseUrl}/notifications/`)
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                if (data.debug_user_id) {
                    console.log('🔔 Notification System Debug:', {
                        userId: data.debug_user_id,
                        count: data.data.length,
                        notifications: data.data
                    });
                }
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

    // Header logic
    let html = `
        <div class="notif-header">
            <span>Notifications</span>
        </div>
        <div class="notif-scroll-area">
    `;

    if (notifications.length === 0) {
        html += `
            <div class="notif-empty">
                <i data-lucide="bell-off" style="width:48px;height:48px;color:#bcc0c4;margin-bottom:12px;"></i>
                <div style="font-weight:600;color:#65676b;">No notifications</div>
            </div>`;
    } else {
        notifications.forEach(notif => {
            const isUnread = notif.status === 'pending'; // 'unread' is not in enum, pending is the new unread
            const unreadClass = isUnread ? 'unread' : '';
            let contextData = {};
            try {
                contextData = JSON.parse(notif.context || '{}');
            } catch (e) {
                console.error('Invalid JSON context for notification:', notif.id);
            }

            let message = getNotificationMessage(contextData.type, notif.sender_name, contextData, notif.id);
            let link = getNotificationLink(contextData);
            const type = contextData.type || '';

            // Icon Overlay Logic
            let iconOverlay = '';
            if (type.includes('like')) iconOverlay = '<div class="notif-icon-overlay icon-like"><i data-lucide="thumbs-up" style="width:12px;"></i></div>';
            else if (type.includes('comment')) iconOverlay = '<div class="notif-icon-overlay icon-comment"><i data-lucide="message-circle" style="width:12px;"></i></div>';
            else if (type.includes('friend')) iconOverlay = '<div class="notif-icon-overlay icon-friend"><i data-lucide="user-plus" style="width:12px;"></i></div>';
            else if (type.includes('group')) iconOverlay = '<div class="notif-icon-overlay icon-group"><i data-lucide="users" style="width:12px;"></i></div>';
            else if (type.includes('warning') || type.includes('ban') || type.includes('admin')) iconOverlay = '<div class="notif-icon-overlay icon-warning"><i data-lucide="alert-triangle" style="width:12px;"></i></div>';

            // Action Buttons Logic
            let actions = '';
            if (type === 'friend_request') {
                actions = `
                    <div class="notif-actions">
                        <button class="notif-btn notif-btn-primary" onclick="event.preventDefault(); handleFriendAction('${notif.id}', '${contextData.request_id || ''}', 'confirm')">Confirm</button>
                        <button class="notif-btn notif-btn-secondary" onclick="event.preventDefault(); handleFriendAction('${notif.id}', '${contextData.request_id || ''}', 'delete')">Delete</button>
                    </div>`;
            } else if (type === 'comment_add') {
                actions = `
                    <div class="notif-actions">
                        <button class="notif-btn notif-btn-secondary" onclick="event.preventDefault(); window.location.href='${link}'">Reply</button>
                    </div>`;
            } else if (type === 'group_invite') {
                actions = `
                    <div class="notif-actions">
                        <button class="notif-btn notif-btn-primary" onclick="event.preventDefault(); window.location.href='${link}'">View Invite</button>
                    </div>`;
            } else if (type === 'account_banned' || type === 'account_warning') {
                actions = `
                    <div class="notif-actions">
                        <button class="notif-btn notif-btn-danger" style="background:#fee2e2; color:#991b1b;" onclick="event.preventDefault(); event.stopPropagation(); showAppealModal('${notif.id}')">Appeal Suspension</button>
                    </div>`;
            }

            // Custom click handler for specific types
            let itemOnClick = `markAsRead('${notif.id}')`;
            if (type === 'account_banned' || type === 'account_warning') {
                // Open modal (div), do not navigate
                itemOnClick = `event.preventDefault(); markAsRead('${notif.id}'); showAppealModal('${notif.id}')`;
            }

            html += `
                <a href="${link}" class="notif-item ${unreadClass}" onclick="${itemOnClick}">
                    <div class="notif-avatar-wrapper">
                        <img src="${notif.sender_name === 'System Admin' ? window.BASE_URL + '/ICON/logo.png' : notif.sender_avatar}" class="notif-avatar" alt="${notif.sender_name}" 
                        onerror="this.onerror=null;this.src='${window.BASE_URL}/ICON/avatar-default.svg';">
                        ${iconOverlay}
                    </div>
                    <div class="notif-content">
                        <div>${message}</div>
                        <span class="notif-time ${!isUnread ? 'seen' : ''}">${notif.time_ago}</span>
                        ${actions}
                    </div>
                    ${isUnread ? '<div class="notif-unread-dot"></div>' : ''}
                </a>
            `;
        });
    }

    html += '</div>'; // Close scroll area

    dropdown.innerHTML = html;

    // Re-initialize icons just in case
    if (window.lucide) lucide.createIcons();
}

function handleFriendAction(notifId, requestId, action) {
    // Mark with specific action (confirm/delete) which maps to approved/cancelled
    markAsRead(notifId, action);
    // Redirect logic remains
    window.location.href = window.BASE_URL + '/friends/requests';
}

function getNotificationMessage(type, senderName, context, notifId) {
    const messages = {
        'friend_request': `<strong>${senderName}</strong> sent you a friend request`,
        'friend_accept': `<strong>${senderName}</strong> accepted your friend request`,
        'group_invite': `<strong>${senderName}</strong> invited you to join <strong>${context.group_name || 'a group'}</strong>`,
        'group_accept': `Your request to join <strong>${context.group_name || 'the group'}</strong> was approved`,
        'member_approved': `<strong>${senderName}</strong> approved your request to join <strong>${context.group_name || 'the group'}</strong>`,
        'post_approved': `Your post in <strong>${context.group_name || 'the group'}</strong> was approved`,
        'post_rejected': `Your post in <strong>${context.group_name || 'the group'}</strong> was rejected`,
        'new_join_request': `<strong>${senderName}</strong> wants to join <strong>${context.group_name || 'your group'}</strong>`,
        'group_kicked': `You have been removed from <strong>${context.group_name || 'a group'}</strong>`,
        'group_promoted': `You are now an admin of <strong>${context.group_name || 'a group'}</strong>`,
        'group_reject': `Your request to join <strong>${context.group_name || 'a group'}</strong> was declined`,
        // Admin notifications
        'account_banned': `⚠️ <strong>Account Suspended</strong><br>Reason: ${context.reason || 'Violation'}`,
        'account_unbanned': `✅ <strong>Account Reactivated</strong>`,
        'account_warning': `⚠️ <strong>Warning</strong><br>${context.reason || 'Please follow guidelines'}`,
        'post_removed_admin': `<strong>Post Removed</strong><br>Your post was removed: ${context.reason || 'content violation'}`,
        'group_removed_admin': `<strong>Group Removed</strong><br>Your group <strong>${context.group_name || ''}</strong> was removed`,
        // Appeal responses
        'ban_appeal': `<strong>${senderName}</strong> submitted a ban appeal`,
        'appeal_accepted': `✅ <strong>Appeal Accepted</strong><br>Your account is active.`,
        'appeal_rejected': `❌ <strong>Appeal Rejected</strong><br>${context.message || ''}`,
        // Interactions
        'post_like': `<strong>${senderName}</strong> liked your post`,
        'comment_add': `<strong>${senderName}</strong> commented on your post`,
        'post_reaction': `<strong>${senderName}</strong> reacted to your post`
    };

    return messages[type] || `New notification from <strong>${senderName}</strong>`;
}

function getNotificationLink(context) {
    const type = context.type;
    const baseUrl = window.BASE_URL;

    if (type === 'friend_request' || type === 'friend_accept') {
        return `${baseUrl}/friends`;
    }

    if (type === 'post_approved' && context.group_id) {
        return `${baseUrl}/group/${context.group_id}`;
    }

    if (type === 'post_rejected' || type === 'group_reject' || type === 'group_kicked') {
        return `${baseUrl}/user/dashboard`;
    }

    // Interactions link to post
    if ((type === 'post_like' || type === 'comment_add' || type === 'post_reaction') && context.post_id) {
        return `${baseUrl}/post/${context.post_id}`; // Assuming separate post view or just dashboard
    }

    if (type.includes('group') && context.group_id) {
        if (type === 'new_join_request') {
            return `${baseUrl}/group/${context.group_id}/manage/members`;
        }
        return `${baseUrl}/group/${context.group_id}`;
    }

    if (type === 'ban_appeal') {
        return `${baseUrl}/admin/appeals`;
    }

    if (type === 'account_banned' || type === 'account_warning') return '#'; // handled by modal

    return `${baseUrl}/user/dashboard`;
}

function getCookie(name) {
    const value = `; ${document.cookie}`;
    const parts = value.split(`; ${name}=`);
    if (parts.length === 2) return parts.pop().split(';').shift();
    return null;
}

function getCsrfToken() {
    // Try to get from cookie first (most up to date)
    const token = getCookie('csrf_cookie_name'); // Default CI4 cookie name
    if (token) return token;

    // Fallback to config (might be stale)
    return window.APP_CONFIG ? window.APP_CONFIG.csrfHash : '';
}

function markAsRead(notifId, action = 'read') {
    console.log(`Marking as ${action}:`, notifId);
    const baseUrl = window.BASE_URL;
    const csrfToken = getCsrfToken();
    const csrfName = window.APP_CONFIG ? window.APP_CONFIG.csrfTokenName : 'csrf_test_name';

    const formData = new FormData();
    formData.append(csrfName, csrfToken);
    formData.append('action', action);

    fetch(`${baseUrl}/notifications/read/${notifId}`, {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': csrfToken
        },
        body: formData
    })
        .then(response => response.json())
        .then(data => {
            console.log('Mark read response:', data);
            if (data.status === 'success') {
                checkUnreadNotifications();
            }
        })
        .catch(error => console.error('Error marking notification as read:', error));
}

function markAllRead() {
    const baseUrl = window.BASE_URL;
    fetch(`${baseUrl}/notifications/read-all`, {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': getCsrfToken()
        }
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
    const dot = document.getElementById('notif-count');
    if (!dot) return;

    const unreadCount = notifications.filter(n => n.status === 'pending').length;

    if (unreadCount > 0) {
        dot.style.display = 'block';
        dot.textContent = unreadCount > 9 ? '9+' : unreadCount;
    } else {
        dot.style.display = 'none';
    }
}

function checkUnreadNotifications() {
    const baseUrl = window.BASE_URL;
    if (!baseUrl) return;

    fetch(`${baseUrl}/notifications/`)
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

// ================== BAN APPEAL FUNCTIONS ==================

function showAppealModal(notificationId) {
    console.log('Opening Appeal Modal for:', notificationId);
    // Close notification dropdown
    const dropdown = document.getElementById('notif-dropdown');
    if (dropdown) dropdown.style.display = 'none';

    const modal = document.createElement('div');
    modal.id = 'appeal-modal';
    modal.innerHTML = `
        <div style="position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.6); z-index:10000; display:flex; justify-content:center; align-items:center;">
            <div style="background:white; padding:24px; border-radius:12px; max-width:500px; width:90%; box-shadow: 0 4px 12px rgba(0,0,0,0.15);">
                <h3 style="margin-top:0; margin-bottom:16px;">⚖️ Submit Ban Appeal</h3>
                <p style="color:#65676b; margin-bottom:16px;">Explain why you believe your ban should be lifted. You have 7 days to submit an appeal.</p>
                <textarea id="appeal-reason" placeholder="Write your appeal reason here..." style="width:100%; height:120px; padding:12px; border:1px solid #ddd; border-radius:8px; resize:none; font-family:inherit;"></textarea>
                <div style="display:flex; justify-content:flex-end; gap:10px; margin-top:16px;">
                    <button onclick="closeAppealModal()" style="padding:10px 20px; border:none; background:#e4e6eb; border-radius:6px; cursor:pointer; font-weight:600;">Cancel</button>
                    <button onclick="submitAppeal('${notificationId}')" style="padding:10px 20px; border:none; background:#1877f2; color:white; border-radius:6px; cursor:pointer; font-weight:600;">Submit Appeal</button>
                </div>
            </div>
        </div>
    `;
    document.body.appendChild(modal);
}

function closeAppealModal() {
    const modal = document.getElementById('appeal-modal');
    if (modal) modal.remove();
}

function submitAppeal(notificationId) {
    const reason = document.getElementById('appeal-reason').value.trim();

    if (!reason) {
        alert('Please provide a reason for your appeal.');
        return;
    }

    const baseUrl = window.BASE_URL;
    const csrfToken = getCsrfToken();
    const csrfName = window.APP_CONFIG ? window.APP_CONFIG.csrfTokenName : 'csrf_test_name';

    fetch(`${baseUrl}/appeal/submit`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `reason=${encodeURIComponent(reason)}&notification_id=${encodeURIComponent(notificationId)}&${csrfName}=${encodeURIComponent(csrfToken)}`
    })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                closeAppealModal();
                alert('Your appeal has been submitted. An admin will review it soon.');
                loadNotifications();
            } else {
                alert(data.message || 'Failed to submit appeal');
            }
        })
        .catch(error => {
            console.error('Error submitting appeal:', error);
            alert('Failed to submit appeal');
        });
}

