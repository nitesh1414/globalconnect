<?php
/**
 * Global Connect - Add New Hero Banner / Slide
 */
require_once __DIR__ . '/includes/auth.php';

$db = get_db();
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['csrf_token'] ?? '')) {
        $errors[] = 'CSRF validation failed. Please submit the form again.';
    } else {
        $title = clean($_POST['title'] ?? '');
        $subtitle = clean($_POST['subtitle'] ?? '');
        $badgeText = clean($_POST['badge_text'] ?? '');
        $imagePath = clean($_POST['image_path'] ?? '');
        $buttonText = clean($_POST['button_text'] ?? '');
        $buttonLink = clean($_POST['button_link'] ?? '');
        $secButtonText = clean($_POST['secondary_button_text'] ?? '');
        $secButtonLink = clean($_POST['secondary_button_link'] ?? '');
        $showTextOverlay = isset($_POST['show_text_overlay']) && $_POST['show_text_overlay'] === '1' ? 1 : 0;
        $status = in_array($_POST['status'] ?? '', ['active', 'inactive']) ? $_POST['status'] : 'active';
        $sortOrder = (int)($_POST['sort_order'] ?? 1);

        // Handle File Upload if provided
        if (isset($_FILES['banner_file']) && $_FILES['banner_file']['error'] === UPLOAD_ERR_OK) {
            $uploaded = upload_image($_FILES['banner_file']);
            if ($uploaded) {
                $imagePath = $uploaded;
            } else {
                $errors[] = 'Image upload failed. Allowed types: JPG, PNG, WebP, SVG, GIF (Max 8MB).';
            }
        }

        // Validate image path
        if (empty($imagePath)) {
            $errors[] = 'Please upload a banner image or specify an image path.';
        }

        if (empty($errors)) {
            try {
                $stmt = $db->prepare("
                    INSERT INTO hero_slides (
                        title, subtitle, badge_text, image_path, button_text, 
                        button_link, secondary_button_text, secondary_button_link, 
                        show_text_overlay, status, sort_order, created_at, updated_at
                    ) VALUES (
                        ?, ?, ?, ?, ?, 
                        ?, ?, ?, 
                        ?, ?, ?, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP
                    )
                ");

                $stmt->execute([
                    $title,
                    $subtitle,
                    $badgeText,
                    $imagePath,
                    $buttonText,
                    $buttonLink,
                    $secButtonText,
                    $secButtonLink,
                    $showTextOverlay,
                    $status,
                    $sortOrder
                ]);

                $newId = $db->lastInsertId();
                set_flash('success', 'Hero slide added successfully!');
                header('Location: ' . admin_url('sliders.php'));
                exit;
            } catch (Exception $e) {
                $errors[] = 'Database error: ' . $e->getMessage();
            }
        }
    }
}

$adminPageTitle = 'Add Hero Banner | Global Connect CMS';
$adminPageHeading = 'Add New Hero Banner';
require_once __DIR__ . '/includes/header.php';
?>

<div style="margin-bottom: 20px;">
    <a href="<?= admin_url('sliders.php') ?>" style="color: var(--admin-primary); text-decoration: none; font-size: 14px; display: inline-flex; align-items: center; gap: 5px;">
        ← Back to Hero Sliders
    </a>
</div>

