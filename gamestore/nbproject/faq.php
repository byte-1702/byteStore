<?php
$pageTitle = "GameStore Academy";
$cssPath = "../CSS/index.css";
include __DIR__ . '/../include/header.php';
?>

<section class="home-hero">
    <div class="home-hero__content">
        <p class="home-hero__kicker">Soporte</p>
        <h2 class="home-hero__title">Preguntas Frecuentes</h2>
        <p class="home-hero__subtitle">
            Respuestas base para dudas comunes de clientes sobre cuentas, compras y seguridad.
        </p>
    </div>
</section>

<section class="home-section">
    <div class="home-container">
        <h3 class="home-section__title">FAQ inicial</h3>
        <div class="home-grid">
            <article class="home-card">
                <h4 class="home-card__title">¿Necesito cuenta para comprar?</h4>
                <p class="home-card__text">Si. Debes estar registrado e iniciar sesion para finalizar una compra.</p>
            </article>
            <article class="home-card">
                <h4 class="home-card__title">¿Que metodos de pago habra?</h4>
                <p class="home-card__text">Puedes integrar pago con tarjeta, transferencias o pasarelas externas.</p>
            </article>
            <article class="home-card">
                <h4 class="home-card__title">¿Puedo recuperar mi contrasena?</h4>
                <p class="home-card__text">Si. Agrega una vista de recuperacion por correo en siguientes iteraciones.</p>
            </article>
        </div>
    </div>
</section>

<?php include __DIR__ . '/../include/footer.php'; ?>
