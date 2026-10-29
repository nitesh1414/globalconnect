<?php
/**
 * Global Connect - Homepage
 * Fully Dynamic from Admin Settings with Modern Corporate Animations
 */
require_once __DIR__ . '/includes/functions.php';

$siteTitle = get_setting('site_title', 'Global Connect | Global Business Enterprise Headquartered in Singapore & India');
$pageTitle = $siteTitle;
$pageMetaDesc = get_setting('site_tagline', 'We are a global business enterprise headquartered in Singapore And India. Reliable partner for international trade services, trade finance, and financial advisory.');

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

// Dynamic Homepage Settings
$statPartnersNum = get_setting('stat_partners_num', '3,600+');
$statPartnersLabel = get_setting('stat_partners_label', 'Associate Partners');
$statCountriesNum = get_setting('stat_countries_num', '19+');
$statCountriesLabel = get_setting('stat_countries_label', 'Countries Globally');
$statBanksNum = get_setting('stat_banks_num', '19+');
$statBanksLabel = get_setting('stat_banks_label', 'Partner Banks');
$statExpNum = get_setting('stat_experience_num', '25+');
$statExpLabel = get_setting('stat_experience_label', 'Years Cumulative Experience');

$homeExpSubtitle = get_setting('home_exp_subtitle', 'Company Experience');
$homeExpTitle = get_setting('home_exp_title', 'Global Connect’s Believe in Growth Strategies');
$homeExpLead = get_setting('home_exp_lead', 'The organisation involves various teams to handle client needs which start from Capital Raising, Project Finance, Financial Engineering or Structuring, and Fund Raising programs to name a few.');
$homeExpBody = get_setting('home_exp_body', 'Fund raising is our daily work and we are always available to support your short term & long term needs. Global Connect involves itself with clients in achieving results. May it start with determining the most suitable structure to reach the end result. Global Connect is looking forward to participate with clients to translate their visions to reality.');
$homeExpImage = get_setting('home_exp_image', 'about-growth.jpg');

$homeServicesSubtitle = get_setting('home_services_subtitle', 'Our Capabilities');
$homeServicesTitle = get_setting('home_services_title', 'Comprehensive Trade & Financial Solutions');
$homeServicesLead = get_setting('home_services_lead', 'Delivering excellence across international commodity trading, custom trade banking solutions, and corporate financial advisory.');

$homeSingaporeSubtitle = get_setting('home_singapore_subtitle', 'Global Connectivity');
$homeSingaporeTitle = get_setting('home_singapore_title', 'Singapore – The World’s Premier Financial & Trade Hub');
$homeSingaporeLead = get_setting('home_singapore_lead', 'Strategically headquartered in Singapore with direct operational corridors into India, Southeast Asia, and global markets.');

$homeCtaTitle = get_setting('home_cta_title', 'Ready to Expand Your Global Trade & Capital Reach?');
$homeCtaLead = get_setting('home_cta_lead', 'Partner with Global Connect for seamless sourcing, commodity allocations, and structured trade finance solutions tailored to your business needs.');
$homeCtaBtn1Text = get_setting('home_cta_btn1_text', 'Schedule an Executive Consultation');
$homeCtaBtn1Link = get_setting('home_cta_btn1_link', 'contact.php');
$homeCtaBtn2Text = get_setting('home_cta_btn2_text', 'Explore Product Portfolio');
$homeCtaBtn2Link = get_setting('home_cta_btn2_link', 'trading.php');
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
                                <span class="hero-badge animate-badge"><?= e($slide['badge_text']) ?></span>
                            <?php endif; ?>
                            <h1 class="hero-title animate-title"><?= e($slide['title']) ?></h1>
                            <?php if (!empty($slide['subtitle'])): ?>
                                <p class="hero-subtitle animate-sub"><?= nl2br(e($slide['subtitle'])) ?></p>
                            <?php endif; ?>
                            <div class="hero-actions animate-actions">
                                <?php if (!empty($slide['button_text']) && !empty($slide['button_link'])): ?>
                                    <a href="<?= url($slide['button_link']) ?>" class="btn-primary shimmer-btn"><?= e($slide['button_text']) ?></a>
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

