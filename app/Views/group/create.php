<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="base_url" content="<?= rtrim(base_url(), '/') ?>">
    <meta name="user_id" content="<?= $user_id ?>">
    <title>Create Group | Social Media</title>
    
    <!-- Stylesheets -->
    <link rel="stylesheet" href="<?= base_url('CSS/navbar.css') ?>">
    <link rel="stylesheet" href="<?= base_url('CSS/overall.css') ?>">
    
    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    
    <style>
        .create-group-container {
            max-width: 680px;
            margin: 40px auto;
            padding: 0 20px;
        }

        .create-group-card {
            background: white;
            border-radius: 8px;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
            padding: 24px;
        }

        .page-header {
            margin-bottom: 20px;
            padding-bottom: 16px;
            border-bottom: 1px solid #e4e6eb;
        }

        .page-header h1 {
            font-size: 24px;
            font-weight: 700;
            color: #050505;
            margin: 0;
        }

        .group-intro-section {
            display: flex;
            gap: 16px;
            margin-bottom: 24px;
            padding: 16px;
            background: #f0f2f5;
            border-radius: 8px;
        }

        .intro-illustration {
            flex-shrink: 0;
        }

        .intro-text h3 {
            font-size: 17px;
            font-weight: 600;
            margin: 0 0 8px 0;
            color: #050505;
        }

        .intro-text p {
            font-size: 15px;
            line-height: 20px;
            color: #65676b;
            margin: 0;
        }

        .form-section {
            margin-bottom: 24px;
        }

        .form-section label {
            display: block;
            font-size: 17px;
            font-weight: 600;
            color: #050505;
            margin-bottom: 8px;
        }

        .form-input, .form-textarea, .form-select {
            width: 100%;
            padding: 12px;
            font-size: 15px;
            border: 1px solid #ccd0d5;
            border-radius: 6px;
            font-family: inherit;
            transition: border-color 0.2s;
        }

        .form-input:focus, .form-textarea:focus, .form-select:focus {
            outline: none;
            border-color: #1877f2;
        }

        .form-textarea {
            resize: vertical;
            min-height: 100px;
        }

        .privacy-option {
            padding: 12px;
            border: 1px solid #ccd0d5;
            border-radius: 8px;
            margin-bottom: 8px;
            cursor: pointer;
            transition: all 0.2s;
        }

        .privacy-option:hover {
            background: #f0f2f5;
        }

        .privacy-option.selected {
            border-color: #1877f2;
            background: #e7f3ff;
        }

        .privacy-option input[type="radio"] {
            margin-right: 12px;
        }

        .privacy-label {
            display: flex;
            align-items: start;
            gap: 12px;
        }

        .privacy-info h4 {
            font-size: 15px;
            font-weight: 600;
            margin: 0 0 4px 0;
            color: #050505;
        }

        .privacy-info p {
            font-size: 13px;
            color: #65676b;
            margin: 0;
        }

        .form-actions {
            display: flex;
            gap: 12px;
            justify-content: flex-end;
            padding-top: 16px;
            border-top: 1px solid #e4e6eb;
        }

        .btn {
            padding: 10px 24px;
            font-size: 15px;
            font-weight: 600;
            border-radius: 6px;
            border: none;
            cursor: pointer;
            transition: all 0.2s;
        }

        .btn-primary {
            background: #1877f2;
            color: white;
        }

        .btn-primary:hover {
            background: #166fe5;
        }

        .btn-secondary {
            background: #e4e6eb;
            color: #050505;
        }

        .btn-secondary:hover {
            background: #d8dadf;
        }
    </style>
</head>
<body class="background">

<?= view('components/user/navbar', ['avatarUrl' => $avatarUrl]) ?>

<div class="create-group-container">
    <div class="create-group-card">
        <div class="page-header">
            <h1>Create New Group</h1>
        </div>

        <div class="group-intro-section">
            <div class="intro-illustration">
                <svg width="60" height="60" viewBox="0 0 60 60" fill="none">
                    <circle cx="20" cy="25" r="15" fill="#4CAF50"/>
                    <circle cx="35" cy="30" r="12" fill="#2196F3"/>
                    <circle cx="45" cy="20" r="10" fill="#FFC107"/>
                </svg>
            </div>
            <div class="intro-text">
                <h3>Bring people together</h3>
                <p>Groups are great for getting things done and staying in touch with just the people you want. Share photos and videos, have conversations, make plans and more.</p>
            </div>
        </div>

        <form id="create-group-form" method="POST" enctype="multipart/form-data">
            <?= csrf_field() ?>
            
            <div class="form-section">
                <label for="group-name">Name your group</label>
                <input type="text" 
                       name="name" 
                       id="group-name" 
                       class="form-input" 
                       placeholder="Enter group name" 
                       required 
                       maxlength="100">
            </div>

            <div class="form-section">
                <label>Select privacy</label>
                
                <div class="privacy-option selected" onclick="selectPrivacy('public', this)">
                    <label class="privacy-label">
                        <input type="radio" name="privacy" value="public" checked>
                        <div class="privacy-info">
                            <h4>🌍 Public</h4>
                            <p>Anyone can see who's in the group and what they post.</p>
                        </div>
                    </label>
                </div>

                <div class="privacy-option" onclick="selectPrivacy('private', this)">
                    <label class="privacy-label">
                        <input type="radio" name="privacy" value="private">
                        <div class="privacy-info">
                            <h4>🔒 Private</h4>
                            <p>Only members see who's in the group and what they post.</p>
                        </div>
                    </label>
                </div>
            </div>

            <div class="form-section">
                <label for="group-description">Description (Optional)</label>
                <textarea name="description" 
                          id="group-description" 
                          class="form-textarea" 
                          placeholder="What's your group about?"
                          maxlength="500"></textarea>
            </div>

            <div class="form-actions">
                <button type="button" class="btn btn-secondary" onclick="window.location.href='<?= base_url('group') ?>'">
                    Cancel
                </button>
                <button type="submit" class="btn btn-primary">
                    Create Group
                </button>
            </div>
        </form>
    </div>
</div>

<script>
const BASE_URL = $('meta[name="base_url"]').attr('content');

function selectPrivacy(value, element) {
    document.querySelectorAll('.privacy-option').forEach(opt => {
        opt.classList.remove('selected');
    });
    element.classList.add('selected');
    element.querySelector('input[type="radio"]').checked = true;
}

$('#create-group-form').on('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    $.ajax({
        url: BASE_URL + '/group/create',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(res) {
            if (res.status === 'success') {
                window.location.href = BASE_URL + '/group/' + res.group_id;
            } else {
                alert(res.message || 'Failed to create group');
            }
        },
        error: function() {
            alert('Failed to create group. Please try again.');
        }
    });
});

lucide.createIcons();
</script>

</body>
</html>
