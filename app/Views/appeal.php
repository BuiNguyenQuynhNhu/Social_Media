<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submit Appeal</title>
    <link rel="stylesheet" href="<?= base_url('CSS/navbar.css') ?>">
    <link rel="stylesheet" href="<?= base_url('CSS/overall.css') ?>">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        .appeal-container {
            max-width: 600px;
            margin: 40px auto;
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .form-group { margin-bottom: 20px; }
        label { display: block; font-weight: 600; margin-bottom: 8px; }
        textarea { width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 6px; min-height: 150px; font-family: inherit; resize: vertical; }
        .btn-submit { background: #1877f2; color: white; padding: 10px 20px; border: none; border-radius: 6px; font-weight: 600; cursor: pointer; width: 100%; }
        .btn-submit:hover { background: #166fe5; }
        .alert { padding: 15px; border-radius: 6px; margin-bottom: 20px; display: none; }
        .alert-success { background: #d4edda; color: #155724; }
        .alert-error { background: #f8d7da; color: #721c24; }
    </style>
</head>
<body class="background">
    <?= view('components/user/navbar', ['avatarUrl' => $avatarUrl]) ?>

    <div class="appeal-container">
        <h2 style="margin-top:0;">⚖️ Submit Ban Appeal</h2>
        <p style="color:#65676b; margin-bottom:24px;">If you believe your account was suspended in error, you can submit an appeal here. Our team will review your request within 7 days.</p>

        <div id="alert-box" class="alert"></div>

        <form id="appeal-form">
            <input type="hidden" name="notification_id" value="<?= esc($notification_id ?? '') ?>">
            
            <div class="form-group">
                <label for="appeal-reason">Reason for Appeal</label>
                <textarea id="appeal-reason" name="reason" placeholder="Please explain why your suspension should be lifted..." required></textarea>
            </div>

            <button type="submit" class="btn-submit">Submit Appeal</button>
        </form>
    </div>

    <script>
        window.BASE_URL = '<?= base_url() ?>';
        // Simple CSRF handling
        const csrfName = '<?= csrf_token() ?>';
        const csrfHash = '<?= csrf_hash() ?>';

        $('#appeal-form').on('submit', function(e) {
            e.preventDefault();
            const reason = $('#appeal-reason').val().trim();
            const notifId = $('input[name="notification_id"]').val();

            if (!reason) {
                alert('Please provide a reason.');
                return;
            }

            $.ajax({
                url: window.BASE_URL + '/appeal/submit',
                type: 'POST',
                data: {
                    reason: reason,
                    notification_id: notifId,
                    [csrfName]: csrfHash
                },
                success: function(res) {
                    if (res.status === 'success') {
                        $('#alert-box').removeClass('alert-error').addClass('alert-success').text(res.message).show();
                        $('#appeal-form').hide();
                    } else {
                        $('#alert-box').removeClass('alert-success').addClass('alert-error').text(res.message || 'Error').show();
                    }
                },
                error: function(xhr) {
                    $('#alert-box').removeClass('alert-success').addClass('alert-error').text('An error occurred.').show();
                }
            });
        });
    </script>
</body>
</html>
