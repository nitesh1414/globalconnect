<?php
/**
 * Global Connect - Hero Sliders & Banners Management
 */
require_once __DIR__ . '/includes/auth.php';

$db = get_db();

// Handle quick status toggle
if (isset($_GET['toggle_id']) && is_numeric($_GET['toggle_id'])) {
    $toggleId = (int)$_GET['toggle_id'];
    try {
        $stmt = $db->prepare("UPDATE hero_slides SET status = CASE WHEN status = 'active' THEN 'inactive' ELSE 'active' END, updated_at = CURRENT_TIMESTAMP WHERE id = ?");
        $stmt->execute([$toggleId]);
        set_flash('success', 'Slide status updated successfully!');
    } catch (Exception $e) {
        set_flash('error', 'Failed to update slide status.');
    }
    header('Location: ' . admin_url('sliders.php'));
    exit;
}

$slides = [];
try {
    $stmt = $db->query("SELECT * FROM hero_slides ORDER BY sort_order ASC, id ASC");
    $slides = $stmt->fetchAll();
} catch (Exception $e) {
    $slides = [];
}

$adminPageTitle = 'Hero Banners & Sliders | Global Connect CMS';
$adminPageHeading = 'Hero Section Sliders & Banners';
require_once __DIR__ . '/includes/header.php';
?>

<div style="margin-bottom: 25px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px;">
    <div>
        <p style="color: var(--admin-text-muted); font-size: 14px; margin: 0;">
            Manage homepage hero banners. Choose between <strong>Clean Image Mode</strong> (full infographic visible across all devices) or <strong>Text Overlay Mode</strong> (with customizable headlines &amp; CTA buttons).
        </p>
    </div>
    <div>
        <a href="<?= admin_url('slider-add.php') ?>" class="btn-admin-primary">
            <span>+</span> Add New Hero Banner
        </a>
    </div>
</div>

