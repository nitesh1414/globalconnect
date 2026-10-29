<?php
/**
 * Global Connect - About Us Page
 */
require_once __DIR__ . '/includes/functions.php';

$pageTitle = 'About Us | ' . get_setting('company_name', 'Global Connect');
$pageMetaDesc = 'Learn about Global Connect, our growth strategies, expert team, and unique cross-border financial advisory capabilities.';

require_once __DIR__ . '/includes/header.php';
?>

<!-- Page Banner -->
<div class="page-banner reveal-up">
    <div class="container">
        <h1 class="page-banner-title">About Us</h1>
        <p class="page-banner-subtitle">Global Connect believe in Growth Strategies</p>
        <div class="breadcrumb-nav">
            <a href="<?= url('index.php') ?>">Home</a> <span>/</span> <span>About Us</span>
        </div>
    </div>
</div>

<div class="page-content-wrapper">
    <div class="container">
        <!-- Main Intro Section -->
        <div class="about-grid" style="margin-bottom: 70px;">
            <div class="reveal-left">
                <span class="section-subtitle">Our Vision &amp; Philosophy</span>
                <h2 class="section-title">Growth Ideas and Financial Strategies</h2>
                <div style="color: var(--text-main); font-size: 15px; line-height: 1.8;">
                    <p style="margin-bottom: 16px;">
                        The organisation involves various teams to handle client needs which start from <strong>Capital Raising, Project Finance, Financial Engineering or Structuring, and Fund Raising programs</strong> to name a few.
                    </p>
                    <p style="margin-bottom: 16px;">
                        Fund raising is our daily work and we are always available to support your short term &amp; long term needs. Global Connect involves itself with clients in achieving results. May it start with determining the most suitable structure to reach the end result.
                    </p>
                    <p style="margin-bottom: 16px;">
                        Global Connect is looking forward to participate with clients to translate their visions into reality — which is uniquely reflected in our Tag line itself. Global Connect's Team is a combination of Business, Functional, Financial, Technical, and Operational skills to align with the activities of businesses and promoters for achieving highest results.
                    </p>
                </div>
            </div>
            <div class="about-image-wrapper reveal-right">
                <div class="image-frame-glow">
                    <img src="<?= image_url('about-growth.jpg') ?>" alt="Growth Ideas and Financial Strategies" class="img-fluid floating-img" style="border-radius: var(--radius-md); width: 100%; max-height: 480px; object-fit: cover;">
                </div>
            </div>
        </div>

        <!-- 6 Core Expertise Pillars Grid -->
        <div class="section-header reveal-up">
            <span class="section-subtitle">Core Pillars of Excellence</span>
            <h2 class="section-title">What Makes Global Connect Unique</h2>
            <p class="section-lead">
                Our multi-disciplinary team combines cross-border execution with institutional banking partnerships.
            </p>
        </div>

        <div class="pillar-grid">
            <!-- Pillar 1 -->
            <div class="pillar-card reveal-up hover-lift">
                <div class="pillar-image">
                    <img src="<?= image_url('financial-structure.jpg') ?>" alt="Financial Structuring">
                </div>
                <div class="pillar-body">
                    <h3 class="pillar-title">1. Financial Structuring</h3>
                    <p class="pillar-text">
                        Designing customized balance-sheet structures, cross-border corporate frameworks, and multi-tier funding models to optimize financial efficiency and minimize cost of capital.
                    </p>
                </div>
            </div>

            <!-- Pillar 2 -->
            <div class="pillar-card reveal-up hover-lift" style="transition-delay: 0.1s;">
                <div class="pillar-image">
                    <img src="<?= image_url('international-trade-finance.jpg') ?>" alt="International Trade Finance">
                </div>
                <div class="pillar-body">
                    <h3 class="pillar-title">2. International Trade Finance</h3>
                    <p class="pillar-text">
                        Structuring robust documentary credit instruments including Standby Letters of Credit (SBLC), Transferable LC, and Back-to-Back LC facilities with tier-1 international banks.
                    </p>
                </div>
            </div>

            <!-- Pillar 3 -->
            <div class="pillar-card reveal-up hover-lift" style="transition-delay: 0.2s;">
                <div class="pillar-image">
                    <img src="<?= image_url('merge-acquisition.jpg') ?>" alt="Merge and Acquisition">
                </div>
                <div class="pillar-body">
                    <h3 class="pillar-title">3. Merge &amp; Acquisition (M&amp;A)</h3>
                    <p class="pillar-text">
                        Guiding cross-border mergers, buyouts, and strategic asset divestments with deep valuation analysis, regulatory clearance, and confidential deal structuring.
                    </p>
                </div>
            </div>

            <!-- Pillar 4 -->
            <div class="pillar-card reveal-up hover-lift" style="transition-delay: 0.3s;">
                <div class="pillar-image">
                    <img src="<?= image_url('off-shore-fund-raising.jpg') ?>" alt="Off Shore Fund Raising">
                </div>
                <div class="pillar-body">
                    <h3 class="pillar-title">4. Off Shore Fund Raising</h3>
                    <p class="pillar-text">
                        Mobilizing global capital through syndicated loans, private equity placements, foreign institutional investors (FII), and international credit lines.
                    </p>
                </div>
            </div>

            <!-- Pillar 5 -->
            <div class="pillar-card reveal-up hover-lift" style="transition-delay: 0.4s;">
                <div class="pillar-image">
                    <img src="<?= image_url('unlocking-values.jpg') ?>" alt="Unlocking Values">
                </div>
                <div class="pillar-body">
                    <h3 class="pillar-title">5. Unlocking Values</h3>
                    <p class="pillar-text">
                        Identifying undervalued corporate assets, monetizing dormant physical assets, and transforming non-liquid holdings into productive working capital.
                    </p>
                </div>
            </div>

            <!-- Pillar 6 -->
            <div class="pillar-card reveal-up hover-lift" style="transition-delay: 0.5s;">
                <div class="pillar-image">
                    <img src="<?= image_url('debt-syndication.jpg') ?>" alt="Debt Syndication">
                </div>
                <div class="pillar-body">
                    <h3 class="pillar-title">6. Debt Syndication</h3>
                    <p class="pillar-text">
                        Arranging multi-bank debt syndication for large-scale infrastructure, power, manufacturing, and commodity projects across emerging markets.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.pillar-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 30px;
}
.pillar-card {
    background: #ffffff;
    border-radius: var(--radius-md);
    overflow: hidden;
    box-shadow: var(--shadow-sm);
    border: 1px solid var(--border-color);
    display: flex;
    flex-direction: column;
}
.pillar-image {
    height: 190px;
    overflow: hidden;
}
.pillar-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.5s ease;
}
.pillar-card:hover .pillar-image img {
    transform: scale(1.08);
}
.pillar-body {
    padding: 22px;
}
.pillar-title {
    font-size: 18px;
    font-weight: 700;
    color: var(--primary-color);
    margin-bottom: 10px;
}
.pillar-text {
    font-size: 14px;
    color: var(--text-muted);
    line-height: 1.6;
}
.page-banner {
    background: var(--bg-navy-gradient);
    color: #ffffff;
    padding: 65px 0 55px;
    text-align: center;
    position: relative;
    border-bottom: 1px solid rgba(255, 255, 255, 0.08);
}
.page-banner-title {
    font-size: 38px;
    font-weight: 800;
    margin-bottom: 8px;
    letter-spacing: -0.5px;
}
.page-banner-subtitle {
    font-size: 16px;
    color: var(--accent-gold-light);
    margin-bottom: 15px;
}
.breadcrumb-nav {
    font-size: 13px;
    color: #94a3b8;
}
.breadcrumb-nav a {
    color: #cbd5e1;
}
.breadcrumb-nav a:hover {
    color: var(--accent-gold);
}
.page-content-wrapper {
    padding: 70px 0;
}
@media (max-width: 992px) {
    .pillar-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}
@media (max-width: 640px) {
    .pillar-grid {
        grid-template-columns: 1fr;
    }
}
</style>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
