<?php
/**
 * Global Connect - Key Executive Contacts & Directors Management
 */
require_once __DIR__ . '/includes/auth.php';

$db = get_db();

// Handle quick status toggle
if (isset($_GET['toggle_id']) && is_numeric($_GET['toggle_id'])) {
    $toggleId = (int)$_GET['toggle_id'];
    try {
        $stmt = $db->prepare("UPDATE executives SET status = CASE WHEN status = 'active' THEN 'inactive' ELSE 'active' END, updated_at = CURRENT_TIMESTAMP WHERE id = ?");
        $stmt->execute([$toggleId]);
        set_flash('success', 'Executive status updated successfully!');
    } catch (Exception $e) {
        set_flash('error', 'Failed to update executive status: ' . $e->getMessage());
    }
    header('Location: ' . admin_url('executives.php'));
    exit;
}

$search = clean($_GET['search'] ?? '');
$statusFilter = clean($_GET['status'] ?? '');

$sql = "SELECT * FROM executives WHERE 1=1";
$params = [];

if (!empty($search)) {
    $sql .= " AND (name LIKE ? OR role LIKE ? OR email LIKE ? OR location LIKE ?)";
    $like = "%{$search}%";
    $params[] = $like;
    $params[] = $like;
    $params[] = $like;
    $params[] = $like;
}

if (!empty($statusFilter) && in_array($statusFilter, ['active', 'inactive'])) {
    $sql .= " AND status = ?";
    $params[] = $statusFilter;
}

$sql .= " ORDER BY sort_order ASC, id ASC";

$executives = [];
try {
    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    $executives = $stmt->fetchAll();
} catch (Exception $e) {
    $executives = [];
}

$adminPageTitle = 'Executive Contacts & Directors | Global Connect CMS';
$adminPageHeading = 'Key Executive Contacts & Directors';
require_once __DIR__ . '/includes/header.php';
?>

<div style="margin-bottom: 25px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px;">
    <div>
        <p style="color: var(--admin-text-muted); font-size: 14px; margin: 0;">
            Manage executive leadership contacts, board directors, and regional business development leaders displayed on the public Contact page.
        </p>
    </div>
    <div>
        <a href="<?= admin_url('executive-add.php') ?>" class="btn-admin-primary">
            <span>+</span> Add New Executive
        </a>
    </div>
</div>

<!-- Search & Filter Card -->
<div class="admin-card" style="margin-bottom: 25px;">
    <div class="admin-card-body" style="padding: 16px 20px;">
        <form method="GET" action="<?= admin_url('executives.php') ?>" style="display: flex; gap: 15px; align-items: center; flex-wrap: wrap;">
            <div style="flex: 1; min-width: 240px;">
                <input type="text" name="search" class="admin-form-input" placeholder="Search by name, role, email, or location..." value="<?= e($search) ?>" style="margin: 0;">
            </div>
            <div style="width: 180px;">
                <select name="status" class="admin-form-select" style="margin: 0;">
                    <option value="">All Statuses</option>
                    <option value="active" <?= $statusFilter === 'active' ? 'selected' : '' ?>>Active</option>
                    <option value="inactive" <?= $statusFilter === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                </select>
            </div>
            <div>
                <button type="submit" class="btn-admin-secondary">Filter</button>
                <?php if (!empty($search) || !empty($statusFilter)): ?>
                    <a href="<?= admin_url('executives.php') ?>" class="btn-admin-secondary" style="background: transparent; border: 1px solid var(--admin-border); margin-left: 5px;">Reset</a>
                <?php endif; ?>
            </div>
        </form>
    </div>
</div>

