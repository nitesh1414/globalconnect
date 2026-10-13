<?php
/**
 * Global Connect - Inquiries & Contact Inbox
 */
$adminPageTitle = 'Inquiries Inbox | Global Connect CMS';
$adminPageHeading = 'Customer Messages & Inquiries';

require_once __DIR__ . '/includes/header.php';

$db = get_db();

// Handle status updates or deletion
if (isset($_GET['action']) && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $action = $_GET['action'];

    if ($action === 'mark_read') {
        $db->prepare("UPDATE contacts SET status = 'read' WHERE id = ?")->execute([$id]);
        set_flash('success', 'Inquiry marked as read.');
    } elseif ($action === 'mark_replied') {
        $db->prepare("UPDATE contacts SET status = 'replied' WHERE id = ?")->execute([$id]);
        set_flash('success', 'Inquiry marked as replied.');
    } elseif ($action === 'delete') {
        $db->prepare("DELETE FROM contacts WHERE id = ?")->execute([$id]);
        set_flash('success', 'Inquiry deleted.');
    }
    header('Location: /admin/contacts.php');
    exit;
}

$statusFilter = clean($_GET['status'] ?? '');
$sql = "SELECT * FROM contacts WHERE 1=1";
$params = [];

if (!empty($statusFilter)) {
    $sql .= " AND status = ?";
    $params[] = $statusFilter;
}

$sql .= " ORDER BY created_at DESC";
$stmt = $db->prepare($sql);
$stmt->execute($params);
$inquiries = $stmt->fetchAll();

// Check if specific inquiry is being viewed
$viewId = (int)($_GET['id'] ?? 0);
$selectedInquiry = null;
if ($viewId > 0) {
    $stmtView = $db->prepare("SELECT * FROM contacts WHERE id = ? LIMIT 1");
    $stmtView->execute([$viewId]);
    $selectedInquiry = $stmtView->fetch();
    if ($selectedInquiry && $selectedInquiry['status'] === 'unread') {
        $db->prepare("UPDATE contacts SET status = 'read' WHERE id = ?")->execute([$viewId]);
        $selectedInquiry['status'] = 'read';
    }
}
?>

