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
    <link rel="stylesheet" href="<?= base_url('CSS/message/message.css') ?>">
    <link rel="stylesheet" href="<?= base_url('CSS/message/conversation.css') ?>">
    <link rel="stylesheet" href="<?= base_url('CSS/message/chat.css') ?>">
    
    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body class="background">

<?= view('components/user/navbar', ['avatarUrl' => $avatarUrl]) ?>

<div class="message-container">
    <!-- Left Sidebar - Conversation List -->
    <?= view('message/components/conversation_list') ?>

    <!-- Right Side - Message Thread -->
    <div class="message-thread">
        <!-- Empty State -->
        <?= view('message/components/empty_state') ?>

        <!-- Chat Container -->
        <div class="chat-container" id="chat-container" style="display: none;">
            <!-- Chat Header -->
            <?= view('message/components/chat_header') ?>

            <!-- Message Bubbles -->
            <?= view('message/components/message_bubble') ?>

            <!-- Chat Input -->
            <?= view('message/components/chat_input') ?>
        </div>
    </div>
</div>

<!-- JavaScript -->
<script src="<?= base_url('JS/message.js') ?>"></script>

</body>
</html>
