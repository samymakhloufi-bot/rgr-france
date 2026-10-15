<?php

declare(strict_types=1);

use App\Repository\UserRepository;

require_once __DIR__ . '/config/app.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/src/Entity/User.php';
require_once __DIR__ . '/src/Repository/UserRepository.php';

if (session_status() !== PHP_SESSION_ACTIVE) {
    ini_set('session.gc_maxlifetime', (string) (30 * 24 * 60 * 60));

    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'secure' => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
        'httponly' => true,
        'samesite' => 'Strict',
    ]);

    session_start();
}

$dashboardUrl = BASE_URL . '/admin/admin-index.php';
$loginError = null;
$username = '';
$maximumAttempts = 5;
$lockDuration = 15 * 60;

if (
    isset($_SESSION['user_id'], $_SESSION['user_role'])
    && $_SESSION['user_role'] === 'ADMIN'
) {
    header('Location: ' . $dashboardUrl);
    exit;
}

$_SESSION['admin_login_csrf'] ??= bin2hex(random_bytes(32));
$_SESSION['admin_login_attempts'] ??= 0;
$_SESSION['admin_login_locked_at'] ??= null;

if (
    is_int($_SESSION['admin_login_locked_at'])
    && time() - $_SESSION['admin_login_locked_at'] >= $lockDuration
) {
    $_SESSION['admin_login_attempts'] = 0;
    $_SESSION['admin_login_locked_at'] = null;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim((string) ($_POST['username'] ?? ''));
    $password = (string) ($_POST['password'] ?? '');
    $csrfToken = (string) ($_POST['csrf_token'] ?? '');
    $rememberMe = ($_POST['remember_me'] ?? '') === '1';
    $website = trim((string) ($_POST['website'] ?? ''));

    $isLocked = $_SESSION['admin_login_attempts'] >= $maximumAttempts
        && is_int($_SESSION['admin_login_locked_at']);

    if ($website !== '') {
        $loginError = 'Identifiant ou mot de passe incorrect.';
    } elseif (
        $csrfToken === ''
        || !hash_equals((string) $_SESSION['admin_login_csrf'], $csrfToken)
    ) {
        $loginError = 'Le formulaire a expiré. Veuillez réessayer.';
    } elseif ($isLocked) {
        $loginError = 'Trop de tentatives. Veuillez réessayer dans quelques minutes.';
    } elseif ($username === '' || $password === '') {
        $loginError = 'Renseignez votre identifiant et votre mot de passe.';
    } else {
        try {
            $userRepository = new UserRepository($pdo);
            $user = $userRepository->findByUsername($username);

            $isAuthenticated = $user !== null
                && $user->isActive()
                && $user->isAdmin()
                && password_verify($password, $user->getPasswordHash());

            if ($isAuthenticated) {
                session_regenerate_id(true);

                $_SESSION['user_id'] = $user->getId();
                $_SESSION['username'] = $user->getUsername();
                $_SESSION['user_role'] = $user->getRole();
                $_SESSION['authenticated_at'] = time();
                $_SESSION['admin_login_attempts'] = 0;
                $_SESSION['admin_login_locked_at'] = null;
                $_SESSION['admin_login_csrf'] = bin2hex(random_bytes(32));

                if ($rememberMe) {
                    setcookie(session_name(), session_id(), [
                        'expires' => time() + 30 * 24 * 60 * 60,
                        'path' => '/',
                        'secure' => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
                        'httponly' => true,
                        'samesite' => 'Strict',
                    ]);
                }

                header('Location: ' . $dashboardUrl);
                exit;
            }

            $_SESSION['admin_login_attempts']++;

            if ($_SESSION['admin_login_attempts'] >= $maximumAttempts) {
                $_SESSION['admin_login_locked_at'] = time();
                $loginError = 'Trop de tentatives. Veuillez réessayer dans 15 minutes.';
            } else {
                $loginError = 'Identifiant ou mot de passe incorrect.';
            }
        } catch (Throwable $exception) {
            error_log('Connexion administrateur impossible : ' . $exception->getMessage());
            $loginError = 'La connexion est momentanément indisponible.' .$exception->getMessage();
        }
    }
}

