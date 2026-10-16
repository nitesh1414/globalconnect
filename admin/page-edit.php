<?php
/**
 * Global Connect - Edit CMS Page
 */
require_once __DIR__ . '/includes/auth.php';

$db = get_db();
$id = (int)($_GET['id'] ?? 0);

if (!$id) {
    header('Location: ' . admin_url('pages.php'));
    exit;
}

$stmt = $db->prepare("SELECT * FROM pages WHERE id = ?");
$stmt->execute([$id]);
$page = $stmt->fetch();

if (!$page) {
    set_flash('error', 'Page not found.');
    header('Location: ' . admin_url('pages.php'));
    exit;
}

$categories = get_all_categories();
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['csrf_token'] ?? '')) {
        $errors[] = 'CSRF validation failed. Please submit the form again.';
    } else {
        $title = clean($_POST['title'] ?? '');
        $slug = clean($_POST['slug'] ?? '');
        $categoryId = (int)($_POST['category_id'] ?? 0);
        $subtitle = clean($_POST['subtitle'] ?? '');
        $bannerSubtitle = clean($_POST['banner_subtitle'] ?? '');
        $summary = clean($_POST['summary'] ?? '');
        $content = $_POST['content'] ?? '';
        $featuredImage = clean($_POST['featured_image'] ?? '');
        $icon = clean($_POST['icon'] ?? '');
        $metaTitle = clean($_POST['meta_title'] ?? '');
        $metaDescription = clean($_POST['meta_description'] ?? '');
        $status = in_array($_POST['status'] ?? '', ['published', 'draft']) ? $_POST['status'] : 'published';
        $menuOrder = (int)($_POST['menu_order'] ?? 0);

        if (empty($title)) {
            $errors[] = 'Page title is required.';
        }

        if (empty($slug)) {
            $slug = slugify($title);
        } else {
            $slug = slugify($slug);
        }

        // Handle File Upload if provided
        if (isset($_FILES['featured_image_file']) && $_FILES['featured_image_file']['error'] === UPLOAD_ERR_OK) {
            $uploaded = upload_image($_FILES['featured_image_file']);
            if ($uploaded) {
                $featuredImage = $uploaded;
            } else {
                $errors[] = 'Image upload failed. Allowed types: JPG, PNG, WebP, SVG, GIF (Max 8MB).';
            }
        }

        // Check if slug taken by another page
        if (empty($errors)) {
            $chk = $db->prepare("SELECT id FROM pages WHERE slug = ? AND id != ?");
            $chk->execute([$slug, $id]);
            if ($chk->fetch()) {
                $slug = $slug . '-' . time();
            }

            try {
                $stmt = $db->prepare("
                    UPDATE pages SET 
                        category_id = ?, 
                        title = ?, 
                        slug = ?, 
                        subtitle = ?, 
                        banner_subtitle = ?, 
                        summary = ?, 
                        content = ?, 
                        featured_image = ?, 
                        icon = ?, 
                        meta_title = ?, 
                        meta_description = ?, 
                        status = ?, 
                        menu_order = ?, 
                        updated_at = CURRENT_TIMESTAMP 
                    WHERE id = ?
                ");

                $stmt->execute([
                    $categoryId ?: null,
                    $title,
                    $slug,
                    $subtitle,
                    $bannerSubtitle,
                    $summary,
                    $content,
                    $featuredImage,
                    $icon,
                    $metaTitle,
                    $metaDescription,
                    $status,
                    $menuOrder,
                    $id
                ]);

                set_flash('success', "Page '{$title}' updated successfully!");
                header('Location: ' . admin_url('page-edit.php?id=' . $id));
                exit;
            } catch (Exception $e) {
                $errors[] = 'Database error: ' . $e->getMessage();
            }
        }
    }
}

$adminPageTitle = 'Edit Page | Global Connect CMS';
$adminPageHeading = 'Edit Content Page';
require_once __DIR__ . '/includes/header.php';
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

