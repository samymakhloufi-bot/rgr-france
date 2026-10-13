<?php

declare(strict_types=1);

use App\Repository\ProductRepository;

require_once __DIR__ . '/config/app.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/src/Entity/Product.php';
require_once __DIR__ . '/src/Repository/ProductRepository.php';

$pageCss = '/assets/css/pages/catalog-detail.css';
$slug = trim((string) ($_GET['slug'] ?? ''));
$product = null;
$pageError = null;

if ($slug === '' || strlen($slug) > 190) {
    http_response_code(404);
    $pageError = 'Le produit demandé est introuvable.';
} else {
    try {
        $productRepository = new ProductRepository($pdo);
        $product = $productRepository->findBySlug($slug);

        if ($product === null) {
            http_response_code(404);
            $pageError = 'Le produit demandé est introuvable ou n’est plus disponible.';
        }
    } catch (Throwable $exception) {
        error_log('Impossible de charger le produit : ' . $exception->getMessage());
        http_response_code(500);
        $product = null;
        $pageError = 'Cette fiche est momentanément indisponible.';
    }
}

$productName = $product === null
    ? 'Produit introuvable'
    : $product->getName();

$activePage = 'Nos produits';
$metaDescription = $product === null
    ? 'Le produit demandé est introuvable.'
    : 'Découvrez ' . $productName . ' sélectionné par RGR France.';

$escape = static function (?string $value): string {
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
};

$formatPrice = static function (float $price): string {
    return number_format($price, 2, ',', ' ') . ' €';
};

$getImageUrl = static function (string $imagePath): string {
    if (filter_var($imagePath, FILTER_VALIDATE_URL) !== false) {
        return $imagePath;
    }

    return BASE_URL . '/' . ltrim($imagePath, '/');
};

$contactUrl = $product === null
    ? BASE_URL . '/contact.php'
    : BASE_URL . '/contact.php?product=' . urlencode($product->getSlug());
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
                <header class="catalog-detail__header">
                    <div class="catalog-container">
                        <nav class="catalog-detail__breadcrumb" aria-label="Fil d’Ariane">
                            <a href="<?= $escape(BASE_URL) ?>/nos-produits.php">
                                Nos produits
                            </a>
                            <span aria-hidden="true">/</span>
                            <span><?= $escape($productName) ?></span>
                        </nav>

                        <div class="catalog-detail__heading">
                            <div>
                                <p class="catalog-eyebrow">Pièces &amp; équipements</p>
                                <h1 class="catalog-detail__title">
                                    <?= $escape($productName) ?>
                                </h1>

                                <?php if ($product->getSku() !== null): ?>
                                    <p class="catalog-detail__subtitle">
                                        Réf. <?= $escape($product->getSku()) ?>
                                    </p>
                                <?php endif; ?>
                            </div>

                            <div class="catalog-detail__price">
                                <?php if ($product->getPromotionalPrice() !== null): ?>
                                    <span class="catalog-detail__price-old">
                                        <?= $escape($formatPrice($product->getRegularPrice())) ?>
                                    </span>
                                <?php else: ?>
                                    <span>Prix affiché</span>
                                <?php endif; ?>

                                <strong>
                                    <?= $escape($formatPrice($product->getCurrentPrice())) ?>
                                </strong>
                            </div>
                        </div>
                    </div>
                </header>

                <section class="catalog-detail__content">
                    <div class="catalog-container catalog-detail__layout">
                        <div class="catalog-detail__gallery">
                            <?php if ($product->getImagePath() !== null): ?>
                                <figure class="catalog-detail__visual">
                                    <img
                                        src="<?= $escape($getImageUrl($product->getImagePath())) ?>"
                                        alt="<?= $escape($productName) ?>"
                                    >
                                </figure>
                            <?php else: ?>
                                <div class="catalog-detail__placeholder" aria-hidden="true">
                                    <span>RGR</span>
                                    France
                                </div>
                            <?php endif; ?>
                        </div>

                        <aside class="catalog-detail__summary" aria-label="Résumé du produit">
                            <?php if ($product->getSku() !== null): ?>
                                <p class="catalog-detail__reference">
                                    Réf. <?= $escape($product->getSku()) ?>
                                </p>
                            <?php endif; ?>

                            <h2>Disponibilité</h2>

                            <dl class="catalog-detail__specifications-grid">
                                <div>
                                    <dt>État</dt>
                                    <dd><?= $product->isInStock() ? 'En stock' : 'Sur commande' ?></dd>
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
                                    <dt>Accompagnement</dt>
                                    <dd>Conseil RGR</dd>
                                </div>
                            </dl>

                            <a class="catalog-detail__cta" href="<?= $escape($contactUrl) ?>">
                                Demander des informations
                                <span aria-hidden="true">→</span>
                            </a>

                            <a class="catalog-detail__back" href="<?= $escape(BASE_URL) ?>/nos-produits.php">
                                ← Retour aux produits
                            </a>
                        </aside>

                        <section class="catalog-detail__description" aria-labelledby="product-description-title">
                            <p class="catalog-eyebrow">Présentation</p>
                            <h2 id="product-description-title">À propos de ce produit</h2>
                            <p>
                                <?= nl2br($escape(
                                    $product->getDescription()
                                    ?? 'Contactez RGR France pour obtenir toutes les informations concernant ce produit.'
                                )) ?>
                            </p>
                        </section>
                    </div>
                </section>

                <aside class="catalog-detail__contact">
                    <div class="catalog-container catalog-detail__contact-content">
                        <div>
                            <p class="catalog-eyebrow">Besoin d’un conseil ?</p>
                            <h2>Vérifions la compatibilité avec votre véhicule</h2>
                        </div>
                        <a href="<?= $escape($contactUrl) ?>">Contacter RGR France</a>
                    </div>
                </aside>
            <?php endif; ?>
        </main>

        <?php include __DIR__ . '/includes/footer.php'; ?>
    </body>
</html>
