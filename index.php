<?php
/**
 * Global Connect - Homepage
 */
$pageTitle = 'Global Connect | Global Business Enterprise Headquartered in Singapore & India';
$pageMetaDesc = 'We are a global business enterprise headquartered in Singapore And India. Reliable partner for international trade services, trade finance, and financial advisory.';

require_once __DIR__ . '/includes/header.php';
?>

<!-- Hero Section with Background & Visuals -->
<section class="hero-section">
    <div class="container hero-content">
        <span class="hero-badge">Singapore &bull; India &bull; Global Network</span>
        <h1 class="hero-title">
            Your Strategic Partner in Sourcing &amp; Supply Chain Management
        </h1>
        <p class="hero-subtitle">
            We are a global business enterprise headquartered in Singapore and India. At Global Connect, we believe in building meaningful partnerships that drive success across international borders.
        </p>
        <div class="hero-actions">
            <a href="<?= url('trading.php') ?>" class="btn-primary">Explore Trading</a>
            <a href="<?= url('finance.php') ?>" class="btn-gold">Trade Finance</a>
            <a href="<?= url('contact.php') ?>" class="btn-outline-white">Contact Us</a>
        </div>
    </div>
</section>

<!-- Key Stats Counter Bar -->
<section class="stats-section">
    <div class="container">
        <div class="stats-grid">
            <div class="stat-item">
                <span class="stat-number">3,600+</span>
                <span class="stat-label">Associate Partners</span>
            </div>
            <div class="stat-item">
                <span class="stat-number">19+</span>
                <span class="stat-label">Countries Globally</span>
            </div>
            <div class="stat-item">
                <span class="stat-number">19+</span>
                <span class="stat-label">Partner Banks</span>
            </div>
            <div class="stat-item">
                <span class="stat-number">25+</span>
                <span class="stat-label">Years Cumulative Experience</span>
            </div>
        </div>
    </div>
</section>

<!-- Company Experience & Philosophy -->
<section class="section">
    <div class="container">
        <div class="about-grid">
            <div>
                <span class="section-subtitle">Company Experience</span>
                <h2 class="section-title">Global Connect’s Believe in Growth Strategies</h2>
                <p class="section-lead">
                    The organisation involves various teams to handle client needs which start from Capital Raising, Project Finance, Financial Engineering or Structuring, and Fund Raising programs to name a few.
                </p>
                <p style="margin-bottom: 25px; color: var(--text-muted); line-height: 1.7;">
                    Fund raising is our daily work and we are always available to support your short term &amp; long term needs. Global Connect involves itself with clients in achieving results. May it start with determining the most suitable structure to reach the end result. Global Connect is looking forward to participate with clients to translate their visions to reality.
                </p>
                <div style="display: flex; gap: 15px; flex-wrap: wrap;">
                    <a href="<?= url('about.php') ?>" class="btn-primary">View More About Us →</a>
                    <a href="<?= url('contact.php') ?>" class="btn-outline-primary">Get Consultation</a>
                </div>
            </div>
            <div class="about-image-wrapper">
                <img src="<?= image_url('about-growth.jpg') ?>" alt="Company Growth Strategies" class="img-fluid" width="560" height="340">
            </div>
        </div>
    </div>
</section>

<!-- What We Do / Core Services Overview -->
<section class="section section-light">
    <div class="container">
        <div class="section-header">
            <span class="section-subtitle">Our Capabilities</span>
            <h2 class="section-title">Comprehensive Trade &amp; Financial Solutions</h2>
            <p class="section-lead">
                Delivering excellence across international commodity trading, custom trade banking solutions, and corporate financial advisory.
            </p>
        </div>

        <div class="services-grid">
            <!-- Service 1: Trading -->
            <div class="service-card">
                <div class="service-card-image">
                    <img src="<?= image_url('trading-home.jpg') ?>" alt="Global Trading" loading="lazy">
                </div>
                <div class="service-card-body">
                    <h3 class="service-card-title">Trading Portfolio</h3>
                    <p class="service-card-text">
                        We trade in a variety of products and commodities all around the world including Gold Bullion, Sugar, Agro Products, Minerals, Metals &amp; Ores, and Petroleum &amp; Lubricants.
                    </p>
                    <a href="<?= url('trading.php') ?>" class="service-card-link">Read More +</a>
                </div>
            </div>

            <!-- Service 2: Finance -->
            <div class="service-card">
                <div class="service-card-image">
                    <img src="<?= image_url('finance-home.jpg') ?>" alt="Trade Finance" loading="lazy">
                </div>
                <div class="service-card-body">
                    <h3 class="service-card-title">Finance Solutions</h3>
                    <p class="service-card-text">
                        Partnering with leading global institutions to provide Trade Finance (Standby LC, Transferable LC, Documentary LC), Corporate Banking, and International Trade Advisory.
                    </p>
                    <a href="<?= url('finance.php') ?>" class="service-card-link">Read More +</a>
                </div>
            </div>

            <!-- Service 3: Consulting -->
            <div class="service-card">
                <div class="service-card-image">
                    <img src="<?= image_url('consulting-home.jpg') ?>" alt="Corporate Consulting" loading="lazy">
                </div>
                <div class="service-card-body">
                    <h3 class="service-card-title">Corporate Consulting</h3>
                    <p class="service-card-text">
                        High quality Financial Advisory services and capital market solutions: Financial Structuring, M&amp;A, Offshore Fund Raising, Unlocking Values, and Debt Syndication.
                    </p>
                    <a href="<?= url('consulting.php') ?>" class="service-card-link">Read More +</a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Singapore Financial Hub Advantages -->
