<?php

declare(strict_types=1);

use App\Repository\VehicleRepository;
use App\Support\ImageUrl;

require_once __DIR__ . '/config/bootstrap.php';

$pageCss = '/assets/css/pages/catalog-detail.css?v=2';
$internalReference = trim((string) ($_GET['ref'] ?? ''));
$vehicle = null;
$vehicleImages = [];
$pageError = null;

if ($internalReference === '' || strlen($internalReference) > 50) {
    http_response_code(404);
    $pageError = 'Le véhicule demandé est introuvable.';
} else {
    try {
        $vehicleRepository = new VehicleRepository($pdo);
        $vehicle = $vehicleRepository->findByInternalReference(
            $internalReference
        );

        if ($vehicle === null
    || !$vehicle->isPublished()
    || $vehicle->getStatusId() !== 1) {
            http_response_code(404);
            $pageError = 'Le véhicule demandé est introuvable ou n’est plus disponible.';
        } elseif ($vehicle->getId() !== null) {
            $vehicleImages = $vehicleRepository->findImagesByVehicleId(
                $vehicle->getId()
            );
        }
    } catch (Throwable $exception) {
        error_log(
            'Impossible de charger la fiche véhicule : '
            . $exception->getMessage()
        );

        http_response_code(500);
        $vehicle = null;
        $pageError = 'Cette fiche est momentanément indisponible.';
    }
}

$vehicleTitle = $vehicle === null
    ? 'Véhicule introuvable'
    : $vehicle->getModel();

$activePage = 'Nos véhicules';
$metaDescription = $vehicle === null
    ? 'Le véhicule demandé est introuvable.'
    : 'Découvrez le ' . $vehicle->getBrand() . ' ' . $vehicleTitle . ' proposé par RGR France.';

$escape = static function (mixed $value): string {
    return htmlspecialchars(
        (string) ($value ?? ''),
        ENT_QUOTES | ENT_SUBSTITUTE,
        'UTF-8'
    );
};

$formatPrice = static function (float $price): string {
    return number_format($price, 0, ',', ' ') . ' €';
};

$formatMileage = static function (int $mileage): string {
    return number_format($mileage, 0, ',', ' ') . ' km';
};

$contactUrl = $vehicle === null
    ? BASE_URL . '/contact.php'
    : BASE_URL
        . '/contact.php?vehicle='
        . urlencode($vehicle->getInternalReference());

