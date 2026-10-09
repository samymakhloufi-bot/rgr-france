<?php
declare(strict_types=1);

$activePage = 'Mentions légales';
$metaDescription = 'Mentions légales, propriété intellectuelle et traitement des données du site RGR France.';
$pageCss = '/assets/css/pages/legal.css';

    require_once __DIR__ . '/config/database.php';
?>
<!DOCTYPE html>
<html lang="fr">
    <?php include __DIR__ . '/includes/head.php'; ?>

    <body>
        <?php include __DIR__ . '/includes/header.php'; ?>

        <main class="legal-page">
            <header class="legal-hero">
                <div class="legal-container legal-hero__content">
                    <p class="legal-eyebrow">RGR France · Informations légales</p>
                    <h1 class="legal-title">Mentions légales</h1>
                    <p class="legal-intro">
                        Retrouvez les informations relatives à l’éditeur, à l’hébergement
                        et à l’utilisation du site RGR France.
                    </p>
                </div>
            </header>

            <div class="legal-container legal-layout">
                <nav class="legal-summary" aria-label="Sommaire des mentions légales">
                    <h2 class="legal-summary__title">Sommaire</h2>
                    <ol class="legal-summary__list">
                        <li><a class="legal-summary__link" href="#informations"><span>01</span>Informations générales</a></li>
                        <li><a class="legal-summary__link" href="#hebergement"><span>02</span>Hébergement</a></li>
                        <li><a class="legal-summary__link" href="#propriete"><span>03</span>Propriété intellectuelle</a></li>
                        <li><a class="legal-summary__link" href="#donnees"><span>04</span>Données personnelles</a></li>
                        <li><a class="legal-summary__link" href="#cookies"><span>05</span>Cookies</a></li>
                    </ol>
                </nav>

                <article class="legal-content">
                    <section class="legal-section" id="informations">
                        <p class="legal-section__number">Article 01</p>
                        <h2>Informations générales</h2>

                        <dl class="legal-data">
                            <div class="legal-data__row">
                                <dt>Raison sociale</dt>
                                <dd>RGR France</dd>
                            </div>
                            <div class="legal-data__row">
                                <dt>Forme juridique</dt>
                                <dd>Société par Actions Simplifiée (SAS)</dd>
                            </div>
                            <div class="legal-data__row">
                                <dt>SIREN</dt>
                                <dd>999 227 572</dd>
                            </div>
                            <div class="legal-data__row">
                                <dt>Siège social</dt>
                                <dd>19 rue des Fours Banaux, 89690 Chéroy, France</dd>
                            </div>
                            <div class="legal-data__row">
                                <dt>Directeur de la publication</dt>
                                <dd>THIESSE BILLY</dd>
                            </div>
                            <div class="legal-data__row">
                                <dt>Contact</dt>
                                <dd><a href="mailto:rgrcustome@gmail.com">rgrcustome@gmail.com</a></dd>
                            </div>
                        </dl>
                    </section>

                    <section class="legal-section" id="hebergement">
                        <p class="legal-section__number">Article 02</p>
                        <h2>Hébergement</h2>
                        <p>Le site est hébergé par :</p>
                        <p>
                            <strong>OVH SAS</strong><br>
                            2 rue Kellermann<br>
                            59100 Roubaix – France
                        </p>
                        <p>
                            Site web :
                            <a href="https://www.ovh.com/" target="_blank" rel="noopener noreferrer">www.ovh.com</a>
                        </p>
                    </section>

                    <section class="legal-section" id="propriete">
                        <p class="legal-section__number">Article 03</p>
                        <h2>Propriété intellectuelle</h2>
                        <p>
                            L’ensemble des contenus présents sur ce site (textes, images, logos,
                            éléments graphiques, structure, vidéos) est protégé par le droit de la
                            propriété intellectuelle.
                        </p>
                        <p>
                            Toute reproduction, représentation, modification ou exploitation,
                            totale ou partielle, est strictement interdite sans autorisation écrite
                            préalable de RGR France.
                        </p>
                    </section>

                    <section class="legal-section" id="donnees">
                        <p class="legal-section__number">Article 04</p>
                        <h2>Données personnelles</h2>
                        <p>
                            Les données collectées via les formulaires sont destinées exclusivement
                            à RGR France et ne sont jamais transmises à des tiers sans consentement.
                        </p>
                        <p>
                            Conformément au RGPD et à la loi Informatique et Libertés, vous disposez
                            d’un droit d’accès, de rectification, de suppression et d’opposition
                            concernant vos données.
                        </p>
                        <p>
                            Pour toute demande :
                            <a href="mailto:rgrcustome@gmail.com">rgrcustome@gmail.com</a>
                        </p>
                    </section>

                    <section class="legal-section" id="cookies">
                        <p class="legal-section__number">Article 05</p>
                        <h2>Cookies</h2>
                        <p>
                            Le site utilise des cookies nécessaires à son bon fonctionnement et à
                            l’amélioration de l’expérience utilisateur.
                        </p>
                        <p>
                            Pour en savoir plus ou gérer vos préférences, consultez notre politique
                            de cookies.
                        </p>
                    </section>
                </article>
            </div>
        </main>

        <?php include __DIR__ . '/includes/footer.php'; ?>
    </body>
</html>
