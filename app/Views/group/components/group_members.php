<div class="group-members" id="group-members-content">
    <div class="card-container">
        <div class="members-header">
            <h2>Members <span class="count-badge" id="members-count-badge"></span></h2>
            <?php if ($is_member): ?>
                <button class="btn-primary" onclick="openInviteFriendsModal()">
                    <i data-lucide="user-plus"></i>
                    Invite Friends
                </button>
            <?php endif; ?>
        </div>
        
        <div class="members-search-wrapper">
             <i data-lucide="search"></i>
             <input type="text" placeholder="Find a member..." id="search-members">
        </div>

        <div class="members-content">
            <!-- All Members in One List -->
            <div class="members-section-block">
                <h3>All Members</h3>
                <div class="members-grid" id="all-members">
                    <!-- All members loaded via JavaScript (including admins, mods, and regular members) -->
                </div>
            </div>
        </div>
    </div>
</div>
