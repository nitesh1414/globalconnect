<?php
/**
 * Global Connect - Contact Us Page (100% Dynamic from Admin Settings & Executive Management)
 * Corporate & Modern Redesign with Right-Side Enquiry Form & Structured Office + Leadership Directory
 */
require_once __DIR__ . '/includes/functions.php';

$pageTitle = 'Contact Us | ' . get_setting('company_name', 'Global Connect');
$pageMetaDesc = 'Contact Global Connect Singapore Global Headquarters, Bangalore India HQ, Indonesia & Hong Kong offices, and Key Executive Directors.';

require_once __DIR__ . '/includes/header.php';

$successMessage = '';
$errorMessage = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['csrf_token'] ?? '')) {
        $errorMessage = 'Security validation failed. Please refresh and try again.';
    } else {
        $name = clean($_POST['name'] ?? '');
        $email = clean($_POST['email'] ?? '');
        $phone = clean($_POST['phone'] ?? '');
        $subject = clean($_POST['subject'] ?? '');
        $message = clean($_POST['message'] ?? '');
        $ip = $_SERVER['REMOTE_ADDR'] ?? '';

        if (empty($name) || empty($email) || empty($message)) {
            $errorMessage = 'Please complete all required fields (Name, Email, Message).';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errorMessage = 'Please provide a valid email address.';
        } else {
            try {
                $db = get_db();
                $stmt = $db->prepare("INSERT INTO contacts (name, email, phone, subject, message, ip_address, created_at) VALUES (?, ?, ?, ?, ?, ?, CURRENT_TIMESTAMP)");
                $stmt->execute([$name, $email, $phone, $subject, $message, $ip]);
                $successMessage = 'Thank you! Your inquiry has been transmitted successfully to our trade desk. An executive will respond promptly.';
                $_POST = [];
            } catch (Exception $e) {
                $errorMessage = 'We encountered an error saving your message. Please reach us via direct email or telephone.';
            }
        }
    }
}

// Dynamic Contact Settings
$companyName = get_setting('company_name', 'GLOBAL CONNECT');
$addrSg = get_setting('address_sg', '101 KITCHENER ROAD #03-43 JALAN BESAR PLAZA SINGAPORE (208511)');
$addrIndia = get_setting('address_india', 'NO. 211 RAJNIGANDHA BLOCK, GARDEN APARTMENT, 21 VITTHAL MALLYA ROAD (opp. UB CITY) BANGLORE 560001 INDIA');
$addrBranches = get_setting('address_branches', 'New Delhi, Mumbai, Hyderabad and Chennai (By Appointment ONLY – IN HYATT GROUP OF HOTELS)');
$addrIndonesia = get_setting('address_indonesia', 'HOTEL GRAND HYATT PLAZA, JL. M.H. THAMRIN KAV. 28-30, GONDANGDIA, MENTENG, JAKARTA, 10350, INDONESIA');
$addrHongkong = get_setting('address_hongkong', 'HOTEL HYATT REGENCY 18 HANOI ROAD TSIM SHA TSU KOWLOON HONGKONG');
$phoneSg = get_setting('header_phone_sg', '(65) 89451100');
$phoneRoam = get_setting('header_phone_roam', '(91) 99100 02299');
$faxNumber = get_setting('fax_number', '(65) 98260001 / (91) 99100 02299');
$emailPrimary = get_setting('contact_email_primary', 'admin@globalconnectsg.com');
$emailSecondary = get_setting('contact_email_secondary', 'shammy@globalconnectsg.com');
$emailTertiary = get_setting('contact_email_tertiary', 'tsnshammy@gmail.com');

// Executive Leadership Team (Loaded dynamically from database)
$executives = get_executives(true);
?>

<!-- Page Banner -->
<div class="page-banner reveal-up">
    <div class="container">
        <span class="banner-badge" style="background: rgba(212,175,55,0.15); color: var(--accent-gold-light); border: 1px solid rgba(212,175,55,0.3); padding: 4px 14px; border-radius: var(--radius-full); font-size: 13px; font-weight: 600; display: inline-block; margin-bottom: 12px; letter-spacing: 0.5px;">
            SINGAPORE • INDIA • GLOBAL NETWORK
        </span>
        <h1 class="page-banner-title">Get in Touch</h1>
        <p class="page-banner-subtitle">Singapore Global Headquarters • India Corporate Office • Global Regional Desks</p>
        <div class="breadcrumb-nav">
            <a href="<?= url('index.php') ?>">Home</a> <span>/</span> <span>Contact Us</span>
        </div>
    </div>
</div>

