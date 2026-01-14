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
    const avatarFile = $('#group-avatar-input')[0].files[0];
    const coverFile = $('#group-cover-input')[0].files[0];

    if (!GROUP_ID || !name) {
        alert('Group name is required');
        return;
    }

    const formData = new FormData();
    formData.append('name', name);
    formData.append('description', description);
    if (avatarFile) formData.append('avatar', avatarFile);
    if (coverFile) formData.append('cover_photo', coverFile);

    $.ajax({
        url: BASE_URL + '/group/' + GROUP_ID + '/update',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function (res) {
            if (res.status === 'success') {
                alert('Group information updated successfully!');
                location.reload(); // Reload to show new images
            } else {
                alert(res.message || 'Failed to update group');
            }
        },
        error: function () {
            alert('An error occurred. Please try again.');
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
 * Delete group with password confirmation
 */
function deleteGroup() {
    if (!GROUP_ID) {
        alert('Error: Group ID not found');
        return;
    }

    const confirmDelete = confirm('Are you sure you want to delete this group? This action cannot be undone.');
    if (!confirmDelete) return;

    const password = prompt("Please enter your password to confirm deletion:");
    if (!password) return;

    $.ajax({
        url: `${BASE_URL}/group/${GROUP_ID}/delete`,
        type: 'POST',
        data: { password },
        success: function (response) {
            if (response.status === 'success') {
                alert('Group deleted successfully!');
                window.location.href = `${BASE_URL}/group`;
            } else {
                alert(response.message || 'Failed to delete group');
            }
        },
        error: function (xhr) {
            const errorMsg = xhr.responseJSON?.message || 'An error occurred';
            alert(`Error: ${errorMsg}`);
        }
    });
}

