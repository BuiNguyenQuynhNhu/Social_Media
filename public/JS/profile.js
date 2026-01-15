
    lucide.createIcons();

    function openEditModal(focusSection) {
        const modal = document.getElementById('edit-modal');
        modal.style.display = 'flex';
        document.querySelectorAll('.edit-section').forEach(s => s.style.background = 'transparent');
        if (focusSection !== 'all') {
            const section = document.getElementById(focusSection);
            section.style.background = '#f0f2f5';
            section.scrollIntoView({ behavior: 'smooth' });
        }
    }

    function closeModal() {
        document.getElementById('edit-modal').style.display = 'none';
    }

    function previewImage(input, previewId) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById(previewId).src = e.target.result;
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    window.onclick = function(e) {
        const modal = document.getElementById('edit-modal');
        if (e.target === modal) closeModal();
    }

    function switchTab(tabName, element) {
        document.querySelectorAll('.nav-tab').forEach(tab => tab.classList.remove('active'));
        element.classList.add('active');

        document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
        const target = document.getElementById(tabName + '-container');
        if(target) target.classList.add('active');
    }