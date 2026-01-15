<?= $this->extend('group/layout') ?>

<?= $this->section('content') ?>
    <?= view('group/components/group_about', ['group' => $group]) ?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            $('#group-about-content').show();
        });
    </script>
<?= $this->endSection() ?>