<!-- Quick Communication Highlights Bar -->
<div class="contact-quick-bar">
    <div class="container">
        <div class="quick-bar-grid">
            <a href="tel:<?= preg_replace('/[^0-9+]/', '', $phoneSg) ?>" class="quick-bar-item hover-lift">
                <div class="quick-icon-box">🇸🇬</div>
                <div class="quick-info">
                    <span class="quick-label">Singapore Global HQ</span>
                    <strong class="quick-val"><?= e($phoneSg) ?></strong>
                </div>
            </a>

            <a href="tel:<?= preg_replace('/[^0-9+]/', '', $phoneRoam) ?>" class="quick-bar-item hover-lift">
                <div class="quick-icon-box">🇮🇳</div>
                <div class="quick-info">
                    <span class="quick-label">24/7 Global Roaming</span>
                    <strong class="quick-val"><?= e($phoneRoam) ?></strong>
                </div>
            </a>

            <a href="mailto:<?= e($emailPrimary) ?>" class="quick-bar-item hover-lift">
                <div class="quick-icon-box">✉</div>
                <div class="quick-info">
                    <span class="quick-label">Corporate Enquiries</span>
                    <strong class="quick-val"><?= e($emailPrimary) ?></strong>
                </div>
            </a>

            <div class="quick-bar-item">
                <div class="quick-icon-box">📠</div>
                <div class="quick-info">
                    <span class="quick-label">Official Fax</span>
                    <strong class="quick-val" style="font-size: 13px;"><?= e($faxNumber) ?></strong>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="page-content-wrapper" style="padding-top: 50px;">
    <div class="container">
        
        <!-- Main Layout: Office Locations on LEFT (1.1fr) | Send Message Form on RIGHT (1fr) -->
        <div class="contact-main-grid">
            
            <!-- LEFT COLUMN: Structured Office Addresses & Global Presence -->
            <div class="reveal-left office-section">
                <div class="section-header-compact">
                    <span class="section-subtitle">Our Global Presence</span>
                    <h2 class="section-title">International Corporate Offices</h2>
                    <p class="section-lead" style="margin-bottom: 25px;">
                        Headquartered at the world's premier trade and financial gateway in Singapore, with direct operational desks across India, Indonesia, and Hong Kong.
                    </p>
                </div>

                <!-- 1. Singapore Global HQ Card -->
                <div class="office-card primary-office hover-lift">
                    <div class="office-card-header">
                        <div class="office-flag-title">
                            <span class="country-badge">🇸🇬 SINGAPORE</span>
                            <h3 class="office-name">Global Headquarter</h3>
                        </div>
                        <span class="office-tag">Main Hub</span>
                    </div>
                    <div class="office-entity-name"><?= e($companyName) ?></div>
                    <p class="office-address">
                        <span class="addr-icon">📍</span>
                        <span><?= nl2br(e($addrSg)) ?></span>
                    </p>
                    <div class="office-contact-rows">
                        <div class="contact-row">
                            <span class="row-label">Direct Mobile / WhatsApp:</span>
                            <a href="tel:<?= preg_replace('/[^0-9+]/', '', $phoneSg) ?>" class="row-val-highlight"><?= e($phoneSg) ?></a>
                        </div>
                        <div class="contact-row">
                            <span class="row-label">Corporate Email:</span>
                            <a href="mailto:<?= e($emailPrimary) ?>" class="row-val"><?= e($emailPrimary) ?></a>
                        </div>
                        <div class="contact-row">
                            <span class="row-label">Official Fax:</span>
                            <span class="row-val-text"><?= e($faxNumber) ?></span>
                        </div>
                    </div>
                    <div class="office-footer-badge">
                        <span class="status-dot"></span> Mon – Fri: 09:00 – 18:00 (SGT / UTC+8)
                    </div>
                </div>

                <!-- 2. India Corporate HQ Card -->
                <div class="office-card hover-lift" style="margin-top: 20px;">
                    <div class="office-card-header">
                        <div class="office-flag-title">
                            <span class="country-badge" style="background: rgba(249, 115, 22, 0.1); color: #c2410c; border-color: rgba(249, 115, 22, 0.25);">🇮🇳 INDIA</span>
                            <h3 class="office-name">India Corporate Office</h3>
                        </div>
                        <span class="office-tag" style="background: rgba(14, 165, 233, 0.1); color: #0284c7; border-color: rgba(14, 165, 233, 0.2);">Bangalore Hub</span>
                    </div>
                    <p class="office-address">
                        <span class="addr-icon">📍</span>
                        <span><?= nl2br(e($addrIndia)) ?></span>
                    </p>
                    <div class="office-contact-rows">
                        <div class="contact-row">
                            <span class="row-label">24/7 Global Roaming No:</span>
                            <a href="tel:<?= preg_replace('/[^0-9+]/', '', $phoneRoam) ?>" class="row-val-highlight"><?= e($phoneRoam) ?></a>
                        </div>
                        <div class="contact-row">
                            <span class="row-label">Executive Mail:</span>
                            <a href="mailto:<?= e($emailSecondary) ?>" class="row-val"><?= e($emailSecondary) ?></a>
                        </div>
                        <div class="contact-row">
                            <span class="row-label">Alternate Mail:</span>
                            <a href="mailto:<?= e($emailTertiary) ?>" class="row-val"><?= e($emailTertiary) ?></a>
                        </div>
                    </div>
                    <div class="office-footer-badge" style="background: #f0fdf4; color: #166534; border-color: #bbf7d0;">
                        <span class="status-dot" style="background: #22c55e;"></span> 24/7 Global Priority Roaming Desk Active
                    </div>
                </div>

                <!-- 3. Indonesia & Hong Kong & India Branches Card -->
                <div class="office-card branch-office hover-lift" style="margin-top: 20px;">
                    <div class="office-card-header">
                        <div class="office-flag-title">
                            <span class="country-badge" style="background: rgba(16, 185, 129, 0.1); color: #047857; border-color: rgba(16, 185, 129, 0.25);">🌏 REGIONAL DESKS</span>
                            <h3 class="office-name">International Branch Desks</h3>
                        </div>
                    </div>
                    <div class="branch-locations-list">
                        <div class="branch-item">
                            <div class="branch-header">
                                <strong>🇮🇩 Indonesia (Jakarta Desk)</strong>
                            </div>
                            <p class="branch-desc"><?= e($addrIndonesia) ?></p>
                        </div>
                        <div class="branch-item">
                            <div class="branch-header">
                                <strong>🇭🇰 Hong Kong Corporate Desk</strong>
                            </div>
                            <p class="branch-desc"><?= e($addrHongkong) ?></p>
                        </div>
                        <div class="branch-item">
                            <div class="branch-header">
                                <strong>🇮🇳 India Branches Network</strong>
                            </div>
                            <p class="branch-desc"><?= e($addrBranches) ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- RIGHT COLUMN: Send Us a Message (Interactive Inquiry Form) -->
            <div class="reveal-right form-section">
                <div class="contact-form-wrapper">
                    <div class="form-card-header">
                        <div class="form-header-badge">DIRECT ENQUIRY DESK</div>
                        <h2 class="form-header-title">Send Us a Message</h2>
                        <p class="form-header-lead">
                            Have an inquiry regarding commodity supply, trade finance facilities, or corporate advisory? Fill out the form below and our trade team will respond within 24 hours.
                        </p>
                    </div>

                    <?php if ($successMessage): ?>
                        <div class="alert alert-success">
                            <div class="alert-icon">✓</div>
                            <div class="alert-content">
                                <strong>Inquiry Submitted!</strong>
                                <p><?= e($successMessage) ?></p>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if ($errorMessage): ?>
                        <div class="alert alert-danger">
                            <div class="alert-icon">⚠</div>
                            <div class="alert-content">
                                <strong>Submission Error</strong>
                                <p><?= e($errorMessage) ?></p>
                            </div>
                        </div>
                    <?php endif; ?>

                    <form action="<?= url('contact.php') ?>" method="POST" class="styled-contact-form">
                        <?= csrf_field() ?>

                        <div class="form-row-2">
                            <div class="form-group">
                                <label class="form-label" for="name">Your Full Name <span class="req">*</span></label>
                                <div class="input-with-icon">
                                    <input type="text" id="name" name="name" class="form-control" required placeholder="e.g. Johnathan Tan" value="<?= e($_POST['name'] ?? '') ?>">
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="form-label" for="email">Corporate Email Address <span class="req">*</span></label>
                                <div class="input-with-icon">
                                    <input type="email" id="email" name="email" class="form-control" required placeholder="e.g. jtan@company.com" value="<?= e($_POST['email'] ?? '') ?>">
                                </div>
                            </div>
                        </div>

                        <div class="form-row-2">
                            <div class="form-group">
                                <label class="form-label" for="phone">Phone / WhatsApp Number</label>
                                <input type="text" id="phone" name="phone" class="form-control" placeholder="e.g. +65 8945 1100" value="<?= e($_POST['phone'] ?? '') ?>">
                            </div>

                            <div class="form-group">
                                <label class="form-label" for="subject">Area of Interest / Inquiry</label>
                                <select id="subject" name="subject" class="form-control custom-select">
                                    <option value="Gold Bullion & Precious Metals" <?= ($_POST['subject'] ?? '') === 'Gold Bullion & Precious Metals' ? 'selected' : '' ?>>Precious Metals &amp; Gold Bullion</option>
                                    <option value="Sugar & Soft Commodities" <?= ($_POST['subject'] ?? '') === 'Sugar & Soft Commodities' ? 'selected' : '' ?>>Sugar (ICUMSA 45) &amp; Soft Commodities</option>
                                    <option value="Agro Products & Rice" <?= ($_POST['subject'] ?? '') === 'Agro Products & Rice' ? 'selected' : '' ?>>Agro Products, Rice &amp; Edible Oils</option>
                                    <option value="Minerals, Metals & Ores" <?= ($_POST['subject'] ?? '') === 'Minerals, Metals & Ores' ? 'selected' : '' ?>>Minerals, Metals, Ores &amp; MICA</option>
                                    <option value="Petroleum & Lubricants" <?= ($_POST['subject'] ?? '') === 'Petroleum & Lubricants' ? 'selected' : '' ?>>Petroleum &amp; Lubricants (Mazut / Gas Oil)</option>
                                    <option value="Trade Finance / LC / SBLC" <?= ($_POST['subject'] ?? '') === 'Trade Finance / LC / SBLC' ? 'selected' : '' ?>>Trade Finance (DLC / SBLC / Transferable / BG)</option>
                                    <option value="Banking Support & Wire Transfers" <?= ($_POST['subject'] ?? '') === 'Banking Support & Wire Transfers' ? 'selected' : '' ?>>Banking Support &amp; International Transfers</option>
                                    <option value="Corporate Consulting & M&A" <?= ($_POST['subject'] ?? '') === 'Corporate Consulting & M&A' ? 'selected' : '' ?>>Corporate Consulting, M&amp;A &amp; Capital Advisory</option>
                                    <option value="General Enquiry" <?= ($_POST['subject'] ?? '') === 'General Enquiry' ? 'selected' : '' ?>>General Corporate Enquiry</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="message">Detailed Message / Specifications <span class="req">*</span></label>
                            <textarea id="message" name="message" class="form-control" rows="5" required placeholder="Please outline your commodity specifications, quantities, target discharge port, or trade financing structure..."><?= e($_POST['message'] ?? '') ?></textarea>
                        </div>

                        <div class="form-footer-action">
                            <button type="submit" class="btn-primary shimmer-btn submit-btn">
                                <span>Send Official Enquiry</span>
                                <span class="btn-arrow">→</span>
                            </button>
                            <span class="privacy-note">
                                🔒 Institutional confidentiality &amp; NDA compliance assured.
                            </span>
                        </div>
                    </form>
                </div>
            </div>

        </div>

        <!-- SECTION: Key Executive Contacts & Directors (Leadership Team) -->
        <div class="executive-leadership-section reveal-up">
            <div class="leadership-section-header">
                <div class="badge-tag">EXECUTIVE BOARD &amp; MANAGEMENT</div>
                <h2 class="section-title">Key Executive Contacts &amp; Directors</h2>
                <p class="section-lead">
                    Direct access to our founding directors, regional heads, and corporate advisory specialists across Singapore, India, and international markets.
                </p>
            </div>

            <div class="executive-grid">
                <?php foreach ($executives as $idx => $exec): 
                    $cleanName = preg_replace('/^(Mr\.?|Ms\.?|Mrs\.?|Dr\.?)\s+/i', '', $exec['name']);
                    $parts = preg_split('/\s+/', trim($cleanName));
                    $initials = '';
                    if (!empty($parts[0])) $initials .= mb_substr($parts[0], 0, 1);
                    if (!empty($parts[1])) $initials .= mb_substr($parts[1], 0, 1);
                    if (empty($initials)) $initials = mb_substr($exec['name'], 0, 2);
                ?>
                    <div class="executive-card hover-lift reveal-up" style="transition-delay: <?= ($idx * 0.05) ?>s;">
                        <div class="exec-card-top">
                            <div class="exec-avatar-wrapper">
                                <?php if (!empty($exec['avatar'])): ?>
                                    <img src="<?= e(image_url($exec['avatar'])) ?>" alt="<?= e($exec['name']) ?>" class="exec-avatar-img">
                                <?php else: ?>
                                    <div class="exec-monogram-avatar">
                                        <?= e(strtoupper($initials)) ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="exec-main-meta">
                                <h3 class="exec-name"><?= e($exec['name']) ?></h3>
                                <div class="exec-role-pill"><?= e($exec['role']) ?></div>
                            </div>
                        </div>

                        <div class="exec-card-details">
                            <?php if (!empty($exec['location'])): ?>
                                <div class="exec-detail-item">
                                    <span class="detail-icon">📍</span>
                                    <span class="detail-text"><?= e($exec['location']) ?></span>
                                </div>
                            <?php endif; ?>

                            <?php if (!empty($exec['phone'])): ?>
                                <div class="exec-detail-item">
                                    <span class="detail-icon">📞</span>
                                    <a href="tel:<?= preg_replace('/[^0-9+]/', '', $exec['phone']) ?>" class="detail-link detail-phone">
                                        <?= e($exec['phone']) ?>
                                    </a>
                                </div>
                            <?php endif; ?>

                            <div class="exec-detail-item">
                                <span class="detail-icon">✉</span>
                                <a href="mailto:<?= e($exec['email']) ?>" class="detail-link detail-email" title="Send direct email to <?= e($exec['name']) ?>">
                                    <?= e($exec['email']) ?>
                                </a>
                            </div>

                            <?php if (!empty($exec['bio'])): ?>
                                <p class="exec-bio-excerpt"><?= nl2br(e($exec['bio'])) ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Institutional Compliance & Delivery Protocol Note -->
        <div class="compliance-protocol-card reveal-up">
            <div class="protocol-content">
                <div class="protocol-icon-box">🛡</div>
                <div class="protocol-text">
                    <h4>Institutional Delivery &amp; Account Compliance Notice</h4>
                    <p>
                        All physical deliveries of precious metals and commodity contracts must be executed under the registered entity name of <strong>"GLOBAL CONNECT"</strong> with prior written consent and authorization. Assaying receipts must be forwarded immediately to <strong><?= e($emailSecondary) ?></strong> or via fax to <strong><?= e($faxNumber) ?></strong>.
                    </p>
                </div>
            </div>
        </div>

    </div>
