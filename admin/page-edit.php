<?php
/**
 * Global Connect - Edit CMS Page
 */
$adminPageTitle = 'Edit Page | Global Connect CMS';
$adminPageHeading = 'Edit Website Page';

require_once __DIR__ . '/includes/header.php';

$db = get_db();
$id = (int)($_GET['id'] ?? 0);

if ($id <= 0) {
    header('Location: /admin/pages.php');
    exit;
}

$stmt = $db->prepare("SELECT * FROM pages WHERE id = ? LIMIT 1");
$stmt->execute([$id]);
$page = $stmt->fetch();

if (!$page) {
    set_flash('danger', 'Page not found.');
    header('Location: /admin/pages.php');
    exit;
}

$categories = get_all_categories();
$errorMsg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['csrf_token'] ?? '')) {
        $errorMsg = 'Security validation failed. Please try again.';
    } else {
        $title = clean($_POST['title'] ?? '');
        $slug = slugify($_POST['slug'] ?: $title);
        $categoryId = (int)($_POST['category_id'] ?? 1);
        $subtitle = clean($_POST['subtitle'] ?? '');
        $bannerTitle = clean($_POST['banner_title'] ?? $title);
        $bannerSubtitle = clean($_POST['banner_subtitle'] ?? '');
        $summary = clean($_POST['summary'] ?? '');
        $content = $_POST['content'] ?? '';
        $metaTitle = clean($_POST['meta_title'] ?? '');
        $metaDesc = clean($_POST['meta_description'] ?? '');
        $metaKeywords = clean($_POST['meta_keywords'] ?? '');
        $status = in_array($_POST['status'] ?? '', ['published', 'draft']) ? $_POST['status'] : 'published';
        $menuOrder = (int)($_POST['menu_order'] ?? 0);
        $featuredImage = clean($_POST['featured_image_url'] ?? $page['featured_image']);

        // Handle File Upload if provided
        if (!empty($_FILES['featured_image_file']['name'])) {
            $file = $_FILES['featured_image_file'];
            $allowedExts = ['jpg', 'jpeg', 'png', 'svg', 'webp', 'gif'];
            $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

            if (in_array($ext, $allowedExts) && $file['error'] === UPLOAD_ERR_OK) {
                $newFilename = 'page-' . $slug . '-' . time() . '.' . $ext;
                $targetPath = __DIR__ . '/../../uploads/' . $newFilename;
                if (move_uploaded_file($file['tmp_name'], $targetPath)) {
                    $featuredImage = '/uploads/' . $newFilename;

                    // Log in media table
                    $stmtMed = $db->prepare("INSERT INTO media (title, filename, filepath, filetype, filesize, created_at) VALUES (?, ?, ?, ?, ?, CURRENT_TIMESTAMP)");
                    $stmtMed->execute([$title, $newFilename, $featuredImage, $file['type'], $file['size']]);
                }
            }
        }

        if (empty($title)) {
            $errorMsg = 'Page title is required.';
        } else {
            // Check if slug is unique (ignoring this page)
            $checkStmt = $db->prepare("SELECT id FROM pages WHERE slug = ? AND id != ? LIMIT 1");
            $checkStmt->execute([$slug, $id]);
            if ($checkStmt->fetch()) {
                $slug .= '-' . time();
            }

            try {
                $stmt = $db->prepare("
                    UPDATE pages SET 
                        category_id = ?, title = ?, slug = ?, subtitle = ?, 
                        banner_title = ?, banner_subtitle = ?, featured_image = ?, 
                        summary = ?, content = ?, meta_title = ?, meta_description = ?, 
                        meta_keywords = ?, status = ?, menu_order = ?, updated_at = CURRENT_TIMESTAMP
                    WHERE id = ?
                ");
                $stmt->execute([
                    $categoryId, $title, $slug, $subtitle, $bannerTitle, $bannerSubtitle, 
                    $featuredImage, $summary, $content, $metaTitle, $metaDesc, 
                    $metaKeywords, $status, $menuOrder, $id
                ]);

                set_flash('success', "Page '{$title}' updated successfully!");
                header('Location: /admin/pages.php');
                exit;
            } catch (Exception $e) {
                $errorMsg = 'Error updating page: ' . $e->getMessage();
            }
        }
    }
}
?>

