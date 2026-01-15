<div class="chat-input-area">
    <div class="chat-input-wrapper">
        <label for="message-file-input" style="cursor: pointer; padding: 8px; display: flex; align-items: center; color: #65676b;">
            <i data-lucide="image" style="width: 20px; height: 20px;"></i>
        </label>
        <input type="file" id="message-file-input" accept="image/*,video/*" style="display: none;">
        
        <input type="text" class="chat-input" id="message-input" placeholder="Type a message...">
        
        <button class="chat-send-btn" id="send-btn" disabled>
            <i data-lucide="send"></i>
        </button>
    </div>
    <div id="media-preview" style="display: none; padding: 10px;"></div>
</div>