<?php if (!empty($errors)): ?>
    <div style="background: #fee2e2; border-left: 4px solid #ef4444; color: #991b1b; padding: 12px 16px; border-radius: 6px; margin-bottom: 20px;">
        <strong>Please correct the following:</strong>
        <ul style="margin: 6px 0 0; padding-left: 20px;">
            <?php foreach ($errors as $err): ?>
                <li><?= e($err) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<form action="<?= admin_url('slider-add.php') ?>" method="POST" enctype="multipart/form-data">
    <?= csrf_field() ?>

    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 25px;">
        <!-- Left Column: Image & Display Options -->
        <div style="display: flex; flex-direction: column; gap: 20px;">
            <!-- Card 1: Banner Image & Display Mode -->
            <div class="admin-card">
                <div class="admin-card-header">
                    <h3 class="admin-card-title">1. Banner Image &amp; Mode</h3>
                </div>
                <div class="admin-card-body">
                    <div class="admin-form-group">
                        <label class="admin-form-label" for="banner_file">Upload Banner Image *</label>
                        <input type="file" id="banner_file" name="banner_file" class="admin-form-input" accept="image/*" onchange="previewBannerImage(this)">
                        <small style="color: var(--admin-text-muted); font-size: 12px; margin-top: 4px; display: block;">
                            Recommended resolution: 1500x700 or 1920x800 px (PNG, JPG, or WebP).
                        </small>
                    </div>

                    <div class="admin-form-group">
                        <label class="admin-form-label" for="image_path">Or Image Filename / Path in Assets</label>
                        <input type="text" id="image_path" name="image_path" class="admin-form-input" placeholder="e.g. assets/images/global-connect-banner-1.png" value="<?= e($_POST['image_path'] ?? '') ?>">
                        <small style="color: var(--admin-text-muted); font-size: 12px;">Existing images: <code>assets/images/global-connect-banner-1.png</code>, <code>assets/images/banner-hero-1.png</code></small>
                    </div>

                    <!-- Live Image Preview Box -->
                    <div id="imagePreviewContainer" style="margin-top: 15px; background: #020b14; border: 1px solid var(--admin-border); border-radius: 8px; padding: 12px; text-align: center; min-height: 120px; display: flex; align-items: center; justify-content: center;">
                        <img id="imagePreview" src="<?= !empty($_POST['image_path']) ? image_url($_POST['image_path']) : '' ?>" alt="Preview" style="max-width: 100%; max-height: 220px; object-fit: contain; <?= empty($_POST['image_path']) ? 'display: none;' : '' ?>">
                        <span id="imagePreviewPlaceholder" style="color: #64748b; font-size: 13px; <?= !empty($_POST['image_path']) ? 'display: none;' : '' ?>">Image preview will appear here</span>
                    </div>

                    <hr style="border: 0; border-top: 1px solid var(--admin-border); margin: 20px 0;">

                    <div class="admin-form-group">
                        <label class="admin-form-label" style="font-size: 14px; font-weight: 700;">Display Mode</label>
                        <div style="display: flex; flex-direction: column; gap: 12px; margin-top: 8px;">
                            <label style="display: flex; align-items: flex-start; gap: 10px; cursor: pointer; padding: 12px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px;">
                                <input type="radio" name="show_text_overlay" value="0" <?= (!isset($_POST['show_text_overlay']) || $_POST['show_text_overlay'] === '0') ? 'checked' : '' ?> onchange="toggleOverlayFields(false)" style="margin-top: 3px;">
                                <div>
                                    <strong style="color: var(--admin-text-dark); display: block;">🖼 Clean Image Mode (Without text on image)</strong>
                                    <span style="font-size: 12px; color: var(--admin-text-muted);">Displays the complete image cleanly without any text overlay. Guarantees 100% full visibility across all desktop, tablet, and mobile devices with zero cropping (ideal for infographic banners).</span>
                                </div>
                            </label>

                            <label style="display: flex; align-items: flex-start; gap: 10px; cursor: pointer; padding: 12px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px;">
                                <input type="radio" name="show_text_overlay" value="1" <?= (isset($_POST['show_text_overlay']) && $_POST['show_text_overlay'] === '1') ? 'checked' : '' ?> onchange="toggleOverlayFields(true)" style="margin-top: 3px;">
                                <div>
                                    <strong style="color: var(--admin-text-dark); display: block;">✍ Text Overlay Mode (With text on image)</strong>
                                    <span style="font-size: 12px; color: var(--admin-text-muted);">Renders title, badge, description, and action CTA buttons overlaid on top of the banner image with a high-contrast dark backdrop.</span>
                                </div>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card 2: Text Overlay Content (Shown when mode is Text Overlay) -->
            <div class="admin-card" id="overlayFieldsCard" style="<?= (isset($_POST['show_text_overlay']) && $_POST['show_text_overlay'] === '1') ? '' : 'display: none;' ?>">
                <div class="admin-card-header">
                    <h3 class="admin-card-title">2. Text Overlay Content</h3>
                    <span style="font-size: 12px; color: var(--admin-text-muted);">Displayed on top of banner</span>
                </div>
                <div class="admin-card-body">
                    <div class="admin-form-group">
                        <label class="admin-form-label" for="badge_text">Badge / Top Pill Tag</label>
                        <input type="text" id="badge_text" name="badge_text" class="admin-form-input" placeholder="e.g. Singapore • India • Global Network" value="<?= e($_POST['badge_text'] ?? 'Singapore • India • Global Network') ?>">
                    </div>

                    <div class="admin-form-group">
                        <label class="admin-form-label" for="title">Headline / Main Title</label>
                        <input type="text" id="title" name="title" class="admin-form-input" placeholder="e.g. Your Strategic Partner in Sourcing & Supply Chain" value="<?= e($_POST['title'] ?? '') ?>">
                    </div>

                    <div class="admin-form-group">
                        <label class="admin-form-label" for="subtitle">Subtitle / Lead Description</label>
                        <textarea id="subtitle" name="subtitle" class="admin-form-textarea" rows="3" placeholder="Enter paragraph description..."><?= e($_POST['subtitle'] ?? '') ?></textarea>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                        <div class="admin-form-group">
                            <label class="admin-form-label" for="button_text">Primary Button Text</label>
                            <input type="text" id="button_text" name="button_text" class="admin-form-input" placeholder="e.g. Explore Trading" value="<?= e($_POST['button_text'] ?? 'Explore Trading') ?>">
                        </div>
                        <div class="admin-form-group">
                            <label class="admin-form-label" for="button_link">Primary Button Link</label>
                            <input type="text" id="button_link" name="button_link" class="admin-form-input" placeholder="e.g. trading.php" value="<?= e($_POST['button_link'] ?? 'trading.php') ?>">
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                        <div class="admin-form-group">
                            <label class="admin-form-label" for="secondary_button_text">Secondary Button Text</label>
                            <input type="text" id="secondary_button_text" name="secondary_button_text" class="admin-form-input" placeholder="e.g. Trade Finance" value="<?= e($_POST['secondary_button_text'] ?? 'Trade Finance') ?>">
                        </div>
                        <div class="admin-form-group">
                            <label class="admin-form-label" for="secondary_button_link">Secondary Button Link</label>
                            <input type="text" id="secondary_button_link" name="secondary_button_link" class="admin-form-input" placeholder="e.g. finance.php" value="<?= e($_POST['secondary_button_link'] ?? 'finance.php') ?>">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column: Settings & Publish -->
        <div style="display: flex; flex-direction: column; gap: 20px;">
            <div class="admin-card">
                <div class="admin-card-header">
                    <h3 class="admin-card-title">Publish Settings</h3>
                </div>
                <div class="admin-card-body">
                    <div class="admin-form-group">
                        <label class="admin-form-label" for="status">Display Status</label>
                        <select id="status" name="status" class="admin-form-select">
                            <option value="active" <?= (!isset($_POST['status']) || $_POST['status'] === 'active') ? 'selected' : '' ?>>Active (Visible on Homepage)</option>
                            <option value="inactive" <?= (isset($_POST['status']) && $_POST['status'] === 'inactive') ? 'selected' : '' ?>>Inactive (Hidden)</option>
                        </select>
                    </div>

                    <div class="admin-form-group">
                        <label class="admin-form-label" for="sort_order">Sort Order</label>
                        <input type="number" id="sort_order" name="sort_order" class="admin-form-input" value="<?= e($_POST['sort_order'] ?? '1') ?>" min="0" step="1">
                        <small style="color: var(--admin-text-muted); font-size: 12px;">Lower numbers appear first (e.g. 1, 2, 3).</small>
                    </div>

                    <button type="submit" class="btn-admin-primary" style="width: 100%; justify-content: center; padding: 12px; margin-top: 10px;">
                        Save Hero Slide →
                    </button>
                </div>
            </div>

            <div class="admin-card" style="background: #f8fafc;">
                <div class="admin-card-body" style="font-size: 13px; color: var(--admin-text-muted); line-height: 1.6;">
                    <strong style="color: var(--admin-primary); display: block; margin-bottom: 6px;">💡 Need Help?</strong>
                    Upload wide banners with high resolution. When you choose <strong>Clean Image Mode</strong>, all text embedded on the image (country details, registration numbers) is preserved and fully visible on all mobile and desktop screens.
                </div>
            </div>
        </div>
    </div>
</form>

<script>
function toggleOverlayFields(show) {
    const card = document.getElementById('overlayFieldsCard');
    if (card) {
        card.style.display = show ? 'block' : 'none';
    }
}

function previewBannerImage(input) {
    const preview = document.getElementById('imagePreview');
    const placeholder = document.getElementById('imagePreviewPlaceholder');
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.src = e.target.result;
            preview.style.display = 'block';
            if (placeholder) placeholder.style.display = 'none';
        }
        reader.readAsDataURL(input.files[0]);
    }
}

// Update preview on manual path change
document.getElementById('image_path').addEventListener('input', function() {
    const preview = document.getElementById('imagePreview');
    const placeholder = document.getElementById('imagePreviewPlaceholder');
    if (this.value.trim().length > 0) {
        let path = this.value.trim();
        if (!path.startsWith('http') && !path.startsWith('/')) {
            path = '/' + path;
        }
        preview.src = path;
        preview.style.display = 'block';
        if (placeholder) placeholder.style.display = 'none';
    }
});
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
