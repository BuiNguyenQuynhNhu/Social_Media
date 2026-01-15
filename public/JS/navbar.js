

// Initialize Lucide Icons immediately if available
if (window.lucide) {
    lucide.createIcons();
}

/**
 * Main function to position the sliding line
 */
// Update nav line position
function updateNavLine() {
    const navMenu = document.querySelector('.nav-menu');
    const line = document.querySelector('.nav-line');
    // Scope selector to ONLY the main navbar menu
    let activeItem = document.querySelector('.nav-menu .nav-item.active');

    // Safety check: if no active item, hide line or try to find it
    if (!activeItem) {
        setActiveByUrl();
        activeItem = document.querySelector('.nav-menu .nav-item.active');
    }

    if (activeItem && navMenu && line) {
        const rect = activeItem.getBoundingClientRect();
        const menuRect = navMenu.getBoundingClientRect();
        const left = rect.left - menuRect.left;

        // Ensure valid values
        if (rect.width > 0) {
            line.style.width = `${rect.width}px`;
            line.style.left = `${left}px`;
            line.style.opacity = '1';
        }
    } else if (line) {
        line.style.opacity = '0'; // Hide if no active state
    }
}

// Fallback: Find active link based on URL if PHP didn't set it
function setActiveByUrl() {
    if (document.querySelector('.nav-menu .nav-item.active')) return;

    const currentUrl = window.location.href;
    const navItems = document.querySelectorAll('.nav-menu .nav-item');

    // 1. Try exact match
    for (let item of navItems) {
        if (item.href === currentUrl) {
            item.classList.add('active');
            return;
        }
    }

    // 2. Try partial match
    // Sort by length desc to match most specific path first
    const sortedItems = Array.from(navItems).sort((a, b) => b.href.length - a.href.length);

    for (let item of sortedItems) {
        // Skip base URL / items to prevent default highlighting everything
        const listUrl = new URL(item.href);
        if (listUrl.pathname === '/' || listUrl.pathname === '') continue;

        if (currentUrl.includes(item.href) || currentUrl.startsWith(item.href)) {
            item.classList.add('active');
            return;
        }
    }
}

function initNavbarListeners() {
    const navItems = document.querySelectorAll('.nav-item');

    navItems.forEach(item => {
        item.addEventListener('click', (e) => {
            // Remove active from all
            navItems.forEach(i => i.classList.remove('active'));
            // Add to clicked
            e.currentTarget.classList.add('active');
            updateNavLine();
        });
    });

    // Resize observer
    const navMenu = document.querySelector('.nav-menu');
    if (navMenu) {
        const resizeObserver = new ResizeObserver(() => {
            requestAnimationFrame(updateNavLine);
        });
        resizeObserver.observe(navMenu);
    }

    window.addEventListener('resize', updateNavLine);
}


document.addEventListener('DOMContentLoaded', () => {
    initNavbarListeners();
    // Try immediately
    updateNavLine();

    // Retry effectively to catch rendering delays
    setTimeout(updateNavLine, 50);
    setTimeout(updateNavLine, 150);
    setTimeout(updateNavLine, 300);
});

window.addEventListener('load', () => {
    if (window.lucide) lucide.createIcons();
    updateNavLine();
});
