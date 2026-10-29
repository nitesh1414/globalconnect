<?php
/**
 * Global Connect - Core Helper Functions & Dynamic Path Resolution
 * Ensures site runs in any path: root, subdirectory (/mysite/, /globalconnect/), subdomain, or localhost
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/db.php';

function get_db(): PDO {
    return Database::getConnection();
}

/**
 * Get Dynamic Base URL / Path
 * Automatically detects domain, subfolder, port, and protocol
 */
function base_url(string $path = ''): string {
    static $base = null;
    if ($base === null) {
        $script = $_SERVER['SCRIPT_NAME'] ?? $_SERVER['PHP_SELF'] ?? '';
        $dir = str_replace('\\', '/', dirname($script));
        
        // Remove /admin, /includes, /config from the end
        $dir = preg_replace('#/(admin|includes|config)$#i', '', $dir);
        $dir = rtrim($dir, '/');
        
        // Ensure leading slash if not empty
        if (!empty($dir) && $dir[0] !== '/') {
            $dir = '/' . $dir;
        }

        // Fallback check against DOCUMENT_ROOT if script is in root but app is in subfolder
        if (empty($dir) && isset($_SERVER['DOCUMENT_ROOT'])) {
            $appRoot = str_replace('\\', '/', realpath(__DIR__ . '/..') ?: dirname(__DIR__));
            $docRoot = str_replace('\\', '/', realpath($_SERVER['DOCUMENT_ROOT']) ?: $_SERVER['DOCUMENT_ROOT']);
            if (!empty($docRoot) && strpos($appRoot, $docRoot) === 0) {
                $sub = substr($appRoot, strlen($docRoot));
                $sub = rtrim(str_replace('\\', '/', $sub), '/');
                if (!empty($sub)) {
                    $dir = ($sub[0] === '/') ? $sub : '/' . $sub;
                }
            }
        }

        // Fallback check against REQUEST_URI for known subfolders if SCRIPT_NAME is /index.php
        if (empty($dir) && isset($_SERVER['REQUEST_URI'])) {
            $reqUri = explode('?', $_SERVER['REQUEST_URI'])[0];
            if (preg_match('#^/([^/]+)(/|$)#', $reqUri, $m)) {
                $segment = $m[1];
                if (!in_array($segment, ['assets', 'uploads', 'api']) && !preg_match('#\.(php|html|css|js|png|jpg|svg)$#i', $segment)) {
                    // Check if this segment corresponds to folder name
                    $appFolder = basename(dirname(__DIR__));
                    if ($segment === $appFolder || in_array($segment, ['mysite', 'globalconnect', 'site'])) {
                        $dir = '/' . $segment;
                    }
                }
            }
        }

        $base = $dir;
    }
    
    $cleanPath = ltrim($path, '/');
    if (empty($cleanPath)) {
        return empty($base) ? '/' : $base . '/';
    }
    return (empty($base) ? '/' : $base . '/') . $cleanPath;
}

/**
 * Public Page URL Helper
 */
function url(string $path = ''): string {
    return base_url($path);
}

/**
 * Static Asset URL Helper
 */
function asset(string $path = ''): string {
    return base_url('assets/' . ltrim($path, '/'));
}

/**
 * Image URL Helper
 */
function image_url(string $filename = ''): string {
    if (empty($filename)) {
        return asset('images/logo.svg');
    }
    if (preg_match('/^https?:\/\//i', $filename)) {
        return $filename;
    }
    if (strpos($filename, 'assets/') === 0 || strpos($filename, '/assets/') === 0) {
        return base_url(ltrim($filename, '/'));
    }
    if (strpos($filename, 'uploads/') === 0 || strpos($filename, '/uploads/') === 0) {
        return base_url(ltrim($filename, '/'));
    }
    // Check if file exists in uploads
    if (file_exists(__DIR__ . '/../uploads/' . $filename)) {
        return base_url('uploads/' . $filename);
    }
    return base_url('assets/images/' . $filename);
}

