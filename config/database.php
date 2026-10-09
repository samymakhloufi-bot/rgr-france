<?php

declare(strict_types=1);

$dbHost = (string) (getenv('DB_HOST') ?: 'db');
$dbPort = (int) (getenv('DB_PORT') ?: 3306);
$dbName = (string) (getenv('DB_NAME') ?: 'rgr_france');
$dbUser = (string) (getenv('DB_USER') ?: 'rgr_user');
$dbPassword = (string) (getenv('DB_PASSWORD') ?: '');

$dsn = sprintf(
    'mysql:host=%s;port=%d;dbname=%s;charset=utf8mb4',
    $dbHost,
    $dbPort,
    $dbName
);

try {
    $pdo = new PDO(
        $dsn,
        $dbUser,
        $dbPassword,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]
    );
} catch (PDOException $exception) {
    error_log($exception->getMessage());

    http_response_code(500);
    exit('Connexion à la base de données impossible.');
}