<div class="admin-card">
    <div class="admin-card-header">
        <h3>Editing: <?= e($page['title']) ?></h3>
        <div style="display: flex; gap: 10px;">
            <a href="/page.php?slug=<?= e($page['slug']) ?>" target="_blank" class="btn-action btn-view">↗ Preview on Site</a>
            <a href="/admin/pages.php" class="btn-action btn-view">← Back to Pages</a>
        </div>
    </div>

    <div class="admin-card-body">
        <?php if ($errorMsg): ?>
            <div style="background: #fee2e2; color: #b91c1c; padding: 12px 18px; border-radius: 8px; margin-bottom: 20px;">
                ⚠ <?= e($errorMsg) ?>
            </div>
        <?php endif; ?>

        <form action="/admin/page-edit.php?id=<?= $id ?>" method="POST" enctype="multipart/form-data">
            <?= csrf_field() ?>

            <div class="form-row">
                <div class="admin-form-group">
                    <label class="admin-form-label" for="title">Page Title *</label>
                    <input type="text" id="title" name="title" class="admin-form-input" required value="<?= e($page['title']) ?>">
                </div>

                <div class="admin-form-group">
                    <label class="admin-form-label" for="category_id">Category *</label>
                    <select id="category_id" name="category_id" class="admin-form-select">
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?= $cat['id'] ?>" <?= ($page['category_id'] == $cat['id']) ? 'selected' : '' ?>>
                                <?= e($cat['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="form-row">
                <div class="admin-form-group">
                    <label class="admin-form-label" for="slug">URL Slug</label>
                    <input type="text" id="slug" name="slug" class="admin-form-input" value="<?= e($page['slug']) ?>">
                    <small style="color: var(--admin-text-muted); font-size: 12px;">Public URL: <code>/page.php?slug=<?= e($page['slug']) ?></code></small>
                </div>

                <div class="admin-form-group">
                    <label class="admin-form-label" for="subtitle">Subtitle / Tagline</label>
                    <input type="text" id="subtitle" name="subtitle" class="admin-form-input" value="<?= e($page['subtitle']) ?>">
                </div>
            </div>

            <!-- Featured Image -->
            <div class="form-row">
                <div class="admin-form-group">
                    <label class="admin-form-label" for="featured_image_file">Upload New Image</label>
                    <input type="file" id="featured_image_file" name="featured_image_file" class="admin-form-input" accept="image/*">
                </div>

                <div class="admin-form-group">
                    <label class="admin-form-label" for="featured_image_url">Image Path / URL</label>
                    <input type="text" id="featured_image_url" name="featured_image_url" class="admin-form-input" value="<?= e($page['featured_image']) ?>">
                </div>
            </div>

            <?php if (!empty($page['featured_image'])): ?>
                <div style="margin-bottom: 20px;">
                    <small style="display: block; font-weight: 600; margin-bottom: 6px;">Current Featured Image Preview:</small>
                    <img src="<?= e($page['featured_image']) ?>" alt="" style="max-height: 120px; border-radius: 6px; border: 1px solid var(--admin-border);">
                </div>
            <?php endif; ?>

            <div class="admin-form-group">
                <label class="admin-form-label" for="summary">Short Summary / Excerpt</label>
                <textarea id="summary" name="summary" class="admin-form-textarea" rows="2"><?= e($page['summary']) ?></textarea>
            </div>

            <!-- WYSIWYG Editor -->
            <div class="admin-form-group">
                <label class="admin-form-label" for="content">Page Content (WYSIWYG Rich Text Editor) *</label>
                <textarea id="content" name="content" class="rich-editor" rows="14"><?= e($page['content']) ?></textarea>
            </div>

            <!-- SEO & Meta -->
            <div class="form-row" style="margin-top: 30px; border-top: 1px solid var(--admin-border); padding-top: 20px;">
                <div class="admin-form-group">
                    <label class="admin-form-label" for="meta_title">SEO Meta Title</label>
                    <input type="text" id="meta_title" name="meta_title" class="admin-form-input" value="<?= e($page['meta_title']) ?>">
                </div>

                <div class="admin-form-group">
                    <label class="admin-form-label" for="meta_description">SEO Meta Description</label>
                    <input type="text" id="meta_description" name="meta_description" class="admin-form-input" value="<?= e($page['meta_description']) ?>">
                </div>
            </div>

            <div class="form-row">
                <div class="admin-form-group">
                    <label class="admin-form-label" for="status">Publication Status</label>
                    <select id="status" name="status" class="admin-form-select">
                        <option value="published" <?= $page['status'] === 'published' ? 'selected' : '' ?>>Published (Visible on site)</option>
                        <option value="draft" <?= $page['status'] === 'draft' ? 'selected' : '' ?>>Draft (Hidden)</option>
                    </select>
                </div>

                <div class="admin-form-group">
                    <label class="admin-form-label" for="menu_order">Display Menu Order</label>
                    <input type="number" id="menu_order" name="menu_order" class="admin-form-input" value="<?= (int)$page['menu_order'] ?>">
                </div>
            </div>

            <div style="margin-top: 25px; display: flex; gap: 12px;">
                <button type="submit" class="btn-admin-primary" style="padding: 12px 28px;">
                    Save Changes →
                </button>
                <a href="/admin/pages.php" class="btn-action btn-view" style="padding: 12px 20px;">Cancel</a>
            </div>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
