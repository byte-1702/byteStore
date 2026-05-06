<?php
declare(strict_types=1);

session_start();

if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

function calculateCartTotals(array $cart): array
{
    $items = [];
    $subtotal = 0.0;
    $itemsCount = 0;

    foreach ($cart as $item) {
        $qty = (int) ($item['quantity'] ?? 0);
        $price = (float) ($item['price'] ?? 0);
        $lineTotal = $price * $qty;

        if ($qty <= 0 || $price < 0) {
            continue;
        }

        $items[] = [
            'title' => (string) ($item['title'] ?? ''),
            'quantity' => $qty,
            'price' => $price,
            'line_total' => $lineTotal,
        ];

        $itemsCount += $qty;
        $subtotal += $lineTotal;
    }

    $taxes = $subtotal * 0.19;
    $total = $subtotal + $taxes;

    return [
        'items' => $items,
        'items_count' => $itemsCount,
        'subtotal' => $subtotal,
        'taxes' => $taxes,
        'total' => $total,
    ];
}

$cartTotals = calculateCartTotals($_SESSION['cart']);

$paymentMethods = [
    'tarjeta_credito' => 'Tarjeta de credito',
    'tarjeta_debito' => 'Tarjeta de debito',
    'pse' => 'PSE',
    'transferencia' => 'Transferencia bancaria',
];

$banks = [
    'bancolombia' => 'Bancolombia',
    'davivienda' => 'Davivienda',
    'bbva' => 'BBVA',
    'bogota' => 'Banco de Bogota',
    'nequi' => 'Nequi',
];

$selectedMethod = '';
$selectedBank = '';
$confirmData = false;
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm_purchase'])) {
    $token = isset($_POST['csrf_token']) ? (string) $_POST['csrf_token'] : '';

    if (!hash_equals($_SESSION['csrf_token'], $token)) {
        http_response_code(403);
        exit('Token CSRF invalido.');
    }

    if ($cartTotals['items_count'] === 0) {
        header('Location: carrito.php');
        exit;
    }

    $selectedMethod = isset($_POST['payment_method']) ? trim((string) $_POST['payment_method']) : '';
    $selectedBank = isset($_POST['bank']) ? trim((string) $_POST['bank']) : '';
    $confirmData = isset($_POST['confirm_data']) && $_POST['confirm_data'] === '1';

    if (!isset($paymentMethods[$selectedMethod])) {
        $errors[] = 'Selecciona un metodo de pago valido.';
    }
    if (!isset($banks[$selectedBank])) {
        $errors[] = 'Selecciona un banco valido.';
    }
    if (!$confirmData) {
        $errors[] = 'Debes confirmar que los datos del pedido son correctos.';
    }

    if (count($errors) === 0) {
        $orderNumber = 'BS-' . random_int(100000, 999999);

        $_SESSION['last_order'] = [
            'order_number' => $orderNumber,
            'created_at' => date('d/m/Y H:i'),
            'items_count' => $cartTotals['items_count'],
            'subtotal' => $cartTotals['subtotal'],
            'taxes' => $cartTotals['taxes'],
            'total' => $cartTotals['total'],
            'items' => $cartTotals['items'],
            'payment_method' => $paymentMethods[$selectedMethod],
            'bank' => $banks[$selectedBank],
        ];

        $_SESSION['cart'] = [];

        header('Location: confirmacion_pago.php?estado=confirmado');
        exit;
    }
}

$order = null;
$isConfirmedView = isset($_GET['estado']) && $_GET['estado'] === 'confirmado';
if ($isConfirmedView) {
    $order = $_SESSION['last_order'] ?? null;
    if (!$order) {
        header('Location: carrito.php');
        exit;
    }
} elseif ($cartTotals['items_count'] === 0) {
    header('Location: carrito.php');
    exit;
}

$pageTitle = "Confirmacion de pago - ByteStore";
$cssPath = "../CSS/carrito.css";
include __DIR__ . '/../include/header.php';
?>

<section class="home-hero">
    <div class="home-hero__content">
        <?php if ($isConfirmedView): ?>
            <p class="home-hero__kicker">Pago confirmado</p>
            <h2 class="home-hero__title">Compra realizada con exito</h2>
            <p class="home-hero__subtitle">
                Gracias por tu compra, <?php echo htmlspecialchars((string) ($_SESSION['user']['nombre'] ?? 'usuario'), ENT_QUOTES, 'UTF-8'); ?>.
                Tu pedido se registro correctamente en ByteStore.
            </p>
        <?php else: ?>
            <p class="home-hero__kicker">Checkout</p>
            <h2 class="home-hero__title">Confirma tu metodo de pago</h2>
            <p class="home-hero__subtitle">
                Revisa tu pedido, selecciona metodo de pago y banco, confirma tus datos y luego finaliza la compra.
            </p>
        <?php endif; ?>
        <div class="home-hero__actions">
            <a class="home-btn home-btn--primary" href="productos.php">Seguir comprando</a>
            <a class="home-btn home-btn--ghost" href="carrito.php">Volver al carrito</a>
        </div>
    </div>
</section>