<!-- Quick Action Strip with Ambient Indicator -->
<div class="hero-quick-bar">
    <div class="container hero-quick-grid">
        <div class="hero-quick-tagline">
            <span class="pulsing-dot"></span>
            <span>Headquartered in <strong>Singapore</strong> &amp; <strong>India</strong> &bull; Presence in <strong>Indonesia</strong> &amp; <strong>Hong Kong</strong></span>
        </div>
        <div class="hero-quick-links">
            <a href="<?= url('trading.php') ?>" class="hero-quick-btn hero-quick-btn-primary">Explore Trading</a>
            <a href="<?= url('finance.php') ?>" class="hero-quick-btn hero-quick-btn-gold">Trade Finance</a>
            <a href="<?= url('contact.php') ?>" class="hero-quick-btn hero-quick-btn-outline">Contact Us</a>
        </div>
    </div>
</div>

<!-- Key Stats Counter Bar with Animated Live Counters -->
<section class="stats-section">
    <div class="container">
        <div class="stats-grid">
            <div class="stat-item reveal-up">
                <span class="stat-number count-up" data-value="<?= e($statPartnersNum) ?>"><?= e($statPartnersNum) ?></span>
                <span class="stat-label"><?= e($statPartnersLabel) ?></span>
            </div>
            <div class="stat-item reveal-up" style="transition-delay: 0.1s;">
                <span class="stat-number count-up" data-value="<?= e($statCountriesNum) ?>"><?= e($statCountriesNum) ?></span>
                <span class="stat-label"><?= e($statCountriesLabel) ?></span>
            </div>
            <div class="stat-item reveal-up" style="transition-delay: 0.2s;">
                <span class="stat-number count-up" data-value="<?= e($statBanksNum) ?>"><?= e($statBanksNum) ?></span>
                <span class="stat-label"><?= e($statBanksLabel) ?></span>
            </div>
            <div class="stat-item reveal-up" style="transition-delay: 0.3s;">
                <span class="stat-number count-up" data-value="<?= e($statExpNum) ?>"><?= e($statExpNum) ?></span>
                <span class="stat-label"><?= e($statExpLabel) ?></span>
            </div>
        </div>
    </div>
</section>

<!-- Company Experience & Philosophy (Dynamic from Admin Settings) -->
<section class="section">
    <div class="container">
        <div class="about-grid">
            <div class="reveal-left">
                <span class="section-subtitle"><?= e($homeExpSubtitle) ?></span>
                <h2 class="section-title"><?= e($homeExpTitle) ?></h2>
                <p class="section-lead">
                    <?= nl2br(e($homeExpLead)) ?>
                </p>
                <p style="margin-bottom: 25px; color: var(--text-muted); line-height: 1.75;">
                    <?= nl2br(e($homeExpBody)) ?>
                </p>
                <div style="display: flex; gap: 15px; flex-wrap: wrap;">
                    <a href="<?= url('about.php') ?>" class="btn-primary shimmer-btn">View More About Us →</a>
                    <a href="<?= url('contact.php') ?>" class="btn-outline-primary">Get Consultation</a>
                </div>
            </div>
            <div class="about-image-wrapper reveal-right">
                <div class="image-frame-glow">
                    <img src="<?= image_url($homeExpImage) ?>" alt="<?= e($homeExpTitle) ?>" class="img-fluid floating-img" width="560" height="340">
                </div>
            </div>
        </div>
    </div>
</section>

