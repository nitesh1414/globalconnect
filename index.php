<?php
/**
 * Global Connect - Homepage
 */
$pageTitle = 'Global Connect | Global Business Enterprise Headquartered in Singapore & India';
$pageMetaDesc = 'We are a global business enterprise headquartered in Singapore And India. Reliable partner for international trade services, trade finance, and financial advisory.';

require_once __DIR__ . '/includes/header.php';

// Fetch active hero slides from database
$heroSlides = get_hero_slides(true);
if (empty($heroSlides)) {
    // Default fallback slide with the uploaded banner
    $heroSlides = [
        [
            'id' => 1,
            'title' => 'Global Connect Group of Companies',
            'subtitle' => 'Singapore • India • Indonesia • Hongkong | Sourcing, Supply Chain & Financial Structuring',
            'badge_text' => 'Singapore • India • Global Network',
            'image_path' => 'assets/images/global-connect-banner-1.png',
            'button_text' => 'Explore Trading',
            'button_link' => 'trading.php',
            'secondary_button_text' => 'Trade Finance',
            'secondary_button_link' => 'finance.php',
            'show_text_overlay' => 0,
            'status' => 'active'
        ]
    ];
}
?>

<!-- Dynamic Hero Slider Section (Clean Image Graphic & Text Overlay modes) -->
<section class="hero-slider-section" id="heroSlider" data-slide-count="<?= count($heroSlides) ?>">
    <div class="hero-slider-track">
        <?php foreach ($heroSlides as $idx => $slide): ?>
            <div class="hero-slide-item <?= $idx === 0 ? 'active' : '' ?> <?= $slide['show_text_overlay'] ? 'slide-has-overlay' : 'slide-clean-graphic' ?>" data-slide-index="<?= $idx ?>">
                <?php if ($slide['show_text_overlay']): ?>
                    <!-- Slide With Text Overlay Mode -->
                    <div class="hero-slide-bg" style="background-image: url('<?= image_url($slide['image_path']) ?>');">
                        <div class="hero-overlay-backdrop"></div>
                        <div class="hero-overlay-grid"></div>
                        <div class="container hero-content">
                            <?php if (!empty($slide['badge_text'])): ?>
                                <span class="hero-badge"><?= e($slide['badge_text']) ?></span>
                            <?php endif; ?>
                            <h1 class="hero-title"><?= e($slide['title']) ?></h1>
                            <?php if (!empty($slide['subtitle'])): ?>
                                <p class="hero-subtitle"><?= nl2br(e($slide['subtitle'])) ?></p>
                            <?php endif; ?>
                            <div class="hero-actions">
                                <?php if (!empty($slide['button_text']) && !empty($slide['button_link'])): ?>
                                    <a href="<?= url($slide['button_link']) ?>" class="btn-primary"><?= e($slide['button_text']) ?></a>
                                <?php endif; ?>
                                <?php if (!empty($slide['secondary_button_text']) && !empty($slide['secondary_button_link'])): ?>
                                    <a href="<?= url($slide['secondary_button_link']) ?>" class="btn-gold"><?= e($slide['secondary_button_text']) ?></a>
                                <?php endif; ?>
                                <a href="<?= url('contact.php') ?>" class="btn-outline-white">Contact Us</a>
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    <!-- Slide Clean Graphic Mode (Full image completely visible on all devices) -->
                    <div class="hero-clean-wrapper">
                        <img src="<?= image_url($slide['image_path']) ?>" alt="<?= e($slide['title'] ?: 'Global Connect Group of Companies') ?>" class="hero-clean-banner-img" loading="<?= $idx === 0 ? 'eager' : 'lazy' ?>">
                    </div>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>

    <?php if (count($heroSlides) > 1): ?>
        <!-- Slider Navigation Controls -->
        <button type="button" class="hero-nav-btn hero-nav-prev" aria-label="Previous Slide">&#10094;</button>
        <button type="button" class="hero-nav-btn hero-nav-next" aria-label="Next Slide">&#10095;</button>
        
        <div class="hero-dots-container">
            <?php foreach ($heroSlides as $idx => $slide): ?>
                <button type="button" class="hero-dot <?= $idx === 0 ? 'active' : '' ?>" data-slide-to="<?= $idx ?>" aria-label="Slide <?= $idx + 1 ?>"></button>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>

