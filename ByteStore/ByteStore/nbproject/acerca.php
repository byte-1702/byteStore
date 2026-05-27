<?php
$pageTitle = "Acerca de Nosotros";
$cssPath = "../CSS/index.css";
include __DIR__ . '/../include/header.php';
?>

<section class="home-hero">
    <div class="home-hero__content">
        <p class="home-hero__kicker">Informacion</p>
        <h2 class="home-hero__title">Acerca de Nosotros</h2>
        <p class="home-hero__subtitle">
        ByteStore es una plataforma de distribución de juegos digital, nos encargamos de brindar a los usuarios
        las ultimas novedades de juegos, actualizaciones y los mejores clasicos, desde juegos de PC, Playstation y XBOX y muchos mas.
        </p>
    </div>
</section>

<section class="home-section">
    <div class="home-container">
        
        <div class="home-grid">
            <article class="home-card">
                <h4 class="home-card__title">Misión</h4>
                <p class="home-card__text">Construir una plataforma tipo Steam, escalable y mantenible.</p>
            </article>
            <article class="home-card">
                <h4 class="home-card__title">Visión</h4>
                <p class="home-card__text">Frontend con HTML/CSS y backend con PHP para autenticar, listar y vender juegos.</p>
            </article>
            <article class="home-card">
                <h4 class="home-card__title">Proximo objetivo</h4>
                <p class="home-card__text">Conectar catalogo y carrito a base de datos para habilitar compras reales.</p>
            </article>
        </div>
    </div>
</section>

<?php include __DIR__ . '/../include/footer.php'; ?>
