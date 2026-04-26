<?php
$pageTitle = "GameStore Academy";
$cssPath = "../CSS/registro.css";
include __DIR__ . '/../include/header.php';
?>

<section class="auth-section">
    <div class="auth-card">
        <h2 class="auth-title">Crear cuenta</h2>
        <form action="procesar_registro.php" method="post" class="auth-form">
            <div class="auth-form-group">
                <label for="nombre" class="auth-form-label">Nombre completo</label>
                <input
                    type="text"
                    id="nombre"
                    name="nombre"
                    class="auth-form-input"
                    placeholder="Escribe tu nombre completo"
                    required
                >
            </div>

            <div class="auth-form-group">
                <label for="usuario" class="auth-form-label">Nombre de usuario</label>
                <input
                    type="text"
                    id="usuario"
                    name="usuario"
                    class="auth-form-input"
                    placeholder="Elige un nombre de usuario"
                    minlength="4"
                    maxlength="20"
                    required
                >
            </div>

            <div class="auth-form-group">
                <label for="correo" class="auth-form-label">Correo electrónico</label>
                <input
                    type="email"
                    id="correo"
                    name="correo"
                    class="auth-form-input"
                    placeholder="tucorreo@ejemplo.com"
                    required
                >
            </div>

            <div class="auth-form-group">
                <label for="password" class="auth-form-label">Contraseña</label>
                <input
                    type="password"
                    id="password"
                    name="password"
                    class="auth-form-input"
                    placeholder="Crea una contraseña segura"
                    minlength="8"
                    required
                >
            </div>

            <div class="auth-form-group">
                <label for="confirm_password" class="auth-form-label">Confirmar contraseña</label>
                <input
                    type="password"
                    id="confirm_password"
                    name="confirm_password"
                    class="auth-form-input"
                    placeholder="Repite tu contraseña"
                    minlength="8"
                    required
                >
            </div>

            <div class="auth-form-group auth-form-group-checkbox">
                <label class="auth-form-check">
                    <input type="checkbox" name="terminos" required>
                    <span>Acepto los términos y condiciones</span>
                </label>
            </div>

            <button type="submit" class="auth-form-button">Crear mi cuenta</button>
        </form>
    </div>
</section>

<?php include __DIR__ . '/../include/footer.php'; ?>