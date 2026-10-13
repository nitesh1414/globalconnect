<?php
/**
 * Global Connect - Public Header Component
 */
require_once __DIR__ . '/functions.php';

$siteTitle = get_setting('site_title', 'Global Connect');
$phoneSg = get_setting('header_phone_sg', '(65) 89451100');
$phoneRoam = get_setting('header_phone_roam', '(91) 99100 02299');
$emailPrimary = get_setting('contact_email_primary', 'admin@globalconnectsg.com');
$emailSecondary = get_setting('contact_email_secondary', 'shammy@globalconnectsg.com');
$logoUrl = get_setting('logo_url', '/assets/images/logo.svg');

// Fetch dynamic subpages for Trading and Finance dropdowns
$tradingPages = get_pages_by_category('trading');
$financePages = get_pages_by_category('finance');

$pageTitle = $pageTitle ?? $siteTitle . ' | Global Business Enterprise Headquartered in Singapore & India';
$pageMetaDesc = $pageMetaDesc ?? 'Global Connect - Reliable partner for international trade services, trade finance, and corporate financial advisory.';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle) ?></title>
    <meta name="description" content="<?= e($pageMetaDesc) ?>">
    <link rel="icon" type="image/svg+xml" href="/assets/images/logo.svg">
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>

    <!-- Top Info Bar -->
    <div class="top-bar">
        <div class="top-bar-container">
            <div class="top-bar-left">
                <div class="top-bar-item">
                    <span class="tag">Singapore</span>
                    <a href="tel:<?= e(preg_replace('/[^0-9+]/', '', $phoneSg)) ?>"><?= e($phoneSg) ?></a>
                </div>
                <div class="top-bar-item">
                    <span class="tag">Global Roam</span>
                    <a href="tel:<?= e(preg_replace('/[^0-9+]/', '', $phoneRoam)) ?>"><?= e($phoneRoam) ?></a>
                </div>
            </div>
            <div class="top-bar-right">
                <div class="top-bar-item">
                    <span>✉</span>
                    <a href="mailto:<?= e($emailPrimary) ?>"><?= e($emailPrimary) ?></a>
                </div>
                <div class="top-bar-item">
                    <a href="mailto:<?= e($emailSecondary) ?>"><?= e($emailSecondary) ?></a>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Navigation Header -->
    <header class="site-header">
        <div class="header-container">
            <!-- Brand Logo -->
            <a href="/index.php" class="site-logo" title="<?= e($siteTitle) ?>">
                <img src="<?= e($logoUrl) ?>" alt="<?= e($siteTitle) ?> Logo" width="220" height="52">
            </a>

            <!-- Navigation Bar -->
            <nav class="main-nav" aria-label="Main Navigation">
                <ul class="nav-menu" id="navMenu">
                    <li class="nav-item <?= is_active('home') ? 'active' : '' ?>">
                        <a href="/index.php" class="nav-link">Home</a>
                    </li>
                    <li class="nav-item <?= is_active('about') ? 'active' : '' ?>">
                        <a href="/about.php" class="nav-link">About Us</a>
                    </li>
                    
                    <!-- Trading with Dynamic Subpages Dropdown -->
                    <li class="nav-item has-dropdown <?= is_category_active('trading') ? 'active' : '' ?>">
                        <a href="/trading.php" class="nav-link">
                            Trading <span class="dropdown-arrow">▼</span>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a href="/trading.php" class="dropdown-item"><strong>All Trading Overview</strong></a></li>
                            <?php foreach ($tradingPages as $tp): ?>
                                <li>
                                    <a href="/page.php?slug=<?= e($tp['slug']) ?>" class="dropdown-item">
                                        <?= e($tp['title']) ?>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </li>

                    <!-- Finance with Dynamic Subpages Dropdown -->
                    <li class="nav-item has-dropdown <?= is_category_active('finance') ? 'active' : '' ?>">
                        <a href="/finance.php" class="nav-link">
                            Finance <span class="dropdown-arrow">▼</span>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a href="/finance.php" class="dropdown-item"><strong>All Finance Overview</strong></a></li>
                            <?php foreach ($financePages as $fp): ?>
                                <li>
                                    <a href="/page.php?slug=<?= e($fp['slug']) ?>" class="dropdown-item">
                                        <?= e($fp['title']) ?>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </li>

                    <li class="nav-item <?= is_active('consulting') ? 'active' : '' ?>">
                        <a href="/consulting.php" class="nav-link">Consulting</a>
                    </li>

                    <li class="nav-item <?= is_active('contact') ? 'active' : '' ?>">
                        <a href="/contact.php" class="nav-link">Contact</a>
                    </li>
                </ul>
            </nav>

            <!-- Header Action CTA -->
            <div class="header-action">
                <a href="/contact.php" class="btn-header-cta">Get in Touch →</a>
            </div>

            <!-- Mobile Hamburger Toggle -->
            <button class="mobile-toggle" id="mobileToggle" aria-label="Toggle Navigation Menu">
                ☰
            </button>
        </div>
    </header>
