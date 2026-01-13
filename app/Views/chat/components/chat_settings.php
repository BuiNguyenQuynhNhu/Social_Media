<!-- Chat Settings Modal -->
<div id="chat-settings-modal" class="modal" style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Chat Settings</h2>
            <button class="icon-btn" onclick="closeChatSettings()">
                <i data-lucide="x"></i>
            </button>
        </div>
        
        <div class="modal-body">
            <div class="settings-section">
                <h3>Chat Name</h3>
                <input type="text" id="chat-name-input" placeholder="Enter chat name">
                <button class="btn-primary" onclick="updateChatName()">Update Name</button>
            </div>
            
            <div class="settings-section">
                <h3>Your Nickname</h3>
                <input type="text" id="nickname-input" placeholder="Enter your nickname">
                <button class="btn-primary" onclick="updateNickname()">Update Nickname</button>
            </div>
            
            <div class="settings-section">
                <h3>Privacy</h3>
                <label class="toggle-switch">
                    <input type="checkbox" id="chat-privacy-toggle">
                    <span class="toggle-slider"></span>
                    <span class="toggle-label">Private Chat</span>
                </label>
            </div>
            
            <div class="settings-section danger-zone">
                <button class="btn-danger" onclick="leaveChat()">
                    <i data-lucide="log-out"></i>
                    Leave Chat
                </button>
            </div>
        </div>
    </div>
</div>
