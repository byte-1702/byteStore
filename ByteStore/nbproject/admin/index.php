<?php
declare(strict_types=1);

session_start();

if (!isset($_SESSION['user']) || ($_SESSION['user']['role'] ?? '') !== 'admin') {
    header('Location: ../login.php');
    exit;
}

$pageTitle = 'Administracion - ByteStore';
$cssPath = '../../CSS/admin.css';
include __DIR__ . '/../../include/header.php';
?>

<section class="home-hero">
    <div class="home-hero__content">
        <p class="home-hero__kicker">Panel</p>
        <h2 class="home-hero__title">Administracion</h2>
        <p class="home-hero__subtitle">
            Gestion de ejemplo: inventario en archivo JSON compartido con la tienda publica.
        </p>
        <div class="home-hero__actions">
            <a class="home-btn home-btn--primary" href="inventario.php">Ir a inventario</a>
            <a class="home-btn home-btn--ghost" href="../productos.php">Ver tienda</a>
        </div>
    </div>
</section>

<?php include __DIR__ . '/../../include/footer.php'; ?>