$coverImage = $vehicleImages[0] ?? null;
?>
<!DOCTYPE html>
<html lang="fr">
    <?php include __DIR__ . '/includes/head.php'; ?>

    <body>
        <?php include __DIR__ . '/includes/header.php'; ?>

        <main class="catalog-detail catalog-detail--vehicle">
            <?php if ($vehicle === null): ?>
                <section class="catalog-detail__error">
                    <div class="catalog-container catalog-message catalog-message--error">
                        <p class="catalog-eyebrow">Erreur</p>
                        <h1><?= $escape($pageError) ?></h1>
                        <a href="<?= $escape(BASE_URL) ?>/vehicles.php">
                            Retour aux véhicules
                        </a>
                    </div>
                </section>
            <?php else: ?>
                <section class="catalog-detail__intro">
                    <div class="catalog-container">
                        <nav class="catalog-detail__breadcrumb" aria-label="Fil d’Ariane">
                            <a href="<?= $escape(BASE_URL) ?>/vehicles.php">
                                Nos véhicules
                            </a>
                            <span aria-hidden="true">›</span>
                            <span><?= $escape($vehicle->getBrand()) ?></span>
                            <span aria-hidden="true">›</span>
                            <span aria-current="page">
                                <?= $escape($vehicle->getModel()) ?>
                            </span>
                        </nav>

                        <p class="catalog-detail__brand">
                            <?= $escape($vehicle->getBrand()) ?>
                        </p>

                        <h1 class="catalog-detail__title">
                            <?= $escape($vehicleTitle) ?>
                        </h1>

                        <p class="catalog-detail__reference">
                            Réf. <?= $escape($vehicle->getInternalReference()) ?>
                        </p>
                    </div>
                </section>

                <section class="catalog-detail__content" aria-label="Présentation du véhicule">
                    <div class="catalog-container catalog-detail__layout">
                        <div class="catalog-detail__gallery" data-catalog-gallery>
                            <?php if ($coverImage !== null): ?>
                                <figure class="catalog-detail__visual">
                                    <img
                                        src="<?= $escape(ImageUrl::resolve($coverImage['file_path'], 'vehicles')) ?>"
                                        alt="<?= $escape($coverImage['alt_text'] ?: $vehicleTitle) ?>"
                                        data-gallery-main
                                    >

                                    <?php if (count($vehicleImages) > 1): ?>
                                        <button
                                            class="catalog-detail__gallery-control catalog-detail__gallery-control--previous"
                                            type="button"
                                            data-gallery-previous
                                            aria-label="Afficher la photo précédente"
                                        >
                                            ‹
                                        </button>

                                        <button
                                            class="catalog-detail__gallery-control catalog-detail__gallery-control--next"
                                            type="button"
                                            data-gallery-next
                                            aria-label="Afficher la photo suivante"
                                        >
                                            ›
                                        </button>
                                    <?php endif; ?>

                                    <span class="catalog-detail__counter" data-gallery-counter>
                                        1 / <?= count($vehicleImages) ?>
                                    </span>
                                </figure>

                                <?php if (count($vehicleImages) > 1): ?>
                                    <div class="catalog-detail__thumbnails" aria-label="Galerie du véhicule">
                                        <?php foreach ($vehicleImages as $index => $image): ?>
                                            <button
                                                class="catalog-detail__thumbnail<?= $index === 0 ? ' catalog-detail__thumbnail--active' : '' ?>"
                                                type="button"
                                                data-gallery-thumbnail
                                                data-image-src="<?= $escape(ImageUrl::resolve($image['file_path'], 'vehicles')) ?>"
                                                data-image-alt="<?= $escape($image['alt_text'] ?: $vehicleTitle) ?>"
                                                data-image-index="<?= $index ?>"
                                                aria-label="Afficher la photo <?= $index + 1 ?> sur <?= count($vehicleImages) ?>"
                                                aria-pressed="<?= $index === 0 ? 'true' : 'false' ?>"
                                            >
                                                <img
                                                    src="<?= $escape(ImageUrl::resolve($image['file_path'], 'vehicles')) ?>"
                                                    alt=""
                                                    loading="lazy"
                                                    decoding="async"
                                                >
                                            </button>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                            <?php else: ?>
                                <div class="catalog-detail__placeholder" aria-hidden="true">
                                    <span>RGR</span>
                                    France
                                </div>
                            <?php endif; ?>
                        </div>

                        <aside class="catalog-detail__summary" aria-label="Informations principales">
                            <div class="catalog-detail__price">
                                <strong>
                                    <?= $vehicle->getPrice() === null
                                        ? 'Sur demande'
                                        : $escape($formatPrice($vehicle->getPrice())) ?>
                                </strong>
                            </div>



                            <dl class="catalog-detail__specifications-grid catalog-detail__specifications-grid--summary">
                                <?php if ($vehicle->getRegistrationYear() !== null): ?>
                                    <div>
                                        <dt>Année</dt>
                                        <dd><?= $vehicle->getRegistrationYear() ?></dd>
                                    </div>
                                <?php endif; ?>

                                <?php if ($vehicle->getMileageKm() !== null): ?>
                                    <div>
                                        <dt>Kilométrage</dt>
                                        <dd><?= $escape($formatMileage($vehicle->getMileageKm())) ?></dd>
                                    </div>
                                <?php endif; ?>

                                <?php if ($vehicle->getFuelType() !== null): ?>
                                    <div>
                                        <dt>Énergie</dt>
                                        <dd><?= $escape($vehicle->getFuelType()) ?></dd>
                                    </div>
                                <?php endif; ?>

                                <?php if ($vehicle->getTransmission() !== null): ?>
                                    <div>
                                        <dt>Transmission</dt>
                                        <dd><?= $escape($vehicle->getTransmission()) ?></dd>
                                    </div>
                                <?php endif; ?>

                                <?php if ($vehicle->getPowerHp() !== null): ?>
                                    <div>
                                        <dt>Puissance</dt>
                                        <dd><?= $vehicle->getPowerHp() ?> ch</dd>
                                    </div>
                                <?php endif; ?>

                                <?php if ($vehicle->getExteriorColor() !== null): ?>
                                    <div>
                                        <dt>Couleur</dt>
                                        <dd><?= $escape($vehicle->getExteriorColor()) ?></dd>
                                    </div>
                                <?php endif; ?>
                            </dl>

                            <a class="catalog-detail__cta" href="<?= $escape($contactUrl) ?>">
                                Demander des informations
                                <span aria-hidden="true">→</span>
                            </a>
                        </aside>

                        <div class="catalog-detail__lower-content">
                            <article class="catalog-detail__description">
                                <h2>À propos de ce véhicule</h2>
                                <p>
                                    <?= nl2br($escape(
                                        $vehicle->getDescription()
                                        ?? 'Contactez RGR France pour obtenir toutes les informations concernant ce véhicule.'
                                    )) ?>
                                </p>
                            </article>

                            <section class="catalog-detail__specifications" aria-labelledby="vehicle-specifications-title">
                                <h2 id="vehicle-specifications-title">Caractéristiques</h2>

                                <dl class="catalog-detail__specifications-grid">
                                    <?php if ($vehicle->getRegistrationYear() !== null): ?>
                                        <div><dt>Année</dt><dd><?= $vehicle->getRegistrationYear() ?></dd></div>
                                    <?php endif; ?>
                                    <?php if ($vehicle->getMileageKm() !== null): ?>
                                        <div><dt>Kilométrage</dt><dd><?= $escape($formatMileage($vehicle->getMileageKm())) ?></dd></div>
                                    <?php endif; ?>
                                    <?php if ($vehicle->getFuelType() !== null): ?>
                                        <div><dt>Énergie</dt><dd><?= $escape($vehicle->getFuelType()) ?></dd></div>
                                    <?php endif; ?>
                                    <?php if ($vehicle->getTransmission() !== null): ?>
                                        <div><dt>Transmission</dt><dd><?= $escape($vehicle->getTransmission()) ?></dd></div>
                                    <?php endif; ?>
                                    <?php if ($vehicle->getPowerHp() !== null): ?>
                                        <div><dt>Puissance</dt><dd><?= $vehicle->getPowerHp() ?> ch</dd></div>
                                    <?php endif; ?>
                                    <?php if ($vehicle->getExteriorColor() !== null): ?>
                                        <div><dt>Couleur</dt><dd><?= $escape($vehicle->getExteriorColor()) ?></dd></div>
                                    <?php endif; ?>
                                </dl>
                            </section>
                        </div>
                    </div>
                </section>
            <?php endif; ?>
        </main>

        <?php include __DIR__ . '/includes/footer.php'; ?>

        <?php if ($vehicle !== null && count($vehicleImages) > 1): ?>
            <script src="<?= $escape(BASE_URL) ?>/assets/js/pages/catalog-detail-gallery.js" defer></script>
        <?php endif; ?>
    </body>
</html>
