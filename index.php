<?php
/**
 * Global Connect - Home Page
 * Same to same content & structure as reference website
 */
$pageTitle = 'Global Connect | Global Business Enterprise Headquartered in Singapore & India';
$pageMetaDesc = 'We are a global business enterprise headquartered in Singapore And India. Reliable partner for international trade services, trade finance, and financial advisory.';

require_once __DIR__ . '/includes/header.php';

$statPartners = get_setting('stat_partners', '3,600+');
$statCountries = get_setting('stat_countries', '19+');
$statBanks = get_setting('stat_banks', '19+');
?>

<!-- Hero Banner Section -->
<section class="hero-section">
    <div class="hero-overlay-grid"></div>
    <div class="hero-container">
        <div class="hero-content">
            <div class="hero-badge">
                <span>✦</span> Welcome to Global Connect
            </div>
            <h1 class="hero-title">
                We are a global business enterprise <br>
                headquartered in <span class="highlight">Singapore And India</span>
            </h1>
            <p class="hero-description">
                At Global Connect, we believe in building meaningful partnerships that drive success across borders. With our expertise and network, we connect businesses to new markets and lasting opportunities. Join us in creating pathways for growth, innovation, and global collaboration.
            </p>
            <div class="hero-actions">
                <a href="/trading.php" class="btn-primary">Explore Trading</a>
                <a href="/finance.php" class="btn-gold">Trade Finance</a>
                <a href="/contact.php" class="btn-outline-white">Contact Us</a>
            </div>
        </div>
    </div>
</section>

<!-- Key Statistics Bar -->
<section class="stats-section">
    <div class="container">
        <div class="stats-grid">
            <div class="stat-item">
                <div class="stat-number"><?= e($statPartners) ?></div>
                <div class="stat-label">Associate Partners</div>
            </div>
            <div class="stat-item">
                <div class="stat-number"><?= e($statCountries) ?></div>
                <div class="stat-label">Countries</div>
            </div>
            <div class="stat-item">
                <div class="stat-number"><?= e($statBanks) ?></div>
                <div class="stat-label">Partner Banks</div>
            </div>
        </div>
    </div>
</section>

<!-- Company Experience Section -->
<section class="section">
    <div class="container">
        <div class="experience-box">
            <div class="experience-grid">
                <div class="experience-content">
                    <span class="section-subtitle">Company Experience</span>
                    <h3>Growth Strategies That Drive Cross-Border Success</h3>
                    <p>
                        Global Connect’s believe in Growth Strategies. The organisation involves various teams to handle client needs which start from Capital Raising, Project Finance, Financial Engineering or Structuring, Fund raising programs to name a few.
                    </p>
                    <a href="/about.php" class="btn-primary">View More About Us →</a>
                </div>
                <div class="experience-image">
                    <img src="/assets/images/about-growth.jpg" alt="Company Growth Strategies" class="img-fluid" width="560" height="340">
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Our Services Section -->
<section class="section section-bg-light">
    <div class="container">
        <div class="section-header">
            <span class="section-subtitle">Our Services</span>
            <h2 class="section-title">Your Reliable Partner For International Trade Services</h2>
            <p class="section-lead">
                Comprehensive global solutions across physical commodities trading, structured trade finance, and institutional corporate advisory.
            </p>
        </div>

        <div class="services-grid">
            <!-- Service 1: Trading -->
            <div class="service-card">
                <div class="service-card-image">
                    <img src="/assets/images/trading-home.jpg" alt="Global Trading" loading="lazy">
                </div>
                <div class="service-card-body">
                    <h3 class="service-card-title">Trading</h3>
                    <p class="service-card-text">
                        We trade in a variety of products and commodities all around the world. We pride in our diverse range of offerings to match our customer needs and requirements.
                    </p>
                    <a href="/trading.php" class="service-card-link">Read More +</a>
                </div>
            </div>

            <!-- Service 2: Finance -->
            <div class="service-card">
                <div class="service-card-image">
                    <img src="/assets/images/finance-home.jpg" alt="Trade Finance" loading="lazy">
                </div>
                <div class="service-card-body">
                    <h3 class="service-card-title">Finance</h3>
                    <p class="service-card-text">
                        We are TradeFinanceSpecialists offering various services in the Finance Domain all around the world. We pride in our diverse range of offerings to match our customer needs and requirements.
                    </p>
                    <a href="/finance.php" class="service-card-link">Read More +</a>
                </div>
            </div>

            <!-- Service 3: Consulting -->
            <div class="service-card">
                <div class="service-card-image">
                    <img src="/assets/images/consulting-home.jpg" alt="Corporate Consulting" loading="lazy">
                </div>
                <div class="service-card-body">
                    <h3 class="service-card-title">Consulting</h3>
                    <p class="service-card-text">
                        Global Connect’s group provides solutions to Institutions and Corporate in managing and growing their business via high quality financial advisory services and capital market solutions.
                    </p>
                    <a href="/consulting.php" class="service-card-link">Read More +</a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Singapore Financial Hub Advantages Section -->
