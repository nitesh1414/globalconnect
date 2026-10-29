<?php
/**
 * Global Connect - Footer Component (100% Dynamic from Admin Settings)
 */
$tradingFooterPages = get_pages_by_category('trading');
$financeFooterPages = get_pages_by_category('finance');

// Dynamic settings
$siteLogo = get_setting('site_logo', 'logo-design-gcv.png');
$logoP1 = get_setting('logo_title_p1', 'GLOBAL');
$logoP2 = get_setting('logo_title_p2', 'CONNECT');
$logoTagline = get_setting('logo_tagline', 'Ventures • Singapore • India');
$footerAbout = get_setting('footer_about', 'Global Connect is a premier international trade facilitator and financial structuring partner headquartered in Singapore and India, operating across Southeast Asia and global commodity hubs.');
$addrSg = get_setting('address_sg', '101 Kitchener Road #03-43, Jalan Besar Plaza, Singapore (208511)');
$addrIndia = get_setting('address_india', 'No. 211 Rajnigandha Block, Garden Apartment, 21 Vitthal Mallya Road, Bangalore 560001');
$phoneSg = get_setting('header_phone_sg', '(65) 89451100');
$phoneRoam = get_setting('header_phone_roam', '(91) 99100 02299');
$emailPrimary = get_setting('contact_email_primary', 'admin@globalconnectsg.com');
$footerCopyright = get_setting('footer_copyright', '© ' . date('Y') . ' Global Connect. All rights reserved. | Sourcing & Supply Chain Strategic Partner.');
?>

    <!-- Floating Back To Top Button with Progress Ring -->
    <button type="button" class="back-to-top" id="backToTop" aria-label="Back to top">
        <svg class="progress-ring" width="48" height="48">
            <circle class="progress-ring-circle" stroke="rgba(212, 175, 55, 0.3)" stroke-width="3" fill="transparent" r="21" cx="24" cy="24"/>
            <circle class="progress-ring-circle-active" id="progressCircle" stroke="#d4af37" stroke-width="3" fill="transparent" r="21" cx="24" cy="24"/>
        </svg>
        <span class="back-to-top-icon">↑</span>
    </button>

    <!-- Footer Area -->
    <footer class="site-footer">
        <div class="footer-container">
            <!-- Col 1: About & Registered Offices -->
            <div class="footer-col reveal-up">
                <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 18px;">
                    <img src="<?= image_url($siteLogo) ?>" alt="GCV Logo" class="footer-logo-img">
                    <div>
                        <div class="footer-logo"><?= e($logoP1) ?> <span><?= e($logoP2) ?></span></div>
                        <small style="color: var(--accent-gold); font-size: 11px; letter-spacing: 1px; font-weight: 600; text-transform: uppercase;"><?= e($logoTagline) ?></small>
                    </div>
                </div>
                <p class="footer-desc">
                    <?= nl2br(e($footerAbout)) ?>
                </p>
                <div style="margin-top: 15px; font-size: 13px; color: var(--text-light);">
                    <p style="margin-bottom: 6px;"><strong>Singapore Global HQ:</strong><br><?= nl2br(e($addrSg)) ?></p>
                    <p style="margin-bottom: 6px;"><strong>India Corporate Office:</strong><br><?= nl2br(e($addrIndia)) ?></p>
                </div>
            </div>

            <!-- Col 2: Trading Portfolio Links -->
            <div class="footer-col reveal-up" style="transition-delay: 0.1s;">
                <h4 class="footer-title">Trading Portfolio</h4>
                <ul class="footer-links">
                    <li><a href="<?= url('trading.php') ?>">All Trading Services</a></li>
                    <?php foreach (array_slice($tradingFooterPages, 0, 5) as $tp): ?>
                        <li><a href="<?= url('page.php?slug=' . urlencode($tp['slug'])) ?>"><?= e($tp['title']) ?></a></li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <!-- Col 3: Finance & Advisory -->
            <div class="footer-col reveal-up" style="transition-delay: 0.2s;">
                <h4 class="footer-title">Finance &amp; Advisory</h4>
                <ul class="footer-links">
                    <li><a href="<?= url('finance.php') ?>">All Finance Services</a></li>
                    <?php foreach (array_slice($financeFooterPages, 0, 4) as $fp): ?>
                        <li><a href="<?= url('page.php?slug=' . urlencode($fp['slug'])) ?>"><?= e($fp['title']) ?></a></li>
                    <?php endforeach; ?>
                    <li><a href="<?= url('consulting.php') ?>">Corporate Consulting</a></li>
                </ul>
            </div>

            <!-- Col 4: Quick Contact Desk -->
            <div class="footer-col reveal-up" style="transition-delay: 0.3s;">
                <h4 class="footer-title">Global Contact Desk</h4>
                <div class="footer-contact-item">
                    <span class="footer-contact-icon">📞</span>
                    <div>
                        <small style="color: var(--text-muted); display: block;">Singapore Office:</small>
                        <a href="tel:<?= preg_replace('/[^0-9+]/', '', $phoneSg) ?>"><?= e($phoneSg) ?></a>
                    </div>
                </div>
                <div class="footer-contact-item">
                    <span class="footer-contact-icon">📱</span>
                    <div>
                        <small style="color: var(--text-muted); display: block;">Global Roaming (24/7):</small>
                        <a href="tel:<?= preg_replace('/[^0-9+]/', '', $phoneRoam) ?>"><?= e($phoneRoam) ?></a>
                    </div>
                </div>
                <div class="footer-contact-item">
                    <span class="footer-contact-icon">✉</span>
                    <div>
                        <small style="color: var(--text-muted); display: block;">Direct Enquiries:</small>
                        <a href="mailto:<?= e($emailPrimary) ?>"><?= e($emailPrimary) ?></a>
                    </div>
                </div>
            </div>
        </div>

        <div class="footer-bottom">
            <div class="footer-bottom-container">
                <div>
                    <?= e($footerCopyright) ?>
                </div>
                <div>
                    <a href="<?= url('about.php') ?>">About</a> &bull; 
                    <a href="<?= url('trading.php') ?>">Trading</a> &bull; 
                    <a href="<?= url('finance.php') ?>">Finance</a> &bull; 
                    <a href="<?= url('contact.php') ?>">Contact</a> &bull; 
                    <a href="<?= admin_url('index.php') ?>" style="color: var(--accent-gold); font-weight: 600;">Admin Panel</a>
                </div>
            </div>
        </div>
    </footer>

    <!-- Core Scripts with animations and counter observers -->
    <script src="<?= asset('js/main.js') ?>"></script>
</body>
</html>
