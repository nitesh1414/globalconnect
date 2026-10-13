<?php
/**
 * Global Connect - Delete CMS Page
 */
require_once __DIR__ . '/includes/auth.php';

$id = (int)($_GET['id'] ?? 0);

if ($id > 0) {
    $db = get_db();
    $stmt = $db->prepare("SELECT * FROM pages WHERE id = ? LIMIT 1");
    $stmt->execute([$id]);
    $page = $stmt->fetch();

    if ($page && empty($page['is_system'])) {
        $del = $db->prepare("DELETE FROM pages WHERE id = ?");
        $del->execute([$id]);
        set_flash('success', "Page '{$page['title']}' deleted successfully.");
    } else {
        set_flash('danger', "System pages cannot be deleted.");
    }
}

header('Location: /admin/pages.php');
exit;
