// =========================================
// GROUP ACTIONS MODULE
// Handles group join, leave, and navigation
// =========================================

/**
 * Join group
 */
function joinGroup() {
    if (!window.GROUP_ID) return;

    // Use sendRequest (from post.js) which handles CSRF
    // Use window.BASE_URL
    sendRequest(window.BASE_URL + '/group/' + window.GROUP_ID + '/join')
        .then(res => {
            if (res.status === 'success') {
                location.reload();
            } else if (res.code === 'REQUIREMENTS_NEEDED') {
                showRequirementsModal(res.questions);
            } else {
                alert(res.message || 'Failed to join group');
            }
        });
}

function showRequirementsModal(questions) {
    let html = `
        <div id="req-modal" style="position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:9999; display:flex; justify-content:center; align-items:center;">
            <div style="background:white; padding:20px; border-radius:8px; max-width:500px; width:90%;">
                <h3 style="margin-top:0;">Answer Questions to Join</h3>
                <p>The admin requires you to answer these questions:</p>
                <form id="req-form">
    `;

    questions.forEach((q, i) => {
        html += `
            <div style="margin-bottom:12px;">
                <label style="display:block; font-weight:600; margin-bottom:4px;">${q}</label>
                <input type="text" name="answers[${i}]" required style="width:100%; padding:8px; border:1px solid #ddd; border-radius:4px;">
            </div>
        `;
    });

    html += `
                    <div style="display:flex; justify-content:flex-end; gap:8px; margin-top:16px;">
                        <button type="button" onclick="$('#req-modal').remove()" style="padding:8px 16px; border:none; background:#eee; cursor:pointer; border-radius:4px;">Cancel</button>
                        <button type="submit" style="padding:8px 16px; border:none; background:#1877f2; color:white; cursor:pointer; border-radius:4px;">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    `;

    $('body').append(html);

    $('#req-form').on('submit', function (e) {
        e.preventDefault();

        let postData = new FormData();
        $(this).find('input').each(function () {
            postData.append('answers[]', $(this).val());
        });

        // Use sendRequest for CSRF
        sendRequest(window.BASE_URL + '/group/' + window.GROUP_ID + '/join', postData)
            .then(res => {
                if (res.status === 'success') {
                    location.reload();
                } else {
                    alert(res.message || 'Failed to submit answers');
                }
            });
    });
}

/**
 * Leave group
 */
function leaveGroup() {
    if (!window.GROUP_ID) return;

    if (confirm('Are you sure you want to leave this group?')) {
        // Use sendRequest for CSRF
        sendRequest(window.BASE_URL + '/group/' + window.GROUP_ID + '/leave')
            .then(res => {
                if (res.status === 'success') {
                    window.location.href = window.BASE_URL + '/group';
                } else {
                    alert(res.message || 'Failed to leave group');
                }
            });
    }
}

/**
 * Switch between tabs (Feed, Members, About)
 */
function switchTab(tabName) {
    // Hide all tab contents
    $('#group-feed-content').hide();
    $('#group-members-content').hide();
    $('#group-about-content').hide();

    // Show selected tab content
    $(`#group-${tabName}-content`).show();

    // Update tab button active state
    $('.group-nav-tab').removeClass('active');
    $(`.group-nav-tab[data-tab="${tabName}"]`).addClass('active');

    // Load data if needed
    if (tabName === 'members') {
        loadGroupMembers();
    }
}

/**
 * Open group post modal
 */
function openGroupPostModal() {
    if (typeof openCreatePostModal === 'function') {
        // Set group ID in the modal
        const groupInput = document.getElementById('modal-group-id');
        if (groupInput) {
            groupInput.value = window.GROUP_ID;
        }
        openCreatePostModal();
    } else {
        alert('Post creation feature integrated with existing post system');
    }
}
