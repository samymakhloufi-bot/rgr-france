<?php

declare(strict_types=1);

require_once __DIR__ . '/config/bootstrap.php';

$activePage = 'Nos avis';
$metaDescription = 'Découvrez les avis des clients accompagnés par RGR France pour leur importation, leur homologation ou l’entretien de leur véhicule américain.';
$pageCss = BASE_URL . '/assets/css/pages/reviews.css?v=1';

$googleMapsUrl = 'https://www.google.com/maps/search/?api=1&query=RGR+France+Chéroy';
$googleReviewUrl = $googleMapsUrl;

/*
 * ================================================================
 * FUTURE CONNEXION À GOOGLE PLACES API (NEW)
 * ================================================================
 *
 * 1. Ajouter dans le fichier .env :
 *
 *    GOOGLE_PLACES_API_KEY=votre_cle_serveur
 *    GOOGLE_PLACE_ID=identifiant_google_de_rgr_france
 *
 * 2. Charger ces valeurs dans $_ENV depuis config/app.php.
 *
 * 3. Supprimer le tableau fictif situé plus bas et décommenter ce bloc.
 *    La clé reste côté PHP : elle ne doit jamais être envoyée au JavaScript.
 *
 * Place Details (New) renvoie au maximum cinq avis classés par pertinence.
 * Pour afficher l’intégralité des avis, il faudra utiliser Google Business
 * Profile API avec l’autorisation du compte propriétaire de l’établissement.
 * La répartition détaillée 1 à 5 étoiles n’est pas fournie par Place Details.
 */

// $googleApiKey = (string) ($_ENV['GOOGLE_PLACES_API_KEY'] ?? '');
// $googlePlaceId = (string) ($_ENV['GOOGLE_PLACE_ID'] ?? '');
//
// if ($googleApiKey !== '' && $googlePlaceId !== '') {
//     $endpoint = sprintf(
//         'https://places.googleapis.com/v1/places/%s?languageCode=fr&regionCode=FR',
//         rawurlencode($googlePlaceId)
//     );
//
//     $curl = curl_init($endpoint);
//     curl_setopt_array($curl, [
//         CURLOPT_RETURNTRANSFER => true,
//         CURLOPT_CONNECTTIMEOUT => 5,
//         CURLOPT_TIMEOUT => 10,
//         CURLOPT_HTTPHEADER => [
//             'Accept: application/json',
//             'X-Goog-Api-Key: ' . $googleApiKey,
//             'X-Goog-FieldMask: displayName,rating,userRatingCount,reviews,googleMapsUri',
//         ],
//     ]);
//
//     $response = curl_exec($curl);
//     $statusCode = (int) curl_getinfo($curl, CURLINFO_RESPONSE_CODE);
//     $curlError = curl_error($curl);
//     curl_close($curl);
//
//     if ($response !== false && $statusCode === 200) {
//         $googleData = json_decode($response, true, 512, JSON_THROW_ON_ERROR);
//
//         $averageRating = (float) ($googleData['rating'] ?? 0);
//         $totalReviewCount = (int) ($googleData['userRatingCount'] ?? 0);
//         $googleMapsUrl = (string) ($googleData['googleMapsUri'] ?? $googleMapsUrl);
//         $googleReviewUrl = 'https://search.google.com/local/writereview?placeid=' . rawurlencode($googlePlaceId);
//
//         $reviews = array_map(
//             static function (array $review): array {
//                 return [
//                     'author' => (string) ($review['authorAttribution']['displayName'] ?? 'Client Google'),
//                     'author_url' => (string) ($review['authorAttribution']['uri'] ?? ''),
//                     'photo_url' => (string) ($review['authorAttribution']['photoUri'] ?? ''),
//                     'rating' => (int) ($review['rating'] ?? 0),
//                     'relative_date' => (string) ($review['relativePublishTimeDescription'] ?? ''),
//                     'published_at' => (string) ($review['publishTime'] ?? ''),
//                     'content' => (string) ($review['text']['text'] ?? ''),
//                     'review_url' => (string) ($review['googleMapsUri'] ?? ''),
//                 ];
//             },
//             $googleData['reviews'] ?? []
//         );
//     } else {
//         error_log('Google Places API : ' . $statusCode . ' — ' . $curlError);
//     }
// }

/* Données fictives utilisées pendant le développement du front-end. */
$averageRating = 4.9;
$totalReviewCount = 47;

$ratingDistribution = [
    5 => 92,
    4 => 6,
    3 => 1,
    2 => 0,
    1 => 1,
];