</div>

<style>
/* Page Banner Enhancements */
.page-banner {
    background: var(--bg-navy-gradient);
    color: #ffffff;
    padding: 70px 0 55px;
    text-align: center;
    position: relative;
    border-bottom: 1px solid rgba(255, 255, 255, 0.08);
}
.page-banner-title {
    font-size: 42px;
    font-weight: 800;
    margin-bottom: 10px;
    letter-spacing: -0.5px;
    color: #ffffff;
}
.page-banner-subtitle {
    font-size: 16px;
    color: var(--accent-gold-light);
    margin-bottom: 16px;
    max-width: 720px;
    margin-left: auto;
    margin-right: auto;
}
.breadcrumb-nav {
    font-size: 13.5px;
    color: #94a3b8;
}
.breadcrumb-nav a {
    color: #cbd5e1;
    text-decoration: none;
    transition: var(--transition-fast);
}
.breadcrumb-nav a:hover {
    color: var(--accent-gold);
}

/* Quick Communication Highlights Bar */
.contact-quick-bar {
    background: #03182e;
    border-bottom: 1px solid rgba(212, 175, 55, 0.2);
    padding: 18px 0;
}
.quick-bar-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 20px;
}
.quick-bar-item {
    background: rgba(255, 255, 255, 0.04);
    border: 1px solid rgba(255, 255, 255, 0.08);
    border-radius: var(--radius-md);
    padding: 14px 18px;
    display: flex;
    align-items: center;
    gap: 14px;
    text-decoration: none;
    transition: var(--transition);
}
.quick-bar-item:hover {
    background: rgba(255, 255, 255, 0.08);
    border-color: rgba(212, 175, 55, 0.4);
    transform: translateY(-2px);
}
.quick-icon-box {
    width: 42px;
    height: 42px;
    border-radius: var(--radius-sm);
    background: rgba(212, 175, 55, 0.12);
    border: 1px solid rgba(212, 175, 55, 0.3);
    color: var(--accent-gold-light);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    flex-shrink: 0;
}
.quick-info {
    display: flex;
    flex-direction: column;
    min-width: 0;
}
.quick-label {
    font-size: 11.5px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: #94a3b8;
    margin-bottom: 2px;
}
.quick-val {
    font-size: 14px;
    font-weight: 700;
    color: #ffffff;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

/* Main Layout Grid: Office (Left) vs Form (Right) */
.contact-main-grid {
    display: grid;
    grid-template-columns: 1.15fr 1fr;
    gap: 40px;
    align-items: start;
    margin-bottom: 70px;
}

/* Office Section (Left Column) */
.section-header-compact .section-subtitle {
    display: inline-block;
    font-size: 12.5px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 1px;
    color: var(--secondary-color);
    margin-bottom: 8px;
}
.section-header-compact .section-title {
    font-size: 28px;
    font-weight: 800;
    color: var(--primary-color);
    margin-bottom: 12px;
    letter-spacing: -0.3px;
}
.section-header-compact .section-lead {
    font-size: 14.5px;
    color: var(--text-muted);
    line-height: 1.6;
}

/* Office Cards */
.office-card {
    background: #ffffff;
    border: 1px solid var(--border-color);
    border-radius: var(--radius-lg);
    padding: 26px;
    box-shadow: var(--shadow-sm);
    transition: var(--transition);
    position: relative;
}
.office-card.primary-office {
    border: 1.5px solid rgba(2, 132, 199, 0.35);
    box-shadow: 0 4px 20px rgba(2, 132, 199, 0.08);
}
.office-card.primary-office:hover {
    border-color: var(--secondary-color);
    box-shadow: 0 10px 30px rgba(2, 132, 199, 0.15);
}
.office-card-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 12px;
    border-bottom: 1px solid var(--border-color);
    padding-bottom: 12px;
}
.office-flag-title {
    display: flex;
    align-items: center;
    gap: 10px;
}
.country-badge {
    font-size: 11.5px;
    font-weight: 700;
    padding: 3px 8px;
    border-radius: var(--radius-sm);
    background: rgba(2, 132, 199, 0.1);
    color: var(--secondary-color);
    border: 1px solid rgba(2, 132, 199, 0.2);
    letter-spacing: 0.5px;
}
.office-name {
    font-size: 17px;
    font-weight: 800;
    color: var(--primary-color);
    margin: 0;
}
.office-tag {
    font-size: 11px;
    font-weight: 700;
    padding: 3px 10px;
    border-radius: var(--radius-full);
    background: rgba(212, 175, 55, 0.15);
    color: var(--accent-gold-dark);
    border: 1px solid rgba(212, 175, 55, 0.3);
    text-transform: uppercase;
}
.office-entity-name {
    font-size: 13.5px;
    font-weight: 700;
    color: var(--secondary-hover);
    margin-bottom: 8px;
}
.office-address {
    font-size: 13.5px;
    color: #475569;
    line-height: 1.6;
    margin-bottom: 16px;
    display: flex;
    gap: 8px;
    align-items: flex-start;
}
.addr-icon {
    flex-shrink: 0;
    font-size: 15px;
    margin-top: 1px;
}
.office-contact-rows {
    background: #f8fafc;
    border-radius: var(--radius-sm);
    padding: 12px 16px;
    margin-bottom: 14px;
    border: 1px solid #e2e8f0;
}
.contact-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 5px 0;
    font-size: 13px;
    border-bottom: 1px dashed #e2e8f0;
}
.contact-row:last-child {
    border-bottom: none;
}
.row-label {
    color: var(--text-muted);
    font-weight: 500;
}
.row-val {
    color: var(--secondary-color);
    font-weight: 600;
    text-decoration: none;
    transition: var(--transition-fast);
}
.row-val:hover {
    color: var(--secondary-hover);
    text-decoration: underline;
}
.row-val-highlight {
    color: #0284c7;
    font-weight: 700;
    text-decoration: none;
    font-size: 13.5px;
}
.row-val-highlight:hover {
    color: #0369a1;
    text-decoration: underline;
}
.row-val-text {
    color: #334155;
    font-weight: 600;
}
.office-footer-badge {
    font-size: 12px;
    color: #475569;
    display: flex;
    align-items: center;
    gap: 6px;
    background: #f1f5f9;
    padding: 6px 12px;
    border-radius: var(--radius-sm);
    border: 1px solid #e2e8f0;
}
.status-dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: #22c55e;
    display: inline-block;
    box-shadow: 0 0 0 2px rgba(34, 197, 94, 0.2);
}

