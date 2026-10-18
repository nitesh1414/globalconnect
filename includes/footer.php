<?php
/**
 * Global Connect - Footer Component
 */
$tradingFooterPages = get_pages_by_category('trading');
$financeFooterPages = get_pages_by_category('finance');
?>

    <!-- Footer Area -->
    <footer class="site-footer">
        <div class="footer-container">
            <!-- Col 1: About & Registered Offices -->
            <div class="footer-col">
                <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 18px;">
                    <img src="<?= image_url('logo-design-gcv.png') ?>" alt="GCV Logo" style="height: 48px; width: 48px; border-radius: 50%; object-fit: cover; border: 1.5px solid var(--accent-gold); background: #000000; box-shadow: 0 2px 8px rgba(0,0,0,0.3);">
                    <div>
                        <div class="footer-logo" style="margin-bottom: 0;">GLOBAL <span>CONNECT</span></div>
                        <small style="color: var(--accent-gold); font-size: 11px; letter-spacing: 1px; font-weight: 600; text-transform: uppercase;">Ventures &bull; Singapore &bull; India</small>
                    </div>
                </div>
                <p class="footer-desc">
                    Global Connect is a premier international trade facilitator and financial structuring partner headquartered in Singapore and India, operating across Southeast Asia and global commodity hubs.
                </p>
                <div style="margin-top: 15px; font-size: 13px; color: var(--text-light);">
                    <p style="margin-bottom: 6px;"><strong>Singapore Global HQ:</strong><br>101 Kitchener Road #03-43, Jalan Besar Plaza, Singapore (208511)</p>
                    <p style="margin-bottom: 6px;"><strong>India Corporate Office:</strong><br>No. 211 Rajnigandha Block, Garden Apartment, 21 Vitthal Mallya Road, Bangalore 560001</p>
                </div>
            </div>

            <!-- Col 2: Trading Portfolio Links -->
            <div class="footer-col">
                <h4 class="footer-title">Trading Portfolio</h4>
                <ul class="footer-links">
                    <li><a href="<?= url('trading.php') ?>">All Trading Services</a></li>
                    <?php foreach (array_slice($tradingFooterPages, 0, 5) as $tp): ?>
                        <li><a href="<?= url('page.php?slug=' . urlencode($tp['slug'])) ?>"><?= e($tp['title']) ?></a></li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <!-- Col 3: Finance & Advisory -->
            <div class="footer-col">
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
            <div class="footer-col">
                <h4 class="footer-title">Global Contact Desk</h4>
                <div class="footer-contact-item">
                    <span class="footer-contact-icon">📞</span>
                    <div>
                        <small style="color: var(--text-muted); display: block;">Singapore Office:</small>
                        <a href="tel:+6589451100">(65) 89451100</a>
                    </div>
                </div>
                <div class="footer-contact-item">
                    <span class="footer-contact-icon">📱</span>
                    <div>
                        <small style="color: var(--text-muted); display: block;">Global Roaming (24/7):</small>
                        <a href="tel:+919910002299">(91) 99100 02299</a>
                    </div>
                </div>
                <div class="footer-contact-item">
                    <span class="footer-contact-icon">✉</span>
                    <div>
                        <small style="color: var(--text-muted); display: block;">Direct Enquiries:</small>
                        <a href="mailto:admin@globalconnectsg.com">admin@globalconnectsg.com</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="footer-bottom">
            <div class="footer-bottom-container">
                <div>
                    &copy; <?= date('Y') ?> <strong>Global Connect</strong>. All rights reserved. | Sourcing &amp; Supply Chain Strategic Partner.
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

    <!-- Core Scripts -->
    <script src="<?= asset('js/main.js') ?>"></script>
</body>
</html>
