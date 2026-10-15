<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/bootstrap.php';

$vehicleId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT) ?: null;
$vehicle = $vehicleId === null ? null : $adminRepository->findById('vehicles', $vehicleId);

if ($vehicleId !== null && $vehicle === null) {
    http_response_code(404);
    exit('Véhicule introuvable.');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $adminVerifyCsrf($_POST['csrf_token'] ?? null);
    $action = (string) ($_POST['action'] ?? 'save');
    $postedId = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT) ?: null;

    try {
        if ($action === 'delete_image' && $postedId !== null) {
            $imageId = filter_input(INPUT_POST, 'image_id', FILTER_VALIDATE_INT);
            $path = $imageId ? $adminRepository->deleteVehicleImage($postedId, $imageId) : null;
            $adminDeleteImageFile($path);
            $adminFlash('success', 'Photo supprimée.');
            $adminRedirect('/admin/pages/vehicle-form.php?id=' . $postedId);
        }

        if ($action === 'cover_image' && $postedId !== null) {
            $imageId = filter_input(INPUT_POST, 'image_id', FILTER_VALIDATE_INT);
            if (!$imageId) {
                throw new RuntimeException('Photo invalide.');
            }
            $adminRepository->setVehicleCover($postedId, $imageId);
            $adminFlash('success', 'Photo de couverture mise à jour.');
            $adminRedirect('/admin/pages/vehicle-form.php?id=' . $postedId);
        }

        $reference = trim((string) ($_POST['internal_reference'] ?? ''));
        $brand = trim((string) ($_POST['brand'] ?? ''));
        $model = trim((string) ($_POST['model'] ?? ''));

        if ($reference === '' || $brand === '' || $model === '') {
            throw new InvalidArgumentException('La référence, la marque et le modèle sont obligatoires.');
        }

        $data = [
            'status_id' => max(1, (int) ($_POST['status_id'] ?? 1)),
            'internal_reference' => $reference,
            'vin' => $adminNullableString($_POST['vin'] ?? null),
            'brand' => $brand,
            'model' => $model,
            'trim_level' => $adminNullableString($_POST['trim_level'] ?? null),
            'registration_number' => $adminNullableString($_POST['registration_number'] ?? null),
            'registration_year' => $adminNullableInt($_POST['registration_year'] ?? null),
            'mileage_km' => $adminNullableInt($_POST['mileage_km'] ?? null),
            'fuel_type' => $adminNullableString($_POST['fuel_type'] ?? null),
            'transmission' => $adminNullableString($_POST['transmission'] ?? null),
            'power_hp' => $adminNullableInt($_POST['power_hp'] ?? null),
            'exterior_color' => $adminNullableString($_POST['exterior_color'] ?? null),
            'price' => $adminNullableFloat($_POST['price'] ?? null),
            'description' => $adminNullableString($_POST['description'] ?? null),
            'is_published' => isset($_POST['is_published']) ? 1 : 0,
        ];

        $savedId = $adminRepository->save('vehicles', $data, $postedId);
        $existingImages = $adminRepository->findVehicleImages($savedId);
        $files = $_FILES['images'] ?? null;

        if (is_array($files) && is_array($files['name'] ?? null)) {
            foreach ($files['name'] as $index => $name) {
                if (($files['error'][$index] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
                    continue;
                }
                $file = ['name' => $name, 'type' => $files['type'][$index] ?? '', 'tmp_name' => $files['tmp_name'][$index] ?? '', 'error' => $files['error'][$index] ?? UPLOAD_ERR_NO_FILE, 'size' => $files['size'][$index] ?? 0];
                $path = $adminStoreImage($file, 'vehicles');
                $adminRepository->addVehicleImage($savedId, $path, $brand . ' ' . $model, $existingImages === [] && $index === array_key_first($files['name']));
                $existingImages[] = ['file_path' => $path];
            }
        }

        $adminFlash('success', $postedId === null ? 'Véhicule ajouté.' : 'Véhicule mis à jour.');
        $adminRedirect('/admin/pages/vehicle-form.php?id=' . $savedId);
    } catch (Throwable $exception) {
        error_log($exception->getMessage());
        $adminFlash('error', $exception instanceof InvalidArgumentException ? $exception->getMessage() : 'Impossible d’enregistrer le véhicule.');
        $adminRedirect('/admin/pages/vehicle-form.php' . ($postedId ? '?id=' . $postedId : ''));
    }
}

$statuses = $adminRepository->findVehicleStatuses();
$images = $vehicleId === null ? [] : $adminRepository->findVehicleImages($vehicleId);
$pageTitle = $vehicle === null ? 'Ajouter un véhicule' : 'Modifier le véhicule';
$pageEyebrow = 'Catalogue automobile';
$activeAdminPage = 'vehicles';

