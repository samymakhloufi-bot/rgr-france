<?php

declare(strict_types=1);

use App\Repository\DashboardRepository;
use App\Support\ImageUrl;

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

require_once dirname(__DIR__) . '/config/app.php';
require_once dirname(__DIR__) . '/config/database.php';
require_once dirname(__DIR__) . '/src/Repository/DashboardRepository.php';
require_once dirname(__DIR__) . '/src/Support/ImageUrl.php';

if (
    !isset($_SESSION['user_id'], $_SESSION['user_role'])
    || $_SESSION['user_role'] !== 'ADMIN'
) {
    header('Location: ' . BASE_URL . '/admin-login.php');
    exit;
}

$activeAdminPage = 'dashboard';
$adminName = trim((string) ($_SESSION['username'] ?? 'Administrateur'));
$dashboardError = null;
$statistics = [
    'vehicles' => 0,
    'products' => 0,
    'contacts' => 0,
    'services' => 0,
];
$recentContacts = [];
$openingHours = [];
$catalogItems = [];

try {
    $dashboardRepository = new DashboardRepository($pdo);
    $statistics = $dashboardRepository->getStatistics();
    $recentContacts = $dashboardRepository->findRecentContacts(5);
    $openingHours = $dashboardRepository->findOpeningHours();
    $catalogItems = $dashboardRepository->findCatalogPreview();
} catch (Throwable $exception) {
    error_log('Tableau de bord indisponible : ' . $exception->getMessage());
    $dashboardError = 'Certaines données du tableau de bord sont momentanément indisponibles.';
}

$_SESSION['admin_logout_csrf'] ??= bin2hex(random_bytes(32));

$escape = static function (?string $value): string {
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
};

$formatPrice = static function (?float $price): string {
    return $price === null
        ? 'Prix sur demande'
        : number_format($price, 0, ',', ' ') . ' €';
};

$formatDate = static function (?string $date): string {
    if ($date === null || $date === '') {
        return 'Date inconnue';
    }

    try {
        return (new DateTimeImmutable($date))->format('d/m/Y H:i');
    } catch (Throwable) {
        return $date;
    }
};

$formatTime = static function (?string $time): string {
    return $time === null || $time === '' ? '' : substr($time, 0, 5);
};

$imageUrl = ImageUrl::resolve(
    $item['image_path'] ?? null,
    'products'
);

$statusLabel = static function (?string $status): string {
    return match (strtolower(trim((string) $status))) {
        'processed', 'treated', 'closed', 'archived', 'traite', 'traité' => 'Traité',
        'in_progress', 'processing', 'en_cours' => 'En cours',
        default => 'À traiter',
    };
};

$statusModifier = static function (?string $status): string {
    return match (strtolower(trim((string) $status))) {
        'processed', 'treated', 'closed', 'archived', 'traite', 'traité' => 'processed',
        'in_progress', 'processing', 'en_cours' => 'progress',
        default => 'new',
    };
};

$adminLinks = [
    'dashboard' => ['label' => 'Tableau de bord', 'href' => BASE_URL . '/admin/admin-index.php', 'icon' => 'home'],
    'vehicles' => ['label' => 'Véhicules', 'href' => BASE_URL . '/admin/pages/vehicles.php', 'icon' => 'car'],
    'products' => ['label' => 'Produits', 'href' => BASE_URL . '/admin/pages/products.php', 'icon' => 'box'],
    'services' => ['label' => 'Services', 'href' => BASE_URL . '/admin/pages/services.php', 'icon' => 'tool'],
    'contacts' => ['label' => 'Demandes', 'href' => BASE_URL . '/admin/pages/contacts.php', 'icon' => 'mail'],
    'hours' => ['label' => 'Horaires', 'href' => BASE_URL . '/admin/pages/opening-hours.php', 'icon' => 'clock'],
];

