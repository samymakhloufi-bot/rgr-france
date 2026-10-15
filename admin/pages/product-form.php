<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/bootstrap.php';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT) ?: null;
$product = $id ? $adminRepository->findById('products', $id) : null;
if ($id !== null && $product === null) { http_response_code(404); exit('Produit introuvable.'); }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $adminVerifyCsrf($_POST['csrf_token'] ?? null);
    $postedId = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT) ?: null;
    $oldProduct = $postedId ? $adminRepository->findById('products', $postedId) : null;

    try {
        $name = trim((string) ($_POST['name'] ?? ''));
        $slug = trim((string) ($_POST['slug'] ?? ''));
        $regularPrice = $adminNullableFloat($_POST['regular_price'] ?? null);
        if ($name === '' || $slug === '' || $regularPrice === null || $regularPrice < 0) {
            throw new InvalidArgumentException('Le nom, le slug et un prix valide sont obligatoires.');
        }

        $imagePath = isset($oldProduct['image_path']) ? (string) $oldProduct['image_path'] : null;
        $newImagePath = null;
        if (isset($_FILES['image']) && ($_FILES['image']['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE) {
            $newImagePath = $adminStoreImage($_FILES['image'], 'products');
            $imagePath = $newImagePath;
        }

        $savedId = $adminRepository->save('products', [
            'name' => $name,
            'slug' => $slug,
            'sku' => $adminNullableString($_POST['sku'] ?? null),
            'description' => $adminNullableString($_POST['description'] ?? null),
            'regular_price' => $regularPrice,
            'promotional_price' => $adminNullableFloat($_POST['promotional_price'] ?? null),
            'stock_quantity' => max(0, (int) ($_POST['stock_quantity'] ?? 0)),
            'image_path' => $imagePath,
            'display_order' => max(0, (int) ($_POST['display_order'] ?? 0)),
            'is_active' => isset($_POST['is_active']) ? 1 : 0,
        ], $postedId);

        if ($newImagePath !== null && !empty($oldProduct['image_path'])) {
            $adminDeleteImageFile((string) $oldProduct['image_path']);
        }
        $adminFlash('success', $postedId ? 'Produit mis à jour.' : 'Produit ajouté.');
        $adminRedirect('/admin/pages/product-form.php?id=' . $savedId);
    } catch (Throwable $exception) {
        error_log($exception->getMessage());
        if (isset($newImagePath) && $newImagePath !== null) { $adminDeleteImageFile($newImagePath); }
        $adminFlash('error', $exception instanceof InvalidArgumentException ? $exception->getMessage() : 'Impossible d’enregistrer le produit.');
        $adminRedirect('/admin/pages/product-form.php' . ($postedId ? '?id=' . $postedId : ''));
    }
}

$pageTitle = $product ? 'Modifier le produit' : 'Ajouter un produit';
$pageEyebrow = 'Pièces & équipements';
$activeAdminPage = 'products';
require __DIR__ . '/../includes/page-start.php';
?>
<form class="admin-edit-form" method="post" enctype="multipart/form-data">
    <input type="hidden" name="csrf_token" value="<?= $escape((string) $_SESSION['admin_csrf']) ?>"><?php if ($id): ?><input type="hidden" name="id" value="<?= $id ?>"><?php endif; ?>
    <section class="admin-form-section"><div class="admin-form-section__heading"><p>01</p><div><h2>Informations du produit</h2><span>Nom, adresse de la fiche et référence.</span></div></div><div class="admin-form-grid">
        <label><span>Nom *</span><input name="name" required value="<?= $escape((string) ($product['name'] ?? '')) ?>" data-slug-source></label>
        <label><span>Slug *</span><input name="slug" required value="<?= $escape((string) ($product['slug'] ?? '')) ?>" data-slug-target></label>
        <label><span>Référence / SKU</span><input name="sku" value="<?= $escape((string) ($product['sku'] ?? '')) ?>"></label>
        <label><span>Ordre d’affichage</span><input type="number" min="0" name="display_order" value="<?= $escape((string) ($product['display_order'] ?? 0)) ?>"></label>
        <label class="admin-form-field--full"><span>Description</span><textarea name="description" rows="8"><?= $escape((string) ($product['description'] ?? '')) ?></textarea></label>
    </div></section>
    <section class="admin-form-section"><div class="admin-form-section__heading"><p>02</p><div><h2>Prix et disponibilité</h2><span>Le prix promotionnel remplace le prix normal s’il est renseigné.</span></div></div><div class="admin-form-grid">
        <label><span>Prix normal (€) *</span><input type="number" min="0" step="0.01" name="regular_price" required value="<?= $escape((string) ($product['regular_price'] ?? '')) ?>"></label>
        <label><span>Prix promotionnel (€)</span><input type="number" min="0" step="0.01" name="promotional_price" value="<?= $escape((string) ($product['promotional_price'] ?? '')) ?>"></label>
        <label><span>Quantité en stock</span><input type="number" min="0" name="stock_quantity" value="<?= $escape((string) ($product['stock_quantity'] ?? 0)) ?>"></label>
        <label class="admin-switch"><input type="checkbox" name="is_active" value="1" <?= !isset($product['is_active']) || !empty($product['is_active']) ? 'checked' : '' ?>><span>Afficher ce produit sur le site</span></label>
    </div></section>
    <section class="admin-form-section"><div class="admin-form-section__heading"><p>03</p><div><h2>Image</h2><span>Une nouvelle image remplacera l’image actuelle.</span></div></div><?php if (!empty($product['image_path'])): ?><img class="admin-current-image" src="<?= $escape((string) $adminImageUrl((string) $product['image_path'], 'products')) ?>" alt=""><?php endif; ?><label class="admin-upload"><input type="file" name="image" accept="image/jpeg,image/png,image/webp"><span>Choisir une image</span><small>JPG, PNG ou WebP · 8 Mo maximum</small></label></section>
    <div class="admin-form-actions"><a href="<?= $escape(BASE_URL) ?>/admin/pages/products.php">Annuler</a><button>Enregistrer le produit</button></div>
</form>
<?php require __DIR__ . '/../includes/page-end.php'; ?>
