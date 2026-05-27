</main>
<?php $navBase = $navBase ?? ''; ?>
<footer class="main-footer">
    <div class="footer-content">

        <div class="footer-section">
            <h3>ByteStore Academy</h3>
            <p><small>Plataforma académica para practicar un e-commerce de videojuegos tipo Steam.</small></p>
            <div class="social-links">
                <a href="https://www.facebook.com/" class="social-link" aria-label="Facebook" target="_blank" rel="noopener">
                    <?php echo gs_icon('facebook-f'); ?>
                </a>
                <a href="https://x.com/" class="social-link" aria-label="X (Twitter)" target="_blank" rel="noopener">
                    <?php echo gs_icon('x-twitter'); ?>
                </a>
                <a href="https://www.youtube.com/" class="social-link" aria-label="YouTube" target="_blank" rel="noopener">
                    <?php echo gs_icon('youtube'); ?>
                </a>
            </div>
        </div>

        <div class="footer-section">
            <h4>Navegación</h4>
            <ul class="footer-links">
                <li><a href="<?php echo htmlspecialchars($navBase . 'index.php', ENT_QUOTES, 'UTF-8'); ?>">Inicio</a></li>
                <li><a href="<?php echo htmlspecialchars($navBase . 'login.php', ENT_QUOTES, 'UTF-8'); ?>">Entrar</a></li>
                <li><a href="<?php echo htmlspecialchars($navBase . 'registro.php', ENT_QUOTES, 'UTF-8'); ?>">Regístrate</a></li>
                <li><a href="<?php echo htmlspecialchars($navBase . 'productos.php', ENT_QUOTES, 'UTF-8'); ?>">Productos</a></li>
                <li><a href="<?php echo htmlspecialchars($navBase . 'carrito.php', ENT_QUOTES, 'UTF-8'); ?>">Carrito</a></li>
            </ul>
        </div>

        <div class="footer-section">
            <h4>Legal</h4>
            <ul class="footer-links">
                <li><a href="<?php echo htmlspecialchars($navBase . 'terms_legales.php', ENT_QUOTES, 'UTF-8'); ?>">Términos y Condiciones</a></li>
                <li><a href="<?php echo htmlspecialchars($navBase . 'privacidad.php', ENT_QUOTES, 'UTF-8'); ?>">Política de Privacidad</a></li>
            </ul>
        </div>

        <div class="footer-section">
            <h4>Ayuda</h4>
            <ul class="footer-links">
                <li><a href="<?php echo htmlspecialchars($navBase . 'manual.php', ENT_QUOTES, 'UTF-8'); ?>">Manual de Usuario</a></li>
                <li><a href="<?php echo htmlspecialchars($navBase . 'faq.php', ENT_QUOTES, 'UTF-8'); ?>">Preguntas Frecuentes</a></li>
            </ul>
        </div>

        <div class="footer-section">
            <h4>Información</h4>
            <ul class="footer-links">
                <li><a href="<?php echo htmlspecialchars($navBase . 'acerca.php', ENT_QUOTES, 'UTF-8'); ?>">Acerca de Nosotros</a></li>
            </ul>
        </div>

    </div>

    <div class="footer-bottom">
        <p>&copy; <?php echo date('Y'); ?> ByteStore Academy &mdash; Todos los derechos reservados.</p>
        <p>Hecho con <span class="footer-heart"><?php echo gs_icon('heart'); ?></span> por Fredwil Márquez.</p>
    </div>
</footer>

</body>
</html>
