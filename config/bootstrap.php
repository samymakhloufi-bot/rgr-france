<?php

declare(strict_types=1);

$projectRoot = dirname(__DIR__);
$autoloadPath = $projectRoot . '/vendor/autoload.php';

if (!is_file($autoloadPath)) {
    throw new RuntimeException(
        'Autoload Composer introuvable. Exécutez : composer install'
    );
}

require_once $autoloadPath;
require_once __DIR__ . '/app.php';
require_once __DIR__ . '/database.php';