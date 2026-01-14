<div class="modal-overlay" id="create-group-modal" style="display: none;">
    <div class="modal-content create-group-modal">
        <div class="modal-header">
            <h2>Create New Group</h2>
            <button class="close-btn" onclick="closeCreateGroupModal()">
                <i data-lucide="x"></i>
            </button>
        </div>

        <div class="modal-body">
            <!-- Illustration Banner -->
            <div class="group-banner">
                <div class="illustration">
                    <!-- SVG Placeholder for reliability -->
                    <svg width="60" height="60" viewBox="0 0 60 60" fill="none" style="display: block;">
                         <circle cx="30" cy="30" r="28" fill="#E4F2FF"/>
                         <path d="M20 30L40 30M30 20L30 40" stroke="#1877F2" stroke-width="4" stroke-linecap="round"/>
                         <circle cx="15" cy="45" r="5" fill="#4B4C4F"/>
                         <circle cx="45" cy="15" r="5" fill="#4CAF50"/>
                    </svg>
                </div>
                <p class="banner-text">
                    Groups are great for getting things done and staying in touch with just the people you want. 
                    Share photos and videos, have conversations, make plans and more.
                </p>
            </div>

            <form id="create-group-form" enctype="multipart/form-data">
                <?= csrf_field() ?>
                
                <!-- Group Name -->
                <div class="form-section">
                    <label>Name your group</label>
                    <input type="text" 
                           name="name" 
                           id="group-name" 
                           class="form-input" 
                           required 
                           autocomplete="off">
                </div>

                <!-- Add People (Placeholder) -->
                <div class="form-section">
                    <label>Add some people</label>
                    <input type="text" 
                           id="invite-input" 
                           class="form-input" 
                           placeholder="Enter names or email addresses...">
                </div>

                <!-- Privacy Selection -->
                <div class="form-section">
                    <div class="privacy-header">
                        <label>Select privacy</label>
                        <a href="#" class="learn-more">Learn more about groups privacy</a>
                    </div>
                    
                    <div class="custom-select-wrapper">
                        <div class="custom-select" onclick="togglePrivacyDropdown()">
                            <div class="selected-option" id="selected-privacy">
                                <i data-lucide="lock" class="privacy-icon"></i>
                                <div>
                                    <span class="privacy-title">Closed Group</span>
                                    <span class="privacy-subtitle">Anyone can find the group and see who's in it. Only members can see posts.</span>
                                </div>
                                <i data-lucide="chevron-down" class="dropdown-arrow"></i>
                            </div>
                            
                            <div class="privacy-dropdown" id="privacy-dropdown">
                                <div class="privacy-option" onclick="selectPrivacy('public')">
                                    <i data-lucide="globe"></i>
                                    <div>
                                        <span class="privacy-title">Public Group</span>
                                        <span class="privacy-subtitle">Anyone can see posts and who's in the group.</span>
                                    </div>
                                </div>
                                <div class="privacy-option" onclick="selectPrivacy('private')">
                                    <i data-lucide="lock"></i>
                                    <div>
                                        <span class="privacy-title">Closed Group</span>
                                        <span class="privacy-subtitle">Anyone can find the group and see who's in it. Only members can see posts.</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <input type="hidden" name="privacy" id="privacy-input" value="private">
                    </div>
                </div>

                <!-- Pin to Shortcuts -->
                <div class="form-checkbox">
                    <input type="checkbox" id="pin-shortcuts" name="pin_shortcuts">
                    <label for="pin-shortcuts">Pin to Shortcuts</label>
                </div>

                <!-- Hidden fields -->
                <input type="hidden" name="description" value="">
                <input type="hidden" name="invite_friends" id="invite-friends-ids" value="">

                <div class="modal-footer">
                    <button type="submit" class="btn-create">Create</button>
                </div>
            </form>
        </div>
    </div>
</div>



<script>
function openCreateGroupModal() {
    document.getElementById('create-group-modal').style.display = 'flex';
    document.getElementById('group-name').focus();
    lucide.createIcons();
}

function closeCreateGroupModal() {
    document.getElementById('create-group-modal').style.display = 'none';
    document.getElementById('create-group-form').reset();
}

function togglePrivacyDropdown() {
    const dropdown = document.getElementById('privacy-dropdown');
    dropdown.style.display = dropdown.style.display === 'block' ? 'none' : 'block';
}

function selectPrivacy(type) {
    const input = document.getElementById('privacy-input');
    const selectedDiv = document.getElementById('selected-privacy');
    
    input.value = type;
    
    let html = '';
    if (type === 'public') {
        html = `
            <i data-lucide="globe" class="privacy-icon"></i>
            <div>
                <span class="privacy-title">Public Group</span>
                <span class="privacy-subtitle">Anyone can see posts and who's in the group.</span>
            </div>
            <i data-lucide="chevron-down" class="dropdown-arrow"></i>
        `;
    } else {
        html = `
            <i data-lucide="lock" class="privacy-icon"></i>
            <div>
                <span class="privacy-title">Closed Group</span>
                <span class="privacy-subtitle">Anyone can find the group and see who's in it. Only members can see posts.</span>
            </div>
            <i data-lucide="chevron-down" class="dropdown-arrow"></i>
        `;
    }
    
    selectedDiv.innerHTML = html;
    document.getElementById('privacy-dropdown').style.display = 'none';
    lucide.createIcons();
}

// Close dropdown when clicking outside
window.addEventListener('click', function(e) {
    if (!e.target.closest('.custom-select-wrapper')) {
        document.getElementById('privacy-dropdown').style.display = 'none';
    }
    const modal = document.getElementById('create-group-modal');
    if (e.target === modal) {
        closeCreateGroupModal();
    }
});

// Form submission
$('#create-group-form').on('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const BASE_URL = $('meta[name="base_url"]').attr('content');
    
    $.ajax({
        url: BASE_URL + '/group/create',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(res) {
            if (res.status === 'success') {
                closeCreateGroupModal();
                window.location.href = BASE_URL + '/group/' + res.data.group_id;
            } else {
                alert(res.message || 'Failed to create group');
            }
        },
        error: function() {
            alert('Failed to create group. Please try again.');
        }
    });
});
</script>