<div class="admin-card">
    <div class="admin-card-header">
        <h3 class="admin-card-title">Homepage Slides (<?= count($slides) ?>)</h3>
        <span style="font-size: 13px; color: var(--admin-text-muted);">Ordered by Sort Order</span>
    </div>

    <div class="admin-card-body" style="padding: 0;">
        <?php if (empty($slides)): ?>
            <div style="padding: 40px; text-align: center; color: var(--admin-text-muted);">
                <p style="font-size: 16px; margin-bottom: 12px;">No hero slides found.</p>
                <a href="<?= admin_url('slider-add.php') ?>" class="btn-admin-primary">Create Your First Slide</a>
            </div>
        <?php else: ?>
            <table class="admin-table">
                <thead>
                    <tr>
                        <th style="width: 140px;">Banner Preview</th>
                        <th>Title / Badge</th>
                        <th style="width: 160px;">Display Mode</th>
                        <th style="width: 180px;">Buttons &amp; Links</th>
                        <th style="width: 90px; text-align: center;">Order</th>
                        <th style="width: 100px; text-align: center;">Status</th>
                        <th style="width: 130px; text-align: right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($slides as $slide): ?>
                        <tr>
                            <td>
                                <div style="width: 130px; height: 60px; background: #020b14; border-radius: 6px; overflow: hidden; display: flex; align-items: center; justify-content: center; border: 1px solid var(--admin-border);">
                                    <img src="<?= image_url($slide['image_path']) ?>" alt="Slide Preview" style="max-width: 100%; max-height: 100%; object-fit: contain;">
                                </div>
                            </td>
                            <td>
                                <strong style="color: var(--admin-text-dark); font-size: 14px; display: block; margin-bottom: 4px;">
                                    <?= e($slide['title'] ?: 'Untitled Slide') ?>
                                </strong>
                                <?php if (!empty($slide['badge_text'])): ?>
                                    <span style="display: inline-block; font-size: 11px; background: rgba(212, 175, 55, 0.15); color: #b45309; padding: 2px 8px; border-radius: 4px; border: 1px solid rgba(212, 175, 55, 0.3);">
                                        <?= e($slide['badge_text']) ?>
                                    </span>
                                <?php endif; ?>
                                <?php if (!empty($slide['subtitle'])): ?>
                                    <p style="font-size: 12px; color: var(--admin-text-muted); margin: 4px 0 0; max-width: 320px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                        <?= e($slide['subtitle']) ?>
                                    </p>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($slide['show_text_overlay']): ?>
                                    <span class="badge-status badge-published" style="background: #e0e7ff; color: #3730a3; border: 1px solid #c7d2fe;">
                                        ✍ With Text Overlay
                                    </span>
                                <?php else: ?>
                                    <span class="badge-status badge-published" style="background: #dcfce7; color: #15803d; border: 1px solid #bbf7d0;">
                                        🖼 Clean Image (Full Graphic)
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($slide['show_text_overlay']): ?>
                                    <div style="font-size: 12px;">
                                        <?php if (!empty($slide['button_text'])): ?>
                                            <div><strong>Btn 1:</strong> <?= e($slide['button_text']) ?> (<code><?= e($slide['button_link']) ?></code>)</div>
                                        <?php endif; ?>
                                        <?php if (!empty($slide['secondary_button_text'])): ?>
                                            <div><strong>Btn 2:</strong> <?= e($slide['secondary_button_text']) ?> (<code><?= e($slide['secondary_button_link']) ?></code>)</div>
                                        <?php endif; ?>
                                    </div>
                                <?php else: ?>
                                    <span style="color: var(--admin-text-muted); font-size: 12px;">N/A (Graphic mode)</span>
                                <?php endif; ?>
                            </td>
                            <td style="text-align: center; font-weight: 600; color: var(--admin-text-dark);">
                                <?= (int)$slide['sort_order'] ?>
                            </td>
                            <td style="text-align: center;">
                                <a href="<?= admin_url('sliders.php?toggle_id=' . $slide['id']) ?>" title="Click to toggle status" style="text-decoration: none;">
                                    <?php if ($slide['status'] === 'active'): ?>
                                        <span class="badge-status badge-published" style="cursor: pointer;">Active</span>
                                    <?php else: ?>
                                        <span class="badge-status badge-draft" style="cursor: pointer;">Inactive</span>
                                    <?php endif; ?>
                                </a>
                            </td>
                            <td style="text-align: right;">
                                <div style="display: flex; gap: 6px; justify-content: flex-end;">
                                    <a href="<?= admin_url('slider-edit.php?id=' . $slide['id']) ?>" class="btn-action btn-edit" title="Edit Slide">Edit</a>
                                    <a href="<?= admin_url('slider-delete.php?id=' . $slide['id']) ?>" class="btn-action btn-delete" onclick="return confirm('Are you sure you want to delete this hero slide?');" title="Delete Slide">Delete</a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>

<div class="admin-card" style="margin-top: 25px; background: #f8fafc; border: 1px solid #e2e8f0;">
    <div class="admin-card-body" style="padding: 20px;">
        <h4 style="margin: 0 0 10px; font-size: 15px; color: var(--admin-primary); display: flex; align-items: center; gap: 8px;">
            <span>💡</span> Hero Banner Best Practices
        </h4>
        <ul style="margin: 0; padding-left: 20px; font-size: 13px; color: #475569; line-height: 1.7;">
            <li><strong>Clean Image Mode:</strong> Use when the uploaded image is an infographic banner (like the Global Connect world globe map) with text, country lines, and UEN numbers already designed into it. It automatically renders with full visibility and zero cropping across mobile and desktop.</li>
            <li><strong>Text Overlay Mode:</strong> Use when uploading high-resolution corporate backgrounds where you want the CMS to dynamically render high-contrast headlines, subheadings, and action buttons.</li>
            <li><strong>Multi-Slide Slider:</strong> When more than 1 slide is set to <em>Active</em>, the homepage automatically turns into a smooth, swipe-ready carousel with navigation arrows, dots, and a 6.5s auto-rotation timer.</li>
        </ul>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
