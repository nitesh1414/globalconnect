<?php
/**
 * Global Connect - Add New CMS Page
 */
require_once __DIR__ . '/includes/auth.php';

$db = get_db();
$categories = get_all_categories();

$preselectedCategory = clean($_GET['category'] ?? '');
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

        // Validation
        if (empty($title)) {
            $errors[] = 'Page title is required.';
        }

        // Auto-generate slug if blank
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

        // Check if slug exists
        if (empty($errors)) {
            $chk = $db->prepare("SELECT id FROM pages WHERE slug = ?");
            $chk->execute([$slug]);
            if ($chk->fetch()) {
                $slug = $slug . '-' . time();
            }

            try {
                $stmt = $db->prepare("
                    INSERT INTO pages (
                        category_id, title, slug, subtitle, banner_subtitle, 
                        summary, content, featured_image, icon, meta_title, 
                        meta_description, status, menu_order, created_at, updated_at
                    ) VALUES (
                        ?, ?, ?, ?, ?, 
                        ?, ?, ?, ?, ?, 
                        ?, ?, ?, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP
                    )
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
                    $menuOrder
                ]);

                $newPageId = $db->lastInsertId();
                set_flash('success', "Page '{$title}' created successfully!");
                header('Location: ' . admin_url('page-edit.php?id=' . $newPageId));
                exit;
            } catch (Exception $e) {
                $errors[] = 'Database error: ' . $e->getMessage();
            }
        }
    }
}

$adminPageTitle = 'Add New Page | Global Connect CMS';
$adminPageHeading = 'Create New Content Page';
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

