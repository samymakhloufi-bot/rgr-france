<?php

declare(strict_types=1);

use App\Repository\VehicleRepository;

require_once __DIR__ . '/config/app.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/src/Entity/Vehicle.php';
require_once __DIR__ . '/src/Repository/VehicleRepository.php';

$activePage = 'Nos véhicules';
$metaDescription = 'Découvrez les véhicules américains disponibles et les projets suivis par RGR France.';
$pageCss = '/assets/css/pages/catalog.css';

$statusFilters = [
    'all' => [
        'id' => null,
        'label' => 'Tous',
        'cardLabel' => '',
        'modifier' => 'all',
    ],
    'available' => [
        'id' => 1,
        'label' => 'Disponibles',
        'cardLabel' => 'Disponible à l’achat',
        'modifier' => 'available',
    ],
    'homologation' => [
        'id' => 2,
        'label' => 'En homologation',
        'cardLabel' => 'En cours d’homologation',
        'modifier' => 'homologation',
    ],
    'service' => [
        'id' => 3,
        'label' => 'Service programmé',
        'cardLabel' => 'Service programmé',
        'modifier' => 'service',
    ],
    'returned' => [
        'id' => 4,
        'label' => 'Véhicules rendus',
        'cardLabel' => 'Véhicule rendu',
        'modifier' => 'returned',
    ],
];

$statusFilter = (string) ($_GET['status'] ?? 'all');

if (!array_key_exists($statusFilter, $statusFilters)) {
    $statusFilter = 'all';
}

$vehicles = [];
$coverImages = [];
$catalogError = null;

try {
    $vehicleRepository = new VehicleRepository($pdo);
    $vehicles = $vehicleRepository->findAllPublished();

    $vehicleIds = array_values(array_filter(array_map(
        static fn ($vehicle): ?int => $vehicle->getId(),
        $vehicles
    )));

    $coverImages = $vehicleRepository->findCoverImagesByVehicleIds($vehicleIds);
} catch (Throwable $exception) {
    error_log('Impossible de charger les véhicules : ' . $exception->getMessage());
    $catalogError = 'Les véhicules sont momentanément indisponibles.';
}

$selectedStatusId = $statusFilters[$statusFilter]['id'];

if ($selectedStatusId !== null) {
    $vehicles = array_values(array_filter(
        $vehicles,
        static fn ($vehicle): bool => $vehicle->getStatusId() === $selectedStatusId
    ));
}

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

$getStatusData = static function (int $statusId) use ($statusFilters): array {
    foreach ($statusFilters as $statusData) {
        if ($statusData['id'] === $statusId) {
            return $statusData;
        }
    }

    return [
        'label' => 'Suivi RGR',
        'cardLabel' => 'Suivi RGR',
        'modifier' => 'default',
    ];
};

