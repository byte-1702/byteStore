<?php
declare(strict_types=1);

session_start();

$pageTitle = "Crear Cuenta - ByteStore";
$cssPath   = "../CSS/registro.css";
include __DIR__ . '/../include/header.php';

// Si ya está logueado, redirigir
if (isset($_SESSION['user'])) {
    header('Location: index.php');
    exit;
}

$error = $_SESSION['registro_error'] ?? null;
unset($_SESSION['registro_error']);
?>

<section class="auth-section">
    <div class="auth-card">
        <h2 class="auth-title">Crear cuenta</h2>
        <p class="auth-subtitle">Únete a ByteStore Academy</p>

        <?php if ($error): ?>
            <div class="auth-alert" role="alert">
                <?php echo gs_icon('circle-exclamation'); ?>
                <?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?>
            </div>
        <?php endif; ?>

        <form action="procesar_registro.php" method="post" class="auth-form" id="registroForm" novalidate>
            <!-- Token CSRF -->
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8'); ?>">

            <div class="auth-form-group">
                <label for="nombre" class="auth-form-label">
                    <?php echo gs_icon('id-card'); ?> Nombre completo
                </label>
                <input type="text" id="nombre" name="nombre" class="auth-form-input"
                    placeholder="Ej: Juan Carlos Marquez Ovalle" required>
            </div>

            <div class="auth-form-group">
                <label for="usuario" class="auth-form-label">
                    <?php echo gs_icon('at'); ?> Nombre de usuario
                </label>
                <input type="text" id="usuario" name="usuario" class="auth-form-input"
                    placeholder="Ej: juangamer99" minlength="4" maxlength="20" required>
            </div>

            <div class="auth-form-group">
                <label for="correo" class="auth-form-label">
                    <?php echo gs_icon('envelope'); ?> Correo electrónico
                </label>
                <input type="email" id="correo" name="correo" class="auth-form-input"
                    placeholder="tucorreo@ejemplo.com" required>
            </div>

            <div class="auth-form-group">
                <label for="password" class="auth-form-label">
                    <?php echo gs_icon('lock'); ?> Contraseña
                </label>
                <input type="password" id="password" name="password" class="auth-form-input"
                    placeholder="Mínimo 8 caracteres" minlength="8" required
                    autocomplete="new-password">
            </div>

            <div class="auth-form-group">
                <label for="confirm_password" class="auth-form-label">
                    <?php echo gs_icon('lock'); ?> Confirmar contraseña
                </label>
                <input type="password" id="confirm_password" name="confirm_password"
                    class="auth-form-input" placeholder="Repite tu contraseña"
                    minlength="8" required autocomplete="new-password">
            </div>

            <div class="auth-form-group auth-form-group-checkbox">
                <label class="auth-form-check">
                    <input type="checkbox" name="terminos" required>
                    <span>Acepto los <a href="terms_legales.php" target="_blank">términos y condiciones</a></span>
                </label>
            </div>

            <button type="submit" class="auth-form-button" id="submitBtn">
                <?php echo gs_icon('user-plus'); ?> Crear mi cuenta
            </button>
        </form>

        <p class="auth-footer-link">
            ¿Ya tienes cuenta? <a href="login.php">Inicia sesión</a>
        </p>
    </div>
</section>

<?php include __DIR__ . '/../include/footer.php'; ?>
