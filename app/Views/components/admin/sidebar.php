<?php
// Determine active menu item based on URI
$uri = service('uri');
$segment = $uri->getSegment(2); // 'dashboard', 'list_user', etc.
?>
<aside class="admin-sidebar">
    <div class="sidebar-header">
        <img src="<?= base_url('ICON/logo.png') ?>" alt="Logo" class="sidebar-logo">
        <div class="sidebar-title">Admin</div>
    </div>

    <nav class="sidebar-menu">
        <a href="<?= base_url('admin/dashboard') ?>" class="sidebar-link <?= $segment == 'dashboard' ? 'active' : '' ?>">
            <i data-lucide="layout-dashboard"></i>
            <span>Dashboard</span>
        </a>
        
        <a href="<?= base_url('admin/list_user') ?>" class="sidebar-link <?= $segment == 'list_user' ? 'active' : '' ?>">
            <i data-lucide="users"></i>
            <span>Users</span>
        </a>

        <a href="<?= base_url('admin/list_post') ?>" class="sidebar-link <?= $segment == 'list_post' ? 'active' : '' ?>">
            <i data-lucide="file-text"></i>
            <span>Posts</span>
        </a>

        <a href="<?= base_url('admin/list_group') ?>" class="sidebar-link <?= $segment == 'list_group' ? 'active' : '' ?>">
            <i data-lucide="users-2"></i>
            <span>Groups</span>
        </a>

        <a href="<?= base_url('admin/list_chat') ?>" class="sidebar-link <?= $segment == 'list_chat' ? 'active' : '' ?>">
            <i data-lucide="message-circle"></i>
            <span>Chats</span>
        </a>

        <div style="margin: 8px 0; border-top: 1px solid #eee;"></div>

        <a href="<?= base_url('admin/appeals') ?>" class="sidebar-link <?= $segment == 'appeals' ? 'active' : '' ?>">
            <i data-lucide="gavel"></i>
            <span>Ban Appeals</span>
        </a>
    </nav>

    <div class="sidebar-footer">
        <a href="<?= base_url('logout') ?>" class="sidebar-link" style="color: #dc3545;">
            <i data-lucide="log-out"></i>
            <span>Logout</span>
        </a>
    </div>
</aside>
