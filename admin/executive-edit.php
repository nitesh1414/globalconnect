<?php
/**
 * Global Connect - Edit Executive Contact / Director
 */
require_once __DIR__ . '/includes/auth.php';

$db = get_db();
$id = (int)($_GET['id'] ?? 0);

if ($id <= 0) {
    set_flash('error', 'Invalid executive ID provided.');
    header('Location: ' . admin_url('executives.php'));
    exit;
}

$executive = get_executive_by_id($id);
if (!$executive) {
    set_flash('error', 'Executive contact not found.');
    header('Location: ' . admin_url('executives.php'));
    exit;
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['csrf_token'] ?? '')) {
        $errors[] = 'CSRF validation failed. Please submit the form again.';
    } else {
        $name = clean($_POST['name'] ?? '');
        $role = clean($_POST['role'] ?? '');
        $email = clean($_POST['email'] ?? '');
        $phone = clean($_POST['phone'] ?? '');
        $location = clean($_POST['location'] ?? '');
        $bio = clean($_POST['bio'] ?? '');
        $avatarPath = clean($_POST['avatar_path'] ?? $executive['avatar']);
        $status = in_array($_POST['status'] ?? '', ['active', 'inactive']) ? $_POST['status'] : 'active';
        $sortOrder = (int)($_POST['sort_order'] ?? 1);

        // Validation
        if (empty($name)) {
            $errors[] = 'Executive full name is required.';
        }
        if (empty($role)) {
            $errors[] = 'Executive role or designation is required.';
        }
        if (empty($email)) {
            $errors[] = 'Executive email address is required.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Please provide a valid email address.';
        }

        // Handle File Upload if provided
        if (isset($_FILES['avatar_file']) && $_FILES['avatar_file']['error'] === UPLOAD_ERR_OK) {
            $uploaded = upload_image($_FILES['avatar_file']);
            if ($uploaded) {
                $avatarPath = $uploaded;
            } else {
                $errors[] = 'Avatar upload failed. Allowed types: JPG, PNG, WebP, SVG, GIF (Max 8MB).';
            }
        }

        // Handle avatar removal checkbox
        if (isset($_POST['remove_avatar']) && $_POST['remove_avatar'] === '1') {
            $avatarPath = '';
        }

        if (empty($errors)) {
            try {
                $stmt = $db->prepare("
                    UPDATE executives SET 
                        name = ?, 
                        role = ?, 
                        email = ?, 
                        phone = ?, 
                        location = ?, 
                        bio = ?, 
                        avatar = ?, 
                        sort_order = ?, 
                        status = ?, 
                        updated_at = CURRENT_TIMESTAMP
                    WHERE id = ?
                ");

                $stmt->execute([
                    $name,
                    $role,
                    $email,
                    $phone,
                    $location,
                    $bio,
                    $avatarPath,
                    $sortOrder,
                    $status,
                    $id
                ]);

                set_flash('success', 'Executive contact "' . $name . '" updated successfully!');
                header('Location: ' . admin_url('executives.php'));
                exit;
            } catch (Exception $e) {
                $errors[] = 'Database error: ' . $e->getMessage();
            }
        }
    }
}

$adminPageTitle = 'Edit Executive Contact | Global Connect CMS';
$adminPageHeading = 'Edit Executive / Director';
require_once __DIR__ . '/includes/header.php';

$cleanName = preg_replace('/^(Mr\.?|Ms\.?|Mrs\.?|Dr\.?)\s+/i', '', $executive['name']);
$parts = preg_split('/\s+/', trim($cleanName));
$initials = '';
if (!empty($parts[0])) $initials .= mb_substr($parts[0], 0, 1);
if (!empty($parts[1])) $initials .= mb_substr($parts[1], 0, 1);
if (empty($initials)) $initials = mb_substr($executive['name'], 0, 2);
?>

<div style="margin-bottom: 20px;">
    <a href="<?= admin_url('executives.php') ?>" style="color: var(--admin-primary); text-decoration: none; font-size: 14px; display: inline-flex; align-items: center; gap: 5px;">
        ← Back to Executive Team
    </a>
