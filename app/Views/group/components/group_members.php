<div class="group-members" id="group-members-content" style="display: none;">
    <div class="members-header">
        <h2>Members</h2>
        <?php if ($is_member): ?>
            <button class="btn-primary" onclick="openInviteFriendsModal()">
                <i data-lucide="user-plus"></i>
                Invite Friends
            </button>
        <?php endif; ?>
    </div>
    
    <div class="members-search">
        <input type="text" placeholder="Search members..." id="search-members">
    </div>
    
    <!-- Admins Section -->
    <div class="members-section">
        <h3>Admins</h3>
        <div class="members-list" id="admin-members">
            <!-- Admin members loaded via JavaScript -->
        </div>
    </div>
    
    <!-- All Members Section -->
    <div class="members-section">
        <h3>All Members</h3>
        <div class="members-list" id="all-members">
            <!-- All members loaded via JavaScript -->
        </div>
    </div>
</div>
