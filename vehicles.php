<?php

declare(strict_types=1);

use App\Repository\VehicleRepository;
use App\Support\ImageUrl;

require_once __DIR__ . '/config/app.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/src/Entity/Vehicle.php';
require_once __DIR__ . '/src/Repository/VehicleRepository.php';
require_once __DIR__ . '/src/Support/ImageUrl.php';

$activePage = 'Nos véhicules';
$metaDescription = 'Découvrez les véhicules américains disponibles et les projets suivis par RGR France.';
$pageCss = '/assets/css/pages/catalog.css';


$vehicles = [];
$coverImages = [];
$catalogError = null;

try {
    $vehicleRepository = new VehicleRepository($pdo);
    $vehicles = $vehicleRepository->findAllPublishedForSale();
} catch (Throwable $exception) {
    error_log('Impossible de charger les véhicules : ' . $exception->getMessage());
    $catalogError = $exception->getMessage();
}

if ($vehicles !== []) {
    $vehicleIds = array_values(array_filter(array_map(
        static fn ($vehicle): ?int => $vehicle->getId(),
        $vehicles
    )));

    try {
        $coverImages = $vehicleRepository->findCoverImagesByVehicleIds($vehicleIds);
    } catch (Throwable $exception) {
        error_log(
            'Impossible de charger les images des véhicules : '
            . $exception->getMessage()
        );
    }
}

$fuelTypes = [];
$mileageOptions = [];
$powerOptions = [];

foreach ($vehicles as $vehicle) {
    $fuelType = $vehicle->getFuelType();
    $mileage = $vehicle->getMileageKm();
    $power = $vehicle->getPowerHp();

    if ($fuelType !== null && !in_array($fuelType, $fuelTypes, true)) {
        $fuelTypes[] = $fuelType;
    }

    if ($mileage !== null && !in_array($mileage, $mileageOptions, true)) {
        $mileageOptions[] = $mileage;
    }

    if ($power !== null && !in_array($power, $powerOptions, true)) {
        $powerOptions[] = $power;
    }
}

