<?php
declare(strict_types=1);

session_start();

require_once __DIR__ . '/../include/products_store.php';

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

/**
 * @return array{items: list<array<string, mixed>>, items_count: int, subtotal: float, taxes: float, total: float, stock_lines: list<array{id:int, quantity:int}>}
 */
function calculateCartTotals(array $cart): array
{
    $items = [];
    $subtotal = 0.0;
    $itemsCount = 0;
    $stockLines = [];

    foreach ($cart as $item) {
        $qty = (int) ($item['quantity'] ?? 0);
        $price = (float) ($item['price'] ?? 0);
        $lineTotal = $price * $qty;
        $id = (int) ($item['id'] ?? 0);

        if ($qty <= 0 || $price < 0 || $id <= 0) {
            continue;
        }

        $items[] = [
            'id' => $id,
            'title' => (string) ($item['title'] ?? ''),
            'quantity' => $qty,
            'price' => $price,
            'line_total' => $lineTotal,
        ];
        $stockLines[] = ['id' => $id, 'quantity' => $qty];

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
        'stock_lines' => $stockLines,
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

$pseDocTypes = [
    'CC' => 'Cedula de ciudadania',
    'CE' => 'Cedula de extranjeria',
    'NIT' => 'NIT',
    'PAS' => 'Pasaporte',
];

$personTypes = [
    'natural' => 'Persona natural',
    'juridica' => 'Persona juridica',
];

$selectedMethod = '';
$selectedBank = '';
$confirmData = false;
$errors = [];

$postStr = static function (string $key): string {
    return isset($_POST[$key]) ? trim((string) $_POST[$key]) : '';
};

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

    $selectedMethod = $postStr('payment_method');
    $selectedBank = $postStr('bank');
    $confirmData = isset($_POST['confirm_data']) && $_POST['confirm_data'] === '1';

    if (!isset($paymentMethods[$selectedMethod])) {
        $errors[] = 'Selecciona un metodo de pago valido.';
    }

    $isCard = in_array($selectedMethod, ['tarjeta_credito', 'tarjeta_debito'], true);
    $isPse = $selectedMethod === 'pse';
    $isXfer = $selectedMethod === 'transferencia';

    if ($isCard || $isPse) {
        if (!isset($banks[$selectedBank])) {
            $errors[] = 'Selecciona un banco valido.';
        }
    }

    if (!$confirmData) {
        $errors[] = 'Debes confirmar que los datos del pedido son correctos.';
    }

    $paymentMeta = [];

    if ($isCard && count($errors) === 0) {
        $holder = $postStr('card_holder');
        $number = preg_replace('/\D/', '', $postStr('card_number'));
        $month = $postStr('card_exp_month');
        $year = $postStr('card_exp_year');
        $cvv = preg_replace('/\D/', '', $postStr('card_cvv'));

        if (strlen($holder) < 3) {
            $errors[] = 'Indica el titular de la tarjeta.';
        }
        if ($number === '' || strlen($number) < 13 || strlen($number) > 19) {
            $errors[] = 'Numero de tarjeta invalido (demo: 13 a 19 digitos).';
        }
        if (!preg_match('/^(0?[1-9]|1[0-2])$/', $month)) {
            $errors[] = 'Mes de vencimiento invalido.';
        }
        $y = strlen($year) === 2 ? (int) ('20' . $year) : (int) $year;
        $currentY = (int) date('Y');
        if ($y < $currentY || $y > $currentY + 20) {
            $errors[] = 'Anio de vencimiento invalido.';
        }
        if (strlen($cvv) < 3 || strlen($cvv) > 4) {
            $errors[] = 'CVV invalido (3 o 4 digitos).';
        }

        if (count($errors) === 0) {
            $last4 = substr($number, -4);
            $paymentMeta = [
                'card_holder' => $holder,
                'card_last4' => $last4,
                'card_exp' => str_pad($month, 2, '0', STR_PAD_LEFT) . '/' . (strlen($year) === 2 ? $year : substr((string) $y, -2)),
            ];
        }
    }

    if ($isPse && count($errors) === 0) {
        $docType = $postStr('pse_doc_type');
        $docNum = $postStr('pse_doc_number');
        $pType = $postStr('pse_person_type');
        $email = $postStr('pse_email');

        if (!isset($pseDocTypes[$docType])) {
            $errors[] = 'Selecciona un tipo de documento valido.';
        }
        if (strlen($docNum) < 5) {
            $errors[] = 'Numero de documento invalido.';
        }
        if (!isset($personTypes[$pType])) {
            $errors[] = 'Selecciona tipo de persona.';
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Correo para PSE invalido.';
        }

        if (count($errors) === 0) {
            $paymentMeta = [
                'pse_doc_type' => $pseDocTypes[$docType],
                'pse_doc_number' => $docNum,
                'pse_person_type' => $personTypes[$pType],
                'pse_email' => $email,
            ];
        }
    }

    if ($isXfer && count($errors) === 0) {
        $fromBank = $postStr('xfer_bank_from');
        $ref = $postStr('xfer_reference');
        $dateStr = $postStr('xfer_date');
        $notes = $postStr('xfer_notes');

        if (!isset($banks[$fromBank])) {
            $errors[] = 'Selecciona el banco desde el que transferiste.';
        }
        if (strlen($ref) < 4) {
            $errors[] = 'Indica la referencia o numero de consignacion.';
        }
        $ts = strtotime($dateStr);
        if ($dateStr === '' || $ts === false) {
            $errors[] = 'Fecha de transferencia invalida.';
        }

        if (count($errors) === 0) {
            $paymentMeta = [
                'xfer_bank' => $banks[$fromBank],
                'xfer_reference' => $ref,
                'xfer_date' => date('d/m/Y', $ts),
                'xfer_notes' => $notes,
            ];
            $selectedBank = $fromBank;
        }
    }

    if (count($errors) === 0 && !products_stock_sufficient($cartTotals['stock_lines'])) {
        $errors[] = 'No hay stock suficiente para uno o mas productos. Actualiza el carrito e intenta de nuevo.';
    }

    if (count($errors) === 0) {
        products_decrement_stock($cartTotals['stock_lines']);

        $orderNumber = 'BS-' . random_int(100000, 999999);
        $invoiceNumber = 'FAC-EJ-' . random_int(100000, 999999);

        $bankLabel = '';
        if ($isXfer) {
            $bankLabel = $banks[$selectedBank] ?? '';
        } elseif ($selectedBank !== '') {
            $bankLabel = $banks[$selectedBank];
        }

        $_SESSION['last_order'] = [
            'order_number' => $orderNumber,
            'invoice_number' => $invoiceNumber,
            'created_at' => date('d/m/Y H:i'),
            'items_count' => $cartTotals['items_count'],
            'subtotal' => $cartTotals['subtotal'],
            'taxes' => $cartTotals['taxes'],
            'total' => $cartTotals['total'],
            'items' => $cartTotals['items'],
            'payment_method' => $paymentMethods[$selectedMethod],
            'bank' => $bankLabel,
            'payment_meta' => $paymentMeta,
            'buyer' => [
                'nombre' => (string) ($_SESSION['user']['nombre'] ?? ''),
                'email' => (string) ($_SESSION['user']['email'] ?? ''),
            ],
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
                Tu pedido se registro correctamente en ByteStore (entorno de demostracion).
            </p>
        <?php else: ?>
            <p class="home-hero__kicker">Checkout</p>
            <h2 class="home-hero__title">Confirma tu metodo de pago</h2>
            <p class="home-hero__subtitle">
                Completa los datos segun el metodo elegido. Ningun pago real se procesa; es un flujo educativo de ejemplo.
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
                        <span>Factura (ejemplo)</span>
                        <strong><?php echo htmlspecialchars((string) ($order['invoice_number'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></strong>
                    </div>
                    <div class="summary-row">
                        <span>Fecha</span>
                        <strong><?php echo htmlspecialchars((string) $order['created_at'], ENT_QUOTES, 'UTF-8'); ?></strong>
                    </div>
                    <div class="summary-row">
                        <span>Metodo</span>
                        <strong><?php echo htmlspecialchars((string) $order['payment_method'], ENT_QUOTES, 'UTF-8'); ?></strong>
                    </div>
                    <?php if (($order['bank'] ?? '') !== ''): ?>
                        <div class="summary-row">
                            <span>Banco</span>
                            <strong><?php echo htmlspecialchars((string) $order['bank'], ENT_QUOTES, 'UTF-8'); ?></strong>
                        </div>
                    <?php endif; ?>
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
                    <p class="home-card__text">Tu pedido fue confirmado (simulacion). Puedes abrir el comprobante tipo factura de ejemplo.</p>
                    <a class="home-btn home-btn--primary cart-checkout-btn" href="comprobante.php" target="_blank" rel="noopener">Ver factura / comprobante (ejemplo)</a>
                    <a class="home-btn home-btn--ghost cart-checkout-btn" href="productos.php">Comprar nuevamente</a>
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

                    <form method="post" action="confirmacion_pago.php" class="checkout-form" id="checkout-form">
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

                        <div id="panel-bank" class="payment-panel" hidden>
                            <label for="bank" class="checkout-label" id="label-bank">Banco</label>
                            <select id="bank" name="bank" class="checkout-input">
                                <option value="">Selecciona un banco</option>
                                <?php foreach ($banks as $value => $label): ?>
                                    <option value="<?php echo htmlspecialchars($value, ENT_QUOTES, 'UTF-8'); ?>" <?php echo $selectedBank === $value ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($label, ENT_QUOTES, 'UTF-8'); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div id="panel-card" class="payment-panel" hidden>
                            <p class="checkout-hint">Datos de tarjeta (solo demostracion; no se almacena el numero completo ni el CVV).</p>
                            <label class="checkout-label" for="card_holder">Titular</label>
                            <input class="checkout-input" type="text" id="card_holder" name="card_holder" autocomplete="cc-name" placeholder="Como figura en la tarjeta">

                            <label class="checkout-label" for="card_number">Numero de tarjeta</label>
                            <input class="checkout-input" type="text" id="card_number" name="card_number" inputmode="numeric" maxlength="19" placeholder="16 digitos de ejemplo">

                            <div class="checkout-row-2">
                                <div>
                                    <label class="checkout-label" for="card_exp_month">Mes</label>
                                    <input class="checkout-input" type="text" id="card_exp_month" name="card_exp_month" maxlength="2" placeholder="MM">
                                </div>
                                <div>
                                    <label class="checkout-label" for="card_exp_year">Año</label>
                                    <input class="checkout-input" type="text" id="card_exp_year" name="card_exp_year" maxlength="4" placeholder="AAAA">
                                </div>
                                <div>
                                    <label class="checkout-label" for="card_cvv">CVV</label>
                                    <input class="checkout-input" type="password" id="card_cvv" name="card_cvv" maxlength="4" placeholder="***" autocomplete="off">
                                </div>
                            </div>
                        </div>

                        <div id="panel-pse" class="payment-panel" hidden>
                            <label class="checkout-label" for="pse_doc_type">Tipo de documento</label>
                            <select id="pse_doc_type" name="pse_doc_type" class="checkout-input">
                                <option value="">Selecciona</option>
                                <?php foreach ($pseDocTypes as $value => $label): ?>
                                    <option value="<?php echo htmlspecialchars($value, ENT_QUOTES, 'UTF-8'); ?>"><?php echo htmlspecialchars($label, ENT_QUOTES, 'UTF-8'); ?></option>
                                <?php endforeach; ?>
                            </select>
                            <label class="checkout-label" for="pse_doc_number">Numero de documento</label>
                            <input class="checkout-input" type="text" id="pse_doc_number" name="pse_doc_number" inputmode="numeric">

                            <label class="checkout-label" for="pse_person_type">Tipo de persona</label>
                            <select id="pse_person_type" name="pse_person_type" class="checkout-input">
                                <option value="">Selecciona</option>
                                <?php foreach ($personTypes as $value => $label): ?>
                                    <option value="<?php echo htmlspecialchars($value, ENT_QUOTES, 'UTF-8'); ?>"><?php echo htmlspecialchars($label, ENT_QUOTES, 'UTF-8'); ?></option>
                                <?php endforeach; ?>
                            </select>

                            <label class="checkout-label" for="pse_email">Correo (notificacion PSE)</label>
                            <input class="checkout-input" type="email" id="pse_email" name="pse_email" autocomplete="email">
                        </div>

                        <div id="panel-transfer" class="payment-panel" hidden>
                            <div class="transfer-destino">
                                <strong>Cuenta destino (demo ByteStore)</strong>
                                <p>Banco: Bancolombia · Cuenta de ahorros · No. 1234567890</p>
                                <p>Titular: ByteStore Academy S.A.S. (ficticio)</p>
                            </div>
                            <label class="checkout-label" for="xfer_bank_from">Tu banco (origen)</label>
                            <select id="xfer_bank_from" name="xfer_bank_from" class="checkout-input">
                                <option value="">Selecciona</option>
                                <?php foreach ($banks as $value => $label): ?>
                                    <option value="<?php echo htmlspecialchars($value, ENT_QUOTES, 'UTF-8'); ?>"><?php echo htmlspecialchars($label, ENT_QUOTES, 'UTF-8'); ?></option>
                                <?php endforeach; ?>
                            </select>
                            <label class="checkout-label" for="xfer_reference">Referencia / consignacion</label>
                            <input class="checkout-input" type="text" id="xfer_reference" name="xfer_reference">
                            <label class="checkout-label" for="xfer_date">Fecha de transferencia</label>
                            <input class="checkout-input" type="date" id="xfer_date" name="xfer_date">
                            <label class="checkout-label" for="xfer_notes">Notas (opcional)</label>
                            <input class="checkout-input" type="text" id="xfer_notes" name="xfer_notes" placeholder="Observaciones">
                        </div>

                        <label class="checkout-check">
                            <input type="checkbox" name="confirm_data" value="1" <?php echo $confirmData ? 'checked' : ''; ?>>
                            <span>Confirmo que los datos del pedido y del pago son correctos.</span>
                        </label>

                        <button type="submit" class="home-btn home-btn--primary cart-checkout-btn">Confirmar compra ahora</button>
                        <a class="home-btn home-btn--ghost cart-checkout-btn" href="carrito.php">Volver al carrito</a>
                    </form>
                <?php endif; ?>
            </article>
        </div>
    </div>
</section>

<?php if (!$isConfirmedView): ?>
<script>
(function () {
    var method = document.getElementById('payment_method');
    var panelBank = document.getElementById('panel-bank');
    var panelCard = document.getElementById('panel-card');
    var panelPse = document.getElementById('panel-pse');
    var panelXfer = document.getElementById('panel-transfer');
    var bankSelect = document.getElementById('bank');
    var labelBank = document.getElementById('label-bank');

    function setRequired(el, on) {
        if (!el) return;
        if (on) el.setAttribute('required', 'required');
        else el.removeAttribute('required');
    }

    function sync() {
        var v = method && method.value;
        if (!v) {
            panelBank.hidden = true;
            panelCard.hidden = true;
            panelPse.hidden = true;
            panelXfer.hidden = true;
            setRequired(bankSelect, false);
            return;
        }
        var isCard = v === 'tarjeta_credito' || v === 'tarjeta_debito';
        var isPse = v === 'pse';
        var isXfer = v === 'transferencia';

        panelBank.hidden = isXfer;
        panelCard.hidden = !isCard;
        panelPse.hidden = !isPse;
        panelXfer.hidden = !isXfer;

        setRequired(bankSelect, isCard || isPse);
        setRequired(document.getElementById('card_holder'), isCard);
        setRequired(document.getElementById('card_number'), isCard);
        setRequired(document.getElementById('card_exp_month'), isCard);
        setRequired(document.getElementById('card_exp_year'), isCard);
        setRequired(document.getElementById('card_cvv'), isCard);

        setRequired(document.getElementById('pse_doc_type'), isPse);
        setRequired(document.getElementById('pse_doc_number'), isPse);
        setRequired(document.getElementById('pse_person_type'), isPse);
        setRequired(document.getElementById('pse_email'), isPse);

        setRequired(document.getElementById('xfer_bank_from'), isXfer);
        setRequired(document.getElementById('xfer_reference'), isXfer);
        setRequired(document.getElementById('xfer_date'), isXfer);

        if (labelBank) {
            labelBank.textContent = isCard ? 'Banco emisor' : 'Banco (PSE)';
        }
    }

    if (method) {
        method.addEventListener('change', sync);
        sync();
    }
})();
</script>
<?php endif; ?>

<?php include __DIR__ . '/../include/footer.php'; ?>
