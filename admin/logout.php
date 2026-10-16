<?php

declare(strict_types=1);


require_once dirname(__DIR__) . '/config/session.php';
require_once dirname(__DIR__) . '/config/app.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . BASE_URL . '/admin/admin-index.php');
    exit;
}

$sessionToken = (string) ($_SESSION['admin_logout_csrf'] ?? '');
$submittedToken = (string) ($_POST['csrf_token'] ?? '');

if (
    $sessionToken === ''
    || $submittedToken === ''
    || !hash_equals($sessionToken, $submittedToken)
) {
    http_response_code(403);
    exit('Requête invalide.');
}

$_SESSION = [];

if (ini_get('session.use_cookies')) {
    $cookie = session_get_cookie_params();
}

session_destroy();

header('Location: ' . BASE_URL . '/admin-login.php');
exit;
