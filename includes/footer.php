

<footer class="footer">

    
    <div class="footer__header">

        <a
            href="index.php"
            class="footer__brand"
            aria-label="Accueil RGR France"
        >
            <img 
            src='assets/images/icons/logo-rgr-vector.svg' 
            class="logo-img" alt='RGR France Logo' 
            title='RGR France Logo' 
            >
        </a>

        <p class="footer__tagline">
            Importation et homologation automobile
        </p>

    </div>


   
    <div class="footer__grid">
        <section class="footer__section">

            <h2 class="footer__title">
                Contact
            </h2>

            <ul class="footer__list">

                <li class="footer__item">

                    <a
                        href="tel:<?= htmlspecialchars(
                            $telephoneLink,
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>"
                        class="footer__link footer__link--important"
                    >
                        <?= htmlspecialchars(
                            $telephone,
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </a>

                </li>

                <li class="footer__item">

                    <a
                        href="mailto:<?= htmlspecialchars(
                            $email,
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>"
                        class="footer__link"
                    >
                        <?= htmlspecialchars(
                            $email,
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </a>

                </li>

            </ul>

        </section>


        
        <section class="footer__section">

            <h2 class="footer__title">
                Horaires
            </h2>

            <ul class="footer__list footer__hours">

                <?php foreach ($horairesFooter as $horaire): ?>

                    <li class="footer__hour">

                        <span class="footer__day">

                            <?= htmlspecialchars(
                                $horaire['jour'],
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>

                        </span>

                        <span class="footer__time">

                            <?php if (!(bool) $horaire['ferme']): ?>

                                <?= htmlspecialchars(
                                    substr(
                                        $horaire['ouverture_matin'],
                                        0,
                                        5
                                    ),
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>

                                à

                                <?= htmlspecialchars(
                                    substr(
                                        $horaire['fermeture_apm'],
                                        0,
                                        5
                                    ),
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>

                            <?php else: ?>

                                Fermé

                            <?php endif; ?>

                        </span>

                    </li>

                <?php endforeach; ?>

            </ul>

        </section>


        
        <section class="footer__section">

            <h2 class="footer__title">
                Le garage
            </h2>

            <address class="footer__address">

                <p>
                    <?= htmlspecialchars(
                        $adresse,
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>
                </p>

                <a
                    href="https://www.google.com/maps/place/19+Rue+des+Fours+Banaux,+89690+Ch%C3%A9roy/"
                    class="footer__map-link"
                    target="_blank"
                    rel="noopener noreferrer"
                >
                    Voir l’itinéraire

                    <span aria-hidden="true">
                        ↗
                    </span>
                </a>

            </address>

        </section>

    </div>


    
    <div class="footer__social">

        <h2 class="footer__social-title">
            Suivez-nous
        </h2>

        <ul
            class="footer__social-list"
            aria-label="Réseaux sociaux de RGR France"
        >

            <li class="footer__social-item">

                <a
                    href="https://www.facebook.com/RGRofficiel"
                    class="footer__social-link"
                    target="_blank"
                    rel="noopener noreferrer"
                    aria-label="Suivre RGR France sur Facebook"
                >
                    <img
                        src="assets/images/icons/facebook.svg"
                        class="footer__social-icon"
                        alt=""
                    >
                </a>

            </li>

            <li class="footer__social-item">

                <a
                    href="https://www.instagram.com/rgr_inst/"
                    class="footer__social-link"
                    target="_blank"
                    rel="noopener noreferrer"
                    aria-label="Suivre RGR France sur Instagram"
                >
                    <img
                        src="assets/images/icons/instagram.svg"
                        class="footer__social-icon"
                        alt=""
                    >
                </a>

            </li>

            <li class="footer__social-item">

                <a
                    href="https://www.linkedin.com/in/rgr-france"
                    class="footer__social-link"
                    target="_blank"
                    rel="noopener noreferrer"
                    aria-label="Suivre RGR France sur LinkedIn"
                >
                    <img
                        src="assets/images/icons/linkedin.svg"
                        class="footer__social-icon"
                        alt=""
                    >
                </a>

            </li>

            <li class="footer__social-item">

                <a
                    href="https://www.youtube.com/@RGR_officiel"
                    class="footer__social-link"
                    target="_blank"
                    rel="noopener noreferrer"
                    aria-label="Suivre RGR France sur YouTube"
                >
                    <img
                        src="assets/images/icons/youtube.svg"
                        class="footer__social-icon"
                        alt=""
                    >
                </a>

            </li>

        </ul>

    </div>


    
    <div class="footer__bottom">

        <p class="footer__copyright">
            © <?= date('Y') ?> RGR France
        </p>

        <p class="footer__siren">
            SIREN :
            <?= htmlspecialchars(
                $siren,
                ENT_QUOTES,
                'UTF-8'
            ) ?>
        </p>

        <nav
            class="footer__legal"
            aria-label="Informations légales"
        >

            <a
                href="mentions.php"
                class="footer__legal-link"
            >
                Mentions légales et confidentialité
            </a>

            <a
                href="CGV.php"
                class="footer__legal-link"
            >
                Conditions générales de vente
            </a>

        </nav>

    </div>

</footer>