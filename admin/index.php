<?php
/**
 * Global Connect - Admin Dashboard
 */
$adminPageTitle = 'Dashboard | Global Connect CMS';
$adminPageHeading = 'CMS Dashboard Overview';

require_once __DIR__ . '/includes/header.php';

$db = get_db();

// Metrics
$totalPages = $db->query("SELECT COUNT(*) FROM pages")->fetchColumn();
$tradingPagesCount = $db->query("SELECT COUNT(*) FROM pages WHERE category_id = (SELECT id FROM categories WHERE slug = 'trading')")->fetchColumn();
$financePagesCount = $db->query("SELECT COUNT(*) FROM pages WHERE category_id = (SELECT id FROM categories WHERE slug = 'finance')")->fetchColumn();
$totalInquiries = $db->query("SELECT COUNT(*) FROM contacts")->fetchColumn();
$unreadInquiries = $db->query("SELECT COUNT(*) FROM contacts WHERE status = 'unread'")->fetchColumn();

// Recent inquiries
$recentInquiries = $db->query("SELECT * FROM contacts ORDER BY created_at DESC LIMIT 5")->fetchAll();

// Recent pages
$recentPages = $db->query("
    SELECT p.*, c.name as category_name, c.slug as category_slug 
    FROM pages p 
    LEFT JOIN categories c ON p.category_id = c.id 
    ORDER BY p.updated_at DESC, p.id DESC 
    LIMIT 6
")->fetchAll();
?>

<!-- KPI Overview Grid -->
<div class="kpi-grid">
    <div class="kpi-card">
        <div class="kpi-info">
            <h4>Total CMS Pages</h4>
            <div class="number"><?= $totalPages ?></div>
        </div>
        <div class="kpi-icon icon-blue">📄</div>
    </div>

    <div class="kpi-card">
        <div class="kpi-info">
            <h4>Trading Pages</h4>
            <div class="number"><?= $tradingPagesCount ?></div>
        </div>
        <div class="kpi-icon icon-gold">📦</div>
    </div>

    <div class="kpi-card">
        <div class="kpi-info">
            <h4>Finance Pages</h4>
            <div class="number"><?= $financePagesCount ?></div>
        </div>
        <div class="kpi-icon icon-purple">💳</div>
    </div>

    <div class="kpi-card">
        <div class="kpi-info">
            <h4>Inquiries</h4>
            <div class="number"><?= $totalInquiries ?> <?= $unreadInquiries > 0 ? "<small style='font-size: 14px; color: var(--admin-danger);'>($unreadInquiries new)</small>" : "" ?></div>
        </div>
        <div class="kpi-icon icon-green">✉</div>
    </div>
</div>

<!-- Quick Action Shortcuts -->
<div class="admin-card">
    <div class="admin-card-header">
        <h3>Quick Content Actions</h3>
    </div>
    <div class="admin-card-body" style="display: flex; gap: 15px; flex-wrap: wrap;">
        <a href="/admin/page-add.php?category=trading" class="btn-admin-primary" style="background: #0284c7;">
            <span>+</span> Add New Trading Page
        </a>
        <a href="/admin/page-add.php?category=finance" class="btn-admin-primary" style="background: #16a34a;">
            <span>+</span> Add New Finance Page
        </a>
        <a href="/admin/page-add.php" class="btn-admin-primary" style="background: #0f172a;">
            <span>+</span> Add Custom Page
        </a>
        <a href="/admin/contacts.php" class="btn-admin-primary" style="background: #d97706;">
            <span>✉</span> View Contact Inquiries (<?= $unreadInquiries ?>)
        </a>
        <a href="/admin/settings.php" class="btn-admin-primary" style="background: #475569;">
            <span>⚙</span> Update Site Settings
        </a>
    </div>
</div>

<div style="display: grid; grid-template-columns: 1.2fr 1fr; gap: 25px;">
    <!-- Recent Pages -->
    <div class="admin-card">
        <div class="admin-card-header">
            <h3>Recent Website Pages</h3>
            <a href="/admin/pages.php" style="font-size: 13px; color: var(--admin-primary); font-weight: 600;">View All Pages →</a>
        </div>
        <div class="admin-card-body" style="padding: 0;">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Page Title</th>
                        <th>Category</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recentPages as $rp): ?>
                        <tr>
                            <td>
                                <strong><?= e($rp['title']) ?></strong>
                                <small style="display: block; color: var(--admin-text-muted);"><?= e($rp['slug']) ?></small>
                            </td>
                            <td>
                                <span style="font-weight: 600; color: var(--admin-primary);">
                                    <?= e($rp['category_name'] ?: 'General') ?>
                                </span>
                            </td>
                            <td>
                                <span class="status-badge badge-<?= $rp['status'] === 'published' ? 'published' : 'draft' ?>">
                                    <?= ucfirst($rp['status']) ?>
                                </span>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <a href="/admin/page-edit.php?id=<?= $rp['id'] ?>" class="btn-action btn-edit">Edit</a>
                                    <a href="/page.php?slug=<?= e($rp['slug']) ?>" target="_blank" class="btn-action btn-view">View</a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Recent Inquiries -->
    <div class="admin-card">
        <div class="admin-card-header">
            <h3>Recent Inquiries</h3>
            <a href="/admin/contacts.php" style="font-size: 13px; color: var(--admin-primary); font-weight: 600;">Inbox (<?= $totalInquiries ?>) →</a>
        </div>
        <div class="admin-card-body" style="padding: 0;">
            <?php if (!empty($recentInquiries)): ?>
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Contact</th>
                            <th>Subject</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recentInquiries as $inq): ?>
                            <tr>
                                <td>
                                    <strong><?= e($inq['name']) ?></strong>
                                    <small style="display: block; color: var(--admin-text-muted);"><?= e($inq['email']) ?></small>
                                </td>
                                <td>
                                    <span style="font-size: 13px;"><?= e(mb_strimwidth($inq['subject'] ?: $inq['message'], 0, 30, '...')) ?></span>
                                </td>
                                <td>
                                    <span class="status-badge badge-<?= e($inq['status']) ?>">
                                        <?= ucfirst($inq['status']) ?>
                                    </span>
                                </td>
                                <td>
                                    <a href="/admin/contacts.php?id=<?= $inq['id'] ?>" class="btn-action btn-edit">Open</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div style="padding: 30px; text-align: center; color: var(--admin-text-muted);">
                    No inquiries received yet.
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