<div class="admin-card">
    <div class="admin-card-header">
        <h3 class="admin-card-title">Leadership Directory (<?= count($executives) ?>)</h3>
        <span style="font-size: 13px; color: var(--admin-text-muted);">Sorted by Sort Order</span>
    </div>

    <div class="admin-card-body" style="padding: 0;">
        <?php if (empty($executives)): ?>
            <div style="padding: 40px; text-align: center; color: var(--admin-text-muted);">
                <p style="font-size: 16px; margin-bottom: 12px;">No executive contacts found.</p>
                <a href="<?= admin_url('executive-add.php') ?>" class="btn-admin-primary">Add Executive Contact</a>
            </div>
        <?php else: ?>
            <table class="admin-table">
                <thead>
                    <tr>
                        <th style="width: 70px;">Avatar</th>
                        <th>Name &amp; Role</th>
                        <th>Location / Office</th>
                        <th>Email &amp; Direct Phone</th>
                        <th style="width: 80px; text-align: center;">Order</th>
                        <th style="width: 100px; text-align: center;">Status</th>
                        <th style="width: 150px; text-align: right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($executives as $exec): 
                        $cleanName = preg_replace('/^(Mr\.?|Ms\.?|Mrs\.?|Dr\.?)\s+/i', '', $exec['name']);
                        $parts = preg_split('/\s+/', trim($cleanName));
                        $initials = '';
                        if (!empty($parts[0])) $initials .= mb_substr($parts[0], 0, 1);
                        if (!empty($parts[1])) $initials .= mb_substr($parts[1], 0, 1);
                        if (empty($initials)) $initials = mb_substr($exec['name'], 0, 2);
                    ?>
                        <tr>
                            <td>
                                <?php if (!empty($exec['avatar'])): ?>
                                    <div style="width: 44px; height: 44px; border-radius: 50%; overflow: hidden; border: 1.5px solid var(--admin-gold); display: flex; align-items: center; justify-content: center; background: #f1f5f9;">
                                        <img src="<?= image_url($exec['avatar']) ?>" alt="<?= e($exec['name']) ?>" style="width: 100%; height: 100%; object-fit: cover;">
                                    </div>
                                <?php else: ?>
                                    <div style="width: 44px; height: 44px; border-radius: 50%; background: #0a192f; color: #d4af37; border: 1.5px solid #d4af37; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 14px;">
                                        <?= e(strtoupper($initials)) ?>
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td>
                                <strong style="color: var(--admin-text-dark); font-size: 14.5px; display: block; margin-bottom: 3px;">
                                    <?= e($exec['name']) ?>
                                </strong>
                                <span style="font-size: 12.5px; color: var(--admin-secondary); font-weight: 600;">
                                    <?= e($exec['role']) ?>
                                </span>
                                <?php if (!empty($exec['bio'])): ?>
                                    <p style="font-size: 11.5px; color: var(--admin-text-muted); margin: 4px 0 0; max-width: 320px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                        <?= e($exec['bio']) ?>
                                    </p>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if (!empty($exec['location'])): ?>
                                    <span style="font-size: 13px; color: #334155;">
                                        📍 <?= e($exec['location']) ?>
                                    </span>
                                <?php else: ?>
                                    <span style="color: var(--admin-text-muted); font-size: 12px;">—</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div style="font-size: 13px;">
                                    <a href="mailto:<?= e($exec['email']) ?>" style="color: var(--admin-secondary); text-decoration: none; font-weight: 500; display: inline-flex; align-items: center; gap: 4px;">
                                        ✉ <?= e($exec['email']) ?>
                                    </a>
                                </div>
                                <?php if (!empty($exec['phone'])): ?>
                                    <div style="font-size: 12px; color: #475569; margin-top: 3px;">
                                        📞 <?= e($exec['phone']) ?>
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td style="text-align: center;">
                                <span style="display: inline-block; padding: 2px 8px; background: #f1f5f9; border-radius: 4px; font-weight: 600; font-size: 12px; color: #475569;">
                                    <?= (int)$exec['sort_order'] ?>
                                </span>
                            </td>
                            <td style="text-align: center;">
                                <a href="<?= admin_url('executives.php?toggle_id=' . $exec['id']) ?>" title="Click to toggle status" style="text-decoration: none;">
                                    <?php if ($exec['status'] === 'active'): ?>
                                        <span class="badge-status badge-published" style="cursor: pointer;">Active</span>
                                    <?php else: ?>
                                        <span class="badge-status badge-draft" style="cursor: pointer;">Inactive</span>
                                    <?php endif; ?>
                                </a>
                            </td>
                            <td style="text-align: right;">
                                <div style="display: inline-flex; gap: 8px;">
                                    <a href="<?= admin_url('executive-edit.php?id=' . $exec['id']) ?>" class="btn-action btn-edit" title="Edit Executive">
                                        ✏ Edit
                                    </a>
                                    <a href="<?= admin_url('executive-delete.php?id=' . $exec['id'] . '&csrf_token=' . csrf_token()) ?>" class="btn-action btn-delete" onclick="return confirm('Are you sure you want to delete \'<?= addslashes(e($exec['name'])) ?>\'?');" title="Delete Executive">
                                        🗑 Delete
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
