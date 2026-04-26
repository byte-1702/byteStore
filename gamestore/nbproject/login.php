<?php
$pageTitle = "GameStore Academy";
$cssPath = "../CSS/glob_login.css";
include __DIR__ . '/../include/header.php';
?>


    <section>
        <div>
            <h2>Iniciar Sesion</h2>
            <form action="authenticate.php" method="post">

                <div class="auth-form-group">
                    <input type="text" name="identifier" class="auth-form-input"
                           placeholder="Usuario o correo electrónico" required>
                </div>

                <div class="auth-form-group">
                    <input type="password" name="password" class="auth-form-input"
                           placeholder="Contraseña" required>
                </div>

                <button type="submit" class="auth-form-button">Entrar a mi cuenta</button>

            </form>
        </div>
    </section>

<?php include __DIR__ . '/../include/footer.php'; ?>