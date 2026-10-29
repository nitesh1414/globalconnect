<?php
/**
 * Global Connect - Header Component (100% Dynamic from Admin Settings)
 */
require_once __DIR__ . '/functions.php';

$siteTitle = get_setting('site_title', 'Global Connect | Global Business Enterprise Headquartered in Singapore & India');
$siteName = get_setting('company_name', 'Global Connect');
$siteTagline = get_setting('site_tagline', 'We are a global business enterprise headquartered in Singapore And India');
$phoneSg = get_setting('header_phone_sg', '(65) 89451100');
$phoneRoam = get_setting('header_phone_roam', '(91) 99100 02299');
$emailPrimary = get_setting('contact_email_primary', 'admin@globalconnectsg.com');
$emailSecondary = get_setting('contact_email_secondary', 'shammy@globalconnectsg.com');

// Dynamic Logo & Typography
$siteLogo = get_setting('site_logo', 'logo-design-gcv.png');
$logoP1 = get_setting('logo_title_p1', 'GLOBAL');
$logoP2 = get_setting('logo_title_p2', 'CONNECT');
$logoTagline = get_setting('logo_tagline', 'Ventures • Singapore • India');
$siteFavicon = get_setting('site_favicon', $siteLogo);

// Dynamic subpages for dropdown menus
$tradingSubpages = get_pages_by_category('trading');
$financeSubpages = get_pages_by_category('finance');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle ?? $siteTitle) ?></title>
    <meta name="description" content="<?= e($pageMetaDesc ?? $siteTagline) ?>">
    <link rel="icon" type="image/png" href="<?= image_url($siteFavicon) ?>">
    <link rel="stylesheet" href="<?= asset('css/style.css') ?>">
</head>
<body>

    <!-- Top Info Bar with animated pulses -->
    <div class="top-bar">
        <div class="top-bar-container">
            <div class="top-bar-left">
                <div class="top-bar-item">
                    <span class="tag tag-pulse">Singapore</span>
                    <a href="tel:<?= preg_replace('/[^0-9+]/', '', $phoneSg) ?>"><?= e($phoneSg) ?></a>
                </div>
                <div class="top-bar-item">
                    <span class="tag">Global Roam</span>
                    <a href="tel:<?= preg_replace('/[^0-9+]/', '', $phoneRoam) ?>"><?= e($phoneRoam) ?></a>
                </div>
            </div>
            <div class="top-bar-right">
                <div class="top-bar-item">
                    <span>✉</span>
                    <a href="mailto:<?= e($emailPrimary) ?>"><?= e($emailPrimary) ?></a>
                </div>
                <?php if (!empty($emailSecondary)): ?>
                    <div class="top-bar-item">
                        <a href="mailto:<?= e($emailSecondary) ?>"><?= e($emailSecondary) ?></a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Main Navigation Header (Frosted Glass & Animated Reveal) -->
    <header class="site-header" id="siteHeader">
        <div class="header-container">
            <!-- Brand Logo with Dynamic Uploaded Emblem & Logo Text -->
            <a href="<?= url('index.php') ?>" class="site-logo" title="<?= e($siteTitle) ?>">
                <div class="site-logo-img-wrapper">
                    <img src="<?= image_url($siteLogo) ?>" alt="<?= e($siteName) ?> Logo" class="site-logo-img" width="50" height="50">
                </div>
                <div class="site-logo-text">
                    <span class="site-logo-title"><?= e($logoP1) ?> <span><?= e($logoP2) ?></span></span>
                    <span class="site-logo-tagline"><?= e($logoTagline) ?></span>
                </div>
            </a>

            <!-- Navigation Bar with Micro-Animations -->
            <nav class="main-nav" aria-label="Main Navigation">
                <ul class="nav-menu" id="navMenu">
                    <li class="nav-item <?= is_active('home') ? 'active' : '' ?>">
                        <a href="<?= url('index.php') ?>" class="nav-link">Home</a>
                    </li>
                    <li class="nav-item <?= is_active('about') ? 'active' : '' ?>">
                        <a href="<?= url('about.php') ?>" class="nav-link">About Us</a>
                    </li>
                    
                    <!-- Trading with Dynamic Subpages Dropdown -->
                    <li class="nav-item has-dropdown <?= is_category_active('trading') ? 'active' : '' ?>">
                        <a href="<?= url('trading.php') ?>" class="nav-link">
                            Trading <span class="arrow-down">▾</span>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a href="<?= url('trading.php') ?>" class="dropdown-item"><strong>All Trading Overview</strong></a></li>
                            <?php foreach ($tradingSubpages as $tp): ?>
                                <li>
                                    <a href="<?= url('page.php?slug=' . urlencode($tp['slug'])) ?>" class="dropdown-item">
                                        <?= e($tp['title']) ?>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </li>

                    <!-- Finance with Dynamic Subpages Dropdown -->
                    <li class="nav-item has-dropdown <?= is_category_active('finance') ? 'active' : '' ?>">
                        <a href="<?= url('finance.php') ?>" class="nav-link">
                            Finance <span class="arrow-down">▾</span>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a href="<?= url('finance.php') ?>" class="dropdown-item"><strong>All Finance Overview</strong></a></li>
                            <?php foreach ($financeSubpages as $fp): ?>
                                <li>
                                    <a href="<?= url('page.php?slug=' . urlencode($fp['slug'])) ?>" class="dropdown-item">
                                        <?= e($fp['title']) ?>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </li>

                    <li class="nav-item <?= is_active('consulting') ? 'active' : '' ?>">
                        <a href="<?= url('consulting.php') ?>" class="nav-link">Consulting</a>
                    </li>
                    
                    <li class="nav-item <?= is_active('contact') ? 'active' : '' ?>">
                        <a href="<?= url('contact.php') ?>" class="nav-link">Contact</a>
                    </li>
                </ul>

                <div class="header-action">
                    <a href="<?= url('contact.php') ?>" class="btn-quote shimmer-btn">
                        <span>Get Consultation</span>
                        <span class="btn-arrow">→</span>
                    </a>
                </div>

                <!-- Mobile Hamburger Toggle Button -->
                <button class="mobile-toggle" id="mobileToggle" aria-label="Toggle Navigation Menu">
                    <span class="bar"></span>
                    <span class="bar"></span>
                    <span class="bar"></span>
                </button>
            </nav>
        </div>
    </header>
