<?php
/**
 * Global Connect - Header Component
 */
require_once __DIR__ . '/functions.php';

$siteTitle = get_setting('site_title', 'Global Connect | Global Business Enterprise Headquartered in Singapore & India');
$siteName = get_setting('company_name', 'Global Connect');
$siteTagline = get_setting('site_tagline', 'We are a global business enterprise headquartered in Singapore And India');
$phoneSg = get_setting('header_phone_sg', '(65) 89451100');
$phoneRoam = get_setting('header_phone_roam', '(91) 99100 02299');
$emailPrimary = get_setting('contact_email_primary', 'admin@globalconnectsg.com');
$emailSecondary = get_setting('contact_email_secondary', 'shammy@globalconnectsg.com');
$logoUrl = get_setting('logo_url', 'logo.svg');

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
    <meta name="description" content="<?= e($pageMetaDesc ?? 'We are a global business enterprise headquartered in Singapore And India. Reliable partner for international trade services, trade finance, and financial advisory.') ?>">
    <link rel="icon" type="image/svg+xml" href="<?= image_url('logo.svg') ?>">
    <link rel="stylesheet" href="<?= asset('css/style.css') ?>">
</head>
<body>

    <!-- Top Info Bar -->
    <div class="top-bar">
        <div class="top-bar-container">
            <div class="top-bar-left">
                <div class="top-bar-item">
                    <span class="tag">Singapore</span>
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
            <a href="<?= url('index.php') ?>" class="site-logo" title="<?= e($siteTitle) ?>">
                <img src="<?= image_url($logoUrl) ?>" alt="<?= e($siteName) ?> Logo" width="220" height="52">
            </a>

            <!-- Navigation Bar -->
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

                <!-- Mobile Hamburger Button -->
                <button class="mobile-toggle" id="mobileToggle" aria-label="Toggle Navigation Menu">
                    <span></span>
                    <span></span>
                    <span></span>
                </button>

                <!-- Header CTA Button -->
                <a href="<?= url('contact.php') ?>" class="btn-header-cta">Get in Touch →</a>
            </nav>
        </div>
    </header>
