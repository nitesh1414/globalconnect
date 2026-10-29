<?php
/**
 * Global Connect - Comprehensive Site Settings Management
 */
require_once __DIR__ . '/includes/auth.php';

$db = get_db();
$errorMsg = '';
$successMsg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['csrf_token'] ?? '')) {
        $errorMsg = 'Security validation failed. Please submit the form again.';
    } else {
        // Handle Logo File Upload
        if (isset($_FILES['logo_file']) && $_FILES['logo_file']['error'] === UPLOAD_ERR_OK) {
            $uploadedLogo = upload_image($_FILES['logo_file']);
            if ($uploadedLogo) {
                set_setting('site_logo', $uploadedLogo);
            }
        } elseif (!empty($_POST['site_logo'])) {
            set_setting('site_logo', clean($_POST['site_logo']));
        }

        // Handle Favicon File Upload
        if (isset($_FILES['favicon_file']) && $_FILES['favicon_file']['error'] === UPLOAD_ERR_OK) {
            $uploadedFavicon = upload_image($_FILES['favicon_file']);
            if ($uploadedFavicon) {
                set_setting('site_favicon', $uploadedFavicon);
            }
        } elseif (!empty($_POST['site_favicon'])) {
            set_setting('site_favicon', clean($_POST['site_favicon']));
        }

        // Handle Experience Image Upload
        if (isset($_FILES['exp_image_file']) && $_FILES['exp_image_file']['error'] === UPLOAD_ERR_OK) {
            $uploadedExpImg = upload_image($_FILES['exp_image_file']);
            if ($uploadedExpImg) {
                set_setting('home_exp_image', $uploadedExpImg);
            }
        } elseif (!empty($_POST['home_exp_image'])) {
            set_setting('home_exp_image', clean($_POST['home_exp_image']));
        }

        // Array of text fields to save
        $textFields = [
            'site_title', 'company_name', 'site_tagline',
            'logo_title_p1', 'logo_title_p2', 'logo_tagline',
            'header_phone_sg', 'header_phone_roam', 'fax_number',
            'contact_email_primary', 'contact_email_secondary', 'contact_email_tertiary',
            'address_sg', 'address_india', 'address_branches', 'address_indonesia', 'address_hongkong',
            'stat_partners_num', 'stat_partners_label',
            'stat_countries_num', 'stat_countries_label',
            'stat_banks_num', 'stat_banks_label',
            'stat_experience_num', 'stat_experience_label',
            'home_exp_subtitle', 'home_exp_title', 'home_exp_lead', 'home_exp_body',
            'home_services_subtitle', 'home_services_title', 'home_services_lead',
            'home_singapore_subtitle', 'home_singapore_title', 'home_singapore_lead',
            'home_cta_title', 'home_cta_lead', 'home_cta_btn1_text', 'home_cta_btn1_link', 'home_cta_btn2_text', 'home_cta_btn2_link',
            'footer_about', 'footer_copyright'
        ];

        foreach ($textFields as $f) {
            if (isset($_POST[$f])) {
                set_setting($f, clean($_POST[$f]));
            }
        }

        set_flash('success', 'Website settings and branding updated successfully!');
        header('Location: ' . admin_url('settings.php'));
        exit;
    }
}

$adminPageTitle = 'Website Settings & Branding | Global Connect CMS';
$adminPageHeading = 'Site Settings & Dynamic Content Manager';
require_once __DIR__ . '/includes/header.php';
?>

<div style="margin-bottom: 25px;">
    <p style="color: var(--admin-text-muted); font-size: 14px; margin: 0;">
        Manage all global website content, brand identity, uploaded logos, typography, contact details, statistics counters, and homepage copy.
    </p>
</div>

<?php if ($errorMsg): ?>
    <div style="background: #fee2e2; color: #b91c1c; padding: 12px 18px; border-radius: 8px; margin-bottom: 20px;">
        ⚠ <?= e($errorMsg) ?>
    </div>
<?php endif; ?>