<div style="display: grid; grid-template-columns: <?= $selectedInquiry ? '1.2fr 1fr' : '1fr' ?>; gap: 25px;">
    <!-- Inquiries List -->
    <div class="admin-card">
        <div class="admin-card-header">
            <div style="display: flex; gap: 8px;">
                <a href="/admin/contacts.php" class="btn-action <?= empty($statusFilter) ? 'btn-edit' : 'btn-view' ?>">All (<?= $totalInquiries ?>)</a>
                <a href="/admin/contacts.php?status=unread" class="btn-action <?= $statusFilter === 'unread' ? 'btn-edit' : 'btn-view' ?>">Unread</a>
                <a href="/admin/contacts.php?status=read" class="btn-action <?= $statusFilter === 'read' ? 'btn-edit' : 'btn-view' ?>">Read</a>
                <a href="/admin/contacts.php?status=replied" class="btn-action <?= $statusFilter === 'replied' ? 'btn-edit' : 'btn-view' ?>">Replied</a>
            </div>
        </div>

        <div class="admin-card-body" style="padding: 0;">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Sender</th>
                        <th>Subject / Preview</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($inquiries)): ?>
                        <?php foreach ($inquiries as $inq): ?>
                            <tr style="<?= ($selectedInquiry && $selectedInquiry['id'] == $inq['id']) ? 'background: #f0f9ff;' : '' ?>">
                                <td>
                                    <strong><?= e($inq['name']) ?></strong>
                                    <small style="display: block; color: var(--admin-text-muted);"><?= e($inq['email']) ?></small>
                                </td>
                                <td>
                                    <strong><?= e($inq['subject'] ?: 'Trade Inquiry') ?></strong>
                                    <small style="display: block; color: var(--admin-text-muted);"><?= e(mb_strimwidth($inq['message'], 0, 50, '...')) ?></small>
                                </td>
                                <td>
                                    <small style="color: var(--admin-text-muted);"><?= date('M d, Y H:i', strtotime($inq['created_at'])) ?></small>
                                </td>
                                <td>
                                    <span class="status-badge badge-<?= e($inq['status']) ?>">
                                        <?= ucfirst($inq['status']) ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <a href="/admin/contacts.php?id=<?= $inq['id'] ?>" class="btn-action btn-edit">View</a>
                                        <a href="/admin/contacts.php?action=delete&id=<?= $inq['id'] ?>" class="btn-action btn-delete" onclick="return confirm('Delete this inquiry?');">✕</a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" style="text-align: center; padding: 40px; color: var(--admin-text-muted);">
                                No contact inquiries found.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Selected Inquiry Detail Pane -->
    <?php if ($selectedInquiry): ?>
        <div class="admin-card">
            <div class="admin-card-header">
                <h3>Message from <?= e($selectedInquiry['name']) ?></h3>
                <a href="/admin/contacts.php" class="btn-action btn-view">✕ Close</a>
            </div>
            <div class="admin-card-body">
                <div style="background: #f8fafc; border-radius: 8px; padding: 18px; margin-bottom: 20px; border: 1px solid var(--admin-border);">
                    <div style="margin-bottom: 8px;">
                        <span style="font-weight: 700; color: var(--admin-text-muted); font-size: 13px;">FROM:</span>
                        <strong style="color: var(--admin-text);"><?= e($selectedInquiry['name']) ?></strong>
                        &lt;<a href="mailto:<?= e($selectedInquiry['email']) ?>"><?= e($selectedInquiry['email']) ?></a>&gt;
                    </div>
                    <?php if (!empty($selectedInquiry['phone'])): ?>
                        <div style="margin-bottom: 8px;">
                            <span style="font-weight: 700; color: var(--admin-text-muted); font-size: 13px;">PHONE:</span>
                            <a href="tel:<?= e($selectedInquiry['phone']) ?>"><?= e($selectedInquiry['phone']) ?></a>
                        </div>
                    <?php endif; ?>
                    <div style="margin-bottom: 8px;">
                        <span style="font-weight: 700; color: var(--admin-text-muted); font-size: 13px;">SUBJECT:</span>
                        <strong><?= e($selectedInquiry['subject'] ?: 'Inquiry from website') ?></strong>
                    </div>
                    <div>
                        <span style="font-weight: 700; color: var(--admin-text-muted); font-size: 13px;">RECEIVED:</span>
                        <?= date('l, F j, Y \a\t g:i A', strtotime($selectedInquiry['created_at'])) ?>
                    </div>
                </div>

                <div style="margin-bottom: 25px;">
                    <h4 style="font-size: 14px; font-weight: 700; color: var(--admin-text-muted); margin-bottom: 10px; text-transform: uppercase;">Message Content:</h4>
                    <div style="background: #ffffff; border: 1px solid var(--admin-border); border-radius: 8px; padding: 20px; line-height: 1.7; font-size: 15px; white-space: pre-wrap; color: #1e293b;">
                        <?= e($selectedInquiry['message']) ?>
                    </div>
                </div>

                <!-- Action Controls -->
                <div style="display: flex; gap: 10px; flex-wrap: wrap; border-top: 1px solid var(--admin-border); padding-top: 18px;">
                    <a href="mailto:<?= e($selectedInquiry['email']) ?>?subject=Re: <?= urlencode($selectedInquiry['subject'] ?: 'Global Connect Inquiry') ?>" class="btn-admin-primary" style="text-decoration: none;">
                        ✉ Reply via Email
                    </a>
                    <a href="/admin/contacts.php?action=mark_replied&id=<?= $selectedInquiry['id'] ?>" class="btn-action btn-edit">
                        ✓ Mark as Replied
                    </a>
                    <a href="/admin/contacts.php?action=delete&id=<?= $selectedInquiry['id'] ?>" class="btn-action btn-delete" onclick="return confirm('Delete this inquiry?');">
                        Delete Inquiry
                    </a>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