/**
 * Admin URL Helper
 */
function admin_url(string $path = ''): string {
    return base_url('admin/' . ltrim($path, '/'));
}

/**
 * Check if current page is active
 */
function is_active(string $name): bool {
    $script = basename($_SERVER['SCRIPT_NAME'] ?? '', '.php');
    if ($name === 'home' && ($script === 'index' || empty($script))) {
        return true;
    }
    return $script === $name;
}

/**
 * Check if category is active
 */
function is_category_active(string $cat): bool {
    $script = basename($_SERVER['SCRIPT_NAME'] ?? '', '.php');
    if ($script === $cat) {
        return true;
    }
    if ($script === 'page') {
        $slug = $_GET['slug'] ?? '';
        if ($slug) {
            $page = get_page_by_slug($slug);
            return ($page['category_slug'] ?? '') === $cat;
        }
    }
    return false;
}

/**
 * Get setting value from database
 */
function get_setting(string $key, string $default = ''): string {
    static $settingsCache = null;
    if ($settingsCache === null) {
        try {
            $db = get_db();
            $stmt = $db->query("SELECT setting_key, setting_value FROM settings");
            $settingsCache = [];
            while ($row = $stmt->fetch()) {
                $settingsCache[$row['setting_key']] = $row['setting_value'];
            }
        } catch (Exception $e) {
            $settingsCache = [];
        }
    }
    return $settingsCache[$key] ?? $default;
}

/**
 * Set setting value in database (Universal UPSERT)
 */
function set_setting(string $key, string $value): bool {
    try {
        $db = get_db();
        $driver = Database::getDriver();
        if ($driver === 'sqlite') {
            $stmt = $db->prepare("INSERT INTO settings (setting_key, setting_value, updated_at) VALUES (?, ?, CURRENT_TIMESTAMP) ON CONFLICT(setting_key) DO UPDATE SET setting_value = excluded.setting_value, updated_at = CURRENT_TIMESTAMP");
            return $stmt->execute([$key, $value]);
        } else {
            $stmt = $db->prepare("INSERT INTO settings (setting_key, setting_value, updated_at) VALUES (?, ?, CURRENT_TIMESTAMP) ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value), updated_at = CURRENT_TIMESTAMP");
            return $stmt->execute([$key, $value]);
        }
    } catch (Exception $e) {
        try {
            $del = $db->prepare("DELETE FROM settings WHERE setting_key = ?");
            $del->execute([$key]);
            $ins = $db->prepare("INSERT INTO settings (setting_key, setting_value, updated_at) VALUES (?, ?, CURRENT_TIMESTAMP)");
            return $ins->execute([$key, $value]);
        } catch (Exception $ex) {
            return false;
        }
    }
}


/**
 * Get single page by slug
 */
function get_page_by_slug(string $slug): ?array {
    try {
        $db = get_db();
        $stmt = $db->prepare("SELECT p.*, c.name as category_name, c.slug as category_slug FROM pages p LEFT JOIN categories c ON p.category_id = c.id WHERE p.slug = ? AND p.status = 'published' LIMIT 1");
        $stmt->execute([$slug]);
        $page = $stmt->fetch();
        return $page ?: null;
    } catch (Exception $e) {
        return null;
    }
}

/**
 * Get pages by category slug
 */
function get_pages_by_category(string $categorySlug, bool $publishedOnly = true): array {
    try {
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
        return $stmt->fetchAll() ?: [];
    } catch (Exception $e) {
        return [];
    }
}

/**
 * Get all categories
 */
function get_all_categories(): array {
    try {
        $db = get_db();
        $stmt = $db->query("SELECT * FROM categories ORDER BY menu_order ASC, id ASC");
        return $stmt->fetchAll() ?: [];
    } catch (Exception $e) {
        return [];
    }
}

/**
 * Escape HTML output
 */
function e(?string $string): string {
    return htmlspecialchars((string)$string, ENT_QUOTES, 'UTF-8');
}

