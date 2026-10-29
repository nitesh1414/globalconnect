<?php
/**
 * Global Connect - Contact Us Page (100% Dynamic from Admin Settings)
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
$addrSg = get_setting('address_sg', '101 Kitchener Road #03-43, Jalan Besar Plaza, Singapore (208511)');
$addrIndia = get_setting('address_india', 'No. 211 Rajnigandha Block, Garden Apartment, 21 Vitthal Mallya Road (opp. UB City), Bangalore 560001, India');
$addrBranches = get_setting('address_branches', 'New Delhi, Mumbai, Hyderabad, and Chennai (By Appointment - Hyatt Hotels)');
$addrIndonesia = get_setting('address_indonesia', 'HOTEL GRAND HYATT PLAZA, JL. M.H. THAMRIN KAV. 28-30, GONDANGDIA, MENTENG, JAKARTA, 10350, INDONESIA');
$addrHongkong = get_setting('address_hongkong', 'HOTEL HYATT REGENCY 18 HANOI ROAD TSIM SHA TSU KOWLOON HONGKONG');
$phoneSg = get_setting('header_phone_sg', '(65) 89451100');
$phoneRoam = get_setting('header_phone_roam', '(91) 99100 02299');
$emailPrimary = get_setting('contact_email_primary', 'admin@globalconnectsg.com');
$emailSecondary = get_setting('contact_email_secondary', 'shammy@globalconnectsg.com');
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
        <div style="display: grid; grid-template-columns: 1.2fr 1fr; gap: 40px; margin-bottom: 60px;" class="contact-grid">
            <!-- Left: Contact Form -->
            <div class="reveal-left">
                <span class="section-subtitle">Send an Enquiry</span>
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
                                <option value="Trading Services" <?= ($_POST['subject'] ?? '') === 'Trading Services' ? 'selected' : '' ?>>Commodity Trading &amp; Sourcing</option>
                                <option value="Trade Finance / LC" <?= ($_POST['subject'] ?? '') === 'Trade Finance / LC' ? 'selected' : '' ?>>Trade Finance / Letter of Credit (LC)</option>
                                <option value="Project Financing" <?= ($_POST['subject'] ?? '') === 'Project Financing' ? 'selected' : '' ?>>Project Finance &amp; Capital Raising</option>
                                <option value="Corporate Consulting" <?= ($_POST['subject'] ?? '') === 'Corporate Consulting' ? 'selected' : '' ?>>Corporate Consulting / M&amp;A</option>
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
                    <h4>🇸🇬 Singapore Global Headquarters</h4>
                    <p style="color: var(--text-muted); font-size: 14px; margin-bottom: 12px;">
                        <strong><?= e(get_setting('company_name', 'GLOBAL CONNECT')) ?></strong><br>
                        <?= nl2br(e($addrSg)) ?>
                    </p>
                    <p style="font-size: 14px; margin-bottom: 6px;">
                        <strong>Office Direct:</strong> <a href="tel:<?= preg_replace('/[^0-9+]/', '', $phoneSg) ?>" style="color: var(--secondary-color); font-weight: 600;"><?= e($phoneSg) ?></a>
                    </p>
                    <p style="font-size: 14px;">
                        <strong>Corporate Email:</strong> <a href="mailto:<?= e($emailPrimary) ?>" style="color: var(--secondary-color); font-weight: 600;"><?= e($emailPrimary) ?></a>
                    </p>
                </div>

                <!-- India Corporate Office -->
                <div class="contact-card hover-lift" style="margin-top: 20px;">
                    <h4>🇮🇳 India Corporate Office</h4>
                    <p style="color: var(--text-muted); font-size: 14px; margin-bottom: 12px;">
                        <?= nl2br(e($addrIndia)) ?>
                    </p>
                    <p style="font-size: 14px; margin-bottom: 6px;">
                        <strong>Global Roaming (24/7):</strong> <a href="tel:<?= preg_replace('/[^0-9+]/', '', $phoneRoam) ?>" style="color: var(--secondary-color); font-weight: 600;"><?= e($phoneRoam) ?></a>
                    </p>
                    <?php if (!empty($emailSecondary)): ?>
                        <p style="font-size: 14px;">
                            <strong>Executive Email:</strong> <a href="mailto:<?= e($emailSecondary) ?>" style="color: var(--secondary-color); font-weight: 600;"><?= e($emailSecondary) ?></a>
                        </p>
                    <?php endif; ?>
                </div>

                <!-- International Branch Desks -->
                <div class="contact-card hover-lift" style="margin-top: 20px;">
                    <h4>🌏 International Branch Network</h4>
                    <p style="color: var(--text-muted); font-size: 13px; line-height: 1.6; margin-bottom: 8px;">
                        <strong>Indonesia:</strong> <?= e($addrIndonesia) ?><br>
                        <strong>Hong Kong:</strong> <?= e($addrHongkong) ?><br>
                        <strong>India Branches:</strong> <?= e($addrBranches) ?>
                    </p>
                </div>
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
    font-size: 16px;
    margin-bottom: 10px;
    border-bottom: 1px solid var(--border-color);
    padding-bottom: 6px;
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
@media (max-width: 900px) {
    .contact-grid {
        grid-template-columns: 1fr !important;
    }
}
</style>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
