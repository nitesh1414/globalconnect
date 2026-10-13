<?php
/**
 * Global Connect - Site Settings Management
 */
$adminPageTitle = 'Site Settings | Global Connect CMS';
$adminPageHeading = 'Global Website Settings';

require_once __DIR__ . '/includes/header.php';

$db = get_db();
$errorMsg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['csrf_token'] ?? '')) {
        $errorMsg = 'Security validation failed.';
    } else {
        $fields = [
            'site_title', 'site_tagline', 'company_name',
            'header_phone_sg', 'header_phone_roam', 'fax_number',
            'contact_email_primary', 'contact_email_secondary', 'contact_email_tertiary',
            'address_sg', 'address_india', 'address_branches', 'address_indonesia', 'address_hongkong',
            'stat_partners', 'stat_countries', 'stat_banks',
            'logo_url', 'banner_url', 'footer_about'
        ];

        try {
            $stmt = $db->prepare("INSERT OR REPLACE INTO settings (setting_key, setting_value, updated_at) VALUES (?, ?, CURRENT_TIMESTAMP)");
            foreach ($fields as $field) {
                if (isset($_POST[$field])) {
                    $val = clean($_POST[$field]);
                    $stmt->execute([$field, $val]);
                }
            }
            set_flash('success', 'Website settings updated successfully!');
            header('Location: /admin/settings.php');
            exit;
        } catch (Exception $e) {
            $errorMsg = 'Error saving settings: ' . $e->getMessage();
        }
    }
}
?>

<div class="admin-card">
    <div class="admin-card-header">
        <h3>Company Information &amp; Website Configuration</h3>
    </div>

    <div class="admin-card-body">
        <?php if ($errorMsg): ?>
            <div style="background: #fee2e2; color: #b91c1c; padding: 12px 18px; border-radius: 8px; margin-bottom: 20px;">
                ⚠ <?= e($errorMsg) ?>
            </div>
        <?php endif; ?>

        <form action="/admin/settings.php" method="POST">
            <?= csrf_field() ?>

            <!-- General -->
            <h4 style="font-size: 16px; color: var(--admin-primary); margin-bottom: 15px; border-bottom: 2px solid var(--admin-border); padding-bottom: 8px;">
                General Branding
            </h4>

            <div class="form-row">
                <div class="admin-form-group">
                    <label class="admin-form-label" for="site_title">Website Title</label>
                    <input type="text" id="site_title" name="site_title" class="admin-form-input" value="<?= e(get_setting('site_title')) ?>">
                </div>

                <div class="admin-form-group">
                    <label class="admin-form-label" for="company_name">Company Name</label>
                    <input type="text" id="company_name" name="company_name" class="admin-form-input" value="<?= e(get_setting('company_name')) ?>">
                </div>
            </div>

            <div class="admin-form-group">
                <label class="admin-form-label" for="site_tagline">Header Tagline</label>
                <input type="text" id="site_tagline" name="site_tagline" class="admin-form-input" value="<?= e(get_setting('site_tagline')) ?>">
            </div>

            <!-- Contacts -->
            <h4 style="font-size: 16px; color: var(--admin-primary); margin-top: 30px; margin-bottom: 15px; border-bottom: 2px solid var(--admin-border); padding-bottom: 8px;">
                Phone Numbers &amp; Emails
            </h4>

            <div class="form-row">
                <div class="admin-form-group">
                    <label class="admin-form-label" for="header_phone_sg">Singapore Mobile No.</label>
                    <input type="text" id="header_phone_sg" name="header_phone_sg" class="admin-form-input" value="<?= e(get_setting('header_phone_sg')) ?>">
                </div>

                <div class="admin-form-group">
                    <label class="admin-form-label" for="header_phone_roam">Global Roaming No.</label>
                    <input type="text" id="header_phone_roam" name="header_phone_roam" class="admin-form-input" value="<?= e(get_setting('header_phone_roam')) ?>">
                </div>
            </div>

            <div class="form-row">
                <div class="admin-form-group">
                    <label class="admin-form-label" for="contact_email_primary">Primary Contact Email</label>
                    <input type="email" id="contact_email_primary" name="contact_email_primary" class="admin-form-input" value="<?= e(get_setting('contact_email_primary')) ?>">
                </div>

                <div class="admin-form-group">
                    <label class="admin-form-label" for="contact_email_secondary">Secondary Director Email</label>
                    <input type="email" id="contact_email_secondary" name="contact_email_secondary" class="admin-form-input" value="<?= e(get_setting('contact_email_secondary')) ?>">
                </div>
            </div>

            <!-- Headquarters -->
            <h4 style="font-size: 16px; color: var(--admin-primary); margin-top: 30px; margin-bottom: 15px; border-bottom: 2px solid var(--admin-border); padding-bottom: 8px;">
                Office Locations
            </h4>

            <div class="admin-form-group">
                <label class="admin-form-label" for="address_sg">Singapore Global HQ Address</label>
                <input type="text" id="address_sg" name="address_sg" class="admin-form-input" value="<?= e(get_setting('address_sg')) ?>">
            </div>

            <div class="admin-form-group">
                <label class="admin-form-label" for="address_india">India Bangalore HQ Address</label>
                <input type="text" id="address_india" name="address_india" class="admin-form-input" value="<?= e(get_setting('address_india')) ?>">
            </div>

            <div class="form-row">
                <div class="admin-form-group">
                    <label class="admin-form-label" for="address_indonesia">Indonesia Presence</label>
                    <input type="text" id="address_indonesia" name="address_indonesia" class="admin-form-input" value="<?= e(get_setting('address_indonesia')) ?>">
                </div>

                <div class="admin-form-group">
                    <label class="admin-form-label" for="address_hongkong">Hong Kong Presence</label>
                    <input type="text" id="address_hongkong" name="address_hongkong" class="admin-form-input" value="<?= e(get_setting('address_hongkong')) ?>">
                </div>
            </div>

            <!-- Key Statistics -->
            <h4 style="font-size: 16px; color: var(--admin-primary); margin-top: 30px; margin-bottom: 15px; border-bottom: 2px solid var(--admin-border); padding-bottom: 8px;">
                Homepage Statistics Counters
            </h4>

            <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px;">
                <div class="admin-form-group">
                    <label class="admin-form-label" for="stat_partners">Associate Partners Counter</label>
                    <input type="text" id="stat_partners" name="stat_partners" class="admin-form-input" value="<?= e(get_setting('stat_partners')) ?>">
                </div>

                <div class="admin-form-group">
                    <label class="admin-form-label" for="stat_countries">Countries Counter</label>
                    <input type="text" id="stat_countries" name="stat_countries" class="admin-form-input" value="<?= e(get_setting('stat_countries')) ?>">
                </div>

                <div class="admin-form-group">
                    <label class="admin-form-label" for="stat_banks">Partner Banks Counter</label>
                    <input type="text" id="stat_banks" name="stat_banks" class="admin-form-input" value="<?= e(get_setting('stat_banks')) ?>">
                </div>
            </div>

            <!-- Footer -->
            <div class="admin-form-group" style="margin-top: 20px;">
                <label class="admin-form-label" for="footer_about">Footer Company Summary</label>
                <textarea id="footer_about" name="footer_about" class="admin-form-textarea" rows="3"><?= e(get_setting('footer_about')) ?></textarea>
            </div>

            <div style="margin-top: 30px;">
                <button type="submit" class="btn-admin-primary" style="padding: 13px 32px;">
                    Save All Settings →
                </button>
            </div>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
