<?php
// Determinar la página actual para marcarla como activa
$currentPage = basename($_SERVER['PHP_SELF'] ?? '');
$selfPath = (string) ($_SERVER['SCRIPT_NAME'] ?? '');
$inAdminArea = str_contains($selfPath, '/admin/');
$navBase = $navBase ?? '';

// Contar ítems del carrito desde la sesión
$cartCount = 0;
if (!empty($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $item) {
        $cartCount += (int) ($item['quantity'] ?? 0);
    }
}

// Helper: devuelve 'nav-active' si la página coincide
function navActive(string $page, string $current): string
{
    return $page === $current ? ' class="nav-active"' : '';
}

$isAdminUser = isset($_SESSION['user']) && ($_SESSION['user']['role'] ?? '') === 'admin';
?>
<nav aria-label="Navegación principal">
    <ul>
        <li><a href="<?php echo htmlspecialchars($navBase . 'index.php', ENT_QUOTES, 'UTF-8'); ?>"<?php echo !$inAdminArea && navActive('index.php', $currentPage) ? ' class="nav-active"' : ''; ?>>
            <?php echo gs_icon('house'); ?> Inicio
        </a></li>

        <?php if (isset($_SESSION['user'])): ?>
            <li><span class="nav-user">
                <?php echo gs_icon('circle-user'); ?>
                <?php echo htmlspecialchars($_SESSION['user']['nombre'], ENT_QUOTES, 'UTF-8'); ?>
            </span></li>
            <?php if ($isAdminUser): ?>
                <li><a href="<?php echo htmlspecialchars($navBase . 'admin/index.php', ENT_QUOTES, 'UTF-8'); ?>"<?php echo $inAdminArea ? ' class="nav-active"' : ''; ?>>
                    <?php echo gs_icon('id-card'); ?> Admin
                </a></li>
            <?php endif; ?>
            <li><a href="<?php echo htmlspecialchars($navBase . 'logout.php', ENT_QUOTES, 'UTF-8'); ?>"<?php echo navActive('logout.php', $currentPage); ?>>
                <?php echo gs_icon('right-from-bracket'); ?> Salir
            </a></li>
        <?php else: ?>
            <li><a href="<?php echo htmlspecialchars($navBase . 'login.php', ENT_QUOTES, 'UTF-8'); ?>"<?php echo navActive('login.php', $currentPage); ?>>
                <?php echo gs_icon('right-to-bracket'); ?> Entrar
            </a></li>
            <li><a href="<?php echo htmlspecialchars($navBase . 'registro.php', ENT_QUOTES, 'UTF-8'); ?>"<?php echo navActive('registro.php', $currentPage); ?>>
                <?php echo gs_icon('user-plus'); ?> Regístrate
            </a></li>
        <?php endif; ?>

        <li><a href="<?php echo htmlspecialchars($navBase . 'producto.php', ENT_QUOTES, 'UTF-8'); ?>"<?php echo navActive('productos.php', $currentPage); ?>>
            <?php echo gs_icon('gamepad'); ?> Productos
        </a></li>

        <li><a href="<?php echo htmlspecialchars($navBase . 'carrito.php', ENT_QUOTES, 'UTF-8'); ?>"<?php echo navActive('carrito.php', $currentPage); ?>>
            <?php echo gs_icon('cart-shopping'); ?> Carrito
            <?php if ($cartCount > 0): ?>
                <span class="nav-badge"><?php echo $cartCount; ?></span>
            <?php endif; ?>
        </a></li>
    </ul>
</nav>
