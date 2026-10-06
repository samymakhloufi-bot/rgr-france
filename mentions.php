<?php
$activePage = 'Mentions légales';
$metaDescription = 'Consultez les mentions légales et les informations relatives au site RGR France.';
$pageCss = '/assets/css/pages/mentions.css';
?>

<!DOCTYPE html>
<html lang="fr">

<?php include __DIR__ . '/includes/head.php'; ?>

<body>
    <?php include __DIR__ . '/includes/header.php'; ?>

    <main class="mentions-page">
        <header class="mentions-hero">
            <div class="mentions-hero__content">
                <p class="mentions-hero__eyebrow">RGR France</p>
                <h1 class="mentions-hero__title">Mentions légales</h1>
                <p class="mentions-hero__introduction">
                    Informations relatives à l’éditeur, à l’hébergement et à
                    l’utilisation du site RGR France.
                </p>
            </div>
        </header>

        <div class="mentions-layout">
            <aside class="mentions-sidebar">
                <details class="mentions-summary" open>
                    <summary class="mentions-summary__title">Sommaire</summary>

                    <nav aria-label="Sommaire des mentions légales">
                        <ol class="mentions-summary__list">
                            <li><a href="#informations-generales">01 — Informations générales</a></li>
                            <li><a href="#hebergement">02 — Hébergement</a></li>
                            <li><a href="#propriete-intellectuelle">03 — Propriété intellectuelle</a></li>
                            <li><a href="#donnees-personnelles">04 — Données personnelles</a></li>
                            <li><a href="#cookies">05 — Cookies</a></li>
                        </ol>
                    </nav>
                </details>
            </aside>

            <article class="mentions-content">
                <section class="mentions-section" id="informations-generales">
                    <p class="mentions-section__number">01</p>
                    <h2>Informations générales</h2>

                    <dl class="mentions-details">
                        <div>
                            <dt>Raison sociale</dt>
                            <dd>RGR France</dd>
                        </div>
                        <div>
                            <dt>Forme juridique</dt>
                            <dd>Société par Actions Simplifiée (SAS)</dd>
                        </div>
                        <div>
                            <dt>SIREN</dt>
                            <dd>999 227 572</dd>
                        </div>
                        <div>
                            <dt>Siège social</dt>
                            <dd>19 rue des Fours Banaux, 89690 Chéroy, France</dd>
                        </div>
                        <div>
                            <dt>Directeur de la publication</dt>
                            <dd>THIESSE BILLY</dd>
                        </div>
                        <div>
                            <dt>Contact</dt>
                            <dd><a href="mailto:rgrcustome@gmail.com">rgrcustome@gmail.com</a></dd>
                        </div>
                    </dl>
                </section>

                <section class="mentions-section" id="hebergement">
                    <p class="mentions-section__number">02</p>
                    <h2>Hébergement</h2>

                    <p>Le site est hébergé par :</p>
                    <address class="mentions-card">
                        <strong>OVH SAS</strong><br>
                        2 rue Kellermann<br>
                        59100 Roubaix – France<br>
                        <a href="https://www.ovhcloud.com/fr/" target="_blank" rel="noopener noreferrer">
                            www.ovh.com
                        </a>
                    </address>
                </section>

                <section class="mentions-section" id="propriete-intellectuelle">
                    <p class="mentions-section__number">03</p>
                    <h2>Propriété intellectuelle</h2>

                    <p>
                        L’ensemble des contenus présents sur ce site (textes,
                        images, logos, éléments graphiques, structure, vidéos) est
                        protégé par le droit de la propriété intellectuelle.
                    </p>
                    <p>
                        Toute reproduction, représentation, modification ou
                        exploitation, totale ou partielle, est strictement interdite
                        sans autorisation écrite préalable de RGR France.
                    </p>
                </section>

                <section class="mentions-section" id="donnees-personnelles">
                    <p class="mentions-section__number">04</p>
                    <h2>Données personnelles</h2>

                    <p>
                        Les données collectées via les formulaires sont destinées
                        exclusivement à RGR France et ne sont jamais transmises à
                        des tiers sans consentement.
                    </p>
                    <p>
                        Conformément au RGPD et à la loi Informatique et Libertés,
                        vous disposez d’un droit d’accès, de rectification, de
                        suppression et d’opposition concernant vos données.
                    </p>

                    <aside class="mentions-highlight">
                        Pour exercer vos droits, écrivez à
                        <a href="mailto:rgrcustome@gmail.com">rgrcustome@gmail.com</a>.
                    </aside>
                </section>

                <section class="mentions-section" id="cookies">
                    <p class="mentions-section__number">05</p>
                    <h2>Cookies</h2>

                    <p>
                        Le site utilise des cookies nécessaires à son bon
                        fonctionnement et à l’amélioration de l’expérience utilisateur.
                    </p>
                    <p>
                        Pour en savoir plus ou gérer vos préférences, consultez notre
                        <a href="/politique-cookies.php">Politique de cookies</a>.
                    </p>
                </section>
            </article>
        </div>
    </main>

    <?php include __DIR__ . '/includes/footer.php'; ?>
</body>
</html>
