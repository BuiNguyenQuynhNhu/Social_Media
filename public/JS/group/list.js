// =========================================
// GROUP LIST MODULE
// Handles group browsing and listing
// =========================================

/**
 * Load user's groups
 */
function loadMyGroups() {
    $.get(BASE_URL + '/group/my-groups', function (res) {
        if (res.status === 'success') {
            const container = $('#my-groups');
            container.empty();

            if (res.data.length === 0) {
                container.append(`
                    <div class="empty-state small">
                        <p>You haven't joined any groups yet</p>
                    </div>
                `);
                return;
            }

            res.data.forEach(group => {
                renderGroupCard(group, container);
            });

            lucide.createIcons();
        }
    });
}

/**
 * Load suggested groups
 */
function loadSuggestedGroups() {
    $.get(BASE_URL + '/group/suggested', function (res) {
        if (res.status === 'success') {
            const container = $('#suggested');
            container.empty();

            res.data.forEach(group => {
                renderGroupCard(group, container);
            });

            lucide.createIcons();
        }
    });
}

/**
 * Render a group card
 */
function renderGroupCard(group, container) {
    const privacyIcon = group.privacy === 'public' ? 'globe' : 'lock';

    container.append(`
        <div class="group-card" onclick="window.location.href='${BASE_URL}/group/${group.id}'">
            <div class="group-card-cover" style="background-image: url('${group.cover_photo || BASE_URL + '/images/default-cover.jpg'}');"></div>
            <div class="group-card-body">
                <h3 class="group-card-name">${escapeHtml(group.name)}</h3>
                <div class="group-card-info">
                    <i data-lucide="${privacyIcon}"></i>
                    ${group.privacy}
                    <span>•</span>
                    <i data-lucide="users"></i>
                    ${group.member_count || 0} members
                </div>
            </div>
        </div>
    `);
}
