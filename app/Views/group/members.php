<?= $this->extend('group/layout') ?>

<?= $this->section('content') ?>
    <?= view('group/components/group_members', [
        'is_member' => $is_member
    ]) ?>
    <script>
        // Trigger member loading on page load since we are on the members page
        document.addEventListener('DOMContentLoaded', function() {
            // Unhide content since it might be hidden by default styled components
            $('#group-members-content').show();
            // Load members
            if(typeof loadGroupMembers === 'function') {
                loadGroupMembers();
            }
        });
    </script>
<?= $this->endSection() ?>
