<div class="group-browse-container">
    <div class="browse-header">
        <h1>Groups</h1>
        <a href="<?= base_url('group/create-view') ?>" class="btn btn-blue" style="text-decoration: none; display: inline-flex; align-items: center; gap: 8px;">
            <i data-lucide="plus"></i>
            Create Group
        </a>
    </div>

    <div class="group-tabs">
        <button class="group-tab active" data-tab="my-groups">My Groups</button>
        <button class="group-tab" data-tab="suggested">Suggested</button>
    </div>

    <div id="my-groups" class="group-grid" style="display: grid;"></div>
    <div id="suggested" class="group-grid" style="display: none;"></div>
</div>
