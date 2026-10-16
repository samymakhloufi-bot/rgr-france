<?php

declare(strict_types=1);

require_once __DIR__ . '/config/bootstrap.php';

$activePage = 'Importation';
$metaDescription = 'RGR France vous accompagne pour rechercher, acheter, transporter et réceptionner votre véhicule américain en toute sécurité.';
$pageCss = '/assets/css/pages/services.css?v=importation-1';
?>

<!DOCTYPE html>
<html lang="fr">
    <?php include __DIR__ . '/includes/head.php'; ?>

    <body>
        <?php include __DIR__ . '/includes/header.php'; ?>

        <main class="service-page service-page--importation">
            <section class="import-hero" aria-labelledby="importation-title">
                <div class="service-container import-hero__content">
                    <p class="service-eyebrow">Importation sur mesure</p>

                    <h1 id="importation-title">
                        Votre américaine,
                        <span>sans intermédiaire</span>
                    </h1>

                    <p class="import-hero__description">
                        Un achat transparent et sécurisé pour importer votre Ford
                        ou Lincoln des États-Unis, au juste prix.
                    </p>

                    <a class="service-button" href="/contact.php?service=importation">
                        Démarrer mon projet
                        <span aria-hidden="true">→</span>
                    </a>

                    <p class="import-hero__price">
                        <span aria-hidden="true"></span>
                        Prestation RGR — 1&nbsp;200&nbsp;€
                    </p>
                </div>
            </section>

            <section class="import-process" aria-labelledby="import-process-title">
                <div class="service-container">
                    <div class="import-section-heading">
                        <div>
                            <p class="service-eyebrow">Notre méthode</p>
                            <h2 id="import-process-title">Un accompagnement de bout en bout</h2>
                        </div>

                        <p>
                            De la définition de votre projet à la réception de votre
                            véhicule en France, nous gérons chaque étape pour un import
                            en toute sérénité.
                        </p>
                    </div>

                    <ol class="import-process__list">
                        <li>
                            <div class="import-process__step"><strong>01</strong><span></span></div>
                            <svg viewBox="0 0 32 32" aria-hidden="true"><path d="M6 7h20v14H14l-6 5v-5H6zM11 14h2m4 0h2m4 0h2"/></svg>
                            <h3>Projet &amp; budget</h3>
                            <p>Nous définissons ensemble votre projet, vos critères et votre budget.</p>
                        </li>
                        <li>
                            <div class="import-process__step"><strong>02</strong><span></span></div>
                            <svg viewBox="0 0 32 32" aria-hidden="true"><circle cx="14" cy="14" r="8"/><path d="m20 20 7 7"/></svg>
                            <h3>Recherche</h3>
                            <p>Nous identifions les meilleurs véhicules sur le marché américain.</p>
                        </li>
                        <li>
                            <div class="import-process__step"><strong>03</strong><span></span></div>
                            <svg viewBox="0 0 32 32" aria-hidden="true"><path d="M8 3h11l6 6v20H8zM19 3v7h6M12 16h9m-9 5h9"/></svg>
                            <h3>Sélection &amp; CARFAX</h3>
                            <p>Nous analysons l’historique et l’état du véhicule avec un rapport CARFAX.</p>
                        </li>
                        <li>
                            <div class="import-process__step"><strong>04</strong><span></span></div>
                            <svg viewBox="0 0 32 32" aria-hidden="true"><path d="m3 15 6-6 6 5-5 6zm26 0-6-6-6 5 5 6zM11 18l5 5 5-5m-8 3-3 3m9-3 3 3"/></svg>
                            <h3>Achat sécurisé</h3>
                            <p>Nous gérons l’achat directement auprès du vendeur sélectionné.</p>
                        </li>
                        <li>
                            <div class="import-process__step"><strong>05</strong><span></span></div>
                            <svg viewBox="0 0 32 32" aria-hidden="true"><path d="M3 21h26l-4 7H8zM8 21V11h16v10M12 11V7h8v4M3 28c3-2 5-2 8 0 3-2 5-2 8 0 3-2 5-2 8 0"/></svg>
                            <h3>Transport &amp; suivi</h3>
                            <p>Nous organisons le transport et assurons un suivi jusqu’au Havre.</p>
                        </li>
                        <li>
                            <div class="import-process__step"><strong>06</strong><span></span></div>
                            <svg viewBox="0 0 32 32" aria-hidden="true"><rect x="5" y="4" width="22" height="25" rx="2"/><path d="m11 17 4 4 7-9"/></svg>
                            <h3>Réception &amp; contrôle</h3>
                            <p>Nous réceptionnons le véhicule et réalisons un contrôle complet à son arrivée.</p>
                        </li>
                    </ol>
                </div>
            </section>

            <section class="import-transparency" aria-labelledby="import-transparency-title">
                <img
                    src="/assets/images/importation/importation-inspection.webp"
                    alt="Contrôle du châssis d’un Ford F-150 dans l’atelier"
                    loading="lazy"
                >

                <div class="import-transparency__content">
                    <p class="service-eyebrow">Transparence</p>
                    <h2 id="import-transparency-title">Achetez au juste prix</h2>
                    <p>
                        Nous achetons votre véhicule directement aux États-Unis,
                        sans intermédiaire commercial et sans surcoût inutile.
                        Vous bénéficiez du prix réel du marché et d’un accompagnement
                        expert à chaque étape.
                    </p>
                    <span class="import-red-rule" aria-hidden="true"></span>
                    <p>
                        Notre rôle est de sécuriser votre achat, de vérifier l’historique
                        et l’état du véhicule, et d’assurer un import en toute confiance,
                        du premier contact jusqu’à la livraison au Havre.
                    </p>
                </div>
            </section>

            <section class="import-security" aria-labelledby="import-security-title">
                <div class="service-container">
                    <div class="import-section-heading import-section-heading--dark">
                        <div>
                            <p class="service-eyebrow">Sécurité et sérénité</p>
                            <h2 id="import-security-title">Chaque étape est sécurisée</h2>
                        </div>
                        <p>
                            Nous mettons en place des solutions fiables et reconnues
                            pour sécuriser votre importation, de l’historique du véhicule
                            jusqu’au paiement et aux documents officiels.
                        </p>
                    </div>

                    <div class="import-security__grid">
                        <article>
                            <strong class="import-security__brand import-security__brand--ford">Ford</strong>
                            <h3>Ford Blue Advantage</h3>
                            <p>Nous privilégions les véhicules du réseau officiel Ford aux États-Unis pour plus de sérénité.</p>
                        </article>
                        <article>
                            <strong class="import-security__brand import-security__brand--carfax">CARFAX</strong>
                            <h3>CARFAX</h3>
                            <p>Un rapport d’historique complet pour connaître le passé du véhicule : accidents, entretien, kilométrage.</p>
                        </article>
                        <article>
                            <strong class="import-security__brand import-security__brand--hsbc"><span></span> HSBC</strong>
                            <h3>Paiements internationaux HSBC</h3>
                            <p>Des transactions sécurisées et traçables via notre partenaire bancaire de confiance.</p>
                        </article>
                        <article>
                            <svg class="import-security__document" viewBox="0 0 32 32" aria-hidden="true"><path d="M8 3h11l6 6v20H8zM19 3v7h6M12 17h9m-9 5h9"/></svg>
                            <h3>Certificate of Title</h3>
                            <p>Nous vérifions et sécurisons tous les documents officiels, notamment le Certificate of Title.</p>
                        </article>
                    </div>
                </div>
            </section>

            <section class="import-journey" aria-labelledby="import-journey-title">
                <div class="service-container">
                    <div class="import-section-heading">
                        <div>
                            <p class="service-eyebrow">Du départ à la réception</p>
                            <h2 id="import-journey-title">Des États-Unis jusqu’au Havre</h2>
                        </div>
                        <p>
                            Nous organisons l’ensemble de la logistique maritime et
                            terrestre pour que votre véhicule arrive en France dans les
                            meilleures conditions, puis nous assurons sa réception et un
                            contrôle complet.
                        </p>
                    </div>

                    <ol class="import-route" aria-label="Étapes du transport">
                        <li><span>🇺🇸</span><strong>Achat aux<br>États-Unis</strong></li>
                        <li><span>▰</span><strong>Transport<br>maritime</strong></li>
                        <li><span>⚓</span><strong>Arrivée<br>au Havre</strong></li>
                        <li><span>▣</span><strong>Réception<br>du véhicule</strong></li>
                        <li><span>⌕</span><strong>Contrôle<br>complet</strong></li>
                        <li><span>◆</span><strong>Conseils<br>homologation &amp; entretien</strong></li>
                    </ol>

                    <div class="import-journey__cards">
                        <article>
                            <img src="/assets/images/importation/importation-transport.webp" alt="Transport maritime par porte-conteneurs" loading="lazy">
                            <p>Nous organisons le transport maritime de votre véhicule jusqu’au Havre avec un suivi régulier.</p>
                        </article>
                        <article>
                            <img src="/assets/images/importation/importation-reception.webp" alt="Réception d’un Ford F-150 au port" loading="lazy">
                            <p>À son arrivée, nous réceptionnons le véhicule et effectuons un contrôle complet.</p>
                        </article>
                        <article>
                            <img src="/assets/images/importation/importation-control.webp" alt="Contrôle final du véhicule importé" loading="lazy">
                            <p>Nous vous conseillons ensuite sur les démarches d’homologation et l’entretien de votre véhicule en France.</p>
                        </article>
                    </div>
                </div>
            </section>

            <section class="import-cta" aria-labelledby="import-cta-title">
                <div class="service-container import-cta__content">
                    <div>
                        <h2 id="import-cta-title">Parlons de votre projet d’importation</h2>
                        <p>Échangez avec un expert RGR France pour étudier votre projet et obtenir un accompagnement sur mesure.</p>
                    </div>
                    <a class="service-button" href="/contact.php?service=importation">
                        Contacter un expert
                        <span aria-hidden="true">→</span>
                    </a>
                </div>
            </section>
        </main>

        <?php include __DIR__ . '/includes/footer.php'; ?>
    </body>
</html>
