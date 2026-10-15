<?php

declare(strict_types=1);

namespace App\Support;

final class ImageUrl
{
    public static function resolve(
        ?string $imagePath,
        ?string $defaultDirectory = null
    ): ?string {
        if ($imagePath === null || trim($imagePath) === '') {
            return null;
        }

        $imagePath = trim(
            str_replace('\\', '/', $imagePath)
        );

        /*
         * Les URL externes sont laissées intactes.
         */
        if (
            filter_var($imagePath, FILTER_VALIDATE_URL) !== false
            && in_array(
                parse_url($imagePath, PHP_URL_SCHEME),
                ['http', 'https'],
                true
            )
        ) {
            return $imagePath;
        }

        $imagePath = ltrim($imagePath, '/');

        /*
         * Nouveau format  :
         * assets/images/vehicles/image.webp
         */
        if (str_starts_with($imagePath, 'assets/')) {
            return BASE_URL . '/' . $imagePath;
        }

        /*
         * Compatibilité avec les anciens chemins :
         * images/vehicles/image.webp
         */
        if (str_starts_with($imagePath, 'images/')) {
            return BASE_URL . '/assets/' . $imagePath;
        }

        /*
         * Compatibilité avec :
         * vehicles/image.webp
         * products/image.webp
         */
        if (
            str_starts_with($imagePath, 'vehicles/')
            || str_starts_with($imagePath, 'products/')
        ) {
            return BASE_URL . '/assets/images/' . $imagePath;
        }

        /*
         * Compatibilité avec un simple nom de fichier.
         */
        if ($defaultDirectory !== null) {
            $defaultDirectory = trim(
                $defaultDirectory,
                '/'
            );

            return BASE_URL
                . '/assets/images/'
                . $defaultDirectory
                . '/'
                . $imagePath;
        }

        return BASE_URL . '/' . $imagePath;
    }
}