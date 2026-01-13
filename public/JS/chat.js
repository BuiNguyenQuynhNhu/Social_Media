const BASE_URL = document.querySelector('meta[name="base_url"]')?.content || '';
const MY_ID = document.querySelector('meta[name="user_id"]')?.content || '';
let currentChatId = null;

/**
 * Initialize chat page
 */
$(document).ready(function () {
    lucide.createIcons();
    loadChats();

    $('#group-message-form').on('submit', function (e) {
        e.preventDefault();
        sendGroupMessage();
    });

    $('#group-message-input').on('keydown', function (e) {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            sendGroupMessage();
        }
    });

    // Auto-refresh chats every 10 seconds
    setInterval(() => {
        loadChats();
        if (currentChatId) {
            loadChatMessages(currentChatId);
        }
    }, 10000);
});

/**
 * Load all chats for current user
 */
function loadChats() {
    $.get(BASE_URL + '/chat/list', function (res) {
        if (res.status === 'success') {
            const list = $('#chat-list');
            list.empty();

            res.data.forEach(chat => {
                const unreadBadge = chat.unread_count > 0
                    ? `<span class="unread-badge">${chat.unread_count}</span>`
                    : '';

                const itemClass = chat.unread_count > 0 ? 'chat-item unread' : 'chat-item';
                const activeClass = currentChatId === chat.id ? ' active' : '';

                list.append(`
                    <div class="${itemClass}${activeClass}" onclick="openChatRoom('${chat.id}', '${escapeHtml(chat.name)}', '${chat.avatar || ''}')">
                        <img src="${chat.avatar || BASE_URL + '/images/default-group.png'}" class="avatar-medium" alt="${escapeHtml(chat.name)}">
                        <div class="chat-item-info">
                            <div class="chat-name">${escapeHtml(chat.name)}</div>
                            <div class="chat-preview">${escapeHtml(chat.last_message || 'No messages yet')}</div>
                        </div>
                        ${unreadBadge}
                    </div>
                `);
            });

            lucide.createIcons();
        }
    });
}

/**
 * Open a chat room
 */
function openChatRoom(chatId, name, avatar) {
    currentChatId = chatId;
    $('#chat-id').val(chatId);
    $('#chat-room-name').text(name);
    $('#chat-room-avatar').attr('src', avatar || BASE_URL + '/images/default-group.png');

    $('#chat-empty-state').hide();
    $('#chat-room-content').show();

    $('.chat-item').removeClass('active');
    $(event.target).closest('.chat-item').addClass('active');

    loadChatMessages(chatId);
    loadMembers(chatId);
}

/**
 * Load messages for specific chat
 */
function loadChatMessages(chatId) {
    $.get(BASE_URL + '/message/get/' + chatId + '?type=group', function (res) {
        if (res.status === 'success') {
            const container = $('#group-chat-messages');
            container.empty();

            res.data.forEach(msg => {
                const isMine = msg.sender_id === MY_ID;
                const wrapperClass = isMine ? 'group-message-wrapper mine' : 'group-message-wrapper';
                const bubbleClass = isMine ? 'message-bubble mine' : 'message-bubble';

                let content = '';

                if (msg.media) {
                    if (msg.media.type === 'video') {
                        content = `<video src="${msg.media.url}" controls style="max-width: 300px; border-radius: 12px;"></video>`;
                    } else {
                        content = `<img src="${msg.media.url}" style="max-width: 300px; border-radius: 12px;" alt="Media">`;
                    }
                }

                if (msg.content) {
                    content += `<p>${escapeHtml(msg.content)}</p>`;
                }

                container.append(`
                    <div class="${wrapperClass}">
                        ${!isMine ? `<img src="${msg.sender_avatar || BASE_URL + '/images/default-avatar.png'}" class="sender-avatar" alt="${escapeHtml(msg.sender_name)}">` : ''}
                        <div class="group-message-content">
                            ${!isMine ? `<div class="sender-name">${escapeHtml(msg.sender_name)}</div>` : ''}
                            <div class="${bubbleClass}">
                                ${content}
                                <div class="message-time">${formatTime(msg.created_at)}</div>
                            </div>
                        </div>
                    </div>
                `);
            });

            container.scrollTop(container[0].scrollHeight);
        }
    });
}

