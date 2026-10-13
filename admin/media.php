<?php
/**
 * Global Connect - Media Library Manager
 */
$adminPageTitle = 'Media Library | Global Connect CMS';
$adminPageHeading = 'Media & Asset Manager';

require_once __DIR__ . '/includes/header.php';

$db = get_db();
$errorMsg = '';

// Handle Image Upload
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['csrf_token'] ?? '')) {
        $errorMsg = 'CSRF validation failed.';
    } elseif (!empty($_FILES['media_file']['name'])) {
        $file = $_FILES['media_file'];
        $allowedExts = ['jpg', 'jpeg', 'png', 'svg', 'webp', 'gif'];
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

        if (in_array($ext, $allowedExts) && $file['error'] === UPLOAD_ERR_OK) {
            $title = clean($_POST['title'] ?: pathinfo($file['name'], PATHINFO_FILENAME));
            $newFilename = 'media-' . time() . '-' . rand(100, 999) . '.' . $ext;
            $targetPath = __DIR__ . '/../../uploads/' . $newFilename;

            if (move_uploaded_file($file['tmp_name'], $targetPath)) {
                $filePath = '/uploads/' . $newFilename;
                $stmt = $db->prepare("INSERT INTO media (title, filename, filepath, filetype, filesize, created_at) VALUES (?, ?, ?, ?, ?, CURRENT_TIMESTAMP)");
                $stmt->execute([$title, $newFilename, $filePath, $file['type'], $file['size']]);

                set_flash('success', "Image '{$title}' uploaded successfully!");
                header('Location: /admin/media.php');
                exit;
            } else {
                $errorMsg = 'Could not move uploaded file.';
            }
        } else {
            $errorMsg = 'Invalid file type. Allowed: JPG, PNG, SVG, WEBP, GIF.';
        }
    }
}

// Fetch database media
$uploadedMedia = $db->query("SELECT * FROM media ORDER BY created_at DESC")->fetchAll();

// Also scan assets/images directory
$assetImages = [];
$assetsDir = __DIR__ . '/../../assets/images';
if (is_dir($assetsDir)) {
    $files = scandir($assetsDir);
    foreach ($files as $f) {
        if ($f !== '.' && $f !== '..' && !is_dir($assetsDir . '/' . $f)) {
            $assetImages[] = [
                'title' => ucwords(str_replace(['-', '_', '.'], ' ', pathinfo($f, PATHINFO_FILENAME))),
                'filename' => $f,
                'filepath' => '/assets/images/' . $f
            ];
        }
    }
}
?>

<!-- Upload Box -->
<div class="admin-card">
    <div class="admin-card-header">
        <h3>Upload New Image</h3>
    </div>
    <div class="admin-card-body">
        <?php if ($errorMsg): ?>
            <div style="background: #fee2e2; color: #b91c1c; padding: 12px 18px; border-radius: 8px; margin-bottom: 20px;">
                ⚠ <?= e($errorMsg) ?>
            </div>
        <?php endif; ?>

        <form action="/admin/media.php" method="POST" enctype="multipart/form-data" style="display: flex; gap: 15px; align-items: flex-end; flex-wrap: wrap;">
            <?= csrf_field() ?>

            <div style="flex: 1; min-width: 200px;">
                <label class="admin-form-label" for="title">Image Title / Description</label>
                <input type="text" id="title" name="title" class="admin-form-input" placeholder="e.g. Copper Ingots, Dubai Port">
            </div>

            <div style="flex: 1; min-width: 240px;">
                <label class="admin-form-label" for="media_file">Select Image File</label>
                <input type="file" id="media_file" name="media_file" class="admin-form-input" required accept="image/*">
            </div>

            <div>
                <button type="submit" class="btn-admin-primary" style="padding: 11px 24px;">
                    Upload File ↑
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Uploaded Media Gallery -->
<?php if (!empty($uploadedMedia)): ?>
    <div class="admin-card">
        <div class="admin-card-header">
            <h3>Uploaded Media (<?= count($uploadedMedia) ?>)</h3>
        </div>
        <div class="admin-card-body">
            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 20px;">
                <?php foreach ($uploadedMedia as $m): ?>
                    <div style="border: 1px solid var(--admin-border); border-radius: 8px; overflow: hidden; background: #ffffff;">
                        <img src="<?= e($m['filepath']) ?>" alt="" style="width: 100%; height: 130px; object-fit: cover;">
                        <div style="padding: 10px;">
                            <strong style="font-size: 13px; display: block; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;"><?= e($m['title']) ?></strong>
                            <input type="text" readonly value="<?= e($m['filepath']) ?>" style="width: 100%; font-size: 11px; padding: 4px; margin-top: 6px; border: 1px solid var(--admin-border); border-radius: 4px;" onclick="this.select(); document.execCommand('copy'); alert('Copied image path: ' + this.value);">
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
<?php endif; ?>

<!-- System Asset Images -->
<div class="admin-card">
    <div class="admin-card-header">
        <h3>Theme Asset Library (<?= count($assetImages) ?> Images)</h3>
    </div>
    <div class="admin-card-body">
        <p style="font-size: 13.5px; color: var(--admin-text-muted); margin-bottom: 20px;">
            Click on any image URL below to copy its path for use in page content and headings.
        </p>

        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap: 20px;">
            <?php foreach ($assetImages as $ai): ?>
                <div style="border: 1px solid var(--admin-border); border-radius: 8px; overflow: hidden; background: #ffffff; box-shadow: 0 1px 3px rgba(0,0,0,0.03);">
                    <div style="height: 120px; background: #0b1f3a; display: flex; align-items: center; justify-content: center; overflow: hidden;">
                        <img src="<?= e($ai['filepath']) ?>" alt="" style="max-height: 120px; width: 100%; object-fit: cover;">
                    </div>
                    <div style="padding: 10px;">
                        <strong style="font-size: 12.5px; display: block; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; margin-bottom: 4px;"><?= e($ai['title']) ?></strong>
                        <input type="text" readonly value="<?= e($ai['filepath']) ?>" style="width: 100%; font-size: 11px; padding: 5px; border: 1px solid var(--admin-border); border-radius: 4px; font-family: monospace; background: #f8fafc;" onclick="this.select(); navigator.clipboard.writeText(this.value); alert('Copied: ' + this.value);">
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
