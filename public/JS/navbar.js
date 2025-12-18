lucide.createIcons();

const navMenu = document.querySelector('.nav-menu');
const navItems = document.querySelectorAll('.nav-item');
const line = document.querySelector('.nav-line');

function moveLine(target) {
    const rect = target.getBoundingClientRect();
    const menuRect = navMenu.getBoundingClientRect();

    line.style.width = rect.width + 'px';
    line.style.left = (rect.left - menuRect.left) + 'px';
}

const activeItem = document.querySelector('.nav-item.active');
if (activeItem) moveLine(activeItem);

navItems.forEach(item => {
    item.addEventListener('click', () => {
        navItems.forEach(i => i.classList.remove('active'));
        item.classList.add('active');
        moveLine(item);
    });
});

window.addEventListener('resize', () => {
    const active = document.querySelector('.nav-item.active');
    if (active) moveLine(active);
});