/* Branch List */
.branch-locations-list {
    display: flex;
    flex-direction: column;
    gap: 14px;
}
.branch-item {
    background: #f8fafc;
    border-radius: var(--radius-sm);
    padding: 12px 14px;
    border: 1px solid #e2e8f0;
}
.branch-header {
    font-size: 13.5px;
    color: var(--primary-color);
    margin-bottom: 4px;
}
.branch-desc {
    font-size: 12.5px;
    color: var(--text-muted);
    line-height: 1.5;
    margin: 0;
}

/* RIGHT COLUMN: Send Us a Message (Interactive Form) */
.contact-form-wrapper {
    background: #ffffff;
    border: 1px solid var(--border-color);
    border-radius: var(--radius-lg);
    padding: 36px 32px;
    box-shadow: 0 10px 30px rgba(3, 24, 46, 0.08);
    position: relative;
    border-top: 4px solid var(--accent-gold);
}
.form-card-header {
    margin-bottom: 25px;
}
.form-header-badge {
    font-size: 11.5px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 1px;
    color: var(--accent-gold-dark);
    display: inline-block;
    margin-bottom: 6px;
}
.form-header-title {
    font-size: 26px;
    font-weight: 800;
    color: var(--primary-color);
    margin-bottom: 8px;
    letter-spacing: -0.3px;
}
.form-header-lead {
    font-size: 13.5px;
    color: var(--text-muted);
    line-height: 1.55;
    margin: 0;
}