<section class="section">
    <div class="container">
        <div class="section-header">
            <span class="section-subtitle">Strategic Advantage</span>
            <h2 class="section-title">Singapore: The World’s Premier Gateway For Global Business</h2>
            <p class="section-lead">
                Discover why Singapore is the chosen headquarters and financial hub for leading multinational institutions.
            </p>
        </div>

        <div class="sg-grid">
            <!-- Card 1 -->
            <div class="sg-card">
                <div class="sg-card-icon">🏛</div>
                <h4>Singapore is one of the top financial hubs</h4>
                <p>
                    Singapore is one of the top financial hubs after that of New York, London and Hong Kong. Being one of the core nations in South East Asia, most of the funds of the area are parked in Singapore. Business owners take advantage of the closeness of all the financial services in Singapore to manage their funds for their personal investments and corporate investments.
                </p>
                <p style="margin-top: 10px;">
                    Having the status of a financial hub also means that you can get your currency exchanged done quite easy and with good rates in Singapore because most of the funds are in Singapore. Singapore is also able to handle your financial work close to 24/7 with a strong and never sleeping financial sector that is in place for the wealthy and powerful to grow their money at a constant rate.
                </p>
            </div>

            <!-- Card 2 -->
            <div class="sg-card">
                <div class="sg-card-icon">⚖</div>
                <h4>Singapore has a strong rule of law and court system</h4>
                <p>
                    Rule of law is what is lacking in the region. There is still rampant corruption in most countries in the region and this is something that many business owners are worried about. It is well known that contracts for transactions in other countries are signed in Singapore. Since both companies can set up base in Singapore, they can sign their contracts in Singapore for execution in the home country.
                </p>
                <p style="margin-top: 10px;">
                    In such situation, if one of the members decide to go against their word, the victim company can use the laws of Singapore to take the other party to court and recover some losses. This fair system allows both parties to have a better chance as compared to money politics and money law that can be in place in other countries in the region.
                </p>
            </div>

            <!-- Card 3 -->
            <div class="sg-card">
                <div class="sg-card-icon">🎓</div>
                <h4>Singapore has a very well educated employee pool</h4>
                <p>
                    Head quartering a company means you need to have strong executive and also administrative team in that country. Singapore has the best educated pool of employees in the world. Being ranked one of the top for education, Singapore is able to handle the needs of most demanding companies that might need someone who is good in accounting, economics or finance to manage their businesses remotely.
                </p>
                <p style="margin-top: 10px;">
                    Hiring is also easy for most companies, you will be able to find a local who will be happy to travel around the region for work. You will also be able to bring in employees from the region to work full time here, after you have fulfilled quota requirements.
                </p>
                <p style="margin-top: 10px;">
                    With new technologies coming up, Singapore has started its push into what we call a Smart Nation initiative to ensure our competitive edge against other countries by making sure everyone is trained with future skills so that the Singapore economy will be relevant in the years to come.
                </p>
            </div>

            <!-- Card 4 -->
            <div class="sg-card">
                <div class="sg-card-icon">💵</div>
                <h4>Singapore has a strong and stable currency</h4>
                <p>
                    Currency can mean profits or loss depending on which side you are. This is also one of the worst fears of the businessman in the region. There are economies where the fluctuation of the currency can be 10% , you will be safe to have your hands on the Singapore dollar. One of the most stable in the world and controlled closely by the MAS, the Singapore dollar is a great way to hedge against losses in currency exchanges.
                </p>
                <p style="margin-top: 10px;">
                    Singapore needs to maintain a strong currency structure because it is highly dependent on trade and this is something that you can be assured to not change for a long time. You would also be safe to hold your cash in Singapore for your savings and investment as Singapore is also one of the best countries in terms of security.
                </p>
                <p style="margin-top: 10px;">
                    Being a big importer of goods, Singapore has to maintain a strong and stable currency and therefore it is a good place to trade in terms of Singapore Dollars and to hold the SGD for a long periods of time without fear of it losing its value.
                </p>
            </div>

            <!-- Card 5 -->
            <div class="sg-card">
                <div class="sg-card-icon">✈</div>
                <h4>Singapore is position right in the middle, traveling in the region is mostly within 2 to 4 hours</h4>
                <p>
                    Singapore is right smack in the middle and Singapore Changi Airport is also one of the biggest aviation hubs of the world. You can get to most parts of the South East Asian region within 2 to 4 hours and most business owners fly to have their meetings and return within the day which makes business doing in the region very easy and smooth. Coordination with them over time zones is also easier because since you are in the middle, you are not distracted too much by the extremes of time zones.
                </p>
                <p style="margin-top: 10px;">
                    Singapore is also aligned to the time zones of Hong Kong and Shanghai which makes doing business with this major business hubs easier since you can make phone calls and having meetings with them easily.
                </p>
            </div>
        </div>

        <!-- FDI Report Box -->
        <div class="fdi-report-box">
            <div class="fdi-header">
                <span class="section-subtitle" style="color: var(--accent-gold);">Economic Insights</span>
                <h3>Singapore Continues as India’s Largest Source of Foreign Direct Investment (FDI)</h3>
                <p style="color: #cbd5e1; margin-top: 8px;">
                    Singapore continued to be India’s largest source of foreign direct investment (FDI) for the last seven years, as the country received the highest inflows of about $15 billion in 2024-25. The overseas inflow grew 13 per cent to $50 billion in the last fiscal.
                </p>
            </div>

            <div class="fdi-stats-row">
                <div class="fdi-stat-card">
                    <div class="fdi-stat-num">$14.94B</div>
                    <div class="fdi-stat-desc">FDI from Singapore in 2024-25 (Up from $11.77B in 2023-24)</div>
                </div>
                <div class="fdi-stat-card">
                    <div class="fdi-stat-num">19%</div>
                    <div class="fdi-stat-desc">Singapore Share of Total Inflows into India</div>
                </div>
                <div class="fdi-stat-card">
                    <div class="fdi-stat-num">$81.04B</div>
                    <div class="fdi-stat-desc">Total FDI into India (14% Growth, 3-Year High)</div>
                </div>
            </div>

            <p style="color: #e2e8f0; margin-bottom: 25px; font-size: 15px;">
                Since 2018-19, Singapore has been the largest source of such investments in India. During 2024-25, Singapore was followed by Mauritius (USD 8.34B), US (USD 5.45B), Netherlands (USD 4.62B), UAE (USD 3.12B), Japan (USD 2.47B), Cyprus (USD 1.2B), UK (USD 795M), Germany (USD 469M), and Cayman Islands (USD 371M).
            </p>

            <div class="quotes-grid">
                <div class="quote-card">
                    <blockquote>
                        “Given that Asia is the second largest region to receive foreign capital inflows, a large part of the funds come from Singapore. One, being a low-tax jurisdiction and with a robust legal framework, Singapore is considered the strategic financial gateway to Asia. Double Tax Avoidance Agreement between the two nations helps all Singapore-based organisations to invest in India and reduce the total tax burden on income earned from India.”
                    </blockquote>
                    <div class="quote-author">
                        <strong>Rumki Majumdar</strong>
                        <span>Economist, Deloitte India</span>
                    </div>
                </div>

                <div class="quote-card">
                    <blockquote>
                        “Singapore’s continued dominance in India FDI now relies more on genuine business and regulatory advantages, Singapore’s sophisticated financial market, its status as a regional hub, and political and economic stability.”
                    </blockquote>
                    <div class="quote-author">
                        <strong>Lokesh Shah</strong>
                        <span>Partner, IndusLaw</span>
                    </div>
                </div>

                <div class="quote-card">
                    <blockquote>
                        “Singapore’s rising FDI into India is anchored in its role as a global financial hub, home to a large number of international private equity and venture capital funds. These investors see India as a high-growth destination, particularly in sectors like financial services, banking, insurance, IT, trading and pharmaceuticals.”
                    </blockquote>
                    <div class="quote-author">
                        <strong>Rudra Kumar Pandey</strong>
                        <span>Partner, Shardul Amarchand Mangaldas &amp; Co</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Capability Callout -->
        <div style="margin-top: 50px; background: #ffffff; border: 2px dashed var(--secondary-color); border-radius: var(--radius-md); padding: 30px; text-align: center;">
            <p style="font-size: 17px; font-weight: 600; color: var(--primary-color); margin-bottom: 0;">
                Having strong networks with manufacturers and exporters, we are enabled to ensure a timely and efficient fulfilment of trade requirements from our customers. We pride ourselves in our constantly evolving product lines and offerings.
            </p>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
