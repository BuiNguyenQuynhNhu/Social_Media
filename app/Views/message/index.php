<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="base_url" content="<?= rtrim(base_url(), '/') ?>">
    <meta name="user_id" content="<?= $user_id ?>">
    <title>Messages | Social Media</title>
    
    <!-- Stylesheets -->
    <link rel="stylesheet" href="<?= base_url('CSS/navbar.css') ?>">
    <link rel="stylesheet" href="<?= base_url('CSS/overall.css') ?>">
    <link rel="stylesheet" href="<?= base_url('CSS/message/messenger.css') ?>">
    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body class="background">

<?= view('components/user/navbar', ['avatarUrl' => $avatarUrl]) ?>

<div class="message-container">
    <!-- Conversation List (Left) -->
    <?= view('message/components/conversation_list') ?>

    <!-- Chat Area (Center/Right) -->
    <div class="chat-area" id="chat-area">
        <!-- Empty State -->
        <?= view('message/components/empty_state') ?>

        <!-- Active Chat (Hidden by default) -->
        <div id="active-chat" style="display: none;">
            <?= view('message/components/chat_header') ?>
            <div class="chat-messages" id="chat-messages">
                <!-- Messages loaded via JS -->
            </div>
            <?= view('message/components/chat_input') ?>
        </div>
    </div>
</div>

<!-- JavaScript -->
<script>
    const BASE_URL = '<?= rtrim(base_url(), '/') ?>';
    const USER_ID = '<?= $user_id ?>';
    lucide.createIcons();
</script>
<script src="<?= base_url('JS/message.js') ?>"></script>

</body>
</html>
