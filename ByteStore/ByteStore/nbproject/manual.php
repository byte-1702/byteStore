<?php
$pageTitle = "Manual de Usuario";
$cssPath = "../CSS/index.css";
include __DIR__ . '/../include/header.php';
?>

<section class="home-hero">
    <div class="home-hero__content">
        <p class="home-hero__kicker">Ayuda</p>
        <h2 class="home-hero__title">Manual de Usuario</h2>
        <p class="home-hero__subtitle">
            Guia rapida para navegar por la plataforma: crear cuenta, entrar, ver productos y usar el carrito.
        </p>
    </div>
</section>

<section class="home-section">
    <div class="home-container">
        <h3 class="home-section__title">Pasos recomendados</h3>
        <div class="home-grid">
            <article class="home-card">
                <h4 class="home-card__title">1. Registrate</h4>
                <p class="home-card__text">Crea tu cuenta con datos validos desde la opcion Registrate.</p>
            </article>
            <article class="home-card">
                <h4 class="home-card__title">2. Entra</h4>
                <p class="home-card__text">Accede con usuario/correo y contrasena para iniciar sesion.</p>
            </article>
            <article class="home-card">
                <h4 class="home-card__title">3. Compra</h4>
                <p class="home-card__text">Explora Productos, agrega juegos al Carrito y continua al pago.</p>
            </article>
        </div>
    </div>
</section>

<?php include __DIR__ . '/../include/footer.php'; ?>
