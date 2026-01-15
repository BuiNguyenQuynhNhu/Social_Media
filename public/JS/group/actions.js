// =========================================
// GROUP ACTIONS MODULE
// Handles group join, leave, and navigation
// =========================================

/**
 * Join group
 */
function joinGroup() {
    if (!window.GROUP_ID) return;

    $.ajax({
        url: window.BASE_URL + '/group/' + window.GROUP_ID + '/join',
        type: 'POST',
        success: function (res) {
            if (res.status === 'success') {
                location.reload();
            } else if (res.code === 'REQUIREMENTS_NEEDED') {
                showRequirementsModal(res.questions);
            } else {
                alert(res.message || 'Failed to join group');
            }
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
        const answers = $(this).serializeArray();

        // Convert to object or keep as array based on backend expectation
        // Backend expects 'answers' post data. serializeArray sends name/value pairs.
        // Let's manually construct the data object to match PHP expectation
        let postData = { answers: [] };
        $(this).find('input').each(function () {
            postData.answers.push($(this).val());
        });

        $.ajax({
            url: BASE_URL + '/group/' + GROUP_ID + '/join',
            type: 'POST',
            data: postData,
            success: function (res) {
                if (res.status === 'success') {
                    location.reload();
                } else {
                    alert(res.message || 'Failed to submit answers');
                }
            }
        });
    });
}

/**
 * Leave group
 */
function leaveGroup() {
    if (!GROUP_ID) return;

    if (confirm('Are you sure you want to leave this group?')) {
        $.ajax({
            url: BASE_URL + '/group/' + GROUP_ID + '/leave',
            type: 'POST',
            success: function (res) {
                if (res.status === 'success') {
                    window.location.href = BASE_URL + '/group';
                } else {
                    alert(res.message || 'Failed to leave group');
                }
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
            groupInput.value = GROUP_ID;
        }
        openCreatePostModal();
    } else {
        alert('Post creation feature integrated with existing post system');
    }
}
