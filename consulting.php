<?php
/**
 * Global Connect - Consulting Overview Page
 */
$pageTitle = 'Corporate Consulting | Global Connect';
$pageMetaDesc = 'Financial Structuring, Trade Finance, M&A, Offshore Fund Raising, Unlocking Values & Debt Syndication consulting by Global Connect.';

require_once __DIR__ . '/includes/header.php';
?>

<!-- Page Banner -->
<div class="page-banner">
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
        <div class="about-grid" style="margin-bottom: 50px;">
            <div>
                <span class="section-subtitle">What We Specialize In</span>
                <h2 class="section-title">Institutional Financial Advisory &amp; Capital Market Solutions</h2>
                <p class="section-lead">
                    The Global Connect group provides advisory solutions to institutions and corporates in managing and growing their business via high quality financial advisory services and capital market solutions.
                </p>
                <p style="color: var(--text-muted); line-height: 1.7; margin-bottom: 25px;">
                    Our senior leadership team brings decades of institutional banking, investment structuring, and commodity trading background. We structure cross-border joint ventures, advise on debt syndication, and guide entities toward listing readiness across regional exchanges.
                </p>
                <a href="<?= url('contact.php') ?>" class="btn-primary">Schedule Consultation →</a>
            </div>
            <div>
                <img src="<?= image_url('consulting-home.jpg') ?>" alt="Consulting Group" class="img-fluid" style="border-radius: var(--radius-md); box-shadow: var(--shadow-md); width: 100%; max-height: 440px; object-fit: cover;">
            </div>
        </div>

        <!-- Consulting Service Disciplines -->
        <div class="hub-grid" style="grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));">
            <div class="hub-card">
                <div class="hub-icon">💼</div>
                <h3 class="hub-card-title">International Trade Consulting</h3>
                <p class="hub-card-text">
                    Structuring end-to-end import/export contracts, counter-trade models, escrow frameworks, and incoterm compliance across international waters.
                </p>
            </div>

            <div class="hub-card">
                <div class="hub-icon">🏭</div>
                <h3 class="hub-card-title">Sourcing &amp; Contract Manufacturing</h3>
                <p class="hub-card-text">
                    Connecting global buyers directly with certified manufacturing plants, refineries, and mining concessions across Southeast Asia, Africa, and South America.
                </p>
            </div>

            <div class="hub-card">
                <div class="hub-icon">🚢</div>
                <h3 class="hub-card-title">Logistical Support &amp; Shipping</h3>
                <p class="hub-card-text">
                    Comprehensive freight management, Capesize/Handymax chartering, SGS port inspections, and marine cargo insurance coordination.
                </p>
            </div>

            <div class="hub-card">
                <div class="hub-icon">🤝</div>
                <h3 class="hub-card-title">Negotiations &amp; Deal Structuring</h3>
                <p class="hub-card-text">
                    Acting as independent advisory mediators in multi-party trade agreements, sovereign procurement tenders, and long-term commodity offtakes.
                </p>
            </div>

            <div class="hub-card">
                <div class="hub-icon">📈</div>
                <h3 class="hub-card-title">Offshore Fund Raising</h3>
                <p class="hub-card-text">
                    Mobilizing institutional debt, mezzanine facilities, and private credit from global capital pools located in Singapore, London, and Zurich.
                </p>
            </div>

            <div class="hub-card">
                <div class="hub-icon">🏦</div>
                <h3 class="hub-card-title">Debt Syndication</h3>
                <p class="hub-card-text">
                    Coordinating multi-lender syndicates and working capital consortia for high-capex infrastructure, mining, and industrial developments.
                </p>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
