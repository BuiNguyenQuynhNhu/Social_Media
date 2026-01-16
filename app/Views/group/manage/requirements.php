<?= $this->extend('group/manage/layout') ?>

<?= $this->section('content') ?>
<div class="card">
    <div class="card-header">
        <h2>Participation Questions</h2>
    </div>
    <div class="card-body">
        <input type="hidden" class="csrf-token" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>">
        <p class="text-muted" style="margin-bottom: 20px;">Ask new members questions when they join. Admin approval is required if questions are set.</p>
        
        <!-- VIEW MODE -->
        <div id="view-mode">
            <div id="view-questions-list" style="margin-bottom: 20px;"></div>
            <button class="btn-secondary" onclick="toggleEditMode()">Edit Questions</button>
        </div>

        <!-- EDIT MODE -->
        <div id="edit-mode" style="display: none;">
            <div id="edit-questions-list"></div>
            <button class="btn-secondary" onclick="addQuestion()" style="margin-top:10px; margin-bottom: 10px; width:100%; border-style:dashed;">+ Add Question</button>
            
            <div style="display: flex; gap: 10px; margin-top: 20px;">
                <button class="btn-primary" onclick="saveRequirements()">Save Changes</button>
                <button class="btn-secondary" onclick="cancelEdit()">Cancel</button>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    let questions = <?= json_encode($questions ?? []) ?>;
    // Normalize data
    questions = questions.map(q => typeof q === 'string' ? {type: 'text', question: q} : q);

    function init() {
        renderViewMode();
    }

    function toggleEditMode() {
        $('#view-mode').hide();
        $('#edit-mode').fadeIn();
        renderEditMode();
    }

    function cancelEdit() {
        $('#edit-mode').hide();
        $('#view-mode').fadeIn();
    }

    function renderViewMode() {
        const container = $('#view-questions-list');
        container.empty();
        
        if (questions.length === 0) {
            container.html('<div class="empty-state">No questions set.</div>');
            return;
        }

        questions.forEach((q, i) => {
             let details = '';
             if (q.type === 'radio' || q.type === 'checkbox') {
                 details = `<ul style="margin-left:20px; color:#666; font-size:13px; margin-top:4px;">${(q.options||[]).map(o => `<li>${esc(o)}</li>`).join('')}</ul>`;
             }
             container.append(`
                <div class="view-q-item" style="padding: 10px; border-bottom: 1px solid #eee;">
                    <div style="font-weight:600; font-size:15px;">${i+1}. ${esc(q.question)}</div>
                    <div style="font-size:12px; color:#888; margin-bottom:4px;">Type: ${q.type}</div>
                    ${details}
                </div>
             `);
        });
    }
    
    // Helper to escape HTML in JS (basic)
    function esc(str) {
        if(!str) return '';
        return str.replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/"/g, "&quot;").replace(/'/g, "&#039;");
    }

    function renderEditMode() {
        const container = $('#edit-questions-list');
        container.empty();
        
        questions.forEach((q, index) => {
            let optionsHtml = '';
            if (['radio', 'checkbox'].includes(q.type)) {
                const opts = q.options || [];
                const optsList = opts.map((opt, oi) => `
                    <div style="display:flex; gap:8px; margin-top:4px;">
                        <input type="text" class="form-input" value="${esc(opt)}" placeholder="Option ${oi+1}" onchange="updateOption(${index}, ${oi}, this.value)" style="width:100%; font-size:13px; padding:6px;">
                        <button onclick="removeOption(${index}, ${oi})" class="icon-btn" style="color:red; width:30px;"><i data-lucide="x"></i></button>
                    </div>
                `).join('');
                
                optionsHtml = `
                    <div style="margin-top:8px; padding-left:12px; border-left:2px solid #eee;">
                        <small style="display:block; margin-bottom:4px; font-weight:600;">Options:</small>
                        ${optsList}
                        <button onclick="addOption(${index})" class="btn-secondary" style="margin-top:8px; font-size:12px; padding:4px 8px;">+ Add Option</button>
                    </div>
                `;
            }

            container.append(`
                <div class="question-item" style="margin-bottom: 12px; border: 1px solid #ddd; padding: 12px; border-radius: 8px; background: #fff;">
                    <div style="display:flex; justify-content:space-between; margin-bottom:8px; align-items:center;">
                        <strong style="font-size:15px;">Question ${index + 1}</strong>
                        <div style="display:flex; gap:8px;">
                            <select class="form-select" onchange="updateType(${index}, this.value)" style="padding:4px; font-size:13px;">
                                <option value="text" ${q.type === 'text' ? 'selected' : ''}>Text Answer</option>
                                <option value="radio" ${q.type === 'radio' ? 'selected' : ''}>Multiple Choice</option>
                                <option value="checkbox" ${q.type === 'checkbox' ? 'selected' : ''}>Checkboxes</option>
                            </select>
                            <button onclick="removeQuestion(${index})" class="icon-btn" style="color:red;"><i data-lucide="trash-2"></i></button>
                        </div>
                    </div>
                    <input type="text" class="form-input" value="${esc(q.question)}" placeholder="Enter your question here..." onchange="updateQuestionText(${index}, this.value)" style="width:100%;">
                    ${optionsHtml}
                </div>
            `);
        });
        lucide.createIcons();
    }

    function addQuestion() {
        questions.push({type: 'text', question: '', options: []});
        renderEditMode();
    }

    function removeQuestion(index) {
        questions.splice(index, 1);
        renderEditMode();
    }

    function updateQuestionText(index, val) {
        questions[index].question = val;
    }

    function updateType(index, val) {
        questions[index].type = val;
        if (!questions[index].options) questions[index].options = [];
        renderEditMode();
    }

    function addOption(qIndex) {
        if (!questions[qIndex].options) questions[qIndex].options = [];
        questions[qIndex].options.push('');
        renderEditMode();
    }

    function updateOption(qIndex, optIndex, val) {
        questions[qIndex].options[optIndex] = val;
    }

    function removeOption(qIndex, optIndex) {
        questions[qIndex].options.splice(optIndex, 1);
        renderEditMode();
    }

    function saveRequirements() {
        const csrfName = $('.csrf-token').attr('name');
        const csrfHash = $('.csrf-token').val();
        
        let data = { questions: questions };
        data[csrfName] = csrfHash;

        $.post(BASE_URL + '/group/' + GROUP_ID + '/requirements', data, function(res) {
            if (res.status === 'success') {
                alert('Saved!');
                cancelEdit(); // Go back to view
                renderViewMode(); // Refresh view
            }
        });
    }

    init();
</script>
<?= $this->endSection() ?>
