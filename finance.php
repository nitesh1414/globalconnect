<?php
/**
 * Global Connect - Finance Overview Page
 * Dynamically displays all Finance services/pages from DB
 */
require_once __DIR__ . '/includes/functions.php';

$pageTitle = 'Finance | ' . get_setting('company_name', 'Global Connect');
$pageMetaDesc = 'Comprehensive international trade finance, letters of credit (LC / SBLC), bank guarantees, structured working capital, and transaction support.';

require_once __DIR__ . '/includes/header.php';

$financePages = get_pages_by_category('finance');
?>

<!-- Page Banner -->
<div class="page-banner reveal-up">
    <div class="container">
        <h1 class="page-banner-title">Finance</h1>
        <p class="page-banner-subtitle">Institutional trade banking, letter of credit structuring, and capital solutions</p>
        <div class="breadcrumb-nav">
            <a href="<?= url('index.php') ?>">Home</a> <span>/</span> <span>Finance</span>
        </div>
    </div>
</div>

<div class="page-content-wrapper">
    <div class="container">
        <div class="section-header reveal-left" style="text-align: left; max-width: 900px; margin-bottom: 45px;">
            <span class="section-subtitle">Our Financial Solutions</span>
            <h2 class="section-title">
                Comprehensive Trade Finance, Banking Support &amp; Cross-Border Structuring
            </h2>
            <p class="section-lead">
                Leveraging relationships with top-tier international partner banks across Singapore, Hong Kong, London, and Zurich to provide credit enhancement, liquidity, and risk containment.
            </p>
        </div>

        <div class="services-grid">
            <?php if (!empty($financePages)): ?>
                <?php foreach ($financePages as $idx => $p): ?>
                    <div class="service-card reveal-up hover-lift" style="transition-delay: <?= ($idx * 0.1) ?>s;">
                        <div class="service-card-image">
                            <a href="<?= url('page.php?slug=' . urlencode($p['slug'])) ?>">
                                <img src="<?= image_url($p['featured_image'] ?: 'finance-home.jpg') ?>" alt="<?= e($p['title']) ?>" loading="lazy">
                            </a>
                            <div class="service-badge badge-gold">Finance</div>
                        </div>
                        <div class="service-card-body">
                            <h3 class="service-card-title">
                                <a href="<?= url('page.php?slug=' . urlencode($p['slug'])) ?>" style="color: inherit;">
                                    <?= e($p['title']) ?>
                                </a>
                            </h3>
                            <p class="service-card-text">
                                <?= e($p['summary'] ?: 'Explore our ' . $p['title'] . ' structured finance solutions.') ?>
                            </p>
                            <a href="<?= url('page.php?slug=' . urlencode($p['slug'])) ?>" class="service-card-link">
                                <span>Read More</span>
                                <span class="link-arrow">+</span>
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12">
                    <p class="text-muted">No finance solutions published yet.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
