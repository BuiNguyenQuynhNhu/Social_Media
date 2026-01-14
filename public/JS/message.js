const BASE_URL = document.querySelector('meta[name="base_url"]')?.content || '';
const MY_ID = document.querySelector('meta[name="user_id"]')?.content || '';
let currentFriendId = null;

/**
 * Initialize message page
 */
$(document).ready(function () {
    lucide.createIcons();
    loadConversations();

    $('#message-form').on('submit', function (e) {
        e.preventDefault();
        sendMessage();
    });

    $('#message-input').on('keydown', function (e) {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            sendMessage();
        }
    });

    // Auto-refresh conversations every 10 seconds
    setInterval(loadConversations, 10000);
});

/**
 * Load all conversations for current user
 */
function loadConversations() {
    $.get(BASE_URL + '/message/conversations', function (res) {
        if (res.status === 'success') {
            const list = $('#conversation-list');
            list.empty();

            res.data.forEach(conv => {
                const unreadBadge = conv.unread_count > 0
                    ? `<span class="unread-badge">${conv.unread_count}</span>`
                    : '';

                const isActive = (currentFriendId === conv.friend_id) ? 'active' : '';
                const itemClass = conv.unread_count > 0 ? `conversation-item unread ${isActive}` : `conversation-item ${isActive}`;

                list.append(`
                    <div class="${itemClass}" onclick="openChat('${conv.friend_id}', '${escapeHtml(conv.name)}', '${conv.avatar}')">
                        <img src="${conv.avatar}" class="avatar-medium" alt="${escapeHtml(conv.name)}">
                        <div class="conversation-info">
                            <div class="conversation-name">${escapeHtml(conv.name)}</div>
                            <div class="conversation-preview">${escapeHtml(conv.last_message || 'No messages yet')}</div>
                        </div>
                        ${unreadBadge}
                    </div>
                `);
            });

            // Auto-load first chat if none selected
            if (!currentFriendId && res.data.length > 0) {
                const first = res.data[0];
                // Only if not mobile (optional check, but good for UX)
                if (window.innerWidth > 768) {
                    openChat(first.friend_id, first.name, first.avatar);
                }
            }

            lucide.createIcons();
        }
    });
}

/**
 * Open a chat with specific friend
 */
function openChat(friendId, name, avatar) {
    currentFriendId = friendId;
    $('#receiver-id').val(friendId);
    $('#chat-name').text(name);
    $('#chat-avatar').attr('src', avatar);

    $('#empty-state').hide();
    $('#chat-container').show();

    $('.conversation-item').removeClass('active');
    $(event.target).closest('.conversation-item').addClass('active');

    loadMessages(friendId);
}

/**
 * Load messages for specific friend
 */
function loadMessages(friendId) {
    $.get(BASE_URL + '/message/get/' + friendId, function (res) {
        if (res.status === 'success') {
            const container = $('#chat-messages');
            container.empty();

            res.data.forEach(msg => {
                const bubble = msg.is_mine ? 'message-bubble mine' : 'message-bubble';
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
                    <div class="message-group ${msg.is_mine ? 'mine' : ''}">
                        <div class="${bubble}">
                            ${content}
                            <div class="message-time">${formatTime(msg.created_at)}</div>
                        </div>
                    </div>
                `);
            });

            container.scrollTop(container[0].scrollHeight);
            loadConversations(); // Refresh to update unread count
        }
    });
}

/**
 * Send a message
 */
function sendMessage() {
    const form = $('#message-form')[0];
    const formData = new FormData(form);
    const receiverId = $('#receiver-id').val();

    if (!receiverId) return;

    $.ajax({
        url: BASE_URL + '/message/send/' + receiverId,
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function (res) {
            if (res.status === 'success') {
                $('#message-input').val('');
                $('#media-preview').empty();
                $('#media-input').val('');
                loadMessages(receiverId);
            }
        }
    });
}

/**
 * Preview media before sending
 */
function previewMedia(input) {
    const preview = $('#media-preview');
    preview.empty();

    if (input.files && input.files[0]) {
        const file = input.files[0];
        const reader = new FileReader();

        reader.onload = function (e) {
            if (file.type.startsWith('video')) {
                preview.html(`<video src="${e.target.result}" style="max-height: 100px;"></video>`);
            } else {
                preview.html(`<img src="${e.target.result}" style="max-height: 100px;" alt="Preview">`);
            }
        };

        reader.readAsDataURL(file);
    }
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
