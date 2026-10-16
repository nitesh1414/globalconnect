<?php
/**
 * Global Connect - Contact Us Page
 */
$pageTitle = 'Contact Us | Global Connect';
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
?>

<!-- Page Banner -->
<div class="page-banner">
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
            <div>
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

                    <button type="submit" class="btn-primary" style="padding: 14px 36px; font-size: 15px;">
                        Submit Official Enquiry →
                    </button>
                </form>
            </div>

            <!-- Right: Office Directory -->
            <div>
                <!-- Singapore HQ -->
                <div class="contact-card">
                    <h4>🇸🇬 Singapore Global Headquarters</h4>
                    <p style="color: var(--text-muted); font-size: 14px; margin-bottom: 12px;">
                        <strong>GLOBAL CONNECT</strong><br>
                        101 Kitchener Road #03-43, Jalan Besar Plaza, Singapore (208511)
                    </p>
                    <p style="font-size: 14px; margin-bottom: 6px;">
                        <strong>Office Direct:</strong> <a href="tel:+6589451100" style="color: var(--secondary-color); font-weight: 600;">(65) 89451100</a>
                    </p>
                    <p style="font-size: 14px;">
                        <strong>Corporate Email:</strong> <a href="mailto:admin@globalconnectsg.com" style="color: var(--secondary-color); font-weight: 600;">admin@globalconnectsg.com</a>
                    </p>
                </div>

                <!-- India Corporate Office -->
                <div class="contact-card">
                    <h4>🇮🇳 India Corporate Office</h4>
                    <p style="color: var(--text-muted); font-size: 14px; margin-bottom: 12px;">
                        No. 211 Rajnigandha Block, Garden Apartment, 21 Vitthal Mallya Road (opp. UB City), Bangalore 560001, India
                    </p>
                    <p style="font-size: 14px; margin-bottom: 6px;">
                        <strong>Global Roaming (24/7):</strong> <a href="tel:+919910002299" style="color: var(--secondary-color); font-weight: 600;">(91) 99100 02299</a>
                    </p>
                    <p style="font-size: 14px;">
                        <strong>Executive Email:</strong> <a href="mailto:shammy@globalconnectsg.com" style="color: var(--secondary-color); font-weight: 600;">shammy@globalconnectsg.com</a>
                    </p>
                </div>

                <!-- International Branch Desks -->
                <div class="contact-card">
                    <h4>🌏 International Branch Network</h4>
                    <p style="color: var(--text-muted); font-size: 13px; line-height: 1.6; margin-bottom: 8px;">
                        <strong>Indonesia:</strong> Grand Hyatt Plaza, Jakarta Pusat, Indonesia<br>
                        <strong>Hong Kong:</strong> Hyatt Regency Tsim Sha Tsui, Kowloon, Hong Kong<br>
                        <strong>India Branches:</strong> New Delhi, Mumbai, Hyderabad, and Chennai (By Appointment - Hyatt Hotels)
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
@media (max-width: 900px) {
    .contact-grid {
        grid-template-columns: 1fr !important;
    }
}
</style>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
