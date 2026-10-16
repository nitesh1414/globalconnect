<?php
/**
 * Global Connect - Contacts & Inquiries Manager
 */
require_once __DIR__ . '/includes/auth.php';

$db = get_db();

// Handle Status Updates or Deletions
if (isset($_GET['action'])) {
    $action = clean($_GET['action']);
    $id = (int)($_GET['id'] ?? 0);

    if ($action === 'mark_read' && $id > 0) {
        $stmt = $db->prepare("UPDATE contacts SET status = 'read' WHERE id = ?");
        $stmt->execute([$id]);
        set_flash('success', 'Inquiry marked as read.');
        header('Location: ' . admin_url('contacts.php'));
        exit;
    }

    if ($action === 'mark_replied' && $id > 0) {
        $stmt = $db->prepare("UPDATE contacts SET status = 'replied' WHERE id = ?");
        $stmt->execute([$id]);
        set_flash('success', 'Inquiry marked as replied.');
        header('Location: ' . admin_url('contacts.php'));
        exit;
    }

    if ($action === 'delete' && $id > 0) {
        $stmt = $db->prepare("DELETE FROM contacts WHERE id = ?");
        $stmt->execute([$id]);
        set_flash('success', 'Inquiry deleted.');
        header('Location: ' . admin_url('contacts.php'));
        exit;
    }
}

$statusFilter = clean($_GET['status'] ?? '');
$activeId = (int)($_GET['id'] ?? 0);
$selectedInquiry = null;

if ($activeId > 0) {
    $stmt = $db->prepare("SELECT * FROM contacts WHERE id = ?");
    $stmt->execute([$activeId]);
    $selectedInquiry = $stmt->fetch();
    
    // Auto-mark as read if unread
    if ($selectedInquiry && $selectedInquiry['status'] === 'unread') {
        $db->prepare("UPDATE contacts SET status = 'read' WHERE id = ?")->execute([$activeId]);
        $selectedInquiry['status'] = 'read';
    }
}

// Fetch inquiries
$sql = "SELECT * FROM contacts WHERE 1=1";
$params = [];
if (!empty($statusFilter)) {
    $sql .= " AND status = ?";
    $params[] = $statusFilter;
}
$sql .= " ORDER BY created_at DESC";

$inquiries = [];
$totalInquiries = 0;
try {
    $totalInquiries = $db->query("SELECT COUNT(*) FROM contacts")->fetchColumn();
    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    $inquiries = $stmt->fetchAll();
} catch (Exception $e) {}

$adminPageTitle = 'Inquiries | Global Connect CMS';
$adminPageHeading = 'Customer Inquiries &amp; Leads';
require_once __DIR__ . '/includes/header.php';
?>

