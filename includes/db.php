<?php
/**
 * Global Connect - Database Connection and Initialization Handler
 */

class Database {
    private static ?PDO $instance = null;

    public static function getConnection(): PDO {
        if (self::$instance === null) {
            $config = require __DIR__ . '/../config/database.php';
            $driver = $config['driver'] ?? 'sqlite';

            // Check if MySQL credentials are provided via ENV or config
            if ($driver === 'mysql' || (getenv('DB_HOST') && getenv('DB_NAME'))) {
                try {
                    $c = $config['mysql'];
                    $dsn = "mysql:host={$c['host']};port={$c['port']};dbname={$c['database']};charset={$c['charset']}";
                    self::$instance = new PDO($dsn, $c['username'], $c['password'], $c['options']);
                    self::ensureTables(self::$instance, 'mysql');
                    return self::$instance;
                } catch (PDOException $e) {
                    // Fall back to SQLite if MySQL fails
                    error_log("MySQL Connection failed, falling back to SQLite: " . $e->getMessage());
                }
            }

            // SQLite Fallback
            $sqlitePath = $config['sqlite']['path'];
            $dir = dirname($sqlitePath);
            if (!is_dir($dir)) {
                mkdir($dir, 0777, true);
            }

            self::$instance = new PDO("sqlite:" . $sqlitePath, null, null, $config['sqlite']['options']);
            self::ensureTables(self::$instance, 'sqlite');
        }

        return self::$instance;
    }

    private static function ensureTables(PDO $db, string $driver): void {
        if ($driver === 'sqlite') {
            // Check if pages table exists
            $stmt = $db->query("SELECT name FROM sqlite_master WHERE type='table' AND name='pages'");
            $exists = $stmt->fetch();
            if (!$exists) {
                self::initSQLiteTables($db);
                self::seedInitialData($db);
            }
        }
    }

