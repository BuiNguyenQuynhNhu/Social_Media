<?= $this->extend('group/layout') ?>

<?= $this->section('content') ?>
    <?= view('group/components/group_feed', [
        'posts' => $posts ?? [],
        'is_member' => $is_member,
        'avatarUrl' => $avatarUrl,
        'user' => $user,
        'group' => $group
    ]) ?>
<?= $this->endSection() ?>
