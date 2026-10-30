<?php
/**
 * Global Connect - Contact Us Page (100% Dynamic from Admin Settings)
 * Pre-populated with authentic data from https://site.globalconnectsg.com/mysite/contact/
 */
require_once __DIR__ . '/includes/functions.php';

$pageTitle = 'Contact Us | ' . get_setting('company_name', 'Global Connect');
$pageMetaDesc = 'Contact Global Connect Singapore Global Headquarters, Bangalore India HQ, Indonesia & Hong Kong offices.';

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
                $successMessage = 'Thank you! Your message has been sent successfully. Our trade desk will respond promptly.';
                $_POST = [];
            } catch (Exception $e) {
                $errorMessage = 'We encountered an error saving your message. Please reach us via direct email or telephone.';
            }
        }
    }
}

// Dynamic Contact Settings
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
        <h1 class="page-banner-title">Contact Us</h1>
        <p class="page-banner-subtitle">Get in Touch with our Singapore Global HQ &amp; Global Branch Desks</p>
        <div class="breadcrumb-nav">
            <a href="<?= url('index.php') ?>">Home</a> <span>/</span> <span>Contact</span>
        </div>
    </div>
</div>

<div class="page-content-wrapper">
    <div class="container">
        <!-- Top Contact & Form Grid -->
        <div style="display: grid; grid-template-columns: 1.2fr 1fr; gap: 40px; margin-bottom: 70px;" class="contact-grid">
            <!-- Left: Contact Form -->
            <div class="reveal-left">
                <span class="section-subtitle">Send Us a Message</span>
                <h2 class="section-title" style="margin-bottom: 25px;">How Can We Assist Your Business?</h2>

                <?php if ($successMessage): ?>
                    <div style="background: #dcfce7; border: 1px solid #bbf7d0; color: #166534; padding: 16px 20px; border-radius: var(--radius-sm); margin-bottom: 25px;">
                        <strong>✓ Success:</strong> <?= e($successMessage) ?>
                    </div>
                <?php endif; ?>

                <?php if ($errorMessage): ?>
                    <div style="background: #fee2e2; border: 1px solid #fecaca; color: #991b1b; padding: 16px 20px; border-radius: var(--radius-sm); margin-bottom: 25px;">
                        <strong>⚠ Error:</strong> <?= e($errorMessage) ?>
                    </div>
                <?php endif; ?>

                <form action="<?= url('contact.php') ?>" method="POST" class="contact-form">
                    <?= csrf_field() ?>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                        <div class="form-group">
                            <label class="form-label" for="name">Your Full Name <span style="color: red;">*</span></label>
                            <input type="text" id="name" name="name" class="form-control" required placeholder="e.g. Johnathan Tan" value="<?= e($_POST['name'] ?? '') ?>">
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="email">Corporate Email Address <span style="color: red;">*</span></label>
                            <input type="email" id="email" name="email" class="form-control" required placeholder="e.g. jtan@company.com" value="<?= e($_POST['email'] ?? '') ?>">
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                        <div class="form-group">
                            <label class="form-label" for="phone">Phone / WhatsApp Number</label>
                            <input type="text" id="phone" name="phone" class="form-control" placeholder="e.g. +65 8945 1100" value="<?= e($_POST['phone'] ?? '') ?>">
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="subject">Area of Inquiry</label>
                            <select id="subject" name="subject" class="form-control">
                                <option value="Gold Bullion & Precious Metals" <?= ($_POST['subject'] ?? '') === 'Gold Bullion & Precious Metals' ? 'selected' : '' ?>>Gold Bullion &amp; Precious Metals</option>
                                <option value="Sugar & Soft Commodities" <?= ($_POST['subject'] ?? '') === 'Sugar & Soft Commodities' ? 'selected' : '' ?>>Sugar (ICUMSA 45) &amp; Soft Commodities</option>
                                <option value="Agro Products & Rice" <?= ($_POST['subject'] ?? '') === 'Agro Products & Rice' ? 'selected' : '' ?>>Agro Products &amp; Grains</option>
                                <option value="Minerals, Metals & Ores" <?= ($_POST['subject'] ?? '') === 'Minerals, Metals & Ores' ? 'selected' : '' ?>>Minerals, Metals &amp; Ores</option>
                                <option value="Petroleum & Lubricants" <?= ($_POST['subject'] ?? '') === 'Petroleum & Lubricants' ? 'selected' : '' ?>>Petroleum &amp; Lubricants (Mazut/D2)</option>
                                <option value="Trade Finance / LC / SBLC" <?= ($_POST['subject'] ?? '') === 'Trade Finance / LC / SBLC' ? 'selected' : '' ?>>Trade Finance (LC / SBLC / BG)</option>
                                <option value="Banking Support & Wire Transfers" <?= ($_POST['subject'] ?? '') === 'Banking Support & Wire Transfers' ? 'selected' : '' ?>>Banking Support &amp; Wire Transfers</option>
                                <option value="Corporate Consulting & M&A" <?= ($_POST['subject'] ?? '') === 'Corporate Consulting & M&A' ? 'selected' : '' ?>>Corporate Consulting &amp; M&amp;A</option>
                                <option value="General Enquiry" <?= ($_POST['subject'] ?? '') === 'General Enquiry' ? 'selected' : '' ?>>General Enquiry</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="message">Detailed Message / Specifications <span style="color: red;">*</span></label>
                        <textarea id="message" name="message" class="form-control" rows="5" required placeholder="Please outline product specifications, quantities, target delivery port, or financing requirements..."><?= e($_POST['message'] ?? '') ?></textarea>
                    </div>

                    <button type="submit" class="btn-primary shimmer-btn" style="padding: 14px 36px; font-size: 15px;">
                        Submit Official Enquiry →
                    </button>
                </form>
            </div>

            <!-- Right: Office Directory (Dynamic from Admin Settings) -->
            <div class="reveal-right">
                <!-- Singapore HQ -->
                <div class="contact-card hover-lift">
                    <h4>🇸🇬 GLOBAL HEADQUARTER (Singapore)</h4>
                    <p style="color: var(--text-muted); font-size: 14px; margin-bottom: 12px;">
                        <strong><?= e(get_setting('company_name', 'GLOBAL CONNECT')) ?></strong><br>
                        <?= nl2br(e($addrSg)) ?>
                    </p>
                    <p style="font-size: 14px; margin-bottom: 6px;">
                        <strong>Singapore Mob. No:</strong> <a href="tel:<?= preg_replace('/[^0-9+]/', '', $phoneSg) ?>" style="color: var(--secondary-color); font-weight: 600;"><?= e($phoneSg) ?></a>
                    </p>
                    <p style="font-size: 14px; margin-bottom: 6px;">
                        <strong>Corporate Email:</strong> <a href="mailto:<?= e($emailPrimary) ?>" style="color: var(--secondary-color); font-weight: 600;"><?= e($emailPrimary) ?></a>
                    </p>
                    <p style="font-size: 14px;">
                        <strong>Fax:</strong> <span style="color: var(--text-muted);"><?= e($faxNumber) ?></span>
                    </p>
                </div>

                <!-- India Corporate Office -->
                <div class="contact-card hover-lift" style="margin-top: 20px;">
                    <h4>🇮🇳 INDIA HEADQUATER (Bangalore)</h4>
                    <p style="color: var(--text-muted); font-size: 14px; margin-bottom: 12px;">
                        <?= nl2br(e($addrIndia)) ?>
                    </p>
                    <p style="font-size: 14px; margin-bottom: 6px;">
                        <strong>Global Roam. No (24/7):</strong> <a href="tel:<?= preg_replace('/[^0-9+]/', '', $phoneRoam) ?>" style="color: var(--secondary-color); font-weight: 600;"><?= e($phoneRoam) ?></a>
                    </p>
                    <p style="font-size: 14px;">
                        <strong>Executive Mail:</strong> <a href="mailto:<?= e($emailSecondary) ?>" style="color: var(--secondary-color); font-weight: 600;"><?= e($emailSecondary) ?></a>
                    </p>
                </div>

                <!-- International Branch Desks -->
                <div class="contact-card hover-lift" style="margin-top: 20px;">
                    <h4>🌏 INDONESIA, HONG KONG &amp; BRANCHES</h4>
                    <p style="color: var(--text-muted); font-size: 13.5px; line-height: 1.6; margin-bottom: 8px;">
                        <strong>Indonesia (Jakarta):</strong> <?= e($addrIndonesia) ?><br>
                        <strong>Hong Kong:</strong> <?= e($addrHongkong) ?><br>
                        <strong>India Branches:</strong> <?= e($addrBranches) ?>
                    </p>
                </div>
            </div>
        </div>

        <!-- Executive Leadership Directory -->
        <div class="reveal-up" style="margin-top: 50px;">
            <div class="section-header" style="text-align: center; margin-bottom: 40px;">
                <span class="section-subtitle">Leadership Team</span>
                <h2 class="section-title">Key Executive Contacts &amp; Directors</h2>
                <p class="section-lead">
                    Connect directly with our board of directors and regional business development leaders.
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
                    <div class="executive-card hover-lift reveal-up" style="transition-delay: <?= ($idx * 0.06) ?>s;">
                        <?php if (!empty($exec['avatar'])): ?>
                            <img src="<?= e(image_url($exec['avatar'])) ?>" alt="<?= e($exec['name']) ?>" class="exec-avatar-img">
                        <?php else: ?>
                            <div class="exec-avatar">
                                <?= e(strtoupper($initials)) ?>
                            </div>
                        <?php endif; ?>
                        <div class="exec-info">
                            <h4 class="exec-name"><?= e($exec['name']) ?></h4>
                            <p class="exec-role"><?= e($exec['role']) ?></p>
                            <?php if (!empty($exec['location'])): ?>
                                <span class="exec-loc">📍 <?= e($exec['location']) ?></span>
                            <?php endif; ?>
                            <?php if (!empty($exec['phone'])): ?>
                                <a href="tel:<?= preg_replace('/[^0-9+]/', '', $exec['phone']) ?>" class="exec-phone">
                                    <span>📞</span> <?= e($exec['phone']) ?>
                                </a>
                            <?php endif; ?>
                            <a href="mailto:<?= e($exec['email']) ?>" class="exec-email">
                                <span>✉</span> <?= e($exec['email']) ?>
                            </a>
                            <?php if (!empty($exec['bio'])): ?>
                                <p class="exec-bio"><?= nl2br(e($exec['bio'])) ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<style>
