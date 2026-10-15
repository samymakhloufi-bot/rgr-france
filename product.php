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
$pageCss = BASE_URL . '/assets/css/pages/catalog-detail.css?v=4';

$slug = trim((string) ($_GET['slug'] ?? ''));
$product = null;
$pageError = null;

if ($slug === '' || strlen($slug) > 190) {
    http_response_code(404);
    $pageError = 'Le produit demandé est introuvable.';
} else {
    try {
        $productRepository = new ProductRepository($pdo);
        $product = $productRepository->findActiveBySlug($slug);

        if ($product === null) {
            http_response_code(404);
            $pageError = 'Le produit demandé est introuvable ou n’est plus disponible.';
        }
    } catch (Throwable $exception) {
        error_log(
            'Impossible de charger la fiche produit : '
            . $exception->getMessage()
        );

        http_response_code(500);
        $product = null;
        $pageError = 'Cette fiche est momentanément indisponible.';
    }
}

$productName = $product === null
    ? 'Produit introuvable'
    : $product->getName();

$metaDescription = $product === null
    ? 'Le produit demandé est introuvable.'
    : 'Découvrez ' . $productName . ' sélectionné par RGR France.';

$escape = static function (mixed $value): string {
    return htmlspecialchars(
        (string) ($value ?? ''),
        ENT_QUOTES | ENT_SUBSTITUTE,
        'UTF-8'
    );
};

$formatPrice = static function (float $price): string {
    return number_format($price, 2, ',', ' ') . ' €';
};

$contactUrl = $product === null
    ? BASE_URL . '/contact.php'
    : BASE_URL
        . '/contact.php?product='
        . urlencode($product->getSlug());

$imageUrl = ImageUrl::resolve(
    $product?->getImagePath(),
    'products'
);

?>

<!DOCTYPE html>
<html lang="fr">
    <?php include __DIR__ . '/includes/head.php'; ?>

    <body>
        <?php include __DIR__ . '/includes/header.php'; ?>

        <main class="catalog-detail catalog-detail--product">
            <?php if ($product === null): ?>
                <section class="catalog-detail__error">
                    <div class="catalog-container catalog-message catalog-message--error">
                        <p class="catalog-eyebrow">Erreur</p>
                        <h1><?= $escape($pageError) ?></h1>
                        <a href="<?= $escape(BASE_URL) ?>/nos-produits.php">
                            Retour aux produits
                        </a>
                    </div>
                </section>
            <?php else: ?>
                <section class="catalog-detail__intro">
                    <div class="catalog-container">
                        <nav
                            class="catalog-detail__breadcrumb"
                            aria-label="Fil d’Ariane"
                        >
                            <a href="<?= $escape(BASE_URL) ?>/nos-produits.php">
                                Nos produits
                            </a>
                            <span aria-hidden="true">›</span>
                            <span aria-current="page">
                                <?= $escape($productName) ?>
                            </span>
                        </nav>

                        <p class="catalog-detail__brand">
                            Pièces &amp; équipements
                        </p>

                        <h1 class="catalog-detail__title">
                            <?= $escape($productName) ?>
                        </h1>

                        <?php if ($product->getSku() !== null): ?>
                            <p class="catalog-detail__reference">
                                Réf. <?= $escape($product->getSku()) ?>
                            </p>
                        <?php endif; ?>
                    </div>
                </section>

                <section
                    class="catalog-detail__content"
                    aria-label="Présentation du produit"
                >
                    <div class="catalog-container catalog-detail__layout">
                        <div class="catalog-detail__gallery">
                            <?php if ($imageUrl !== null && trim($imageUrl) !== ''): ?>
                                <figure class="catalog-detail__visual">
                                    <img
                                        src="<?= $escape($imageUrl) ?>"
                                        alt="<?= $escape($productName) ?>"
                                        decoding="async"
                                    >
                                </figure>
                            <?php else: ?>
                                <div
                                    class="catalog-detail__placeholder"
                                    aria-hidden="true"
                                >
                                    <span>RGR</span>
                                    France
                                </div>
                            <?php endif; ?>
                        </div>

                        <aside
                            class="catalog-detail__summary"
                            aria-label="Informations principales"
                        >
                            <div class="catalog-detail__price">
                                <?php if ($product->getPromotionalPrice() !== null): ?>
                                    <span class="catalog-detail__price-old">
                                        <?= $escape($formatPrice($product->getRegularPrice())) ?>
                                    </span>
                                <?php else: ?>
                                    <span>Prix TTC</span>
                                <?php endif; ?>

                                <strong>
                                    <?= $escape($formatPrice($product->getCurrentPrice())) ?>
                                </strong>
                            </div>

                            <?php if ($product->getSku() !== null): ?>
                                <p class="catalog-detail__reference">
                                    Réf. <?= $escape($product->getSku()) ?>
                                </p>
                            <?php endif; ?>

                            <dl class="catalog-detail__specifications-grid catalog-detail__specifications-grid--summary">
                                <div>
                                    <dt>État</dt>
                                    <dd>
                                        <?= $product->isInStock()
                                            ? 'En stock'
                                            : 'Sur commande' ?>
                                    </dd>
                                </div>
                                <div>
                                    <dt>Quantité</dt>
                                    <dd>
                                        <?= $product->isInStock()
                                            ? $product->getStockQuantity() . ' disponible(s)'
                                            : 'Sur demande' ?>
                                    </dd>
                                </div>
                                <div>
                                    <dt>Compatibilité</dt>
                                    <dd>Vérifiée par RGR</dd>
                                </div>
                            </dl>

                            <a
                                class="catalog-detail__cta"
                                href="<?= $escape($contactUrl) ?>"
                            >
                                Demander des informations
                                <span aria-hidden="true">→</span>
                            </a>

                            <a
                                class="catalog-detail__back"
                                href="<?= $escape(BASE_URL) ?>/nos-produits.php"
                            >
                                ← Retour aux produits
                            </a>
                        </aside>

                        <div class="catalog-detail__lower-content">
                            <article
                                class="catalog-detail__description"
                                aria-labelledby="product-description-title"
                            >
                                <h2 id="product-description-title">
                                    À propos de ce produit
                                </h2>
                                <p>
                                    <?= nl2br($escape(
                                        $product->getDescription()
                                        ?? 'Contactez RGR France pour connaître les caractéristiques et la compatibilité de ce produit avec votre véhicule.'
                                    )) ?>
                                </p>
                            </article>

                            <section
                                class="catalog-detail__specifications"
                                aria-labelledby="product-service-title"
                            >
                                <h2 id="product-service-title">
                                    Conseil et installation
                                </h2>

                                <dl class="catalog-detail__specifications-grid">
                                    <div>
                                        <dt>Compatibilité</dt>
                                        <dd>Contrôlée avant commande</dd>
                                    </div>
                                    <div>
                                        <dt>Installation</dt>
                                        <dd>Disponible au garage</dd>
                                    </div>
                                    <div>
                                        <dt>Accompagnement</dt>
                                        <dd>Conseil personnalisé</dd>
                                    </div>
                                </dl>
                            </section>
                        </div>
                    </div>
                </section>

            <?php endif; ?>
        </main>

        <?php include __DIR__ . '/includes/footer.php'; ?>
    </body>
</html>
