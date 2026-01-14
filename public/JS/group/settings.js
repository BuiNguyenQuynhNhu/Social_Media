// =========================================
// GROUP SETTINGS MODULE
// Handles group admin settings and updates
// =========================================

/**
 * Open group settings modal
 */
function openGroupSettings() {
    $('#group-settings-modal').fadeIn();
    lucide.createIcons();
}

/**
 * Close group settings modal
 */
function closeGroupSettings() {
    $('#group-settings-modal').fadeOut();
}

/**
 * Update group info
 */
function updateGroupInfo() {
    const name = $('#group-name-input').val();
    const description = $('#group-description-input').val();

    if (!GROUP_ID || !name) return;

    $.ajax({
        url: BASE_URL + '/group/' + GROUP_ID + '/update',
        type: 'POST',
        data: { name, description },
        success: function (res) {
            if (res.status === 'success') {
                $('#group-name').text(name);
                $('#group-description').text(description);
                closeGroupSettings();
                alert('Group information updated successfully!');
            } else {
                alert(res.message || 'Failed to update group');
            }
        }
    });
}

/**
 * Update privacy settings
 */
function updatePrivacy() {
    const privacy = $('input[name="privacy"]:checked').val();

    if (!GROUP_ID || !privacy) return;

    $.ajax({
        url: BASE_URL + '/group/' + GROUP_ID + '/privacy',
        type: 'POST',
        data: { privacy },
        success: function (res) {
            if (res.status === 'success') {
                location.reload();
            } else {
                alert(res.message || 'Failed to update privacy');
            }
        }
    });
}

/**
 * Update post requirements
 */
function updatePostRequirements() {
    const requireApproval = $('#post-approval-toggle').is(':checked');

    if (!GROUP_ID) return;

    $.ajax({
        url: BASE_URL + '/group/' + GROUP_ID + '/requirements',
        type: 'POST',
        data: { require_approval: requireApproval },
        success: function (res) {
            if (res.status === 'success') {
                alert('Post requirements updated successfully!');
            } else {
                alert(res.message || 'Failed to update requirements');
            }
        }
    });
}

/**
 * Delete group
 */
/**
 * Delete group
 */
function deleteGroup() {
    if (!GROUP_ID) return;

    if (confirm('Are you sure you want to delete this group? This action cannot be undone.')) {
        const password = prompt("Please enter your password to confirm deletion:");
        if (!password) return; // User cancelled

        $.ajax({
            url: BASE_URL + '/group/' + GROUP_ID,
            type: 'DELETE',
            data: { password: password }, // Send password
            success: function (res) {
                if (res.status === 'success') {
                    window.location.href = BASE_URL + '/group';
                } else {
                    alert(res.message || 'Failed to delete group');
                }
            }
        });
    }
}
