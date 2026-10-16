<?php
/**
 * Global Connect - Finance Overview Page
 * Dynamically displays all Finance services/pages from DB
 */
$pageTitle = 'Finance Solutions | Global Connect';
$pageMetaDesc = 'Global Connect provides Trade Finance, Banking Support, SBLC, Letters of Credit, and Corporate Financial Advisory solutions.';

require_once __DIR__ . '/includes/header.php';

$financePages = get_pages_by_category('finance');
?>

<!-- Page Banner -->
<div class="page-banner">
    <div class="container">
        <h1 class="page-banner-title">Finance Solutions</h1>
        <p class="page-banner-subtitle">Trade Finance, Banking Support &amp; Advisory Solutions</p>
        <div class="breadcrumb-nav">
            <a href="<?= url('index.php') ?>">Home</a> <span>/</span> <span>Finance</span>
        </div>
    </div>
</div>

<div class="page-content-wrapper">
    <div class="container">
        <div class="section-header" style="text-align: left; max-width: 900px; margin-bottom: 40px;">
            <span class="section-subtitle">Our Finance Capabilities</span>
            <h2 class="section-title">
                We are Trade Finance Specialists offering various services in the Finance Domain all around the world.
            </h2>
            <p class="section-lead">
                Along with our global Partner Banks, we provide customized financial instruments with prioritized processing to facilitate international transactions securely and efficiently.
            </p>
        </div>

        <div class="services-grid">
            <?php if (!empty($financePages)): ?>
                <?php foreach ($financePages as $p): ?>
                    <div class="service-card">
                        <div class="service-card-image">
                            <a href="<?= url('page.php?slug=' . urlencode($p['slug'])) ?>">
                                <img src="<?= image_url($p['featured_image'] ?: 'finance-home.jpg') ?>" alt="<?= e($p['title']) ?>" loading="lazy">
                            </a>
                        </div>
                        <div class="service-card-body">
                            <h3 class="service-card-title">
                                <a href="<?= url('page.php?slug=' . urlencode($p['slug'])) ?>" style="color: inherit;">
                                    <?= e($p['title']) ?>
                                </a>
                            </h3>
                            <p class="service-card-text">
                                <?= e($p['summary'] ?: 'Comprehensive ' . $p['title'] . ' solutions engineered for corporate and institutional clients.') ?>
                            </p>
                            <a href="<?= url('page.php?slug=' . urlencode($p['slug'])) ?>" class="service-card-link">
                                Read More +
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12">
                    <p class="text-muted">No finance services published yet.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
