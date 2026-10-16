<?php
/**
 * Global Connect - Categories Management
 */
require_once __DIR__ . '/includes/auth.php';

$db = get_db();
$errors = [];

// Handle New Category Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add') {
    if (!verify_csrf($_POST['csrf_token'] ?? '')) {
        $errors[] = 'CSRF validation failed.';
    } else {
        $name = clean($_POST['name'] ?? '');
        $slug = clean($_POST['slug'] ?? '');
        $description = clean($_POST['description'] ?? '');
        $menuOrder = (int)($_POST['menu_order'] ?? 0);

        if (empty($name)) {
            $errors[] = 'Category name is required.';
        }

        if (empty($slug)) {
            $slug = slugify($name);
        } else {
            $slug = slugify($slug);
        }

        if (empty($errors)) {
            try {
                $stmt = $db->prepare("INSERT INTO categories (name, slug, description, menu_order) VALUES (?, ?, ?, ?)");
                $stmt->execute([$name, $slug, $description, $menuOrder]);
                set_flash('success', "Category '{$name}' created successfully.");
                header('Location: ' . admin_url('categories.php'));
                exit;
            } catch (Exception $e) {
                $errors[] = 'Category slug already exists or database error occurred.';
            }
        }
    }
}

$adminPageTitle = 'Categories | Global Connect CMS';
$adminPageHeading = 'CMS Categories';
require_once __DIR__ . '/includes/header.php';

// Fetch all categories with page counts
$categories = [];
try {
    $categories = $db->query("
        SELECT c.*, COUNT(p.id) as pages_count 
        FROM categories c 
        LEFT JOIN pages p ON c.id = p.category_id 
        GROUP BY c.id 
        ORDER BY c.menu_order ASC, c.id ASC
    ")->fetchAll();
} catch (Exception $e) {}
?>

<?php if (!empty($errors)): ?>
    <div style="background: #fee2e2; color: #b91c1c; padding: 15px 20px; border-radius: 8px; margin-bottom: 25px;">
        <ul style="margin: 0; padding-left: 20px;">
            <?php foreach ($errors as $err): ?>
                <li><?= e($err) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<div style="display: grid; grid-template-columns: 1.4fr 1fr; gap: 25px;">
    <!-- Categories List -->
    <div class="admin-card">
        <div class="admin-card-header">
            <h3>Website Content Categories</h3>
        </div>
        <div class="admin-card-body" style="padding: 0;">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Category Name</th>
                        <th>Slug</th>
                        <th>Pages Count</th>
                        <th>Order</th>
                        <th>Actions</th>
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
                                <a href="<?= admin_url('pages.php?category=' . urlencode($cat['slug'])) ?>" style="font-weight: 600; color: var(--admin-primary);">
                                    <?= (int)$cat['pages_count'] ?> pages →
                                </a>
                            </td>
                            <td><?= (int)$cat['menu_order'] ?></td>
                            <td>
                                <a href="<?= admin_url('pages.php?category=' . urlencode($cat['slug'])) ?>" class="btn-action btn-view">View Pages</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Add Category Form -->
    <div class="admin-card">
        <div class="admin-card-header">
            <h3>Add New Category</h3>
        </div>
        <div class="admin-card-body">
            <form action="<?= admin_url('categories.php') ?>" method="POST">
                <?= csrf_field() ?>
                <input type="hidden" name="action" value="add">

                <div class="admin-form-group">
                    <label class="admin-form-label" for="name">Category Name <span style="color: red;">*</span></label>
                    <input type="text" id="name" name="name" class="admin-form-input" required placeholder="e.g. Investments">
                </div>

                <div class="admin-form-group">
                    <label class="admin-form-label" for="slug">Slug</label>
                    <input type="text" id="slug" name="slug" class="admin-form-input" placeholder="e.g. investments (auto-generated if empty)">
                </div>

                <div class="admin-form-group">
                    <label class="admin-form-label" for="description">Description</label>
                    <textarea id="description" name="description" class="admin-form-input" rows="3" placeholder="Category purpose or summary..."></textarea>
                </div>

                <div class="admin-form-group">
                    <label class="admin-form-label" for="menu_order">Display Order</label>
                    <input type="number" id="menu_order" name="menu_order" class="admin-form-input" value="0">
                </div>

                <button type="submit" class="btn-admin-primary" style="width: 100%; justify-content: center; margin-top: 10px;">
                    + Create Category
                </button>
            </form>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
