<?php
/**
 * Global Connect - Admin Topbar & HTML Head
 */
require_once __DIR__ . '/auth.php';

$pageTitle = $adminPageTitle ?? 'Admin Dashboard | Global Connect CMS';
$currentUser = current_user();
$siteFavicon = get_setting('site_favicon', get_setting('site_logo', 'logo-design-gcv.png'));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle) ?></title>
    <link rel="icon" type="image/png" href="<?= image_url($siteFavicon) ?>">
    <link rel="stylesheet" href="<?= asset('css/admin.css') ?>">
    <link rel="stylesheet" href="<?= asset('css/editor.css') ?>">
</head>
<body class="admin-body">

    <!-- Admin Sidebar Included -->
    <?php require_once __DIR__ . '/sidebar.php'; ?>

    <!-- Main Content Area -->
    <div class="admin-main">
        <!-- Top Navigation Bar -->
        <header class="admin-topbar">
            <div class="topbar-left">
                <h1 class="topbar-title"><?= e($adminPageHeading ?? 'Dashboard') ?></h1>
            </div>

            <div class="topbar-right">
                <a href="<?= url('index.php') ?>" target="_blank" class="btn-view-site">
                    <span>↗</span> View Public Website
                </a>

                <div class="admin-user-pill">
                    <div class="user-avatar">
                        <?= strtoupper(substr($currentUser['username'] ?? 'A', 0, 1)) ?>
                    </div>
                    <span><?= e($currentUser['full_name'] ?? $currentUser['username'] ?? 'Admin') ?></span>
                </div>
            </div>
        </header>

        <!-- Admin Main Content Container -->
        <main class="admin-content">
            <?php $flash = get_flash(); if ($flash): ?>
                <div class="alert alert-<?= e($flash['type']) ?>" style="background: <?= $flash['type'] === 'success' ? '#dcfce7' : '#fee2e2' ?>; color: <?= $flash['type'] === 'success' ? '#15803d' : '#b91c1c' ?>; padding: 14px 20px; border-radius: 8px; margin-bottom: 25px; display: flex; justify-content: space-between; align-items: center;">
                    <span><?= e($flash['message']) ?></span>
                </div>
            <?php endif; ?>
