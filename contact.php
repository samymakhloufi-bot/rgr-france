<?php

declare(strict_types=1);

use App\Entity\ContactRequest;
use App\Repository\ContactRequestRepository;

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/src/Entity/ContactRequest.php';
require_once __DIR__ . '/src/Repository/ContactRequestRepository.php';

$activePage = 'Nous contacter';
$metaDescription = 'Contactez RGR France pour votre projet d’importation, d’homologation, de programmation ou de recherche de véhicule.';
$pageCss = '/assets/css/pages/contact.css';

$_SESSION['contact_csrf_token'] ??= bin2hex(random_bytes(32));

$contactSuccess = $_SESSION['contact_success'] ?? null;
$errors = $_SESSION['contact_errors'] ?? [];
$old = $_SESSION['contact_old'] ?? [];

unset(
    $_SESSION['contact_success'],
    $_SESSION['contact_errors'],
    $_SESSION['contact_old']
);

$redirectWithErrors = static function (array $errors, array $old): never {
    $_SESSION['contact_errors'] = $errors;
    $_SESSION['contact_old'] = $old;

    header('Location: /contact.php');
    exit;
};

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
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

    // Honeypot antispam
    if ($website !== '') {
        $_SESSION['contact_success'] = 'Votre demande a bien été envoyée.';

        header('Location: /contact.php');
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

        $pdo = getDatabaseConnection();

        $contactRequestRepository = new ContactRequestRepository($pdo);
        $contactRequestRepository->save($contactRequest);

        unset($_SESSION['contact_old']);

        $_SESSION['contact_success'] =
            'Votre demande a bien été envoyée. Notre équipe vous répondra rapidement.';

        $_SESSION['contact_csrf_token'] = bin2hex(random_bytes(32));

        header('Location: /contact.php');
        exit;
    } catch (Throwable $exception) {
        error_log($exception->getMessage());

        $redirectWithErrors(
            ['Une erreur est survenue pendant l’envoi. Veuillez réessayer.'],
            $old
        );
    }
}

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

                <h1 class="contact-hero__title">
                    Parlons de votre projet
                </h1>

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
                            <a href="tel:+33749848045">07 49 84 80 45</a>
                        </div>
                    </div>

                    <div class="contact-information__item">
                        <span class="contact-information__number" aria-hidden="true">02</span>

                        <div>
                            <h3>Adresse e-mail</h3>
                            <a href="mailto:contact@rgrfrance.fr">
                                contact@rgrfrance.fr
                            </a>
                        </div>
                    </div>

                    <div class="contact-information__item">
                        <span class="contact-information__number" aria-hidden="true">03</span>

                        <div>
                            <h3>Le garage</h3>
                            <p>19 rue des Fours Banaux<br>89690 Chéroy</p>

                            <a
                                class="contact-information__map-link"
                                href="https://www.google.com/maps/search/?api=1&query=19+rue+des+Fours+Banaux+89690+Ch%C3%A9roy"
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

                    <dl class="contact-hours__list">
                        <div class="contact-hours__row">
                            <dt>Lundi — Vendredi</dt>
                            <dd>09:00 — 18:00</dd>
                        </div>

                        <div class="contact-hours__row">
                            <dt>Samedi</dt>
                            <dd>Sur rendez-vous</dd>
                        </div>

                        <div class="contact-hours__row">
                            <dt>Dimanche</dt>
                            <dd>Fermé</dd>
                        </div>
                    </dl>
                </section>
            </aside>

            <section class="contact-form-section" aria-labelledby="contact-form-title">
                <h2 class="contact-form-section__title" id="contact-form-title">
                    Votre demande
                </h2>

                <p class="contact-form-section__introduction">
                    Les champs marqués d’un astérisque sont nécessaires pour
                    pouvoir vous répondre.
                </p>

                <form class="contact-form" action="/actions/contact.php" method="post">
                    <div class="contact-form__grid">
                        <div class="contact-form__field">
                            <label for="first_name">Prénom *</label>
                            <input
                                id="first_name"
                                name="first_name"
                                type="text"
                                autocomplete="given-name"
                                maxlength="100"
                                required
                            >
                        </div>

                        <div class="contact-form__field">
                            <label for="last_name">Nom *</label>
                            <input
                                id="last_name"
                                name="last_name"
                                type="text"
                                autocomplete="family-name"
                                maxlength="100"
                                required
                            >
                        </div>

                        <div class="contact-form__field">
                            <label for="email">Adresse e-mail *</label>
                            <input
                                id="email"
                                name="email"
                                type="email"
                                autocomplete="email"
                                maxlength="190"
                                required
                            >
                        </div>

                        <div class="contact-form__field">
                            <label for="phone">Téléphone</label>
                            <input
                                id="phone"
                                name="phone"
                                type="tel"
                                autocomplete="tel"
                                maxlength="30"
                            >
                        </div>

                        <div class="contact-form__field contact-form__field--full">
                            <label for="postal_address">Adresse postale</label>
                            <input
                                id="postal_address"
                                name="postal_address"
                                type="text"
                                autocomplete="street-address"
                                maxlength="255"
                            >
                        </div>

                        <div class="contact-form__field">
                            <label for="postal_code">Code postal</label>
                            <input
                                id="postal_code"
                                name="postal_code"
                                type="text"
                                autocomplete="postal-code"
                                inputmode="numeric"
                                maxlength="20"
                            >
                        </div>

                        <div class="contact-form__field">
                            <label for="city">Ville</label>
                            <input
                                id="city"
                                name="city"
                                type="text"
                                autocomplete="address-level2"
                                maxlength="120"
                            >
                        </div>

                        <div class="contact-form__field contact-form__field--full">
                            <label for="message">Votre message *</label>
                            <textarea
                                id="message"
                                name="message"
                                rows="6"
                                maxlength="5000"
                                required
                            ></textarea>
                        </div>
                    </div>

                    <label class="contact-form__consent" for="privacy_consent">
                        <input
                            id="privacy_consent"
                            name="privacy_consent"
                            type="checkbox"
                            value="1"
                            required
                        >

                        <span>
                            J’accepte que mes informations soient utilisées par
                            RGR France pour traiter ma demande. *
                        </span>
                    </label>

                    <button class="contact-form__submit" type="submit">
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
