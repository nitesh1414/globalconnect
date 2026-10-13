<?php
/**
 * Global Connect - Core Helper Functions
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/db.php';

function get_db(): PDO {
    return Database::getConnection();
}

/**
 * Get setting value from database
 */
function get_setting(string $key, string $default = ''): string {
    static $settingsCache = null;
    if ($settingsCache === null) {
        $db = get_db();
        $stmt = $db->query("SELECT setting_key, setting_value FROM settings");
        $settingsCache = [];
        while ($row = $stmt->fetch()) {
            $settingsCache[$row['setting_key']] = $row['setting_value'];
        }
    }
    return $settingsCache[$key] ?? $default;
}

/**
 * Get single page by slug
 */
function get_page_by_slug(string $slug): ?array {
    $db = get_db();
    $stmt = $db->prepare("SELECT p.*, c.name as category_name, c.slug as category_slug FROM pages p LEFT JOIN categories c ON p.category_id = c.id WHERE p.slug = ? AND p.status = 'published' LIMIT 1");
    $stmt->execute([$slug]);
    $page = $stmt->fetch();
    return $page ?: null;
}

/**
 * Get pages by category slug
 */
function get_pages_by_category(string $categorySlug, bool $publishedOnly = true): array {
    $db = get_db();
    $statusSql = $publishedOnly ? "AND p.status = 'published'" : "";
    $stmt = $db->prepare("
        SELECT p.*, c.name as category_name, c.slug as category_slug 
        FROM pages p 
        JOIN categories c ON p.category_id = c.id 
        WHERE c.slug = ? $statusSql 
        ORDER BY p.menu_order ASC, p.id ASC
    ");
    $stmt->execute([$categorySlug]);
    return $stmt->fetchAll();
}

/**
 * Get all categories
 */
function get_all_categories(): array {
    $db = get_db();
    $stmt = $db->query("SELECT * FROM categories ORDER BY menu_order ASC, id ASC");
    return $stmt->fetchAll();
}

/**
 * Escape HTML output
 */
function e(?string $string): string {
    return htmlspecialchars($string ?? '', ENT_QUOTES, 'UTF-8');
}

/**
 * Clean and sanitize string input
 */
function clean(string $string): string {
    return trim(strip_tags($string));
}

/**
 * Generate URL-friendly slug
 */
function slugify(string $text): string {
    $text = preg_replace('~[^\pL\d]+~u', '-', $text);
    $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
    $text = preg_replace('~[^-\w]+~', '', $text);
    $text = trim($text, '-');
    $text = preg_replace('~-+~', '-', $text);
    $text = strtolower($text);
    return empty($text) ? 'n-a-' . time() : $text;
}

/**
 * Check if current page is active for navbar
 */
function is_active(string $path): bool {
    $currentUri = $_SERVER['REQUEST_URI'] ?? '';
    $currentScript = basename($_SERVER['SCRIPT_NAME'] ?? '');
    $slug = $_GET['slug'] ?? '';

    if ($path === 'home' || $path === '/' || $path === 'index.php') {
        return ($currentScript === 'index.php' || $currentUri === '/' || $currentUri === '/index.php') && empty($slug);
    }

    if (!empty($slug) && $slug === $path) {
        return true;
    }

    if ($currentScript === $path || $currentScript === $path . '.php') {
        return true;
    }

    if (strpos($currentUri, $path) !== false) {
        return true;
    }

    return false;
}

/**
 * Check if category is active
 */
function is_category_active(string $categorySlug): bool {
    $currentScript = basename($_SERVER['SCRIPT_NAME'] ?? '');
    if ($currentScript === $categorySlug . '.php') {
        return true;
    }
    $slug = $_GET['slug'] ?? '';
    if (!empty($slug)) {
        $page = get_page_by_slug($slug);
        if ($page && ($page['category_slug'] ?? '') === $categorySlug) {
            return true;
        }
    }
    return false;
}

/**
 * Set Flash message
 */
function set_flash(string $type, string $message): void {
    $_SESSION['flash'] = [
        'type' => $type, // success, danger, warning, info
        'message' => $message
    ];
}

/**
 * Get and clear Flash message
 */
function get_flash(): ?array {
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

/**
 * CSRF Protection
 */
function csrf_token(): string {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrf_field(): string {
    return '<input type="hidden" name="csrf_token" value="' . e(csrf_token()) . '">';
}

function verify_csrf(?string $token): bool {
    return !empty($token) && !empty($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Authentication Helpers
 */
function is_logged_in(): bool {
    return !empty($_SESSION['admin_user_id']);
}

function current_user(): ?array {
    if (!is_logged_in()) {
        return null;
    }
    $db = get_db();
    $stmt = $db->prepare("SELECT id, username, email, full_name, role FROM users WHERE id = ? LIMIT 1");
    $stmt->execute([$_SESSION['admin_user_id']]);
    return $stmt->fetch() ?: null;
}

function require_auth(): void {
    if (!is_logged_in()) {
        set_flash('warning', 'Please log in to access the administration panel.');
        header('Location: /admin/login.php');
        exit;
    }
}
