// =========================================
// GROUP ACTIONS MODULE
// Handles group join, leave, and navigation
// =========================================

/**
 * Join group
 */
function joinGroup() {
    if (!GROUP_ID) return;

    $.ajax({
        url: BASE_URL + '/group/' + GROUP_ID + '/join',
        type: 'POST',
        success: function (res) {
            if (res.status === 'success') {
                location.reload();
            } else {
                alert(res.message || 'Failed to join group');
            }
        }
    });
}

/**
 * Leave group
 */
function leaveGroup() {
    if (!GROUP_ID) return;

    if (confirm('Are you sure you want to leave this group?')) {
        $.ajax({
            url: BASE_URL + '/group/' + GROUP_ID + '/leave',
            type: 'POST',
            success: function (res) {
                if (res.status === 'success') {
                    window.location.href = BASE_URL + '/group';
                } else {
                    alert(res.message || 'Failed to leave group');
                }
            }
        });
    }
}

/**
 * Switch between tabs (Feed, Members, About)
 */
function switchTab(tabName) {
    // Hide all tab contents
    $('#group-feed-content').hide();
    $('#group-members-content').hide();
    $('#group-about-content').hide();

    // Show selected tab content
    $(`#group-${tabName}-content`).show();

    // Update tab button active state
    $('.group-nav-tab').removeClass('active');
    $(`.group-nav-tab[data-tab="${tabName}"]`).addClass('active');

    // Load data if needed
    if (tabName === 'members') {
        loadGroupMembers();
    }
}

/**
 * Open group post modal
 */
function openGroupPostModal() {
    if (typeof openCreatePostModal === 'function') {
        // Set group ID in the modal
        const groupInput = document.getElementById('modal-group-id');
        if (groupInput) {
            groupInput.value = GROUP_ID;
        }
        openCreatePostModal();
    } else {
        alert('Post creation feature integrated with existing post system');
    }
}