$buildFilterUrl = static function (string $filter): string {
    return BASE_URL . '/vehicles.php?status=' . urlencode($filter);
};
?>
<!DOCTYPE html>
<html lang="fr">
    <?php include __DIR__ . '/includes/head.php'; ?>

    <body>
        <?php include __DIR__ . '/includes/header.php'; ?>

        <main class="catalog-page vehicle-catalog">
            <header class="catalog-hero catalog-hero--vehicles">
                <div class="catalog-container catalog-hero__content">
                    <p class="catalog-eyebrow">Sélection RGR France</p>
                    <h1 class="catalog-hero__title">Nos véhicules</h1>
                    <p class="catalog-hero__introduction">
                        Découvrez nos véhicules américains disponibles ainsi que
                        les projets accompagnés par notre équipe, de l’importation
                        jusqu’à la remise des clés.
                    </p>
                </div>
            </header>

            <section class="catalog-section" aria-labelledby="vehicle-catalog-title">
                <div class="catalog-container">
                    <div class="catalog-toolbar">
                        <div>
                            <p class="catalog-eyebrow">Le parc RGR</p>
                            <h2 class="catalog-section__title" id="vehicle-catalog-title">
                                Véhicules américains
                            </h2>
                            <p class="catalog-count" aria-live="polite">
                                <?= count($vehicles) ?>
                                <?= count($vehicles) > 1 ? 'véhicules' : 'véhicule' ?>
                            </p>
                        </div>

                        <nav class="catalog-filters" aria-label="Filtrer les véhicules">
                            <?php foreach ($statusFilters as $filterKey => $filterData): ?>
                                <a
                                    class="catalog-filter <?= $statusFilter === $filterKey ? 'catalog-filter--active' : '' ?>"
                                    href="<?= $escape($buildFilterUrl($filterKey)) ?>"
                                    <?= $statusFilter === $filterKey ? 'aria-current="page"' : '' ?>
                                >
                                    <?= $escape($filterData['label']) ?>
                                </a>
                            <?php endforeach; ?>
                        </nav>
                    </div>

                    <?php if ($catalogError !== null): ?>
                        <div class="catalog-message catalog-message--error" role="alert">
                            <?= $escape($catalogError) ?>
                        </div>
                    <?php elseif ($vehicles === []): ?>
                        <div class="catalog-message">
                            <h3>Aucun véhicule dans cette sélection</h3>
                            <p>Essayez un autre filtre ou revenez prochainement.</p>
                            <a href="<?= $escape($buildFilterUrl('all')) ?>">
                                Voir tous les véhicules
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="vehicle-grid">
                            <?php foreach ($vehicles as $vehicle): ?>
                                <?php
                                    $vehicleId = $vehicle->getId();
                                    $vehicleUrl = BASE_URL
                                        . '/vehicle.php?ref='
                                        . urlencode($vehicle->getInternalReference());
                                    $coverImage = $vehicleId === null
                                        ? null
                                        : ($coverImages[$vehicleId] ?? null);
                                    $statusData = $getStatusData($vehicle->getStatusId());
                                    $vehicleName = trim(
                                        $vehicle->getBrand()
                                        . ' '
                                        . $vehicle->getModel()
                                        . ' '
                                        . ($vehicle->getTrimLevel() ?? '')
                                    );
                                ?>
                                <article class="vehicle-card">
                                    <a
                                        class="vehicle-card__visual"
                                        href="<?= $escape($vehicleUrl) ?>"
                                        aria-label="Découvrir <?= $escape($vehicleName) ?>"
                                    >
                                        <?php if ($coverImage !== null): ?>
                                            <img
                                                class="vehicle-card__image"
                                                src="<?= $escape($getImageUrl($coverImage['file_path'])) ?>"
                                                alt="<?= $escape($coverImage['alt_text'] ?: $vehicleName) ?>"
                                                loading="lazy"
                                                decoding="async"
                                            >
                                        <?php else: ?>
                                            <span class="product-card__placeholder" aria-hidden="true">
                                                <span>RGR</span>
                                                France
                                            </span>
                                        <?php endif; ?>

                                        <span class="vehicle-card__status vehicle-card__status--<?= $escape($statusData['modifier']) ?>">
                                            <?= $escape($statusData['cardLabel']) ?>
                                        </span>
                                    </a>

                                    <div class="vehicle-card__body">
                                        <p class="vehicle-card__reference">
                                            Réf. <?= $escape($vehicle->getInternalReference()) ?>
                                        </p>

                                        <h3 class="vehicle-card__title">
                                            <a href="<?= $escape($vehicleUrl) ?>">
                                                <?= $escape($vehicle->getBrand()) ?>
                                                <strong><?= $escape($vehicle->getModel()) ?></strong>
                                            </a>
                                        </h3>

                                        <?php if ($vehicle->getTrimLevel() !== null): ?>
                                            <p class="vehicle-card__trim">
                                                <?= $escape($vehicle->getTrimLevel()) ?>
                                            </p>
                                        <?php endif; ?>

                                        <dl class="vehicle-card__specifications">
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

                                        <div class="vehicle-card__footer">
                                            <p class="vehicle-card__price">
                                                <span>Prix</span>
                                                <strong>
                                                    <?= $vehicle->getPrice() === null
                                                        ? 'Sur demande'
                                                        : $escape($formatPrice($vehicle->getPrice())) ?>
                                                </strong>
                                            </p>

                                            <a class="vehicle-card__link" href="<?= $escape($vehicleUrl) ?>">
                                                Découvrir
                                                <span aria-hidden="true">→</span>
                                            </a>
                                        </div>
                                    </div>
                                </article>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </section>

            <aside class="catalog-assurance" aria-label="Les engagements RGR France">
                <div class="catalog-container catalog-assurance__grid">
                    <p><span aria-hidden="true">01</span> Sélection experte</p>
                    <p><span aria-hidden="true">02</span> Homologation maîtrisée</p>
                    <p><span aria-hidden="true">03</span> Suivi personnalisé</p>
                </div>
            </aside>
        </main>

        <?php include __DIR__ . '/includes/footer.php'; ?>
    </body>
</html>