<section class="section">
    <div class="container">
        <div class="section-header">
            <span class="section-subtitle">Jurisdictional Strength</span>
            <h2 class="section-title">Why Singapore is the Global Trade &amp; Financial Hub</h2>
            <p class="section-lead">
                Singapore consistently ranks among the world’s most competitive economies, offering an unparalleled ecosystem for trade and investment.
            </p>
        </div>

        <div class="hub-grid">
            <div class="hub-card">
                <div class="hub-icon">🏛</div>
                <h3 class="hub-card-title">Top Financial Hub</h3>
                <p class="hub-card-text">
                    Ranked among the top financial centres globally, offering deep capital markets, leading international banks, and robust liquidity access.
                </p>
            </div>

            <div class="hub-card">
                <div class="hub-icon">⚖</div>
                <h3 class="hub-card-title">Rule of Law &amp; Governance</h3>
                <p class="hub-card-text">
                    Transparent, trusted legal framework with strong intellectual property protection and highly respected international arbitration courts.
                </p>
            </div>

            <div class="hub-card">
                <div class="hub-icon">🎓</div>
                <h3 class="hub-card-title">Educated &amp; Global Workforce</h3>
                <p class="hub-card-text">
                    A world-class talent pool with extensive experience across cross-border trade, quantitative finance, and international compliance.
                </p>
            </div>

            <div class="hub-card">
                <div class="hub-icon">💵</div>
                <h3 class="hub-card-title">Currency &amp; Monetary Stability</h3>
                <p class="hub-card-text">
                    The Singapore Dollar (SGD) is backed by solid fiscal management under the Monetary Authority of Singapore (MAS), ensuring stability.
                </p>
            </div>

            <div class="hub-card">
                <div class="hub-icon">✈</div>
                <h3 class="hub-card-title">Global Connectivity</h3>
                <p class="hub-card-text">
                    Strategic geographic location with direct flights to over 400 cities and timezone alignment connecting Western and Asian business days.
                </p>
            </div>
        </div>
    </div>
</section>

<!-- FDI Singapore to India Analysis -->
<section class="section section-light">
    <div class="container">
        <div class="fdi-box">
            <div class="section-header" style="text-align: left; margin-bottom: 30px;">
                <span class="section-subtitle">Economic Corridor Insights</span>
                <h2 class="section-title">Singapore Remains the Leading Source of FDI into India</h2>
                <p class="section-lead" style="margin-left: 0;">
                    Singapore maintained its position as the top source of Foreign Direct Investment (FDI) equity inflows into India, contributing <strong>$15 billion (approx. ₹1.24 lakh crore)</strong> in the 2024–25 fiscal year.
                </p>
            </div>

            <div class="fdi-grid">
                <div class="fdi-stat-card">
                    <span class="fdi-number">$15.0 Billion</span>
                    <span class="fdi-desc">Direct Equity Inflow from Singapore in 2024–25</span>
                </div>
                <div class="fdi-stat-card">
                    <span class="fdi-number">+14% Growth</span>
                    <span class="fdi-desc">Increase in Overall FDI Inflows to India ($81.04B Total)</span>
                </div>
                <div class="fdi-stat-card">
                    <span class="fdi-number">1st Rank</span>
                    <span class="fdi-desc">Singapore Ranks as India's #1 Global Investment Partner</span>
                </div>
            </div>

            <div style="margin-top: 30px; background: #ffffff; padding: 25px; border-radius: var(--radius-md); border-left: 4px solid var(--accent-gold);">
                <h4 style="margin-bottom: 10px; color: var(--primary-color);">Industry Expert Analysis</h4>
                <p style="font-size: 14px; color: var(--text-muted); line-height: 1.7; margin-bottom: 10px;">
                    <em>"Singapore's sustained position as India's leading FDI contributor reflects the deep structural trust and comprehensive economic partnership between both nations. Global Connect leverages this vital corridor to facilitate trade structuring, fund raising, and cross-border ventures."</em>
                </p>
                <small style="color: var(--secondary-color); font-weight: 600;">— Economic Insights Desk, Global Connect Singapore</small>
            </div>
        </div>
    </div>
</section>

<!-- Call to Action Banner -->
<section class="cta-section">
    <div class="container text-center">
        <h2 style="font-size: 32px; font-weight: 800; margin-bottom: 15px;">Ready to Structure Your Next Global Trade or Project?</h2>
        <p style="max-width: 680px; margin: 0 auto 30px; font-size: 16px; opacity: 0.9;">
            Connect directly with our specialized trade finance desks in Singapore and India. We provide prioritized assistance and customized execution.
        </p>
        <div style="display: flex; gap: 15px; justify-content: center; flex-wrap: wrap;">
            <a href="<?= url('contact.php') ?>" class="btn-gold" style="padding: 14px 32px; font-size: 16px;">Initiate Trade Enquiry →</a>
            <a href="tel:+6589451100" class="btn-outline-white" style="padding: 14px 32px; font-size: 16px;">Call Singapore: (65) 89451100</a>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
