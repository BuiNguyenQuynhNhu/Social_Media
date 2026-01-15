// =========================================
// GROUP MAIN MODULE
// Coordinates all group-related functionality
// Dependencies: list.js, members.js, actions.js, settings.js
// =========================================

const BASE_URL = document.querySelector('meta[name="base_url"]')?.content || '';
const MY_ID = document.querySelector('meta[name="user_id"]')?.content || '';
const GROUP_ID = document.querySelector('meta[name="group_id"]')?.content || '';

/**
 * Initialize group page
 */
$(document).ready(function () {
    lucide.createIcons();

    if (GROUP_ID) {
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