/**
 * Clean / Sanitize input string
 */
function clean(?string $input): string {
    return trim(strip_tags((string)$input));
}

/**
 * Slugify text
 */
function slugify(string $text): string {
    $text = preg_replace('~[^\pL\d]+~u', '-', $text);
    $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
    $text = preg_replace('~[^-\w]+~', '', $text);
    $text = trim($text, '-');
    $text = preg_replace('~-+~', '-', $text);
    $text = strtolower($text);
    return empty($text) ? 'item-' . time() : $text;
}

/**
 * Generate CSRF token
 */
function csrf_token(): string {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * CSRF hidden field helper
 */
function csrf_field(): string {
    return '<input type="hidden" name="csrf_token" value="' . e(csrf_token()) . '">';
}

/**
 * Verify CSRF token
 */
function verify_csrf(?string $token): bool {
    if (empty($token) || empty($_SESSION['csrf_token'])) {
        return false;
    }
    return hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Set Flash message
 */
function set_flash(string $type, string $message): void {
    $_SESSION['flash_message'] = [
        'type' => $type,
        'message' => $message
    ];
}

/**
 * Get Flash message
 */
function get_flash(): ?array {
    if (isset($_SESSION['flash_message'])) {
        $msg = $_SESSION['flash_message'];
        unset($_SESSION['flash_message']);
        return $msg;
    }
    return null;
}

/**
 * Check if user is logged in
 */
function is_logged_in(): bool {
    return !empty($_SESSION['admin_user_id']);
}

/**
 * Require authentication
 */
function require_auth(): void {
    if (!is_logged_in()) {
        set_flash('warning', 'Please log in to access the administration panel.');
        header('Location: ' . admin_url('login.php'));
        exit;
    }
}

/**
 * Get currently authenticated admin user
 */
function current_user(): ?array {
    if (!is_logged_in()) {
        return null;
    }
    try {
        $db = get_db();
        $stmt = $db->prepare("SELECT id, username, email, full_name, role FROM users WHERE id = ?");
        $stmt->execute([$_SESSION['admin_user_id']]);
        return $stmt->fetch() ?: null;
    } catch (Exception $e) {
        return null;
    }
}

/**
 * Upload Image Helper
 */
function upload_image(array $file): ?string {
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return null;
    }

    $allowedMimes = ['image/jpeg', 'image/png', 'image/webp', 'image/svg+xml', 'image/gif'];
    $allowedExts = ['jpg', 'jpeg', 'png', 'webp', 'svg', 'gif'];

    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);

    if (!in_array($ext, $allowedExts) && !in_array($mime, $allowedMimes)) {
        return null;
    }

    if ($file['size'] > 8 * 1024 * 1024) {
        return null;
    }

    $uploadDir = __DIR__ . '/../uploads/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    $filename = time() . '_' . preg_replace('/[^a-zA-Z0-9_\.-]/', '_', basename($file['name']));
    $targetPath = $uploadDir . $filename;

    if (move_uploaded_file($file['tmp_name'], $targetPath)) {
        return $filename;
    }

    return null;
}

/**
 * Get Hero Slides
 */
function get_hero_slides(bool $onlyActive = true): array {
    try {
        $db = get_db();
        $sql = "SELECT * FROM hero_slides";
        if ($onlyActive) {
            $sql .= " WHERE status = 'active'";
        }
        $sql .= " ORDER BY sort_order ASC, id ASC";
        $stmt = $db->query($sql);
        return $stmt->fetchAll() ?: [];
    } catch (Exception $e) {
        return [];
    }
}

/**
 * Get Single Hero Slide by ID
 */
function get_hero_slide_by_id(int $id): ?array {
    try {
        $db = get_db();
        $stmt = $db->prepare("SELECT * FROM hero_slides WHERE id = ? LIMIT 1");
        $stmt->execute([$id]);
        $slide = $stmt->fetch();
        return $slide ?: null;
    } catch (Exception $e) {
        return null;
    }
}