<section class="home-section">
    <div class="home-container">
        <h3 class="home-section__title"><?php echo $isConfirmedView ? 'Detalle del pedido' : 'Revision y confirmacion'; ?></h3>
        <div class="home-grid cart-grid">
            <article class="home-card cart-items-card">
                <h4 class="home-card__title"><?php echo $isConfirmedView ? 'Items comprados' : 'Items del carrito'; ?></h4>
                <ul class="cart-list">
                    <?php foreach (($isConfirmedView ? $order['items'] : $cartTotals['items']) as $item): ?>
                        <li class="cart-list__item">
                            <div class="cart-item__info">
                                <p class="cart-item__title"><?php echo htmlspecialchars((string) $item['title'], ENT_QUOTES, 'UTF-8'); ?></p>
                                <p class="cart-item__unit">Unitario: $<?php echo number_format((float) $item['price'], 2); ?></p>
                            </div>
                            <div class="cart-item__controls">
                                <span class="cart-item__qty">x<?php echo (int) $item['quantity']; ?></span>
                            </div>
                            <p class="cart-item__subtotal">$<?php echo number_format((float) $item['line_total'], 2); ?></p>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </article>

            <article class="home-card cart-summary-card">
                <h4 class="home-card__title">Resumen de pago</h4>
                <?php if ($isConfirmedView): ?>
                    <div class="summary-row">
                        <span>Pedido</span>
                        <strong><?php echo htmlspecialchars((string) $order['order_number'], ENT_QUOTES, 'UTF-8'); ?></strong>
                    </div>
                    <div class="summary-row">
                        <span>Fecha</span>
                        <strong><?php echo htmlspecialchars((string) $order['created_at'], ENT_QUOTES, 'UTF-8'); ?></strong>
                    </div>
                    <div class="summary-row">
                        <span>Metodo</span>
                        <strong><?php echo htmlspecialchars((string) $order['payment_method'], ENT_QUOTES, 'UTF-8'); ?></strong>
                    </div>
                    <div class="summary-row">
                        <span>Banco</span>
                        <strong><?php echo htmlspecialchars((string) $order['bank'], ENT_QUOTES, 'UTF-8'); ?></strong>
                    </div>
                <?php endif; ?>
                <div class="summary-row">
                    <span>Productos</span>
                    <strong><?php echo (int) ($isConfirmedView ? $order['items_count'] : $cartTotals['items_count']); ?></strong>
                </div>
                <div class="summary-row">
                    <span>Subtotal</span>
                    <strong>$<?php echo number_format((float) ($isConfirmedView ? $order['subtotal'] : $cartTotals['subtotal']), 2); ?></strong>
                </div>
                <div class="summary-row">
                    <span>Impuestos (19%)</span>
                    <strong>$<?php echo number_format((float) ($isConfirmedView ? $order['taxes'] : $cartTotals['taxes']), 2); ?></strong>
                </div>
                <div class="summary-total">
                    <span><?php echo $isConfirmedView ? 'Total pagado' : 'Total a pagar'; ?></span>
                    <strong>$<?php echo number_format((float) ($isConfirmedView ? $order['total'] : $cartTotals['total']), 2); ?></strong>
                </div>
            </article>

            <article class="home-card cart-checkout-card">
                <?php if ($isConfirmedView): ?>
                    <h4 class="home-card__title">Estado</h4>
                    <p class="home-card__text">Tu pedido fue confirmado y esta en preparacion.</p>
                    <p class="home-card__text">Guarda tu numero de pedido para seguimiento.</p>
                    <a class="home-btn home-btn--primary cart-checkout-btn" href="productos.php">Comprar nuevamente</a>
                    <a class="home-btn home-btn--ghost cart-checkout-btn" href="index.php">Volver al inicio</a>
                <?php else: ?>
                    <h4 class="home-card__title">Metodo de pago</h4>

                    <?php if (count($errors) > 0): ?>
                        <div class="checkout-alert" role="alert">
                            <?php foreach ($errors as $error): ?>
                                <p><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></p>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                    <form method="post" action="confirmacion_pago.php" class="checkout-form">
                        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8'); ?>">
                        <input type="hidden" name="confirm_purchase" value="1">

                        <label for="payment_method" class="checkout-label">Metodo de pago</label>
                        <select id="payment_method" name="payment_method" class="checkout-input" required>
                            <option value="">Selecciona un metodo</option>
                            <?php foreach ($paymentMethods as $value => $label): ?>
                                <option value="<?php echo htmlspecialchars($value, ENT_QUOTES, 'UTF-8'); ?>" <?php echo $selectedMethod === $value ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($label, ENT_QUOTES, 'UTF-8'); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>

                        <label for="bank" class="checkout-label">Banco</label>
                        <select id="bank" name="bank" class="checkout-input" required>
                            <option value="">Selecciona un banco</option>
                            <?php foreach ($banks as $value => $label): ?>
                                <option value="<?php echo htmlspecialchars($value, ENT_QUOTES, 'UTF-8'); ?>" <?php echo $selectedBank === $value ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($label, ENT_QUOTES, 'UTF-8'); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>

                        <label class="checkout-check">
                            <input type="checkbox" name="confirm_data" value="1" <?php echo $confirmData ? 'checked' : ''; ?>>
                            <span>Confirmo que los datos de pago y del pedido son correctos.</span>
                        </label>

                        <button type="submit" class="home-btn home-btn--primary cart-checkout-btn">Confirmar compra ahora</button>
                        <a class="home-btn home-btn--ghost cart-checkout-btn" href="carrito.php">Volver al carrito</a>
                    </form>
                <?php endif; ?>
x            </article>
        </div>
    </div>
</section>

<?php include __DIR__ . '/../include/footer.php'; ?>
