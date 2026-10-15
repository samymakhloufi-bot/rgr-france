<?php

declare(strict_types=1);

use App\Repository\ProductRepository;
use App\Support\ImageUrl;


require_once __DIR__ . '/config/app.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/src/Entity/Product.php';
require_once __DIR__ . '/src/Repository/ProductRepository.php';
require_once __DIR__ . '/src/Support/ImageUrl.php';

$activePage = 'Nos produits';
$metaDescription = 'Découvrez les pièces et équipements sélectionnés par RGR France pour les véhicules américains.';
$pageCss = '/assets/css/pages/catalog.css';

$allowedFilters = ['all', 'in-stock', 'on-order'];
$availabilityFilter = (string) ($_GET['availability'] ?? 'all');

if (!in_array($availabilityFilter, $allowedFilters, true)) {
    $availabilityFilter = 'all';
}

$products = [];
$catalogError = null;

try {
    $productRepository = new ProductRepository($pdo);
    $products = $productRepository->findAllActive();
} catch (Throwable $exception) {
    error_log('Impossible de charger le catalogue : ' . $exception->getMessage());
    $catalogError = 'Le catalogue est momentanément indisponible.';
}

$products = array_values(array_filter(
    $products,
    static function ($product) use ($availabilityFilter): bool {
        return match ($availabilityFilter) {
            'in-stock' => $product->isInStock(),
            'on-order' => !$product->isInStock(),
            default => true,
        };
    }
));

$escape = static function (?string $value): string {
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
};

$formatPrice = static function (float $price): string {
    return number_format($price, 2, ',', ' ') . ' €';
};

