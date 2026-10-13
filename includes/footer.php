<?php
/**
 * Global Connect - Public Footer Component
 */
$footerAbout = get_setting('footer_about', 'At Global Connect, we believe in building meaningful partnerships that drive success across borders. With our expertise and network, we connect businesses to new markets and lasting opportunities.');
$addrSg = get_setting('address_sg', '101 KITCHENER ROAD #03-43 JALAN BESAR PLAZA SINGAPORE (208511)');
$addrIn = get_setting('address_india', 'NO. 211 RAJNIGANDHA BLOCK, GARDEN APARTMENT, 21 VITTHAL MALLYA ROAD (opp. UB CITY) BANGLORE 560001 INDIA');
$phoneSg = get_setting('header_phone_sg', '(65) 89451100');
$phoneRoam = get_setting('header_phone_roam', '(91) 99100 02299');
$emailPrimary = get_setting('contact_email_primary', 'admin@globalconnectsg.com');

$tradingPages = get_pages_by_category('trading');
$financePages = get_pages_by_category('finance');
?>
    <!-- Site Footer -->
    <footer class="site-footer">
        <div class="container">
            <div class="footer-grid">
                <!-- Col 1: About -->
                <div class="footer-widget footer-about">
                    <h4>Global Connect</h4>
                    <p><?= e($footerAbout) ?></p>
                    <div class="footer-contact-item">
                        <strong>Singapore Mob:</strong> <?= e($phoneSg) ?>
                    </div>
                    <div class="footer-contact-item">
                        <strong>Global Roam:</strong> <?= e($phoneRoam) ?>
                    </div>
                    <div class="footer-contact-item">
                        <strong>Email:</strong> <a href="mailto:<?= e($emailPrimary) ?>"><?= e($emailPrimary) ?></a>
                    </div>
                </div>

                <!-- Col 2: Trading Links -->
                <div class="footer-widget">
                    <h4>Trading Products</h4>
                    <ul class="footer-links">
                        <li><a href="/trading.php">All Trading Services</a></li>
                        <?php foreach (array_slice($tradingPages, 0, 5) as $tp): ?>
                            <li><a href="/page.php?slug=<?= e($tp['slug']) ?>"><?= e($tp['title']) ?></a></li>
                        <?php endforeach; ?>
                    </ul>
                </div>

                <!-- Col 3: Finance Links -->
                <div class="footer-widget">
                    <h4>Finance Services</h4>
                    <ul class="footer-links">
                        <li><a href="/finance.php">All Finance Services</a></li>
                        <?php foreach (array_slice($financePages, 0, 5) as $fp): ?>
                            <li><a href="/page.php?slug=<?= e($fp['slug']) ?>"><?= e($fp['title']) ?></a></li>
                        <?php endforeach; ?>
                        <li><a href="/consulting.php">Corporate Consulting</a></li>
                    </ul>
                </div>

                <!-- Col 4: Corporate Locations -->
                <div class="footer-widget">
                    <h4>Headquarters</h4>
                    <div class="footer-contact-item">
                        <strong>Global HQ (Singapore):</strong>
                        <?= e($addrSg) ?>
                    </div>
                    <div class="footer-contact-item">
                        <strong>India HQ (Bangalore):</strong>
                        <?= e($addrIn) ?>
                    </div>
                    <div class="footer-contact-item">
                        <strong>Regional Presences:</strong>
                        Indonesia (Grand Hyatt Jakarta) • Hong Kong (Hyatt Regency) • New Delhi • Mumbai
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer Bottom Bar -->
        <div class="footer-bottom">
            <div class="footer-bottom-container">
                <p>&copy; <?= date('Y') ?> Global Connect. All Rights Reserved. Headquartered in Singapore & India.</p>
                <div>
                    <a href="/about.php">About</a> &bull; 
                    <a href="/trading.php">Trading</a> &bull; 
                    <a href="/finance.php">Finance</a> &bull; 
                    <a href="/contact.php">Contact</a> &bull; 
                    <a href="/admin/index.php" style="color: var(--accent-gold); font-weight: 600;">Admin Panel</a>
                </div>
            </div>
        </div>
    </footer>

    <script src="/assets/js/main.js"></script>
</body>
</html>
