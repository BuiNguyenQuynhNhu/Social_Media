// =========================================
// GROUP MEMBERS MODULE
// Handles group member display and management
// =========================================

/**
 * Load group members
 */
function loadGroupMembers() {
    if (!GROUP_ID) return;

    $.get(BASE_URL + '/group/' + GROUP_ID + '/members', function (res) {
        if (res.status === 'success') {
            const adminContainer = $('#admin-members');
            const allContainer = $('#all-members');

            adminContainer.empty();
            allContainer.empty();

            const admins = res.data.filter(m => m.role === 'admin');
            const members = res.data.filter(m => m.role !== 'admin');

            admins.forEach(member => {
                renderMemberItem(member, adminContainer, true);
            });

            members.forEach(member => {
                renderMemberItem(member, allContainer, false);
            });

            lucide.createIcons();
        }
    });
}

/**
 * Render a member item
 */
function renderMemberItem(member, container, isAdmin) {
    const roleBadge = isAdmin ? '<span class="role-badge admin">Admin</span>' : '';

    container.append(`
        <div class="member-item">
            <img src="${member.avatar}" class="member-avatar" alt="${escapeHtml(member.name)}">
            <div class="member-info">
                <div class="member-name">${escapeHtml(member.name)}</div>
                ${roleBadge}
            </div>
        </div>
    `);
}