<form action="<?= admin_url('page-edit.php?id=' . $id) ?>" method="POST" enctype="multipart/form-data">
    <?= csrf_field() ?>

    <div style="display: grid; grid-template-columns: 2.3fr 1fr; gap: 25px;">
        <!-- Left Main Column -->
        <div>
            <div class="admin-card">
                <div class="admin-card-header" style="justify-content: space-between;">
                    <h3>Editing: <?= e($page['title']) ?></h3>
                    <a href="<?= url('page.php?slug=' . urlencode($page['slug'])) ?>" target="_blank" class="btn-action btn-view">
                        ↗ Preview Live Page
                    </a>
                </div>
                <div class="admin-card-body">
                    <div class="admin-form-group">
                        <label class="admin-form-label" for="title">Page Title <span style="color: red;">*</span></label>
                        <input type="text" id="title" name="title" class="admin-form-input" required value="<?= e($page['title']) ?>">
                    </div>

                    <div class="admin-form-group">
                        <label class="admin-form-label" for="slug">URL Slug</label>
                        <input type="text" id="slug" name="slug" class="admin-form-input" value="<?= e($page['slug']) ?>">
                        <small style="color: #64748b; font-size: 12px; margin-top: 4px; display: block;">Live URL: <code>/page.php?slug=<?= e($page['slug']) ?></code></small>
                    </div>

                    <div class="admin-form-group">
                        <label class="admin-form-label" for="subtitle">Subtitle / Highlight Tagline</label>
                        <input type="text" id="subtitle" name="subtitle" class="admin-form-input" value="<?= e($page['subtitle']) ?>">
                    </div>

                    <div class="admin-form-group">
                        <label class="admin-form-label" for="banner_subtitle">Top Banner Subtitle</label>
                        <input type="text" id="banner_subtitle" name="banner_subtitle" class="admin-form-input" value="<?= e($page['banner_subtitle']) ?>">
                    </div>

                    <div class="admin-form-group">
                        <label class="admin-form-label" for="summary">Short Summary (Used in cards &amp; overview lists)</label>
                        <textarea id="summary" name="summary" class="admin-form-input" rows="3"><?= e($page['summary']) ?></textarea>
                    </div>

                    <div class="admin-form-group">
                        <label class="admin-form-label" for="content">Full Page Content (WYSIWYG Rich Text Editor)</label>
                        <textarea id="content" name="content" class="rich-editor-target" rows="22"><?= e($page['content']) ?></textarea>
                    </div>
                </div>
            </div>

            <!-- SEO Settings -->
            <div class="admin-card">
                <div class="admin-card-header">
                    <h3>Search Engine Optimization (SEO)</h3>
                </div>
                <div class="admin-card-body">
                    <div class="admin-form-group">
                        <label class="admin-form-label" for="meta_title">Meta Title</label>
                        <input type="text" id="meta_title" name="meta_title" class="admin-form-input" value="<?= e($page['meta_title']) ?>">
                    </div>

                    <div class="admin-form-group">
                        <label class="admin-form-label" for="meta_description">Meta Description</label>
                        <textarea id="meta_description" name="meta_description" class="admin-form-input" rows="2"><?= e($page['meta_description']) ?></textarea>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Settings Column -->
        <div>
            <!-- Publish Controls -->
            <div class="admin-card">
                <div class="admin-card-header">
                    <h3>Publish Settings</h3>
                </div>
                <div class="admin-card-body">
                    <div class="admin-form-group">
                        <label class="admin-form-label" for="status">Publication Status</label>
                        <select name="status" id="status" class="admin-form-select">
                            <option value="published" <?= $page['status'] === 'published' ? 'selected' : '' ?>>Published</option>
                            <option value="draft" <?= $page['status'] === 'draft' ? 'selected' : '' ?>>Draft</option>
                        </select>
                    </div>

                    <div class="admin-form-group">
                        <label class="admin-form-label" for="category_id">Parent Category <span style="color: red;">*</span></label>
                        <select name="category_id" id="category_id" class="admin-form-select" required>
                            <option value="">-- Select Category --</option>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?= $cat['id'] ?>" <?= (int)$cat['id'] === (int)$page['category_id'] ? 'selected' : '' ?>>
                                    <?= e($cat['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="admin-form-group">
                        <label class="admin-form-label" for="menu_order">Display Sort Order</label>
                        <input type="number" id="menu_order" name="menu_order" class="admin-form-input" value="<?= (int)$page['menu_order'] ?>">
                    </div>

                    <div style="display: flex; gap: 10px; margin-top: 20px;">
                        <button type="submit" class="btn-admin-primary" style="flex: 1; justify-content: center;">
                            💾 Update Page
                        </button>
                    </div>

                    <div style="margin-top: 10px; text-align: center;">
                        <a href="<?= admin_url('pages.php') ?>" class="btn-action btn-view" style="display: block; text-align: center;">Cancel</a>
                    </div>

                    <?php if (!$page['is_system']): ?>
                        <div style="margin-top: 15px; border-top: 1px solid var(--admin-border); padding-top: 12px; text-align: center;">
                            <a href="<?= admin_url('page-delete.php?id=' . $page['id']) ?>" class="btn-action btn-delete" style="display: inline-block; width: 100%; text-align: center;" onclick="return confirm('Delete this page permanently?');">Delete Page</a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Featured Image -->
            <div class="admin-card">
                <div class="admin-card-header">
                    <h3>Featured Image &amp; Icon</h3>
                </div>
                <div class="admin-card-body">
                    <?php if (!empty($page['featured_image'])): ?>
                        <div style="margin-bottom: 15px; text-align: center;">
                            <img src="<?= image_url($page['featured_image']) ?>" alt="Featured preview" style="max-width: 100%; height: 120px; object-fit: cover; border-radius: 6px; border: 1px solid var(--admin-border);">
                            <small style="display: block; color: var(--admin-text-muted); margin-top: 4px;"><?= e($page['featured_image']) ?></small>
                        </div>
                    <?php endif; ?>

                    <div class="admin-form-group">
                        <label class="admin-form-label" for="featured_image_file">Replace / Upload Image</label>
                        <input type="file" id="featured_image_file" name="featured_image_file" class="admin-form-input" accept="image/*">
                    </div>

                    <div class="admin-form-group">
                        <label class="admin-form-label" for="featured_image">Image Filename / URL</label>
                        <input type="text" id="featured_image" name="featured_image" class="admin-form-input" value="<?= e($page['featured_image']) ?>">
                    </div>

                    <div class="admin-form-group">
                        <label class="admin-form-label" for="icon">Icon / Emoji</label>
                        <input type="text" id="icon" name="icon" class="admin-form-input" value="<?= e($page['icon']) ?>">
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
