<?php

declare(strict_types=1);

$appUrl = rtrim((string) (getenv('APP_URL') ?: 'http://localhost:8080'), '/');

if (!defined('BASE_URL')) {
    define('BASE_URL', $appUrl);
}

