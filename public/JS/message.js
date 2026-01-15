/**
 * Message.js - Facebook Messenger Style Messaging
 */

let currentChatId = null;
let conversations = [];

// Helper function to get cookie value
function getCookie(name) {
    if (!name) return null;
    let match = document.cookie.match(new RegExp('(^| )' + name + '=([^;]+)'));
    if (match) return decodeURIComponent(match[2]);
    return null;
}

$(document).ready(function () {
    // Setup CSRF for all AJAX requests
    $.ajaxSetup({
        beforeSend: function (xhr, settings) {
            if (!/^(GET|HEAD|OPTIONS|TRACE)$/i.test(settings.type) && !this.crossDomain) {
                const token = getCookie('csrf_cookie_name') || (window.APP_CONFIG && window.APP_CONFIG.csrfHash);
                if (token) {
                    xhr.setRequestHeader('X-CSRF-TOKEN', token);
                }
            }
        }
    });

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
                conversations = response.data || [];
                renderConversations(conversations);
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
                    <div class="conversation-preview">${escapeHtml(convo.last_message)}</div>
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
        url: `${BASE_URL}/message/get/${friendId}`,
        type: 'GET',
        success: function (response) {
            if (response.status === 'success') {
                renderMessages(response.data || []);
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
        const isSent = msg.is_mine;
        const messageClass = isSent ? 'sent' : 'received';

        let mediaHtml = '';
        if (msg.media) {
            if (msg.media.type === 'image') {
                mediaHtml = `<img src="${msg.media.url}" style="max-width: 250px; border-radius: 12px; display: block; margin-bottom: 4px;" alt="Image">`;
            } else if (msg.media.type === 'video') {
                mediaHtml = `<video src="${msg.media.url}" controls style="max-width: 250px; border-radius: 12px; display: block; margin-bottom: 4px;"></video>`;
            }
        }

        const contentHtml = msg.content ? `<div class="message-bubble">${escapeHtml(msg.content)}</div>` : '';

        const $messageGroup = $(`
            <div class="message-group ${messageClass}">
                ${mediaHtml}
                ${contentHtml}
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
    const fileInput = document.getElementById('message-file-input');
    const file = fileInput && fileInput.files.length > 0 ? fileInput.files[0] : null;

    if (!message && !file) return;
    if (!currentChatId) return;

    const formData = new FormData();
    formData.append('content', message || '');
    if (file) {
        formData.append('media', file);
    }

    $.ajax({
        url: `${BASE_URL}/message/send/${currentChatId}`,
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function (response) {
            if (response.status === 'success') {
                $('#message-input').val('');
                if (fileInput) {
                    fileInput.value = '';
                    $('#media-preview').hide().empty();
                }
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
        const hasFile = $('#message-file-input')[0] && $('#message-file-input')[0].files.length > 0;
        $('#send-btn').prop('disabled', !hasText && !hasFile);
    });

    // Media file selection
    $('#message-file-input').on('change', function () {
        const file = this.files[0];
        if (file) {
            const preview = $('#media-preview');
            const reader = new FileReader();
            reader.onload = function (e) {
                let html = '';
                if (file.type.startsWith('image')) {
                    html = `<img src="${e.target.result}" style="max-height: 100px; border-radius: 8px;">`;
                } else if (file.type.startsWith('video')) {
                    html = `<video src="${e.target.result}" style="max-height: 100px; border-radius: 8px;" controls></video>`;
                }
                preview.html(html + ' <button onclick="clearMediaPreview()" style="margin-left: 10px;">Remove</button>').show();
                $('#send-btn').prop('disabled', false);
            };
            reader.readAsDataURL(file);
        }
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

function clearMediaPreview() {
    $('#message-file-input').val('');
    $('#media-preview').hide().empty();
    const hasText = $('#message-input').val().trim().length > 0;
    $('#send-btn').prop('disabled', !hasText);
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