<div style="display: grid; grid-template-columns: <?= $selectedInquiry ? '1fr 1.2fr' : '1fr' ?>; gap: 25px;">
    <!-- Inquiries Table -->
    <div class="admin-card">
        <div class="admin-card-header" style="flex-wrap: wrap; gap: 10px;">
            <div style="display: flex; gap: 8px;">
                <a href="<?= admin_url('contacts.php') ?>" class="btn-action <?= empty($statusFilter) ? 'btn-edit' : 'btn-view' ?>">All (<?= $totalInquiries ?>)</a>
                <a href="<?= admin_url('contacts.php?status=unread') ?>" class="btn-action <?= $statusFilter === 'unread' ? 'btn-edit' : 'btn-view' ?>">Unread</a>
                <a href="<?= admin_url('contacts.php?status=read') ?>" class="btn-action <?= $statusFilter === 'read' ? 'btn-edit' : 'btn-view' ?>">Read</a>
                <a href="<?= admin_url('contacts.php?status=replied') ?>" class="btn-action <?= $statusFilter === 'replied' ? 'btn-edit' : 'btn-view' ?>">Replied</a>
            </div>
        </div>
        <div class="admin-card-body" style="padding: 0;">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Sender</th>
                        <th>Subject</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($inquiries)): ?>
                        <?php foreach ($inquiries as $inq): ?>
                            <tr style="<?= ($selectedInquiry && $selectedInquiry['id'] == $inq['id']) ? 'background: #eff6ff;' : '' ?>">
                                <td>
                                    <small style="color: var(--admin-text-muted);"><?= date('M d, Y', strtotime($inq['created_at'])) ?></small>
                                </td>
                                <td>
                                    <strong><?= e($inq['name']) ?></strong>
                                    <small style="display: block; color: var(--admin-text-muted);"><?= e($inq['email']) ?></small>
                                </td>
                                <td>
                                    <span style="font-size: 13px;"><?= e(mb_strimwidth($inq['subject'] ?: $inq['message'], 0, 30, '...')) ?></span>
                                </td>
                                <td>
                                    <span class="status-badge badge-<?= e($inq['status']) ?>">
                                        <?= ucfirst($inq['status']) ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <a href="<?= admin_url('contacts.php?id=' . $inq['id']) ?>" class="btn-action btn-edit">View</a>
                                        <a href="<?= admin_url('contacts.php?action=delete&id=' . $inq['id']) ?>" class="btn-action btn-delete" onclick="return confirm('Delete this inquiry?');">✕</a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" style="text-align: center; padding: 30px; color: var(--admin-text-muted);">
                                No inquiries found in this filter.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Inquiry Details View -->
    <?php if ($selectedInquiry): ?>
        <div class="admin-card">
            <div class="admin-card-header" style="justify-content: space-between;">
                <h3>Inquiry Details</h3>
                <a href="<?= admin_url('contacts.php') ?>" class="btn-action btn-view">✕ Close</a>
            </div>
            <div class="admin-card-body">
                <div style="border-bottom: 1px solid var(--admin-border); padding-bottom: 15px; margin-bottom: 15px;">
                    <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                        <strong>From: <?= e($selectedInquiry['name']) ?></strong>
                        <span style="color: var(--admin-text-muted); font-size: 12px;"><?= e($selectedInquiry['created_at']) ?></span>
                    </div>
                    <div style="margin-bottom: 6px;">
                        <span style="color: var(--admin-text-muted); font-size: 13px;">Email:</span> 
                        <a href="mailto:<?= e($selectedInquiry['email']) ?>" style="color: var(--admin-primary); font-weight: 600;"><?= e($selectedInquiry['email']) ?></a>
                    </div>
                    <?php if (!empty($selectedInquiry['phone'])): ?>
                        <div style="margin-bottom: 6px;">
                            <span style="color: var(--admin-text-muted); font-size: 13px;">Phone:</span> 
                            <a href="tel:<?= e($selectedInquiry['phone']) ?>" style="color: var(--admin-primary); font-weight: 600;"><?= e($selectedInquiry['phone']) ?></a>
                        </div>
                    <?php endif; ?>
                    <?php if (!empty($selectedInquiry['subject'])): ?>
                        <div style="margin-top: 10px;">
                            <span style="color: var(--admin-text-muted); font-size: 13px;">Subject:</span>
                            <div style="font-weight: 700; font-size: 15px; color: var(--admin-primary);"><?= e($selectedInquiry['subject']) ?></div>
                        </div>
                    <?php endif; ?>
                </div>

                <div style="margin-bottom: 25px;">
                    <label class="admin-form-label">Message Content:</label>
                    <div style="background: #f8fafc; border: 1px solid var(--admin-border); padding: 15px; border-radius: 6px; font-size: 14px; line-height: 1.6; white-space: pre-wrap;">
                        <?= e($selectedInquiry['message']) ?>
                    </div>
                </div>

                <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                    <a href="mailto:<?= e($selectedInquiry['email']) ?>?subject=<?= urlencode('Re: ' . ($selectedInquiry['subject'] ?: 'Your Inquiry to Global Connect')) ?>" class="btn-admin-primary">
                        ✉ Reply via Email
                    </a>
                    <a href="<?= admin_url('contacts.php?action=mark_replied&id=' . $selectedInquiry['id']) ?>" class="btn-action btn-edit">
                        ✓ Mark Replied
                    </a>
                    <a href="<?= admin_url('contacts.php?action=delete&id=' . $selectedInquiry['id']) ?>" class="btn-action btn-delete" onclick="return confirm('Delete this inquiry?');">
                        Delete
                    </a>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