require __DIR__ . '/../includes/page-start.php';
?>
<form class="admin-edit-form" method="post" enctype="multipart/form-data">
    <input type="hidden" name="csrf_token" value="<?= $escape((string) $_SESSION['admin_csrf']) ?>">
    <?php if ($vehicleId !== null): ?><input type="hidden" name="id" value="<?= $vehicleId ?>"><?php endif; ?>
    <section class="admin-form-section">
        <div class="admin-form-section__heading"><p>01</p><div><h2>Identification</h2><span>Les informations principales affichées dans le catalogue.</span></div></div>
        <div class="admin-form-grid">
            <label><span>Référence interne *</span><input name="internal_reference" required value="<?= $escape((string) ($vehicle['internal_reference'] ?? '')) ?>"></label>
            <label><span>Statut interne</span><select name="status_id"><?php foreach ($statuses as $status): ?><option value="<?= (int) $status['id'] ?>" <?= (int) ($vehicle['status_id'] ?? 1) === (int) $status['id'] ? 'selected' : '' ?>><?= $escape((string) $status['label']) ?></option><?php endforeach; ?></select></label>
            <label><span>Marque *</span><input name="brand" required value="<?= $escape((string) ($vehicle['brand'] ?? '')) ?>"></label>
            <label><span>Modèle *</span><input name="model" required value="<?= $escape((string) ($vehicle['model'] ?? '')) ?>"></label>
            <label><span>Finition</span><input name="trim_level" value="<?= $escape((string) ($vehicle['trim_level'] ?? '')) ?>"></label>
            <label><span>VIN</span><input name="vin" maxlength="17" value="<?= $escape((string) ($vehicle['vin'] ?? '')) ?>"></label>
            <label><span>Immatriculation</span><input name="registration_number" value="<?= $escape((string) ($vehicle['registration_number'] ?? '')) ?>"></label>
            <label><span>Année</span><input type="number" name="registration_year" min="1900" max="2100" value="<?= $escape((string) ($vehicle['registration_year'] ?? '')) ?>"></label>
        </div>
    </section>
    <section class="admin-form-section">
        <div class="admin-form-section__heading"><p>02</p><div><h2>Caractéristiques</h2><span>Données utilisées sur la fiche et dans les filtres publics.</span></div></div>
        <div class="admin-form-grid">
            <label><span>Kilométrage</span><input type="number" min="0" name="mileage_km" value="<?= $escape((string) ($vehicle['mileage_km'] ?? '')) ?>"></label>
            <label><span>Carburant</span><input name="fuel_type" placeholder="Essence, E85…" value="<?= $escape((string) ($vehicle['fuel_type'] ?? '')) ?>"></label>
            <label><span>Transmission</span><input name="transmission" value="<?= $escape((string) ($vehicle['transmission'] ?? '')) ?>"></label>
            <label><span>Puissance (ch)</span><input type="number" min="0" name="power_hp" value="<?= $escape((string) ($vehicle['power_hp'] ?? '')) ?>"></label>
            <label><span>Couleur</span><input name="exterior_color" value="<?= $escape((string) ($vehicle['exterior_color'] ?? '')) ?>"></label>
            <label><span>Prix (€)</span><input type="number" min="0" step="0.01" name="price" value="<?= $escape((string) ($vehicle['price'] ?? '')) ?>"></label>
            <label class="admin-form-field--full"><span>Description</span><textarea name="description" rows="8"><?= $escape((string) ($vehicle['description'] ?? '')) ?></textarea></label>
        </div>
    </section>
    <section class="admin-form-section">
        <div class="admin-form-section__heading"><p>03</p><div><h2>Photos et publication</h2><span>La première photo envoyée devient la couverture.</span></div></div>
        <label class="admin-upload"><input type="file" name="images[]" accept="image/jpeg,image/png,image/webp" multiple><span>Ajouter des photos</span><small>JPG, PNG ou WebP · 8 Mo maximum par image</small></label>
        <label class="admin-switch"><input type="checkbox" name="is_published" value="1" <?= !isset($vehicle['is_published']) || !empty($vehicle['is_published']) ? 'checked' : '' ?>><span>Publier ce véhicule sur le site</span></label>
    </section>
    <div class="admin-form-actions"><a href="<?= $escape(BASE_URL) ?>/admin/pages/vehicles.php">Annuler</a><button type="submit">Enregistrer le véhicule</button></div>
</form>

<?php if ($images !== []): ?>
<section class="admin-form-section admin-gallery-manager">
    <div class="admin-form-section__heading"><p>04</p><div><h2>Galerie actuelle</h2><span>Choisissez la couverture ou retirez une photo.</span></div></div>
    <div class="admin-gallery-grid">
        <?php foreach ($images as $image): ?>
            <article><img src="<?= $escape((string) $adminImageUrl((string) $image['file_path'], 'vehicles')) ?>" alt=""><div><?php if (!empty($image['is_cover'])): ?><span class="admin-badge admin-badge--success">Couverture</span><?php else: ?><form method="post"><input type="hidden" name="csrf_token" value="<?= $escape((string) $_SESSION['admin_csrf']) ?>"><input type="hidden" name="id" value="<?= $vehicleId ?>"><input type="hidden" name="image_id" value="<?= (int) $image['id'] ?>"><button name="action" value="cover_image">Définir en couverture</button></form><?php endif; ?><form method="post" data-confirm="Supprimer cette photo ?"><input type="hidden" name="csrf_token" value="<?= $escape((string) $_SESSION['admin_csrf']) ?>"><input type="hidden" name="id" value="<?= $vehicleId ?>"><input type="hidden" name="image_id" value="<?= (int) $image['id'] ?>"><button class="admin-danger-button" name="action" value="delete_image">Supprimer</button></form></div></article>
        <?php endforeach; ?>
    </div>
</section>
<?php endif; ?>
<?php require __DIR__ . '/../includes/page-end.php'; ?>
