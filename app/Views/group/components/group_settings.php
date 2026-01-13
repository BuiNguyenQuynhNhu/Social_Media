<!-- Group Settings Modal -->
<div id="group-settings-modal" class="modal" style="display: none;">
    <div class="modal-content large">
        <div class="modal-header">
            <h2>Group Settings</h2>
            <button class="icon-btn" onclick="closeGroupSettings()">
                <i data-lucide="x"></i>
            </button>
        </div>
        
        <div class="modal-body">
            <!-- Basic Info Section -->
            <div class="settings-section">
                <h3>Basic Information</h3>
                <div class="form-group">
                    <label>Group Name</label>
                    <input type="text" id="group-name-input" placeholder="Enter group name">
                </div>
                <div class="form-group">
                    <label>Description</label>
                    <textarea id="group-description-input" rows="4" placeholder="Describe your group..."></textarea>
                </div>
                <button class="btn-primary" onclick="updateGroupInfo()">Save Changes</button>
            </div>
            
            <!-- Privacy Settings -->
            <div class="settings-section">
                <h3>Privacy Settings</h3>
                <div class="radio-group">
                    <label class="radio-option">
                        <input type="radio" name="privacy" value="public">
                        <div class="radio-content">
                            <i data-lucide="globe"></i>
                            <div>
                                <strong>Public</strong>
                                <p>Anyone can see posts and members</p>
                            </div>
                        </div>
                    </label>
                    <label class="radio-option">
                        <input type="radio" name="privacy" value="private">
                        <div class="radio-content">
                            <i data-lucide="lock"></i>
                            <div>
                                <strong>Private</strong>
                                <p>Only members can see posts</p>
                            </div>
                        </div>
                    </label>
                </div>
                <button class="btn-primary" onclick="updatePrivacy()">Update Privacy</button>
            </div>
            
            <!-- Post Requirements -->
            <div class="settings-section">
                <h3>Post Requirements</h3>
                <label class="toggle-switch">
                    <input type="checkbox" id="post-approval-toggle">
                    <span class="toggle-slider"></span>
                    <span class="toggle-label">Require admin approval for posts</span>
                </label>
                <button class="btn-primary" onclick="updatePostRequirements()">Save</button>
            </div>
            
            <!-- Member Management -->
            <div class="settings-section">
                <h3>Member Management</h3>
                <div class="member-actions">
                    <button class="btn-secondary" onclick="viewPendingRequests()">
                        <i data-lucide="user-check"></i>
                        View Pending Requests
                    </button>
                    <button class="btn-secondary" onclick="manageAdmins()">
                        <i data-lucide="shield"></i>
                        Manage Admins
                    </button>
                </div>
            </div>
            
            <!-- Danger Zone -->
            <div class="settings-section danger-zone">
                <h3>Danger Zone</h3>
                <button class="btn-danger" onclick="deleteGroup()">
                    <i data-lucide="trash-2"></i>
                    Delete Group
                </button>
            </div>
        </div>
    </div>
</div>
