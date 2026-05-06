<?php
// Determinar la página actual para marcarla como activa
$currentPage = basename($_SERVER['PHP_SELF']);

// Contar ítems del carrito desde la sesión
$cartCount = 0;
if (!empty($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $item) {
        $cartCount += (int) ($item['quantity'] ?? 0);
    }
}

// Helper: devuelve 'nav-active' si la página coincide
function navActive(string $page, string $current): string {
    return $page === $current ? ' class="nav-active"' : '';
}
?>
<nav aria-label="Navegación principal">
    <ul>
        <li><a href="index.php"<?php echo navActive('index.php', $currentPage); ?>>
            <?php echo gs_icon('house'); ?> Inicio
        </a></li>

        <?php if (isset($_SESSION['user'])): ?>
            <!-- Usuario autenticado -->
            <li><span class="nav-user">
                <?php echo gs_icon('circle-user'); ?>
                <?php echo htmlspecialchars($_SESSION['user']['nombre'], ENT_QUOTES, 'UTF-8'); ?>
            </span></li>
            <li><a href="logout.php"<?php echo navActive('logout.php', $currentPage); ?>>
                <?php echo gs_icon('right-from-bracket'); ?> Salir
            </a></li>
        <?php else: ?>
            <!-- Usuario no autenticado -->
            <li><a href="login.php"<?php echo navActive('login.php', $currentPage); ?>>
                <?php echo gs_icon('right-to-bracket'); ?> Entrar
            </a></li>
            <li><a href="registro.php"<?php echo navActive('registro.php', $currentPage); ?>>
                <?php echo gs_icon('user-plus'); ?> Regístrate
            </a></li>
        <?php endif; ?>

        <li><a href="productos.php"<?php echo navActive('productos.php', $currentPage); ?>>
            <?php echo gs_icon('gamepad'); ?> Productos
        </a></li>

        <li><a href="carrito.php"<?php echo navActive('carrito.php', $currentPage); ?>>
            <?php echo gs_icon('cart-shopping'); ?> Carrito
            <?php if ($cartCount > 0): ?>
                <span class="nav-badge"><?php echo $cartCount; ?></span>
            <?php endif; ?>
        </a></li>
    </ul>
</nav>
