<?php
/**
 * Global Connect - Consulting Overview Page
 */
require_once __DIR__ . '/includes/functions.php';

$pageTitle = 'Corporate Consulting | ' . get_setting('company_name', 'Global Connect');
$pageMetaDesc = 'Financial Structuring, Trade Finance, M&A, Offshore Fund Raising, Unlocking Values & Debt Syndication consulting by Global Connect.';

require_once __DIR__ . '/includes/header.php';
?>

<!-- Page Banner -->
<div class="page-banner reveal-up">
    <div class="container">
        <h1 class="page-banner-title">Corporate Consulting</h1>
        <p class="page-banner-subtitle">Strategic Sourcing, Capital Markets &amp; Corporate Advisory</p>
        <div class="breadcrumb-nav">
            <a href="<?= url('index.php') ?>">Home</a> <span>/</span> <span>Consulting</span>
        </div>
    </div>
</div>

<div class="page-content-wrapper">
    <div class="container">
        <div class="about-grid" style="margin-bottom: 60px;">
            <div class="reveal-left">
                <span class="section-subtitle">What We Specialize In</span>
                <h2 class="section-title">Institutional Financial Advisory &amp; Capital Market Solutions</h2>
                <p class="section-lead">
                    The Global Connect group provides advisory solutions to institutions and corporates in managing and growing their business via high quality financial advisory services and capital market solutions.
                </p>
                <p style="color: var(--text-muted); line-height: 1.75; margin-bottom: 25px;">
                    Our senior leadership team brings decades of institutional banking, investment structuring, and commodity trading background. We structure cross-border joint ventures, advise on debt syndication, and guide entities toward listing readiness across regional exchanges.
                </p>
                <a href="<?= url('contact.php') ?>" class="btn-primary shimmer-btn">Schedule Consultation →</a>
            </div>
            <div class="about-image-wrapper reveal-right">
                <div class="image-frame-glow">
                    <img src="<?= image_url('consulting-home.jpg') ?>" alt="Consulting Group" class="img-fluid floating-img" style="border-radius: var(--radius-md); width: 100%; max-height: 440px; object-fit: cover;">
                </div>
            </div>
        </div>

        <!-- Consulting Service Disciplines -->
        <div class="services-grid" style="grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));">
            <div class="service-card reveal-up hover-lift" style="padding: 30px;">
                <div class="feature-icon feature-icon-anim" style="margin-bottom: 18px;">💼</div>
                <h3 class="service-card-title">International Trade Consulting</h3>
                <p class="service-card-text">
                    Structuring end-to-end import/export contracts, counter-trade models, escrow frameworks, and incoterm compliance across international waters.
                </p>
                <a href="<?= url('contact.php') ?>" class="service-card-link">Request Advisory <span class="link-arrow">→</span></a>
            </div>

            <div class="service-card reveal-up hover-lift" style="padding: 30px; transition-delay: 0.1s;">
                <div class="feature-icon feature-icon-anim" style="margin-bottom: 18px;">🏭</div>
                <h3 class="service-card-title">Sourcing &amp; Contract Manufacturing</h3>
                <p class="service-card-text">
                    Connecting global buyers directly with certified manufacturing plants, refineries, and mining concessions across Southeast Asia, Africa, and South America.
                </p>
                <a href="<?= url('contact.php') ?>" class="service-card-link">Request Advisory <span class="link-arrow">→</span></a>
            </div>

            <div class="service-card reveal-up hover-lift" style="padding: 30px; transition-delay: 0.2s;">
                <div class="feature-icon feature-icon-anim" style="margin-bottom: 18px;">🚢</div>
                <h3 class="service-card-title">Logistical Support &amp; Shipping</h3>
                <p class="service-card-text">
                    Comprehensive freight management, Capesize/Handymax chartering, SGS port inspections, and marine cargo insurance coordination.
                </p>
                <a href="<?= url('contact.php') ?>" class="service-card-link">Request Advisory <span class="link-arrow">→</span></a>
            </div>

            <div class="service-card reveal-up hover-lift" style="padding: 30px; transition-delay: 0.3s;">
                <div class="feature-icon feature-icon-anim" style="margin-bottom: 18px;">🤝</div>
                <h3 class="service-card-title">Negotiations &amp; Deal Structuring</h3>
                <p class="service-card-text">
                    High-stakes transaction negotiation, counterparty risk assessment, dispute mediation, and closing support for complex international agreements.
                </p>
                <a href="<?= url('contact.php') ?>" class="service-card-link">Request Advisory <span class="link-arrow">→</span></a>
            </div>

            <div class="service-card reveal-up hover-lift" style="padding: 30px; transition-delay: 0.4s;">
                <div class="feature-icon feature-icon-anim" style="margin-bottom: 18px;">📈</div>
                <h3 class="service-card-title">Capital Raising &amp; Debt Syndication</h3>
                <p class="service-card-text">
                    Mobilizing institutional liquidity from regional and global commercial banks, developmental finance institutions, and sovereign wealth funds.
                </p>
                <a href="<?= url('contact.php') ?>" class="service-card-link">Request Advisory <span class="link-arrow">→</span></a>
            </div>

            <div class="service-card reveal-up hover-lift" style="padding: 30px; transition-delay: 0.5s;">
                <div class="feature-icon feature-icon-anim" style="margin-bottom: 18px;">🏛</div>
                <h3 class="service-card-title">Cross-Border Corporate Advisory</h3>
                <p class="service-card-text">
                    Singapore corporate headquarters structuring, MAS tax incentive planning, cross-border holding vehicles, and bilateral investment treaty utilization.
                </p>
                <a href="<?= url('contact.php') ?>" class="service-card-link">Request Advisory <span class="link-arrow">→</span></a>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
