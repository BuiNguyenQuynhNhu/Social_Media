// =========================================
// POST REACTIONS MODULE
// Handles post reactions (like, love, haha, etc.)
// =========================================

function sendReaction(postId, type) {
    if (!window.BASE_URL) {
        console.error('BASE_URL is not defined');
        return;
    }

    $.post(window.BASE_URL + '/interaction/react/' + postId, { type: type }, function (res) {
        if (res.status === 'success') {
            updateLikes(postId);
            updateReactionButton(postId, res.action, type);
        }
    }).fail(function () {
        console.error('Failed to send reaction');
    });
}

function updateReactionButton(postId, action, type) {
    const btnText = document.getElementById('reaction-btn-text-' + postId);
    const btn = document.getElementById('btn-react-' + postId);

    if (!btnText || !btn) return;

    if (action === 'unreacted') {
        btnText.innerText = 'Like';
        btn.classList.remove('active');
        btn.style.color = '';
    } else {
        const typeMap = {
            'like': 'Like', 'love': 'Love', 'haha': 'Haha',
            'wow': 'Wow', 'sad': 'Sad', 'angry': 'Angry'
        };
        const colorMap = {
            'like': '#1877f2', 'love': '#e41e3f', 'haha': '#ffe815ff',
            'wow': '#f7b928', 'sad': '#e29e00ff', 'angry': '#e41e3f'
        };
        btnText.innerText = typeMap[type] || 'Like';
        btn.classList.add('active');
        btn.style.color = colorMap[type] || '';
    }
}

function updateLikes(postId) {
    if (!window.BASE_URL) return;

    $.get(window.BASE_URL + '/interaction/get_likes/' + postId, function (res) {
        const count = res.total || 0;
        $('#likes-count-' + postId).text(count > 0 ? count : '');

        // Update reaction button based on user's reaction
        if (res.myReaction) {
            updateReactionButton(postId, 'reacted', res.myReaction);
        } else {
            updateReactionButton(postId, 'unreacted', 'like');
        }
    }).fail(function () {
        console.error('Failed to update likes for post ' + postId);
    });
}
