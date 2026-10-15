<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $adminVerifyCsrf($_POST['csrf_token'] ?? null);
    $vehicleId = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

    if ($vehicleId === false || $vehicleId === null) {
        $adminFlash('error', 'Véhicule invalide.');
    } else {
        try {
            foreach ($adminRepository->deleteVehicleWithImages($vehicleId) as $path) {
                $adminDeleteImageFile((string) $path);
            }
            $adminFlash('success', 'Le véhicule et ses photos ont été supprimés.');
        } catch (Throwable $exception) {
            error_log($exception->getMessage());
            $adminFlash('error', 'Impossible de supprimer ce véhicule.');
        }
    }

    $adminRedirect('/admin/pages/vehicles.php');
}

$vehicles = $adminRepository->findAll('vehicles');
$vehicleCovers = [];

foreach ($vehicles as $vehicle) {
    $images = $adminRepository->findVehicleImages((int) $vehicle['id']);
    $vehicleCovers[(int) $vehicle['id']] = $images[0]['file_path'] ?? null;
}

$pageTitle = 'Véhicules';
$pageEyebrow = 'Catalogue automobile';
$activeAdminPage = 'vehicles';
$pageAction = ['label' => 'Ajouter un véhicule', 'href' => '/admin/pages/vehicle-form.php'];

require __DIR__ . '/../includes/page-start.php';
?>
<section class="admin-panel">
    <div class="admin-toolbar">
        <label class="admin-search">
            <span>Rechercher</span>
            <input type="search" placeholder="Marque, modèle, référence…" data-admin-search="vehicles-list">
        </label>
        <p><strong data-result-count><?= count($vehicles) ?></strong> véhicule(s)</p>
    </div>

    <?php if ($vehicles === []): ?>
        <div class="admin-empty"><h2>Aucun véhicule</h2><p>Ajoutez votre premier véhicule au catalogue.</p></div>
    <?php else: ?>
        <div class="admin-list" id="vehicles-list">
            <?php foreach ($vehicles as $vehicle): ?>
                <?php
                $vehicleId = (int) $vehicle['id'];
                $title = trim((string) ($vehicle['brand'] ?? '') . ' ' . (string) ($vehicle['model'] ?? ''));
                $search = $title . ' ' . ($vehicle['internal_reference'] ?? '') . ' ' . ($vehicle['fuel_type'] ?? '');
                $imageUrl = $adminImageUrl($vehicleCovers[$vehicleId], 'vehicles');
                ?>
                <article class="admin-list-card" data-admin-row data-search-value="<?= $escape(strtolower($search)) ?>">
                    <div class="admin-list-card__media">
                        <?php if ($imageUrl !== null): ?><img src="<?= $escape($imageUrl) ?>" alt=""><?php else: ?><span>RGR</span><?php endif; ?>
                    </div>
                    <div class="admin-list-card__content">
                        <div class="admin-list-card__heading">
                            <div><p><?= $escape((string) ($vehicle['internal_reference'] ?? 'Sans référence')) ?></p><h2><?= $escape($title !== '' ? $title : 'Véhicule sans nom') ?></h2></div>
                            <span class="admin-badge <?= !empty($vehicle['is_published']) ? 'admin-badge--success' : '' ?>"><?= !empty($vehicle['is_published']) ? 'Publié' : 'Masqué' ?></span>
                        </div>
                        <dl class="admin-list-card__meta">
                            <div><dt>Année</dt><dd><?= $escape((string) ($vehicle['registration_year'] ?? '—')) ?></dd></div>
                            <div><dt>Kilométrage</dt><dd><?= isset($vehicle['mileage_km']) ? number_format((int) $vehicle['mileage_km'], 0, ',', ' ') . ' km' : '—' ?></dd></div>
                            <div><dt>Prix</dt><dd><?= isset($vehicle['price']) ? number_format((float) $vehicle['price'], 0, ',', ' ') . ' €' : 'Sur demande' ?></dd></div>
                        </dl>
                    </div>
                    <div class="admin-list-card__actions">
                        <a href="<?= $escape(BASE_URL) ?>/admin/pages/vehicle-form.php?id=<?= $vehicleId ?>">Modifier</a>
                        <form method="post" data-confirm="Supprimer définitivement ce véhicule et toutes ses photos ?">
                            <input type="hidden" name="csrf_token" value="<?= $escape((string) $_SESSION['admin_csrf']) ?>">
                            <input type="hidden" name="id" value="<?= $vehicleId ?>">
                            <button class="admin-danger-button" type="submit">Supprimer</button>
                        </form>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
        <p class="admin-no-result" data-no-result hidden>Aucun véhicule ne correspond à cette recherche.</p>
    <?php endif; ?>
</section>
<?php require __DIR__ . '/../includes/page-end.php'; ?>
