<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $adminVerifyCsrf($_POST['csrf_token'] ?? null);
    try {
        foreach ((array) ($_POST['hours'] ?? []) as $id => $hour) {
            $id = filter_var($id, FILTER_VALIDATE_INT);
            if (!$id || !is_array($hour)) { continue; }
            $closed = isset($hour['is_closed']);
            $adminRepository->save('opening_hours', [
                'morning_open' => $closed ? null : $adminNullableString($hour['morning_open'] ?? null),
                'morning_close' => $closed ? null : $adminNullableString($hour['morning_close'] ?? null),
                'afternoon_open' => $closed ? null : $adminNullableString($hour['afternoon_open'] ?? null),
                'afternoon_close' => $closed ? null : $adminNullableString($hour['afternoon_close'] ?? null),
                'is_closed' => $closed ? 1 : 0,
            ], $id);
        }
        $adminFlash('success', 'Horaires mis à jour.');
    } catch (Throwable $exception) { error_log($exception->getMessage()); $adminFlash('error', 'Impossible de mettre à jour les horaires.'); }
    $adminRedirect('/admin/pages/opening-hours.php');
}

$hours = $adminRepository->findOpeningHours();
$pageTitle = 'Horaires d’ouverture'; $pageEyebrow = 'Informations du garage'; $activeAdminPage = 'hours';
require __DIR__ . '/../includes/page-start.php';
?>
<form class="admin-edit-form" method="post"><input type="hidden" name="csrf_token" value="<?= $escape((string) $_SESSION['admin_csrf']) ?>"><section class="admin-form-section"><div class="admin-form-section__heading"><p>01</p><div><h2>Semaine du garage</h2><span>Ces horaires sont utilisés dans le footer et la page contact.</span></div></div><?php if ($hours === []): ?><div class="admin-empty"><h2>Aucun horaire enregistré</h2><p>Ajoutez d’abord les sept jours dans la table <code>opening_hours</code>.</p></div><?php else: ?><div class="admin-hours-list"><?php foreach ($hours as $hour): ?><?php $id = (int) $hour['id']; ?><fieldset class="admin-hour-row" data-hour-row><legend><?= $escape((string) ($hour['day_name'] ?? 'Jour')) ?></legend><label><span>Ouverture matin</span><input type="time" name="hours[<?= $id ?>][morning_open]" value="<?= $escape(substr((string) ($hour['morning_open'] ?? ''), 0, 5)) ?>"></label><label><span>Fermeture matin</span><input type="time" name="hours[<?= $id ?>][morning_close]" value="<?= $escape(substr((string) ($hour['morning_close'] ?? ''), 0, 5)) ?>"></label><label><span>Ouverture après-midi</span><input type="time" name="hours[<?= $id ?>][afternoon_open]" value="<?= $escape(substr((string) ($hour['afternoon_open'] ?? ''), 0, 5)) ?>"></label><label><span>Fermeture après-midi</span><input type="time" name="hours[<?= $id ?>][afternoon_close]" value="<?= $escape(substr((string) ($hour['afternoon_close'] ?? ''), 0, 5)) ?>"></label><label class="admin-switch"><input type="checkbox" name="hours[<?= $id ?>][is_closed]" value="1" data-hour-closed <?= !empty($hour['is_closed']) ? 'checked' : '' ?>><span>Fermé</span></label></fieldset><?php endforeach; ?></div><?php endif; ?></section><?php if ($hours !== []): ?><div class="admin-form-actions"><a href="<?= $escape(BASE_URL) ?>/admin/admin-index.php">Annuler</a><button>Enregistrer les horaires</button></div><?php endif; ?></form>
<?php require __DIR__ . '/../includes/page-end.php'; ?>