.contact-card {
    background: #ffffff;
    border-radius: var(--radius-md);
    padding: 24px;
    border: 1px solid var(--border-color);
    box-shadow: var(--shadow-sm);
    transition: var(--transition);
}
.contact-card h4 {
    color: var(--primary-color);
    font-size: 15px;
    margin-bottom: 10px;
    border-bottom: 1.5px solid var(--border-color);
    padding-bottom: 6px;
    font-weight: 700;
}
.form-group {
    margin-bottom: 20px;
}
.form-label {
    display: block;
    font-size: 13.5px;
    font-weight: 600;
    color: var(--primary-color);
    margin-bottom: 6px;
}
.form-control {
    width: 100%;
    padding: 11px 15px;
    border: 1.5px solid var(--border-color);
    border-radius: var(--radius-sm);
    font-family: inherit;
    font-size: 14px;
    transition: var(--transition-fast);
    background: #ffffff;
}
.form-control:focus {
    outline: none;
    border-color: var(--secondary-color);
    box-shadow: 0 0 0 3px rgba(2, 132, 199, 0.15);
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

/* Executive Cards Grid */
.executive-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 25px;
}
.executive-card {
    background: #ffffff;
    border: 1px solid var(--border-color);
    border-radius: var(--radius-md);
    padding: 24px;
    display: flex;
    gap: 16px;
    align-items: flex-start;
    box-shadow: var(--shadow-sm);
}
.exec-avatar {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    background: var(--bg-navy-gradient);
    color: var(--accent-gold);
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 800;
    font-size: 16px;
    flex-shrink: 0;
    border: 1.5px solid var(--accent-gold);
}
.exec-avatar-img {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    object-fit: cover;
    flex-shrink: 0;
    border: 1.5px solid var(--accent-gold);
}
.exec-info {
    flex-grow: 1;
}
.exec-name {
    font-size: 16px;
    font-weight: 700;
    color: var(--primary-color);
    margin-bottom: 4px;
}
.exec-role {
    font-size: 13px;
    color: var(--text-muted);
    line-height: 1.4;
    margin-bottom: 6px;
}
.exec-loc {
    display: block;
    font-size: 12px;
    color: #64748b;
    margin-bottom: 6px;
}
.exec-phone {
    display: block;
    font-size: 12.5px;
    color: #475569;
    margin-bottom: 4px;
    text-decoration: none;
}
.exec-phone:hover {
    color: var(--secondary-color);
}
.exec-bio {
    font-size: 12.5px;
    color: #64748b;
    margin-top: 8px;
    line-height: 1.45;
    border-top: 1px dashed var(--border-color);
    padding-top: 6px;
}
.exec-email {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-size: 13px;
    font-weight: 600;
    color: var(--secondary-color);
    word-break: break-all;
}
.exec-email:hover {
    color: var(--secondary-hover);
    text-decoration: underline;
}

@media (max-width: 900px) {
    .contact-grid {
        grid-template-columns: 1fr !important;
    }
}
</style>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
