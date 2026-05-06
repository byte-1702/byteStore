<?php
declare(strict_types=1);

$pageTitle = "Iniciar Sesión - ByteStore";
$cssPath   = "../CSS/glob_login.css";
include __DIR__ . '/../include/header.php';

// Si ya está logueado, redirigir al inicio
if (isset($_SESSION['user'])) {
    header('Location: index.php');
    exit;
}

$error   = $_SESSION['login_error'] ?? null;
$success = $_SESSION['registro_success'] ?? null;
unset($_SESSION['login_error'], $_SESSION['registro_success']);
?>

<section class="login-section">
    <div class="login-card">
        <h2 class="login-title">Bienvenido de nuevo</h2>
        <p class="login-subtitle">Ingresa tus datos para continuar</p>

        <?php if ($error): ?>
            <div class="auth-alert" role="alert">
                <?php echo gs_icon('circle-exclamation'); ?>
                <?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?>
            </div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="auth-alert auth-alert--success" role="status">
                <?php echo gs_icon('circle-check'); ?>
                <?php echo htmlspecialchars($success, ENT_QUOTES, 'UTF-8'); ?>
            </div>
        <?php endif; ?>

        <form action="authenticate.php" method="post">
            <!-- Token CSRF -->
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8'); ?>">

            <div class="auth-form-group">
                <label for="identifier" class="auth-form-label">
                    <?php echo gs_icon('user'); ?> Usuario o correo electrónico
                </label>
                <input
                    type="text"
                    id="identifier"
                    name="identifier"
                    class="auth-form-input"
                    placeholder="Ej: usuario123 o correo@ejemplo.com"
                    autocomplete="username"
                    required
                >
            </div>

            <div class="auth-form-group">
                <label for="password" class="auth-form-label">
                    <?php echo gs_icon('lock'); ?> Contraseña
                </label>
                <input
                    type="password"
                    id="password"
                    name="password"
                    class="auth-form-input"
                    placeholder="Tu contraseña"
                    autocomplete="current-password"
                    required
                >
            </div>

            <button type="submit" class="auth-form-button">
                <?php echo gs_icon('right-to-bracket'); ?> Entrar a mi cuenta
            </button>
        </form>

        <p class="auth-footer-link">
            ¿No tienes cuenta? <a href="registro.php">Regístrate gratis</a>
        </p>

        <p class="auth-footer-link" style="margin-top:8px; font-size:0.8rem; color:#555;">
            <?php echo gs_icon('circle-info'); ?>
            Demo: usuario <strong style="color:#9bb1ff;">demo</strong> / contraseña <strong style="color:#9bb1ff;">demo1234</strong>
        </p>
    </div>
</section>

<?php include __DIR__ . '/../include/footer.php'; ?>
