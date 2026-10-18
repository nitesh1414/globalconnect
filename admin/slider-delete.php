<?php
/**
 * Global Connect - Delete Hero Slide
 */
require_once __DIR__ . '/includes/auth.php';

$db = get_db();
$id = (int)($_GET['id'] ?? 0);

if ($id > 0) {
    try {
        $stmt = $db->prepare("DELETE FROM hero_slides WHERE id = ?");
        $stmt->execute([$id]);
        set_flash('success', 'Hero slide deleted successfully.');
    } catch (Exception $e) {
        set_flash('error', 'Error deleting slide: ' . $e->getMessage());
    }
}

header('Location: ' . admin_url('sliders.php'));
exit;