</div>

<?php if (!empty($errors)): ?>
    <div style="background: #fee2e2; border-left: 4px solid #ef4444; color: #991b1b; padding: 12px 16px; border-radius: 6px; margin-bottom: 20px;">
        <strong>Please correct the following errors:</strong>
        <ul style="margin: 6px 0 0; padding-left: 20px;">
            <?php foreach ($errors as $err): ?>
                <li><?= e($err) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<form action="<?= admin_url('executive-edit.php?id=' . $id) ?>" method="POST" enctype="multipart/form-data">
    <?= csrf_field() ?>

    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 25px;">
        <!-- Left Column: Executive Details -->
        <div style="display: flex; flex-direction: column; gap: 20px;">
            <div class="admin-card">
                <div class="admin-card-header">
                    <h3 class="admin-card-title">Edit Profile &amp; Contact Details</h3>
                </div>
                <div class="admin-card-body">
                    <div class="admin-form-group">
                        <label class="admin-form-label" for="name">Full Name with Salutation *</label>
                        <input type="text" id="name" name="name" class="admin-form-input" required 
                               placeholder="e.g. Mr Shammy Songra, Mr Cliff Poh" 
                               value="<?= e($_POST['name'] ?? $executive['name']) ?>">
                    </div>

                    <div class="admin-form-group">
                        <label class="admin-form-label" for="role">Role / Corporate Designation *</label>
                        <input type="text" id="role" name="role" class="admin-form-input" required 
                               placeholder="e.g. Founder / Director, Director – Singapore" 
                               value="<?= e($_POST['role'] ?? $executive['role']) ?>">
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                        <div class="admin-form-group">
                            <label class="admin-form-label" for="email">Corporate Email *</label>
                            <input type="email" id="email" name="email" class="admin-form-input" required 
                                   placeholder="e.g. shammy@globalconnectsg.com" 
                                   value="<?= e($_POST['email'] ?? $executive['email']) ?>">
                        </div>

                        <div class="admin-form-group">
                            <label class="admin-form-label" for="phone">Direct / Mobile / Roaming Phone</label>
                            <input type="text" id="phone" name="phone" class="admin-form-input" 
                                   placeholder="e.g. (65) 89451100 / (91) 99100 02299" 
                                   value="<?= e($_POST['phone'] ?? $executive['phone']) ?>">
                        </div>
                    </div>

                    <div class="admin-form-group">
                        <label class="admin-form-label" for="location">Location / Office Desk</label>
                        <input type="text" id="location" name="location" class="admin-form-input" 
                               placeholder="e.g. Singapore / India, Singapore HQ, Jakarta, Indonesia" 
                               value="<?= e($_POST['location'] ?? $executive['location']) ?>">
                    </div>

                    <div class="admin-form-group">
                        <label class="admin-form-label" for="bio">Short Biography / Executive Summary (Optional)</label>
                        <textarea id="bio" name="bio" class="admin-form-textarea" rows="3" 
                                  placeholder="Brief overview of executive's industry experience, commodities focus, or advisory practice..."><?= e($_POST['bio'] ?? $executive['bio']) ?></textarea>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column: Avatar Photo & Publishing Controls -->
        <div style="display: flex; flex-direction: column; gap: 20px;">
            <!-- Publishing Status & Sort Order -->
            <div class="admin-card">
                <div class="admin-card-header">
                    <h3 class="admin-card-title">Publishing Settings</h3>
                </div>
                <div class="admin-card-body">
                    <div class="admin-form-group">
                        <label class="admin-form-label" for="status">Display Status</label>
                        <select id="status" name="status" class="admin-form-select">
                            <option value="active" <?= ($_POST['status'] ?? $executive['status']) === 'active' ? 'selected' : '' ?>>Active (Visible on Contact Page)</option>
                            <option value="inactive" <?= ($_POST['status'] ?? $executive['status']) === 'inactive' ? 'selected' : '' ?>>Inactive (Hidden)</option>
                        </select>
                    </div>

                    <div class="admin-form-group">
                        <label class="admin-form-label" for="sort_order">Sort Order (Sequence)</label>
                        <input type="number" id="sort_order" name="sort_order" class="admin-form-input" 
                               value="<?= e($_POST['sort_order'] ?? $executive['sort_order']) ?>" min="0" step="1">
                        <small style="color: var(--admin-text-muted); font-size: 12px;">Lower numbers appear first.</small>
                    </div>

                    <div style="margin-top: 20px; display: flex; flex-direction: column; gap: 10px;">
                        <button type="submit" class="btn-admin-primary" style="width: 100%; justify-content: center;">
                            💾 Update Executive Contact
                        </button>
                        <a href="<?= admin_url('executives.php') ?>" class="btn-admin-secondary" style="width: 100%; text-align: center; justify-content: center;">
                            Cancel
                        </a>
                    </div>
                </div>
            </div>

            <!-- Avatar Photo -->
            <div class="admin-card">
                <div class="admin-card-header">
                    <h3 class="admin-card-title">Profile Avatar Photo</h3>
                </div>
                <div class="admin-card-body">
                    <!-- Current Avatar Display -->
                    <div style="text-align: center; margin-bottom: 15px;">
                        <span style="font-size: 12px; color: var(--admin-text-muted); display: block; margin-bottom: 6px;">Current Avatar:</span>
                        <?php if (!empty($executive['avatar'])): ?>
                            <div style="width: 80px; height: 80px; border-radius: 50%; overflow: hidden; border: 2px solid var(--admin-gold); margin: 0 auto; box-shadow: var(--admin-shadow-sm);">
                                <img src="<?= image_url($executive['avatar']) ?>" alt="<?= e($executive['name']) ?>" style="width: 100%; height: 100%; object-fit: cover;">
                            </div>
                            <div style="margin-top: 8px;">
                                <label style="font-size: 12px; color: #ef4444; cursor: pointer; display: inline-flex; align-items: center; gap: 4px;">
                                    <input type="checkbox" name="remove_avatar" value="1"> Remove current avatar
                                </label>
                            </div>
                        <?php else: ?>
                            <div style="width: 80px; height: 80px; border-radius: 50%; background: #0a192f; color: #d4af37; border: 2px solid #d4af37; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 24px; margin: 0 auto;">
                                <?= e(strtoupper($initials)) ?>
                            </div>
                            <small style="color: var(--admin-text-muted); font-size: 11.5px; display: block; margin-top: 6px;">Monogram Initials Fallback</small>
                        <?php endif; ?>
                    </div>

                    <div class="admin-form-group">
                        <label class="admin-form-label" for="avatar_file">Upload New Avatar Image</label>
                        <input type="file" id="avatar_file" name="avatar_file" class="admin-form-input" accept="image/*" onchange="previewAvatar(this)">
                        <small style="color: var(--admin-text-muted); font-size: 12px; display: block; margin-top: 4px;">
                            Recommended: Square headshot (400x400 px, JPG or PNG).
                        </small>
                    </div>

                    <div class="admin-form-group">
                        <label class="admin-form-label" for="avatar_path">Or Image Filename</label>
                        <input type="text" id="avatar_path" name="avatar_path" class="admin-form-input" 
                               placeholder="e.g. avatar-shammy.jpg" 
                               value="<?= e($_POST['avatar_path'] ?? $executive['avatar']) ?>">
                    </div>

                    <div id="avatar_preview_container" style="display: none; margin-top: 15px; text-align: center;">
                        <span style="font-size: 12px; color: var(--admin-text-muted); display: block; margin-bottom: 6px;">New Avatar Preview:</span>
                        <div style="width: 80px; height: 80px; border-radius: 50%; overflow: hidden; border: 2px solid var(--admin-gold); margin: 0 auto; box-shadow: var(--admin-shadow-sm);">
                            <img id="avatar_preview" src="" alt="Avatar Preview" style="width: 100%; height: 100%; object-fit: cover;">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

<script>
function previewAvatar(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('avatar_preview').src = e.target.result;
            document.getElementById('avatar_preview_container').style.display = 'block';
        }
        reader.readAsDataURL(input.files[0]);
    }
}
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
