<?php
/**
 * Global Connect - Dynamic Page Viewer
 * Displays subpages for Trading, Finance, and custom CMS pages
 */
require_once __DIR__ . '/includes/functions.php';

$slug = clean($_GET['slug'] ?? '');

if (empty($slug)) {
    header('Location: ' . url('index.php'));
    exit;
}

$page = get_page_by_slug($slug);

if (!$page) {
    http_response_code(404);
    $pageTitle = 'Page Not Found | ' . get_setting('company_name', 'Global Connect');
    require_once __DIR__ . '/includes/header.php';
    ?>
    <div class="page-banner reveal-up">
        <div class="container">
            <h1 class="page-banner-title">Page Not Found</h1>
            <p class="page-banner-subtitle">The requested page could not be found.</p>
        </div>
    </div>
    <div class="page-content-wrapper">
        <div class="container text-center" style="padding: 60px 0; text-align: center;">
            <h2 style="font-size: 32px; color: var(--primary-color);">404 - Page Not Found</h2>
            <p style="color: var(--text-muted); margin: 20px 0;">The page you are looking for may have been moved or updated.</p>
            <a href="<?= url('index.php') ?>" class="btn-primary shimmer-btn">Return to Homepage</a>
        </div>
    </div>
    <?php
    require_once __DIR__ . '/includes/footer.php';
    exit;
}

$pageTitle = ($page['meta_title'] ?: $page['title']) . ' | ' . get_setting('company_name', 'Global Connect');
$pageMetaDesc = $page['meta_description'] ?: ($page['summary'] ?: $page['title']);

// Sibling subpages for sidebar navigation
$categorySlug = $page['category_slug'] ?? 'trading';
$categoryName = $page['category_name'] ?? 'Services';
$siblingPages = get_pages_by_category($categorySlug);

// Clean content to ensure image sources are resolved properly
$contentHtml = $page['content'] ?? '';
$contentHtml = preg_replace_callback('/<img[^>]+src=([\'"])([^\'"]+)([\'"])/i', function($matches) {
    $src = $matches[2];
    $resolved = image_url($src);
    return str_replace($src, $resolved, $matches[0]);
}, $contentHtml);

require_once __DIR__ . '/includes/header.php';
?>

<!-- Page Banner -->
<div class="page-banner reveal-up">
    <div class="container">
        <h1 class="page-banner-title"><?= e($page['title']) ?></h1>
        <p class="page-banner-subtitle"><?= e($page['banner_subtitle'] ?: ($page['subtitle'] ?: 'Global Connect ' . $categoryName)) ?></p>
        <div class="breadcrumb-nav">
            <a href="<?= url('index.php') ?>">Home</a> 
            <span>/</span> 
            <a href="<?= url($categorySlug . '.php') ?>"><?= e($categoryName) ?></a> 
            <span>/</span> 
            <span><?= e($page['title']) ?></span>
        </div>
    </div>
</div>

