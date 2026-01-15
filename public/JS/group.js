// =========================================
// GROUP MAIN MODULE
// Coordinates all group-related functionality
// Dependencies: list.js, members.js, actions.js, settings.js
// =========================================

// Globals handled by window.BASE_URL, window.GROUP_ID

/**
 * Initialize group page
 */
$(document).ready(function () {
    lucide.createIcons();

    if (window.GROUP_ID) {
        // Individual group page
        loadGroupMembers();
    } else {
        // Browse groups page
        loadMyGroups();
        loadSuggestedGroups();
    }
});

/**
 * Escape HTML to prevent XSS
 */
function escapeHtml(text) {
    const map = { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' };
    return text.replace(/[&<>"']/g, m => map[m]);
}
