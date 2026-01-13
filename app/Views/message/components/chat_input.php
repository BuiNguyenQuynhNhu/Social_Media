<div class="chat-input-container">
    <form id="message-form" enctype="multipart/form-data">
        <?= csrf_field() ?>
        <input type="hidden" id="receiver-id">
        
        <button type="button" class="icon-btn" onclick="$('#media-input').click()" title="Attach media">
            <i data-lucide="image"></i>
        </button>
        
        <input type="file" id="media-input" name="media" accept="image/*,video/*" hidden onchange="previewMedia(this)">
        
        <div id="media-preview" class="media-preview"></div>
        
        <input type="text" name="content" id="message-input" placeholder="Type a message..." autocomplete="off">
        
        <button type="submit" class="send-btn" title="Send message">
            <i data-lucide="send"></i>
        </button>
    </form>
</div>
