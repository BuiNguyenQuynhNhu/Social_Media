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

    // Debounce search - wait 300ms after user stops typing
    searchTimeout = setTimeout(() => {
        fetch(`${BASE_URL}/search/live?q=${encodeURIComponent(query)}`)
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

        html += `
            <a href="${result.url}" class="search-result-item">
                <img src="${result.avatar}" alt="${result.name}" class="search-avatar">
                <div class="search-info">
                    <div class="search-name">${escapeHtml(result.name)}</div>
                    <div class="search-type">${result.type === 'user' ? 'User' : 'Group'} ${badge}</div>
                </div>
            </a>
        `;
    });

    dropdown.innerHTML = html;
    dropdown.style.display = 'block';

    // Re-initialize Lucide icons
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

// Close search dropdown when clicking outside
document.addEventListener('click', function (event) {
    const searchContainer = document.querySelector('.search-container');
    const dropdown = document.getElementById('search-dropdown');

    if (searchContainer && !searchContainer.contains(event.target)) {
        dropdown.style.display = 'none';
    }
});
