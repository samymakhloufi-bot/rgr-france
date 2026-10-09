<?php

declare(strict_types=1);

use App\Repository\VehicleRepository;

require_once __DIR__ . '/config/app.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/src/Entity/Vehicle.php';
require_once __DIR__ . '/src/Repository/VehicleRepository.php';

$pageCss = '/assets/css/pages/catalog.css';
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
        $vehicle = $vehicleRepository->findByInternalReference($internalReference);

        if ($vehicle === null || !$vehicle->isPublished()) {
            http_response_code(404);
            $vehicle = null;
            $pageError = 'Le véhicule demandé est introuvable ou n’est plus publié.';
        } elseif ($vehicle->getId() !== null) {
            $vehicleImages = $vehicleRepository->findImagesByVehicleId($vehicle->getId());
        }
    } catch (Throwable $exception) {
        error_log('Impossible de charger le véhicule : ' . $exception->getMessage());
        http_response_code(500);
        $vehicle = null;
        $pageError = 'Cette fiche est momentanément indisponible.';
    }
}

$vehicleName = $vehicle === null
    ? 'Véhicule introuvable'
    : trim($vehicle->getBrand() . ' ' . $vehicle->getModel());

$activePage = $vehicleName;
$metaDescription = $vehicle === null
    ? 'Le véhicule demandé est introuvable.'
    : 'Découvrez ' . $vehicleName . ' proposé et accompagné par RGR France.';

$escape = static function (?string $value): string {
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
};

$formatPrice = static function (float $price): string {
    return number_format($price, 0, ',', ' ') . ' €';
};

$formatMileage = static function (int $mileage): string {
    return number_format($mileage, 0, ',', ' ') . ' km';
};

$getImageUrl = static function (string $imagePath): string {
    if (filter_var($imagePath, FILTER_VALIDATE_URL) !== false) {
        return $imagePath;
    }

    return BASE_URL . '/' . ltrim($imagePath, '/');
};

