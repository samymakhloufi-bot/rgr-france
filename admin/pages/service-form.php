<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/bootstrap.php';
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT) ?: null;
$service = $id ? $adminRepository->findById('services', $id) : null;
if ($id !== null && $service === null) { http_response_code(404); exit('Service introuvable.'); }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $adminVerifyCsrf($_POST['csrf_token'] ?? null);
    $postedId = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT) ?: null;
    try {
        $name = trim((string) ($_POST['name'] ?? '')); $slug = trim((string) ($_POST['slug'] ?? ''));
        if ($name === '' || $slug === '') { throw new InvalidArgumentException('Le nom et le slug sont obligatoires.'); }
        $price = $adminNullableFloat($_POST['price'] ?? null);
        $savedId = $adminRepository->save('services', ['name' => $name, 'slug' => $slug, 'description' => $adminNullableString($_POST['description'] ?? null), 'price' => $price, 'base_price' => $price, 'duration_minutes' => $adminNullableInt($_POST['duration_minutes'] ?? null), 'display_order' => max(0, (int) ($_POST['display_order'] ?? 0)), 'is_active' => isset($_POST['is_active']) ? 1 : 0], $postedId);
        $adminFlash('success', $postedId ? 'Service mis à jour.' : 'Service ajouté.');
        $adminRedirect('/admin/pages/service-form.php?id=' . $savedId);
    } catch (Throwable $exception) { error_log($exception->getMessage()); $adminFlash('error', $exception instanceof InvalidArgumentException ? $exception->getMessage() : 'Impossible d’enregistrer ce service.'); $adminRedirect('/admin/pages/service-form.php' . ($postedId ? '?id=' . $postedId : '')); }
}
$pageTitle = $service ? 'Modifier le service' : 'Ajouter un service'; $pageEyebrow = 'Prestations RGR France'; $activeAdminPage = 'services';
require __DIR__ . '/../includes/page-start.php';
?>
<form class="admin-edit-form" method="post"><input type="hidden" name="csrf_token" value="<?= $escape((string) $_SESSION['admin_csrf']) ?>"><?php if ($id): ?><input type="hidden" name="id" value="<?= $id ?>"><?php endif; ?><section class="admin-form-section"><div class="admin-form-section__heading"><p>01</p><div><h2>Informations du service</h2><span>Contenu visible sur le site.</span></div></div><div class="admin-form-grid"><label><span>Nom *</span><input name="name" required value="<?= $escape((string) ($service['name'] ?? '')) ?>" data-slug-source></label><label><span>Slug *</span><input name="slug" required value="<?= $escape((string) ($service['slug'] ?? '')) ?>" data-slug-target></label><label><span>Prix de départ (€)</span><input type="number" min="0" step="0.01" name="price" value="<?= $escape((string) ($service['price'] ?? $service['base_price'] ?? '')) ?>"></label><label><span>Durée indicative (minutes)</span><input type="number" min="0" name="duration_minutes" value="<?= $escape((string) ($service['duration_minutes'] ?? '')) ?>"></label><label><span>Ordre d’affichage</span><input type="number" min="0" name="display_order" value="<?= $escape((string) ($service['display_order'] ?? 0)) ?>"></label><label class="admin-switch"><input type="checkbox" name="is_active" value="1" <?= !isset($service['is_active']) || !empty($service['is_active']) ? 'checked' : '' ?>><span>Afficher ce service sur le site</span></label><label class="admin-form-field--full"><span>Description</span><textarea name="description" rows="10"><?= $escape((string) ($service['description'] ?? '')) ?></textarea></label></div></section><div class="admin-form-actions"><a href="<?= $escape(BASE_URL) ?>/admin/pages/services.php">Annuler</a><button>Enregistrer le service</button></div></form>
<?php require __DIR__ . '/../includes/page-end.php'; ?>
