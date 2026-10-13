<?php
/**
 * Global Connect - Add New CMS Page
 */
$adminPageTitle = 'Add New Page | Global Connect CMS';
$adminPageHeading = 'Create New Website Page';

require_once __DIR__ . '/includes/header.php';

$db = get_db();
$categories = get_all_categories();

$preselectedCategory = clean($_GET['category'] ?? '');
$defaultCategoryId = 1;
if (!empty($preselectedCategory)) {
    foreach ($categories as $cat) {
        if ($cat['slug'] === $preselectedCategory) {
            $defaultCategoryId = $cat['id'];
            break;
        }
    }
}

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
        $featuredImage = clean($_POST['featured_image_url'] ?? '');

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
            // Check if slug is unique
            $checkStmt = $db->prepare("SELECT id FROM pages WHERE slug = ? LIMIT 1");
            $checkStmt->execute([$slug]);
            if ($checkStmt->fetch()) {
                $slug .= '-' . time();
            }

            try {
                $stmt = $db->prepare("
                    INSERT INTO pages (category_id, title, slug, subtitle, banner_title, banner_subtitle, featured_image, summary, content, meta_title, meta_description, meta_keywords, status, menu_order, is_system, created_at, updated_at)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 0, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)
                ");
                $stmt->execute([
                    $categoryId, $title, $slug, $subtitle, $bannerTitle, $bannerSubtitle, $featuredImage, $summary, $content, $metaTitle, $metaDesc, $metaKeywords, $status, $menuOrder
                ]);

                set_flash('success', "Page '{$title}' created successfully!");
                header('Location: /admin/pages.php');
                exit;
            } catch (Exception $e) {
                $errorMsg = 'Error creating page: ' . $e->getMessage();
            }
        }
    }
}
?>

<div class="admin-card">
    <div class="admin-card-header">
        <h3>Page Details &amp; Content</h3>
        <a href="/admin/pages.php" class="btn-action btn-view">← Back to Pages</a>
    </div>

    <div class="admin-card-body">
        <?php if ($errorMsg): ?>
            <div style="background: #fee2e2; color: #b91c1c; padding: 12px 18px; border-radius: 8px; margin-bottom: 20px;">
                ⚠ <?= e($errorMsg) ?>
            </div>
        <?php endif; ?>

        <form action="/admin/page-add.php" method="POST" enctype="multipart/form-data">
            <?= csrf_field() ?>

            <div class="form-row">
                <div class="admin-form-group">
                    <label class="admin-form-label" for="title">Page Title *</label>
                    <input type="text" id="title" name="title" class="admin-form-input" required placeholder="e.g. Copper Cathodes, Offshore Letter of Credit" value="<?= e($_POST['title'] ?? '') ?>">
                </div>

                <div class="admin-form-group">
                    <label class="admin-form-label" for="category_id">Category *</label>
                    <select id="category_id" name="category_id" class="admin-form-select">
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?= $cat['id'] ?>" <?= ($defaultCategoryId == $cat['id']) ? 'selected' : '' ?>>
                                <?= e($cat['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="form-row">
                <div class="admin-form-group">
                    <label class="admin-form-label" for="slug">URL Slug (Auto-generated)</label>
                    <input type="text" id="slug" name="slug" class="admin-form-input" placeholder="e.g. copper-cathodes" value="<?= e($_POST['slug'] ?? '') ?>">
                    <small style="color: var(--admin-text-muted); font-size: 12px;">Public URL will be: <code>/page.php?slug=&lt;slug&gt;</code></small>
                </div>

                <div class="admin-form-group">
                    <label class="admin-form-label" for="subtitle">Subtitle / Tagline</label>
                    <input type="text" id="subtitle" name="subtitle" class="admin-form-input" placeholder="e.g. Industrial Resource Trading" value="<?= e($_POST['subtitle'] ?? '') ?>">
                </div>
            </div>

            <!-- Image Options -->
            <div class="form-row">
                <div class="admin-form-group">
                    <label class="admin-form-label" for="featured_image_file">Upload Featured Image</label>
                    <input type="file" id="featured_image_file" name="featured_image_file" class="admin-form-input" accept="image/*">
                </div>

                <div class="admin-form-group">
                    <label class="admin-form-label" for="featured_image_url">Or Image Path / URL</label>
                    <input type="text" id="featured_image_url" name="featured_image_url" class="admin-form-input" placeholder="/assets/images/trading-home.jpg" value="<?= e($_POST['featured_image_url'] ?? '') ?>">
                </div>
            </div>

            <div class="admin-form-group">
                <label class="admin-form-label" for="summary">Short Summary / Excerpt</label>
                <textarea id="summary" name="summary" class="admin-form-textarea" rows="2" placeholder="Brief summary of this product or service..."><?= e($_POST['summary'] ?? '') ?></textarea>
            </div>

            <!-- WYSIWYG Editor -->
            <div class="admin-form-group">
                <label class="admin-form-label" for="content">Page Content (WYSIWYG Rich Text Editor) *</label>
                <textarea id="content" name="content" class="rich-editor" rows="12"><?= e($_POST['content'] ?? '') ?></textarea>
                <small style="color: var(--admin-text-muted); font-size: 12px; margin-top: 6px; display: block;">
                    Use the toolbar above to format headings, bullet points, insert images, tables, and toggle raw HTML code.
                </small>
            </div>

            <!-- SEO & Meta -->
            <div class="form-row" style="margin-top: 30px; border-top: 1px solid var(--admin-border); padding-top: 20px;">
                <div class="admin-form-group">
                    <label class="admin-form-label" for="meta_title">SEO Meta Title</label>
                    <input type="text" id="meta_title" name="meta_title" class="admin-form-input" placeholder="Custom page title for search engines" value="<?= e($_POST['meta_title'] ?? '') ?>">
                </div>

                <div class="admin-form-group">
                    <label class="admin-form-label" for="meta_description">SEO Meta Description</label>
                    <input type="text" id="meta_description" name="meta_description" class="admin-form-input" placeholder="Brief search engine snippet" value="<?= e($_POST['meta_description'] ?? '') ?>">
                </div>
            </div>

            <div class="form-row">
                <div class="admin-form-group">
                    <label class="admin-form-label" for="status">Publication Status</label>
                    <select id="status" name="status" class="admin-form-select">
                        <option value="published" selected>Published (Visible on site)</option>
                        <option value="draft">Draft (Hidden)</option>
                    </select>
                </div>

                <div class="admin-form-group">
                    <label class="admin-form-label" for="menu_order">Display Menu Order</label>
                    <input type="number" id="menu_order" name="menu_order" class="admin-form-input" value="0">
                </div>
            </div>

            <div style="margin-top: 25px; display: flex; gap: 12px;">
                <button type="submit" class="btn-admin-primary" style="padding: 12px 28px;">
                    Publish Page →
                </button>
                <a href="/admin/pages.php" class="btn-action btn-view" style="padding: 12px 20px;">Cancel</a>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const titleInput = document.getElementById('title');
    const slugInput = document.getElementById('slug');

    if (titleInput && slugInput) {
        titleInput.addEventListener('input', () => {
            if (!slugInput.dataset.manual) {
                slugInput.value = titleInput.value
                    .toLowerCase()
                    .replace(/[^\w\s-]/g, '')
                    .replace(/[\s_-]+/g, '-')
                    .replace(/^-+|-+$/g, '');
            }
        });

        slugInput.addEventListener('input', () => {
            slugInput.dataset.manual = 'true';
        });
    }
});
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