$reviews = [
    [
        'author' => 'Maxime D.',
        'rating' => 5,
        'relative_date' => 'Il y a 2 semaines',
        'published_at' => '2026-09-30',
        'content' => 'Un accompagnement au top du début à la fin pour l’importation de mon Ford F-150. L’équipe est disponible, transparente et très professionnelle. Le véhicule est arrivé dans les délais et conforme à toutes leurs promesses.',
    ],
    [
        'author' => 'Sophie L.',
        'rating' => 5,
        'relative_date' => 'Il y a 1 mois',
        'published_at' => '2026-09-12',
        'content' => 'Nous avons fait homologuer notre Lincoln Navigator avec RGR France. Démarches claires, suivi régulier et une équipe vraiment à l’écoute. On se sent en confiance et bien conseillé.',
    ],
    [
        'author' => 'Thomas B.',
        'rating' => 5,
        'relative_date' => 'Il y a 1 mois',
        'published_at' => '2026-09-04',
        'content' => 'Achat d’une Mustang GT importée via RGR France. Une expérience incroyable du premier contact à la livraison. Véhicule magnifique et parfaitement conforme.',
    ],
    [
        'author' => 'Laura M.',
        'rating' => 5,
        'relative_date' => 'Il y a 2 mois',
        'published_at' => '2026-08-18',
        'content' => 'Un grand merci à toute l’équipe pour l’homologation de notre Ford Raptor. Processus bien expliqué, aucune mauvaise surprise et un suivi impeccable.',
    ],
    [
        'author' => 'Julien R.',
        'rating' => 5,
        'relative_date' => 'Il y a 2 mois',
        'published_at' => '2026-08-02',
        'content' => 'Entretien et optimisation de mon F-150 chez RGR France. Travail de grande qualité, équipe passionnée et très compétente. Mon véhicule n’a jamais été aussi performant.',
    ],
    [
        'author' => 'Clément V.',
        'rating' => 5,
        'relative_date' => 'Il y a 3 mois',
        'published_at' => '2026-07-11',
        'content' => 'Une équipe sérieuse et passionnée. Ils ont pris en charge l’importation et l’homologation de mon Bronco dans des conditions parfaites. Toujours disponibles pour répondre aux questions.',
    ],
    [
        'author' => 'Nicolas P.',
        'rating' => 4,
        'relative_date' => 'Il y a 4 mois',
        'published_at' => '2026-06-20',
        'content' => 'Très bonne expérience pour la recherche et l’importation de mon véhicule. Les délais ont été un peu longs côté transport maritime, mais le suivi de RGR a toujours été précis.',
    ],
    [
        'author' => 'Elodie C.',
        'rating' => 5,
        'relative_date' => 'Il y a 5 mois',
        'published_at' => '2026-05-16',
        'content' => 'Des explications claires et un vrai accompagnement personnalisé. Je recommande RGR France à tous ceux qui souhaitent acheter une américaine sans mauvaise surprise.',
    ],
    [
        'author' => 'David G.',
        'rating' => 5,
        'relative_date' => 'Il y a 6 mois',
        'published_at' => '2026-04-09',
        'content' => 'Diagnostic sérieux et réparation parfaitement réalisée sur ma Mustang. Une adresse rare pour les propriétaires de véhicules américains.',
    ],
];

$escape = static function (?string $value): string {
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
};

$formatRating = static function (float $rating): string {
    return number_format($rating, 1, ',', '');
};
?>