/* Alerts */
.alert {
    display: flex;
    gap: 12px;
    align-items: flex-start;
    padding: 14px 18px;
    border-radius: var(--radius-sm);
    margin-bottom: 22px;
    font-size: 13.5px;
}
.alert-icon {
    font-size: 18px;
    font-weight: 800;
    line-height: 1;
}
.alert-content strong {
    display: block;
    margin-bottom: 2px;
}
.alert-content p {
    margin: 0;
}
.alert-success {
    background: #dcfce7;
    border: 1px solid #bbf7d0;
    color: #166534;
}
.alert-danger {
    background: #fee2e2;
    border: 1px solid #fecaca;
    color: #991b1b;
}

/* Form Controls */
.styled-contact-form {
    display: flex;
    flex-direction: column;
    gap: 18px;
}
.form-row-2 {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 16px;
}
.form-group {
    display: flex;
    flex-direction: column;
}
.form-label {
    font-size: 13px;
    font-weight: 700;
    color: var(--primary-color);
    margin-bottom: 6px;
}
.form-label .req {
    color: #ef4444;
}
.form-control {
    width: 100%;
    padding: 12px 14px;
    font-size: 13.5px;
    color: var(--text-main);
    background: #ffffff;
    border: 1.5px solid var(--border-color);
    border-radius: var(--radius-sm);
    font-family: inherit;
    transition: var(--transition-fast);
    box-sizing: border-box;
}
.form-control:focus {
    outline: none;
    border-color: var(--secondary-color);
    box-shadow: 0 0 0 3.5px rgba(2, 132, 199, 0.12);
}
.custom-select {
    cursor: pointer;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%23475569'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'%3E%3C/path%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 12px center;
    background-size: 16px;
    padding-right: 36px;
    appearance: none;
}
.form-footer-action {
    display: flex;
    flex-direction: column;
    gap: 10px;
    margin-top: 6px;
}
.submit-btn {
    width: 100%;
    padding: 14px 28px;
    font-size: 15px;
    font-weight: 700;
    border-radius: var(--radius-sm);
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    cursor: pointer;
    border: none;
    background: linear-gradient(135deg, #03182e 0%, #0369a1 100%);
    color: #ffffff;
    transition: var(--transition);
    box-shadow: 0 4px 14px rgba(3, 24, 46, 0.2);
}
.submit-btn:hover {
    background: linear-gradient(135deg, #020e1c 0%, #0284c7 100%);
    box-shadow: 0 8px 24px rgba(2, 132, 199, 0.3);
    transform: translateY(-2px);
}
.btn-arrow {
    font-size: 18px;
    transition: transform 0.2s ease;
}
.submit-btn:hover .btn-arrow {
    transform: translateX(4px);
}
.privacy-note {
    font-size: 11.5px;
    color: var(--text-muted);
    text-align: center;
}

/* SECTION: Leadership Team & Executive Directory */
.executive-leadership-section {
    margin-top: 80px;
    border-top: 1px solid var(--border-color);
    padding-top: 60px;
}
.leadership-section-header {
    text-align: center;
    max-width: 720px;
    margin: 0 auto 45px;
}
.leadership-section-header .badge-tag {
    font-size: 12px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 1.2px;
    color: var(--accent-gold-dark);
    background: rgba(212, 175, 55, 0.12);
    border: 1px solid rgba(212, 175, 55, 0.3);
    padding: 4px 14px;
    border-radius: var(--radius-full);
    display: inline-block;
    margin-bottom: 12px;
}
.leadership-section-header .section-title {
    font-size: 32px;
    font-weight: 800;
    color: var(--primary-color);
    margin-bottom: 12px;
    letter-spacing: -0.4px;
}
.leadership-section-header .section-lead {
    font-size: 15px;
    color: var(--text-muted);
    line-height: 1.6;
}

/* Executive Cards Grid */
.executive-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 25px;
}
.executive-card {
    background: #ffffff;
    border: 1px solid var(--border-color);
    border-radius: var(--radius-lg);
    padding: 24px;
    box-shadow: var(--shadow-sm);
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    transition: var(--transition);
    position: relative;
    overflow: hidden;
}
.executive-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 3px;
    background: linear-gradient(90deg, var(--accent-gold) 0%, var(--secondary-color) 100%);
    opacity: 0;
    transition: var(--transition-fast);
}
.executive-card:hover {
    border-color: rgba(212, 175, 55, 0.4);
    box-shadow: 0 12px 30px rgba(3, 24, 46, 0.1);
    transform: translateY(-4px);
}
.executive-card:hover::before {
    opacity: 1;
}