$escape = static function (?string $value): string {
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
};
?>
<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="robots" content="noindex, nofollow, noarchive">
        <meta name="description" content="Accès réservé à l’administration de RGR France.">
        <title>Administration | RGR France</title>

        <link rel="icon" href="<?= $escape(BASE_URL) ?>/assets/images/icons/logo-rgr-vector.svg" type="image/svg+xml">
        <link rel="stylesheet" href="<?= $escape(BASE_URL) ?>/assets/css/style.css">
        <link rel="stylesheet" href="<?= $escape(BASE_URL) ?>/assets/css/pages/admin-login.css?v=1">
    </head>

    <body class="admin-login-page">
        <main class="admin-login">
            <section class="admin-login__visual" aria-label="RGR France">
                <img
                    class="admin-login__logo admin-login__logo--visual"
                    src="<?= $escape(BASE_URL) ?>/assets/images/icons/logo-rgr-vector.svg"
                    alt="RGR France"
                >

                <p class="admin-login__notice">
                    Accès strictement réservé<br>
                    au personnel autorisé.
                </p>
            </section>

            <section class="admin-login__panel">
                <div class="admin-login__form-container">
                    <img
                        class="admin-login__logo admin-login__logo--mobile"
                        src="<?= $escape(BASE_URL) ?>/assets/images/icons/logo-rgr-vector.svg"
                        alt="RGR France"
                    >

                    <div class="admin-login__heading">
                        <span class="admin-login__lock" aria-hidden="true">
                            <svg viewBox="0 0 24 24" focusable="false">
                                <path d="M7 10V7a5 5 0 0 1 10 0v3m-11 0h12v10H6V10Zm6 4v3" />
                            </svg>
                        </span>
                        <p>Accès réservé</p>
                        <h1>Espace administration</h1>
                    </div>

                    <?php if ($loginError !== null): ?>
                        <div class="admin-login__error" role="alert">
                            <?= $escape($loginError) ?>
                        </div>
                    <?php endif; ?>

                    <form class="admin-login__form" method="post" action="">
                        <input
                            type="hidden"
                            name="csrf_token"
                            value="<?= $escape((string) $_SESSION['admin_login_csrf']) ?>"
                        >

                        <div class="admin-login__honeypot" aria-hidden="true">
                            <label for="website">Site internet</label>
                            <input id="website" name="website" type="text" tabindex="-1" autocomplete="off">
                        </div>

                        <div class="admin-login__field">
                            <label for="username">Identifiant</label>
                            <div class="admin-login__control">
                                <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false">
                                    <circle cx="12" cy="8" r="4" />
                                    <path d="M4.5 21a7.5 7.5 0 0 1 15 0" />
                                </svg>
                                <input
                                    id="username"
                                    name="username"
                                    type="text"
                                    value="<?= $escape($username) ?>"
                                    autocomplete="username"
                                    required
                                    autofocus
                                >
                            </div>
                        </div>

                        <div class="admin-login__field">
                            <label for="password">Mot de passe</label>
                            <div class="admin-login__control">
                                <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false">
                                    <rect x="5" y="10" width="14" height="11" />
                                    <path d="M8 10V7a4 4 0 0 1 8 0v3" />
                                </svg>
                                <input
                                    id="password"
                                    name="password"
                                    type="password"
                                    autocomplete="current-password"
                                    required
                                >
                                <button
                                    class="admin-login__password-toggle"
                                    type="button"
                                    aria-label="Afficher le mot de passe"
                                    aria-pressed="false"
                                    data-password-toggle
                                >
                                    <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false">
                                        <path d="M2 12s3.5-6 10-6 10 6 10 6-3.5 6-10 6S2 12 2 12Z" />
                                        <circle cx="12" cy="12" r="2.5" />
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <div class="admin-login__options">
                            <label class="admin-login__remember">
                                <input type="checkbox" name="remember_me" value="1">
                                <span>Se souvenir de moi</span>
                            </label>
                        </div>

                        <button class="admin-login__submit" type="submit">
                            Se connecter
                            <span aria-hidden="true">→</span>
                        </button>
                    </form>

                    <p class="admin-login__security-message">
                        Accès strictement réservé au personnel autorisé.
                    </p>
                </div>
            </section>
        </main>

        <script src="<?= $escape(BASE_URL) ?>/assets/js/pages/admin-login.js?v=1" defer></script>
    </body>
</html>
