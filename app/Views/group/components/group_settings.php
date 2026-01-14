<!-- Group Settings Modal -->
<div id="group-settings-modal" class="modal-overlay" style="display: none;">
    <div class="modal-content create-group-modal">
        <div class="modal-header">
            <h2>Group Settings</h2>
            <button class="close-btn" onclick="closeGroupSettings()">
                <i data-lucide="x"></i>
            </button>
        </div>
        
        <div class="modal-body">
            <!-- Basic Info Section -->
            <div class="form-section">
                <label>Name and Description</label>
                <input type="text" id="group-name-input" class="form-input" placeholder="Group Name" style="margin-bottom: 8px;">
                <textarea id="group-description-input" class="form-input" rows="3" placeholder="Description"></textarea>
            </div>
            
            <hr style="border: 0; border-top: 1px solid #e4e6eb; margin: 16px 0;">

            <!-- Privacy Settings -->
            <div class="form-section">
                <label>Privacy</label>
                <div class="custom-select-wrapper">
                    <div class="custom-select">
                        <div class="selected-option" id="settings-privacy-display">
                            <i data-lucide="lock" class="privacy-icon"></i>
                            <div>
                                <span class="privacy-title">Private</span>
                                <span class="privacy-subtitle">Only members can see who's in the group and what they post.</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <hr style="border: 0; border-top: 1px solid #e4e6eb; margin: 16px 0;">

            <!-- Post Requirements -->
            <div class="form-section">
                <label>Post Approval</label>
                <div class="settings-row">
                    <div class="row-content">
                        <span>Require Post Approval</span>
                        <small>Admin must approve posts before they are visible.</small>
                    </div>
                    <label class="switch" style="position: relative; display: inline-block; width: 40px; height: 24px;">
                        <input type="checkbox" id="post-approval-toggle" <?= (!empty($group['post_requirement']) && $group['post_requirement'] == 1) ? 'checked' : '' ?> onchange="updatePostRequirements()">
                        <span class="slider round" style="position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; background-color: #ccc; transition: .4s; border-radius: 34px;"></span>
                        <style>
                            .switch input { opacity: 0; width: 0; height: 0; }
                            .slider:before { position: absolute; content: ""; height: 16px; width: 16px; left: 4px; bottom: 4px; background-color: white; transition: .4s; border-radius: 50%; }
                            input:checked + .slider { background-color: #1877f2; }
                            input:checked + .slider:before { transform: translateX(16px); }
                        </style>
                    </label>
                </div>
            </div>

            <hr style="border: 0; border-top: 1px solid #e4e6eb; margin: 16px 0;">

            <!-- Customize Group -->
            <div class="form-section">
                <label>Customize Group</label>
                <div class="settings-row" onclick="document.getElementById('cover-upload').click()">
                    <div class="row-icon"><i data-lucide="image"></i></div>
                    <div class="row-content">
                        <span>Cover Photo</span>
                        <small>Change group cover photo</small>
                    </div>
                    <i data-lucide="chevron-right" class="row-arrow"></i>
                    <input type="file" id="cover-upload" hidden accept="image/*">
                </div>
            </div>

            <!-- Manage Membership -->
            <div class="form-section">
                <label>Manage Membership</label>
                <div class="settings-row" onclick="viewPendingRequests()">
                    <div class="row-icon"><i data-lucide="user-plus"></i></div>
                    <div class="row-content">
                        <span>Member Requests</span>
                        <small>Manage pending member requests</small>
                    </div>
                    <i data-lucide="chevron-right" class="row-arrow"></i>
                </div>
                
                 <div class="settings-row" onclick="manageAdmins()">
                    <div class="row-icon"><i data-lucide="shield"></i></div>
                    <div class="row-content">
                        <span>Admins & Moderators</span>
                        <small>Manage group roles</small>
                    </div>
                    <i data-lucide="chevron-right" class="row-arrow"></i>
                </div>
            </div>

             <!-- Danger Zone -->
             <div class="form-section" style="margin-top: 24px;">
                <label style="color: #dc3545;">Danger Zone</label>
                <button class="btn-danger-outline" onclick="deleteGroup()">Pause Group</button>
                <button class="btn-danger-outline" onclick="deleteGroup()" style="margin-top: 8px;">Delete Group</button>
            </div>

            <div class="modal-footer">
                <button class="btn-secondary" onclick="closeGroupSettings()" style="margin-right: 8px;">Cancel</button>
                <button class="btn-create" onclick="saveGroupSettings()">Save</button>
            </div>
        </div>
    </div>
</div>