.exec-card-top {
    display: flex;
    gap: 16px;
    align-items: flex-start;
    margin-bottom: 16px;
}
.exec-avatar-wrapper {
    flex-shrink: 0;
}
.exec-avatar-img {
    width: 54px;
    height: 54px;
    border-radius: 50%;
    object-fit: cover;
    border: 2px solid var(--accent-gold);
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}
.exec-monogram-avatar {
    width: 54px;
    height: 54px;
    border-radius: 50%;
    background: var(--bg-navy-gradient);
    color: var(--accent-gold);
    border: 2px solid var(--accent-gold);
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 800;
    font-size: 17px;
    letter-spacing: 0.5px;
    box-shadow: 0 2px 8px rgba(3, 24, 46, 0.15);
}
.exec-main-meta {
    flex-grow: 1;
    min-width: 0;
}
.exec-name {
    font-size: 16px;
    font-weight: 800;
    color: var(--primary-color);
    margin: 0 0 4px;
    line-height: 1.3;
}
.exec-role-pill {
    font-size: 12px;
    font-weight: 600;
    color: var(--secondary-hover);
    line-height: 1.35;
}

.exec-card-details {
    display: flex;
    flex-direction: column;
    gap: 8px;
    border-top: 1px solid #f1f5f9;
    padding-top: 14px;
}
.exec-detail-item {
    display: flex;
    align-items: flex-start;
    gap: 8px;
    font-size: 12.5px;
}
.detail-icon {
    font-size: 13px;
    flex-shrink: 0;
    margin-top: 1px;
}
.detail-text {
    color: #475569;
}
.detail-link {
    color: var(--secondary-color);
    font-weight: 600;
    text-decoration: none;
    word-break: break-all;
    transition: var(--transition-fast);
}
.detail-link:hover {
    color: var(--secondary-hover);
    text-decoration: underline;
}
.detail-phone {
    color: #334155;
    font-weight: 600;
}
.exec-bio-excerpt {
    font-size: 12px;
    color: #64748b;
    line-height: 1.45;
    margin: 6px 0 0;
    padding-top: 8px;
    border-top: 1px dashed #e2e8f0;
}

