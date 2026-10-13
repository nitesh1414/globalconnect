<?php
/**
 * Global Connect - Dynamic Page Viewer
 * Displays subpages for Trading, Finance, and custom CMS pages
 */
require_once __DIR__ . '/includes/functions.php';

$slug = clean($_GET['slug'] ?? '');

if (empty($slug)) {
    header('Location: /index.php');
    exit;
}

$page = get_page_by_slug($slug);

if (!$page) {
    http_response_code(404);
    $pageTitle = 'Page Not Found | Global Connect';
    require_once __DIR__ . '/includes/header.php';
    ?>
    <div class="page-banner">
        <div class="container">
            <h1 class="page-banner-title">Page Not Found</h1>
            <p class="page-banner-subtitle">The requested page could not be found.</p>
        </div>
    </div>
    <div class="page-content-wrapper">
        <div class="container text-center" style="padding: 60px 0;">
            <h2>404 - Page Not Found</h2>
            <p class="text-muted" style="margin: 20px 0;">The page you are looking for may have been moved or removed.</p>
            <a href="/index.php" class="btn-primary">Return to Home</a>
        </div>
    </div>
    <?php
    require_once __DIR__ . '/includes/footer.php';
    exit;
}

$pageTitle = ($page['meta_title'] ?: $page['title']) . ' | Global Connect';
$pageMetaDesc = $page['meta_description'] ?: ($page['summary'] ?: $page['title']);

// Sibling subpages for sidebar navigation
$categorySlug = $page['category_slug'] ?? 'trading';
$categoryName = $page['category_name'] ?? 'Services';
$siblingPages = get_pages_by_category($categorySlug);

require_once __DIR__ . '/includes/header.php';
?>

<!-- Page Banner -->
<div class="page-banner">
    <div class="container">
        <h1 class="page-banner-title"><?= e($page['title']) ?></h1>
        <p class="page-banner-subtitle"><?= e($page['banner_subtitle'] ?: ($page['subtitle'] ?: 'Global Connect ' . $categoryName)) ?></p>
        <div class="breadcrumb-nav">
            <a href="/index.php">Home</a> 
            <span>/</span> 
            <a href="/<?= e($categorySlug) ?>.php"><?= e($categoryName) ?></a> 
            <span>/</span> 
            <span><?= e($page['title']) ?></span>
        </div>
    </div>
</div>

<div class="page-content-wrapper">
    <div class="container">
        <div style="display: grid; grid-template-columns: 2.4fr 1fr; gap: 40px;" class="page-layout-grid">
            <!-- Main Content Area -->
            <div class="content-body">
                <?= $page['content'] ?>
            </div>

            <!-- Sidebar Navigation -->
            <div>
                <!-- Category Submenu Box -->
                <div class="contact-card" style="margin-bottom: 30px;">
                    <h4 style="font-size: 17px; margin-bottom: 15px;">
                        <span>📂</span> <?= e($categoryName) ?> Portfolio
                    </h4>
                    <ul class="footer-links" style="margin-left: 0;">
                        <?php foreach ($siblingPages as $sp): ?>
                            <li style="margin-bottom: 12px; border-bottom: 1px solid var(--border-color); padding-bottom: 8px;">
                                <a href="/page.php?slug=<?= e($sp['slug']) ?>" style="font-weight: <?= $sp['slug'] === $slug ? '700' : '500' ?>; color: <?= $sp['slug'] === $slug ? 'var(--secondary-color)' : 'var(--text-main)' ?>; display: flex; justify-content: space-between; align-items: center;">
                                    <span><?= e($sp['title']) ?></span>
                                    <span><?= $sp['slug'] === $slug ? '●' : '›' ?></span>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>

                <!-- Fast Contact Inquiry Box -->
                <div class="contact-card" style="background: linear-gradient(135deg, #0b2545, #022c43); color: #ffffff;">
                    <h4 style="color: #ffffff; border-color: rgba(255,255,255,0.15);">Need Direct Assistance?</h4>
                    <p style="color: #cbd5e1; font-size: 14px; margin-bottom: 20px;">
                        Our priority desk is available to assist with <?= e($page['title']) ?> requirements and trade structuring.
                    </p>
                    <div style="margin-bottom: 15px;">
                        <small style="color: var(--accent-gold); display: block; font-weight: 700;">Singapore Direct:</small>
                        <a href="tel:+6589451100" style="color: #ffffff; font-weight: 700;">(65) 89451100</a>
                    </div>
                    <div style="margin-bottom: 20px;">
                        <small style="color: var(--accent-gold); display: block; font-weight: 700;">Direct Email:</small>
                        <a href="mailto:admin@globalconnectsg.com" style="color: #ffffff; font-weight: 600;">admin@globalconnectsg.com</a>
                    </div>
                    <a href="/contact.php" class="btn-gold" style="width: 100%; text-align: center;">
                        Inquire Now →
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
@media (max-width: 900px) {
    .page-layout-grid {
        grid-template-columns: 1fr !important;
    }
}
</style>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