<!DOCTYPE html>
<html lang="fr">
    <?php include __DIR__ . '/includes/head.php'; ?>

    <body>
        <?php include __DIR__ . '/includes/header.php'; ?>

        <main class="reviews-page">
            <section class="reviews-hero" aria-labelledby="reviews-title">
                <div class="reviews-container reviews-hero__content">
                    <p class="reviews-eyebrow">L’expérience RGR France</p>
                    <h1 id="reviews-title">Ils nous font confiance</h1>
                    <p>
                        Des passionnés, des professionnels et des familles nous
                        confient leur projet. Découvrez leur expérience avec RGR France.
                    </p>
                </div>
            </section>

            <section class="reviews-summary" aria-label="Résumé des avis Google">
                <div class="reviews-container reviews-summary__layout">
                    <div class="reviews-summary__score">
                        <strong><?= $escape($formatRating($averageRating)) ?><span>/5</span></strong>
                        <div class="reviews-stars" aria-label="<?= $escape($formatRating($averageRating)) ?> étoiles sur 5">
                            <span aria-hidden="true">★★★★★</span>
                        </div>
                        <p><span class="reviews-google-mark" aria-hidden="true">G</span> Avis Google · <?= $totalReviewCount ?> avis</p>
                    </div>

                    <div class="reviews-distribution" aria-label="Répartition des notes">
                        <?php foreach ($ratingDistribution as $rating => $percentage): ?>
                            <div class="reviews-distribution__row">
                                <span><?= $rating ?> étoile<?= $rating > 1 ? 's' : '' ?></span>
                                <span class="reviews-distribution__track" aria-hidden="true">
                                    <span style="width: <?= $percentage ?>%"></span>
                                </span>
                                <strong><?= $percentage ?> %</strong>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <div class="reviews-summary__actions">
                        <a class="reviews-button" href="<?= $escape($googleReviewUrl) ?>" target="_blank" rel="noopener noreferrer">
                            Laisser un avis <span aria-hidden="true">→</span>
                        </a>
                        <a class="reviews-button reviews-button--outline" href="<?= $escape($googleMapsUrl) ?>" target="_blank" rel="noopener noreferrer">
                            Voir sur Google <span aria-hidden="true">↗</span>
                        </a>
                    </div>
                </div>
            </section>

            <section class="reviews-list-section" aria-labelledby="reviews-list-title">
                <div class="reviews-container">
                    <div class="reviews-heading">
                        <div>
                            <p class="reviews-eyebrow">Avis clients</p>
                            <h2 id="reviews-list-title">Les avis de nos clients</h2>
                        </div>

                        <div class="reviews-toolbar">
                            <p><span data-visible-review-count><?= min(6, count($reviews)) ?></span> avis affichés sur <?= count($reviews) ?></p>
                            <label class="reviews-sort">
                                <span class="sr-only">Trier les avis</span>
                                <select id="reviews-sort" name="reviews-sort">
                                    <option value="recent">Les plus récents</option>
                                    <option value="rating-desc">Les mieux notés</option>
                                    <option value="rating-asc">Les moins bien notés</option>
                                </select>
                            </label>
                        </div>
                    </div>

                    <div class="reviews-grid" id="reviews-grid">
                        <?php foreach ($reviews as $index => $review): ?>
                            <article
                                class="review-card<?= $index >= 6 ? ' review-card--hidden' : '' ?>"
                                data-review-card
                                data-rating="<?= (int) $review['rating'] ?>"
                                data-date="<?= $escape($review['published_at']) ?>"
                            >
                                <header class="review-card__header">
                                    <span class="review-card__avatar" aria-hidden="true">
                                        <?= $escape(strtoupper(substr($review['author'], 0, 1))) ?>
                                    </span>
                                    <div>
                                        <h3><?= $escape($review['author']) ?></h3>
                                        <div class="review-card__meta">
                                            <span class="reviews-stars" aria-label="<?= (int) $review['rating'] ?> étoiles sur 5">
                                                <span aria-hidden="true"><?= str_repeat('★', (int) $review['rating']) ?><?= str_repeat('☆', 5 - (int) $review['rating']) ?></span>
                                            </span>
                                            <time datetime="<?= $escape($review['published_at']) ?>"><?= $escape($review['relative_date']) ?></time>
                                        </div>
                                    </div>
                                    <span class="reviews-google-mark" aria-label="Avis publié sur Google">G</span>
                                </header>

                                <p class="review-card__content"><?= $escape($review['content']) ?></p>

                                <footer class="review-card__footer">
                                    <span aria-hidden="true">◆</span>
                                    Avis Google
                                </footer>
                            </article>
                        <?php endforeach; ?>
                    </div>

                    <?php if (count($reviews) > 6): ?>
                        <button class="reviews-load-more" type="button" data-load-more-reviews>
                            Afficher plus d’avis <span aria-hidden="true">→</span>
                        </button>
                    <?php endif; ?>
                </div>
            </section>

            <aside class="reviews-assurance" aria-label="Les engagements RGR France">
                <div class="reviews-container reviews-assurance__grid">
                    <article><span>01</span><div><h2>Accompagnement personnalisé</h2><p>Un interlocuteur dédié à chaque étape de votre projet.</p></div></article>
                    <article><span>02</span><div><h2>Transparence</h2><p>Des informations claires et un suivi régulier, sans mauvaise surprise.</p></div></article>
                    <article><span>03</span><div><h2>Expertise Ford &amp; Lincoln</h2><p>Une connaissance approfondie des véhicules et du marché américain.</p></div></article>
                </div>
            </aside>

            <section class="reviews-cta" aria-labelledby="reviews-cta-title">
                <div class="reviews-container reviews-cta__content">
                    <p class="reviews-eyebrow">Rejoignez nos clients</p>
                    <h2 id="reviews-cta-title">Votre projet sera le prochain&nbsp;?</h2>
                    <p>Échangez avec notre équipe pour concrétiser votre projet automobile américain en toute sérénité.</p>
                    <a class="reviews-button" href="<?= $escape(BASE_URL) ?>/contact.php">
                        Échanger avec notre équipe <span aria-hidden="true">→</span>
                    </a>
                </div>
            </section>
        </main>

        <?php include __DIR__ . '/includes/footer.php'; ?>
        <script src="<?= $escape(BASE_URL) ?>/assets/js/pages/review.js?v=1" defer></script>
    </body>
</html>
