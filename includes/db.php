<?php
/**
 * Global Connect - MySQL Database Handler
 * Standard MySQL PDO Connection with automated preview resilience
 * Pre-populated with authentic content from https://site.globalconnectsg.com/mysite/
 */

class Database {
    private static ?PDO $instance = null;
    private static string $driver = 'mysql';

    public static function getConnection(): PDO {
        if (self::$instance === null) {
            $config = require __DIR__ . '/../config/database.php';
            $c = $config['mysql'] ?? [];
            $host = $c['host'] ?? '127.0.0.1';
            $port = $c['port'] ?? 3306;
            $dbname = $c['database'] ?? 'globalconnect_db';
            $user = $c['username'] ?? 'root';
            $pass = $c['password'] ?? '';
            $charset = $c['charset'] ?? 'utf8mb4';

            try {
                // Primary: Connect to MySQL
                $dsn = "mysql:host={$host};port={$port};dbname={$dbname};charset={$charset}";
                self::$instance = new PDO($dsn, $user, $pass, $c['options'] ?? [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ]);
                self::$driver = 'mysql';
                self::ensureSchema(self::$instance);
            } catch (PDOException $e) {
                // If database doesn't exist on MySQL server, attempt auto-creation
                if ($e->getCode() == 1049 || strpos($e->getMessage(), 'Unknown database') !== false) {
                    try {
                        $serverDsn = "mysql:host={$host};port={$port};charset={$charset}";
                        $serverPdo = new PDO($serverDsn, $user, $pass, $c['options'] ?? []);
                        $serverPdo->exec("CREATE DATABASE IF NOT EXISTS `{$dbname}` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
                        
                        $dsn = "mysql:host={$host};port={$port};dbname={$dbname};charset={$charset}";
                        self::$instance = new PDO($dsn, $user, $pass, $c['options'] ?? []);
                        self::$driver = 'mysql';
                        self::ensureSchema(self::$instance);
                        return self::$instance;
                    } catch (PDOException $ex) {
                        // Fall through to preview fallback
                    }
                }

                // Resilience Fallback: If no MySQL daemon is running in local environment/preview runner,
                // initialize temporary SQLite database seeded from default dataset
                try {
                    $tempDbPath = sys_get_temp_dir() . '/globalconnect_temp.db';
                    self::$instance = new PDO("sqlite:" . $tempDbPath, null, null, [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    ]);
                    self::$driver = 'sqlite';
                    self::initFallbackSchema(self::$instance);
                } catch (Exception $fallbackEx) {
                    die("<div style='font-family:sans-serif;padding:30px;background:#fef2f2;border:1px solid #fecaca;color:#991b1b;border-radius:8px;'><h2>Database Connection Error</h2><p>" . htmlspecialchars($e->getMessage()) . "</p><p>Please check MySQL credentials in <code>config/database.php</code> or import <code>database.sql</code>.</p></div>");
                }
            }
        }

        return self::$instance;
    }

    public static function getDriver(): string {
        return self::$driver;
    }

    private static function ensureSchema(PDO $db): void {
        try {
            $stmt = $db->query("SHOW TABLES LIKE 'pages'");
            $exists = $stmt->fetch();
            if (!$exists) {
                $sqlFile = __DIR__ . '/../database.sql';
                if (file_exists($sqlFile)) {
                    $sql = file_get_contents($sqlFile);
                    $db->exec($sql);
                }
            } else {
                // Ensure executives table exists if database was created earlier
                $stmt = $db->query("SHOW TABLES LIKE 'executives'");
                if (!$stmt->fetch()) {
                    $db->exec("
                        CREATE TABLE IF NOT EXISTS `executives` (
                          `id` INT AUTO_INCREMENT PRIMARY KEY,
                          `name` VARCHAR(150) NOT NULL,
                          `role` VARCHAR(150) NOT NULL,
                          `email` VARCHAR(150) NOT NULL,
                          `phone` VARCHAR(50) DEFAULT '',
                          `location` VARCHAR(150) DEFAULT '',
                          `bio` TEXT NULL,
                          `avatar` VARCHAR(255) DEFAULT '',
                          `sort_order` INT DEFAULT 0,
                          `status` ENUM('active', 'inactive') DEFAULT 'active',
                          `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
                          `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
                        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
                    ");
                    $executives = [
                        [1, 'Mr Shammy Songra', 'Founder / Director', 'shammy@globalconnectsg.com', '(65) 89451100', 'Singapore / India', '', 1, 'active'],
                        [2, 'Mr Cliff Poh', 'Director – Singapore', 'cliffpoh@globalconnectsg.com', '(65) 89451100', 'Singapore HQ', '', 2, 'active'],
                        [3, 'Mr. Lima Hutama Halim', 'Director – Indonesia Business', 'limahalim@globalconnectsg.com', '', 'Jakarta, Indonesia', '', 3, 'active'],
                        [4, 'Mr. Kapil Gulati', 'Director – International Business & Financial Advisory', 'kapil@globalconnectsg.com', '', 'Global Advisory Desk', '', 4, 'active'],
                        [5, 'Mr Sandeep Pratap Sanap', 'Associate Director & Partner – India & International Business', 'sandeep@globalconnectsg.com', '(91) 99100 02299', 'India Corporate Office', '', 5, 'active'],
                        [6, 'Mr. Siddharth Maheshwari', 'Director – Business Development', 'siddharth@globalconnectsg.com', '', 'Business Development Desk', '', 6, 'active'],
                        [7, 'Mr. Amit Gupta', 'Director – Business Development', 'amit@globalconnectsg.com', '', 'Business Development Desk', '', 7, 'active']
                    ];
                    $eStmt = $db->prepare("INSERT INTO `executives` (`id`, `name`, `role`, `email`, `phone`, `location`, `avatar`, `sort_order`, `status`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
                    foreach ($executives as $ex) {
                        $eStmt->execute($ex);
                    }
                }
            }
        } catch (Exception $e) {
            error_log("Schema ensure error: " . $e->getMessage());
        }
    }

    private static function initFallbackSchema(PDO $db): void {
        // Create tables if not existing
        $db->exec("
            CREATE TABLE IF NOT EXISTS users (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                username TEXT UNIQUE NOT NULL,
                password TEXT NOT NULL,
                email TEXT UNIQUE NOT NULL,
                full_name TEXT DEFAULT 'Administrator',
                role TEXT DEFAULT 'admin',
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
            );

            CREATE TABLE IF NOT EXISTS categories (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                name TEXT NOT NULL,
                slug TEXT UNIQUE NOT NULL,
                description TEXT,
                menu_order INTEGER DEFAULT 0,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP
            );

            CREATE TABLE IF NOT EXISTS pages (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                category_id INTEGER,
                title TEXT NOT NULL,
                slug TEXT UNIQUE NOT NULL,
                subtitle TEXT DEFAULT '',
                banner_title TEXT DEFAULT '',
                banner_subtitle TEXT DEFAULT '',
                featured_image TEXT DEFAULT '',
                icon TEXT DEFAULT '',
                summary TEXT,
                content TEXT,
                meta_title TEXT DEFAULT '',
                meta_description TEXT,
                meta_keywords TEXT DEFAULT '',
                status TEXT DEFAULT 'published',
                menu_order INTEGER DEFAULT 0,
                is_system INTEGER DEFAULT 0,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
            );

            CREATE TABLE IF NOT EXISTS hero_slides (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                title TEXT,
                subtitle TEXT,
                badge_text TEXT,
                image_path TEXT NOT NULL,
                button_text TEXT,
                button_link TEXT,
                secondary_button_text TEXT,
                secondary_button_link TEXT,
                show_text_overlay INTEGER DEFAULT 0,
                status TEXT DEFAULT 'active',
                sort_order INTEGER DEFAULT 0,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
            );

            CREATE TABLE IF NOT EXISTS executives (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                name TEXT NOT NULL,
                role TEXT NOT NULL,
                email TEXT NOT NULL,
                phone TEXT DEFAULT '',
                location TEXT DEFAULT '',
                bio TEXT,
                avatar TEXT DEFAULT '',
                sort_order INTEGER DEFAULT 0,
                status TEXT DEFAULT 'active',
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
            );

            CREATE TABLE IF NOT EXISTS settings (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                setting_key TEXT UNIQUE NOT NULL,
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

        // Seed Admin user
        $adminPass = password_hash('admin123', PASSWORD_BCRYPT);
        $stmt = $db->prepare("INSERT OR IGNORE INTO users (id, username, password, email, full_name, role) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([1, 'admin', $adminPass, 'admin@globalconnectsg.com', 'Administrator', 'admin']);

        // Seed Categories
        $cats = [
            [1, 'General', 'general', 'Core company pages and overviews', 1],
            [2, 'Trading', 'trading', 'Commodities, Minerals, Agro & Energy Trading', 2],
            [3, 'Finance', 'finance', 'Trade Finance, Banking Support & Advisory Solutions', 3],
            [4, 'Consulting', 'consulting', 'Corporate Financial & Advisory Consulting', 4]
        ];
        $catStmt = $db->prepare("INSERT OR IGNORE INTO categories (id, name, slug, description, menu_order) VALUES (?, ?, ?, ?, ?)");
        foreach ($cats as $c) {
            $catStmt->execute($c);
        }

        // Seed Hero Slides if empty
        $slideCount = $db->query("SELECT COUNT(*) FROM hero_slides")->fetchColumn();
        if ($slideCount == 0) {
            $slideStmt = $db->prepare("
                INSERT INTO hero_slides (id, title, subtitle, badge_text, image_path, button_text, button_link, secondary_button_text, secondary_button_link, show_text_overlay, status, sort_order)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $slideStmt->execute([
                1,
                'Global Connect Group of Companies',
                'Singapore • India • Indonesia • Hongkong | Sourcing, Supply Chain & Financial Structuring',
                'Singapore • India • Global Network',
                'assets/images/global-connect-banner-1.png',
                'Explore Trading',
                'trading.php',
                'Trade Finance',
                'finance.php',
                0, // Clean mode (no text overlay on image)
                'active',
                1
            ]);
            $slideStmt->execute([
                2,
                'Your Strategic Partner in Sourcing & Supply Chain',
                'We are a global business enterprise headquartered in Singapore and India. At Global Connect, we believe in building meaningful partnerships that drive success across international borders.',
                'Singapore • India • Global Network',
                'assets/images/banner-hero-1.png',
                'Explore Trading',
                'trading.php',
                'Trade Finance',
                'finance.php',
                1, // With text overlay
                'active',
                2
            ]);
        }

        // Seed Executives if empty
        $execCount = $db->query("SELECT COUNT(*) FROM executives")->fetchColumn();
        if ($execCount == 0) {
            $execs = [
                [1, 'Mr Shammy Songra', 'Founder / Director', 'shammy@globalconnectsg.com', '(65) 89451100', 'Singapore / India', '', 1, 'active'],
                [2, 'Mr Cliff Poh', 'Director – Singapore', 'cliffpoh@globalconnectsg.com', '(65) 89451100', 'Singapore HQ', '', 2, 'active'],
                [3, 'Mr. Lima Hutama Halim', 'Director – Indonesia Business', 'limahalim@globalconnectsg.com', '', 'Jakarta, Indonesia', '', 3, 'active'],
                [4, 'Mr. Kapil Gulati', 'Director – International Business & Financial Advisory', 'kapil@globalconnectsg.com', '', 'Global Advisory Desk', '', 4, 'active'],
                [5, 'Mr Sandeep Pratap Sanap', 'Associate Director & Partner – India & International Business', 'sandeep@globalconnectsg.com', '(91) 99100 02299', 'India Corporate Office', '', 5, 'active'],
                [6, 'Mr. Siddharth Maheshwari', 'Director – Business Development', 'siddharth@globalconnectsg.com', '', 'Business Development Desk', '', 6, 'active'],
                [7, 'Mr. Amit Gupta', 'Director – Business Development', 'amit@globalconnectsg.com', '', 'Business Development Desk', '', 7, 'active']
            ];
            $eStmt = $db->prepare("INSERT INTO executives (id, name, role, email, phone, location, avatar, sort_order, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            foreach ($execs as $ex) {
                $eStmt->execute($ex);
            }
        }

        // Seed default settings
        $settings = [
            ['site_title', 'Global Connect | Global Business Enterprise Headquartered in Singapore & India'],
            ['company_name', 'Global Connect'],
            ['site_tagline', 'We are a global business enterprise headquartered in Singapore And India'],
            ['site_logo', 'logo-design-gcv.png'],
            ['logo_title_p1', 'GLOBAL'],
            ['logo_title_p2', 'CONNECT'],
            ['logo_tagline', 'Ventures • Singapore • India'],
            ['site_favicon', 'logo-design-gcv.png'],
            ['header_phone_sg', '(65) 89451100'],
            ['header_phone_roam', '(91) 99100 02299'],
            ['fax_number', '(65) 98260001 / (91) 99100 02299'],
            ['contact_email_primary', 'admin@globalconnectsg.com'],
            ['contact_email_secondary', 'shammy@globalconnectsg.com'],
            ['contact_email_tertiary', 'tsnshammy@gmail.com'],
            ['address_sg', '101 KITCHENER ROAD #03-43 JALAN BESAR PLAZA SINGAPORE (208511)'],
            ['address_india', 'NO. 211 RAJNIGANDHA BLOCK, GARDEN APARTMENT, 21 VITTHAL MALLYA ROAD (opp. UB CITY) BANGLORE 560001 INDIA'],
            ['address_branches', 'New Delhi, Mumbai, Hyderabad and Chennai (By Appointment ONLY – IN HYATT GROUP OF HOTELS)'],
            ['address_indonesia', 'HOTEL GRAND HYATT PLAZA, JL. M.H. THAMRIN KAV. 28-30, GONDANGDIA, MENTENG, JAKARTA, 10350, INDONESIA'],
            ['address_hongkong', 'HOTEL HYATT REGENCY 18 HANOI ROAD TSIM SHA TSU KOWLOON HONGKONG'],
            ['stat_partners_num', '3,600+'],
            ['stat_partners_label', 'Associate Partners'],
            ['stat_countries_num', '19+'],
            ['stat_countries_label', 'Countries Globally'],
            ['stat_banks_num', '19+'],
            ['stat_banks_label', 'Partner Banks'],
            ['stat_experience_num', '25+'],
            ['stat_experience_label', 'Years Cumulative Experience'],
            ['home_exp_subtitle', 'Company Experience'],
            ['home_exp_title', 'Global Connect’s Believe in Growth Strategies'],
            ['home_exp_lead', 'The organisation involves various teams to handle client needs which start from Capital Raising, Project Finance, Financial Engineering or Structuring, and Fund Raising programs to name a few.'],
            ['home_exp_body', 'Fund raising is our daily work and we are always available to support your short term & long term needs. Global Connect involves itself with clients in achieving results. May it start with determining the most suitable structure to reach the end result. Global Connect is looking forward to participate with clients to translate their visions to reality.'],
            ['home_exp_image', 'about-growth.jpg'],
            ['home_services_subtitle', 'Our Capabilities'],
            ['home_services_title', 'Your reliable partner For international trade services'],
            ['home_services_lead', 'Delivering excellence across international commodity trading, custom trade banking solutions, and corporate financial advisory.'],
            ['home_singapore_subtitle', 'Global Connectivity'],
            ['home_singapore_title', 'Singapore – The World’s Premier Financial & Trade Hub'],
            ['home_singapore_lead', 'Strategically headquartered in Singapore with direct operational corridors into India, Southeast Asia, and global markets.'],
            ['home_cta_title', 'Ready to Expand Your Global Trade & Capital Reach?'],
            ['home_cta_lead', 'Partner with Global Connect for seamless sourcing, commodity allocations, and structured trade finance solutions tailored to your business needs.'],
            ['home_cta_btn1_text', 'Schedule an Executive Consultation'],
            ['home_cta_btn1_link', 'contact.php'],
            ['home_cta_btn2_text', 'Explore Product Portfolio'],
            ['home_cta_btn2_link', 'trading.php'],
            ['footer_about', 'At Global Connect, we believe in building meaningful partnerships that drive success across borders. With our expertise and network, we connect businesses to new markets and lasting opportunities.'],
            ['footer_copyright', '© 2026 Global Connect. All rights reserved. | Sourcing & Supply Chain Strategic Partner.']
        ];

        $setStmt = $db->prepare("INSERT OR IGNORE INTO settings (setting_key, setting_value) VALUES (?, ?)");
        foreach ($settings as $s) {
            $setStmt->execute($s);
        }

        // Seed 8 authentic pages from Global Connect SG
        $pages = [
            [
                1, 2, 'Gold Bullion', 'gold-bullion', 'Gold Bar and Mining - Investment Grade Precious Metals', 'Gold Bullion', 'Gold Bar and Mining Sourcing & Refinery Allocations', 'gold-bullion.jpg', 'Active physical trading in Gold Bullion Bars and Coins with international refinery assay standards and daily capacity up to 200kg.',
                '<h5>Gold Bar and Mining</h5><h2>Of all the precious metals, Gold has been the most popular as an investment. Gold bullion comes in two forms namely Gold Bullion Bars and Bullion Coins. Global Connect, has been actively trading in Gold Bullion for many years now.</h2><p>We buy and sell Gold Bullion Bars from multiple countries. Bullion bars come in many sizes ranging from a few grams to 5 kilo gram bars. We buy Bullion bars of sizes 10 grams to 500 grams, 1 Kilo Gram, 2 Kilo Gram and 5 Kilo Gram, of purity 9995 and up. We can buy 200 Kilo grams worth of Bullion daily. Our payments would be made after receiving the Assay reports from the respective refineries. Interested sellers can send their quotes on the basis of deliveries globally.</p><p><img src="/assets/images/gold-bullion-2.jpg" alt="Gold Bullion Bars" class="img-fluid" /></p><p>Our suppliers should ensure that their gold is assayed in the following refinery globally, for testing the purity of precious metals prior to our payment.</p><p>Before you make your gold deliveries to the mentioned refinery, kindly take a prior consent from us. In addition, all deliveries to the refinery should be made in our company name account of <strong>&ldquo;GLOBAL CONNECT&rdquo;</strong>, and kindly fax the delivery receipt to us at <strong>(65) 98260001 / (91) 99100 02299</strong> or Email us at <strong>shammy@globalconnectsg.com</strong> for necessary action.</p><p>Country of origin and a declaration that goods are not of criminal origin by the owner is required while shipping the goods to our above refinery under our company name. Gold delivery done to our refinery will be subject to your prior acceptance of our terms and our written consent for delivery to our refinery. If any delivery of precious metal is done without our consent, it will be rejected by us and our refinery. Refinery does the assaying of precious metals as follows:</p><h3>Assaying &amp; Returnable Rate Standards</h3><ul><li><strong>Gold Bars / Major SCRAP 99% Plus:</strong> Returnable Rate: Gold 100% on assay from after melt weight.</li><li><strong>Gold Bars / Major SCRAP 97% - 98.99% Plus:</strong> Returnable Rate: Gold 100% on assay from after melt weight.</li><li><strong>Gold Bars / Major SCRAP 50% - 96.99% Plus with Silver:</strong><ul><li>Returnable Rate: Gold 99.8% on assay</li><li>Silver &gt; 0.5% &amp; &lt; 20%: 98% on assay</li><li>Platinum &gt; 0.05% &amp; &lt; 15%: 95% on assay (Refining charges for US$35.00/oz on recovery)</li><li>Palladium &gt; 0.05% &amp; &lt; 15%: 95% on assay (Refining charges for US$35.00/oz on recovery)</li></ul></li></ul>',
                1
            ],
            [
                2, 2, 'Sugar', 'sugar', 'ICUMSA 45 Refined White Cane Sugar & Raw Sugar', 'Sugar Trading', 'High Volume Global Sugar Allocations from Certified Mills', 'sugar-trade.jpg', 'Direct allocation and bulk shipment of refined ICUMSA 45 white cane sugar and raw sugar from premier Brazilian and Indian sugar mills.',
                '<h2>Global Refined White Sugar &amp; Raw Cane Sugar</h2><p>Global Connect facilitates the international trade and vessel-level allocation of refined white sugar (ICUMSA 45 RBU) and raw cane sugar. With deep contractual ties to premier sugar mills and refinery syndicates in Brazil, India, and Thailand, we ensure stringent quality control and on-schedule maritime dispatch.</p><h3>Product Specifications</h3><ul><li><strong>Commodity:</strong> Refined White Cane Sugar (ICUMSA 45 RBU)</li><li><strong>Polarization:</strong> 99.80% Minimum at 20&deg;C</li><li><strong>Moisture Content:</strong> 0.04% Maximum</li><li><strong>Ash Content:</strong> 0.04% Maximum by Electrical Conductivity</li><li><strong>Solubility:</strong> 100% Dry and Free Flowing</li><li><strong>Granulation:</strong> Fine / Medium Standard Crystal</li><li><strong>Color:</strong> Sparkling White</li><li><strong>Radiation:</strong> Normal without presence of Cesium or Iodine (Certified)</li><li><strong>Crop Year:</strong> Current Production</li></ul><h3>Shipping &amp; Vessel Logistics</h3><p>We supply in 50kg poly-lined PP export bags or bulk vessel charters (Handymax, Supramax 12,500 MT to 50,000 MT per shipment) with 100% SGS / Bureau Veritas inspection at loading port.</p>',
                2
            ],
            [
                3, 2, 'Agro Products', 'agro-products', 'Agricultural Commodities, Rice, Grains, Pulses & Edible Oils', 'Agro Products', 'Sourcing Farm & Agricultural Commodities Worldwide', 'agro-products.jpg', 'Comprehensive agro-commodity trade encompassing Basmati rice, non-Basmati white rice, wheat, corn, pulses, and refined edible vegetable oils.',
                '<h2>Agricultural Food Commodities &amp; Grains</h2><p>At Global Connect, our agricultural trading desk sources high-yield crops, staples, and edible vegetable oils directly from verified farming cooperatives, processing hubs, and port elevators across India, Southeast Asia, and South America.</p><h3>Product Portfolio</h3><ul><li><strong>Premium Rice Varieties:</strong> Traditional 1121 Basmati Rice (Raw, Steam, Sella), IR64 Non-Basmati Parboiled Rice, and 5% / 25% Broken White Rice.</li><li><strong>Edible Oils:</strong> Refined Bleached Deodorized (RBD) Palm Olein (CP8 / CP10), Crude Palm Oil (CPO), and Pure Sunflower Oil.</li><li><strong>Grains &amp; Pulses:</strong> Milling Wheat, Yellow Corn (Maize), Soybeans, Chickpeas, and Lentils.</li><li><strong>Spices:</strong> Whole Black Pepper, Red Chili, Turmeric Finger, and Cumin.</li></ul><h3>Export Quality Assurance</h3><p>All agricultural shipments adhere strictly to phytosanitary regulations, fumigation protocols, non-GMO certifications, and SGS weight and quality inspections.</p>',
                3
            ],
            [
                4, 2, 'Minerals Metals and Ores', 'minerals-metals-and-ores', 'Refined Minerals, Ferroalloys, Non-Ferrous Metals & MICA', 'Minerals Metals and Ores', 'Refined and Reliable Qualities Across Global Mineral Corridors', 'minerals-metals.jpg', 'Dealing in iron ore, coal, copper mine allocations, aluminum smelting, ferroalloys, steel products, and industrial MICA.',
                '<h2>Minerals, Metals and Ores Portfolio</h2><p>We have been dealing in minerals, metals and ores for many years now, and have built our expertise to ensure that only refined and reliable qualities are offered. We are currently dealing in the following:</p><ul><li><strong>Development of iron ore, coal, and copper mines</strong></li><li><strong>Smelting and refining of aluminum</strong></li><li><strong>Trading of iron ore, coal, ferroalloy, and ferrous raw materials</strong></li><li><strong>Trading of non-ferrous metals, ingots, and related products</strong></li><li><strong>Steel product trading and business investment</strong></li><li><strong>Leasing of temporary steel construction materials</strong></li><li><strong>MICA processing and export</strong></li></ul><p><img src="/assets/images/mica.jpg" alt="Industrial MICA Products" class="img-fluid" /></p><p>We, at Global Connect, candidly aspire to become one of the privileged global market leaders in mineral and metal trading and processing industry by means of our unswerving and channelized efforts towards operational excellence.</p>',
                4
            ],
            [
                5, 2, 'Petroleum and Lubricants', 'petroleum-and-lubricants', 'MAZUT M-100 Heavy Furnace Fuel Oil & GAS OIL D-2', 'Petroleum and Lubricants', 'Refined Petroleum Distillates from Leading Producing Nations', 'gas-oil.jpg', 'Direct supply contracts for Russian GOST specification Mazut M-100 heavy furnace oil and high-grade Gas Oil D-2.',
                '<h2>Petroleum and Lubricant Products</h2><p>We deal in many petroleum and lubricant products from leading producing nations around the world.</p><h3>MAZUT M-100</h3><p>Fuel oil mazut 100 is supplied according to the Russian technical specification GOST 10585/75, section for fuel oil, mazut sort 100. This kind of oil is graded as heavy furnace oil. The product is produced from the remains of raw oil processing. This kind of mazut is produced only from low sulphur raw oil. Origin country for this kind of mazut is only Russia.</p><p><img src="/assets/images/mazut-oil.jpg" alt="Mazut M-100 Fuel Oil" class="img-fluid" /></p><h3>GAS OIL D-2</h3><p>Gas Oil D-2 is a premium automotive and industrial diesel distillate with low sulfur content, high cetane rating, and optimal combustion efficiency for marine vessels, industrial turbines, and commercial transport fleets.</p><p><img src="/assets/images/petroleum-lubricants.jpg" alt="Gas Oil D-2 Petroleum Distillate" class="img-fluid" /></p>',
                5
            ],
            [
                6, 3, 'Trade Finance', 'trade-finance', 'Letters of Credit: Standby LC, Transferable LC, DLC & Back to Back LC', 'Trade Finance Solutions', 'Customized Trade Finance Instruments with Priority Bank Processing', 'trade-finance.jpg', 'Partnered with premier international banks as Trade Finance Specialists to issue MT700 DLC, MT760 SBLC, Transferable LC, and Back to Back LC.',
                '<h5>What We Offer</h5><h2>Global Connect has partnered with many banks as &ldquo;TradeFinanceSpecialists&rdquo;. Along with our Partner Banks we provide customised solutions to our clientele. We have priority processing for our clients.</h2><p><img src="/assets/images/international-trade-finance.jpg" alt="Trade Finance Specialists" class="img-fluid" /></p><h5>Our Expertise &amp; Core Instruments</h5><h3>Standby Letter of Credit (SB L/C)</h3><p>A written undertaking given to a beneficiary (the party with whom you have entered into a contract for the supply of goods) that in the event of your inability to meet the specific financial obligations stated in the contract, the bank will satisfy that obligation on behalf of the applicant, after which the applicant reimburses the issuing bank for the payment made.</p><p><img src="/assets/images/standby-lc.jpg" alt="Standby Letter of Credit" class="img-fluid" /></p><h3>Transferable Letter of Credit</h3><p>A Transferable Letter of Credit enables the transfer of payment to be made directly to the Supplier(s) if the Letter of Credit&rsquo;s beneficiary is a middleman or trading intermediary.</p><p><img src="/assets/images/transferable-lc.jpg" alt="Transferable Letter of Credit" class="img-fluid" /></p><h3>Documentary Letter of Credit (L/C)</h3><p>This is an undertaking given to the supplier on behalf of the buyer that payment would be made for goods upon delivery within a stipulated time. An importer who uses the Letter of Credit reduces the risk of having to pay for goods in advance, thereby saving him the situation of having to pay for goods not consistent with the terms stated in the Letter of Credit.</p><p><img src="/assets/images/documentary-lc.jpg" alt="Documentary Letter of Credit" class="img-fluid" /></p><h3>Back to Back Letter of Credit</h3><p>This is a new letter of credit opened based on an already existing, non-transferable credit used as collateral. A Back-to-Back Letter is used when the beneficiary (client) does not wish to divulge certain information deemed sensitive. A trader receives a letter of credit from the buyer and then opens another letter of credit in favour of the Supplier; thus, the first letter of credit serves as collateral for the second letter of credit.</p><p><img src="/assets/images/backtoback-lc.jpg" alt="Back to Back Letter of Credit" class="img-fluid" /></p>',
                1
            ],
            [
                7, 3, 'Banking Support', 'banking-support', 'Corporate Banking, Pre-Paid Master Cards & International Wire Transfers', 'Banking Support Services', 'Integrated Corporate Accounts & Global Payment Platforms', 'banking-support.jpg', 'Facilitating corporate banking accounts with 24x7 online access, international wire transfers (KYC/AML verified), and pre-paid master cards.',
                '<h2>Comprehensive Banking Support Services</h2><p><img src="/assets/images/banking-support.jpg" alt="Banking Support Services" class="img-fluid" /></p><h3>Trade Finance</h3><p>We offer Trade Finance in association with our partner banks. With our experience and knowledge in the trade finance industry, we are ready to provide reliable and effective services for business transactions of our clients internationally. We offer very quick processing in the issuance and relay of Letters of Credit [LC], Express Letters of Credit [ELC], Stand-By Letters of Credit [SBLC], and Bank Guarantees [BG]. We ensure that you spend minimum time on documentation.</p><h3>Corporate Banking</h3><p>We offer Corporate Banking in association with our partner banks. This is designed to provide flexible and integrated savings solutions with ease of opening accounts from anywhere in the world. These can be operated as corporate savings accounts as well as personal savings accounts and access is available online 24&times;7. With adequate due diligence and security assurance, we provide our clients safe banking facilities.</p><p><img src="/assets/images/corporate-banking.jpg" alt="Corporate Banking Facilities" class="img-fluid" /></p><h3>Pre-paid Master Cards</h3><p><img src="/assets/images/prepaid-card.jpg" alt="Pre-paid Master Cards" class="img-fluid" /></p><p>Pre-paid master cards offered through our partner banks provide a consistent payment platform for those who want the best. You can travel, shop, dine, and indulge in countless products and services with complete convenience.</p><h3>International Wire Transfers</h3><p>International Wire Transfers offered through our partner banks provide wire transfer facilities to all customers and non-customers. We process comprehensive due diligence, KYC, AML, PEP, and sanctions checks to ensure maximum security of your transactions. Existing account holders of our partner banks can transfer money or send instructions for incoming transfers through online banking facilities hassle-free.</p><p><img src="/assets/images/wire-transfer.jpg" alt="International Wire Transfers" class="img-fluid" /></p>',
                2
            ],
            [
                8, 3, 'Advisory', 'advisory', 'International Trade Consulting, Sourcing, Logistics & Shipping Assistance', 'Advisory Services', 'Strategic Guidance for Cross-Border Sourcing and Negotiations', 'advisory.jpg', 'Assisting clients in international sourcing, Chinese supplier contract manufacturing, global warehousing, freight forwarding, and direct manufacturer negotiations.',
                '<h5>What We Offer</h5><h2>The Global Connect group provides advisory solutions to Institutions and Corporates in managing and growing their business via high quality Financial Advisory services and capital market solutions.</h2><p><img src="/assets/images/consulting-home.jpg" alt="Financial Advisory Desk" class="img-fluid" /></p><h3>International Trade Consulting</h3><p>In today&rsquo;s fierce business environment, companies considering sourcing or selling their products overseas must evaluate many more options than in the past and process greater amounts of information in order to make decisions. And cultural barriers and complex compliance initiatives make global trade even more of a challenge. Our experience and knowledge helps mitigate the risks of our customers, by developing a solid understanding of the international trade issues that may impact their specific business.</p><h3>Sourcing and Contract Manufacturing</h3><p>We assist our customers in sourcing products from any part of the world, depending on their requirements and accurate product specifications. With excellent networks with Chinese Suppliers and Manufacturers, we even provide support in contract manufacturing products.</p><p><img src="/assets/images/contract-manufacturing.jpg" alt="Sourcing and Contract Manufacturing" class="img-fluid" /></p><h3>Logistical Support</h3><p>With the support of our logistical partners globally, we can provide logistical support for our customers. Our support can extend to warehousing of the products, based on specific tariffs and charges.</p><p><img src="/assets/images/logistics.jpg" alt="Logistical Support & Warehousing" class="img-fluid" /></p><h3>Shipping Assistance</h3><p>We can provide shipping assistance to our customers if required in special cases. Our charges will be based on expenses and handling fees, as well as our consulting commissions. We can help in documentation, shipment, and freight forwarding.</p><p><img src="/assets/images/shipping.jpg" alt="Shipping & Freight Forwarding Assistance" class="img-fluid" /></p><h3>Negotiations</h3><p>In most cases of dealing with manufacturers, a certain level of experience and negotiation skill becomes imperative for businesses. We can assist our customers in direct negotiations with manufacturers to ensure a favourable outcome.</p><p><img src="/assets/images/negotiation.jpg" alt="Manufacturer Negotiations & Deal Closing" class="img-fluid" /></p>',
                3
            ]
        ];

        $pageCount = $db->query("SELECT COUNT(*) FROM pages")->fetchColumn();
        if ($pageCount == 0) {
            $pageStmt = $db->prepare("
                INSERT OR REPLACE INTO pages (
                    id, category_id, title, slug, subtitle, banner_title, banner_subtitle, 
                    featured_image, summary, content, status, menu_order, is_system
                ) VALUES (
                    ?, ?, ?, ?, ?, ?, 
                    ?, ?, ?, ?, 'published', ?, 0
                )
            ");

            foreach ($pages as $p) {
                $pageStmt->execute([
                    $p[0], $p[1], $p[2], $p[3], $p[4], $p[5], $p[6],
                    $p[7], $p[8], $p[9], $p[10]
                ]);
            }
        }
    }
}

