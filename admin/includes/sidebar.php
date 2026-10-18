<?php
/**
 * Global Connect - Admin Sidebar
 */
$db = get_db();
$unreadInquiries = 0;
$totalPages = 0;
try {
    $unreadInquiries = $db->query("SELECT COUNT(*) FROM contacts WHERE status = 'unread'")->fetchColumn();
    $totalPages = $db->query("SELECT COUNT(*) FROM pages")->fetchColumn();
} catch (Exception $e) {}

$currentScript = basename($_SERVER['SCRIPT_NAME'] ?? '');
$categoryParam = $_GET['category'] ?? '';
?>
<aside class="admin-sidebar">
    <div class="sidebar-header">
        <img src="<?= image_url('logo-design-gcv.png') ?>" alt="GCV Logo">
        <div class="sidebar-brand-text">GLOBAL <span>CONNECT</span> CMS</div>
    </div>

    <ul class="sidebar-menu">
        <li class="sidebar-heading">Main</li>
        <li class="sidebar-item <?= $currentScript === 'index.php' ? 'active' : '' ?>">
            <a href="<?= admin_url('index.php') ?>" class="sidebar-link">
                <div class="sidebar-link-content">
                    <span class="sidebar-link-icon">📊</span>
                    <span>Dashboard</span>
                </div>
            </a>
        </li>

        <li class="sidebar-heading">Content Management</li>
        <li class="sidebar-item <?= ($currentScript === 'pages.php' && empty($categoryParam)) ? 'active' : '' ?>">
            <a href="<?= admin_url('pages.php') ?>" class="sidebar-link">
                <div class="sidebar-link-content">
                    <span class="sidebar-link-icon">📄</span>
                    <span>All Pages</span>
                </div>
                <span class="sidebar-badge" style="background: rgba(255,255,255,0.15); color: #fff;"><?= $totalPages ?></span>
            </a>
        </li>

        <li class="sidebar-item <?= ($currentScript === 'page-add.php' && $categoryParam === 'trading') ? 'active' : '' ?>">
            <a href="<?= admin_url('page-add.php?category=trading') ?>" class="sidebar-link">
                <div class="sidebar-link-content">
                    <span class="sidebar-link-icon">📦</span>
                    <span>+ Add Trading Page</span>
                </div>
            </a>
        </li>

        <li class="sidebar-item <?= ($currentScript === 'page-add.php' && $categoryParam === 'finance') ? 'active' : '' ?>">
            <a href="<?= admin_url('page-add.php?category=finance') ?>" class="sidebar-link">
                <div class="sidebar-link-content">
                    <span class="sidebar-link-icon">💳</span>
                    <span>+ Add Finance Page</span>
                </div>
            </a>
        </li>

        <li class="sidebar-item <?= $currentScript === 'categories.php' ? 'active' : '' ?>">
            <a href="<?= admin_url('categories.php') ?>" class="sidebar-link">
                <div class="sidebar-link-content">
                    <span class="sidebar-link-icon">📂</span>
                    <span>Categories</span>
                </div>
            </a>
        </li>

        <li class="sidebar-item <?= $currentScript === 'media.php' ? 'active' : '' ?>">
            <a href="<?= admin_url('media.php') ?>" class="sidebar-link">
                <div class="sidebar-link-content">
                    <span class="sidebar-link-icon">🖼</span>
                    <span>Media Library</span>
                </div>
            </a>
        </li>

        <li class="sidebar-heading">Inquiries & Leads</li>
        <li class="sidebar-item <?= $currentScript === 'contacts.php' ? 'active' : '' ?>">
            <a href="<?= admin_url('contacts.php') ?>" class="sidebar-link">
                <div class="sidebar-link-content">
                    <span class="sidebar-link-icon">✉</span>
                    <span>Inquiries</span>
                </div>
                <?php if ($unreadInquiries > 0): ?>
                    <span class="sidebar-badge" style="background: var(--admin-danger); color: #ffffff;"><?= $unreadInquiries ?> new</span>
                <?php endif; ?>
            </a>
        </li>

        <li class="sidebar-heading">Configuration</li>
        <li class="sidebar-item <?= $currentScript === 'settings.php' ? 'active' : '' ?>">
            <a href="<?= admin_url('settings.php') ?>" class="sidebar-link">
                <div class="sidebar-link-content">
                    <span class="sidebar-link-icon">⚙</span>
                    <span>Site Settings</span>
                </div>
            </a>
        </li>

        <li class="sidebar-item <?= $currentScript === 'profile.php' ? 'active' : '' ?>">
            <a href="<?= admin_url('profile.php') ?>" class="sidebar-link">
                <div class="sidebar-link-content">
                    <span class="sidebar-link-icon">👤</span>
                    <span>Admin Profile</span>
                </div>
            </a>
        </li>

        <li class="sidebar-item">
            <a href="<?= admin_url('logout.php') ?>" class="sidebar-link" style="color: #f87171;">
                <div class="sidebar-link-content">
                    <span class="sidebar-link-icon">🚪</span>
                    <span>Log Out</span>
                </div>
            </a>
        </li>
    </ul>

    <div class="sidebar-footer">
        <span>Global Connect CMS</span>
        <span style="color: var(--admin-gold);">Core PHP &amp; MySQL</span>
    </div>
</aside>
