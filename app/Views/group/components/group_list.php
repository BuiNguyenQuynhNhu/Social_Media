<div class="group-browse-container">
    <div class="browse-header">
        <h1>Groups</h1>
        <button class="btn btn-blue" onclick="openCreateGroupModal()">
            <i data-lucide="plus"></i>
            Create Group
        </button>
    </div>

    <div class="group-tabs">
        <button class="group-tab active" data-tab="my-groups">My Groups</button>
        <button class="group-tab" data-tab="suggested">Suggested</button>
    </div>

    <div id="my-groups" class="group-grid" style="display: grid;"></div>
    <div id="suggested" class="group-grid" style="display: none;"></div>
</div>

<?= view('group/components/create_group_modal') ?>
