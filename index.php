<?php         
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);
    require_once __DIR__ . '/config/database.test.php';
    $activePage = 'Accueil';
?>

<!DOCTYPE html>
<html lang="fr">
    
    <?php include 'includes/head.php';?>
<body>
    <?php include 'includes/header.php'; ?>
    <main>
        <h1> BONJOUR</h1>
    </main>
    <?php include 'includes/footer.php'; ?>
</body>


</html>