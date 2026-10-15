<?php

declare(strict_types=1);

$pageTitle ??= 'Administration';
$pageEyebrow ??= 'Administration';
$activeAdminPage ??= '';
$pageAction ??= null;
$flash = $adminPullFlash();

$adminNavigation = [
    'dashboard' => ['Tableau de bord', '/admin/admin-index.php'],
    'vehicles' => ['Véhicules', '/admin/pages/vehicles.php'],
    'products' => ['Produits', '/admin/pages/products.php'],
    'services' => ['Services', '/admin/pages/services.php'],
    'contacts' => ['Demandes', '/admin/pages/contacts.php'],
    'hours' => ['Horaires', '/admin/pages/opening-hours.php'],
];
?>
<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="robots" content="noindex, nofollow, noarchive">
        <title><?= $escape($pageTitle) ?> | RGR France</title>
        <link rel="stylesheet" href="<?= $escape(BASE_URL) ?>/assets/css/style.css">
        <link rel="stylesheet" href="<?= $escape(BASE_URL) ?>/assets/css/admin/admin.css?v=2">
        <link rel="stylesheet" href="<?= $escape(BASE_URL) ?>/assets/css/admin/admin-pages.css?v=1">
    </head>
    <body class="admin-body">
        <div class="admin-shell">
            <aside class="admin-sidebar" id="admin-sidebar">
                <div class="admin-sidebar__brand">
                    <img src="<?= $escape(BASE_URL) ?>/assets/images/icons/logo-rgr-vector.svg" alt="RGR France">
                    <span>Administration</span>
                </div>
                <nav class="admin-navigation" aria-label="Administration">
                    <ul>
                        <?php foreach ($adminNavigation as $key => [$label, $href]): ?>
                            <li><a class="admin-navigation__link admin-navigation__link--text<?= $activeAdminPage === $key ? ' admin-navigation__link--active' : '' ?>" href="<?= $escape(BASE_URL . $href) ?>" <?= $activeAdminPage === $key ? 'aria-current="page"' : '' ?>><span><?= $escape($label) ?></span></a></li>
                        <?php endforeach; ?>
                    </ul>
                </nav>
                <form class="admin-sidebar__logout" action="<?= $escape(BASE_URL) ?>/admin/logout.php" method="post">
                    <input type="hidden" name="csrf_token" value="<?= $escape((string) ($_SESSION['admin_logout_csrf'] ?? $_SESSION['admin_csrf'])) ?>">
                    <button type="submit">Déconnexion</button>
                </form>
            </aside>
            <div class="admin-sidebar-overlay" data-sidebar-overlay></div>
            <div class="admin-workspace">
                <header class="admin-topbar">
                    <button class="admin-menu-button" type="button" aria-label="Ouvrir le menu" aria-controls="admin-sidebar" aria-expanded="false" data-admin-menu><span></span><span></span><span></span></button>
                    <img class="admin-topbar__logo" src="<?= $escape(BASE_URL) ?>/assets/images/icons/logo-rgr-vector.svg" alt="RGR France">
                    <div class="admin-topbar__account"><span class="admin-topbar__welcome">Bonjour, <?= $escape($adminName) ?></span><span class="admin-avatar" aria-hidden="true"><?= $escape(strtoupper(substr($adminName, 0, 1))) ?></span></div>
                </header>
                <main class="admin-main admin-page-main">
                    <header class="admin-page-heading">
                        <div><p><?= $escape($pageEyebrow) ?></p><h1><?= $escape($pageTitle) ?></h1></div>
                        <?php if (is_array($pageAction)): ?><a class="admin-primary-link" href="<?= $escape(BASE_URL . $pageAction['href']) ?>"><?= $escape($pageAction['label']) ?> <span aria-hidden="true">＋</span></a><?php endif; ?>
                    </header>
                    <?php if ($flash !== null): ?><div class="admin-flash admin-flash--<?= $escape($flash['type']) ?>" role="status"><?= $escape($flash['message']) ?></div><?php endif; ?>
