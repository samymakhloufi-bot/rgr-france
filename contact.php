<?php

declare(strict_types=1);

use App\Entity\ContactRequest;
use App\Repository\CompanyInformationRepository;
use App\Repository\ContactRequestRepository;
use App\Repository\OpeningHourRepository;

require_once __DIR__ . '/config/app.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/src/Entity/ContactRequest.php';
require_once __DIR__ . '/src/Entity/CompanyInformation.php';
require_once __DIR__ . '/src/Entity/OpeningHour.php';
require_once __DIR__ . '/src/Repository/ContactRequestRepository.php';
require_once __DIR__ . '/src/Repository/CompanyInformationRepository.php';
require_once __DIR__ . '/src/Repository/OpeningHourRepository.php';
require_once __DIR__ . '/config/session.php';

$activePage = 'Nous contacter';
$metaDescription = 'Contactez RGR France pour votre projet d’importation, d’homologation, de programmation ou de recherche de véhicule.';
$pageCss = '/assets/css/pages/forms.css';

$_SESSION['contact_csrf_token'] ??= bin2hex(random_bytes(32));

$contactSuccess = $_SESSION['contact_success'] ?? null;
$errors = $_SESSION['contact_errors'] ?? [];
$old = $_SESSION['contact_old'] ?? [];

unset(
    $_SESSION['contact_success'],
    $_SESSION['contact_errors'],
    $_SESSION['contact_old'],
    $_SESSION['contact_form_started_at'] ??= time()
);

$redirectWithErrors = static function (array $errors, array $old): never {
    $_SESSION['contact_errors'] = $errors;
    $_SESSION['contact_old'] = $old;
    

    header('Location: ' . BASE_URL . '/contact.php');
    exit;
};

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $errors = [];

    $firstName = trim((string) ($_POST['first_name'] ?? ''));
    $lastName = trim((string) ($_POST['last_name'] ?? ''));
    $email = strtolower(trim((string) ($_POST['email'] ?? '')));
    $phone = trim((string) ($_POST['phone'] ?? ''));
    $postalAddress = trim((string) ($_POST['postal_address'] ?? ''));
    $postalCode = trim((string) ($_POST['postal_code'] ?? ''));
    $city = trim((string) ($_POST['city'] ?? ''));
    $message = trim((string) ($_POST['message'] ?? ''));
    $privacyConsent = ($_POST['privacy_consent'] ?? '') === '1';
    $csrfToken = (string) ($_POST['csrf_token'] ?? '');
    $website = trim((string) ($_POST['website'] ?? ''));
    $formStartedAt = (int) ($_SESSION['contact_form_started_at'] ?? 0);

    if (
        $formStartedAt === 0
        || time() - $formStartedAt < 3
    ) {
        $errors[] = 'Le formulaire a été envoyé trop rapidement.';
    }

    $old = [
        'first_name' => $firstName,
        'last_name' => $lastName,
        'email' => $email,
        'phone' => $phone,
        'postal_address' => $postalAddress,
        'postal_code' => $postalCode,
        'city' => $city,
        'message' => $message,
        'privacy_consent' => $privacyConsent,
    ];

    if ($website !== '') {
        $_SESSION['contact_success'] = 'Votre demande a bien été envoyée.';
        header('Location: ' . BASE_URL . '/contact.php');
        exit;
    }

    $sessionToken = $_SESSION['contact_csrf_token'] ?? '';

    if (
        !is_string($sessionToken)
        || $sessionToken === ''
        || !hash_equals($sessionToken, $csrfToken)
    ) {
        $errors[] = 'Le formulaire a expiré. Veuillez réessayer.';
    }

    if ($firstName === '') {
        $errors[] = 'Le prénom est obligatoire.';
    }

    if ($lastName === '') {
        $errors[] = 'Le nom est obligatoire.';
    }

    if (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
        $errors[] = 'L’adresse e-mail est invalide.';
    }

    if ($message === '') {
        $errors[] = 'Le message est obligatoire.';
    }

    if (!$privacyConsent) {
        $errors[] = 'Vous devez accepter le traitement de vos informations.';
    }

    if (strlen($firstName) > 100) {
        $errors[] = 'Le prénom est trop long.';
    }

    if (strlen($lastName) > 100) {
        $errors[] = 'Le nom est trop long.';
    }

    if (strlen($email) > 190) {
        $errors[] = 'L’adresse e-mail est trop longue.';
    }

    if (strlen($phone) > 30) {
        $errors[] = 'Le numéro de téléphone est trop long.';
    }

    if (strlen($postalAddress) > 255) {
        $errors[] = 'L’adresse postale est trop longue.';
    }

    if (strlen($postalCode) > 20) {
        $errors[] = 'Le code postal est trop long.';
    }

    if (strlen($city) > 120) {
        $errors[] = 'Le nom de la ville est trop long.';
    }

    if (strlen($message) > 5000) {
        $errors[] = 'Le message ne peut pas dépasser 5 000 caractères.';
    }

    if ($errors !== []) {
        $redirectWithErrors($errors, $old);
    }

    try {
        $contactRequest = new ContactRequest(
            firstName: $firstName,
            lastName: $lastName,
            email: $email,
            message: $message,
            phone: $phone === '' ? null : $phone,
            postalAddress: $postalAddress === '' ? null : $postalAddress,
            postalCode: $postalCode === '' ? null : $postalCode,
            city: $city === '' ? null : $city,
            privacyConsent: true
        );

        $contactRequestRepository = new ContactRequestRepository($pdo);
        $contactRequestRepository->save($contactRequest);

        unset($_SESSION['contact_old']);

        $_SESSION['contact_success'] =
            'Votre demande a bien été envoyée. Notre équipe vous répondra rapidement.';

        $_SESSION['contact_csrf_token'] = bin2hex(random_bytes(32));
        $_SESSION['contact_form_started_at'] = time();

        header('Location: ' . BASE_URL . '/contact.php');
        exit;
    } catch (Throwable $exception) {
        error_log($exception->getMessage());

        $redirectWithErrors(
            ['Une erreur est survenue pendant l’envoi. Veuillez réessayer.'],
            $old
        );
    }
}

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
        'Impossible de charger les coordonnées de contact : '
        . $exception->getMessage()
    );
}

