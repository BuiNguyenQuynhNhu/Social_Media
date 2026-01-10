function previewPostFiles(input) {
    const container = document.getElementById('media-preview-container');
    container.innerHTML = ''; 
    
    if (input.files && input.files.length > 0) {
        container.style.display = 'grid'; 
        Array.from(input.files).forEach(file => {
            const reader = new FileReader();
            reader.onload = function(e) {
                const div = document.createElement('div');
                div.className = 'preview-item';
                
                if (file.type.startsWith('video/')) {
                    div.innerHTML = `<video src="${e.target.result}" muted></video>`;
                } else {
                    div.innerHTML = `<img src="${e.target.result}">`;
                }
                container.appendChild(div);
            }
            reader.readAsDataURL(file);
        });
    } else {
        container.style.display = 'none';
    }
}
function updatePrivacyIcon(select) {
    const iconTag = document.getElementById('privacy-icon');
    const selectedOption = select.options[select.selectedIndex];
    const iconName = selectedOption.getAttribute('data-icon');
    
    iconTag.setAttribute('data-lucide', iconName);
    lucide.createIcons();
}
document.addEventListener('DOMContentLoaded', () => {
    lucide.createIcons();
});