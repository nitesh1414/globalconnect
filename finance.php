<?php
/**
 * Global Connect - Finance Overview Page
 * Dynamically displays all Finance services/pages from DB
 */
$pageTitle = 'Finance | Global Connect';
$pageMetaDesc = 'We are TradeFinanceSpecialists offering various services in the Finance Domain all around the world including Trade Finance, Banking Support, and Advisory.';

require_once __DIR__ . '/includes/header.php';

$financePages = get_pages_by_category('finance');
?>

<!-- Page Banner -->
<div class="page-banner">
    <div class="container">
        <h1 class="page-banner-title">Finance</h1>
        <p class="page-banner-subtitle">Strategic trade finance, institutional banking facilities, and capital solutions</p>
        <div class="breadcrumb-nav">
            <a href="/index.php">Home</a> <span>/</span> <span>Finance</span>
        </div>
    </div>
</div>

<div class="page-content-wrapper">
    <div class="container">
        <div class="section-header" style="text-align: left; max-width: 900px; margin-bottom: 40px;">
            <span class="section-subtitle">Our Finance Capabilities</span>
            <h2 class="section-title">
                We are TradeFinanceSpecialists offering various services in the Finance Domain all around the world. We pride in our diverse range of offerings to match our customer needs and requirements.
            </h2>
            <p class="section-lead">
                Partnered with premier international banks to deliver customized trade instruments, letters of credit, and seamless cross-border banking support with priority processing.
            </p>
        </div>

        <div class="services-grid">
            <?php if (!empty($financePages)): ?>
                <?php foreach ($financePages as $p): ?>
                    <div class="service-card">
                        <div class="service-card-image">
                            <a href="/page.php?slug=<?= e($p['slug']) ?>">
                                <img src="<?= e($p['featured_image'] ?: '/assets/images/finance-home.jpg') ?>" alt="<?= e($p['title']) ?>" loading="lazy">
                            </a>
                        </div>
                        <div class="service-card-body">
                            <h3 class="service-card-title">
                                <a href="/page.php?slug=<?= e($p['slug']) ?>" style="color: inherit;">
                                    <?= e($p['title']) ?>
                                </a>
                            </h3>
                            <p class="service-card-text">
                                <?= e($p['summary'] ?: 'Explore our ' . $p['title'] . ' solutions and priority banking services.') ?>
                            </p>
                            <a href="/page.php?slug=<?= e($p['slug']) ?>" class="service-card-link">
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