/**
 * Load members for specific chat
 */
function loadMembers(chatId) {
    $.get(BASE_URL + '/chat/members/' + chatId, function (res) {
        if (res.status === 'success') {
            const memberList = $('#member-list');
            const memberCount = $('#member-count');

            memberList.empty();
            memberCount.text(res.data.length + ' members');

            res.data.forEach(member => {
                const roleClass = member.is_admin ? 'admin' : '';
                const roleBadge = member.is_admin ? '<span class="role-badge">Admin</span>' : '';

                memberList.append(`
                    <div class="member-item ${roleClass}">
                        <img src="${member.avatar}" class="avatar-small" alt="${escapeHtml(member.name)}">
                        <div class="member-info">
                            <div class="member-name">${escapeHtml(member.nickname || member.name)}</div>
                            ${roleBadge}
                        </div>
                    </div>
                `);
            });

            lucide.createIcons();
        }
    });
}

/**
 * Send a group message
 */
function sendGroupMessage() {
    const form = $('#group-message-form')[0];
    const formData = new FormData(form);
    const chatId = $('#chat-id').val();

    if (!chatId) return;

    formData.append('chat_type', 'group');

    $.ajax({
        url: BASE_URL + '/message/send/' + chatId,
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function (res) {
            if (res.status === 'success') {
                $('#group-message-input').val('');
                $('#group-media-input').val('');
                loadChatMessages(chatId);
            }
        }
    });
}

/**
 * Toggle member sidebar visibility
 */
function toggleMemberSidebar() {
    const sidebar = $('#member-sidebar');
    sidebar.toggleClass('active');
}

/**
 * Open chat settings modal
 */
function openChatSettings() {
    $('#chat-settings-modal').fadeIn();
    lucide.createIcons();
}

/**
 * Close chat settings modal
 */
function closeChatSettings() {
    $('#chat-settings-modal').fadeOut();
}

/**
 * Update chat name
 */
function updateChatName() {
    const newName = $('#chat-name-input').val();
    if (!newName || !currentChatId) return;

    $.ajax({
        url: BASE_URL + '/chat/update/' + currentChatId,
        type: 'POST',
        data: { name: newName },
        success: function (res) {
            if (res.status === 'success') {
                $('#chat-room-name').text(newName);
                loadChats();
                alert('Chat name updated successfully!');
            }
        }
    });
}

/**
 * Update nickname
 */
function updateNickname() {
    const nickname = $('#nickname-input').val();
    if (!currentChatId) return;

    $.ajax({
        url: BASE_URL + '/chat/nickname/' + currentChatId,
        type: 'POST',
        data: { nickname: nickname },
        success: function (res) {
            if (res.status === 'success') {
                loadMembers(currentChatId);
                alert('Nickname updated successfully!');
            }
        }
    });
}

/**
 * Leave chat
 */
function leaveChat() {
    if (!currentChatId) return;

    if (confirm('Are you sure you want to leave this chat?')) {
        $.ajax({
            url: BASE_URL + '/chat/leave/' + currentChatId,
            type: 'POST',
            success: function (res) {
                if (res.status === 'success') {
                    closeChatSettings();
                    currentChatId = null;
                    $('#chat-room-content').hide();
                    $('#chat-empty-state').show();
                    loadChats();
                }
            }
        });
    }
}

/**
 * Open create chat modal
 */
function openCreateChatModal() {
    // Implementation for create chat modal
    alert('Create chat feature will be implemented based on your modal design preferences');
}

/**
 * Open add member modal
 */
function openAddMemberModal() {
    // Implementation for add member modal
    alert('Add member feature will be implemented based on your modal design preferences');
}

/**
 * Escape HTML to prevent XSS
 */
function escapeHtml(text) {
    const map = { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' };
    return text.replace(/[&<>"']/g, m => map[m]);
}

/**
 * Format timestamp
 */
function formatTime(dateString) {
    const date = new Date(dateString);
    return date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
}
