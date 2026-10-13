<?php
/**
 * Global Connect - Contact Us Page
 * Form submission saves inquiries to database
 */
require_once __DIR__ . '/includes/functions.php';

$successMsg = '';
$errorMsg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['csrf_token'] ?? '')) {
        $errorMsg = 'Security validation failed. Please try submitting again.';
    } else {
        $name = clean($_POST['name'] ?? '');
        $email = clean($_POST['email'] ?? '');
        $phone = clean($_POST['phone'] ?? '');
        $subject = clean($_POST['subject'] ?? '');
        $message = clean($_POST['message'] ?? '');

        if (empty($name) || empty($email) || empty($message)) {
            $errorMsg = 'Please complete all required fields (Name, Email, Message).';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errorMsg = 'Please enter a valid email address.';
        } else {
            try {
                $db = get_db();
                $stmt = $db->prepare("INSERT INTO contacts (name, email, phone, subject, message, status, ip_address, created_at) VALUES (?, ?, ?, ?, ?, 'unread', ?, CURRENT_TIMESTAMP)");
                $stmt->execute([$name, $email, $phone, $subject, $message, $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1']);
                
                $successMsg = 'Thank you for reaching out! Your message has been sent successfully. Our team will contact you shortly.';
            } catch (Exception $e) {
                $errorMsg = 'Unable to send message at this time. Please try calling or emailing us directly.';
            }
        }
    }
}

$pageTitle = 'Contact | Global Connect';
$pageMetaDesc = 'Contact Global Connect Singapore Global Headquarters, India Headquarters in Bangalore, Indonesia, and Hong Kong offices.';

require_once __DIR__ . '/includes/header.php';
?>

<!-- Page Banner -->
<div class="page-banner">
    <div class="container">
        <h1 class="page-banner-title">Contact Us</h1>
        <p class="page-banner-subtitle">Get in touch with our global offices and executive team</p>
        <div class="breadcrumb-nav">
            <a href="/index.php">Home</a> <span>/</span> <span>Contact</span>
        </div>
    </div>
</div>

<div class="page-content-wrapper">
    <div class="container">
        <?php if ($successMsg): ?>
            <div class="alert alert-success">
                <span>✓ <?= e($successMsg) ?></span>
            </div>
        <?php endif; ?>

        <?php if ($errorMsg): ?>
            <div class="alert alert-danger">
                <span>⚠ <?= e($errorMsg) ?></span>
            </div>
        <?php endif; ?>

        <div class="contact-grid">
            <!-- Left: Contact Form -->
            <div>
                <div class="contact-card" style="padding: 35px;">
                    <h4>Send Us Message</h4>
                    <p style="color: var(--text-muted); font-size: 14.5px; margin-bottom: 25px;">
                        Thank you for learning more about our company. We care about your questions and feedback. Fill out the form below and an executive will respond promptly.
                    </p>

                    <form action="/contact.php" method="POST">
                        <?= csrf_field() ?>

                        <div class="form-group">
                            <label class="form-label" for="name">Your Name *</label>
                            <input type="text" id="name" name="name" class="form-control" required placeholder="Full Name" value="<?= e($_POST['name'] ?? '') ?>">
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="email">Your Email *</label>
                            <input type="email" id="email" name="email" class="form-control" required placeholder="email@company.com" value="<?= e($_POST['email'] ?? '') ?>">
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="phone">Phone / Mobile</label>
                            <input type="tel" id="phone" name="phone" class="form-control" placeholder="+65 ... or +91 ..." value="<?= e($_POST['phone'] ?? '') ?>">
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="subject">Subject / Area of Interest</label>
                            <input type="text" id="subject" name="subject" class="form-control" placeholder="e.g. Gold Bullion Trading, SBLC Trade Finance, Advisory" value="<?= e($_POST['subject'] ?? '') ?>">
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="message">Your Message *</label>
                            <textarea id="message" name="message" class="form-control" rows="5" required placeholder="Please provide details about your trade or finance requirement..."><?= e($_POST['message'] ?? '') ?></textarea>
                        </div>

                        <button type="submit" class="btn-primary" style="width: 100%; padding: 14px;">
                            Send Message →
                        </button>
                    </form>
                </div>

                <!-- Executive Contacts Directory -->
                <div class="contact-card">
                    <h4>Executive Contacts</h4>
                    <ul class="exec-list">
                        <li class="exec-item">
                            <div class="exec-name">Mr Shammy Songra</div>
                            <div class="exec-role">Founder / Director</div>
                            <a href="mailto:shammy@globalconnectsg.com" class="exec-email">shammy@globalconnectsg.com</a>
                        </li>
                        <li class="exec-item">
                            <div class="exec-name">Mr Cliff Poh</div>
                            <div class="exec-role">Director – Singapore</div>
                            <a href="mailto:cliffpoh@globalconnectsg.com" class="exec-email">cliffpoh@globalconnectsg.com</a>
                        </li>
                        <li class="exec-item">
                            <div class="exec-name">Mr. Lima Hutama Halim</div>
                            <div class="exec-role">Director – Indonesia Business</div>
                            <a href="mailto:limahalim@globalconnectsg.com" class="exec-email">limahalim@globalconnectsg.com</a>
                        </li>
                        <li class="exec-item">
                            <div class="exec-name">Mr. Kapil Gulati</div>
                            <div class="exec-role">Director – International Business &amp; Financial Advisory</div>
                            <a href="mailto:kapil@globalconnectsg.com" class="exec-email">kapil@globalconnectsg.com</a>
                        </li>
                        <li class="exec-item">
                            <div class="exec-name">Mr Sandeep Pratap Sanap</div>
                            <div class="exec-role">Associate Director &amp; Partner – India &amp; International Business</div>
                            <a href="mailto:sandeep@globalconnectsg.com" class="exec-email">sandeep@globalconnectsg.com</a>
                        </li>
                        <li class="exec-item">
                            <div class="exec-name">Mr. Siddharth Maheshwari</div>
                            <div class="exec-role">Director – Business Development</div>
                            <a href="mailto:siddharth@globalconnectsg.com" class="exec-email">siddharth@globalconnectsg.com</a>
                        </li>
                        <li class="exec-item">
                            <div class="exec-name">Mr. Amit Gupta</div>
                            <div class="exec-role">Director – Business Development</div>
                            <a href="mailto:amit@globalconnectsg.com" class="exec-email">amit@globalconnectsg.com</a>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Right: Office Locations & Phones -->
            <div>
                <!-- Singapore Global HQ -->
                <div class="contact-card">
                    <h4><span>🏢</span> GLOBAL HEADQUATER</h4>
                    <div class="contact-card-sub">
                        <h5>Singapore</h5>
                        <p>101 KITCHENER ROAD #03-43 JALAN BESAR PLAZA SINGAPORE (208511)</p>
                    </div>
                </div>

                <!-- India HQ -->
                <div class="contact-card">
                    <h4><span>🇮🇳</span> INDIA HEADQUATER</h4>
                    <div class="contact-card-sub">
                        <h5>Bangalore</h5>
                        <p>NO. 211 RAJNIGANDHA BLOCK, GARDEN APARTMENT, 21 VITTHAL MALLYA ROAD (opp. UB CITY) BANGLORE 560001 INDIA</p>
                    </div>
                    <div class="contact-card-sub">
                        <h5>New Delhi, Mumbai, Hyderabad and Chennai</h5>
                        <p>By Appointment ONLY – IN HYATT GROUP OF HOTELS</p>
                    </div>
                </div>

                <!-- Regional Presences -->
                <div class="contact-card">
                    <h4><span>🌏</span> REGIONAL PRESENCE</h4>
                    <div class="contact-card-sub">
                        <h5>Indonesia (Jakarta)</h5>
                        <p>HOTEL GRAND HYATT PLAZA, JL. M.H. THAMRIN KAV. 28-30, GONDANGDIA, MENTENG, JAKARTA, 10350, INDONESIA</p>
                    </div>
                    <div class="contact-card-sub">
                        <h5>Hong Kong</h5>
                        <p>HOTEL HYATT REGENCY 18 HANOI ROAD TSIM SHA TSU KOWLOON HONGKONG</p>
                    </div>
                </div>

                <!-- Direct Communication Channels -->
                <div class="contact-card">
                    <h4><span>📞</span> Call Us</h4>
                    <div class="contact-card-sub">
                        <p>Singapore Mob. No: <strong><a href="tel:+6589451100">(65) 89451100</a></strong></p>
                        <p>Global Roam. No: <strong><a href="tel:+919910002299">(91) 99100 02299</a></strong></p>
                    </div>

                    <h4 style="margin-top: 20px;"><span>✉</span> Mail Us</h4>
                    <div class="contact-card-sub">
                        <p><a href="mailto:admin@globalconnectsg.com">admin@globalconnectsg.com</a></p>
                        <p><a href="mailto:shammy@globalconnectsg.com">shammy@globalconnectsg.com</a></p>
                        <p><a href="mailto:tsnshammy@gmail.com">tsnshammy@gmail.com</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
