<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="base_url" content="<?= rtrim(base_url(), '/') ?>">
    <meta name="user_id" content="<?= $user_id ?>">
    <title>Chats | Social Media</title>
    
    <!-- Stylesheets -->
    <link rel="stylesheet" href="<?= base_url('CSS/overall.css') ?>">
    <link rel="stylesheet" href="<?= base_url('CSS/chat/chat_components.css') ?>">
    <link rel="stylesheet" href="<?= base_url('CSS/navbar.css') ?>">
    
    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body class="background">

<?= view('components/user/navbar', ['avatarUrl' => $avatarUrl]) ?>

<div class="chat-page-container">
    <!-- Left Sidebar - Chat List -->
    <?= view('chat/components/chat_list') ?>

    <!-- Center - Chat Room -->
    <div class="chat-room-container">
        <!-- Empty State -->
        <div class="empty-state" id="chat-empty-state">
            <i data-lucide="messages-square" style="width: 80px; height: 80px; color: #65676b;"></i>
            <h3>Your Group Chats</h3>
            <p>Create or select a chat to start messaging</p>
        </div>

        <!-- Active Chat Room -->
        <div class="chat-room-content" id="chat-room-content" style="display: none;">
            <!-- Chat Room Header -->
            <?= view('chat/components/chat_room_header') ?>

            <!-- Chat Messages -->
            <?= view('chat/components/chat_message_bubble') ?>

            <!-- Chat Input -->
            <div class="chat-input-container">
                <form id="group-message-form" enctype="multipart/form-data">
                    <?= csrf_field() ?>
                    <input type="hidden" id="chat-id">
                    
                    <button type="button" class="icon-btn" onclick="$('#group-media-input').click()" title="Attach media">
                        <i data-lucide="image"></i>
                    </button>
                    
                    <input type="file" id="group-media-input" name="media" accept="image/*,video/*" hidden>
                    
                    <input type="text" name="content" id="group-message-input" placeholder="Type a message..." autocomplete="off">
                    
                    <button type="submit" class="send-btn" title="Send message">
                        <i data-lucide="send"></i>
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Right Sidebar - Members -->
    <?= view('chat/components/member_sidebar') ?>
</div>

<!-- Chat Settings Modal -->
<?= view('chat/components/chat_settings') ?>

<!-- JavaScript -->
<script src="<?= base_url('JS/chat.js') ?>"></script>
<script src="<?= base_url('JS/navbar.js') ?>?v=<?= time() ?>"></script>

</body>
</html>
