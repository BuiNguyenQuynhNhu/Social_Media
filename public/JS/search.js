// Search functionality
let searchTimeout;

function liveSearch(query) {
    const dropdown = document.getElementById('search-dropdown');

    // Clear previous timeout
    clearTimeout(searchTimeout);

    // Hide dropdown if query is empty
    if (query.length < 2) {
        dropdown.style.display = 'none';
        return;
    }

    const baseUrl = window.BASE_URL || APP_CONFIG.baseURL;

    // Debounce search - wait 300ms after user stops typing
    searchTimeout = setTimeout(() => {
        fetch(`${baseUrl}/search/live?q=${encodeURIComponent(query)}`)
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    renderSearchResults(data.data);
                }
            })
            .catch(error => {
                console.error('Search error:', error);
            });
    }, 300);
}

function renderSearchResults(results) {
    const dropdown = document.getElementById('search-dropdown');

    if (results.length === 0) {
        dropdown.innerHTML = '<div class="search-empty">No results found</div>';
        dropdown.style.display = 'block';
        return;
    }

    let html = '';

    results.forEach(result => {
        const icon = result.type === 'user'
            ? '<i data-lucide="user"></i>'
            : '<i data-lucide="users-2"></i>';

        const badge = result.type === 'group' && result.privacy === 'private'
            ? '<span class="privacy-badge">Private</span>'
            : '';

        let actionBtn = '';
        if (result.type === 'user' && result.status !== 'self') {
            if (result.status === 'none') {
                actionBtn = `<button onclick="sendFriendRequest(event, '${result.id}')" class="btn-xs-primary class-add-friend" style="margin-left:auto; z-index:2;">Add Friend</button>`;
            } else if (result.status === 'pending') {
                actionBtn = `<span class="badge-pending" style="margin-left:auto; font-size:11px; color:#666;">Pending</span>`;
            } else if (result.status === 'friend') {
                actionBtn = `<span class="badge-friend" style="margin-left:auto; font-size:11px; color:#1877f2;">Friend</span>`;
            }
        }

        html += `
            <div class="search-result-wrapper" style="display:flex; align-items:center;">
                <a href="${result.url}" class="search-result-item" style="flex:1;">
                    <img src="${result.avatar}" alt="${result.name}" class="search-avatar">
                    <div class="search-info">
                        <div class="search-name">${escapeHtml(result.name)}</div>
                        <div class="search-type">${result.type === 'user' ? 'User' : 'Group'} ${badge}</div>
                    </div>
                </a>
                ${actionBtn}
            </div>
        `;
    });

    dropdown.innerHTML = html;
    dropdown.style.display = 'block';

    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }
}

function hideSearch() {
    const dropdown = document.getElementById('search-dropdown');
    dropdown.style.display = 'none';
    document.getElementById('search-input').value = '';
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

document.addEventListener('click', function (event) {
    const searchContainer = document.querySelector('.search-container');
    const dropdown = document.getElementById('search-dropdown');

    if (searchContainer && !searchContainer.contains(event.target)) {
        dropdown.style.display = 'none';
    }
});

// Make global
window.sendFriendRequest = function (e, userId) {
    e.preventDefault();
    e.stopPropagation();

    const btn = e.target;
    btn.innerHTML = 'Sending...';
    btn.disabled = true;

    const baseUrl = window.BASE_URL || APP_CONFIG.baseURL;

    fetch(`${baseUrl}/friend/add/${userId}`)
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                btn.innerHTML = 'Sent';
                btn.className = 'badge-pending';
                btn.style.border = 'none';
                btn.style.background = 'transparent';
                btn.style.color = '#666';
            } else {
                btn.innerHTML = 'Add Friend';
                btn.disabled = false;
                alert(data.message || 'Failed to send request');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            btn.innerHTML = 'Add Friend';
            btn.disabled = false;
        });
};
