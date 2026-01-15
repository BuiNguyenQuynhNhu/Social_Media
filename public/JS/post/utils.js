// =========================================
// UTILITY FUNCTIONS MODULE
// Helper functions for post system
// =========================================

function getCookie(name) {
    if (!name) return null;
    let match = document.cookie.match(new RegExp('(^| )' + name + '=([^;]+)'));
    if (match) return decodeURIComponent(match[2]);
    return null;
}

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
        Array.from(input.files).forEach((file, index) => {
            const reader = new FileReader();
            reader.onload = function (e) {
                const div = document.createElement('div');
                div.className = 'preview-item';
                div.style.cssText = 'position: relative; border-radius: 8px; overflow: hidden; aspect-ratio: 1;';

                if (file.type.startsWith('video')) {
                    div.innerHTML = `<video src="${e.target.result}" controls style="width: 100%; height: 100%; object-fit: cover;"></video>`;
                } else {
                    div.innerHTML = `<img src="${e.target.result}" style="width: 100%; height: 100%; object-fit: cover;">`;
                }

                // Add remove button
                const removeBtn = document.createElement('button');
                removeBtn.innerHTML = '×';
                removeBtn.style.cssText = 'position: absolute; top: 4px; right: 4px; width: 24px; height: 24px; border-radius: 50%; background: rgba(0,0,0,0.7); color: white; border: none; cursor: pointer; font-size: 18px;';
                removeBtn.onclick = (e) => {
                    e.preventDefault();
                    div.remove();
                    if (preview.children.length === 0) preview.style.display = 'none';
                };
                div.appendChild(removeBtn);

                preview.appendChild(div);
            }
            reader.readAsDataURL(file);
        });
    }
}
