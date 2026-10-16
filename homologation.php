<?php

declare(strict_types=1);

$activePage = 'Homologation';
$metaDescription = 'RGR France prend en charge l’homologation et la francisation de votre véhicule américain, des dossiers DREAL et UTAC à l’immatriculation.';
$pageCss = '/assets/css/pages/services.css';

require_once __DIR__ . '/config/bootstrap.php';
?>

<!DOCTYPE html>
<html lang="fr">

<?php include __DIR__ . '/includes/head.php'; ?>

<body>
    <?php include __DIR__ . '/includes/header.php'; ?>

    <main class="service-page service-page--homologation">
        <section class="service-hero" aria-labelledby="homologation-title">
            <div class="service-hero__media" aria-hidden="true"></div>

            <div class="service-hero__content">
                <p class="service-eyebrow">Service RGR France</p>

                <h1 class="service-hero__title" id="homologation-title">
                    Homologation
                    <span>&amp; francisation</span>
                </h1>

                <p class="service-hero__price">
                    <span>À partir de</span>
                    5 500&nbsp;€
                </p>

                <p class="service-hero__description">
                    Confiez-nous votre véhicule et bénéficiez d’une prise en
                    charge intégrale. Notre technique brevetée permet une
                    transformation propre, maîtrisée et totalement réversible,
                    sans altérer l’intégrité du véhicule.
                </p>

                <a class="service-button" href="/contact.php">
                    Étudier mon véhicule
                    <span aria-hidden="true">→</span>
                </a>
            </div>
        </section>

        <section class="service-introduction">
            <div class="service-container service-introduction__layout">
                <div>
                    <p class="service-eyebrow">De l’étude à l’immatriculation</p>

                    <h2>Une prise en charge intégrale</h2>
                </div>

                <p>
                    De la préparation du dossier jusqu’à la mise en conformité,
                    la francisation et les démarches administratives, RGR France
                    accompagne chaque étape afin de rendre votre véhicule conforme
                    aux normes françaises et prêt à être immatriculé.
                </p>
            </div>
        </section>

        <section class="service-process" aria-labelledby="services-title">
            <div class="service-container service-process__layout">
                <div class="service-process__content">
                    <p class="service-eyebrow">Notre intervention</p>
                    <h2 id="services-title">Prestations incluses</h2>

                    <ol class="service-steps">
                        <li>
                            <span class="service-steps__number">01</span>
                            <div>
                                <h3>Dossiers DREAL &amp; UTAC</h3>
                                <p>Constitution, dépôt et suivi complet des dossiers.</p>
                            </div>
                        </li>

                        <li>
                            <span class="service-steps__number">02</span>
                            <div>
                                <h3>Diagnostic complet</h3>
                                <p>Analyse du véhicule et identification des anomalies éventuelles.</p>
                            </div>
                        </li>

                        <li>
                            <span class="service-steps__number">03</span>
                            <div>
                                <h3>Mise à jour des calculateurs</h3>
                                <p>Contrôle des calibrations et suppression des modifications non déclarées.</p>
                            </div>
                        </li>

                        <li>
                            <span class="service-steps__number">04</span>
                            <div>
                                <h3>Procédures constructeur</h3>
                                <p>Réalisation des procédures de service et réapprentissages nécessaires.</p>
                            </div>
                        </li>

                        <li>
                            <span class="service-steps__number">05</span>
                            <div>
                                <h3>Francisation des systèmes</h3>
                                <p>Configuration du GPS, de la radio et des calculateurs pour la France.</p>
                            </div>
                        </li>

                        <li>
                            <span class="service-steps__number">06</span>
                            <div>
                                <h3>Frappe à froid supervisée</h3>
                                <p>Réalisation sous supervision officielle de Ford France Homologation.</p>
                            </div>
                        </li>

                        <li>
                            <span class="service-steps__number">07</span>
                            <div>
                                <h3>Contrôle technique</h3>
                                <p>Présentation du véhicule au contrôle technique obligatoire.</p>
                            </div>
                        </li>

                        <li>
                            <span class="service-steps__number">08</span>
                            <div>
                                <h3>Transformation pour les essais</h3>
                                <p>Adaptation du véhicule pour valider les essais DREAL et UTAC.</p>
                            </div>
                        </li>

                        <li>
                            <span class="service-steps__number">09</span>
                            <div>
                                <h3>Remise à l’état d’origine</h3>
                                <p>Après validation, le véhicule vous est restitué dans sa configuration d’origine.</p>
                            </div>
                        </li>
                    </ol>
                </div>

                <div class="service-process__visual">
                    <picture>
                        <source
                            media="(max-width: 759px)"
                            srcset="/assets/images/homologation/homologation-atelier-mobile.webp"
                        >
                        <img
                            src="/assets/images/homologation/homologation-atelier.webp"
                            alt="Ford Raptor en cours de contrôle dans l’atelier RGR France"
                            loading="lazy"
                        >
                    </picture>

                    <article class="service-highlight">
                        <p class="service-eyebrow">Technique brevetée</p>
                        <h2>Une transformation 100&nbsp;% réversible</h2>
                        <p>
                            Les adaptations sont réalisées sans altérer l’habitacle,
                            la carrosserie ou les faisceaux électriques. Après les
                            essais réglementaires, le véhicule retrouve strictement
                            son état d’origine.
                        </p>
                    </article>
                </div>
            </div>
        </section>

        <section class="service-support" aria-labelledby="support-title">
            <div class="service-support__layout">
                <picture class="service-support__picture">
                    <source
                        media="(max-width: 759px)"
                        srcset="/assets/images/homologation/homologation-detail-raptor-mobile.webp"
                    >
                    <img
                        class="service-support__image"
                        src="/assets/images/homologation/homologation-detail-raptor.webp"
                        alt="Détail intérieur d’un Ford Raptor"
                        loading="lazy"
                    >
                </picture>

                <div class="service-support__content">
                    <p class="service-eyebrow">Au-delà de l’homologation</p>
                    <h2 id="support-title">Accompagnement complémentaire</h2>

                    <p>
                        Si notre équipe détecte une anomalie, un défaut ou un besoin
                        particulier, elle vous conseille sur les solutions les plus
                        adaptées afin de garantir un véhicule fiable, propre et valorisé.
                    </p>
                </div>

                <ul class="service-support__list">
                    <li>
                        <img class="service-support__icon" src="/assets/images/icons/service-wrench.svg" alt="" aria-hidden="true">
                        <div>
                            <strong>Conseils d’entretien</strong>
                            <span>Pour une utilisation fiable et durable.</span>
                        </div>
                    </li>
                    <li>
                        <img class="service-support__icon" src="/assets/images/icons/service-gear.svg" alt="" aria-hidden="true">
                        <div>
                            <strong>Réparation</strong>
                            <span>Une expertise dédiée aux véhicules américains.</span>
                        </div>
                    </li>
                    <li>
                        <img class="service-support__icon" src="/assets/images/icons/service-chart.svg" alt="" aria-hidden="true">
                        <div>
                            <strong>Améliorations</strong>
                            <span>Optimisations et personnalisations adaptées.</span>
                        </div>
                    </li>
                </ul>
            </div>
        </section>

        <section class="service-cta" aria-labelledby="service-cta-title">
            <div class="service-container service-cta__content">
                <img
                    class="service-cta__vehicle"
                    src="/assets/images/homologation/homologation-vehicle-cutout.webp"
                    alt=""
                    aria-hidden="true"
                >

                <div class="service-cta__copy">
                    <p class="service-eyebrow">Votre projet</p>
                    <h2 id="service-cta-title">Votre véhicule est-il homologable&nbsp;?</h2>
                    <p>Présentez-nous votre véhicule pour obtenir une première étude personnalisée.</p>
                </div>

                <a class="service-button" href="/contact.php">
                    Nous contacter
                    <span aria-hidden="true">→</span>
                </a>
            </div>
        </section>
    </main>

    <?php include __DIR__ . '/includes/footer.php'; ?>
</body>

</html>
