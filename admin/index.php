<?php
/**
 * Global Connect - Admin Dashboard
 */
require_once __DIR__ . '/includes/auth.php';

$db = get_db();

// Metrics
$totalPages = 0;
$tradingPagesCount = 0;
$financePagesCount = 0;
$totalInquiries = 0;
$unreadInquiries = 0;
$totalSlides = 0;
$recentInquiries = [];
$recentPages = [];

try {
    $totalPages = $db->query("SELECT COUNT(*) FROM pages")->fetchColumn();
    $tradingPagesCount = $db->query("SELECT COUNT(*) FROM pages WHERE category_id = (SELECT id FROM categories WHERE slug = 'trading')")->fetchColumn();
    $financePagesCount = $db->query("SELECT COUNT(*) FROM pages WHERE category_id = (SELECT id FROM categories WHERE slug = 'finance')")->fetchColumn();
    $totalInquiries = $db->query("SELECT COUNT(*) FROM contacts")->fetchColumn();
    $unreadInquiries = $db->query("SELECT COUNT(*) FROM contacts WHERE status = 'unread'")->fetchColumn();
    $totalSlides = $db->query("SELECT COUNT(*) FROM hero_slides")->fetchColumn();

    $recentInquiries = $db->query("SELECT * FROM contacts ORDER BY created_at DESC LIMIT 5")->fetchAll();
    $recentPages = $db->query("
        SELECT p.*, c.name as category_name, c.slug as category_slug 
        FROM pages p 
        LEFT JOIN categories c ON p.category_id = c.id 
        ORDER BY p.updated_at DESC, p.id DESC 
        LIMIT 6
    ")->fetchAll();
} catch (Exception $e) {}

$adminPageTitle = 'Dashboard | Global Connect CMS';
$adminPageHeading = 'CMS Dashboard Overview';
require_once __DIR__ . '/includes/header.php';
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
            <h4>Hero Banners</h4>
            <div class="number"><?= $totalSlides ?></div>
        </div>
        <div class="kpi-icon icon-blue">🏙</div>
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
        <h3>Quick Content &amp; Branding Actions</h3>
    </div>
    <div class="admin-card-body" style="display: flex; gap: 15px; flex-wrap: wrap;">
        <a href="<?= admin_url('sliders.php') ?>" class="btn-admin-primary" style="background: #0369a1;">
            <span>🏙</span> Manage Hero Banners
        </a>
        <a href="<?= admin_url('settings.php') ?>" class="btn-admin-primary" style="background: #b45309;">
            <span>🌟</span> Upload Logo &amp; Settings
        </a>
        <a href="<?= admin_url('page-add.php?category=trading') ?>" class="btn-admin-primary" style="background: #0284c7;">
            <span>+</span> Add Trading Page
        </a>
        <a href="<?= admin_url('page-add.php?category=finance') ?>" class="btn-admin-primary" style="background: #16a34a;">
            <span>+</span> Add Finance Page
        </a>
        <a href="<?= admin_url('contacts.php') ?>" class="btn-admin-primary" style="background: #d97706;">
            <span>✉</span> View Inquiries (<?= $unreadInquiries ?>)
        </a>
    </div>
</div>

<div style="display: grid; grid-template-columns: 1.2fr 1fr; gap: 25px;">
    <!-- Recent Pages -->
    <div class="admin-card">
        <div class="admin-card-header">
            <h3>Recent Website Pages</h3>
            <a href="<?= admin_url('pages.php') ?>" style="font-size: 13px; color: var(--admin-primary); font-weight: 600;">View All Pages →</a>
        </div>
        <div class="admin-card-body" style="padding: 0;">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Category</th>
                        <th>Status</th>
                        <th style="text-align: right;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($recentPages)): ?>
                        <tr><td colspan="4" style="text-align: center; color: var(--admin-text-muted); padding: 30px;">No pages found.</td></tr>
                    <?php else: ?>
                        <?php foreach ($recentPages as $p): ?>
                            <tr>
                                <td>
                                    <strong style="color: var(--admin-text-dark);"><?= e($p['title']) ?></strong>
                                    <div style="font-size: 12px; color: var(--admin-text-muted);">/<?= e($p['slug']) ?></div>
                                </td>
                                <td>
                                    <span style="font-size: 12px; background: #f1f5f9; padding: 2px 8px; border-radius: 4px;">
                                        <?= e($p['category_name'] ?: 'General') ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="badge-status badge-<?= e($p['status']) ?>">
                                        <?= ucfirst(e($p['status'])) ?>
                                    </span>
                                </td>
                                <td style="text-align: right;">
                                    <a href="<?= admin_url('page-edit.php?id=' . $p['id']) ?>" class="btn-action btn-edit">Edit</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Recent Inquiries -->
    <div class="admin-card">
        <div class="admin-card-header">
            <h3>Recent Inquiries</h3>
            <a href="<?= admin_url('contacts.php') ?>" style="font-size: 13px; color: var(--admin-primary); font-weight: 600;">View All →</a>
        </div>
        <div class="admin-card-body" style="padding: 0;">
            <?php if (empty($recentInquiries)): ?>
                <div style="padding: 30px; text-align: center; color: var(--admin-text-muted);">
                    No contact inquiries received yet.
                </div>
            <?php else: ?>
                <ul class="activity-feed">
                    <?php foreach ($recentInquiries as $c): ?>
                        <li class="activity-item">
                            <div class="activity-icon" style="background: <?= $c['status'] === 'unread' ? '#fee2e2' : '#e0f2fe' ?>; color: <?= $c['status'] === 'unread' ? '#dc2626' : '#0284c7' ?>;">
                                ✉
                            </div>
                            <div class="activity-content">
                                <div class="activity-title">
                                    <strong><?= e($c['name']) ?></strong> (<?= e($c['email']) ?>)
                                    <?php if ($c['status'] === 'unread'): ?>
                                        <span class="badge-status badge-draft" style="font-size: 10px; padding: 1px 6px;">NEW</span>
                                    <?php endif; ?>
                                </div>
                                <div class="activity-desc" style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 320px;">
                                    <?= e($c['subject'] ?: substr($c['message'], 0, 50)) ?>
                                </div>
                                <div class="activity-time"><?= date('M j, Y g:i A', strtotime($c['created_at'])) ?></div>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
