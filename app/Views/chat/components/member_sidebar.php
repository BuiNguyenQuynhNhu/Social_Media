<div class="member-sidebar" id="member-sidebar">
    <div class="member-sidebar-header">
        <h3>Members</h3>
        <button class="icon-btn" onclick="toggleMemberSidebar()" title="Close">
            <i data-lucide="x"></i>
        </button>
    </div>
    
    <button class="add-member-btn" onclick="openAddMemberModal()">
        <i data-lucide="user-plus"></i>
        Add Members
    </button>
    
    <div class="member-list" id="member-list">
        <!-- Members loaded via JavaScript -->
    </div>
</div>
