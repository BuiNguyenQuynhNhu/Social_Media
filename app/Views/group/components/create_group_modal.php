<div class="modal-overlay" id="create-group-modal" style="display: none;">
    <div class="modal-content create-group-modal">
        <div class="modal-header">
            <h2>Create New Group</h2>
            <button class="icon-circle mini" onclick="closeCreateGroupModal()">
                <i data-lucide="x"></i>
            </button>
        </div>

        <div class="modal-body">
            <!-- Illustration and Description -->
            <div class="group-intro">
                <div class="group-illustration">
                    <svg width="60" height="60" viewBox="0 0 60 60" fill="none">
                        <circle cx="20" cy="25" r="15" fill="#4CAF50"/>
                        <circle cx="35" cy="30" r="12" fill="#2196F3"/>
                        <circle cx="45" cy="20" r="10" fill="#FFC107"/>
                    </svg>
                </div>
                <p class="group-description">
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
                           placeholder="" 
                           required 
                           maxlength="100">
                </div>

                <!-- Privacy Selection -->
                <div class="form-section">
                    <label>
                        Select privacy
                        <a href="#" class="learn-more">Learn more about groups privacy</a>
                    </label>
                    <select name="privacy" id="group-privacy" class="form-select">
                        <option value="public">
                            <span>🌍</span> Public Group - Anyone can see posts
                        </option>
                        <option value="private" selected>
                            <span>🔒</span> Closed Group - Anyone can find the group and see who's in it. Only members can see posts.
                        </option>
                    </select>
                    <p class="privacy-description" id="privacy-desc">
                        Anyone can find the group and see who's in it. Only members can see posts.
                    </p>
                </div>

                <!-- Hidden fields for additional options -->
                <input type="hidden" name="description" value="">
                <input type="hidden" name="invite_friends" id="invite-friends-ids" value="">

                <div class="modal-footer">
                    <button type="submit" class="btn btn-blue btn-create">Create</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.create-group-modal {
    max-width: 520px;
    border-radius: 8px;
}

.create-group-modal .modal-header {
    padding: 16px 16px 12px;
    border-bottom: 1px solid #e4e6eb;
}

.create-group-modal .modal-header h2 {
    font-size: 20px;
    font-weight: 700;
    color: #050505;
}

.create-group-modal .modal-body {
    padding: 16px;
    max-height: 70vh;
    overflow-y: auto;
}

.group-intro {
    display: flex;
    gap: 12px;
    margin-bottom: 20px;
    padding-bottom: 16px;
    border-bottom: 1px solid #e4e6eb;
}

.group-illustration {
    flex-shrink: 0;
}

.group-description {
    font-size: 15px;
    line-height: 20px;
    color: #65676b;
    margin: 0;
}

.form-section {
    margin-bottom: 16px;
}

.form-section label {
    display: block;
    font-size: 17px;
    font-weight: 600;
    color: #050505;
    margin-bottom: 8px;
}

.learn-more {
    font-size: 13px;
    font-weight: 400;
    color: #1877f2;
    text-decoration: none;
    margin-left: 4px;
}

.learn-more:hover {
    text-decoration: underline;
}

.form-input {
    width: 100%;
    padding: 10px 12px;
    font-size: 15px;
    border: 1px solid #ccd0d5;
    border-radius: 6px;
    transition: border-color 0.2s;
}

.form-input:focus {
    outline: none;
    border-color: #1877f2;
}

.form-select {
    width: 100%;
    padding: 10px 12px;
    font-size: 15px;
    border: 1px solid #ccd0d5;
    border-radius: 6px;
    background: white;
    cursor: pointer;
}

.privacy-description {
    font-size: 13px;
    color: #65676b;
    margin: 8px 0 0 0;
    line-height: 16px;
}

.modal-footer {
    margin-top: 20px;
    padding-top: 16px;
    border-top: 1px solid #e4e6eb;
}

.btn-create {
    width: 100%;
    padding: 10px;
    font-size: 15px;
    font-weight: 600;
}
</style>

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

// Update privacy description
$('#group-privacy').on('change', function() {
    const val = $(this).val();
    const desc = $('#privacy-desc');
    if (val === 'public') {
        desc.text('Anyone can see posts and who\'s in the group.');
    } else {
        desc.text('Anyone can find the group and see who\'s in it. Only members can see posts.');
    }
});

// Form submission
$(document).ready(function() {
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
                    window.location.href = BASE_URL + '/group/' + res.data.id;
                } else {
                    alert(res.message || 'Failed to create group');
                }
            },
            error: function() {
                alert('Failed to create group. Please try again.');
            }
        });
    });
});

// Close modal when clicking outside
window.addEventListener('click', function(e) {
    const modal = document.getElementById('create-group-modal');
    if (e.target === modal) {
        closeCreateGroupModal();
    }
});
</script>