sort($fuelTypes, SORT_NATURAL | SORT_FLAG_CASE);
sort($mileageOptions, SORT_NUMERIC);
sort($powerOptions, SORT_NUMERIC);

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
                            <p
                                class="catalog-count"
                                id="vehicle-result-count"
                                aria-live="polite"
                            >
                                <?= count($vehicles) ?>
                                véhicule<?= count($vehicles) > 1 ? 's' : '' ?>
                                disponible<?= count($vehicles) > 1 ? 's' : '' ?>
                            </p>
                        </div>
                    </div>
                    <?php if ($catalogError === null): ?>
                        <form class="catalog-filter-nav__link-form" id="catalog-filter-nav__link-form" role="search">
                            <div class="catalog-filter-nav__link-field catalog-filter-nav__link-field--search">
                                <label for="vehicle-search">
                                    Rechercher un véhicule
                                </label>

                                <div class="catalog-search-control">
                                    <input
                                        type="search"
                                        id="vehicle-search"
                                        name="search"
                                        placeholder="Ex. Mustang, F-150, Raptor…"
                                        autocomplete="off"
                                    >
                                    <span aria-hidden="true">⌕</span>
                                </div>
                            </div>

                            <div class="catalog-filter-nav__link-field">
                                <label for="vehicle-fuel">Carburant</label>

                                <select id="vehicle-fuel" name="fuel">
                                    <option value="">Tous</option>

                                    <?php foreach ($fuelTypes as $fuelType): ?>
                                        <option value="<?= $escape(strtolower($fuelType)) ?>">
                                            <?= $escape($fuelType) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="catalog-filter-nav__link-field">
                                <label for="vehicle-mileage">
                                    Kilométrage maximum
                                </label>

                                <select id="vehicle-mileage" name="mileage">
                                    <option value="">Indifférent</option>

                                    <?php foreach ($mileageOptions as $mileage): ?>
                                        <option value="<?= $mileage ?>">
                                            Jusqu’à <?= $escape($formatMileage($mileage)) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="catalog-filter-nav__link-field">
                                <label for="vehicle-power">
                                    Puissance minimum
                                </label>

                                <select id="vehicle-power" name="power">
                                    <option value="">Indifférente</option>

                                    <?php foreach ($powerOptions as $power): ?>
                                        <option value="<?= $power ?>">
                                            À partir de <?= $power ?> ch
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <button class="catalog-filter-nav__link-form__reset" type="reset">
                                Réinitialiser
                            </button>
                        </form>
                    <?php endif; ?>
                    <?php if ($catalogError !== null): ?>
                        <div class="catalog-message catalog-message--error" role="alert">
                            <?= $escape($catalogError) ?>
                        </div>
                    <?php elseif ($vehicles === []): ?>
                        <div class="catalog-message">
                            <h3>Aucun véhicule dans cette sélection</h3>
                            <p>Essayez un autre filtre ou revenez prochainement.</p>
                        </div>
                    <?php else: ?>
                        <div class="catalog-grid" id="catalog-grid">
                            <?php foreach ($vehicles as $vehicle): ?>
                                <?php
                                    $vehicleId = $vehicle->getId();
                                    $vehicleUrl = BASE_URL
                                        . '/vehicle.php?ref='
                                        . urlencode($vehicle->getInternalReference());
                                    $coverImage = $vehicleId === null
                                        ? null
                                        : ($coverImages[$vehicleId] ?? null);
                                    $vehicleName = trim(
                                        $vehicle->getBrand()
                                        . ' '
                                        . $vehicle->getModel()
                                        . ' '
                                        . ($vehicle->getTrimLevel() ?? '')
                                    );
                                    $searchableDetails = implode(' ', array_filter([
                                        $vehicleName,
                                        $vehicle->getInternalReference(),
                                        $vehicle->getRegistrationYear(),
                                        $vehicle->getFuelType(),
                                        $vehicle->getTransmission(),
                                            $vehicle->getMileageKm(),
                                            $vehicle->getPowerHp(),
                                            $vehicle->getExteriorColor(),
                                            $vehicle->getPrice(),
                                            $vehicle->getDescription(),
                                        ], static fn ($value): bool => $value !== null && $value !== ''));
                                ?>
                                <article
                                    class="catalog-card"
                                    data-catalog-card
                                    data-search="<?= $escape(strtolower($searchableDetails)) ?>"
                                    data-fuel="<?= $escape(strtolower($vehicle->getFuelType() ?? '')) ?>"
                                    data-mileage="<?= $vehicle->getMileageKm() ?? -1 ?>"
                                    data-power="<?= $vehicle->getPowerHp() ?? 0 ?>"
                                >
                                    <a
                                        class="catalog-card__visual"
                                        href="<?= $escape($vehicleUrl) ?>"
                                        aria-label="Découvrir <?= $escape($vehicleName) ?>"
                                    >
                                        <?php if ($coverImage !== null): ?>
                                            <img
                                                class="catalog-card__image"
                                                src="<?= $escape(ImageUrl::resolve($coverImage['file_path'], 'vehicles')) ?>"
                                                alt="<?= $escape($coverImage['alt_text'] ?: $vehicleName) ?>"
                                                loading="lazy"
                                                decoding="async"
                                            >
                                        <?php else: ?>
                                            <span class="catalog-card__placeholder" aria-hidden="true">
                                                <span>RGR</span>
                                                France
                                            </span>
                                        <?php endif; ?>

                                        <span class="catalog-card__status catalog-card__status--available">
                                            Disponible à l’achat
                                        </span>
                                    </a>

                                    <div class="catalog-card__body">
                                        <p class="catalog-card__reference">
                                            Réf. <?= $escape($vehicle->getInternalReference()) ?>
                                        </p>

                                        <h3 class="catalog-card__title">
                                            <a href="<?= $escape($vehicleUrl) ?>">
                                                <?= $escape($vehicle->getBrand()) ?>
                                                <strong><?= $escape($vehicle->getModel()) ?></strong>
                                            </a>
                                        </h3>

                                        <?php if ($vehicle->getTrimLevel() !== null): ?>
                                            <p class="catalog-card__trim">
                                                <?= $escape($vehicle->getTrimLevel()) ?>
                                            </p>
                                        <?php endif; ?>

                                        <dl class="catalog-card__specifications">
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

                                        <div class="catalog-card__footer">
                                            <p class="catalog-card__price">
                                                <span>Prix</span>
                                                <strong>
                                                    <?= $vehicle->getPrice() === null
                                                        ? 'Sur demande'
                                                        : $escape($formatPrice($vehicle->getPrice())) ?>
                                                </strong>
                                            </p>

                                            <a class="catalog-card__link" href="<?= $escape($vehicleUrl) ?>">
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
        <script src= "<?= $escape(BASE_URL) ?>/assets/js/pages/catalogue-vehicle.js" defer></script>
    </body>
</html>