    private static function initSQLiteTables(PDO $db): void {
        $db->exec("
            CREATE TABLE IF NOT EXISTS users (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                username TEXT NOT NULL UNIQUE,
                password TEXT NOT NULL,
                email TEXT NOT NULL,
                full_name TEXT DEFAULT 'Administrator',
                role TEXT DEFAULT 'admin',
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
            );

            CREATE TABLE IF NOT EXISTS categories (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                name TEXT NOT NULL,
                slug TEXT NOT NULL UNIQUE,
                description TEXT,
                menu_order INTEGER DEFAULT 0,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP
            );

            CREATE TABLE IF NOT EXISTS pages (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                category_id INTEGER,
                title TEXT NOT NULL,
                slug TEXT NOT NULL UNIQUE,
                subtitle TEXT DEFAULT '',
                banner_title TEXT DEFAULT '',
                banner_subtitle TEXT DEFAULT '',
                featured_image TEXT DEFAULT '',
                summary TEXT,
                content TEXT,
                meta_title TEXT DEFAULT '',
                meta_description TEXT DEFAULT '',
                meta_keywords TEXT DEFAULT '',
                status TEXT DEFAULT 'published',
                menu_order INTEGER DEFAULT 0,
                is_system INTEGER DEFAULT 0,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
            );

            CREATE TABLE IF NOT EXISTS settings (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                setting_key TEXT NOT NULL UNIQUE,
                setting_value TEXT,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
            );

            CREATE TABLE IF NOT EXISTS contacts (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                name TEXT NOT NULL,
                email TEXT NOT NULL,
                phone TEXT DEFAULT '',
                subject TEXT DEFAULT '',
                message TEXT NOT NULL,
                status TEXT DEFAULT 'unread',
                ip_address TEXT DEFAULT '',
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP
            );

            CREATE TABLE IF NOT EXISTS media (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                title TEXT NOT NULL,
                filename TEXT NOT NULL,
                filepath TEXT NOT NULL,
                filetype TEXT DEFAULT '',
                filesize INTEGER DEFAULT 0,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP
            );
        ");
    }

    public static function seedInitialData(PDO $db): void {
        // Create Admin User (admin / admin123)
        $adminPass = password_hash('admin123', PASSWORD_BCRYPT);
        $userStmt = $db->prepare("INSERT OR IGNORE INTO users (id, username, password, email, full_name, role) VALUES (1, 'admin', ?, 'admin@globalconnectsg.com', 'Admin User', 'admin')");
        $userStmt->execute([$adminPass]);

        // Categories
        $db->exec("
            INSERT OR IGNORE INTO categories (id, name, slug, description, menu_order) VALUES
            (1, 'General', 'general', 'Core company pages', 1),
            (2, 'Trading', 'trading', 'Commodities, Minerals, Agro & Energy Trading', 2),
            (3, 'Finance', 'finance', 'Trade Finance, Banking Support & Advisory Solutions', 3),
            (4, 'Consulting', 'consulting', 'Corporate Financial & Advisory Consulting', 4);
        ");

        // Settings
        $settings = [
            'site_title' => 'Global Connect',
            'site_tagline' => 'We are a global business enterprise headquartered in Singapore And India',
            'company_name' => 'Global Connect',
            'header_phone_sg' => '(65) 89451100',
            'header_phone_roam' => '(91) 99100 02299',
            'fax_number' => '(65) 98260001 / (91) 99100 02299',
            'contact_email_primary' => 'admin@globalconnectsg.com',
            'contact_email_secondary' => 'shammy@globalconnectsg.com',
            'contact_email_tertiary' => 'tsnshammy@gmail.com',
            'address_sg' => '101 KITCHENER ROAD #03-43 JALAN BESAR PLAZA SINGAPORE (208511)',
            'address_india' => 'NO. 211 RAJNIGANDHA BLOCK, GARDEN APARTMENT, 21 VITTHAL MALLYA ROAD (opp. UB CITY) BANGLORE 560001 INDIA',
            'address_branches' => 'New Delhi, Mumbai, Hyderabad and Chennai (By Appointment ONLY – IN HYATT GROUP OF HOTELS)',
            'address_indonesia' => 'HOTEL GRAND HYATT PLAZA, JL. M.H. THAMRIN KAV. 28-30, GONDANGDIA, MENTENG, JAKARTA, 10350, INDONESIA',
            'address_hongkong' => 'HOTEL HYATT REGENCY 18 HANOI ROAD TSIM SHA TSU KOWLOON HONGKONG',
            'stat_partners' => '3,600+',
            'stat_countries' => '19+',
            'stat_banks' => '19+',
            'logo_url' => '/assets/images/logo.svg',
            'banner_url' => '/assets/images/banner-hero-1.png',
            'footer_about' => 'At Global Connect, we believe in building meaningful partnerships that drive success across borders. With our expertise and network, we connect businesses to new markets and lasting opportunities. Join us in creating pathways for growth, innovation, and global collaboration.'
        ];

        $stmtSet = $db->prepare("INSERT OR REPLACE INTO settings (setting_key, setting_value) VALUES (?, ?)");
        foreach ($settings as $key => $val) {
            $stmtSet->execute([$key, $val]);
        }

        // Pages Seed Data
        self::seedPages($db);
    }

    private static function seedPages(PDO $db): void {
        $pages = [
            // Home
            [
                'category_id' => 1,
                'title' => 'Home',
                'slug' => 'home',
                'subtitle' => 'Welcome to Global Connect',
                'banner_title' => 'We are a global business enterprise headquartered in Singapore And India',
                'banner_subtitle' => 'At Global Connect, we believe in building meaningful partnerships that drive success across borders. With our expertise and network, we connect businesses to new markets and lasting opportunities. Join us in creating pathways for growth, innovation, and global collaboration.',
                'featured_image' => '/assets/images/banner-hero-1.png',
                'summary' => 'Global Connect’s believe in Growth Strategies. The organisation involves various teams to handle client needs which start from Capital Raising, Project Finance, Financial Engineering or Structuring, Fund raising programs to name a few.',
                'content' => '',
                'meta_title' => 'Global Connect | Global Business Enterprise Headquartered in Singapore & India',
                'meta_description' => 'Global Connect - Reliable partner for international trade, trade finance, and corporate financial advisory services.',
                'status' => 'published',
                'menu_order' => 1,
                'is_system' => 1
            ],
            // About
            [
                'category_id' => 1,
                'title' => 'About Us',
                'slug' => 'about',
                'subtitle' => 'About Us',
                'banner_title' => 'About Global Connect',
                'banner_subtitle' => 'Global Connect’s believe in Growth Strategies.',
                'featured_image' => '/assets/images/about-growth.jpg',
                'summary' => 'Fund raising is our daily work and we are always available to support your short term & long term needs.',
                'content' => '<h3>Global Connect’s believe in Growth Strategies</h3>
<p>The organisation involves various teams to handle client needs which start from Capital Raising, Project Finance, Financial Engineering or Structuring, Fund raising programs to name a few.</p>
<p>Fund raising is our daily work and we are always available to support your short term & long term needs. Global Connect’s involves itself with clients in achieving results. May it start with determining you to achieve the most suitable structure to reach the end result. Global Connect’s is looking forward to participate with the clients to translate their visions to reality – which is differently worded in our Tag line itself. Global Connect’s Team is a combination of Business, Functional, Financial, Technical and operational skills to align with the activities of the Businesses and Promoters Skills for achieving highest results.</p>
<div class="my-4"><img src="/assets/images/about-growth.jpg" alt="Growth Ideas" class="img-fluid rounded shadow-sm" /></div>
<h4 class="mt-4 text-primary">What Makes Us Unique</h4>
<p>Global Connect’s group provides solutions to Institutions and Corporate in managing and growing their business via high quality financial advisory services and capital market solutions.</p>',
                'meta_title' => 'About Us | Global Connect',
                'meta_description' => 'Learn about Global Connect, our growth strategies, expert team, and unique cross-border financial advisory capabilities.',
                'status' => 'published',
                'menu_order' => 2,
                'is_system' => 1
            ],
            // Trading Overview
            [
                'category_id' => 2,
                'title' => 'Trading',
                'slug' => 'trading',
                'subtitle' => 'Our Trading Services',
                'banner_title' => 'International Trading Services',
                'banner_subtitle' => 'We trade in a variety of products and commodities all around the world.',
                'featured_image' => '/assets/images/trading-home.jpg',
                'summary' => 'We trade in a variety of products and commodities all around the world. We pride in our diverse range of offerings to match our customer needs and requirements.',
                'content' => '<p class="lead">We trade in a variety of products and commodities all around the world. We pride in our diverse range of offerings to match our customer needs and requirements.</p>
<p>Having strong networks with manufacturers and exporters, we are enabled to ensure a timely and efficient fulfilment of trade requirements from our customers. We pride ourselves in our constantly evolving product lines and offerings.</p>',
                'meta_title' => 'Trading Services | Global Connect',
                'meta_description' => 'Global Connect trades in Gold Bullion, Sugar, Agro Products, Minerals, Metals, Ores, and Petroleum & Lubricants worldwide.',
                'status' => 'published',
                'menu_order' => 3,
                'is_system' => 1
            ],
            // Trading Subpage: Gold Bullion
            [
                'category_id' => 2,
                'title' => 'Gold Bullion',
                'slug' => 'gold-bullion',
                'subtitle' => 'Gold Bar and Mining',
                'banner_title' => 'Gold Bullion Trading',
                'banner_subtitle' => 'Gold Bar and Mining Solutions Worldwide',
                'featured_image' => '/assets/images/gold-bullion.jpg',
                'summary' => 'Of all the precious metals, Gold has been the most popular as an investment. Global Connect has been actively trading in Gold Bullion for many years.',
                'content' => '<div class="page-intro mb-4">
    <span class="badge bg-gold text-dark px-3 py-2 rounded-pill font-weight-bold">Gold Bar and Mining</span>
    <h3 class="mt-3">Of all the precious metals, Gold has been the most popular as an investment. Gold bullion comes in two forms namely Gold Bullion Bars and Bullion Coins. Global Connect, has been actively trading in Gold Bullion for many years now.</h3>
</div>
<p>We buy and sell Gold Bullion Bars from multiple countries. Bullion bars come in many sizes ranging from a few grams to 5 kilo gram bars. We buy Bullion bars of sizes 10 grams to 500 grams, 1 Kilo Gram, 2 Kilo Gram and 5 Kilo Gram, of purity 9995 and up. We can buy 200 Kilo grams worth of Bullion daily. Our payments would be made after receiving the Assay reports from the respective refineries. Interested sellers can send their quotes on the basis of deliveries globally.</p>
<div class="my-4 text-center">
    <img src="/assets/images/gold-bullion-2.jpg" alt="Gold Bullion Bars" class="img-fluid rounded shadow-sm border" style="max-height: 420px; object-fit: cover; width: 100%;" />
</div>
<h4 class="mt-4 text-primary">Assay & Delivery Guidelines</h4>
<p>Our suppliers should ensure that their gold is assayed in the following refinery globally, for testing the purity of precious metals prior to our payment.</p>
<p>Before you make your gold deliveries to the mentioned refinery, kindly take a prior consent from us. In addition, all deliveries to the refinery should be made in our company name account of <strong>“GLOBAL CONNECT”</strong>, and kindly fax the delivery receipt to us at <strong>(65) 98260001 / (91) 99100 02299</strong> or Email us at <a href="mailto:shammy@globalconnectsg.com">shammy@globalconnectsg.com</a> for necessary action.</p>
<p>Country of origin and a declaration that goods are not of criminal origin by the owner is required while shipping the goods to our above refinery under our company name. Gold delivery done to our refinery will be subject to your prior acceptance of our terms and our written consent for delivery to our refinery. If any delivery of precious metal is done without our consent, it will be rejected by us and our refinery.</p>
<div class="card bg-light border-0 p-4 mt-4 shadow-sm">
    <h5 class="text-dark border-bottom pb-2 mb-3">Refinery Assaying Standards & Returnable Rates</h5>
    <div class="mb-3">
        <h6 class="text-primary font-weight-bold">Gold Bars / Major SCRAP 99% Plus</h6>
        <p class="mb-1 text-muted">Returnable Rate: <strong>Gold 100%</strong> on assay from after melt weight.</p>
    </div>
    <div class="mb-3">
        <h6 class="text-primary font-weight-bold">Gold Bars / Major SCRAP 97% - 98.99% Plus</h6>
        <p class="mb-1 text-muted">Returnable Rate: <strong>Gold 100%</strong> on assay from after melt weight.</p>
    </div>
    <div class="mb-0">
        <h6 class="text-primary font-weight-bold">Gold Bars / Major SCRAP 50% - 96.99% Plus with Silver</h6>
        <ul class="text-muted mb-0">
            <li>Returnable Rate : <strong>Gold 99.8%</strong> on assay</li>
            <li>Silver &gt;0.5% &amp; &lt;20% - <strong>98%</strong> on assay</li>
            <li>Platinum &gt;0.05% &amp; &lt;15% - <strong>95%</strong> on assay (Refining charges for US$35.00/oz on recovery)</li>
            <li>Palladium &gt;0.05% &amp; &lt;15% - <strong>95%</strong> on assay (Refining charges for US$35.00/oz on recovery)</li>
        </ul>
    </div>
</div>',
                'meta_title' => 'Gold Bullion Trading | Global Connect',
                'meta_description' => 'Global Connect trades in Gold Bullion Bars and Coins globally with up to 200kg daily capacity, purity 9995+ and international refinery assay.',
                'status' => 'published',
                'menu_order' => 1,
                'is_system' => 0
            ],
            // Trading Subpage: Sugar
            [
                'category_id' => 2,
                'title' => 'Sugar',
                'slug' => 'sugar',
                'subtitle' => 'International Sugar Trading',
                'banner_title' => 'Sugar Trading & Global Sourcing',
                'banner_subtitle' => 'Wholesale White Refined & Raw Cane Sugar Supply',
                'featured_image' => '/assets/images/sugar-trade.jpg',
                'summary' => 'Global Connect specializes in high-volume international sugar trading, supplying refined cane sugar and raw sugar with complete quality assurance.',
                'content' => '<div class="page-intro mb-4">
    <span class="badge bg-primary text-white px-3 py-2 rounded-pill font-weight-bold">Commodity Trading</span>
    <h3 class="mt-3">Global Connect specializes in high-volume international sugar trading, connecting premium sugar mills and refiners with institutional buyers and distributors across the globe.</h3>
</div>
<p>We supply top-grade Refined Cane Sugar (ICUMSA 45) and Raw Brown Sugar (ICUMSA 600-1200 / VHP) sourced from premier producers across Brazil, Thailand, and India. Our trade desk ensures stringent quality verification, SGS certification, and prompt containerized or bulk vessel dispatch to major international ports.</p>
<div class="my-4 text-center">
    <img src="/assets/images/sugar-trade.jpg" alt="Sugar Trading" class="img-fluid rounded shadow-sm border" style="max-height: 420px; object-fit: cover; width: 100%;" />
</div>
<div class="row g-4 mt-2">
    <div class="col-md-6">
        <div class="card h-100 p-4 border-0 shadow-sm bg-light">
            <h5 class="text-primary font-weight-bold">ICUMSA 45 White Refined Sugar</h5>
            <ul class="text-muted mb-0">
                <li>Polarization: 99.80% minimum</li>
                <li>Moisture: 0.04% maximum</li>
                <li>Ash Content: 0.04% maximum</li>
                <li>Solubility: 100% dry and free flowing</li>
                <li>Color: Sparkling White</li>
                <li>Radiation: Normal certified without cesium or iodine</li>
            </ul>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card h-100 p-4 border-0 shadow-sm bg-light">
            <h5 class="text-primary font-weight-bold">Raw Brown Cane Sugar</h5>
            <ul class="text-muted mb-0">
                <li>ICUMSA: 600 - 1200 max</li>
                <li>Polarization: 97.80% to 99.20%</li>
                <li>Moisture: 0.15% maximum</li>
                <li>Packaging: 50kg bags or bulk in vessel</li>
                <li>Origin: Brazil / Thailand / India</li>
                <li>Inspection: SGS / Bureau Veritas at loading port</li>
            </ul>
        </div>
    </div>
</div>',
                'meta_title' => 'Sugar Trading | Global Connect',
                'meta_description' => 'International wholesale sugar trading by Global Connect. ICUMSA 45 refined white cane sugar and raw brown sugar supply.',
                'status' => 'published',
                'menu_order' => 2,
                'is_system' => 0
            ],
            // Trading Subpage: Agro Products
            [
                'category_id' => 2,
                'title' => 'Agro Products',
                'slug' => 'agro-products',
                'subtitle' => 'Agricultural Commodities',
                'banner_title' => 'Agro Products & Agricultural Commodities',
                'banner_subtitle' => 'Supplying Grains, Pulses, Rice, Spices, and Edible Oils Worldwide',
                'featured_image' => '/assets/images/agro-products.jpg',
                'summary' => 'Global Connect trades in diverse agricultural commodities, connecting farm producers and processing facilities with worldwide consumer markets.',
                'content' => '<div class="page-intro mb-4">
    <span class="badge bg-success text-white px-3 py-2 rounded-pill font-weight-bold">Agro Trading</span>
    <h3 class="mt-3">Global Connect trades in diverse agricultural commodities, connecting farm producers, mills, and processing facilities with worldwide consumer markets.</h3>
</div>
<p>Through our expansive agricultural supply chain network in South East Asia, India, and Africa, we deliver consistent quality agro products that meet international food safety standards. We manage complete logistics, phytosanitary certifications, and shipping.</p>
<div class="my-4 text-center">
    <img src="/assets/images/agro-products.jpg" alt="Agro Products" class="img-fluid rounded shadow-sm border" style="max-height: 420px; object-fit: cover; width: 100%;" />
</div>
<div class="row g-4 mt-2">
    <div class="col-md-4">
        <div class="card h-100 p-4 border-0 shadow-sm bg-light">
            <h5 class="text-success font-weight-bold">Rice & Grains</h5>
            <p class="text-muted mb-0">Premium Indian Basmati Rice, 1121 Sella, Non-Basmati (IR64, Sona Masoori, Jasmine rice), Yellow Corn/Maize, Wheat, and Barley.</p>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card h-100 p-4 border-0 shadow-sm bg-light">
            <h5 class="text-success font-weight-bold">Pulses & Oil Seeds</h5>
            <p class="text-muted mb-0">Chickpeas (Kabuli & Desi), Red Lentils, Yellow Peas, Green Mung Beans, Soybeans, Sesame Seeds, and Sunflower Seeds.</p>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card h-100 p-4 border-0 shadow-sm bg-light">
            <h5 class="text-success font-weight-bold">Spices & Edible Oils</h5>
            <p class="text-muted mb-0">Black Pepper, Turmeric, Cumin, Cardamom, Cloves, and refined cooking oils including Crude Palm Oil (CPO), Sunflower Oil, and Soybean Oil.</p>
        </div>
    </div>
</div>',
                'meta_title' => 'Agro Products Trading | Global Connect',
                'meta_description' => 'Agro products trading: Rice, grains, pulses, oil seeds, spices and edible oils supplied internationally by Global Connect.',
                'status' => 'published',
                'menu_order' => 3,
                'is_system' => 0
            ],
            // Trading Subpage: Minerals, Metals and Ores
            [
                'category_id' => 2,
                'title' => 'Minerals, Metals and Ores',
                'slug' => 'minerals-metals-and-ores',
                'subtitle' => 'Industrial Minerals & Metals',
                'banner_title' => 'Minerals, Metals and Ores',
                'banner_subtitle' => 'Refined & Reliable Raw Materials for Global Industry',
                'featured_image' => '/assets/images/minerals-metals.jpg',
                'summary' => 'We have been dealing in minerals, metals and ores for many years now, and have built our expertise to ensure that only refined and reliable qualities are offered.',
                'content' => '<div class="page-intro mb-4">
    <span class="badge bg-secondary text-white px-3 py-2 rounded-pill font-weight-bold">Industrial Resources</span>
    <h3 class="mt-3">We have been dealing in minerals, metals and ores for many years now, and have built our expertise to ensure that only refined and reliable qualities are offered.</h3>
</div>
<p>We are currently dealing in the following key sectors:</p>
<div class="row g-3 my-3">
    <div class="col-md-6">
        <div class="p-3 bg-light rounded shadow-sm border-start border-4 border-primary">
            <h6 class="mb-1 font-weight-bold">Development of iron ore, coal, and copper mines</h6>
        </div>
    </div>
    <div class="col-md-6">
        <div class="p-3 bg-light rounded shadow-sm border-start border-4 border-primary">
            <h6 class="mb-1 font-weight-bold">Smelting and refining of aluminum</h6>
        </div>
    </div>
    <div class="col-md-6">
        <div class="p-3 bg-light rounded shadow-sm border-start border-4 border-primary">
            <h6 class="mb-1 font-weight-bold">Trading of iron ore, coal, ferroalloy, and ferrous raw materials</h6>
        </div>
    </div>
    <div class="col-md-6">
        <div class="p-3 bg-light rounded shadow-sm border-start border-4 border-primary">
            <h6 class="mb-1 font-weight-bold">Trading of non-ferrous metals, ingots, and related products</h6>
        </div>
    </div>
    <div class="col-md-6">
        <div class="p-3 bg-light rounded shadow-sm border-start border-4 border-primary">
            <h6 class="mb-1 font-weight-bold">Steel product trading and business investment</h6>
        </div>
    </div>
    <div class="col-md-6">
        <div class="p-3 bg-light rounded shadow-sm border-start border-4 border-primary">
            <h6 class="mb-1 font-weight-bold">Leasing of temporary steel construction materials</h6>
        </div>
    </div>
</div>
<h4 class="mt-4 text-primary font-weight-bold">MICA</h4>
<p>We supply natural mica blocks, mica flakes, and processed mica sheets utilized across electrical insulation, paints, cosmetics, and automotive industries with rigorous quality testing.</p>
<div class="my-4 text-center">
    <img src="/assets/images/mica.jpg" alt="Mica Mineral" class="img-fluid rounded shadow-sm border" style="max-height: 400px; object-fit: cover; width: 100%;" />
</div>
<div class="alert alert-primary bg-primary text-white p-4 rounded shadow-sm">
    <p class="mb-0 font-italic">"We, at Global Connect, candidly aspire to become one of the privileged global market leaders in mineral and metal trading and processing industry by means of our unswerving and channelized efforts towards operational excellence."</p>
</div>',
                'meta_title' => 'Minerals, Metals and Ores | Global Connect',
                'meta_description' => 'Trading in Iron Ore, Coal, Copper, Aluminum, Ferroalloys, Non-ferrous metals, Steel products and Mica by Global Connect.',
                'status' => 'published',
                'menu_order' => 4,
                'is_system' => 0
            ],
            // Trading Subpage: Petroleum and Lubricants
            [
                'category_id' => 2,
                'title' => 'Petroleum and Lubricants',
                'slug' => 'petroleum-and-lubricants',
                'subtitle' => 'Energy & Petroleum Products',
                'banner_title' => 'Petroleum and Lubricants',
                'banner_subtitle' => 'Energy Products from Leading Producing Nations',
                'featured_image' => '/assets/images/petroleum-lubricants.jpg',
                'summary' => 'We deal in many petroleum and lubricant products from leading producing nations around the world including Mazut M-100 and Gas Oil D-2.',
                'content' => '<div class="page-intro mb-4">
    <span class="badge bg-dark text-white px-3 py-2 rounded-pill font-weight-bold">Energy Resources</span>
    <h3 class="mt-3">We deal in many petroleum and lubricant products from leading producing nations around the world.</h3>
</div>
<div class="card p-4 border-0 shadow-sm bg-light mb-4">
    <h4 class="text-primary font-weight-bold">MAZUT M-100</h4>
    <p>Fuel oil mazut 100 is supplied according to technical specification GOST 10585/75, section for fuel oil, mazut sort 100. This kind of oil is graded as heavy furnace oil. The product is produced from the remains of raw oil processing and is produced only from low sulphur raw oil.</p>
    <div class="my-3 text-center">
        <img src="/assets/images/mazut-oil.jpg" alt="Mazut M-100" class="img-fluid rounded shadow-sm border" style="max-height: 380px; object-fit: cover; width: 100%;" />
    </div>
</div>
<div class="card p-4 border-0 shadow-sm bg-light mb-4">
    <h4 class="text-primary font-weight-bold">GAS OIL D-2</h4>
    <p>We supply Gas Oil D-2 (automotive and industrial diesel) meeting international sulfur, viscosity, and cetane index standards for shipping, transportation, and industrial power generation.</p>
    <div class="my-3 text-center">
        <img src="/assets/images/gas-oil.jpg" alt="Gas Oil D-2" class="img-fluid rounded shadow-sm border" style="max-height: 380px; object-fit: cover; width: 100%;" />
    </div>
</div>',
                'meta_title' => 'Petroleum and Lubricants | Global Connect',
                'meta_description' => 'Mazut M-100 fuel oil and Gas Oil D-2 diesel supply from top producing nations by Global Connect.',
                'status' => 'published',
                'menu_order' => 5,
                'is_system' => 0
            ],
            // Finance Overview
            [
                'category_id' => 3,
                'title' => 'Finance',
                'slug' => 'finance',
                'subtitle' => 'Our Finance Services',
                'banner_title' => 'Trade Finance & Banking Solutions',
                'banner_subtitle' => 'We are TradeFinanceSpecialists offering various services in the Finance Domain all around the world.',
                'featured_image' => '/assets/images/finance-home.jpg',
                'summary' => 'We are TradeFinanceSpecialists offering various services in the Finance Domain all around the world. We pride in our diverse range of offerings to match our customer needs and requirements.',
                'content' => '<p class="lead">We are TradeFinanceSpecialists offering various services in the Finance Domain all around the world. We pride in our diverse range of offerings to match our customer needs and requirements.</p>
<p>Global Connect has established direct strategic relationships with premier international financial institutions and Tier-1 partner banks. Our priority desk streamlines the processing and issuance of structured trade finance instruments, corporate banking facilities, and high-level cross-border advisory.</p>',
                'meta_title' => 'Finance Services | Global Connect',
                'meta_description' => 'Global Connect provides Trade Finance, Banking Support, SBLC, Letters of Credit, and Corporate Financial Advisory solutions.',
                'status' => 'published',
                'menu_order' => 4,
                'is_system' => 1
            ],
            // Finance Subpage: Trade Finance
            [
                'category_id' => 3,
                'title' => 'Trade Finance',
                'slug' => 'trade-finance',
                'subtitle' => 'What We Offer',
                'banner_title' => 'Trade Finance Solutions',
                'banner_subtitle' => 'Customised Trade Finance Instruments with Priority Bank Processing',
                'featured_image' => '/assets/images/trade-finance.jpg',
                'summary' => 'Global Connect has partnered with many banks as “TradeFinanceSpecialists". Along with our Partner Banks we provide customised solutions to our clientele with priority processing.',
                'content' => '<div class="page-intro mb-4">
    <span class="badge bg-primary text-white px-3 py-2 rounded-pill font-weight-bold">What We Offer</span>
    <h3 class="mt-3">Global Connect has partnered with many banks as “TradeFinanceSpecialists”. Along with our Partner Banks we provide customised solutions to our clientele. We have priority processing for our clients.</h3>
</div>
<div class="my-4 text-center">
    <img src="/assets/images/trade-finance.jpg" alt="Trade Finance Services" class="img-fluid rounded shadow-sm border" style="max-height: 420px; object-fit: cover; width: 100%;" />
</div>
<h4 class="mt-4 text-primary font-weight-bold">What Makes Us Unique</h4>
<p>Global Connect’s group provides solutions to Institutions and Corporate in managing and growing their business via high quality financial advisory services and capital market solutions.</p>
<div class="row g-4 mt-2">
    <div class="col-md-6">
        <div class="card h-100 p-4 border-0 shadow-sm bg-light">
            <h5 class="text-primary font-weight-bold">Standby Letter of Credit (SB L/C)</h5>
            <div class="my-2"><img src="/assets/images/standby-lc.jpg" alt="Standby LC" class="img-fluid rounded mb-2" style="max-height: 200px; width: 100%; object-fit: cover;" /></div>
            <p class="text-muted mb-0">A written undertaking given to a beneficiary (the party with whom you have entered into a contract with for the supply of goods) that in the event of your inability to meet the specific financial obligations stated in the contract, will satisfy that obligation on behalf of the beneficiary to the applicant, after which the beneficiary reimburses to the issuing bank/applicant for the payment made.</p>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card h-100 p-4 border-0 shadow-sm bg-light">
            <h5 class="text-primary font-weight-bold">Transferable Letter of Credit</h5>
            <div class="my-2"><img src="/assets/images/transferable-lc.jpg" alt="Transferable LC" class="img-fluid rounded mb-2" style="max-height: 200px; width: 100%; object-fit: cover;" /></div>
            <p class="text-muted mb-0">A Transferable Letter of Credit enables the transfer of payment to be made directly to the Supplier(s) if the Letter of Credit’s beneficiary is a middleman.</p>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card h-100 p-4 border-0 shadow-sm bg-light">
            <h5 class="text-primary font-weight-bold">Documentary Letter of Credit (L/C)</h5>
            <div class="my-2"><img src="/assets/images/documentary-lc.jpg" alt="Documentary LC" class="img-fluid rounded mb-2" style="max-height: 200px; width: 100%; object-fit: cover;" /></div>
            <p class="text-muted mb-0">This is an undertaking given to the supplier on behalf of the buyer that payment would be made for goods upon delivery within a stipulated time. An importer who uses the Letter of Credit reduces the risk of having to pay for goods in advance, thereby saving him the situation of having to pay for goods not consistent with the terms stated in the Letter of Credit.</p>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card h-100 p-4 border-0 shadow-sm bg-light">
            <h5 class="text-primary font-weight-bold">Back to Back Letter of Credit</h5>
            <div class="my-2"><img src="/assets/images/backtoback-lc.jpg" alt="Back to Back LC" class="img-fluid rounded mb-2" style="max-height: 200px; width: 100%; object-fit: cover;" /></div>
            <p class="text-muted mb-0">This is a new letter of credit opened based on an already existing, nontransferable credit used as collateral. A Back-to-Back Letter is used when the beneficiary (client) does not wish to divulge certain information deemed sensitive. A trader receives a letter of credit from the buyer and then opens another letter of credit in favour of the Supplier, thus, the first letter of credit serves as collateral for the second letter of credit.</p>
        </div>
    </div>
</div>',
                'meta_title' => 'Trade Finance | Global Connect',
                'meta_description' => 'Standby Letter of Credit (SBLC), Transferable LC, Documentary LC, and Back to Back LC provided by Global Connect.',
                'status' => 'published',
                'menu_order' => 1,
                'is_system' => 0
            ],
            // Finance Subpage: Banking Support
            [
                'category_id' => 3,
                'title' => 'Banking Support',
                'slug' => 'banking-support',
                'subtitle' => 'Banking Support Solutions',
                'banner_title' => 'Banking Support & Institutional Partnerships',
                'banner_subtitle' => 'Facilitating Trade Finance, Corporate Accounts, Cards & Wire Transfers',
                'featured_image' => '/assets/images/banking-support.jpg',
                'summary' => 'We offer Trade Finance, Corporate Banking, Prepaid Master Cards, and International Wire Transfers in association with our partner banks.',
                'content' => '<div class="page-intro mb-4">
    <span class="badge bg-primary text-white px-3 py-2 rounded-pill font-weight-bold">Banking Services</span>
    <h3 class="mt-3">Comprehensive banking facilities offered in strategic partnership with leading international partner banks.</h3>
</div>
<div class="my-4 text-center">
    <img src="/assets/images/banking-support.jpg" alt="Banking Support" class="img-fluid rounded shadow-sm border" style="max-height: 420px; object-fit: cover; width: 100%;" />
</div>
<div class="card p-4 border-0 shadow-sm bg-light mb-4">
    <h4 class="text-primary font-weight-bold">Trade Finance</h4>
    <p>We offer Trade Finance in association with our partner banks. With our experience and knowledge in trade finance industry we are ready to provide reliable and effective services for business transactions of our clients internationally. We offer very quick processing in issuance and relay of Letters of Credit [LC], Express Letters of Credit [ELC], Stand-By Letters of Credit [SBLC] and Bank Guarantees [BG]. We ensure that you spend minimum time on documentation.</p>
</div>
<div class="card p-4 border-0 shadow-sm bg-light mb-4">
    <h4 class="text-primary font-weight-bold">Corporate Banking</h4>
    <p>We offer Corporate Banking in association with our partner banks. This is designed to provide flexible and integrated savings solutions with ease of opening accounts from anywhere in the world. These can be operated as corporate savings accounts as well as personal savings accounts and access is available online 24×7. With adequate due diligence and security assurance, we provide our clients safe banking facilities.</p>
    <div class="row g-3 mt-2">
        <div class="col-md-6"><img src="/assets/images/corporate-banking.jpg" alt="Corporate Banking" class="img-fluid rounded shadow-sm border" style="max-height: 250px; width: 100%; object-fit: cover;" /></div>
        <div class="col-md-6"><img src="/assets/images/prepaid-card.jpg" alt="Business Card" class="img-fluid rounded shadow-sm border" style="max-height: 250px; width: 100%; object-fit: cover;" /></div>
    </div>
</div>
<div class="card p-4 border-0 shadow-sm bg-light mb-4">
    <h4 class="text-primary font-weight-bold">Pre-paid Master Cards</h4>
    <p class="mb-0">Pre-paid master cards offered through our partner banks providing a consistent payment platform for those who want the best. You can travel, shop, dine and indulge in countless products and services with total financial freedom and security.</p>
</div>
<div class="card p-4 border-0 shadow-sm bg-light mb-4">
    <h4 class="text-primary font-weight-bold">International Wire Transfers</h4>
    <p>International Wire Transfer offered through our partner banks provides wire transfer facilities to all its customers and non-customers. We process comprehensive due diligence, KYC, AML, PEP and sanctions checks to ensure maximum security of your transactions. Existing account holders of our Bank can transfer money or send instructions for incoming transfers through online banking facilities hassle-free.</p>
    <div class="my-3 text-center"><img src="/assets/images/wire-transfer.jpg" alt="International Wire Transfers" class="img-fluid rounded shadow-sm border" style="max-height: 300px; width: 100%; object-fit: cover;" /></div>
</div>',
                'meta_title' => 'Banking Support | Global Connect',
                'meta_description' => 'Trade Finance, Corporate Banking, Prepaid Cards and International Wire Transfers via Global Connect partner banks.',
                'status' => 'published',
                'menu_order' => 2,
                'is_system' => 0
            ],
            // Finance Subpage: Advisory
            [
                'category_id' => 3,
                'title' => 'Advisory',
                'slug' => 'advisory',
                'subtitle' => 'What We Offer',
                'banner_title' => 'International Trade & Business Advisory',
                'banner_subtitle' => 'High Quality Financial Advisory Services and Capital Market Solutions',
                'featured_image' => '/assets/images/advisory.jpg',
                'summary' => 'The Global Connect’s group provides advisory solutions to Institutions and Corporates in managing and growing their business via high quality Financial Advisory services and capital market solutions.',
                'content' => '<div class="page-intro mb-4">
    <span class="badge bg-primary text-white px-3 py-2 rounded-pill font-weight-bold">What We Offer</span>
    <h3 class="mt-3">The Global Connect’s group provides advisory solutions to Institutions and Corporates in managing and growing their business via high quality Financial Advisory services and capital market solutions.</h3>
</div>
<div class="my-4 text-center">
    <img src="/assets/images/advisory.jpg" alt="Advisory Services" class="img-fluid rounded shadow-sm border" style="max-height: 420px; object-fit: cover; width: 100%;" />
</div>
<div class="card p-4 border-0 shadow-sm bg-light mb-4">
    <h4 class="text-primary font-weight-bold">International Trade Consulting</h4>
    <p class="mb-0">In today’s fierce business environment, companies considering sourcing or selling their products overseas must evaluate many more options than in the past and process greater amounts of information in order to make decisions. And cultural barriers and complex compliance initiatives make global trade even more of a challenge. Our experience and knowledge helps mitigate the risks of our customers, by developing a solid understanding of the international trade issues that may impact their specific business.</p>
</div>
<div class="card p-4 border-0 shadow-sm bg-light mb-4">
    <h4 class="text-primary font-weight-bold">Sourcing and Contract Manufacturing</h4>
    <p>We assist our customers in sourcing products from any part of the world, depending on their requirements and accurate product specifications. With excellent networks with Chinese Suppliers and Manufacturers, we even provide support in contract manufacturing products.</p>
    <div class="my-2"><img src="/assets/images/contract-manufacturing.jpg" alt="Sourcing & Manufacturing" class="img-fluid rounded shadow-sm border" style="max-height: 300px; width: 100%; object-fit: cover;" /></div>
</div>
<div class="card p-4 border-0 shadow-sm bg-light mb-4">
    <h4 class="text-primary font-weight-bold">Logistical Support</h4>
    <p>With the support of our logistical partners globally, we can provide logistical support for our customers. Our support can extend to warehousing of the products, based on specific tariffs and charges.</p>
    <div class="my-2"><img src="/assets/images/logistics.jpg" alt="Logistical Support" class="img-fluid rounded shadow-sm border" style="max-height: 300px; width: 100%; object-fit: cover;" /></div>
</div>
<div class="card p-4 border-0 shadow-sm bg-light mb-4">
    <h4 class="text-primary font-weight-bold">Shipping Assistance</h4>
    <p>We can provide shipping assistance to our customers if required in special cases. Our charges will be based on the expenses and handling fees, as well as our consulting commissions. We can help in documentation, shipment and freight forwarding.</p>
    <div class="my-2"><img src="/assets/images/shipping.jpg" alt="Shipping Assistance" class="img-fluid rounded shadow-sm border" style="max-height: 300px; width: 100%; object-fit: cover;" /></div>
</div>
<div class="card p-4 border-0 shadow-sm bg-light mb-4">
    <h4 class="text-primary font-weight-bold">Negotiations</h4>
    <p>In most cases of dealing with manufacturers, a certain level of experience and negotiation skill becomes imperative for businesses. We can assist our customers in direct negotiations with manufacturers to ensure a favourable outcome.</p>
    <div class="my-2"><img src="/assets/images/negotiation.jpg" alt="Negotiation Support" class="img-fluid rounded shadow-sm border" style="max-height: 300px; width: 100%; object-fit: cover;" /></div>
</div>',
                'meta_title' => 'Advisory Services | Global Connect',
                'meta_description' => 'International trade consulting, sourcing, contract manufacturing, logistics, shipping assistance and negotiation advisory by Global Connect.',
                'status' => 'published',
                'menu_order' => 3,
                'is_system' => 0
            ],
            // Consulting Overview & Detail
            [
                'category_id' => 4,
                'title' => 'Consulting',
                'slug' => 'consulting',
                'subtitle' => 'We Specialize in',
                'banner_title' => 'Corporate Financial Consulting & Capital Markets',
                'banner_subtitle' => 'Global Connect’s group provides solutions to Institutions and Corporate in managing and growing their business via high quality financial advisory services and capital market solutions',
                'featured_image' => '/assets/images/consulting-home.jpg',
                'summary' => 'Global Connect’s group provides solutions to Institutions and Corporate in managing and growing their business via high quality financial advisory services and capital market solutions.',
                'content' => '<div class="page-intro mb-4">
    <span class="badge bg-primary text-white px-3 py-2 rounded-pill font-weight-bold">We Specialize In</span>
    <h3 class="mt-3">Global Connect’s group provides solutions to Institutions and Corporate in managing and growing their business via high quality financial advisory services and capital market solutions</h3>
</div>
<div class="my-4 text-center">
    <img src="/assets/images/consulting-home.jpg" alt="Consulting Services" class="img-fluid rounded shadow-sm border" style="max-height: 420px; object-fit: cover; width: 100%;" />
</div>
<div class="card p-4 border-0 shadow-sm bg-light mb-4">
    <h4 class="text-primary font-weight-bold">Financial Structuring</h4>
    <p class="mb-0">Due to our vast experience in various business understanding and financial needs, we advise our clients in financial structuring of working capital, term loans and trade finance. We believe it’s more important to structure borrowing pattern and using it efficiently, which mainly is obtained by its rate of interest and impact on balance sheet.</p>
</div>
<div class="card p-4 border-0 shadow-sm bg-light mb-4">
    <h4 class="text-primary font-weight-bold">International Trade Finance</h4>
    <p class="mb-0">International Trade Finance and Structure Finance is one of the core expertise of Global Connect’s finance. We have good understanding of International Trade and its finance requirements. Our trade finance desk CMA does forfeiting, factoring, discounting, open account funding, recourse funding and non recourse funding.</p>
</div>
<div class="card p-4 border-0 shadow-sm bg-light mb-4">
    <h4 class="text-primary font-weight-bold">Merger and Acquisition</h4>
    <p class="mb-0">Global Connect’s have a separate team for Merger and Acquisition who have experience and connections worldwide to get good opportunities for our Clients. We got strong due diligence team on legal, financial and technological aspects which assures to bring Best Opportunities always.</p>
</div>
<div class="card p-4 border-0 shadow-sm bg-light mb-4">
    <h4 class="text-primary font-weight-bold">Off Shore Fund Raising</h4>
    <p class="mb-0">Global Connect’s has good presence globally, It also has strong banking relationships which makes it capable of Raising debt and Equity for our Clients in India. The Prime benefit of Offshore Debts is the competitive cost of borrowing and availability.</p>
</div>
<div class="card p-4 border-0 shadow-sm bg-light mb-4">
    <h4 class="text-primary font-weight-bold">Unlocking Values</h4>
    <p class="mb-0">Global Connect’s believe in unlocking the values of companies, With our rich experience and business expertise, We judge the potential of companies and give them a long term plan, to take them to the next level. We help companies to execute their business plan by not only giving them financial support but also by giving market and technical collaboration. We try to create more value for the company by bringing new technology patterns and marketing patterns. By strengthening the company we are able to attract investors from India and overseas. Generally we are also prepared to list the company in Indian Stock Exchange such as BSE, NSE and overseas stock like Singapore exchange etc. to get Public participation as equity.</p>
</div>
<div class="card p-4 border-0 shadow-sm bg-light mb-4">
    <h4 class="text-primary font-weight-bold">Debt Syndication</h4>
    <p class="mb-0">Debt syndication is one of the core activities of Global Connect’s group. We have a full fledged in house team of Chartered Accountants, MBAs with rich experience who structure and represent a proposal on behalf of their clients to banks. Apart from finance wizards we also have Business experts like Engineers and Management Graduates with great market intelligence to commercially evaluate value add on technovibility of a project. Our expertise is to create unique structures with effective cost of funds and flexible repayment schedules using Indian and offshore banking systems.</p>
</div>',
                'meta_title' => 'Consulting | Global Connect',
                'meta_description' => 'Financial Structuring, Trade Finance, M&A, Offshore Fund Raising, Unlocking Values & Debt Syndication consulting by Global Connect.',
                'status' => 'published',
                'menu_order' => 5,
                'is_system' => 1
            ],
            // Contact
            [
                'category_id' => 1,
                'title' => 'Contact Us',
                'slug' => 'contact',
                'subtitle' => 'Get in Touch',
                'banner_title' => 'Contact Global Connect',
                'banner_subtitle' => 'Connect with our Singapore Global HQ & India Offices',
                'featured_image' => '',
                'summary' => 'Thank you for learning more about our company. We care about your questions and feedback.',
                'content' => '',
                'meta_title' => 'Contact Us | Global Connect',
                'meta_description' => 'Contact Global Connect Singapore Global Headquarters, Bangalore India HQ, Indonesia & Hong Kong offices.',
                'status' => 'published',
                'menu_order' => 6,
                'is_system' => 1
            ]
        ];

        $stmt = $db->prepare("
            INSERT OR REPLACE INTO pages 
            (category_id, title, slug, subtitle, banner_title, banner_subtitle, featured_image, summary, content, meta_title, meta_description, meta_keywords, status, menu_order, is_system) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");

        foreach ($pages as $p) {
            $stmt->execute([
                $p['category_id'],
                $p['title'],
                $p['slug'],
                $p['subtitle'] ?? '',
                $p['banner_title'] ?? '',
                $p['banner_subtitle'] ?? '',
                $p['featured_image'] ?? '',
                $p['summary'] ?? '',
                $p['content'] ?? '',
                $p['meta_title'] ?? '',
                $p['meta_description'] ?? '',
                $p['meta_keywords'] ?? '',
                $p['status'] ?? 'published',
                $p['menu_order'] ?? 0,
                $p['is_system'] ?? 0
            ]);
        }
    }
}