$statusLabels = [
    1 => 'Disponible à l’achat',
    2 => 'En cours d’homologation',
    3 => 'Service programmé',
    4 => 'Véhicule rendu',
];
?>
<!DOCTYPE html>
<html lang="fr">
    <?php include __DIR__ . '/includes/head.php'; ?>

    <body>
        <?php include __DIR__ . '/includes/header.php'; ?>

        <main class="catalog-page vehicle-detail-page">
            <?php if ($vehicle === null): ?>
                <section class="vehicle-detail-error">
                    <div class="catalog-container catalog-message catalog-message--error">
                        <p class="catalog-eyebrow">Erreur</p>
                        <h1><?= $escape($pageError) ?></h1>
                        <a href="<?= $escape(BASE_URL) ?>/vehicles.php">
                            Retour aux véhicules
                        </a>
                    </div>
                </section>
            <?php else: ?>
                <?php
                    $statusLabel = $statusLabels[$vehicle->getStatusId()] ?? 'Suivi RGR France';
                    $contactUrl = BASE_URL
                        . '/contact.php?vehicle='
                        . urlencode($vehicle->getInternalReference());
                    $coverImage = $vehicleImages[0] ?? null;
                ?>

                <header class="vehicle-detail-header">
                    <div class="catalog-container">
                        <nav class="vehicle-breadcrumb" aria-label="Fil d’Ariane">
                            <a href="<?= $escape(BASE_URL) ?>/vehicles.php">Nos véhicules</a>
                            <span aria-hidden="true">/</span>
                            <span><?= $escape($vehicleName) ?></span>
                        </nav>

                        <div class="vehicle-detail-header__content">
                            <div>
                                <p class="catalog-eyebrow"><?= $escape($statusLabel) ?></p>
                                <h1 class="vehicle-detail-title">
                                    <?= $escape($vehicle->getBrand()) ?>
                                    <strong><?= $escape($vehicle->getModel()) ?></strong>
                                </h1>

                                <?php if ($vehicle->getTrimLevel() !== null): ?>
                                    <p class="vehicle-detail-trim">
                                        <?= $escape($vehicle->getTrimLevel()) ?>
                                    </p>
                                <?php endif; ?>
                            </div>

                            <div class="vehicle-detail-price">
                                <span>Prix affiché</span>
                                <strong>
                                    <?= $vehicle->getPrice() === null
                                        ? 'Sur demande'
                                        : $escape($formatPrice($vehicle->getPrice())) ?>
                                </strong>
                            </div>
                        </div>
                    </div>
                </header>

                <section class="vehicle-detail-content">
                    <div class="catalog-container vehicle-detail-layout">
                        <div class="vehicle-gallery">
                            <?php if ($coverImage !== null): ?>
                                <figure class="vehicle-gallery__main">
                                    <img
                                        src="<?= $escape($getImageUrl($coverImage['file_path'])) ?>"
                                        alt="<?= $escape($coverImage['alt_text'] ?: $vehicleName) ?>"
                                    >
                                </figure>

                                <?php if (count($vehicleImages) > 1): ?>
                                    <div class="vehicle-gallery__secondary">
                                        <?php foreach (array_slice($vehicleImages, 1) as $image): ?>
                                            <figure>
                                                <img
                                                    src="<?= $escape($getImageUrl($image['file_path'])) ?>"
                                                    alt="<?= $escape($image['alt_text'] ?: $vehicleName) ?>"
                                                    loading="lazy"
                                                    decoding="async"
                                                >
                                            </figure>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                            <?php else: ?>
                                <div class="vehicle-gallery__placeholder">
                                    <span>RGR</span>
                                    France
                                </div>
                            <?php endif; ?>
                        </div>

                        <aside class="vehicle-detail-summary" aria-label="Résumé du véhicule">
                            <p class="vehicle-detail-summary__reference">
                                Réf. <?= $escape($vehicle->getInternalReference()) ?>
                            </p>

                            <h2>Votre projet automobile</h2>
                            <p>
                                Notre équipe vous accompagne pour obtenir les informations
                                complémentaires, vérifier la disponibilité et organiser la
                                suite de votre projet.
                            </p>

                            <a class="vehicle-detail-cta" href="<?= $escape($contactUrl) ?>">
                                Demander des informations
                                <span aria-hidden="true">→</span>
                            </a>

                            <a class="vehicle-detail-back" href="<?= $escape(BASE_URL) ?>/vehicles.php">
                                ← Retour au catalogue
                            </a>
                        </aside>

                        <section class="vehicle-detail-description" aria-labelledby="vehicle-description-title">
                            <p class="catalog-eyebrow">Présentation</p>
                            <h2 id="vehicle-description-title">À propos de ce véhicule</h2>
                            <p>
                                <?= nl2br($escape(
                                    $vehicle->getDescription()
                                    ?? 'Contactez RGR France pour obtenir toutes les informations sur ce véhicule.'
                                )) ?>
                            </p>
                        </section>

                        <section class="vehicle-detail-specs" aria-labelledby="vehicle-specs-title">
                            <p class="catalog-eyebrow">Fiche technique</p>
                            <h2 id="vehicle-specs-title">Caractéristiques</h2>

                            <dl class="vehicle-specs-grid">
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
                                    <div><dt>Puissance</dt><dd><?= $vehicle->getPowerHp() ?> hp</dd></div>
                                <?php endif; ?>

                                <?php if ($vehicle->getExteriorColor() !== null): ?>
                                    <div><dt>Couleur</dt><dd><?= $escape($vehicle->getExteriorColor()) ?></dd></div>
                                <?php endif; ?>

                                <div><dt>Statut</dt><dd><?= $escape($statusLabel) ?></dd></div>
                            </dl>
                        </section>
                    </div>
                </section>

                <aside class="vehicle-detail-contact">
                    <div class="catalog-container vehicle-detail-contact__content">
                        <div>
                            <p class="catalog-eyebrow">Un projet en tête ?</p>
                            <h2>Parlons de votre future américaine</h2>
                        </div>
                        <a href="<?= $escape($contactUrl) ?>">Contacter RGR France</a>
                    </div>
                </aside>
            <?php endif; ?>
        </main>

        <?php include __DIR__ . '/includes/footer.php'; ?>
    </body>
</html>