$icons = [
    'home' => '<path d="M3 11.5 12 4l9 7.5"/><path d="M5.5 10v10h13V10M9.5 20v-6h5v6"/>',
    'car' => '<path d="m5 11 1.7-5h10.6l1.7 5M3 14v-2.5L5 10h14l2 1.5V14"/><path d="M5 14h14v5H5zM7 19v2m10-2v2M7.5 14.5h.01m8.99 0h.01"/>',
    'box' => '<path d="m4 7 8-4 8 4-8 4-8-4Z"/><path d="m4 7 8 4 8-4v10l-8 4-8-4V7Zm8 4v10"/>',
    'tool' => '<path d="M14.5 6.5a4 4 0 0 0-5-5L12 4 9 7 6.5 4.5a4 4 0 0 0 5 5L4 17l3 3 7.5-7.5a4 4 0 0 0 5-5L17 10l-3-3 2.5-2.5"/>',
    'mail' => '<rect x="3" y="5" width="18" height="14" rx="1"/><path d="m4 7 8 6 8-6"/>',
    'clock' => '<circle cx="12" cy="12" r="9"/><path d="M12 7v5l3.5 2"/>',
];

?>
<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="robots" content="noindex, nofollow, noarchive">
        <title>Tableau de bord | RGR France</title>

        <link rel="icon" href="<?= $escape(BASE_URL) ?>/assets/images/icons/logo-rgr-vector.svg" type="image/svg+xml">
        <link rel="stylesheet" href="<?= $escape(BASE_URL) ?>/assets/css/style.css">
        <link rel="stylesheet" href="<?= $escape(BASE_URL) ?>/assets/css/admin/admin.css?v=1">
    </head>

    <body class="admin-body">
        <a class="admin-skip-link" href="#admin-main">Aller au contenu</a>

        <div class="admin-shell">
            <aside class="admin-sidebar" id="admin-sidebar" aria-label="Navigation d’administration">
                <div class="admin-sidebar__brand">
                    <img src="<?= $escape(BASE_URL) ?>/assets/images/icons/logo-rgr-vector.svg" alt="RGR France">
                    <span>Administration</span>
                </div>

                <nav class="admin-navigation">
                    <ul>
                        <?php foreach ($adminLinks as $key => $link): ?>
                            <li>
                                <a
                                    class="admin-navigation__link<?= $activeAdminPage === $key ? ' admin-navigation__link--active' : '' ?>"
                                    href="<?= $escape($link['href']) ?>"
                                    <?= $activeAdminPage === $key ? 'aria-current="page"' : '' ?>
                                >
                                    <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false">
                                        <?= $icons[$link['icon']] ?>
                                    </svg>
                                    <span><?= $escape($link['label']) ?></span>

                                    <?php if ($key === 'contacts' && $statistics['contacts'] > 0): ?>
                                        <strong><?= (int) $statistics['contacts'] ?></strong>
                                    <?php endif; ?>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </nav>

                <form class="admin-sidebar__logout" action="<?= $escape(BASE_URL) ?>/admin/logout.php" method="post">
                    <input type="hidden" name="csrf_token" value="<?= $escape((string) $_SESSION['admin_logout_csrf']) ?>">
                    <button type="submit">
                        <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false">
                            <path d="M10 4H5v16h5M14 8l4 4-4 4m4-4H9" />
                        </svg>
                        Déconnexion
                    </button>
                </form>
            </aside>

            <div class="admin-sidebar-overlay" data-sidebar-overlay></div>

            <div class="admin-workspace">
                <header class="admin-topbar">
                    <button
                        class="admin-menu-button"
                        type="button"
                        aria-label="Ouvrir le menu d’administration"
                        aria-controls="admin-sidebar"
                        aria-expanded="false"
                        data-admin-menu
                    >
                        <span></span><span></span><span></span>
                    </button>

                    <img
                        class="admin-topbar__logo"
                        src="<?= $escape(BASE_URL) ?>/assets/images/icons/logo-rgr-vector.svg"
                        alt="RGR France"
                    >

                    <div class="admin-topbar__account">
                        <span class="admin-topbar__welcome">Bonjour, <?= $escape($adminName) ?></span>
                        <a class="admin-notification" href="<?= $escape(BASE_URL) ?>/admin/pages/contacts.php" aria-label="Voir les demandes à traiter">
                            <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false">
                                <path d="M18 9a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9M10 21h4" />
                            </svg>
                            <?php if ($statistics['contacts'] > 0): ?>
                                <span><?= min(99, (int) $statistics['contacts']) ?></span>
                            <?php endif; ?>
                        </a>
                        <span class="admin-avatar" aria-hidden="true">
                            <?= $escape(strtoupper(substr($adminName, 0, 1))) ?>
                        </span>
                    </div>
                </header>

                <main class="admin-main" id="admin-main">
                    <div class="admin-page-heading">
                        <div>
                            <p>Vue d’ensemble</p>
                            <h1>Tableau de bord</h1>
                        </div>
                        <a href="<?= $escape(BASE_URL) ?>" target="_blank" rel="noopener noreferrer">
                            Voir le site <span aria-hidden="true">↗</span>
                        </a>
                    </div>

                    <?php if ($dashboardError !== null): ?>
                        <div class="admin-alert" role="alert">
                            <?= $escape($dashboardError) ?>
                        </div>
                    <?php endif; ?>

                    <section class="admin-statistics" aria-label="Statistiques principales">
                        <a class="admin-stat-card" href="<?= $escape(BASE_URL) ?>/admin/pages/vehicles.php">
                            <svg viewBox="0 0 24 24" aria-hidden="true"><?= $icons['car'] ?></svg>
                            <strong><?= (int) $statistics['vehicles'] ?></strong>
                            <span>Véhicules publiés</span>
                            <i aria-hidden="true">→</i>
                        </a>

                        <a class="admin-stat-card" href="<?= $escape(BASE_URL) ?>/admin/pages/products.php">
                            <svg viewBox="0 0 24 24" aria-hidden="true"><?= $icons['box'] ?></svg>
                            <strong><?= (int) $statistics['products'] ?></strong>
                            <span>Produits actifs</span>
                            <i aria-hidden="true">→</i>
                        </a>

                        <a class="admin-stat-card admin-stat-card--alert" href="<?= $escape(BASE_URL) ?>/admin/pages/contacts.php">
                            <svg viewBox="0 0 24 24" aria-hidden="true"><?= $icons['mail'] ?></svg>
                            <strong><?= (int) $statistics['contacts'] ?></strong>
                            <span>Demandes à traiter</span>
                            <i aria-hidden="true">→</i>
                        </a>

                        <a class="admin-stat-card" href="<?= $escape(BASE_URL) ?>/admin/pages/services.php">
                            <svg viewBox="0 0 24 24" aria-hidden="true"><?= $icons['tool'] ?></svg>
                            <strong><?= (int) $statistics['services'] ?></strong>
                            <span>Services actifs</span>
                            <i aria-hidden="true">→</i>
                        </a>
                    </section>

                    <div class="admin-dashboard-grid">
                        <section class="admin-panel admin-panel--contacts" aria-labelledby="recent-contacts-title">
                            <header class="admin-panel__header">
                                <div>
                                    <p>Prises de contact</p>
                                    <h2 id="recent-contacts-title">Demandes récentes</h2>
                                </div>
                                <a href="<?= $escape(BASE_URL) ?>/admin/pages/contacts.php">Voir toutes <span aria-hidden="true">→</span></a>
                            </header>

                            <?php if ($recentContacts === []): ?>
                                <div class="admin-empty-state">
                                    <svg viewBox="0 0 24 24" aria-hidden="true"><?= $icons['mail'] ?></svg>
                                    <h3>Aucune demande</h3>
                                    <p>Les nouvelles demandes de contact apparaîtront ici.</p>
                                </div>
                            <?php else: ?>
                                <div class="admin-table-wrapper">
                                    <table class="admin-table">
                                        <thead>
                                            <tr>
                                                <th>Client</th>
                                                <th>Demande</th>
                                                <th>Date</th>
                                                <th>Statut</th>
                                                <th><span class="admin-visually-hidden">Action</span></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($recentContacts as $contact): ?>
                                                <?php
                                                    $contactName = trim(($contact['first_name'] ?? '') . ' ' . ($contact['last_name'] ?? ''));
                                                    $contactName = $contactName !== '' ? $contactName : ($contact['email'] ?? 'Contact');
                                                    $modifier = $statusModifier($contact['status'] ?? null);
                                                ?>
                                                <tr>
                                                    <td data-label="Client">
                                                        <strong><?= $escape($contactName) ?></strong>
                                                        <?php if (!empty($contact['email'])): ?>
                                                            <small><?= $escape($contact['email']) ?></small>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td data-label="Demande"><?= $escape($contact['excerpt'] ?? 'Demande de contact') ?></td>
                                                    <td data-label="Date"><?= $escape($formatDate($contact['created_at'] ?? null)) ?></td>
                                                    <td data-label="Statut">
                                                        <span class="admin-status admin-status--<?= $escape($modifier) ?>">
                                                            <?= $escape($statusLabel($contact['status'] ?? null)) ?>
                                                        </span>
                                                    </td>
                                                    <td class="admin-table__action">
                                                        <a href="<?= $escape(BASE_URL) ?>/admin/pages/contact.php?id=<?= (int) $contact['id'] ?>" aria-label="Consulter la demande de <?= $escape($contactName) ?>">Consulter</a>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php endif; ?>
                        </section>

                        <aside class="admin-panel admin-quick-actions" aria-labelledby="quick-actions-title">
                            <header class="admin-panel__header">
                                <div>
                                    <p>Gestion</p>
                                    <h2 id="quick-actions-title">Actions rapides</h2>
                                </div>
                            </header>
                            <a class="admin-action admin-action--primary" href="<?= $escape(BASE_URL) ?>/admin/pages/vehicle-form.php">
                                <svg viewBox="0 0 24 24" aria-hidden="true"><?= $icons['car'] ?></svg>
                                Ajouter un véhicule <span aria-hidden="true">→</span>
                            </a>
                            <a class="admin-action" href="<?= $escape(BASE_URL) ?>/admin/pages/product-form.php">
                                <svg viewBox="0 0 24 24" aria-hidden="true"><?= $icons['box'] ?></svg>
                                Ajouter un produit <span aria-hidden="true">→</span>
                            </a>
                            <a class="admin-action" href="<?= $escape(BASE_URL) ?>/admin/pages/opening-hours.php">
                                <svg viewBox="0 0 24 24" aria-hidden="true"><?= $icons['clock'] ?></svg>
                                Modifier les horaires <span aria-hidden="true">→</span>
                            </a>
                        </aside>

                        <section class="admin-panel admin-catalog-preview" aria-labelledby="catalog-preview-title">
                            <header class="admin-panel__header">
                                <div>
                                    <p>Contenu du site</p>
                                    <h2 id="catalog-preview-title">Catalogue</h2>
                                </div>
                            </header>

                            <div class="admin-catalog-grid">
                                <?php foreach ($catalogItems as $item): ?>
                                    <?php
                                        $imageDirectory = match ($item['type']) {
                                            'vehicle' => 'vehicles',
                                            'product' => 'products',
                                            default => null,
                                        };

                                        $imageUrl = ImageUrl::resolve(
                                            $item['image_path'] ?? null,
                                            $imageDirectory
                                        );
                                        $editPage = match ($item['type']) {
                                            'vehicle' => '/admin/pages/vehicle-form.php',
                                            'product' => '/admin/pages/product-form.php',
                                            default => '/admin/pages/service-form.php',
                                        };
                                    ?>
                                    <article class="admin-catalog-card">
                                        <div class="admin-catalog-card__visual">
                                            <?php if ($imageUrl !== null): ?>
                                                <img src="<?= $escape($imageUrl) ?>" alt="" loading="lazy">
                                            <?php else: ?>
                                                <svg viewBox="0 0 24 24" aria-hidden="true">
                                                    <?= $icons[$item['type'] === 'vehicle' ? 'car' : ($item['type'] === 'product' ? 'box' : 'tool')] ?>
                                                </svg>
                                            <?php endif; ?>
                                        </div>
                                        <div class="admin-catalog-card__body">
                                            <span><?= $escape($item['type_label']) ?></span>
                                            <h3><?= $escape($item['name']) ?></h3>
                                            <?php if (!empty($item['details'])): ?>
                                                <p><?= $escape($item['details']) ?></p>
                                            <?php endif; ?>
                                            <?php if (array_key_exists('price', $item)): ?>
                                                <strong><?= $escape($formatPrice($item['price'])) ?></strong>
                                            <?php endif; ?>
                                            <a href="<?= $escape(BASE_URL . $editPage) ?>?id=<?= (int) $item['id'] ?>">Modifier <span aria-hidden="true">→</span></a>
                                        </div>
                                    </article>
                                <?php endforeach; ?>

                                <?php if ($catalogItems === []): ?>
                                    <div class="admin-empty-state admin-empty-state--catalog">
                                        <h3>Catalogue vide</h3>
                                        <p>Ajoutez un véhicule, un produit ou un service.</p>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </section>

                        <aside class="admin-panel admin-hours" aria-labelledby="opening-hours-title">
                            <header class="admin-panel__header">
                                <div>
                                    <p>Le garage</p>
                                    <h2 id="opening-hours-title">Horaires d’ouverture</h2>
                                </div>
                                <a href="<?= $escape(BASE_URL) ?>/admin/pages/opening-hours.php" aria-label="Modifier les horaires">Modifier</a>
                            </header>

                            <?php if ($openingHours === []): ?>
                                <div class="admin-empty-state">
                                    <p>Aucun horaire enregistré.</p>
                                </div>
                            <?php else: ?>
                                <dl class="admin-hours__list">
                                    <?php foreach ($openingHours as $openingHour): ?>
                                        <div>
                                            <dt><?= $escape($openingHour['day_name']) ?></dt>
                                            <dd>
                                                <?php if ((bool) $openingHour['is_closed']): ?>
                                                    <span>Fermé</span>
                                                <?php else: ?>
                                                    <?php if (!empty($openingHour['morning_open'])): ?>
                                                        <?= $escape($formatTime($openingHour['morning_open'])) ?>–<?= $escape($formatTime($openingHour['morning_close'])) ?>
                                                    <?php endif; ?>
                                                    <?php if (!empty($openingHour['afternoon_open'])): ?>
                                                        <small>/</small>
                                                        <?= $escape($formatTime($openingHour['afternoon_open'])) ?>–<?= $escape($formatTime($openingHour['afternoon_close'])) ?>
                                                    <?php endif; ?>
                                                <?php endif; ?>
                                            </dd>
                                        </div>
                                    <?php endforeach; ?>
                                </dl>
                            <?php endif; ?>
                        </aside>
                    </div>
                </main>

                <nav class="admin-mobile-navigation" aria-label="Navigation mobile d’administration">
                    <?php foreach (array_slice($adminLinks, 0, 6, true) as $key => $link): ?>
                        <a
                            href="<?= $escape($link['href']) ?>"
                            class="<?= $activeAdminPage === $key ? 'admin-mobile-navigation__active' : '' ?>"
                            <?= $activeAdminPage === $key ? 'aria-current="page"' : '' ?>
                        >
                            <svg viewBox="0 0 24 24" aria-hidden="true"><?= $icons[$link['icon']] ?></svg>
                            <span><?= $escape($link['label']) ?></span>
                        </a>
                    <?php endforeach; ?>
                </nav>
            </div>
        </div>

        <script src="<?= $escape(BASE_URL) ?>/assets/js/admin/dashboard.js?v=1" defer></script>
    </body>
</html>
