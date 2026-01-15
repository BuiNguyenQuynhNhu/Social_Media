<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Group | Social Media</title>
    
    <!-- Stylesheets -->
    <link rel="stylesheet" href="<?= base_url('CSS/navbar.css') ?>">
    <link rel="stylesheet" href="<?= base_url('CSS/overall.css') ?>">
    
    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <script>
        window.BASE_URL = '<?= rtrim(base_url(), '/') ?>';
        const APP_CONFIG = {
            baseURL: window.BASE_URL,
            userId: '<?= $user_id ?>',
            csrfTokenName: '<?= csrf_token() ?>',     
            csrfHash: '<?= csrf_hash() ?>'
        };
    </script>
    
    <link rel="stylesheet" href="<?= base_url('CSS/group/group_create.css') ?>">
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
    // Config handled in head
    
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
            url: window.BASE_URL + '/group/create',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(res) {
                if (res.status === 'success') {
                    window.location.href = window.BASE_URL + '/group/' + res.group_id;
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
