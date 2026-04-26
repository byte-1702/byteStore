<?php
declare(strict_types=1);

session_start();

$pageTitle = "Carrito";
$cssPath = "../CSS/carrito.css";

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = isset($_POST['action']) ? trim((string) $_POST['action']) : '';
    $gameId = isset($_POST['game_id']) ? (int) $_POST['game_id'] : 0;

    if ($action === 'increase' && $gameId > 0 && isset($_SESSION['cart'][$gameId])) {
        $_SESSION['cart'][$gameId]['quantity']++;
    }

    if ($action === 'decrease' && $gameId > 0 && isset($_SESSION['cart'][$gameId])) {
        $_SESSION['cart'][$gameId]['quantity']--;
        if ($_SESSION['cart'][$gameId]['quantity'] <= 0) {
            unset($_SESSION['cart'][$gameId]);
        }
    }

    if ($action === 'remove' && $gameId > 0 && isset($_SESSION['cart'][$gameId])) {
        unset($_SESSION['cart'][$gameId]);
    }

    if ($action === 'clear') {
        $_SESSION['cart'] = [];
    }

    header('Location: carrito.php');
    exit;
}

$cart = $_SESSION['cart'] ?? [];
$subtotal = 0.0;
$itemsCount = 0;
foreach ($cart as $item) {
    $lineTotal = ((float) ($item['price'] ?? 0)) * ((int) ($item['quantity'] ?? 0));
    $subtotal += $lineTotal;
    $itemsCount += (int) ($item['quantity'] ?? 0);
}

$taxes = $subtotal * 0.19;
$total = $subtotal + $taxes;

include __DIR__ . '/../include/header.php';
?>

<section class="home-hero">
    <div class="home-hero__content">
        <p class="home-hero__kicker">Compra</p>
        <h2 class="home-hero__title">Tu carrito de videojuegos</h2>
        <p class="home-hero__subtitle">
            Esta vista te servira para gestionar productos seleccionados, cantidades, subtotal y total antes del pago.
        </p>
        <div class="home-hero__actions">
            <a class="home-btn home-btn--primary" href="productos.php">Seguir comprando</a>
            <a class="home-btn home-btn--ghost" href="login.php">Iniciar sesion para pagar</a>
        </div>
    </div>
</section>

<section class="home-section">
    <div class="home-container">
        <h3 class="home-section__title">Estado actual</h3>
        <div class="home-grid cart-grid">
            <article class="home-card cart-items-card">
                <h4 class="home-card__title">Items</h4>
                <?php if ($itemsCount === 0): ?>
                    <p class="home-card__text">Aun no hay productos agregados al carrito.</p>
                <?php else: ?>
                    <ul class="cart-list">
                        <?php foreach ($cart as $item): ?>
                            <li class="cart-list__item">
                                <div class="cart-item__info">
                                    <p class="cart-item__title"><?php echo htmlspecialchars((string) ($item['title'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></p>
                                    <p class="cart-item__unit">Unitario: $<?php echo number_format((float) ($item['price'] ?? 0), 2); ?></p>
                                </div>
                                <div class="cart-item__controls">
                                    <form method="post" action="carrito.php">
                                        <input type="hidden" name="action" value="decrease">
                                        <input type="hidden" name="game_id" value="<?php echo (int) ($item['id'] ?? 0); ?>">
                                        <button type="submit" class="cart-control-btn" aria-label="Disminuir cantidad">-</button>
                                    </form>
                                    <span class="cart-item__qty">x<?php echo (int) ($item['quantity'] ?? 0); ?></span>
                                    <form method="post" action="carrito.php">
                                        <input type="hidden" name="action" value="increase">
                                        <input type="hidden" name="game_id" value="<?php echo (int) ($item['id'] ?? 0); ?>">
                                        <button type="submit" class="cart-control-btn" aria-label="Aumentar cantidad">+</button>
                                    </form>
                                </div>
                                <p class="cart-item__subtotal">$<?php echo number_format(((float) ($item['price'] ?? 0)) * ((int) ($item['quantity'] ?? 0)), 2); ?></p>
                                <form method="post" action="carrito.php" class="cart-item__remove-form">
                                    <input type="hidden" name="action" value="remove">
                                    <input type="hidden" name="game_id" value="<?php echo (int) ($item['id'] ?? 0); ?>">
                                    <button type="submit" class="cart-remove-btn">Quitar</button>
                                </form>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                    <form method="post" action="carrito.php" class="cart-clear-form">
                        <input type="hidden" name="action" value="clear">
                        <button type="submit" class="home-btn home-btn--ghost">Vaciar carrito</button>
                    </form>
                <?php endif; ?>
            </article>
            <article class="home-card cart-summary-card">
                <h4 class="home-card__title">Resumen</h4>
                <div class="summary-row">
                    <span>Productos</span>
                    <strong><?php echo $itemsCount; ?></strong>
                </div>
                <div class="summary-row">
                    <span>Subtotal</span>
                    <strong>$<?php echo number_format($subtotal, 2); ?></strong>
                </div>
                <div class="summary-row">
                    <span>Impuestos (19%)</span>
                    <strong>$<?php echo number_format($taxes, 2); ?></strong>
                </div>
                <div class="summary-total">
                    <span>Total</span>
                    <strong>$<?php echo number_format($total, 2); ?></strong>
                </div>
            </article>
            <article class="home-card cart-checkout-card">
                <h4 class="home-card__title">Checkout</h4>
                <?php if ($itemsCount === 0): ?>
                    <p class="home-card__text">Agrega juegos para habilitar el checkout.</p>
                    <a class="home-btn home-btn--ghost cart-checkout-btn" href="productos.php">Seguir comprando</a>
                <?php else: ?>
                    <p class="home-card__text">Pedido listo para continuar al pago seguro.</p>
                    <a class="home-btn home-btn--primary cart-checkout-btn" href="login.php">Continuar al pago</a>
                    <a class="home-btn home-btn--ghost cart-checkout-btn" href="productos.php">Agregar mas juegos</a>
                <?php endif; ?>
            </article>
        </div>
    </div>
</section>

<?php include __DIR__ . '/../include/footer.php'; ?>
