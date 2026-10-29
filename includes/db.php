<?php
/**
 * Global Connect - MySQL Database Handler
 * Standard MySQL PDO Connection with automated preview resilience
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
                // Ensure hero_slides table exists
                $stmt2 = $db->query("SHOW TABLES LIKE 'hero_slides'");
                if (!$stmt2->fetch()) {
                    $db->exec("
                        CREATE TABLE IF NOT EXISTS `hero_slides` (
                          `id` INT AUTO_INCREMENT PRIMARY KEY,
                          `title` VARCHAR(255) NULL,
                          `subtitle` TEXT NULL,
                          `badge_text` VARCHAR(100) NULL,
                          `image_path` VARCHAR(255) NOT NULL,
                          `button_text` VARCHAR(100) NULL,
                          `button_link` VARCHAR(255) NULL,
                          `secondary_button_text` VARCHAR(100) NULL,
                          `secondary_button_link` VARCHAR(255) NULL,
                          `show_text_overlay` TINYINT(1) DEFAULT 0,
                          `status` ENUM('active', 'inactive') DEFAULT 'active',
                          `sort_order` INT DEFAULT 0,
                          `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
                          `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
                        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
                    ");
                    $db->exec("
                        INSERT INTO `hero_slides` (`id`, `title`, `subtitle`, `badge_text`, `image_path`, `button_text`, `button_link`, `secondary_button_text`, `secondary_button_link`, `show_text_overlay`, `status`, `sort_order`) VALUES
                        (1, 'Global Connect Group of Companies', 'Singapore • India • Indonesia • Hongkong | Sourcing, Supply Chain & Financial Structuring', 'Singapore • India • Global Network', 'assets/images/global-connect-banner-1.png', 'Explore Trading', 'trading.php', 'Trade Finance', 'finance.php', 0, 'active', 1),
                        (2, 'Your Strategic Partner in Sourcing & Supply Chain', 'We are a global business enterprise headquartered in Singapore and India. At Global Connect, we believe in building meaningful partnerships that drive success across international borders.', 'Singapore • India • Global Network', 'assets/images/banner-hero-1.png', 'Explore Trading', 'trading.php', 'Trade Finance', 'finance.php', 1, 'active', 2)
                        ON DUPLICATE KEY UPDATE `image_path`=`image_path`;
                    ");
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

        $cnt = $db->query("SELECT COUNT(*) FROM pages")->fetchColumn();
        if ($cnt == 0) {
            // Admin user
            $adminPass = password_hash('admin123', PASSWORD_BCRYPT);
            $stmt = $db->prepare("INSERT OR IGNORE INTO users (id, username, password, email, full_name, role) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([1, 'admin', $adminPass, 'admin@globalconnectsg.com', 'Admin User', 'admin']);

            // Categories
            $cats = [
                [1, 'General', 'general', 'Core company pages', 1],
                [2, 'Trading', 'trading', 'Commodities, Minerals, Agro & Energy Trading', 2],
                [3, 'Finance', 'finance', 'Trade Finance, Banking Support & Advisory Solutions', 3],
                [4, 'Consulting', 'consulting', 'Corporate Financial & Advisory Consulting', 4]
            ];
            $catStmt = $db->prepare("INSERT OR IGNORE INTO categories (id, name, slug, description, menu_order) VALUES (?, ?, ?, ?, ?)");
            foreach ($cats as $c) {
                $catStmt->execute($c);
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
                ['home_services_title', 'Comprehensive Trade & Financial Solutions'],
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


            // Default Pages for CMS
            $pages = [
                [
                    2, 'Copper Cathodes (Grade A)', 'copper-cathodes',
                    'Grade A 99.99% Electrolytic Copper Cathodes (LME Non-Registered)',
                    'Non-Ferrous Metals & Minerals',
                    'Direct allocation and contract sourcing of electrolytic copper cathodes from compliant smelters globally.',
                    'minerals-metals.jpg',
                    'Grade A Electrolytic Copper Cathodes with 99.99% purity (LME Non-Registered) conforming to BS EN 1978:1998 (Cu-ETP-1).',
                    '<h2>Copper Cathodes Sourcing &amp; Allocation</h2><p>Global Connect facilitates the global physical trading of electrolytic Copper Cathodes (Cu purity 99.99%). Our direct access to Tier-1 mining houses, bonded warehouses, and African / South American supply corridors enables seamless allocation for industrial clients worldwide.</p><h3>Key Specifications</h3><ul><li><strong>Purity:</strong> 99.99% Minimum (Conforms to ASTM B115-00 / LME Grade A)</li><li><strong>Form:</strong> Rectangular sheet cathodes (~125 kg per sheet)</li><li><strong>Packaging:</strong> Palletized in steel strapped bundles (~2.5 MT per bundle)</li><li><strong>Delivery Terms:</strong> CIF, CFR, or FOB to designated world ports with SGS / Alex Stewart inspection.</li></ul>',
                    1
                ],
                [
                    2, 'Sugar (ICUMSA 45 & Raw Cane)', 'sugar-icumsa-45',
                    'Refined White Cane Sugar ICUMSA 45 & VHP Raw Sugar',
                    'Soft Commodities & Agricultural Products',
                    'High volume refined sugar allocations originating from prime Brazilian and Indian sugar mills.',
                    'sugar-trade.jpg',
                    'Brazilian ICUMSA 45 refined white sugar and raw cane sugar supplied via bulk vessel loads and containerized lots with full SGS certification.',
                    '<h2>Refined White Sugar ICUMSA 45</h2><p>As one of the world’s most demanding soft commodity supply chains, Global Connect works directly with certified Brazilian and Indian sugar mills and top-tier trading houses to deliver refined and raw sugar with guaranteed origin documentation and quality inspections.</p><h3>Product Specifications</h3><ul><li><strong>Polarization:</strong> 99.80% Minimum</li><li><strong>ICUMSA Rating:</strong> ICUMSA 45 RBU (Refined White Sugar)</li><li><strong>Moisture:</strong> 0.04% Maximum</li><li><strong>Ash Content:</strong> 0.04% Maximum</li><li><strong>Solubility:</strong> 100% Dry and Free Flowing</li><li><strong>Origin:</strong> Brazil (Santos/Paranaguá) or India</li></ul>',
                    2
                ],
                [
                    2, 'Scrap Metal & Heavy Melting Steel (HMS 1/2)', 'scrap-metal-hms',
                    'Ferrous Scrap Metals: HMS 1, HMS 2 & Used Rails (R50/R65)',
                    'Ferrous Metals & Recycling',
                    'Consolidated yard sourcing and containerized shipment of premium ferrous scrap for steel manufacturers.',
                    'minerals-metals.jpg',
                    'HMS 1/2 (80:20 ratio), shredded steel scrap (ISRI 211), and used rail scrap strictly compliant with ISRI quality standards.',
                    '<h2>Ferrous Scrap Metal &amp; Steel Supplies</h2><p>Global Connect supplies industrial steel mills and re-rolling facilities with high grade ferrous scrap. All cargo is surveyed by certified international inspection agencies (SGS, CCIC, Bureau Veritas) prior to shipment to ensure zero radiation and absence of hazardous materials.</p><h3>Grades Supplied</h3><ul><li><strong>HMS 1 &amp; 2 (ISRI 200-206):</strong> Standard 80:20 blend with clean thickness profiles.</li><li><strong>Used Rails (R50 - R65):</strong> Cut to specified 1.0m / 1.5m lengths for re-melting or re-rolling.</li><li><strong>Shredded Steel Scrap (ISRI 211):</strong> High density homogenous ferrous feed.</li></ul>',
                    3
                ],
                [
                    2, 'Coal & Thermal Energy Commodities', 'coal-energy',
                    'Steam Coal (GAR 3800 - GAR 6000) & Met Coke',
                    'Energy & Thermal Commodities',
                    'Bulk vessel allocations of Indonesian and Australian thermal coal for power utilities and industrial boilers.',
                    'gas-oil.jpg',
                    'High calorific value Indonesian thermal coal (GAR 4200 to 5800 kcal/kg) with low sulfur and moisture content.',
                    '<h2>Thermal Coal &amp; Energy Commodities</h2><p>We supply thermal power utilities, cement plants, and metallurgical processing units with reliable coal shipments sourced directly from licensed mining concessions in Kalimantan (Indonesia) and Australia.</p><h3>Coal Specifications</h3><ul><li><strong>GAR 3800 - 4200:</strong> High volatile, low ash coal for coastal power plants.</li><li><strong>GAR 5000 - 5800:</strong> High energy thermal coal with superior burn efficiency.</li><li><strong>Moisture &amp; Ash:</strong> Strictly controlled to international utility standards.</li><li><strong>Vessel Loading:</strong> Handymax, Supramax, and Panamax chartering.</li></ul>',
                    4
                ],
                [
                    2, 'Palm Oil & Edible Oils', 'palm-oil-edible-oils',
                    'RBD Palm Olein (CP8/CP10), Crude Palm Oil (CPO) & Sunflower Oil',
                    'Agro Commodities & Edible Oils',
                    'Bulk and packaged shipments of premium refined edible vegetable oils from Southeast Asian refineries.',
                    'agro-products.jpg',
                    'Certified sustainably sourced palm oil and derivatives from premier Southeast Asian plantations with international RSPO compliance.',
                    '<h2>Palm Oil Products &amp; Edible Vegetable Oils</h2><p>From Crude Palm Oil (CPO) to Refined Bleached Deodorized (RBD) Palm Olein (CP8 / CP10) and cooking oils, Global Connect manages bulk liquid vessel shipments directly from Malaysian and Indonesian ports.</p><h3>Product Range</h3><ul><li><strong>RBD Palm Olein (CP8 / CP10):</strong> Premium liquid fraction for food service and retail packaging.</li><li><strong>Crude Palm Oil (CPO):</strong> Standard industrial feedstock with FFA &lt; 5%.</li><li><strong>Refined Sunflower Oil:</strong> 100% pure refined sunflower oil for European and Middle Eastern markets.</li></ul>',
                    5
                ],
                [
                    2, 'Rice & Agro Commodities', 'rice-agro-commodities',
                    'Basmati Rice, Non-Basmati Parboiled & White Rice',
                    'Agro Food Commodities',
                    'Premium grain exports sourced from top agricultural belts in India, Thailand, and Vietnam.',
                    'agro-products.jpg',
                    'Long grain white rice, aromatic 1121 Basmati, and parboiled rice packaged to export buyer specifications.',
                    '<h2>Global Rice &amp; Agro Sourcing</h2><p>We source grains and staples through agricultural cooperatives and milling hubs across India, Thailand, and Vietnam, delivering high-yield grains for consumer and government food security programs.</p><h3>Varieties Supplied</h3><ul><li><strong>1121 Basmati Rice (Raw / Steam / Sella):</strong> Grain length 8.35mm+, exceptional aroma and elongation.</li><li><strong>Long Grain White Rice (5% / 10% / 25% Broken):</strong> High yield staple grain.</li><li><strong>Parboiled Rice (IR64 / Swarna):</strong> Nutritious double-steamed parboiled rice.</li></ul>',
                    6
                ],
                [
                    3, 'Trade Finance & LC / SBLC', 'trade-finance-lc-sblc',
                    'Letters of Credit (LC), Standby Letters of Credit (SBLC) & Bank Guarantees',
                    'Trade Banking & Credit Solutions',
                    'Comprehensive trade finance instruments through top tier international partner banks.',
                    'trade-finance.jpg',
                    'Structured financial instruments including MT700 LC, MT760 SBLC, and documentary collections to secure global import-export transactions.',
                    '<h2>Trade Finance &amp; Banking Instruments</h2><p>At Global Connect, our network of over 19+ premier banking partners across Singapore, Hong Kong, London, and Zurich allows us to structure and issue trade finance instruments for international transactions.</p><h3>Financial Instruments Handled</h3><ul><li><strong>Documentary Letters of Credit (DLC / MT700):</strong> Irrevocable, confirmed, transferrable or non-transferrable.</li><li><strong>Standby Letters of Credit (SBLC / MT760):</strong> Performance guarantees and financial assurance.</li><li><strong>Bank Guarantees (BG / MT760):</strong> Tender guarantees, advance payment bonds, and performance bonds.</li><li><strong>Documentary Collections (DP / DA):</strong> Controlled cargo release through trusted corresponding banks.</li></ul>',
                    1
                ],
                [
                    3, 'Project Financing', 'project-financing',
                    'Capital Structuring for Infrastructure, Energy & Industrial Projects',
                    'Strategic Project Financing',
                    'End-to-end capital raising, debt syndication, and equity mobilization for high-value projects.',
                    'financial-structure.jpg',
                    'Tailored capital solutions combining debt, equity, and sovereign guarantees for infrastructure, mining, energy, and logistics ventures.',
                    '<h2>Project Finance &amp; Capital Structuring</h2><p>Global Connect assists developers, sovereign entities, and corporate sponsors in securing capital for complex infrastructure, energy, and industrial projects worldwide.</p><h3>Our Structuring Capabilities</h3><ul><li>Non-recourse and limited-recourse project debt facilities.</li><li>Syndicated lending with multilateral development institutions.</li><li>Export Credit Agency (ECA) covered financing.</li><li>Private equity and mezzanine debt placement.</li></ul>',
                    2
                ],
                [
                    3, 'Working Capital Solutions', 'working-capital-solutions',
                    'Revolving Credit Facilities & Invoice Factoring',
                    'Liquidity & Working Capital',
                    'Optimizing corporate liquidity cycles with dynamic invoice discounting and inventory financing.',
                    'banking-support.jpg',
                    'Accelerate cash flows, bridge funding gaps, and manage supply chain payment terms with tailored liquidity solutions.',
                    '<h2>Working Capital &amp; Supply Chain Financing</h2><p>We provide innovative working capital structures designed to free up trapped liquidity across complex supply chains and trade lifecycles.</p><h3>Solutions Offered</h3><ul><li><strong>Accounts Receivable Discounting:</strong> Convert outstanding invoices into immediate operating capital.</li><li><strong>Supply Chain Finance (Reverse Factoring):</strong> Enable suppliers to receive early payments based on buyer credit.</li><li><strong>Warehouse Receipt Financing:</strong> Leverage physical commodity inventory as loan collateral.</li></ul>',
                    3
                ],
                [
                    3, 'Structured Trade Credit', 'structured-trade-credit',
                    'Risk Mitigation & Credit Enhancement Mechanisms',
                    'Structured Trade Credit & Advisory',
                    'Specialized credit enhancement strategies to de-risk high-volume commodity transactions.',
                    'standby-lc.jpg',
                    'Customized credit mitigation, political risk insurance, and structured escrow arrangements for emerging market transactions.',
                    '<h2>Structured Trade Credit &amp; Risk Enhancement</h2><p>Emerging market commodity flows require sophisticated risk containment. Global Connect structures multi-layered credit security to ensure safe transaction execution.</p><h3>Risk Management Framework</h3><ul><li>Comprehensive buyer and supplier credit assessment.</li><li>Credit insurance underwriting through Lloyd’s syndicates and global insurers.</li><li>Tri-partite collateral management agreements (CMA) with leading inspection agencies.</li></ul>',
                    4
                ]
            ];

            $pageStmt = $db->prepare("
                INSERT OR IGNORE INTO pages (
                    category_id, title, slug, subtitle, banner_title, banner_subtitle, 
                    featured_image, summary, content, status, menu_order, is_system
                ) VALUES (
                    ?, ?, ?, ?, ?, ?, 
                    ?, ?, ?, 'published', ?, 0
                )
            ");

            foreach ($pages as $p) {
                $pageStmt->execute([
                    $p[0], $p[1], $p[2], $p[3], $p[4], $p[3],
                    $p[6], $p[7], $p[8], $p[9]
                ]);
            }
        }
    }
}
