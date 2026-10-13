<?php
/**
 * Global Connect - About Us Page
 */
$pageTitle = 'About Us | Global Connect';
$pageMetaDesc = 'Global Connect’s believe in Growth Strategies. We handle client needs from Capital Raising, Project Finance, Financial Engineering or Structuring, Fund raising programs.';

require_once __DIR__ . '/includes/header.php';
?>

<!-- Page Banner -->
<div class="page-banner">
    <div class="container">
        <h1 class="page-banner-title">About Us</h1>
        <p class="page-banner-subtitle">Building meaningful partnerships and growth strategies across borders</p>
        <div class="breadcrumb-nav">
            <a href="/index.php">Home</a> <span>/</span> <span>About Us</span>
        </div>
    </div>
</div>

<div class="page-content-wrapper">
    <div class="container">
        <div class="row" style="display: grid; grid-template-columns: 1fr; gap: 40px;">
            <!-- Main Content -->
            <div>
                <span class="section-subtitle">Our Philosophy</span>
                <h2 style="font-size: 28px; color: var(--primary-color); font-weight: 800; line-height: 1.35; margin-bottom: 20px;">
                    Global Connect’s believe in Growth Strategies. The organisation involves various teams to handle client needs which start from Capital Raising, Project Finance, Financial Engineering or Structuring, Fund raising programs to name a few.
                </h2>

                <p style="font-size: 16.5px; color: var(--text-main); line-height: 1.8; margin-bottom: 20px;">
                    Fund raising is our daily work and we are always available to support your short term &amp; long term needs. Global Connect’s involves itself with clients in achieving results. May it start with determining you to achieve the most suitable structure to reach the end result. Global Connect’s is looking forward to participate with the clients to translate their visions to reality – which is differently worded in our Tag line itself. Global Connect’s Team is a combination of Business, Functional, Financial, Technical and operational skills to align with the activities of the Businesses and Promoters Skills for achieving highest results.
                </p>

                <div style="margin: 35px 0;">
                    <img src="/assets/images/about-growth.jpg" alt="Growth Ideas and Financial Strategies" class="img-fluid" style="border-radius: var(--radius-md); box-shadow: var(--shadow-md); width: 100%; max-height: 480px; object-fit: cover;">
                </div>

                <!-- Our Expertise -->
                <div style="margin-top: 50px;">
                    <span class="section-subtitle">Our Expertise</span>
                    <h3 style="font-size: 30px; color: var(--primary-color); font-weight: 800; margin-bottom: 12px;">What Makes Us Unique</h3>
                    <p style="font-size: 16.5px; color: var(--text-muted); margin-bottom: 35px;">
                        Global Connect’s group provides solutions to Institutions and Corporate in managing and growing their business via high quality financial advisory services and capital market solutions.
                    </p>

                    <div class="services-grid">
                        <!-- Pillar 1 -->
                        <div class="service-card">
                            <div class="service-card-image">
                                <img src="/assets/images/financial-structure.jpg" alt="Financial Structuring">
                            </div>
                            <div class="service-card-body">
                                <h4 class="service-card-title">Financial Structuring</h4>
                                <p class="service-card-text">
                                    We design optimal financial frameworks that balance growth, risk, and returns to strengthen your organization’s financial foundation.
                                </p>
                            </div>
                        </div>

                        <!-- Pillar 2 -->
                        <div class="service-card">
                            <div class="service-card-image">
                                <img src="/assets/images/international-trade-finance.jpg" alt="International Trade Finance">
                            </div>
                            <div class="service-card-body">
                                <h4 class="service-card-title">International Trade Finance</h4>
                                <p class="service-card-text">
                                    Our trade finance solutions empower seamless global transactions, ensuring smooth cross-border operations and risk management.
                                </p>
                            </div>
                        </div>

                        <!-- Pillar 3 -->
                        <div class="service-card">
                            <div class="service-card-image">
                                <img src="/assets/images/merge-acquisition.jpg" alt="Merge and Acquisition">
                            </div>
                            <div class="service-card-body">
                                <h4 class="service-card-title">Merge and Acquisition</h4>
                                <p class="service-card-text">
                                    We provide end-to-end advisory for mergers and acquisitions, driving strategic growth and maximizing shareholder value.
                                </p>
                            </div>
                        </div>

                        <!-- Pillar 4 -->
                        <div class="service-card">
                            <div class="service-card-image">
                                <img src="/assets/images/off-shore-fund-raising.jpg" alt="Off Shore Fund Raising">
                            </div>
                            <div class="service-card-body">
                                <h4 class="service-card-title">Off Shore Fund Raising</h4>
                                <p class="service-card-text">
                                    We help businesses access global capital markets through efficient offshore fund-raising strategies and investor networks.
                                </p>
                            </div>
                        </div>

                        <!-- Pillar 5 -->
                        <div class="service-card">
                            <div class="service-card-image">
                                <img src="/assets/images/unlocking-values.jpg" alt="Unlocking Values">
                            </div>
                            <div class="service-card-body">
                                <h4 class="service-card-title">Unlocking Values</h4>
                                <p class="service-card-text">
                                    Our experts identify hidden opportunities to enhance business performance and unlock sustainable long-term value.
                                </p>
                            </div>
                        </div>

                        <!-- Pillar 6 -->
                        <div class="service-card">
                            <div class="service-card-image">
                                <img src="/assets/images/debt-syndication.jpg" alt="Debt Syndication">
                            </div>
                            <div class="service-card-body">
                                <h4 class="service-card-title">Debt Syndication</h4>
                                <p class="service-card-text">
                                    We structure and syndicate debt efficiently, connecting clients with leading financial institutions to secure optimal funding.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
