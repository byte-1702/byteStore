<?php
session_start();

$pageTitle = "ByteStore Academy";
$cssPath   = "../CSS/index.css";
include __DIR__ . '/../include/header.php';

require_once(__DIR__ . '/../config/database.php');
require_once(__DIR__ . '/../models/producto.php');

if (!isset($pdo)) {
    die("Error crítico: No se pudo conectar a la base de datos");
}

try {
    $productoModel = new Producto($pdo);
    $productos = $productoModel->todos();
} catch (Exception $e) {
    $productos = [];
    $error = $e->getMessage();
}
?>

<section class="home-hero">
    <div class="home-hero__content">
        <p class="home-hero__kicker">PHP &bull; E-commerce &bull; Videojuegos</p>

        <?php if (isset($_SESSION['user'])): ?>
            <h2 class="home-hero__title">
                ¡Hola, <?php echo htmlspecialchars($_SESSION['user'] ['nombre'], ENT_QUOTES, 'UTF-8'); ?>! 👋
            </h2>
            <p class="home-hero__subtitle">
                Bienvenido de vuelta a ByteStore Academy. Explora el catálogo, gestiona tu carrito y disfruta de la experiencia.
            </p>
        <?php else: ?>
            <h2 class="home-hero__title">Plataforma de venta de videojuegos</h2>
            <p class="home-hero__subtitle">
                Bienvenido al punto de inicio del proyecto. Desde aquí construiremos una experiencia tipo Steam con catálogo, carrito, cuenta de usuario y secciones legales completas.
            </p>
        <?php endif; ?>

        <div class="home-hero__actions">
            <a class="home-btn home-btn--primary" href="producto.php">
                <?php echo gs_icon('gamepad'); ?>&nbsp; Ver productos
            </a>
            <?php if (!isset($_SESSION['user'])): ?>
                <a class="home-btn home-btn--ghost" href="registro.php">
                    <?php echo gs_icon('user-plus'); ?>&nbsp; Crear cuenta
                </a>
            <?php else: ?>
                <a class="home-btn home-btn--ghost" href="carrito.php">
                    <?php echo gs_icon('cart-shopping'); ?>&nbsp; Ver carrito
                </a>
            <?php endif; ?>
        </div>
    </div>
</section>

<section class="home-section">
    <div class="home-container">
        <h3 class="home-section__title">Ruta de trabajo del proyecto</h3>
        <div class="home-grid">
            <article class="home-card">
                <h4 class="home-card__title">Acceso y cuentas</h4>
                <p class="home-card__text">Registro, login y validaciones de seguridad para crear perfiles de cliente.</p>
                <a class="home-link" href="login.php">
                    <?php echo gs_icon('right-to-bracket'); ?> Ir a Entrar
                </a>
            </article>

            <article class="home-card">
                <h4 class="home-card__title">Catálogo y carrito</h4>
                <p class="home-card__text">Listado de videojuegos, filtros por género, plataforma y precio, y flujo de compra en carrito.</p>
                <a class="home-link" href="productos.php">
                    <?php echo gs_icon('gamepad'); ?> Ir a Productos
                </a>
            </article>

            <article class="home-card">
                <h4 class="home-card__title">Soporte y legal</h4>
                <p class="home-card__text">Términos, privacidad, manual, preguntas frecuentes y sección sobre la plataforma.</p>
                <a class="home-link" href="faq.php">
                    <?php echo gs_icon('circle-question'); ?> Ir a FAQ
                </a>
            </article>
        </div>
    </div>
</section>

<?php include __DIR__ . '/../include/footer.php'; ?>