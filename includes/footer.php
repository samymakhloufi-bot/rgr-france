<?php

declare(strict_types=1);

use App\Repository\CompanyInformationRepository;
use App\Repository\OpeningHourRepository;

$companyInformation = null;
$openingHours = [];

try {
    $companyRepository = new CompanyInformationRepository($pdo);
    $openingHourRepository = new OpeningHourRepository($pdo);

    $companyInformation = $companyRepository->findMain();
    $companyId = $companyInformation?->getId();

    if ($companyId !== null) {
        $openingHours = $openingHourRepository->findByCompany($companyId);
    }
} catch (Throwable $exception) {
    error_log(
        'Impossible de charger les données du footer : '
        . $exception->getMessage()
    );
}

$escape = static function (?string $value): string {
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
};

$formatTime = static function (?string $time): string {
    return $time === null ? '' : substr($time, 0, 5);
};

$companyName = $companyInformation?->getCompanyName() ?? 'RGR France';
$telephone = $companyInformation?->getPhone() ?? '07 49 84 80 45';
$telephoneLink = preg_replace('/[^0-9+]/', '', $telephone) ?: '';
$email = $companyInformation?->getEmail() ?? 'contact@rgrfrance.fr';
$addressLine = $companyInformation?->getAddressLine()
    ?? '19 rue des Fours Banaux';
$postalCode = $companyInformation?->getPostalCode() ?? '89690';
$city = $companyInformation?->getCity() ?? 'Chéroy';
$country = $companyInformation?->getCountry() ?? 'France';
$siren = $companyInformation?->getSiren() ?? '999 227 572';

$googleMapsUrl = $companyInformation?->getGoogleMapsUrl()
    ?? 'https://www.google.com/maps/search/?api=1&query=19+rue+des+Fours+Banaux+89690+Chéroy';

$socialNetworks = [
    [
        'name' => 'Facebook',
        'url' => $companyInformation?->getFacebookUrl()
            ?? 'https://www.facebook.com/RGRofficiel',
        'icon' => BASE_URL . '/assets/images/icons/facebook.svg',
    ],
    [
        'name' => 'Instagram',
        'url' => $companyInformation?->getInstagramUrl()
            ?? 'https://www.instagram.com/rgr_inst/',
        'icon' => BASE_URL . '/assets/images/icons/instagram.svg',
    ],
    [
        'name' => 'LinkedIn',
        'url' => $companyInformation?->getLinkedinUrl()
            ?? 'https://www.linkedin.com/in/rgr-france',
        'icon' => BASE_URL . '/assets/images/icons/linkedin.svg',
    ],
    [
        'name' => 'YouTube',
        'url' => $companyInformation?->getYoutubeUrl()
            ?? 'https://www.youtube.com/@RGR_officiel',
        'icon' => BASE_URL . '/assets/images/icons/youtube.svg',
    ],
];
?>

<footer class="footer">
    <div class="footer__header">
        <a
            href="<?= $escape(BASE_URL . '/index.php') ?>"
            class="footer__brand"
            aria-label="Accueil RGR France"
        >
            <img
                src="<?= $escape(BASE_URL . '/assets/images/icons/logo-rgr-vector.svg') ?>"
                class="logo-img"
                alt="Logo <?= $escape($companyName) ?>"
                title="<?= $escape($companyName) ?>"
            >
        </a>

        <p class="footer__tagline">
            Importation et homologation automobile
        </p>
    </div>

    <div class="footer__grid">
        <section class="footer__section">
            <h2 class="footer__title">Contact</h2>

            <ul class="footer__list">
                <li class="footer__item">
                    <a
                        href="tel:<?= $escape($telephoneLink) ?>"
                        class="footer__link footer__link--important"
                    >
                        <?= $escape($telephone) ?>
                    </a>
                </li>

                <li class="footer__item">
                    <a
                        href="mailto:<?= $escape($email) ?>"
                        class="footer__link"
                    >
                        <?= $escape($email) ?>
                    </a>
                </li>
            </ul>
        </section>

        <section class="footer__section">
            <h2 class="footer__title">Horaires</h2>

            <?php if ($openingHours !== []): ?>
                <ul class="footer__list footer__hours">
                    <?php foreach ($openingHours as $openingHour): ?>
                        <li class="footer__hour">
                            <span class="footer__day">
                                <?= $escape($openingHour->getDayName()) ?>
                            </span>

                            <span class="footer__time">
                                <?php if ($openingHour->isClosed()): ?>
                                    Fermé
                                <?php else: ?>
                                    <?php if ($openingHour->getMorningOpen() !== null): ?>
                                        <?= $escape($formatTime($openingHour->getMorningOpen())) ?>
                                        à
                                        <?= $escape($formatTime($openingHour->getMorningClose())) ?>
                                    <?php endif; ?>

                                    <?php if (
                                        $openingHour->getMorningOpen() !== null
                                        && $openingHour->getAfternoonOpen() !== null
                                    ): ?>
                                        / 
                                    <?php endif; ?>

                                    <?php if ($openingHour->getAfternoonOpen() !== null): ?>
                                        <?= $escape($formatTime($openingHour->getAfternoonOpen())) ?>
                                        à
                                        <?= $escape($formatTime($openingHour->getAfternoonClose())) ?>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </span>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p class="footer__item">
                    Horaires disponibles prochainement.
                </p>
            <?php endif; ?>
        </section>

        <section class="footer__section">
            <h2 class="footer__title">Le garage</h2>

            <address class="footer__address">
                <p>
                    <?= $escape($addressLine) ?><br>
                    <?= $escape($postalCode) ?> <?= $escape($city) ?><br>
                    <?= $escape($country) ?>
                </p>

                <a
                    href="<?= $escape($googleMapsUrl) ?>"
                    class="footer__map-link"
                    target="_blank"
                    rel="noopener noreferrer"
                >
                    Voir l’itinéraire
                    <span aria-hidden="true">↗</span>
                </a>
            </address>
        </section>
    </div>

    <div class="footer__social">
        <h2 class="footer__social-title">Suivez-nous</h2>

        <ul
            class="footer__social-list"
            aria-label="Réseaux sociaux de RGR France"
        >
            <?php foreach ($socialNetworks as $socialNetwork): ?>
                <?php if (!empty($socialNetwork['url'])): ?>
                    <li class="footer__social-item">
                        <a
                            href="<?= $escape($socialNetwork['url']) ?>"
                            class="footer__social-link"
                            target="_blank"
                            rel="noopener noreferrer"
                            aria-label="Suivre RGR France sur <?= $escape($socialNetwork['name']) ?>"
                        >
                            <img
                                src="<?= $escape($socialNetwork['icon']) ?>"
                                class="footer__social-icon"
                                alt=""
                                aria-hidden="true"
                            >
                        </a>
                    </li>
                <?php endif; ?>
            <?php endforeach; ?>
        </ul>
    </div>

    <div class="footer__bottom">
        <p class="footer__copyright">
            © <?= date('Y') ?> <?= $escape($companyName) ?>
        </p>

        <?php if ($siren !== ''): ?>
            <p class="footer__siren">
                SIREN : <?= $escape($siren) ?>
            </p>
        <?php endif; ?>

        <nav class="footer__legal" aria-label="Informations légales">
            <a
                href="<?= $escape(BASE_URL . '/mentions.php') ?>"
                class="footer__legal-link"
            >
                Mentions légales et confidentialité
            </a>

            <a
                href="<?= $escape(BASE_URL . '/cgv.php') ?>"
                class="footer__legal-link"
            >
                Conditions générales de vente
            </a>
        </nav>
    </div>
</footer>
