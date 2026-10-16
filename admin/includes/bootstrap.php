<?php

declare(strict_types=1);

use App\Repository\AdminContentRepository;
use App\Support\ImageUrl;


require_once dirname(__DIR__) . '/vendor/autoload.php';
require_once __DIR__ . '/app.php';
require_once __DIR__ . '/database.php';



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

$userRepository = new UserRepository($pdo);
$currentUser = $userRepository->findById(
    (int) $_SESSION['user_id']
);

if (
    $currentUser === null
    || !$currentUser->isActive()
    || !$currentUser->isAdmin()
) {
    $_SESSION = [];
    session_destroy();

    header('Location: ' . BASE_URL . '/admin-login.php');
    exit;
}

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
    if (
    $temporaryPath === ''
    || !is_uploaded_file($temporaryPath)
) {
    throw new RuntimeException(
        'Le fichier envoyé est invalide.'
    );
}

    $imageInformation = getimagesize($temporaryPath);

    if ($imageInformation === false) {
        throw new RuntimeException(
            'Le fichier n’est pas une image valide.'
        );
    }

    [$width, $height] = $imageInformation;

    if ($width > 8000 || $height > 8000) {
        throw new RuntimeException(
            'Les dimensions de l’image sont trop importantes.'
        );
    }
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

    $imagesRoot = realpath(
    $projectRoot . '/assets/images'
    );

    $absolutePath = realpath(
        $projectRoot . '/' . $relativePath
    );

    if (
        $imagesRoot === false
        || $absolutePath === false
        || !str_starts_with(
            $absolutePath,
            $imagesRoot . DIRECTORY_SEPARATOR
        )
    ) {
        return;
    }

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

$adminImageUrl = static function (
    ?string $path,
    ?string $directory = null
): ?string {
    return ImageUrl::resolve(
        $path,
        $directory
    );
};

$idleTimeout = 30 * 60;
$absoluteTimeout = 8 * 60 * 60;
$currentTime = time();

if (
    isset($_SESSION['last_activity'])
    && $currentTime - $_SESSION['last_activity'] > $idleTimeout
) {
    $_SESSION = [];
    session_destroy();

    header('Location: ' . BASE_URL . '/admin-login.php?expired=1');
    exit;
}

if (
    isset($_SESSION['authenticated_at'])
    && $currentTime - $_SESSION['authenticated_at'] > $absoluteTimeout
) {
    $_SESSION = [];
    session_destroy();

    header('Location: ' . BASE_URL . '/admin-login.php?expired=1');
    exit;
}

$_SESSION['last_activity'] = $currentTime;