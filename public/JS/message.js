/**
 * Message.js - Facebook Messenger Style Messaging
 */

let currentChatId = null;
let conversations = [];

$(document).ready(function () {
    loadConversations();
    setupEventListeners();

    // Poll for new messages every 5 seconds
    setInterval(function () {
        if (currentChatId) {
            loadMessages(currentChatId, false);
        }
        loadConversations();
    }, 5000);
});

/**
 * Load all conversations
 */
function loadConversations() {
    $.ajax({
        url: `${BASE_URL}/message/conversations`,
        type: 'GET',
        success: function (response) {
            if (response.status === 'success') {
                conversations = response.conversations;
                renderConversations(response.conversations);
            }
        },
        error: function (xhr) {
            console.error('Failed to load conversations:', xhr);
        }
    });
}

/**
 * Render conversation list
 */
function renderConversations(convos) {
    const $list = $('#conversation-list');

    if (!convos || convos.length === 0) {
        $list.html('<div style="padding: 20px; text-align: center; color: #65676b;">No conversations yet</div>');
        return;
    }

    $list.empty();

    convos.forEach(convo => {
        const isActive = currentChatId == convo.friend_id ? 'active' : '';
        const $item = $(`
            <div class="conversation-item ${isActive}" data-friend-id="${convo.friend_id}">
                <img src="${convo.avatar}" class="conversation-avatar" alt="${convo.name}">
                <div class="conversation-info">
                    <div class="conversation-name">${escapeHtml(convo.name)}</div>
                    <div class="conversation-preview">${escapeHtml(convo.last_message || 'No messages yet')}</div>
                </div>
                <div class="conversation-time">${timeAgo(convo.last_message_time)}</div>
            </div>
        `);

        $item.on('click', function () {
            openChat(convo.friend_id, convo.name, convo.avatar);
        });

        $list.append($item);
    });

    lucide.createIcons();
}

/**
 * Open a chat with a friend
 */
function openChat(friendId, friendName, friendAvatar) {
    currentChatId = friendId;

    // Update UI
    $('.conversation-item').removeClass('active');
    $(`.conversation-item[data-friend-id="${friendId}"]`).addClass('active');

    // Hide empty state, show chat
    $('.chat-empty-state').hide();
    $('#active-chat').show();

    // Update header
    $('#recipient-avatar').attr('src', friendAvatar);
    $('#recipient-name').text(friendName);

    // Load messages
    loadMessages(friendId, true);

    // Focus input
    $('#message-input').focus();
}

/**
 * Load messages for a chat
 */
function loadMessages(friendId, scrollToBottom = false) {
    $.ajax({
        url: `${BASE_URL}/message/chat/${friendId}`,
        type: 'GET',
        success: function (response) {
            if (response.status === 'success') {
                renderMessages(response.messages);
                if (scrollToBottom) {
                    scrollChatToBottom();
                }
            }
        },
        error: function (xhr) {
            console.error('Failed to load messages:', xhr);
        }
    });
}

/**
 * Render messages in chat
 */
function renderMessages(messages) {
    const $container = $('#chat-messages');
    const scrollPos = $container.scrollTop();
    const scrollHeight = $container[0].scrollHeight;
    const wasAtBottom = scrollPos + $container.innerHeight() >= scrollHeight - 50;

    $container.empty();

    if (!messages || messages.length === 0) {
        $container.html('<div style="padding: 20px; text-align: center; color: #65676b;">No messages yet. Start the conversation!</div>');
        return;
    }

    messages.forEach(msg => {
        const isSent = msg.sender_id == USER_ID;
        const messageClass = isSent ? 'sent' : 'received';

        const $messageGroup = $(`
            <div class="message-group ${messageClass}">
                ${!isSent ? `<img src="${msg.avatar}" class="message-avatar" alt="Avatar">` : ''}
                <div class="message-bubble">${escapeHtml(msg.content)}</div>
            </div>
        `);

        $container.append($messageGroup);
    });

    // Maintain scroll position or scroll to bottom
    if (wasAtBottom) {
        scrollChatToBottom();
    }
}

/**
 * Send a message
 */
function sendMessage() {
    const message = $('#message-input').val().trim();

    if (!message || !currentChatId) return;

    $.ajax({
        url: `${BASE_URL}/message/send`,
        type: 'POST',
        data: {
            receiver_id: currentChatId,
            content: message
        },
        success: function (response) {
            if (response.status === 'success') {
                $('#message-input').val('');
                loadMessages(currentChatId, true);
                loadConversations(); // Update conversation list
            } else {
                alert(response.message || 'Failed to send message');
            }
        },
        error: function (xhr) {
            console.error('Failed to send message:', xhr);
            alert('Failed to send message');
        }
    });
}

/**
 * Setup event listeners
 */
function setupEventListeners() {
    // Search conversations
    $('#search-conversations').on('input', function () {
        const query = $(this).val().toLowerCase();
        $('.conversation-item').each(function () {
            const name = $(this).find('.conversation-name').text().toLowerCase();
            $(this).toggle(name.includes(query));
        });
    });

    // Message input - enable/disable send button
    $('#message-input').on('input', function () {
        const hasText = $(this).val().trim().length > 0;
        $('#send-btn').prop('disabled', !hasText);
    });

    // Send message on button click
    $('#send-btn').on('click', sendMessage);

    // Send message on Enter key
    $('#message-input').on('keypress', function (e) {
        if (e.which === 13 && !e.shiftKey) {
            e.preventDefault();
            sendMessage();
        }
    });
}

/**
 * Scroll chat to bottom
 */
function scrollChatToBottom() {
    const $container = $('#chat-messages');
    $container.scrollTop($container[0].scrollHeight);
}

/**
 * Escape HTML to prevent XSS
 */
function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

/**
 * Format time ago
 */
function timeAgo(timestamp) {
    if (!timestamp) return '';

    const now = new Date();
    const time = new Date(timestamp);
    const diff = Math.floor((now - time) / 1000); // seconds

    if (diff < 60) return 'Just now';
    if (diff < 3600) return `${Math.floor(diff / 60)}m`;
    if (diff < 86400) return `${Math.floor(diff / 3600)}h`;
    if (diff < 604800) return `${Math.floor(diff / 86400)}d`;

    return time.toLocaleDateString();
}