$buildFilterUrl = static function (string $filter): string {
    return BASE_URL . '/nos-produits.php?availability=' . urlencode($filter);
};
?>
<!DOCTYPE html>
<html lang="fr">
    <?php include __DIR__ . '/includes/head.php'; ?>

    <body>
        <?php include __DIR__ . '/includes/header.php'; ?>

        <main class="catalog-page">
            <header class="catalog-hero">
                <div class="catalog-container catalog-hero__content">
                    <p class="catalog-eyebrow">Pièces &amp; équipements</p>
                    <h1 class="catalog-hero__title">Équipez votre américaine</h1>
                    <p class="catalog-hero__introduction">
                        Une sélection de pièces et d’équipements pour entretenir,
                        personnaliser et sublimer votre véhicule américain.
                    </p>
                </div>
            </header>

            <section class="catalog-section" aria-labelledby="catalog-title">
                <div class="catalog-container">
                    <div class="catalog-toolbar">
                        <div>
                            <p class="catalog-eyebrow">Catalogue RGR</p>
                            <h2 class="catalog-section__title" id="catalog-title">
                                Nos produits
                            </h2>
                            <p class="catalog-count" aria-live="polite">
                                <?= count($products) ?>
                                <?= count($products) > 1 ? 'produits' : 'produit' ?>
                            </p>
                        </div>

                        <nav class="catalog-filter-nav__link-nav" aria-label="Filtrer les produits">
                            <a
                                class="catalog-filter-nav__link <?= $availabilityFilter === 'all' ? 'catalog-filter-nav__link--active' : '' ?>"
                                href="<?= $escape($buildFilterUrl('all')) ?>"
                                <?= $availabilityFilter === 'all' ? 'aria-current="page"' : '' ?>
                            >
                                Tous
                            </a>
                            <a
                                class="catalog-filter-nav__link <?= $availabilityFilter === 'in-stock' ? 'catalog-filter-nav__link--active' : '' ?>"
                                href="<?= $escape($buildFilterUrl('in-stock')) ?>"
                                <?= $availabilityFilter === 'in-stock' ? 'aria-current="page"' : '' ?>
                            >
                                En stock
                            </a>
                            <a
                                class="catalog-filter-nav__link <?= $availabilityFilter === 'on-order' ? 'catalog-filter-nav__link--active' : '' ?>"
                                href="<?= $escape($buildFilterUrl('on-order')) ?>"
                                <?= $availabilityFilter === 'on-order' ? 'aria-current="page"' : '' ?>
                            >
                                Sur commande
                            </a>
                        </nav>
                    </div>

                    <?php if ($catalogError !== null): ?>
                        <div class="catalog-message catalog-message--error" role="alert">
                            <?= $escape($catalogError) ?>
                        </div>
                    <?php elseif ($products === []): ?>
                        <div class="catalog-message">
                            <h3>Aucun produit dans cette sélection</h3>
                            <p>Essayez un autre filtre ou revenez prochainement.</p>
                            <a href="<?= $escape($buildFilterUrl('all')) ?>">
                                Voir tous les produits
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="catalog-grid">
                            <?php foreach ($products as $product): ?>
                                <?php
                                    $productUrl = BASE_URL
                                        . '/product.php?slug='
                                        . urlencode($product->getSlug());
                                    $imagePath = $product->ImageUrl::resolve($product->getImagePath(), 'products');
                                ?>
                                <article class="catalog-card">
                                    <a
                                        class="catalog-card__visual"
                                        href="<?= $escape($productUrl) ?>"
                                        aria-label="Voir <?= $escape($product->getName()) ?>"
                                    >
                                        <?php if ($imagePath !== null && trim($imagePath) !== ''): ?>
                                            <img
                                                class="catalog-card__image"
                                                src="<?= $escape(ImageUrl::resolve($product->getImagePath(), 'products')) ?>"
                                                alt="<?= $escape($product->getName()) ?>"
                                                loading="lazy"
                                                decoding="async"
                                            >
                                        <?php else: ?>
                                            <span class="catalog-card__placeholder" aria-hidden="true">
                                                <span>RGR</span>
                                                France
                                            </span>
                                        <?php endif; ?>

                                        <span class="catalog-card__availability">
                                            <?= $product->isInStock() ? 'En stock' : 'Sur commande' ?>
                                        </span>
                                    </a>

                                    <div class="catalog-card__body">
                                        <?php if ($product->getSku() !== null): ?>
                                            <p class="catalog-card__reference">
                                                Réf. <?= $escape($product->getSku()) ?>
                                            </p>
                                        <?php endif; ?>

                                        <h3 class="catalog-card__title">
                                            <a href="<?= $escape($productUrl) ?>">
                                                <?= $escape($product->getName()) ?>
                                            </a>
                                        </h3>

                                        <div class="catalog-card__price">
                                            <?php if ($product->getPromotionalPrice() !== null): ?>
                                                <span class="catalog-card__price-old">
                                                    <?= $escape($formatPrice($product->getRegularPrice())) ?>
                                                </span>
                                            <?php endif; ?>

                                            <strong>
                                                <?= $escape($formatPrice($product->getCurrentPrice())) ?>
                                            </strong>
                                        </div>

                                        <dl class="catalog-card__details">
                                            <div>
                                                <dt>Disponibilité</dt>
                                                <dd>
                                                    <?= $product->isInStock()
                                                        ? $product->getStockQuantity() . ' en stock'
                                                        : 'Sur demande' ?>
                                                </dd>
                                            </div>
                                            <div>
                                                <dt>Accompagnement</dt>
                                                <dd>Conseil RGR</dd>
                                            </div>
                                        </dl>

                                        <a
                                            class="catalog-card__link"
                                            href="<?= $escape($productUrl) ?>"
                                        >
                                            Voir le produit
                                            <span aria-hidden="true">→</span>
                                        </a>
                                    </div>
                                </article>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </section>

            <aside class="catalog-assurance" aria-label="Les engagements RGR France">
                <div class="catalog-container catalog-assurance__grid">
                    <p><span aria-hidden="true">01</span> Compatibilité vérifiée</p>
                    <p><span aria-hidden="true">02</span> Montage professionnel</p>
                    <p><span aria-hidden="true">03</span> Conseil RGR</p>
                </div>
            </aside>
        </main>

        <?php include __DIR__ . '/includes/footer.php'; ?>
    </body>
</html>
