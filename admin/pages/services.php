<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $adminVerifyCsrf($_POST['csrf_token'] ?? null);
    $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
    try {
        if (!$id || !$adminRepository->delete('services', $id)) { throw new RuntimeException('Service introuvable.'); }
        $adminFlash('success', 'Service supprimé.');
    } catch (Throwable $exception) { error_log($exception->getMessage()); $adminFlash('error', 'Impossible de supprimer ce service.'); }
    $adminRedirect('/admin/pages/services.php');
}

$services = $adminRepository->findAll('services');
$pageTitle = 'Services'; $pageEyebrow = 'Prestations RGR France'; $activeAdminPage = 'services';
$pageAction = ['label' => 'Ajouter un service', 'href' => '/admin/pages/service-form.php'];
require __DIR__ . '/../includes/page-start.php';
?>
<section class="admin-panel"><div class="admin-toolbar"><label class="admin-search"><span>Rechercher</span><input type="search" placeholder="Nom du service…" data-admin-search="services-list"></label><p><strong data-result-count><?= count($services) ?></strong> service(s)</p></div>
<?php if ($services === []): ?><div class="admin-empty"><h2>Aucun service</h2><p>Ajoutez la première prestation.</p></div><?php else: ?><div class="admin-list admin-list--compact" id="services-list"><?php foreach ($services as $service): ?><?php $id = (int) $service['id']; $price = $service['price'] ?? $service['base_price'] ?? null; ?><article class="admin-list-card" data-admin-row data-search-value="<?= $escape(strtolower((string) ($service['name'] ?? ''))) ?>"><div class="admin-list-card__index"><?= str_pad((string) $id, 2, '0', STR_PAD_LEFT) ?></div><div class="admin-list-card__content"><div class="admin-list-card__heading"><div><p><?= $escape((string) ($service['slug'] ?? 'Service')) ?></p><h2><?= $escape((string) ($service['name'] ?? 'Service')) ?></h2></div><span class="admin-badge <?= !empty($service['is_active']) ? 'admin-badge--success' : '' ?>"><?= !empty($service['is_active']) ? 'Actif' : 'Masqué' ?></span></div><dl class="admin-list-card__meta"><div><dt>Prix</dt><dd><?= $price !== null ? number_format((float) $price, 2, ',', ' ') . ' €' : 'Sur demande' ?></dd></div><div><dt>Durée</dt><dd><?= !empty($service['duration_minutes']) ? (int) $service['duration_minutes'] . ' min' : '—' ?></dd></div></dl></div><div class="admin-list-card__actions"><a href="<?= $escape(BASE_URL) ?>/admin/pages/service-form.php?id=<?= $id ?>">Modifier</a><form method="post" data-confirm="Supprimer ce service ?"><input type="hidden" name="csrf_token" value="<?= $escape((string) $_SESSION['admin_csrf']) ?>"><input type="hidden" name="id" value="<?= $id ?>"><button class="admin-danger-button">Supprimer</button></form></div></article><?php endforeach; ?></div><p class="admin-no-result" data-no-result hidden>Aucun service trouvé.</p><?php endif; ?></section>
<?php require __DIR__ . '/../includes/page-end.php'; ?>
