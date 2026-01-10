<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Messages | Social Media</title>
    
   <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <link rel="stylesheet" href="<?= base_url('CSS/navbar.css') ?>">
    <link rel="stylesheet" href="<?= base_url('CSS/overall.css') ?>">
    <link rel="stylesheet" href="<?= base_url('CSS/message/message.css') ?>">
    <link rel="stylesheet" href="<?= base_url('CSS/message/conversation.css') ?>">
    <link rel="stylesheet" href="<?= base_url('CSS/message/chat.css') ?>">
</head>
<body class="background">

<?= view('components/user/navbar', ['avatarUrl' => $avatarUrl]) ?>

<div class="message-container">
    <!-- Left Sidebar - Conversation List -->
    <div class="conversation-sidebar">
        <div class="conversation-header">
            <h2>Messages</h2>
        </div>
        
        <div class="conversation-search">
            <input type="text" placeholder="Search messages..." id="search-conversations">
        </div>
        
        <div class="conversation-list" id="conversation-list">
            <!-- Conversations loaded via JS -->
        </div>
    </div>

    <!-- Right Side - Message Thread -->
    <div class="message-thread">
        <div class="empty-state" id="empty-state">
            <i data-lucide="message-circle" style="width: 80px; height: 80px; color: #65676b;"></i>
            <h3>Your Messages</h3>
            <p>Send private messages to friends</p>
        </div>

        <div class="chat-container" id="chat-container" style="display: none;">
            <div class="chat-header">
                <div class="chat-user-info">
                    <img src="" id="chat-avatar" class="avatar-medium">
                    <div>
                        <h3 id="chat-name"></h3>
                        <span class="status">Active now</span>
                    </div>
                </div>
                <div class="chat-actions">
                    <button class="icon-btn"><i data-lucide="phone"></i></button>
                    <button class="icon-btn"><i data-lucide="video"></i></button>
                    <button class="icon-btn"><i data-lucide="info"></i></button>
                </div>
            </div>

            <div class="chat-messages" id="chat-messages">
                <!-- Messages loaded via JS -->
            </div>

            <div class="chat-input-container">
                <form id="message-form" enctype="multipart/form-data">
                    <?= csrf_field() ?>
                    <input type="hidden" id="receiver-id">
                    <button type="button" class="icon-btn" onclick="$('#media-input').click()">
                        <i data-lucide="image"></i>
                    </button>
                    <input type="file" id="media-input" name="media" accept="image/*,video/*" hidden onchange="previewMedia(this)">
                    <div id="media-preview" class="media-preview"></div>
                    <input type="text" name="content" id="message-input" placeholder="Type a message..." autocomplete="off">
                    <button type="submit" class="send-btn">
                        <i data-lucide="send"></i>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    const BASE_URL = "<?= rtrim(base_url(), '/') ?>";
    const MY_ID = "<?= $user_id ?>";
    let currentFriendId = null;
    
    lucide.createIcons();

    $(document).ready(function() {
        loadConversations();
        
        $('#message-form').on('submit', function(e) {
            e.preventDefault();
            sendMessage();
        });

        $('#message-input').on('keydown', function(e) {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                sendMessage();
            }
        });
    });

    function loadConversations() {
        $.get(BASE_URL + '/message/conversations', function(res) {
            if (res.status === 'success') {
                const list = $('#conversation-list');
               list.empty();
                
                res.data.forEach(conv => {
                    const unreadBadge = conv.unread_count > 0 
                        ? `<span class="unread-badge">${conv.unread_count}</span>` 
                        : '';
                    
                    const itemClass = conv.unread_count > 0 ? 'conversation-item unread' : 'conversation-item';
                    
                    list.append(`
                        <div class="${itemClass}" onclick="openChat('${conv.friend_id}', '${conv.name}', '${conv.avatar}')">
                            <img src="${conv.avatar}" class="avatar-medium">
                            <div class="conversation-info">
                                <div class="conversation-name">${conv.name}</div>
                                <div class="conversation-preview">${conv.last_message || 'No messages yet'}</div>
                            </div>
                            ${unreadBadge}
                        </div>
                    `);
                });
                
                lucide.createIcons();
            }
        });
    }

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

    function loadMessages(friendId) {
        $.get(BASE_URL + '/message/get/' + friendId, function(res) {
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
                            content = `<img src="${msg.media.url}" style="max-width: 300px; border-radius: 12px;">`;
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
            success: function(res) {
                if (res.status === 'success') {
                    $('#message-input').val('');
                    $('#media-preview').empty();
                    loadMessages(receiverId);
                }
            }
        });
    }

    function previewMedia(input) {
        const preview = $('#media-preview');
        preview.empty();
        
        if (input.files && input.files[0]) {
            const file = input.files[0];
            const reader = new FileReader();
            
            reader.onload = function(e) {
                if (file.type.startsWith('video')) {
                    preview.html(`<video src="${e.target.result}" style="max-height: 100px;"></video>`);
                } else {
                    preview.html(`<img src="${e.target.result}" style="max-height: 100px;">`);
                }
            };
            
            reader.readAsDataURL(file);
        }
    }

    function escapeHtml(text) {
        const map = {'&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;'};
        return text.replace(/[&<>"']/g, m => map[m]);
    }

    function formatTime(dateString) {
        const date = new Date(dateString);
        return date.toLocaleTimeString([], {hour: '2-digit', minute: '2-digit'});
    }

    // Auto-refresh conversations every 10 seconds
    setInterval(loadConversations, 10000);
</script>

</body>
</html>
