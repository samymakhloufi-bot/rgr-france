<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $adminVerifyCsrf($_POST['csrf_token'] ?? null);
    $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
    try {
        if (!$id || !$adminRepository->delete('contact_requests', $id)) { throw new RuntimeException('Demande introuvable.'); }
        $adminFlash('success', 'Demande supprimée.');
    } catch (Throwable $exception) { error_log($exception->getMessage()); $adminFlash('error', 'Impossible de supprimer cette demande.'); }
    $adminRedirect('/admin/pages/contacts.php');
}

$contacts = $adminRepository->findAll('contact_requests');
$formatDate = static function (?string $date): string { try { return $date ? (new DateTimeImmutable($date))->format('d/m/Y à H:i') : '—'; } catch (Throwable) { return (string) $date; } };
$statusLabel = static fn (?string $status): string => match (strtoupper((string) $status)) { 'IN_PROGRESS' => 'En cours', 'CLOSED' => 'Traitée', 'ARCHIVED' => 'Archivée', default => 'Nouvelle' };
$statusClass = static fn (?string $status): string => match (strtoupper((string) $status)) { 'CLOSED' => 'admin-badge--success', 'ARCHIVED' => 'admin-badge--muted', 'IN_PROGRESS' => 'admin-badge--warning', default => 'admin-badge--alert' };
$pageTitle = 'Demandes de contact'; $pageEyebrow = 'Relation client'; $activeAdminPage = 'contacts';
require __DIR__ . '/../includes/page-start.php';
?>

<section class="admin-panel">
    <div class="admin-toolbar">
        <label class="admin-search">
            <span>Rechercher</span>
            <input type="search" placeholder="Nom, e-mail, téléphone…" data-admin-search="contacts-list">
        </label>
        <label class="admin-filter">
            <span>Statut</span>
            <select data-admin-status-filter="contacts-list">
                <option value="">Tous</option>
                <option value="new">Nouvelles</option>
                <option value="in_progress">En cours</option>
                <option value="CLOSED">Traitées</option>
                <option value="archived">Archivées</option>
            </select>
        </label>
        <p><strong data-result-count><?= count($contacts) ?></strong> demande(s)</p>
    </div>
<?php if ($contacts === []): ?>
    <div class="admin-empty">
        <h2>Aucune demande</h2>
        <p>Les messages envoyés depuis le formulaire apparaîtront ici.</p>
    </div>
<?php else: ?>
    <div class="admin-contact-list" id="contacts-list">
        <?php foreach ($contacts as $contact): ?>
            <?php $id = (int) $contact['id']; $fullName = trim((string) ($contact['first_name'] ?? '') . ' ' . (string) ($contact['last_name'] ?? '')); $status = strtolower((string) ($contact['status'] ?? 'new')); $messageExcerpt = (string) ($contact['message'] ?? ''); if (strlen($messageExcerpt) > 150) { $messageExcerpt = substr($messageExcerpt, 0, 147) . '…'; } ?>
            <article class="admin-contact-row" data-admin-row data-status="<?= $escape($status) ?>" data-search-value="<?= $escape(strtolower($fullName . ' ' . ($contact['email'] ?? '') . ' ' . ($contact['phone'] ?? '') . ' ' . ($contact['message'] ?? ''))) ?>">
                <div class="admin-contact-row__identity">
                    <span><?= $escape(strtoupper(substr($fullName !== '' ? $fullName : 'C', 0, 1))) ?></span>
                    <div>
                        <h2><?= $escape($fullName !== '' ? $fullName : 'Contact') ?></h2>
                        <a href="mailto:<?= $escape((string) ($contact['email'] ?? '')) ?>"><?= $escape((string) ($contact['email'] ?? 'Adresse inconnue')) ?></a>
                    </div>
                </div>
                <p class="admin-contact-row__message"><?= $escape($messageExcerpt) ?></p>
                <time><?= $escape($formatDate(isset($contact['created_at']) ? (string) $contact['created_at'] : null)) ?></time>
                <span class="admin-badge <?= $statusClass($contact['status'] ?? null) ?>"><?= $statusLabel($contact['status'] ?? null) ?></span>
                <div class="admin-contact-row__actions">
                    <a href="<?= $escape(BASE_URL) ?>/admin/pages/contact.php?id=<?= $id ?>">Ouvrir</a>
                    <form method="post" data-confirm="Supprimer définitivement cette demande ?">
                        <input type="hidden" name="csrf_token" value="<?= $escape((string) $_SESSION['admin_csrf']) ?>">
                        <input type="hidden" name="id" value="<?= $id ?>">
                        <button class="admin-danger-button">Supprimer</button>
                    </form>
                </div>
            </article>
        <?php endforeach; ?>
    </div>
    <p class="admin-no-result" data-no-result hidden>Aucune demande ne correspond aux filtres.</p>
<?php endif; ?>
</section>
<?php require __DIR__ . '/../includes/page-end.php'; ?>
