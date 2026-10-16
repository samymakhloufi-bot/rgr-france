<?php

declare(strict_types=1);

if (session_status() === PHP_SESSION_ACTIVE) {
    return;
}

$appEnvironment = getenv('APP_ENV') ?: 'local';
$isProduction = $appEnvironment === 'production';

ini_set('session.use_strict_mode', '1');
ini_set('session.use_only_cookies', '1');
ini_set('session.cookie_httponly', '1');
ini_set('session.cookie_samesite', 'Strict');

session_name('rgr_session');

session_set_cookie_params([
    'lifetime' => 0,
    'path' => '/',
    'secure' => $isProduction,
    'httponly' => true,
    'samesite' => 'Strict',
]);

session_start();