<form action="<?= admin_url('page-add.php') ?>" method="POST" enctype="multipart/form-data">
    <?= csrf_field() ?>

    <div style="display: grid; grid-template-columns: 2.3fr 1fr; gap: 25px;">
        <!-- Left Main Column -->
        <div>
            <div class="admin-card">
                <div class="admin-card-header">
                    <h3>Page Content &amp; Details</h3>
                </div>
                <div class="admin-card-body">
                    <div class="admin-form-group">
                        <label class="admin-form-label" for="title">Page Title <span style="color: red;">*</span></label>
                        <input type="text" id="title" name="title" class="admin-form-input" required placeholder="e.g. Copper Cathodes" value="<?= e($_POST['title'] ?? '') ?>">
                    </div>

                    <div class="admin-form-group">
                        <label class="admin-form-label" for="slug">URL Slug</label>
                        <input type="text" id="slug" name="slug" class="admin-form-input" placeholder="copper-cathodes (leave blank to auto-generate)" value="<?= e($_POST['slug'] ?? '') ?>">
                        <small style="color: #64748b; font-size: 12px; margin-top: 4px; display: block;">Page will be accessible at: <code>/page.php?slug=&lt;slug&gt;</code></small>
                    </div>

                    <div class="admin-form-group">
                        <label class="admin-form-label" for="subtitle">Subtitle / Highlight Tagline</label>
                        <input type="text" id="subtitle" name="subtitle" class="admin-form-input" placeholder="e.g. Grade A 99.99% Electrolytic Copper Cathodes" value="<?= e($_POST['subtitle'] ?? '') ?>">
                    </div>

                    <div class="admin-form-group">
                        <label class="admin-form-label" for="banner_subtitle">Top Banner Subtitle</label>
                        <input type="text" id="banner_subtitle" name="banner_subtitle" class="admin-form-input" placeholder="e.g. Global Commodity Sourcing & Supply" value="<?= e($_POST['banner_subtitle'] ?? '') ?>">
                    </div>

                    <div class="admin-form-group">
                        <label class="admin-form-label" for="summary">Short Summary (Used in cards &amp; overview lists)</label>
                        <textarea id="summary" name="summary" class="admin-form-input" rows="3" placeholder="Brief summary of this product or service..."><?= e($_POST['summary'] ?? '') ?></textarea>
                    </div>

                    <div class="admin-form-group">
                        <label class="admin-form-label" for="content">Full Page Content (WYSIWYG Rich Text Editor)</label>
                        <textarea id="content" name="content" class="rich-editor-target" rows="18"><?= e($_POST['content'] ?? '<h2>Overview</h2><p>Provide detailed specifications, capabilities, and transaction terms here.</p><h3>Key Specifications</h3><ul><li>Purity / Standard: High Grade</li><li>Origin & Logistics: CIF / FOB Global Ports</li><li>Inspection: SGS / Alex Stewart / Bureau Veritas</li></ul>') ?></textarea>
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
                        <input type="text" id="meta_title" name="meta_title" class="admin-form-input" placeholder="Default: Page Title | Global Connect" value="<?= e($_POST['meta_title'] ?? '') ?>">
                    </div>

                    <div class="admin-form-group">
                        <label class="admin-form-label" for="meta_description">Meta Description</label>
                        <textarea id="meta_description" name="meta_description" class="admin-form-input" rows="2" placeholder="Search engine description snippet..."><?= e($_POST['meta_description'] ?? '') ?></textarea>
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
                            <option value="published" <?= ($_POST['status'] ?? 'published') === 'published' ? 'selected' : '' ?>>Published</option>
                            <option value="draft" <?= ($_POST['status'] ?? '') === 'draft' ? 'selected' : '' ?>>Draft</option>
                        </select>
                    </div>

                    <div class="admin-form-group">
                        <label class="admin-form-label" for="category_id">Parent Category <span style="color: red;">*</span></label>
                        <select name="category_id" id="category_id" class="admin-form-select" required>
                            <option value="">-- Select Category --</option>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?= $cat['id'] ?>" <?= ((string)$cat['id'] === (string)($_POST['category_id'] ?? '') || $cat['slug'] === $preselectedCategory) ? 'selected' : '' ?>>
                                    <?= e($cat['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="admin-form-group">
                        <label class="admin-form-label" for="menu_order">Display Sort Order</label>
                        <input type="number" id="menu_order" name="menu_order" class="admin-form-input" value="<?= e($_POST['menu_order'] ?? '0') ?>">
                    </div>

                    <div style="display: flex; gap: 10px; margin-top: 20px;">
                        <button type="submit" class="btn-admin-primary" style="flex: 1; justify-content: center;">
                            💾 Save &amp; Publish Page
                        </button>
                    </div>
                    <div style="margin-top: 10px; text-align: center;">
                        <a href="<?= admin_url('pages.php') ?>" class="btn-action btn-view" style="display: block; text-align: center;">Cancel</a>
                    </div>
                </div>
            </div>

            <!-- Featured Image -->
            <div class="admin-card">
                <div class="admin-card-header">
                    <h3>Featured Image &amp; Icon</h3>
                </div>
                <div class="admin-card-body">
                    <div class="admin-form-group">
                        <label class="admin-form-label" for="featured_image_file">Upload Featured Image</label>
                        <input type="file" id="featured_image_file" name="featured_image_file" class="admin-form-input" accept="image/*">
                        <small style="color: #64748b; font-size: 12px; margin-top: 4px; display: block;">Supports JPG, PNG, WebP, SVG (Max 8MB)</small>
                    </div>

                    <div class="admin-form-group">
                        <label class="admin-form-label" for="featured_image">Or Existing Image Filename</label>
                        <input type="text" id="featured_image" name="featured_image" class="admin-form-input" placeholder="e.g. minerals-metals.jpg" value="<?= e($_POST['featured_image'] ?? '') ?>">
                    </div>

                    <div class="admin-form-group">
                        <label class="admin-form-label" for="icon">Icon / Emoji (Optional)</label>
                        <input type="text" id="icon" name="icon" class="admin-form-input" placeholder="e.g. ⚡, 🛡, 💰" value="<?= e($_POST['icon'] ?? '') ?>">
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
