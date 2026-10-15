<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $adminVerifyCsrf($_POST['csrf_token'] ?? null);
    $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
    $product = $id ? $adminRepository->findById('products', $id) : null;

    try {
        if ($product === null || !$adminRepository->delete('products', (int) $id)) {
            throw new RuntimeException('Produit introuvable.');
        }
        $adminDeleteImageFile(isset($product['image_path']) ? (string) $product['image_path'] : null);
        $adminFlash('success', 'Produit supprimé.');
    } catch (Throwable $exception) {
        error_log($exception->getMessage());
        $adminFlash('error', 'Impossible de supprimer ce produit.');
    }
    $adminRedirect('/admin/pages/products.php');
}

$products = $adminRepository->findAll('products');
$pageTitle = 'Produits';
$pageEyebrow = 'Pièces & équipements';
$activeAdminPage = 'products';
$pageAction = ['label' => 'Ajouter un produit', 'href' => '/admin/pages/product-form.php'];
require __DIR__ . '/../includes/page-start.php';
?>
<section class="admin-panel">
    <div class="admin-toolbar"><label class="admin-search"><span>Rechercher</span><input type="search" placeholder="Nom, référence…" data-admin-search="products-list"></label><p><strong data-result-count><?= count($products) ?></strong> produit(s)</p></div>
    <?php if ($products === []): ?><div class="admin-empty"><h2>Aucun produit</h2><p>Ajoutez votre premier produit.</p></div><?php else: ?>
    <div class="admin-list" id="products-list">
        <?php foreach ($products as $product): ?>
            <?php $id = (int) $product['id']; $imageUrl = $adminImageUrl(isset($product['image_path']) ? (string) $product['image_path'] : null, 'products'); ?>
            <article class="admin-list-card" data-admin-row data-search-value="<?= $escape(strtolower((string) ($product['name'] ?? '') . ' ' . (string) ($product['sku'] ?? ''))) ?>">
                <div class="admin-list-card__media"><?php if ($imageUrl): ?><img src="<?= $escape($imageUrl) ?>" alt=""><?php else: ?><span>RGR</span><?php endif; ?></div>
                <div class="admin-list-card__content"><div class="admin-list-card__heading"><div><p><?= $escape((string) ($product['sku'] ?? 'Sans référence')) ?></p><h2><?= $escape((string) ($product['name'] ?? 'Produit')) ?></h2></div><span class="admin-badge <?= !empty($product['is_active']) ? 'admin-badge--success' : '' ?>"><?= !empty($product['is_active']) ? 'Actif' : 'Masqué' ?></span></div><dl class="admin-list-card__meta"><div><dt>Prix</dt><dd><?= isset($product['regular_price']) ? number_format((float) ($product['promotional_price'] ?? $product['regular_price']), 2, ',', ' ') . ' €' : 'Sur demande' ?></dd></div><div><dt>Stock</dt><dd><?= $escape((string) ($product['stock_quantity'] ?? '—')) ?></dd></div><div><dt>Ordre</dt><dd><?= $escape((string) ($product['display_order'] ?? '—')) ?></dd></div></dl></div>
                <div class="admin-list-card__actions"><a href="<?= $escape(BASE_URL) ?>/admin/pages/product-form.php?id=<?= $id ?>">Modifier</a><form method="post" data-confirm="Supprimer définitivement ce produit ?"><input type="hidden" name="csrf_token" value="<?= $escape((string) $_SESSION['admin_csrf']) ?>"><input type="hidden" name="id" value="<?= $id ?>"><button class="admin-danger-button">Supprimer</button></form></div>
            </article>
        <?php endforeach; ?>
    </div><p class="admin-no-result" data-no-result hidden>Aucun produit trouvé.</p>
    <?php endif; ?>
</section>
<?php require __DIR__ . '/../includes/page-end.php'; ?>
