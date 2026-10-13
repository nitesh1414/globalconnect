<?php
/**
 * Global Connect - Category Management
 */
$adminPageTitle = 'Categories | Global Connect CMS';
$adminPageHeading = 'Page Categories';

require_once __DIR__ . '/includes/header.php';

$db = get_db();
$errorMsg = '';

// Add Category
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add') {
    if (!verify_csrf($_POST['csrf_token'] ?? '')) {
        $errorMsg = 'CSRF validation failed.';
    } else {
        $name = clean($_POST['name'] ?? '');
        $slug = slugify($_POST['slug'] ?: $name);
        $desc = clean($_POST['description'] ?? '');
        $order = (int)($_POST['menu_order'] ?? 0);

        if (!empty($name)) {
            try {
                $stmt = $db->prepare("INSERT INTO categories (name, slug, description, menu_order) VALUES (?, ?, ?, ?)");
                $stmt->execute([$name, $slug, $desc, $order]);
                set_flash('success', "Category '{$name}' created.");
                header('Location: /admin/categories.php');
                exit;
            } catch (Exception $e) {
                $errorMsg = 'Error creating category: ' . $e->getMessage();
            }
        }
    }
}

$categories = $db->query("
    SELECT c.*, COUNT(p.id) as page_count 
    FROM categories c 
    LEFT JOIN pages p ON c.id = p.category_id 
    GROUP BY c.id 
    ORDER BY c.menu_order ASC, c.id ASC
")->fetchAll();
?>

<div style="display: grid; grid-template-columns: 1fr 1.6fr; gap: 30px;">
    <!-- Add Category Form -->
    <div class="admin-card">
        <div class="admin-card-header">
            <h3>Add New Category</h3>
        </div>
        <div class="admin-card-body">
            <?php if ($errorMsg): ?>
                <div style="background: #fee2e2; color: #b91c1c; padding: 10px 14px; border-radius: 6px; font-size: 13px; margin-bottom: 15px;">
                    ⚠ <?= e($errorMsg) ?>
                </div>
            <?php endif; ?>

            <form action="/admin/categories.php" method="POST">
                <?= csrf_field() ?>
                <input type="hidden" name="action" value="add">

                <div class="admin-form-group">
                    <label class="admin-form-label" for="name">Category Name *</label>
                    <input type="text" id="name" name="name" class="admin-form-input" required placeholder="e.g. Energy, Investments">
                </div>

                <div class="admin-form-group">
                    <label class="admin-form-label" for="slug">Slug</label>
                    <input type="text" id="slug" name="slug" class="admin-form-input" placeholder="e.g. energy">
                </div>

                <div class="admin-form-group">
                    <label class="admin-form-label" for="description">Description</label>
                    <textarea id="description" name="description" class="admin-form-textarea" rows="3" placeholder="Category purpose..."></textarea>
                </div>

                <div class="admin-form-group">
                    <label class="admin-form-label" for="menu_order">Display Order</label>
                    <input type="number" id="menu_order" name="menu_order" class="admin-form-input" value="0">
                </div>

                <button type="submit" class="btn-admin-primary" style="width: 100%; justify-content: center;">
                    Create Category →
                </button>
            </form>
        </div>
    </div>

    <!-- Category List -->
    <div class="admin-card">
        <div class="admin-card-header">
            <h3>Existing Categories</h3>
        </div>
        <div class="admin-card-body" style="padding: 0;">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Category</th>
                        <th>Slug</th>
                        <th>Pages Count</th>
                        <th>Order</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($categories as $cat): ?>
                        <tr>
                            <td>
                                <strong><?= e($cat['name']) ?></strong>
                                <?php if (!empty($cat['description'])): ?>
                                    <small style="display: block; color: var(--admin-text-muted);"><?= e($cat['description']) ?></small>
                                <?php endif; ?>
                            </td>
                            <td><code><?= e($cat['slug']) ?></code></td>
                            <td>
                                <a href="/admin/pages.php?category=<?= e($cat['slug']) ?>" class="btn-action btn-edit" style="font-size: 12px;">
                                    <?= $cat['page_count'] ?> Pages
                                </a>
                            </td>
                            <td><?= (int)$cat['menu_order'] ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
