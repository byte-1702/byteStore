<?php
declare(strict_types=1);

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
                    <?php echo gs_icon('id-card'); ?> Primer Nombre
                </label>
                <input type="text" id="primer-nombre" name="primer-nombre" class="auth-form-input"
                    placeholder="Ej: Juan" required>
            </div>

            <div class="auth-form-group">
                <label for="nombre" class="auth-form-label">
                    <?php echo gs_icon('id-card'); ?> Segundo Nombre
                </label>
                <input type="text" id="Segundo-nombre" name="Segundo-nombre" class="auth-form-input"
                    placeholder="Ej: Carlos" required>
            </div>

            <div class="auth-form-group">
                <label for="nombre" class="auth-form-label">
                    <?php echo gs_icon('id-card'); ?> Primer Apellido
                </label>
                <input type="text" id="Primer-apellido" name="Primer-apellido" class="auth-form-input"
                    placeholder="Ej: Marquez" required>
            </div>

            <div class="auth-form-group">
                <label for="nombre" class="auth-form-label">
                    <?php echo gs_icon('id-card'); ?> Segundo Apellido
                </label>
                <input type="text" id="Segundo-apellido" name="Segundo-apellido" class="auth-form-input"
                    placeholder="Ej: Ovalle" required>
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
                <p class="password-hint" id="passwordHint"></p>
            </div>

            <div class="auth-form-group">
                <label for="confirm_password" class="auth-form-label">
                    <?php echo gs_icon('lock'); ?> Confirmar contraseña
                </label>
                <input type="password" id="confirm_password" name="confirm_password"
                    class="auth-form-input" placeholder="Repite tu contraseña"
                    minlength="8" required autocomplete="new-password">
                <p class="confirm-hint" id="confirmHint"></p>
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

<script>
// ── Validación en tiempo real ──────────────────────────────────
const passwordInput  = document.getElementById('password');
const confirmInput   = document.getElementById('confirm_password');
const passwordHint   = document.getElementById('passwordHint');
const confirmHint    = document.getElementById('confirmHint');
const submitBtn      = document.getElementById('submitBtn');

function evaluatePasswordStrength(pwd) {
    if (pwd.length === 0) return { level: '', text: '' };
    let score = 0;
    if (pwd.length >= 8)  score++;
    if (pwd.length >= 12) score++;
    if (/[A-Z]/.test(pwd)) score++;
    if (/[0-9]/.test(pwd)) score++;
    if (/[^A-Za-z0-9]/.test(pwd)) score++;

    if (score <= 2) return { level: 'weak',   text: '⚠ Contraseña débil' };
    if (score <= 3) return { level: 'medium',  text: '▲ Contraseña moderada' };
    return              { level: 'strong',  text: '✔ Contraseña fuerte' };
}

function checkPasswords() {
    const pwd     = passwordInput.value;
    const confirm = confirmInput.value;

    // Fuerza
    const strength = evaluatePasswordStrength(pwd);
    passwordHint.textContent  = strength.text;
    passwordHint.className    = 'password-hint ' + strength.level;

    // Coincidencia
    if (confirm.length === 0) {
        confirmHint.textContent = '';
        confirmHint.className   = 'confirm-hint';
        return;
    }
    if (pwd === confirm) {
        confirmHint.textContent = '✔ Las contraseñas coinciden';
        confirmHint.className   = 'confirm-hint match';
    } else {
        confirmHint.textContent = '✖ Las contraseñas no coinciden';
        confirmHint.className   = 'confirm-hint no-match';
    }
}

passwordInput.addEventListener('input', checkPasswords);
confirmInput.addEventListener('input',  checkPasswords);

// Bloquear envío si las contraseñas no coinciden
document.getElementById('registroForm').addEventListener('submit', function (e) {
    const pwd     = passwordInput.value;
    const confirm = confirmInput.value;
    if (pwd !== confirm) {
        e.preventDefault();
        confirmHint.textContent = '✖ Las contraseñas no coinciden. Corrígelas antes de continuar.';
        confirmHint.className   = 'confirm-hint no-match';
        confirmInput.focus();
    }
});
</script>

<?php include __DIR__ . '/../include/footer.php'; ?>