<!-- What We Do / Core Services Overview -->
<section class="section section-light">
    <div class="container">
        <div class="section-header reveal-up">
            <span class="section-subtitle"><?= e($homeServicesSubtitle) ?></span>
            <h2 class="section-title"><?= e($homeServicesTitle) ?></h2>
            <p class="section-lead">
                <?= e($homeServicesLead) ?>
            </p>
        </div>

        <div class="services-grid">
            <!-- Service 1: Trading -->
            <div class="service-card reveal-up hover-lift">
                <div class="service-card-image">
                    <img src="<?= image_url('trading-home.jpg') ?>" alt="Global Trading" loading="lazy">
                    <div class="service-badge">Trading</div>
                </div>
                <div class="service-card-body">
                    <h3 class="service-card-title">Trading Portfolio</h3>
                    <p class="service-card-text">
                        We trade in a variety of products and commodities all around the world including Gold Bullion, Sugar, Agro Products, Minerals, Metals &amp; Ores, and Petroleum &amp; Lubricants.
                    </p>
                    <a href="<?= url('trading.php') ?>" class="service-card-link">
                        <span>Read More</span>
                        <span class="link-arrow">+</span>
                    </a>
                </div>
            </div>

            <!-- Service 2: Finance -->
            <div class="service-card reveal-up hover-lift" style="transition-delay: 0.15s;">
                <div class="service-card-image">
                    <img src="<?= image_url('finance-home.jpg') ?>" alt="Trade Finance" loading="lazy">
                    <div class="service-badge badge-gold">Finance</div>
                </div>
                <div class="service-card-body">
                    <h3 class="service-card-title">Trade Finance</h3>
                    <p class="service-card-text">
                        Providing comprehensive international trade finance, letters of credit (LC / SBLC), bank guarantees, structured working capital, and specialized transaction support.
                    </p>
                    <a href="<?= url('finance.php') ?>" class="service-card-link">
                        <span>Read More</span>
                        <span class="link-arrow">+</span>
                    </a>
                </div>
            </div>

            <!-- Service 3: Consulting -->
            <div class="service-card reveal-up hover-lift" style="transition-delay: 0.3s;">
                <div class="service-card-image">
                    <img src="<?= image_url('consulting-home.jpg') ?>" alt="Financial Consulting" loading="lazy">
                    <div class="service-badge badge-dark">Advisory</div>
                </div>
                <div class="service-card-body">
                    <h3 class="service-card-title">Financial Advisory</h3>
                    <p class="service-card-text">
                        Strategic financial engineering, project financing, debt syndication, mergers &amp; acquisitions, and cross-border investment advisory across emerging markets.
                    </p>
                    <a href="<?= url('consulting.php') ?>" class="service-card-link">
                        <span>Read More</span>
                        <span class="link-arrow">+</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Singapore Advantage Section -->
<section class="section">
    <div class="container">
        <div class="about-grid">
            <div class="about-image-wrapper reveal-left">
                <div class="image-frame-glow">
                    <img src="<?= image_url('international-trade-finance.jpg') ?>" alt="Singapore Financial Center" class="img-fluid" width="560" height="340">
                </div>
            </div>
            <div class="reveal-right">
                <span class="section-subtitle"><?= e($homeSingaporeSubtitle) ?></span>
                <h2 class="section-title"><?= e($homeSingaporeTitle) ?></h2>
                <p class="section-lead">
                    <?= nl2br(e($homeSingaporeLead)) ?>
                </p>
                <div class="features-list">
                    <div class="feature-item reveal-up">
                        <div class="feature-icon feature-icon-anim">🏛</div>
                        <div>
                            <h4 class="feature-title">Top 3 Global Financial Center</h4>
                            <p class="feature-desc">Ranked among the leading financial capitals globally, supported by robust regulatory frameworks and transparent legal systems.</p>
                        </div>
                    </div>
                    <div class="feature-item reveal-up" style="transition-delay: 0.1s;">
                        <div class="feature-icon feature-icon-anim">🛡</div>
                        <div>
                            <h4 class="feature-title">AAA Sovereign Credit Rating</h4>
                            <p class="feature-desc">Exceptional macroeconomic stability, political neutrality, and strong capital reserves providing complete safety for international transactions.</p>
                        </div>
                    </div>
                    <div class="feature-item reveal-up" style="transition-delay: 0.2s;">
                        <div class="feature-icon feature-icon-anim">⚡</div>
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

<!-- Call to Action Section (Dynamic from Admin Settings) -->
<section class="section-cta reveal-up">
    <div class="container cta-content">
        <h2 class="cta-title"><?= e($homeCtaTitle) ?></h2>
        <p class="cta-lead">
            <?= nl2br(e($homeCtaLead)) ?>
        </p>
        <div style="display: flex; gap: 15px; justify-content: center; flex-wrap: wrap;">
            <?php if (!empty($homeCtaBtn1Text)): ?>
                <a href="<?= url($homeCtaBtn1Link) ?>" class="btn-gold shimmer-btn" style="font-size: 16px; padding: 14px 32px;"><?= e($homeCtaBtn1Text) ?></a>
            <?php endif; ?>
            <?php if (!empty($homeCtaBtn2Text)): ?>
                <a href="<?= url($homeCtaBtn2Link) ?>" class="btn-outline-white" style="font-size: 16px; padding: 14px 28px;"><?= e($homeCtaBtn2Text) ?></a>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