$escape = static function (?string $value): string {
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
};

$formatTime = static function (?string $time): string {
    return $time === null ? '' : substr($time, 0, 5);
};

$companyPhone = $companyInformation?->getPhone() ?? '07 49 84 80 45';
$companyPhoneHref = preg_replace('/[^0-9+]/', '', $companyPhone) ?: '';
$companyEmail = $companyInformation?->getEmail() ?? 'contact@rgrfrance.fr';
$companyAddress = $companyInformation?->getAddressLine()
    ?? '19 rue des Fours Banaux';
$companyPostalCode = $companyInformation?->getPostalCode() ?? '89690';
$companyCity = $companyInformation?->getCity() ?? 'Chéroy';
$companyMapUrl = $companyInformation?->getGoogleMapsUrl()
    ?? 'https://www.google.com/maps/search/?api=1&query=19+rue+des+Fours+Banaux+89690+Chéroy';
?>
<!DOCTYPE html>
<html lang="fr">
    <?php include __DIR__ . '/includes/head.php'; ?>

    <body>
        <?php include __DIR__ . '/includes/header.php'; ?>

        <main class="contact-page">
            <header class="contact-hero">
                <div class="contact-hero__content">
                    <p class="contact-hero__eyebrow">RGR France · Chéroy</p>
                    <h1 class="contact-hero__title">Parlons de votre projet</h1>
                    <p class="contact-hero__introduction">
                        Importation, homologation, programmation ou recherche d’un
                        véhicule : décrivez-nous votre besoin et notre équipe vous
                        répondra rapidement.
                    </p>
                </div>
            </header>

            <div class="contact-layout">
                <aside class="contact-information" aria-labelledby="contact-information-title">
                    <h2 class="contact-information__title" id="contact-information-title">
                        Contact direct
                    </h2>

                    <address class="contact-information__list">
                        <div class="contact-information__item">
                            <span class="contact-information__number" aria-hidden="true">01</span>
                            <div>
                                <h3>Téléphone</h3>
                                <a href="tel:<?= $escape($companyPhoneHref) ?>">
                                    <?= $escape($companyPhone) ?>
                                </a>
                            </div>
                        </div>

                        <div class="contact-information__item">
                            <span class="contact-information__number" aria-hidden="true">02</span>
                            <div>
                                <h3>Adresse e-mail</h3>
                                <a href="mailto:<?= $escape($companyEmail) ?>">
                                    <?= $escape($companyEmail) ?>
                                </a>
                            </div>
                        </div>

                        <div class="contact-information__item">
                            <span class="contact-information__number" aria-hidden="true">03</span>
                            <div>
                                <h3>Le garage</h3>
                                <p>
                                    <?= $escape($companyAddress) ?><br>
                                    <?= $escape($companyPostalCode) ?> <?= $escape($companyCity) ?>
                                </p>
                                <a
                                    class="contact-information__map-link"
                                    href="<?= $escape($companyMapUrl) ?>"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                >
                                    Voir sur Google Maps
                                </a>
                            </div>
                        </div>
                    </address>

                    <section class="contact-hours" aria-labelledby="contact-hours-title">
                        <h2 id="contact-hours-title">Horaires d’ouverture</h2>

                        <?php if ($openingHours !== []): ?>
                            <dl class="contact-hours__list">
                                <?php foreach ($openingHours as $openingHour): ?>
                                    <div class="contact-hours__row">
                                        <dt><?= $escape($openingHour->getDayName()) ?></dt>
                                        <dd>
                                            <?php if ($openingHour->isClosed()): ?>
                                                Fermé
                                            <?php else: ?>
                                                <?php if ($openingHour->getMorningOpen() !== null): ?>
                                                    <?= $escape($formatTime($openingHour->getMorningOpen())) ?>–<?= $escape($formatTime($openingHour->getMorningClose())) ?>
                                                <?php endif; ?>
                                                <?php if (
                                                    $openingHour->getMorningOpen() !== null
                                                    && $openingHour->getAfternoonOpen() !== null
                                                ): ?>
                                                    / 
                                                <?php endif; ?>
                                                <?php if ($openingHour->getAfternoonOpen() !== null): ?>
                                                    <?= $escape($formatTime($openingHour->getAfternoonOpen())) ?>–<?= $escape($formatTime($openingHour->getAfternoonClose())) ?>
                                                <?php endif; ?>
                                            <?php endif; ?>
                                        </dd>
                                    </div>
                                <?php endforeach; ?>
                            </dl>
                        <?php else: ?>
                            <p>Horaires disponibles prochainement.</p>
                        <?php endif; ?>
                    </section>
                </aside>

                <section class="contact-form-section form-card" aria-labelledby="contact-form-title">
                    <h2 class="form-card__title" id="contact-form-title">
                        Votre demande
                    </h2>
                    <p class="form-card__introduction">
                        Les champs marqués d’un astérisque sont nécessaires pour
                        pouvoir vous répondre.
                    </p>

                    <?php if (is_string($contactSuccess) && $contactSuccess !== ''): ?>
                        <div class="form-alert form-alert--success" role="status">
                            <?= $escape($contactSuccess) ?>
                        </div>
                    <?php endif; ?>

                    <?php if ($errors !== []): ?>
                        <div class="form-alert form-alert--error" role="alert">
                            <ul class="form-alert__list">
                                <?php foreach ($errors as $error): ?>
                                    <li><?= $escape((string) $error) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <form
                        class="contact-form"
                        action="<?= $escape(BASE_URL . '/contact.php') ?>"
                        method="post"
                    >
                        <input
                            type="hidden"
                            name="csrf_token"
                            value="<?= $escape((string) $_SESSION['contact_csrf_token']) ?>"
                        >

                        <div class="form-honeypot" aria-hidden="true">
                            <label for="website">Site internet</label>
                            <input
                                id="website"
                                name="website"
                                type="text"
                                tabindex="-1"
                                autocomplete="off"
                            >
                        </div>

                        <div class="form-grid">
                            <div class="form-field">
                                <label class="form-field__label" for="first_name">Prénom *</label>
                                <input
                                    class="form-field__input"
                                    id="first_name"
                                    name="first_name"
                                    type="text"
                                    value="<?= $escape((string) ($old['first_name'] ?? '')) ?>"
                                    autocomplete="given-name"
                                    maxlength="100"
                                    required
                                >
                            </div>

                            <div class="form-field">
                                <label class="form-field__label" for="last_name">Nom *</label>
                                <input
                                    class="form-field__input"
                                    id="last_name"
                                    name="last_name"
                                    type="text"
                                    value="<?= $escape((string) ($old['last_name'] ?? '')) ?>"
                                    autocomplete="family-name"
                                    maxlength="100"
                                    required
                                >
                            </div>

                            <div class="form-field">
                                <label class="form-field__label" for="email">Adresse e-mail *</label>
                                <input
                                    class="form-field__input"
                                    id="email"
                                    name="email"
                                    type="email"
                                    value="<?= $escape((string) ($old['email'] ?? '')) ?>"
                                    autocomplete="email"
                                    maxlength="190"
                                    required
                                >
                            </div>

                            <div class="form-field">
                                <label class="form-field__label" for="phone">Téléphone</label>
                                <input
                                    class="form-field__input"
                                    id="phone"
                                    name="phone"
                                    type="tel"
                                    value="<?= $escape((string) ($old['phone'] ?? '')) ?>"
                                    autocomplete="tel"
                                    maxlength="30"
                                >
                            </div>

                            <div class="form-field form-field--full">
                                <label class="form-field__label" for="postal_address">Adresse postale</label>
                                <input
                                    class="form-field__input"
                                    id="postal_address"
                                    name="postal_address"
                                    type="text"
                                    value="<?= $escape((string) ($old['postal_address'] ?? '')) ?>"
                                    autocomplete="street-address"
                                    maxlength="255"
                                >
                            </div>

                            <div class="form-field">
                                <label class="form-field__label" for="postal_code">Code postal</label>
                                <input
                                    class="form-field__input"
                                    id="postal_code"
                                    name="postal_code"
                                    type="text"
                                    value="<?= $escape((string) ($old['postal_code'] ?? '')) ?>"
                                    autocomplete="postal-code"
                                    inputmode="numeric"
                                    maxlength="20"
                                >
                            </div>

                            <div class="form-field">
                                <label class="form-field__label" for="city">Ville</label>
                                <input
                                    class="form-field__input"
                                    id="city"
                                    name="city"
                                    type="text"
                                    value="<?= $escape((string) ($old['city'] ?? '')) ?>"
                                    autocomplete="address-level2"
                                    maxlength="120"
                                >
                            </div>

                            <div class="form-field form-field--full">
                                <label class="form-field__label" for="message">Votre message *</label>
                                <textarea
                                    class="form-field__input"
                                    id="message"
                                    name="message"
                                    rows="6"
                                    maxlength="5000"
                                    required
                                ><?= $escape((string) ($old['message'] ?? '')) ?></textarea>
                            </div>
                        </div>

                        <label class="form-consent" for="privacy_consent">
                            <input
                                class="form-consent__checkbox"
                                id="privacy_consent"
                                name="privacy_consent"
                                type="checkbox"
                                value="1"
                                <?= !empty($old['privacy_consent']) ? 'checked' : '' ?>
                                required
                            >
                            <span>
                                J’accepte que mes informations soient utilisées par
                                RGR France pour traiter ma demande. *
                            </span>
                        </label>

                        <button class="form-submit" type="submit">
                            Envoyer ma demande
                            <span aria-hidden="true">→</span>
                        </button>
                    </form>
                </section>
            </div>
        </main>

        <?php include __DIR__ . '/includes/footer.php'; ?>
    </body>
</html>
