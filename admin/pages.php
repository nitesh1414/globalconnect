<?php
/**
 * Global Connect - Pages Management
 */
require_once __DIR__ . '/includes/auth.php';

$db = get_db();

$filterCategory = clean($_GET['category'] ?? '');
$search = clean($_GET['q'] ?? '');

$sql = "
    SELECT p.*, c.name as category_name, c.slug as category_slug 
    FROM pages p 
    LEFT JOIN categories c ON p.category_id = c.id 
    WHERE 1=1
";
$params = [];

if (!empty($filterCategory)) {
    $sql .= " AND c.slug = ?";
    $params[] = $filterCategory;
}

if (!empty($search)) {
    $sql .= " AND (p.title LIKE ? OR p.slug LIKE ? OR p.summary LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

$sql .= " ORDER BY c.menu_order ASC, p.menu_order ASC, p.id ASC";

$pages = [];
try {
    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    $pages = $stmt->fetchAll();
} catch (Exception $e) {}

$categories = get_all_categories();

$adminPageTitle = 'Pages Manager | Global Connect CMS';
$adminPageHeading = 'CMS Page Management';
require_once __DIR__ . '/includes/header.php';
?>

<div class="admin-card">
    <div class="admin-card-header" style="flex-wrap: wrap; gap: 15px;">
        <div style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
            <a href="<?= admin_url('pages.php') ?>" class="btn-action <?= empty($filterCategory) ? 'btn-edit' : 'btn-view' ?>">All (<?= count($pages) ?>)</a>
            <?php foreach ($categories as $cat): ?>
                <a href="<?= admin_url('pages.php?category=' . urlencode($cat['slug'])) ?>" class="btn-action <?= $filterCategory === $cat['slug'] ? 'btn-edit' : 'btn-view' ?>">
                    <?= e($cat['name']) ?>
                </a>
            <?php endforeach; ?>
        </div>

        <div style="display: flex; gap: 12px; align-items: center;">
            <form action="<?= admin_url('pages.php') ?>" method="GET" style="display: flex; gap: 8px;">
                <?php if (!empty($filterCategory)): ?>
                    <input type="hidden" name="category" value="<?= e($filterCategory) ?>">
                <?php endif; ?>
                <input type="text" name="q" placeholder="Search pages..." value="<?= e($search) ?>" class="admin-form-input" style="padding: 6px 12px; width: 180px;">
                <button type="submit" class="btn-admin-primary" style="padding: 6px 14px; font-size: 13px;">Search</button>
            </form>

            <a href="<?= admin_url('page-add.php' . (!empty($filterCategory) ? '?category=' . urlencode($filterCategory) : '')) ?>" class="btn-admin-primary">
                <span>+</span> Add New Page
            </a>
        </div>
    </div>

    <div class="admin-card-body" style="padding: 0;">
        <table class="admin-table">
            <thead>
                <tr>
                    <th style="width: 50px;">Img</th>
                    <th>Title &amp; Slug</th>
                    <th>Category</th>
                    <th>Status</th>
                    <th>Order</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($pages)): ?>
                    <?php foreach ($pages as $p): ?>
                        <tr>
                            <td>
                                <?php if (!empty($p['featured_image'])): ?>
                                    <img src="<?= image_url($p['featured_image']) ?>" alt="" style="width: 44px; height: 32px; object-fit: cover; border-radius: 4px; border: 1px solid var(--admin-border);">
                                <?php else: ?>
                                    <div style="width: 44px; height: 32px; background: #e2e8f0; border-radius: 4px; display: flex; align-items: center; justify-content: center; font-size: 11px; color: #64748b;">N/A</div>
                                <?php endif; ?>
                            </td>
                            <td>
                                <strong><?= e($p['title']) ?></strong>
                                <?php if ($p['is_system']): ?>
                                    <span style="font-size: 11px; background: #e0f2fe; color: #0369a1; padding: 2px 6px; border-radius: 4px; margin-left: 6px;">System</span>
                                <?php endif; ?>
                                <small style="display: block; color: var(--admin-text-muted); font-family: monospace;">/page.php?slug=<?= e($p['slug']) ?></small>
                            </td>
                            <td>
                                <span style="font-weight: 600; color: var(--admin-primary);">
                                    <?= e($p['category_name'] ?: 'General') ?>
                                </span>
                            </td>
                            <td>
                                <span class="status-badge badge-<?= $p['status'] === 'published' ? 'published' : 'draft' ?>">
                                    <?= ucfirst($p['status']) ?>
                                </span>
                            </td>
                            <td><?= (int)$p['menu_order'] ?></td>
                            <td>
                                <div class="action-buttons">
                                    <a href="<?= admin_url('page-edit.php?id=' . $p['id']) ?>" class="btn-action btn-edit">Edit</a>
                                    <a href="<?= url('page.php?slug=' . urlencode($p['slug'])) ?>" target="_blank" class="btn-action btn-view">View</a>
                                    <?php if (!$p['is_system']): ?>
                                        <a href="<?= admin_url('page-delete.php?id=' . $p['id']) ?>" class="btn-action btn-delete" onclick="return confirm('Are you sure you want to delete &quot;<?= e($p['title']) ?>&quot;? This cannot be undone.');">Delete</a>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" style="text-align: center; padding: 30px; color: var(--admin-text-muted);">
                            No pages found matching criteria.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