<!-- Quick Action Strip -->
<div class="hero-quick-bar">
    <div class="container hero-quick-grid">
        <div class="hero-quick-tagline">
            <span>🌐</span>
            <span>Headquartered in <strong>Singapore</strong> &amp; <strong>India</strong> &bull; Presence in <strong>Indonesia</strong> &amp; <strong>Hong Kong</strong></span>
        </div>
        <div class="hero-quick-links">
            <a href="<?= url('trading.php') ?>" class="hero-quick-btn hero-quick-btn-primary">Explore Trading</a>
            <a href="<?= url('finance.php') ?>" class="hero-quick-btn hero-quick-btn-gold">Trade Finance</a>
            <a href="<?= url('contact.php') ?>" class="hero-quick-btn hero-quick-btn-outline">Contact Us</a>
        </div>
    </div>
</div>

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
                    <h3 class="service-card-title">Trade Finance</h3>
                    <p class="service-card-text">
                        Providing comprehensive international trade finance, letters of credit (LC / SBLC), bank guarantees, structured working capital, and specialized transaction support.
                    </p>
                    <a href="<?= url('finance.php') ?>" class="service-card-link">Read More +</a>
                </div>
            </div>

            <!-- Service 3: Consulting -->
            <div class="service-card">
                <div class="service-card-image">
                    <img src="<?= image_url('consulting-home.jpg') ?>" alt="Financial Consulting" loading="lazy">
                </div>
                <div class="service-card-body">
                    <h3 class="service-card-title">Financial Advisory</h3>
                    <p class="service-card-text">
                        Strategic financial engineering, project financing, debt syndication, mergers &amp; acquisitions, and cross-border investment advisory across emerging markets.
                    </p>
                    <a href="<?= url('consulting.php') ?>" class="service-card-link">Read More +</a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Singapore Advantage Section -->
<section class="section">
    <div class="container">
        <div class="about-grid">
            <div class="about-image-wrapper">
                <img src="<?= image_url('international-trade-finance.jpg') ?>" alt="Singapore Financial Center" class="img-fluid" width="560" height="340">
            </div>
            <div>
                <span class="section-subtitle">Global Connectivity</span>
                <h2 class="section-title">Singapore – The World’s Premier Financial &amp; Trade Hub</h2>
                <p class="section-lead">
                    Strategically headquartered in Singapore with direct operational corridors into India, Southeast Asia, and global markets.
                </p>
                <div class="features-list">
                    <div class="feature-item">
                        <div class="feature-icon">🏛</div>
                        <div>
                            <h4 class="feature-title">Top 3 Global Financial Center</h4>
                            <p class="feature-desc">Ranked among the leading financial capitals globally, supported by robust regulatory frameworks and transparent legal systems.</p>
                        </div>
                    </div>
                    <div class="feature-item">
                        <div class="feature-icon">🛡</div>
                        <div>
                            <h4 class="feature-title">AAA Sovereign Credit Rating</h4>
                            <p class="feature-desc">Exceptional macroeconomic stability, political neutrality, and strong capital reserves providing complete safety for international transactions.</p>
                        </div>
                    </div>
                    <div class="feature-item">
                        <div class="feature-icon">⚡</div>
                        <div>
                            <h4 class="feature-title">Extensive Treaty &amp; Banking Network</h4>
                            <p class="feature-desc">Over 20+ Free Trade Agreements and 19+ partner banking institutions facilitating swift capital and goods movements.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Call to Action Section -->
<section class="section-cta">
    <div class="container cta-content">
        <h2 class="cta-title">Ready to Expand Your Global Trade &amp; Capital Reach?</h2>
        <p class="cta-lead">
            Partner with Global Connect for seamless sourcing, commodity allocations, and structured trade finance solutions tailored to your business needs.
        </p>
        <div style="display: flex; gap: 15px; justify-content: center; flex-wrap: wrap;">
            <a href="<?= url('contact.php') ?>" class="btn-gold" style="font-size: 16px; padding: 14px 32px;">Schedule an Executive Consultation</a>
            <a href="<?= url('trading.php') ?>" class="btn-outline-white" style="font-size: 16px; padding: 14px 28px;">Explore Product Portfolio</a>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
