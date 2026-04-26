<?php
$pageTitle = "GameStore Academy";
$cssPath = "../CSS/index.css";
include __DIR__ . '/../include/header.php';
?>

<section class="home-hero">
    <div class="home-hero__content">
        <p class="home-hero__kicker">PHP • E-commerce • Videojuegos</p>
        <h2 class="home-hero__title">Plataforma de venta de videojuegos</h2>
        <p class="home-hero__subtitle">
            Bienvenido al punto de inicio del proyecto. Desde aqui construiremos una experiencia tipo Steam con catalogo, carrito, cuenta de usuario y secciones legales completas.
        </p>

        <div class="home-hero__actions">
            <a class="home-btn home-btn--primary" href="productos.php">Ver productos</a>
            <a class="home-btn home-btn--ghost" href="registro.php">Crear cuenta</a>
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
                <a class="home-link" href="login.php">Ir a Entra</a>
            </article>

            <article class="home-card">
                <h4 class="home-card__title">Catalogo y carrito</h4>
                <p class="home-card__text">Listado de videojuegos, detalle de producto y flujo de compra en carrito.</p>
                <a class="home-link" href="productos.php">Ir a Productos</a>
            </article>

            <article class="home-card">
                <h4 class="home-card__title">Soporte y legal</h4>
                <p class="home-card__text">Terminos, privacidad, manual, preguntas frecuentes y seccion sobre la plataforma.</p>
                <a class="home-link" href="faq.php">Ir a FAQ</a>
            </article>
        </div>
    </div>
</section>

<?php include __DIR__ . '/../include/footer.php'; ?>