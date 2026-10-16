<head>
    <meta charset="utf-8">

    <title><?= htmlspecialchars($activePage ?? 'RGR France', ENT_QUOTES, 'UTF-8') ?></title>

    <meta
        name="description"
        content="<?= htmlspecialchars(
            $metaDescription ?? "Centre d'expertise dédié aux véhicules Ford et Lincoln.",
            ENT_QUOTES,
            'UTF-8'
        ) ?>"
    >

    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Styles communs à toutes les pages -->
    <link rel="stylesheet" href="<?= htmlspecialchars(BASE_URL . '/assets/css/style.css', ENT_QUOTES, 'UTF-8') ?>">

    <!-- Styles propres à la page actuelle -->
    <?php if (!empty($pageCss)): ?>
        <link
            rel="stylesheet"
            href="<?= htmlspecialchars($pageCss, ENT_QUOTES, 'UTF-8') ?>"
        >
    <?php endif; ?>
    
</head>