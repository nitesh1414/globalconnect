<?php
/**
 * Global Connect - Delete Executive Contact Handler
 */
require_once __DIR__ . '/includes/auth.php';

$id = (int)($_GET['id'] ?? $_POST['id'] ?? 0);
$token = $_GET['csrf_token'] ?? $_POST['csrf_token'] ?? '';

if (!verify_csrf($token)) {
    set_flash('error', 'CSRF validation failed. Action aborted.');
    header('Location: ' . admin_url('executives.php'));
    exit;
}

if ($id <= 0) {
    set_flash('error', 'Invalid executive ID.');
    header('Location: ' . admin_url('executives.php'));
    exit;
}

$executive = get_executive_by_id($id);
if (!$executive) {
    set_flash('error', 'Executive contact not found.');
    header('Location: ' . admin_url('executives.php'));
    exit;
}

try {
    $db = get_db();
    $stmt = $db->prepare("DELETE FROM executives WHERE id = ?");
    $stmt->execute([$id]);

    set_flash('success', 'Executive "' . $executive['name'] . '" has been removed successfully.');
} catch (Exception $e) {
    set_flash('error', 'Failed to delete executive: ' . $e->getMessage());
}

header('Location: ' . admin_url('executives.php'));
exit;
