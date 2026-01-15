// =========================================
// GROUP MEMBERS MODULE
// Handles group member display and management
// =========================================

/**
 * Load group members
 */
function loadGroupMembers() {
    if (!GROUP_ID) return;

    $.get(BASE_URL + '/group/' + GROUP_ID + '/members/data', function (res) {
        if (res.status === 'success') {
            const allContainer = $('#all-members');
            allContainer.empty();

            // Sort members: owner first, then admins/mods, then regular members
            const sortedMembers = res.data.sort((a, b) => {
                const roleOrder = { 'owner': 0, 'admin': 1, 'moderator': 2, 'member': 3 };
                return (roleOrder[a.role] || 3) - (roleOrder[b.role] || 3);
            });

            sortedMembers.forEach(member => {
                renderMemberItem(member, allContainer);
            });

            // Update member count
            $('#members-count-badge').text(`(${res.data.length})`);

            lucide.createIcons();
        }
    });
}

/**
 * Render a member item with role badge
 */
function renderMemberItem(member, container) {
    let roleBadge = '';
    let roleClass = '';

    if (member.role === 'owner') {
        roleBadge = '<span class="role-badge owner">Owner</span>';
        roleClass = 'owner';
    } else if (member.role === 'admin') {
        roleBadge = '<span class="role-badge admin">Admin</span>';
        roleClass = 'admin';
    } else if (member.role === 'moderator') {
        roleBadge = '<span class="role-badge moderator">Moderator</span>';
        roleClass = 'moderator';
    }

    container.append(`
        <div class="member-card ${roleClass}">
            <img src="${member.avatar}" alt="${escapeHtml(member.name)}">
            <div class="member-info">
                <div class="member-name">${escapeHtml(member.name)}</div>
                ${roleBadge}
            </div>
        </div>
    `);
}
