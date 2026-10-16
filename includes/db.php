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
                ['site_title', 'Global Connect - Sourcing & Supply Chain Partner'],
                ['site_name', 'Global Connect'],
                ['site_tagline', 'Your Strategic Partner in Sourcing & Supply Chain Management'],
                ['contact_email', 'admin@globalconnectsg.com'],
                ['contact_phone', '(65) 89451100'],
                ['office_address', '195 Jalan Besar, #03-01, Singapore 208883'],
                ['stat_partners', '3,600+'],
                ['stat_countries', '19+'],
                ['stat_banks', '19+'],
                ['stat_experience', '25+ Years'],
                ['company_reg', '201931818H']
            ];
            $setStmt = $db->prepare("INSERT OR IGNORE INTO settings (setting_key, setting_value) VALUES (?, ?)");
            foreach ($settings as $s) {
                $setStmt->execute($s);
            }

            // Seed CMS pages
            $pages = [
                [
                    2, 'Copper Cathodes', 'copper-cathodes',
                    'Grade A 99.99% Electrolytic Copper Cathodes',
                    'Copper Cathodes Trading & Sourcing',
                    'Global supply of premium copper cathodes meeting international standards with guaranteed purity.',
                    'minerals-metals.jpg',
                    'High purity Non-LME and LME grade copper cathodes sourced from certified refineries across Africa, South America, and Central Asia.',
                    '<h2>Electrolytic Copper Cathodes (Grade A 99.99%)</h2><p>Global Connect facilitates the structured supply of premium Electrolytic Copper Cathodes (Grade A, Non-LME &amp; LME registered brands). Sourced from Tier-1 refineries across Zambia, DR Congo, Chile, and Kazakhstan, our copper products satisfy rigid international manufacturing benchmarks.</p><h3>Key Specifications</h3><table class=\"table\"><thead><tr><th>Parameter</th><th>Specification</th></tr></thead><tbody><tr><td>Purity (Cu)</td><td>99.99% Minimum (ASTM B115-00)</td></tr><tr><td>Dimensions</td><td>914mm x 914mm x 12mm (approx.)</td></tr><tr><td>Weight per Sheet</td><td>125 kg (+/- 1%)</td></tr><tr><td>Packaging</td><td>Palletized bundles banded with steel straps</td></tr><tr><td>Inspection</td><td>SGS / Alex Stewart International at loading port</td></tr></tbody></table><h3>Commercial &amp; Trade Terms</h3><p>We structure spot and long-term annual supply contracts with flexible financial instruments including Irrevocable Confirmed Letters of Credit (DLC / SBLC / MT700) and Escrow facilities.</p>',
                    1
                ],
                [
                    2, 'Sugar ICUMSA 45', 'sugar-icumsa-45',
                    'Refined White Cane Sugar ICUMSA 45 from Brazil & Thailand',
                    'Sugar ICUMSA 45 Sourcing',
                    'Top-tier refined white cane sugar suitable for human consumption and industrial food production.',
                    'sugar-trade.jpg',
                    'High-grade refined white cane sugar ICUMSA 45 sourced directly from major Brazilian and Thai sugar mills with prompt bulk shipping.',
                    '<h2>Refined White Cane Sugar - ICUMSA 45</h2><p>Global Connect is a leading sourcing channel for 100% refined white cane sugar ICUMSA 45, sourced directly from verified Brazilian and Thai mills for global port delivery (CIF/FOB).</p><h3>Technical Specifications</h3><table class=\"table\"><thead><tr><th>Property</th><th>Standard</th></tr></thead><tbody><tr><td>Polarization</td><td>99.80% Minimum at 20°C</td></tr><tr><td>ICUMSA Color</td><td>45 RBU Maximum (ICUMSA Method No. 4/1978)</td></tr><tr><td>Moisture</td><td>0.04% Maximum</td></tr><tr><td>Ash Content</td><td>0.04% Maximum</td></tr><tr><td>Solubility</td><td>100% Dry and Free Flowing</td></tr><tr><td>Radiation</td><td>Normal / Non-radioactive</td></tr></tbody></table><h3>Shipping &amp; Logistics</h3><p>Supplied in 50 kg polypropylene bags or bulk breakbulk vessels. Inspected by SGS prior to dispatch.</p>',
                    2
                ],
                [
                    2, 'HMS 1 & 2 Scrap Metal', 'scrap-metal-hms',
                    'Heavy Melting Steel Scrap (HMS 1 / HMS 2 - 80:20 Ratio)',
                    'Ferrous Scrap Sourcing',
                    'Premium grade heavy melting steel scrap for industrial steel smelting and recycling.',
                    'minerals-metals.jpg',
                    'Quality ferrous scrap ISRI 200-206 compliant heavy melting steel scrap sourced from ship breaking yards and industrial decommissioning projects.',
                    '<h2>Heavy Melting Steel (HMS 1 &amp; 2 - 80:20)</h2><p>We source clean, non-radioactive heavy melting steel scrap according to ISRI 200-206 guidelines, perfectly suited for electric arc furnace (EAF) and induction furnace steel plants globally.</p><h3>Specification Standards</h3><ul><li><strong>HMS 1 (ISRI 200):</strong> Clean wrought iron and steel scrap, minimum thickness 1/4 inch (6mm).</li><li><strong>HMS 2 (ISRI 201-206):</strong> Cut steel scrap, minimum thickness 1/8 inch (3mm).</li><li><strong>Blend Ratio:</strong> 80% HMS 1 : 20% HMS 2.</li><li><strong>Impurity Level:</strong> Less than 0.5% non-metallic residuals.</li></ul>',
                    3
                ],
                [
                    2, 'Coal & Thermal Energy', 'coal-energy',
                    'Steam Coal, Anthracite & Coking Coal',
                    'Thermal Energy Commodities',
                    'High calorific value Indonesian and Australian steam coal for power generation and industrial applications.',
                    'petroleum-lubricants.jpg',
                    'Direct supply of high-grade Indonesian and Australian thermal coal with customized GCV specifications (3800 - 6500 kcal/kg).',
                    '<h2>Thermal Coal &amp; Metallurgical Coking Coal</h2><p>Global Connect partners with concession owners and major coal miners in Indonesia (Kalimantan, Sumatra) and Australia to supply coal for thermal power utilities and cement plants worldwide.</p><h3>Calorific Grades</h3><table class=\"table\"><thead><tr><th>Grade</th><th>Gross Calorific Value (GCV - ARB)</th><th>Total Moisture</th></tr></thead><tbody><tr><td>Low-Rank</td><td>3,800 - 4,200 kcal/kg</td><td>32% - 38%</td></tr><tr><td>Mid-Rank</td><td>4,800 - 5,200 kcal/kg</td><td>22% - 28%</td></tr><tr><td>High-Rank</td><td>5,800 - 6,500 kcal/kg</td><td>12% - 16%</td></tr></tbody></table>',
                    4
                ],
                [
                    2, 'Palm Oil & Edible Oils', 'palm-oil-edible-oils',
                    'Crude Palm Oil (CPO), RBD Palm Olein (CP8, CP10) & Sunflower Oil',
                    'Edible Oils Trading',
                    'Bulk edible oils for commercial food manufacturing, frying, and biodiesel refinement.',
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