/* Institutional Compliance Card */
.compliance-protocol-card {
    margin-top: 50px;
    background: linear-gradient(135deg, #03182e 0%, #062c54 100%);
    border: 1px solid rgba(212, 175, 55, 0.3);
    border-radius: var(--radius-md);
    padding: 24px 28px;
    color: #ffffff;
    box-shadow: var(--shadow-md);
}
.protocol-content {
    display: flex;
    gap: 20px;
    align-items: center;
}
.protocol-icon-box {
    width: 48px;
    height: 48px;
    border-radius: var(--radius-sm);
    background: rgba(212, 175, 55, 0.15);
    border: 1px solid rgba(212, 175, 55, 0.4);
    color: var(--accent-gold);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
    flex-shrink: 0;
}
.protocol-text h4 {
    font-size: 15px;
    font-weight: 700;
    color: var(--accent-gold-light);
    margin: 0 0 6px;
}
.protocol-text p {
    font-size: 13px;
    color: #cbd5e1;
    margin: 0;
    line-height: 1.55;
}
.protocol-text strong {
    color: #ffffff;
}

/* Responsive Breakpoints */
@media (max-width: 1024px) {
    .quick-bar-grid {
        grid-template-columns: repeat(2, 1fr);
    }
    .contact-main-grid {
        grid-template-columns: 1fr;
        gap: 40px;
    }
    .executive-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (max-width: 768px) {
    .page-banner-title {
        font-size: 32px;
    }
    .quick-bar-grid {
        grid-template-columns: 1fr;
    }
    .form-row-2 {
        grid-template-columns: 1fr;
    }
    .executive-grid {
        grid-template-columns: 1fr;
    }
    .contact-form-wrapper {
        padding: 26px 20px;
    }
    .protocol-content {
        flex-direction: column;
        align-items: flex-start;
        gap: 12px;
    }
}
</style>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
