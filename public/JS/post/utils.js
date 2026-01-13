// =========================================
// UTILITY FUNCTIONS MODULE
// Helper functions for post system
// =========================================

function escapeHtml(text) {
    const map = {
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;'
    };
    return text.replace(/[&<>"']/g, m => map[m]);
}

function formatCommentTime(dateString) {
    // Handle different date formats
    let date = new Date(dateString);

    // If invalid, try parsing as MySQL datetime format
    if (isNaN(date.getTime())) {
        date = new Date(dateString.replace(' ', 'T'));
    }

    // Still invalid? Return default
    if (isNaN(date.getTime())) {
        return 'Just now';
    }

    const now = new Date();
    const diffMs = now - date;
    const diffMins = Math.floor(diffMs / 60000);

    if (diffMins < 1) return 'Just now';
    if (diffMins < 60) return diffMins + 'm';

    const diffHours = Math.floor(diffMins / 60);
    if (diffHours < 24) return diffHours + 'h';

    const diffDays = Math.floor(diffHours / 24);
    if (diffDays < 7) return diffDays + 'd';

    return date.toLocaleDateString();
}

function previewPostFiles(input) {
    const preview = document.getElementById('media-preview-container');
    if (!preview) return;

    preview.innerHTML = '';
    preview.style.display = 'none';

    if (input.files && input.files.length > 0) {
        preview.style.display = 'grid';
        Array.from(input.files).forEach(file => {
            const reader = new FileReader();
            reader.onload = function (e) {
                const div = document.createElement('div');
                div.className = 'preview-item';
                if (file.type.startsWith('video')) {
                    div.innerHTML = `<video src="${e.target.result}" controls></video>`;
                } else {
                    div.innerHTML = `<img src="${e.target.result}">`;
                }
                preview.appendChild(div);
            }
            reader.readAsDataURL(file);
        });
    }
}