<!-- Settings Navigation Tabs -->
<div class="settings-tabs" style="display: flex; gap: 8px; margin-bottom: 25px; border-bottom: 2px solid var(--admin-border); padding-bottom: 12px; flex-wrap: wrap;">
    <button type="button" class="tab-btn active" onclick="switchTab('tab-branding', this)">🌟 Branding &amp; Logo</button>
    <button type="button" class="tab-btn" onclick="switchTab('tab-contacts', this)">📞 Contact &amp; Directory</button>
    <button type="button" class="tab-btn" onclick="switchTab('tab-locations', this)">🏢 Office Locations</button>
    <button type="button" class="tab-btn" onclick="switchTab('tab-stats', this)">📊 Statistics Counters</button>
    <button type="button" class="tab-btn" onclick="switchTab('tab-homepage', this)">📑 Homepage Content</button>
    <button type="button" class="tab-btn" onclick="switchTab('tab-footer', this)">📜 Footer &amp; Legal</button>
</div>

<form action="<?= admin_url('settings.php') ?>" method="POST" enctype="multipart/form-data">
    <?= csrf_field() ?>

    <!-- TAB 1: Branding & Logo -->
    <div id="tab-branding" class="settings-tab-pane active">
        <div class="admin-card">
            <div class="admin-card-header">
                <h3 class="admin-card-title">Brand Identity, Logo &amp; Typography</h3>
            </div>
            <div class="admin-card-body">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 25px;">
                    <!-- Logo Upload Section -->
                    <div>
                        <div class="admin-form-group">
                            <label class="admin-form-label" for="logo_file">Upload New Website Logo Image</label>
                            <input type="file" id="logo_file" name="logo_file" class="admin-form-input" accept="image/*" onchange="previewImg(this, 'logo_preview')">
                            <small style="color: var(--admin-text-muted); font-size: 12px; margin-top: 4px; display: block;">
                                Recommended: Circular 3D Emblem or transparent PNG/SVG (Max 8MB).
                            </small>
                        </div>

                        <div class="admin-form-group">
                            <label class="admin-form-label" for="site_logo">Or Logo Image Filename / Path</label>
                            <input type="text" id="site_logo" name="site_logo" class="admin-form-input" value="<?= e(get_setting('site_logo', 'logo-design-gcv.png')) ?>">
                        </div>

                        <div style="background: #020b14; border: 1px solid var(--admin-border); border-radius: 8px; padding: 15px; text-align: center; margin-top: 15px;">
                            <span style="display: block; font-size: 11px; color: #94a3b8; text-transform: uppercase; margin-bottom: 8px; letter-spacing: 1px;">Current Live Logo Preview</span>
                            <img id="logo_preview" src="<?= image_url(get_setting('site_logo', 'logo-design-gcv.png')) ?>" alt="Site Logo" style="height: 60px; max-width: 100%; object-fit: contain;">
                        </div>
                    </div>

                    <!-- Favicon & Text Brand -->
                    <div>
                        <div class="admin-form-group">
                            <label class="admin-form-label" for="favicon_file">Upload Favicon Image (Optional)</label>
                            <input type="file" id="favicon_file" name="favicon_file" class="admin-form-input" accept="image/*" onchange="previewImg(this, 'favicon_preview')">
                            <small style="color: var(--admin-text-muted); font-size: 12px; margin-top: 4px; display: block;">
                                Displays in browser tab (PNG or ICO).
                            </small>
                        </div>

                        <div class="admin-form-group">
                            <label class="admin-form-label" for="site_favicon">Favicon Filename / Path</label>
                            <input type="text" id="site_favicon" name="site_favicon" class="admin-form-input" value="<?= e(get_setting('site_favicon', 'logo-design-gcv.png')) ?>">
                        </div>

                        <div style="background: #020b14; border: 1px solid var(--admin-border); border-radius: 8px; padding: 15px; text-align: center; margin-top: 15px;">
                            <span style="display: block; font-size: 11px; color: #94a3b8; text-transform: uppercase; margin-bottom: 8px; letter-spacing: 1px;">Favicon Preview</span>
                            <img id="favicon_preview" src="<?= image_url(get_setting('site_favicon', 'logo-design-gcv.png')) ?>" alt="Favicon" style="height: 32px; width: 32px; object-fit: contain;">
                        </div>
                    </div>
                </div>

                <hr style="border: 0; border-top: 1px solid var(--admin-border); margin: 25px 0;">

                <h4 style="font-size: 15px; color: var(--admin-primary); margin-bottom: 15px;">Logo Brand Text &amp; Tagline</h4>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                    <div class="admin-form-group">
                        <label class="admin-form-label" for="logo_title_p1">Logo Title (Part 1 - White/Dark)</label>
                        <input type="text" id="logo_title_p1" name="logo_title_p1" class="admin-form-input" value="<?= e(get_setting('logo_title_p1', 'GLOBAL')) ?>" placeholder="GLOBAL">
                    </div>

                    <div class="admin-form-group">
                        <label class="admin-form-label" for="logo_title_p2">Logo Title (Part 2 - Highlight Gold/Cyan)</label>
                        <input type="text" id="logo_title_p2" name="logo_title_p2" class="admin-form-input" value="<?= e(get_setting('logo_title_p2', 'CONNECT')) ?>" placeholder="CONNECT">
                    </div>
                </div>

                <div class="admin-form-group">
                    <label class="admin-form-label" for="logo_tagline">Logo Tagline Subtitle</label>
                    <input type="text" id="logo_tagline" name="logo_tagline" class="admin-form-input" value="<?= e(get_setting('logo_tagline', 'Ventures • Singapore • India')) ?>" placeholder="Ventures • Singapore • India">
                    <small style="color: var(--admin-text-muted); font-size: 12px;">Appears directly beneath the logo title in the header and footer.</small>
                </div>

                <hr style="border: 0; border-top: 1px solid var(--admin-border); margin: 25px 0;">

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                    <div class="admin-form-group">
                        <label class="admin-form-label" for="site_title">Website Browser Title (SEO)</label>
                        <input type="text" id="site_title" name="site_title" class="admin-form-input" value="<?= e(get_setting('site_title', 'Global Connect | Global Business Enterprise Headquartered in Singapore & India')) ?>">
                    </div>

                    <div class="admin-form-group">
                        <label class="admin-form-label" for="company_name">Company Legal Name</label>
                        <input type="text" id="company_name" name="company_name" class="admin-form-input" value="<?= e(get_setting('company_name', 'Global Connect')) ?>">
                    </div>
                </div>

                <div class="admin-form-group">
                    <label class="admin-form-label" for="site_tagline">Site Meta Tagline</label>
                    <input type="text" id="site_tagline" name="site_tagline" class="admin-form-input" value="<?= e(get_setting('site_tagline', 'We are a global business enterprise headquartered in Singapore And India')) ?>">
                </div>
            </div>
        </div>
    </div>

    <!-- TAB 2: Contacts & Directory -->
    <div id="tab-contacts" class="settings-tab-pane" style="display: none;">
        <div class="admin-card">
            <div class="admin-card-header">
                <h3 class="admin-card-title">Corporate Phone Numbers &amp; Email Addresses</h3>
            </div>
            <div class="admin-card-body">
                <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 20px;">
                    <div class="admin-form-group">
                        <label class="admin-form-label" for="header_phone_sg">Singapore Office Mobile</label>
                        <input type="text" id="header_phone_sg" name="header_phone_sg" class="admin-form-input" value="<?= e(get_setting('header_phone_sg', '(65) 89451100')) ?>">
                    </div>

                    <div class="admin-form-group">
                        <label class="admin-form-label" for="header_phone_roam">Global Roaming (24/7)</label>
                        <input type="text" id="header_phone_roam" name="header_phone_roam" class="admin-form-input" value="<?= e(get_setting('header_phone_roam', '(91) 99100 02299')) ?>">
                    </div>

                    <div class="admin-form-group">
                        <label class="admin-form-label" for="fax_number">Fax / Alternate Line</label>
                        <input type="text" id="fax_number" name="fax_number" class="admin-form-input" value="<?= e(get_setting('fax_number', '(65) 98260001 / (91) 99100 02299')) ?>">
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 20px; margin-top: 15px;">
                    <div class="admin-form-group">
                        <label class="admin-form-label" for="contact_email_primary">Primary Enquiries Email</label>
                        <input type="email" id="contact_email_primary" name="contact_email_primary" class="admin-form-input" value="<?= e(get_setting('contact_email_primary', 'admin@globalconnectsg.com')) ?>">
                    </div>

                    <div class="admin-form-group">
                        <label class="admin-form-label" for="contact_email_secondary">Secondary Director Email</label>
                        <input type="email" id="contact_email_secondary" name="contact_email_secondary" class="admin-form-input" value="<?= e(get_setting('contact_email_secondary', 'shammy@globalconnectsg.com')) ?>">
                    </div>

                    <div class="admin-form-group">
                        <label class="admin-form-label" for="contact_email_tertiary">Tertiary Support Email</label>
                        <input type="email" id="contact_email_tertiary" name="contact_email_tertiary" class="admin-form-input" value="<?= e(get_setting('contact_email_tertiary', 'tsnshammy@gmail.com')) ?>">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- TAB 3: Office Locations -->
    <div id="tab-locations" class="settings-tab-pane" style="display: none;">
        <div class="admin-card">
            <div class="admin-card-header">
                <h3 class="admin-card-title">Registered Office Addresses</h3>
            </div>
            <div class="admin-card-body">
                <div class="admin-form-group">
                    <label class="admin-form-label" for="address_sg">Singapore Global HQ Address</label>
                    <textarea id="address_sg" name="address_sg" class="admin-form-textarea" rows="2"><?= e(get_setting('address_sg', '101 KITCHENER ROAD #03-43 JALAN BESAR PLAZA SINGAPORE (208511)')) ?></textarea>
                </div>

                <div class="admin-form-group">
                    <label class="admin-form-label" for="address_india">India Corporate Office Address</label>
                    <textarea id="address_india" name="address_india" class="admin-form-textarea" rows="2"><?= e(get_setting('address_india', 'NO. 211 RAJNIGANDHA BLOCK, GARDEN APARTMENT, 21 VITTHAL MALLYA ROAD (opp. UB CITY) BANGLORE 560001 INDIA')) ?></textarea>
                </div>

                <div class="admin-form-group">
                    <label class="admin-form-label" for="address_branches">India Branches Note</label>
                    <input type="text" id="address_branches" name="address_branches" class="admin-form-input" value="<?= e(get_setting('address_branches', 'New Delhi, Mumbai, Hyderabad and Chennai (By Appointment ONLY – IN HYATT GROUP OF HOTELS)')) ?>">
                </div>

                <div class="admin-form-group">
                    <label class="admin-form-label" for="address_indonesia">Indonesia Jakarta Office</label>
                    <textarea id="address_indonesia" name="address_indonesia" class="admin-form-textarea" rows="2"><?= e(get_setting('address_indonesia', 'HOTEL GRAND HYATT PLAZA, JL. M.H. THAMRIN KAV. 28-30, GONDANGDIA, MENTENG, JAKARTA, 10350, INDONESIA')) ?></textarea>
                </div>

                <div class="admin-form-group">
                    <label class="admin-form-label" for="address_hongkong">Hong Kong Office</label>
                    <textarea id="address_hongkong" name="address_hongkong" class="admin-form-textarea" rows="2"><?= e(get_setting('address_hongkong', 'HOTEL HYATT REGENCY 18 HANOI ROAD TSIM SHA TSU KOWLOON HONGKONG')) ?></textarea>
                </div>
            </div>
        </div>
    </div>

    <!-- TAB 4: Statistics Counters -->
    <div id="tab-stats" class="settings-tab-pane" style="display: none;">
        <div class="admin-card">
            <div class="admin-card-header">
                <h3 class="admin-card-title">Homepage Key Statistics Bar</h3>
            </div>
            <div class="admin-card-body">
                <p style="font-size: 13px; color: var(--admin-text-muted); margin-bottom: 20px;">
                    These 4 statistics animate smoothly on the homepage with a live count-up animation when scrolled into view.
                </p>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                    <!-- Stat 1 -->
                    <div style="background: #f8fafc; padding: 15px; border-radius: 8px; border: 1px solid #e2e8f0;">
                        <h4 style="font-size: 14px; margin: 0 0 10px; color: var(--admin-primary);">Counter 1</h4>
                        <div class="admin-form-group">
                            <label class="admin-form-label" for="stat_partners_num">Number Value</label>
                            <input type="text" id="stat_partners_num" name="stat_partners_num" class="admin-form-input" value="<?= e(get_setting('stat_partners_num', '3,600+')) ?>">
                        </div>
                        <div class="admin-form-group" style="margin-bottom: 0;">
                            <label class="admin-form-label" for="stat_partners_label">Label Text</label>
                            <input type="text" id="stat_partners_label" name="stat_partners_label" class="admin-form-input" value="<?= e(get_setting('stat_partners_label', 'Associate Partners')) ?>">
                        </div>
                    </div>

                    <!-- Stat 2 -->
                    <div style="background: #f8fafc; padding: 15px; border-radius: 8px; border: 1px solid #e2e8f0;">
                        <h4 style="font-size: 14px; margin: 0 0 10px; color: var(--admin-primary);">Counter 2</h4>
                        <div class="admin-form-group">
                            <label class="admin-form-label" for="stat_countries_num">Number Value</label>
                            <input type="text" id="stat_countries_num" name="stat_countries_num" class="admin-form-input" value="<?= e(get_setting('stat_countries_num', '19+')) ?>">
                        </div>
                        <div class="admin-form-group" style="margin-bottom: 0;">
                            <label class="admin-form-label" for="stat_countries_label">Label Text</label>
                            <input type="text" id="stat_countries_label" name="stat_countries_label" class="admin-form-input" value="<?= e(get_setting('stat_countries_label', 'Countries Globally')) ?>">
                        </div>
                    </div>

                    <!-- Stat 3 -->
                    <div style="background: #f8fafc; padding: 15px; border-radius: 8px; border: 1px solid #e2e8f0;">
                        <h4 style="font-size: 14px; margin: 0 0 10px; color: var(--admin-primary);">Counter 3</h4>
                        <div class="admin-form-group">
                            <label class="admin-form-label" for="stat_banks_num">Number Value</label>
                            <input type="text" id="stat_banks_num" name="stat_banks_num" class="admin-form-input" value="<?= e(get_setting('stat_banks_num', '19+')) ?>">
                        </div>
                        <div class="admin-form-group" style="margin-bottom: 0;">
                            <label class="admin-form-label" for="stat_banks_label">Label Text</label>
                            <input type="text" id="stat_banks_label" name="stat_banks_label" class="admin-form-input" value="<?= e(get_setting('stat_banks_label', 'Partner Banks')) ?>">
                        </div>
                    </div>

                    <!-- Stat 4 -->
                    <div style="background: #f8fafc; padding: 15px; border-radius: 8px; border: 1px solid #e2e8f0;">
                        <h4 style="font-size: 14px; margin: 0 0 10px; color: var(--admin-primary);">Counter 4</h4>
                        <div class="admin-form-group">
                            <label class="admin-form-label" for="stat_experience_num">Number Value</label>
                            <input type="text" id="stat_experience_num" name="stat_experience_num" class="admin-form-input" value="<?= e(get_setting('stat_experience_num', '25+')) ?>">
                        </div>
                        <div class="admin-form-group" style="margin-bottom: 0;">
                            <label class="admin-form-label" for="stat_experience_label">Label Text</label>
                            <input type="text" id="stat_experience_label" name="stat_experience_label" class="admin-form-input" value="<?= e(get_setting('stat_experience_label', 'Years Cumulative Experience')) ?>">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- TAB 5: Homepage Content -->
    <div id="tab-homepage" class="settings-tab-pane" style="display: none;">
        <div class="admin-card">
            <div class="admin-card-header">
                <h3 class="admin-card-title">Homepage Section Texts &amp; Call To Action</h3>
            </div>
            <div class="admin-card-body">
                <!-- Section 1: Company Experience -->
                <h4 style="font-size: 15px; color: var(--admin-primary); margin-bottom: 12px; border-bottom: 1px solid var(--admin-border); padding-bottom: 6px;">
                    1. Company Experience &amp; Philosophy Section
                </h4>
                <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 20px;">
                    <div class="admin-form-group">
                        <label class="admin-form-label" for="home_exp_subtitle">Section Subtitle</label>
                        <input type="text" id="home_exp_subtitle" name="home_exp_subtitle" class="admin-form-input" value="<?= e(get_setting('home_exp_subtitle', 'Company Experience')) ?>">
                    </div>
                    <div class="admin-form-group">
                        <label class="admin-form-label" for="home_exp_title">Main Headline Title</label>
                        <input type="text" id="home_exp_title" name="home_exp_title" class="admin-form-input" value="<?= e(get_setting('home_exp_title', 'Global Connect’s Believe in Growth Strategies')) ?>">
                    </div>
                </div>

                <div class="admin-form-group">
                    <label class="admin-form-label" for="home_exp_lead">Lead Introduction Text</label>
                    <textarea id="home_exp_lead" name="home_exp_lead" class="admin-form-textarea" rows="2"><?= e(get_setting('home_exp_lead', 'The organisation involves various teams to handle client needs which start from Capital Raising, Project Finance, Financial Engineering or Structuring, and Fund Raising programs to name a few.')) ?></textarea>
                </div>

                <div class="admin-form-group">
                    <label class="admin-form-label" for="home_exp_body">Detailed Body Paragraph</label>
                    <textarea id="home_exp_body" name="home_exp_body" class="admin-form-textarea" rows="3"><?= e(get_setting('home_exp_body', 'Fund raising is our daily work and we are always available to support your short term & long term needs. Global Connect involves itself with clients in achieving results. May it start with determining the most suitable structure to reach the end result. Global Connect is looking forward to participate with clients to translate their visions to reality.')) ?></textarea>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                    <div class="admin-form-group">
                        <label class="admin-form-label" for="exp_image_file">Upload Experience Section Image</label>
                        <input type="file" id="exp_image_file" name="exp_image_file" class="admin-form-input" accept="image/*">
                    </div>
                    <div class="admin-form-group">
                        <label class="admin-form-label" for="home_exp_image">Or Image Path</label>
                        <input type="text" id="home_exp_image" name="home_exp_image" class="admin-form-input" value="<?= e(get_setting('home_exp_image', 'about-growth.jpg')) ?>">
                    </div>
                </div>

                <!-- Section 2: Bottom Call to Action -->
                <h4 style="font-size: 15px; color: var(--admin-primary); margin-top: 25px; margin-bottom: 12px; border-bottom: 1px solid var(--admin-border); padding-bottom: 6px;">
                    2. Bottom Call To Action (CTA) Section
                </h4>
                <div class="admin-form-group">
                    <label class="admin-form-label" for="home_cta_title">CTA Headline Title</label>
                    <input type="text" id="home_cta_title" name="home_cta_title" class="admin-form-input" value="<?= e(get_setting('home_cta_title', 'Ready to Expand Your Global Trade & Capital Reach?')) ?>">
                </div>

                <div class="admin-form-group">
                    <label class="admin-form-label" for="home_cta_lead">CTA Subtitle Description</label>
                    <textarea id="home_cta_lead" name="home_cta_lead" class="admin-form-textarea" rows="2"><?= e(get_setting('home_cta_lead', 'Partner with Global Connect for seamless sourcing, commodity allocations, and structured trade finance solutions tailored to your business needs.')) ?></textarea>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                    <div class="admin-form-group">
                        <label class="admin-form-label" for="home_cta_btn1_text">Primary Button Text</label>
                        <input type="text" id="home_cta_btn1_text" name="home_cta_btn1_text" class="admin-form-input" value="<?= e(get_setting('home_cta_btn1_text', 'Schedule an Executive Consultation')) ?>">
                    </div>
                    <div class="admin-form-group">
                        <label class="admin-form-label" for="home_cta_btn1_link">Primary Button Link</label>
                        <input type="text" id="home_cta_btn1_link" name="home_cta_btn1_link" class="admin-form-input" value="<?= e(get_setting('home_cta_btn1_link', 'contact.php')) ?>">
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                    <div class="admin-form-group">
                        <label class="admin-form-label" for="home_cta_btn2_text">Secondary Button Text</label>
                        <input type="text" id="home_cta_btn2_text" name="home_cta_btn2_text" class="admin-form-input" value="<?= e(get_setting('home_cta_btn2_text', 'Explore Product Portfolio')) ?>">
                    </div>
                    <div class="admin-form-group">
                        <label class="admin-form-label" for="home_cta_btn2_link">Secondary Button Link</label>
                        <input type="text" id="home_cta_btn2_link" name="home_cta_btn2_link" class="admin-form-input" value="<?= e(get_setting('home_cta_btn2_link', 'trading.php')) ?>">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- TAB 6: Footer & Legal -->
    <div id="tab-footer" class="settings-tab-pane" style="display: none;">
        <div class="admin-card">
            <div class="admin-card-header">
                <h3 class="admin-card-title">Footer Text &amp; Copyright</h3>
            </div>
            <div class="admin-card-body">
                <div class="admin-form-group">
                    <label class="admin-form-label" for="footer_about">Footer Company Summary</label>
                    <textarea id="footer_about" name="footer_about" class="admin-form-textarea" rows="3"><?= e(get_setting('footer_about', 'At Global Connect, we believe in building meaningful partnerships that drive success across borders. With our expertise and network, we connect businesses to new markets and lasting opportunities.')) ?></textarea>
                </div>

                <div class="admin-form-group">
                    <label class="admin-form-label" for="footer_copyright">Footer Copyright Text</label>
                    <input type="text" id="footer_copyright" name="footer_copyright" class="admin-form-input" value="<?= e(get_setting('footer_copyright', '© 2026 Global Connect. All rights reserved. | Sourcing & Supply Chain Strategic Partner.')) ?>">
                </div>
            </div>
        </div>
    </div>

    <!-- Save Floating Action Bar -->
    <div style="margin-top: 25px; padding: 18px 24px; background: #ffffff; border: 1px solid var(--admin-border); border-radius: 8px; display: flex; justify-content: space-between; align-items: center; box-shadow: var(--admin-shadow);">
        <div style="font-size: 13px; color: var(--admin-text-muted);">
            ✨ Changes saved here immediately update the live public website and admin portal.
        </div>
        <button type="submit" class="btn-admin-primary" style="padding: 12px 32px; font-size: 15px;">
            Save All Settings &amp; Logo →
        </button>
    </div>
</form>

<style>
.tab-btn {
    background: transparent;
    border: none;
    padding: 10px 18px;
    font-size: 14px;
    font-weight: 600;
    color: var(--admin-text-muted);
    border-radius: 6px;
    cursor: pointer;
    transition: all 0.2s ease;
}
.tab-btn:hover {
    color: var(--admin-primary);
    background: rgba(14, 116, 144, 0.08);
}
.tab-btn.active {
    color: var(--admin-primary);
    background: #e0f2fe;
}
</style>

<script>
function switchTab(tabId, btn) {
    document.querySelectorAll('.settings-tab-pane').forEach(el => el.style.display = 'none');
    document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
    const target = document.getElementById(tabId);
    if (target) target.style.display = 'block';
    if (btn) btn.classList.add('active');
}

function previewImg(input, targetImgId) {
    const preview = document.getElementById(targetImgId);
    if (input.files && input.files[0] && preview) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.src = e.target.result;
        }
        reader.readAsDataURL(input.files[0]);
    }
}
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
