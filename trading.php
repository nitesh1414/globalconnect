<?php
/**
 * Global Connect - Trading Overview Page
 * Dynamically displays all Trading products/pages from DB
 */
require_once __DIR__ . '/includes/functions.php';

$pageTitle = 'Trading | ' . get_setting('company_name', 'Global Connect');
$pageMetaDesc = 'We trade in a variety of products and commodities all around the world including Gold Bullion, Sugar, Agro Products, Minerals, Metals, and Petroleum.';

require_once __DIR__ . '/includes/header.php';

$tradingPages = get_pages_by_category('trading');
?>

<!-- Page Banner -->
<div class="page-banner reveal-up">
    <div class="container">
        <h1 class="page-banner-title">Trading</h1>
        <p class="page-banner-subtitle">Global commodities and resources traded across worldwide markets</p>
        <div class="breadcrumb-nav">
            <a href="<?= url('index.php') ?>">Home</a> <span>/</span> <span>Trading</span>
        </div>
    </div>
</div>

<div class="page-content-wrapper">
    <div class="container">
        <div class="section-header reveal-left" style="text-align: left; max-width: 900px; margin-bottom: 45px;">
            <span class="section-subtitle">Our Trading Portfolio</span>
            <h2 class="section-title">
                We trade in a variety of products and commodities all around the world. We pride in our diverse range of offerings to match our customer needs and requirements.
            </h2>
            <p class="section-lead">
                Having strong networks with manufacturers and exporters, we are enabled to ensure a timely and efficient fulfilment of trade requirements from our customers.
            </p>
        </div>

        <div class="services-grid">
            <?php if (!empty($tradingPages)): ?>
                <?php foreach ($tradingPages as $idx => $p): ?>
                    <div class="service-card reveal-up hover-lift" style="transition-delay: <?= ($idx * 0.1) ?>s;">
                        <div class="service-card-image">
                            <a href="<?= url('page.php?slug=' . urlencode($p['slug'])) ?>">
                                <img src="<?= image_url($p['featured_image'] ?: 'trading-home.jpg') ?>" alt="<?= e($p['title']) ?>" loading="lazy">
                            </a>
                            <div class="service-badge">Commodity</div>
                        </div>
                        <div class="service-card-body">
                            <h3 class="service-card-title">
                                <a href="<?= url('page.php?slug=' . urlencode($p['slug'])) ?>" style="color: inherit;">
                                    <?= e($p['title']) ?>
                                </a>
                            </h3>
                            <p class="service-card-text">
                                <?= e($p['summary'] ?: 'Explore our ' . $p['title'] . ' trading solutions and international supply capabilities.') ?>
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
                    <p class="text-muted">No trading products published yet.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
