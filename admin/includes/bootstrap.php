<?php

declare(strict_types=1);

use App\Repository\AdminContentRepository;

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

$projectRoot = dirname(__DIR__, 2);

require_once $projectRoot . '/config/app.php';
require_once $projectRoot . '/config/database.php';
require_once $projectRoot . '/src/Repository/AdminContentRepository.php';

if (
    !isset($_SESSION['user_id'], $_SESSION['user_role'])
    || $_SESSION['user_role'] !== 'ADMIN'
) {
    header('Location: ' . BASE_URL . '/admin-login.php');
    exit;
}

$_SESSION['admin_csrf'] ??= bin2hex(random_bytes(32));
$_SESSION['admin_logout_csrf'] ??= bin2hex(random_bytes(32));

$adminRepository = new AdminContentRepository($pdo);
$adminName = trim((string) ($_SESSION['username'] ?? 'Administrateur'));

$escape = static function (?string $value): string {
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
};

$adminVerifyCsrf = static function (?string $token): void {
    $sessionToken = $_SESSION['admin_csrf'] ?? '';

    if (
        !is_string($sessionToken)
        || $sessionToken === ''
        || !is_string($token)
        || !hash_equals($sessionToken, $token)
    ) {
        http_response_code(403);
        exit('Requête invalide.');
    }
};

$adminFlash = static function (string $type, string $message): void {
    $_SESSION['admin_flash'] = ['type' => $type, 'message' => $message];
};

$adminPullFlash = static function (): ?array {
    $flash = $_SESSION['admin_flash'] ?? null;
    unset($_SESSION['admin_flash']);

    return is_array($flash) ? $flash : null;
};

$adminStoreImage = static function (array $file, string $directory) use ($projectRoot): string {
    if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
        throw new RuntimeException('Le fichier image n’a pas pu être envoyé.');
    }

    if ((int) ($file['size'] ?? 0) > 8 * 1024 * 1024) {
        throw new RuntimeException('Chaque image est limitée à 8 Mo.');
    }

    $temporaryPath = (string) ($file['tmp_name'] ?? '');
    $mimeType = (new finfo(FILEINFO_MIME_TYPE))->file($temporaryPath);
    $extensions = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/webp' => 'webp',
    ];

    if (!isset($extensions[$mimeType])) {
        throw new RuntimeException('Format refusé. Utilisez JPG, PNG ou WebP.');
    }

    $safeDirectory = match ($directory) {
        'vehicles' => 'vehicles',
        'products' => 'products',
        default => throw new RuntimeException('Dossier d’image invalide.'),
    };

    $targetDirectory = $projectRoot . '/assets/images/' . $safeDirectory;

    if (!is_dir($targetDirectory) && !mkdir($targetDirectory, 0775, true)) {
        throw new RuntimeException('Impossible de créer le dossier des images.');
    }

    $filename = bin2hex(random_bytes(16)) . '.' . $extensions[$mimeType];
    $targetPath = $targetDirectory . '/' . $filename;

    if (!move_uploaded_file($temporaryPath, $targetPath)) {
        throw new RuntimeException('Impossible d’enregistrer l’image.');
    }

    return 'assets/images/' . $safeDirectory . '/' . $filename;
};

$adminDeleteImageFile = static function (?string $relativePath) use ($projectRoot): void {
    if ($relativePath === null) {
        return;
    }

    $relativePath = ltrim(str_replace('\\', '/', $relativePath), '/');

    if (!str_starts_with($relativePath, 'assets/images/')) {
        return;
    }

    $absolutePath = $projectRoot . '/' . $relativePath;

    if (is_file($absolutePath)) {
        unlink($absolutePath);
    }
};

$adminRedirect = static function (string $path): never {
    header('Location: ' . BASE_URL . $path);
    exit;
};

$adminNullableString = static function (mixed $value): ?string {
    $value = trim((string) $value);
    return $value === '' ? null : $value;
};

$adminNullableInt = static function (mixed $value): ?int {
    return $value === '' || $value === null ? null : (int) $value;
};

$adminNullableFloat = static function (mixed $value): ?float {
    if ($value === '' || $value === null) {
        return null;
    }

    return (float) str_replace(',', '.', (string) $value);
};

$adminImageUrl = static function (?string $path, ?string $directory = null): ?string {
    if ($path === null || trim($path) === '') {
        return null;
    }

    if (filter_var($path, FILTER_VALIDATE_URL) !== false) {
        return $path;
    }

    $path = ltrim(str_replace('\\', '/', $path), '/');

    if (str_starts_with($path, 'assets/')) {
        return BASE_URL . '/' . $path;
    }

    if (str_starts_with($path, 'images/')) {
        return BASE_URL . '/assets/' . $path;
    }

    if ($directory !== null && !str_starts_with($path, $directory . '/')) {
        $path = $directory . '/' . $path;
    }

    return BASE_URL . '/assets/images/' . $path;
};
