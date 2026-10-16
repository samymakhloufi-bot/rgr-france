<?php

declare(strict_types=1);

use App\Repository\VehicleRepository;

require_once __DIR__ . '/config/bootstrap.php';

$activePage = 'Accueil';
$metaDescription = 'RGR France accompagne vos projets automobiles Ford et Lincoln : importation, homologation, entretien et véhicules américains.';
$pageCss = BASE_URL . '/assets/css/pages/index.css?v=1';

$featuredVehicles = [];
$vehicleCoverImages = [];

try {
    $vehicleRepository = new VehicleRepository($pdo);
    $featuredVehicles = array_slice(
        $vehicleRepository->findAllPublishedForSale(),
        0,
        3
    );

    $vehicleIds = array_values(array_filter(array_map(
        static fn ($vehicle): ?int => $vehicle->getId(),
        $featuredVehicles
    )));

    $vehicleCoverImages = $vehicleRepository->findCoverImagesByVehicleIds($vehicleIds);
} catch (Throwable $exception) {
    error_log('Impossible de charger les véhicules de l’accueil : ' . $exception->getMessage());
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

$getVehicleImageUrl = static function (string $imagePath): string {
    if (filter_var($imagePath, FILTER_VALIDATE_URL) !== false) {
        return $imagePath;
    }

    $imagePath = ltrim(str_replace('\\', '/', $imagePath), '/');

    if (str_starts_with($imagePath, 'assets/')) {
        return BASE_URL . '/' . $imagePath;
    }

    if (str_starts_with($imagePath, 'images/')) {
        return BASE_URL . '/assets/' . $imagePath;
    }

    return BASE_URL . '/assets/images/vehicles/' . $imagePath;
};
?>

<!DOCTYPE html>
<html lang="fr">
    <?php include __DIR__ . '/includes/head.php'; ?>

    <body>
        <?php include __DIR__ . '/includes/header.php'; ?>

        <main class="home-page">
            <section class="home-hero" aria-labelledby="home-title">
                <div class="home-container home-hero__content">
                    <p class="home-eyebrow">Expertise automobile américaine</p>

                    <h1 id="home-title">
                        L’Amérique,
                        <span>conduite par l’expertise</span>
                    </h1>

                    <p class="home-hero__description">
                        RGR France importe, homologue et vous accompagne pour
                        concrétiser votre projet Ford ou Lincoln en toute sérénité.
                    </p>

                    <div class="home-hero__actions">
                        <a class="home-button" href="#home-services">
                            Découvrir nos services
                            <span aria-hidden="true">→</span>
                        </a>

                        <a class="home-text-link" href="<?= $escape(BASE_URL) ?>/vehicles.php">
                            Voir nos véhicules
                            <span aria-hidden="true">→</span>
                        </a>
                    </div>
                </div>
            </section>

            <aside class="home-commitments" aria-label="Les expertises RGR France">
                <div class="home-container home-commitments__grid">
                    <article>
                        <svg viewBox="0 0 32 32" aria-hidden="true"><path d="M3 21h26l-4 7H8zM8 21V11h16v10M12 11V7h8v4"/></svg>
                        <div><h2>Importation sécurisée</h2><p>Un processus fiable et transparent des États-Unis jusqu’à chez vous.</p></div>
                    </article>
                    <article>
                        <svg viewBox="0 0 32 32" aria-hidden="true"><path d="M8 3h11l6 6v20H8zM19 3v7h6M12 17h9m-9 5h9"/></svg>
                        <div><h2>Homologation</h2><p>Une prise en charge complète pour rouler en toute légalité.</p></div>
                    </article>
                    <article>
                        <svg viewBox="0 0 32 32" aria-hidden="true"><circle cx="16" cy="16" r="11"/><path d="M9 16h14M16 9v14"/></svg>
                        <div><h2>Expertise Ford &amp; Lincoln</h2><p>Une connaissance approfondie du marché américain.</p></div>
                    </article>
                    <article>
                        <svg viewBox="0 0 32 32" aria-hidden="true"><path d="m3 15 6-6 6 5-5 6zm26 0-6-6-6 5 5 6zM11 18l5 5 5-5"/></svg>
                        <div><h2>Accompagnement sur mesure</h2><p>Un interlocuteur dédié à chaque étape de votre projet.</p></div>
                    </article>
                </div>
            </aside>

            <section class="home-services" id="home-services" aria-labelledby="home-services-title">
                <div class="home-container">
                    <div class="home-heading">
                        <div>
                            <p class="home-eyebrow">Nos services</p>
                            <h2 id="home-services-title">Un seul expert, toutes les étapes</h2>
                        </div>
                        <p>
                            De la recherche de votre véhicule aux démarches
                            d’homologation, en passant par le transport et l’entretien,
                            RGR France gère votre projet avec exigence et transparence.
                        </p>
                    </div>

                    <div class="home-services__grid">
                        <article class="home-service-card">
                            <img src="<?= $escape(BASE_URL) ?>/assets/images/home/home-importation.webp" alt="Transport maritime d’un véhicule depuis les États-Unis" loading="lazy">
                            <div>
                                <h3>Importation</h3>
                                <p>Nous recherchons et importons votre véhicule aux meilleures conditions depuis les États-Unis.</p>
                                <a href="<?= $escape(BASE_URL) ?>/importation.php">En savoir plus <span aria-hidden="true">→</span></a>
                            </div>
                        </article>

                        <article class="home-service-card">
                            <img src="<?= $escape(BASE_URL) ?>/assets/images/home/home-homologation.webp" alt="Contrôle d’un Ford F-150 pour son homologation" loading="lazy">
                            <div>
                                <h3>Homologation &amp; francisation</h3>
                                <p>Nous gérons l’ensemble des démarches pour une mise en conformité complète en France.</p>
                                <a href="<?= $escape(BASE_URL) ?>/homologation.php">En savoir plus <span aria-hidden="true">→</span></a>
                            </div>
                        </article>

                        <article class="home-service-card">
                            <img src="<?= $escape(BASE_URL) ?>/assets/images/home/home-maintenance.webp" alt="Entretien d’un moteur V8 Ford Mustang" loading="lazy">
                            <div>
                                <h3>Entretien &amp; optimisation</h3>
                                <p>Nous assurons l’entretien, le diagnostic et les optimisations de votre véhicule américain.</p>
                                <a href="<?= $escape(BASE_URL) ?>/contact.php?service=entretien">Nous consulter <span aria-hidden="true">→</span></a>
                            </div>
                        </article>
                    </div>
                </div>
            </section>

            <section class="home-vehicles" aria-labelledby="home-vehicles-title">
                <div class="home-container">
                    <div class="home-heading home-heading--dark">
                        <div>
                            <p class="home-eyebrow">Nos véhicules</p>
                            <h2 id="home-vehicles-title">Une sélection qui a du caractère</h2>
                        </div>
                        <p>Des Ford et Lincoln rigoureusement sélectionnés, prêts à prendre la route en France.</p>
                    </div>

                    <?php if ($featuredVehicles !== []): ?>
                        <div class="home-vehicles__grid">
                            <?php foreach ($featuredVehicles as $vehicle): ?>
                                <?php
                                    $vehicleId = $vehicle->getId();
                                    $coverImage = $vehicleId === null
                                        ? null
                                        : ($vehicleCoverImages[$vehicleId] ?? null);
                                    $vehicleName = trim(
                                        $vehicle->getBrand() . ' ' . $vehicle->getModel()
                                    );
                                    $vehicleUrl = BASE_URL . '/vehicle.php?ref=' . urlencode($vehicle->getInternalReference());
                                ?>
                                <article class="home-vehicle-card">
                                    <a class="home-vehicle-card__visual" href="<?= $escape($vehicleUrl) ?>">
                                        <?php if ($coverImage !== null): ?>
                                            <img
                                                src="<?= $escape($getVehicleImageUrl($coverImage['file_path'])) ?>"
                                                alt="<?= $escape($coverImage['alt_text'] ?: $vehicleName) ?>"
                                                loading="lazy"
                                            >
                                        <?php else: ?>
                                            <span class="home-vehicle-card__placeholder" aria-hidden="true">RGR France</span>
                                        <?php endif; ?>
                                    </a>

                                    <div class="home-vehicle-card__body">
                                        <h3>
                                            <a href="<?= $escape($vehicleUrl) ?>">
                                                <?= $escape($vehicleName) ?>
                                            </a>
                                        </h3>

                                        <p class="home-vehicle-card__summary">
                                            <?php if ($vehicle->getRegistrationYear() !== null): ?>
                                                <span><?= $vehicle->getRegistrationYear() ?></span>
                                            <?php endif; ?>
                                            <?php if ($vehicle->getPowerHp() !== null): ?>
                                                <span><?= $vehicle->getPowerHp() ?> ch</span>
                                            <?php endif; ?>
                                            <?php if ($vehicle->getTransmission() !== null): ?>
                                                <span><?= $escape($vehicle->getTransmission()) ?></span>
                                            <?php endif; ?>
                                        </p>

                                        <p class="home-vehicle-card__price">
                                            <?= $vehicle->getPrice() === null
                                                ? 'Prix sur demande'
                                                : $escape($formatPrice($vehicle->getPrice())) ?>
                                        </p>

                                        <div class="home-vehicle-card__details">
                                            <?php if ($vehicle->getMileageKm() !== null): ?>
                                                <span><?= $escape($formatMileage($vehicle->getMileageKm())) ?></span>
                                            <?php endif; ?>
                                            <?php if ($vehicle->getFuelType() !== null): ?>
                                                <span><?= $escape($vehicle->getFuelType()) ?></span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </article>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <p class="home-vehicles__empty">Notre sélection évolue régulièrement. Découvrez prochainement nos nouveaux véhicules.</p>
                    <?php endif; ?>

                    <a class="home-outline-button" href="<?= $escape(BASE_URL) ?>/vehicles.php">
                        Voir tous les véhicules
                        <span aria-hidden="true">→</span>
                    </a>
                </div>
            </section>

            <section class="home-expertise" aria-labelledby="home-expertise-title">
                <div class="home-container home-expertise__layout">
                    <div class="home-expertise__introduction">
                        <p class="home-eyebrow">Notre engagement</p>
                        <h2 id="home-expertise-title">Pourquoi RGR France&nbsp;?</h2>
                        <p>Vous offrir le meilleur de l’automobile américaine avec un service fiable, transparent et personnalisé.</p>
                    </div>

                    <img src="<?= $escape(BASE_URL) ?>/assets/images/home/home-expertise.webp" alt="Contrôle technique d’un véhicule américain dans l’atelier RGR France" loading="lazy">

                    <ul class="home-expertise__list">
                        <li><strong>Transparence</strong><span>Des informations claires à chaque étape, sans mauvaise surprise.</span></li>
                        <li><strong>Expertise technique</strong><span>Une connaissance approfondie des véhicules Ford et Lincoln.</span></li>
                        <li><strong>Suivi de bout en bout</strong><span>Un interlocuteur unique du premier contact à la livraison.</span></li>
                    </ul>
                </div>
            </section>

            <section class="home-cta" aria-labelledby="home-cta-title">
                <div class="home-container home-cta__content">
                    <div>
                        <p class="home-eyebrow">Votre projet</p>
                        <h2 id="home-cta-title">Parlons de votre projet</h2>
                        <p>Échangez avec un expert RGR France et obtenez un accompagnement adapté à votre besoin.</p>
                    </div>
                    <a class="home-button" href="<?= $escape(BASE_URL) ?>/contact.php">
                        Nous contacter
                        <span aria-hidden="true">→</span>
                    </a>
                </div>
            </section>
        </main>

        <?php include __DIR__ . '/includes/footer.php'; ?>
    </body>
</html>