<div class="page-content-wrapper">
    <div class="container">
        <div class="dynamic-page-layout">
            <!-- Main Content Area -->
            <article class="page-main-content reveal-left">
                <?php if (!empty($page['featured_image'])): ?>
                    <div class="page-featured-image-wrapper">
                        <img src="<?= image_url($page['featured_image']) ?>" alt="<?= e($page['title']) ?>" class="img-fluid page-featured-img">
                    </div>
                <?php endif; ?>

                <?php if (!empty($page['subtitle'])): ?>
                    <p class="page-lead-subtitle"><?= e($page['subtitle']) ?></p>
                <?php endif; ?>

                <div class="wysiwyg-content-body">
                    <?= $contentHtml ?>
                </div>

                <!-- Call to action inside page -->
                <div class="page-inquiry-box reveal-up">
                    <h3 style="color: var(--primary-color); margin-bottom: 8px; font-size: 20px;">Ready to Proceed with <?= e($page['title']) ?>?</h3>
                    <p style="color: var(--text-muted); font-size: 14px; margin-bottom: 20px;">
                        Connect directly with our Singapore desk for allocations, standard contracts, or customized banking structures.
                    </p>
                    <a href="<?= url('contact.php') ?>" class="btn-primary shimmer-btn">Request Quotation &amp; Term Sheet →</a>
                </div>
            </article>

            <!-- Sidebar Navigation -->
            <aside class="page-sidebar reveal-right">
                <div class="sidebar-box">
                    <h4 class="sidebar-box-title"><?= e($categoryName) ?> Portfolio</h4>
                    <ul class="sidebar-category-list">
                        <?php foreach ($siblingPages as $sp): ?>
                            <li class="<?= $sp['slug'] === $slug ? 'active' : '' ?>">
                                <a href="<?= url('page.php?slug=' . urlencode($sp['slug'])) ?>">
                                    <span><?= e($sp['title']) ?></span>
                                    <span class="chevron">›</span>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>

                <div class="sidebar-box sidebar-cta-box" style="margin-top: 25px;">
                    <h4 style="color: #ffffff; font-size: 18px; margin-bottom: 10px;">Global Trade Desk</h4>
                    <p style="color: #cbd5e1; font-size: 13px; line-height: 1.6; margin-bottom: 15px;">
                        Reach our Singapore headquarters directly for trade inquiries or financing support.
                    </p>
                    <div style="font-size: 13px; color: #ffffff; margin-bottom: 15px;">
                        <div>📞 <?= e(get_setting('header_phone_sg', '(65) 89451100')) ?></div>
                        <div style="margin-top: 4px;">✉ <?= e(get_setting('contact_email_primary', 'admin@globalconnectsg.com')) ?></div>
                    </div>
                    <a href="<?= url('contact.php') ?>" class="btn-gold shimmer-btn" style="width: 100%; justify-content: center; font-size: 13.5px; padding: 10px 18px;">
                        Contact Singapore Desk
                    </a>
                </div>
            </aside>
        </div>
    </div>
</div>

<style>
.dynamic-page-layout {
    display: grid;
    grid-template-columns: 2.2fr 1fr;
    gap: 45px;
    align-items: flex-start;
}
.page-featured-image-wrapper {
    margin-bottom: 30px;
    border-radius: var(--radius-md);
    overflow: hidden;
    box-shadow: var(--shadow-md);
    border: 1px solid var(--border-color);
}
.page-featured-img {
    width: 100%;
    max-height: 420px;
    object-fit: cover;
}
.page-lead-subtitle {
    font-size: 18px;
    font-weight: 600;
    color: var(--secondary-color);
    line-height: 1.6;
    margin-bottom: 25px;
    border-left: 4px solid var(--accent-gold);
    padding-left: 15px;
}
.wysiwyg-content-body {
    color: var(--text-main);
    font-size: 15px;
    line-height: 1.8;
}
.wysiwyg-content-body h2 {
    font-size: 24px;
    color: var(--primary-color);
    margin: 30px 0 15px;
}
.wysiwyg-content-body h3 {
    font-size: 20px;
    color: var(--primary-color);
    margin: 25px 0 12px;
}
.wysiwyg-content-body p {
    margin-bottom: 18px;
}
.wysiwyg-content-body ul, .wysiwyg-content-body ol {
    margin: 15px 0 20px 25px;
}
.wysiwyg-content-body li {
    margin-bottom: 8px;
}
.page-inquiry-box {
    margin-top: 45px;
    background: #f8fafc;
    border: 1.5px solid #e2e8f0;
    border-radius: var(--radius-md);
    padding: 30px;
}
.sidebar-box {
    background: #ffffff;
    border: 1px solid var(--border-color);
    border-radius: var(--radius-md);
    padding: 24px;
    box-shadow: var(--shadow-sm);
}
.sidebar-box-title {
    font-size: 16px;
    font-weight: 700;
    color: var(--primary-color);
    margin-bottom: 16px;
    border-bottom: 2px solid var(--accent-gold);
    padding-bottom: 8px;
}
.sidebar-category-list {
    list-style: none;
    display: flex;
    flex-direction: column;
    gap: 6px;
}
.sidebar-category-list li a {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 10px 14px;
    border-radius: var(--radius-sm);
    color: var(--text-main);
    font-size: 14px;
    font-weight: 500;
    background: var(--bg-light);
    transition: var(--transition-fast);
}
.sidebar-category-list li.active a,
.sidebar-category-list li a:hover {
    background: var(--primary-color);
    color: #ffffff;
}
.sidebar-cta-box {
    background: var(--bg-navy-gradient);
    border: 1px solid rgba(212, 175, 55, 0.3);
}
@media (max-width: 900px) {
    .dynamic-page-layout {
        grid-template-columns: 1fr;
    }
}
</style>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
