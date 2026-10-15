<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/bootstrap.php';
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$contact = $id ? $adminRepository->findById('contact_requests', $id) : null;
if ($contact === null) { http_response_code(404); exit('Demande introuvable.'); }

$allowedStatuses = ['NEW', 'IN_PROGRESS', 'PROCESSED', 'ARCHIVED'];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $adminVerifyCsrf($_POST['csrf_token'] ?? null);
    $status = strtoupper((string) ($_POST['status'] ?? ''));
    if (!in_array($status, $allowedStatuses, true)) { $adminFlash('error', 'Statut invalide.'); } else { try { $adminRepository->updateContactStatus((int) $id, $status); $adminFlash('success', 'Statut mis à jour.'); } catch (Throwable $exception) { error_log($exception->getMessage()); $adminFlash('error', 'Impossible de modifier le statut.'); } }
    $adminRedirect('/admin/pages/contact.php?id=' . (int) $id);
}

$formatDate = static function (?string $date): string { try { return $date ? (new DateTimeImmutable($date))->format('d/m/Y à H:i') : '—'; } catch (Throwable) { return (string) $date; } };
$fullName = trim((string) ($contact['first_name'] ?? '') . ' ' . (string) ($contact['last_name'] ?? ''));
$address = array_filter([$contact['postal_address'] ?? null, trim((string) ($contact['postal_code'] ?? '') . ' ' . (string) ($contact['city'] ?? ''))]);
$pageTitle = $fullName !== '' ? $fullName : 'Demande de contact'; $pageEyebrow = 'Demande reçue'; $activeAdminPage = 'contacts';
require __DIR__ . '/../includes/page-start.php';
?>
<div class="admin-detail-grid"><section class="admin-form-section admin-contact-message"><div class="admin-form-section__heading"><p>01</p><div><h2>Message</h2><span>Reçu le <?= $escape($formatDate(isset($contact['created_at']) ? (string) $contact['created_at'] : null)) ?></span></div></div><div class="admin-contact-message__body"><?= nl2br($escape((string) ($contact['message'] ?? 'Aucun message.'))) ?></div></section><aside class="admin-form-section"><div class="admin-form-section__heading"><p>02</p><div><h2>Coordonnées</h2><span>Informations transmises par le contact.</span></div></div><dl class="admin-contact-details"><div><dt>E-mail</dt><dd><a href="mailto:<?= $escape((string) ($contact['email'] ?? '')) ?>"><?= $escape((string) ($contact['email'] ?? '—')) ?></a></dd></div><div><dt>Téléphone</dt><dd><a href="tel:<?= $escape((string) ($contact['phone'] ?? '')) ?>"><?= $escape((string) ($contact['phone'] ?? '—')) ?></a></dd></div><div><dt>Adresse</dt><dd><?= $address ? implode('<br>', array_map($escape, $address)) : '—' ?></dd></div></dl><form class="admin-status-form" method="post"><input type="hidden" name="csrf_token" value="<?= $escape((string) $_SESSION['admin_csrf']) ?>"><label><span>Suivi de la demande</span><select name="status"><option value="NEW" <?= strtoupper((string) ($contact['status'] ?? 'NEW')) === 'NEW' ? 'selected' : '' ?>>Nouvelle</option><option value="IN_PROGRESS" <?= strtoupper((string) ($contact['status'] ?? '')) === 'IN_PROGRESS' ? 'selected' : '' ?>>En cours</option><option value="PROCESSED" <?= strtoupper((string) ($contact['status'] ?? '')) === 'PROCESSED' ? 'selected' : '' ?>>Traitée</option><option value="ARCHIVED" <?= strtoupper((string) ($contact['status'] ?? '')) === 'ARCHIVED' ? 'selected' : '' ?>>Archivée</option></select></label><button>Mettre à jour</button></form></aside></div><div class="admin-form-actions"><a href="<?= $escape(BASE_URL) ?>/admin/pages/contacts.php">← Retour aux demandes</a><a class="admin-primary-link" href="mailto:<?= $escape((string) ($contact['email'] ?? '')) ?>">Répondre par e-mail</a></div>
<?php require __DIR__ . '/../includes/page-end.php'; ?>
