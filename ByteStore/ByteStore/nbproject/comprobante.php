<?php
declare(strict_types=1);

session_start();

if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}

$order = $_SESSION['last_order'] ?? null;
if (!is_array($order)) {
    header('Location: carrito.php');
    exit;
}

$pageTitle = 'Comprobante (ejemplo) - ByteStore';
$cssPath = '../CSS/carrito.css';
include __DIR__ . '/../include/header.php';

$meta = is_array($order['payment_meta'] ?? null) ? $order['payment_meta'] : [];
$buyer = is_array($order['buyer'] ?? null) ? $order['buyer'] : [];
?>

<section class="invoice-wrap">
    <div class="invoice-toolbar no-print">
        <button type="button" class="home-btn home-btn--primary" onclick="window.print()">Imprimir / PDF</button>
        <a class="home-btn home-btn--ghost" href="confirmacion_pago.php?estado=confirmado">Volver al resumen</a>
        <a class="home-btn home-btn--ghost" href="productos.php">Seguir comprando</a>
    </div>

    <article class="invoice-sheet">
        <header class="invoice-header">
            <div>
                <h2 class="invoice-brand">ByteStore Academy</h2>
                <p class="invoice-muted">Comprobante de venta de demostracion</p>
            </div>
            <div class="invoice-meta-block">
                <p><strong>No. factura (ejemplo)</strong><br><?php echo htmlspecialchars((string) ($order['invoice_number'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></p>
                <p><strong>No. pedido</strong><br><?php echo htmlspecialchars((string) ($order['order_number'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></p>
                <p><strong>Fecha</strong><br><?php echo htmlspecialchars((string) ($order['created_at'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></p>
            </div>
        </header>

        <p class="invoice-disclaimer no-print">
            Este documento es solo para practicas academicas. No tiene validez como factura electronica ante la DIAN.
        </p>

        <section class="invoice-block">
            <h3>Cliente</h3>
            <p><?php echo htmlspecialchars((string) ($buyer['nombre'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></p>
            <p class="invoice-muted"><?php echo htmlspecialchars((string) ($buyer['email'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></p>
        </section>

        <section class="invoice-block">
            <h3>Pago</h3>
            <p><strong>Metodo:</strong> <?php echo htmlspecialchars((string) ($order['payment_method'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></p>
            <?php if (($order['bank'] ?? '') !== ''): ?>
                <p><strong>Banco:</strong> <?php echo htmlspecialchars((string) $order['bank'], ENT_QUOTES, 'UTF-8'); ?></p>
            <?php endif; ?>
            <?php if (isset($meta['card_last4'])): ?>
                <p><strong>Titular tarjeta:</strong> <?php echo htmlspecialchars((string) ($meta['card_holder'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></p>
                <p><strong>Tarjeta:</strong> **** **** **** <?php echo htmlspecialchars((string) $meta['card_last4'], ENT_QUOTES, 'UTF-8'); ?> · Vence <?php echo htmlspecialchars((string) ($meta['card_exp'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></p>
            <?php endif; ?>
            <?php if (isset($meta['pse_email'])): ?>
                <p><strong>Documento:</strong> <?php echo htmlspecialchars((string) ($meta['pse_doc_type'] ?? ''), ENT_QUOTES, 'UTF-8'); ?> <?php echo htmlspecialchars((string) ($meta['pse_doc_number'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></p>
                <p><strong>Persona:</strong> <?php echo htmlspecialchars((string) ($meta['pse_person_type'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></p>
                <p><strong>Correo PSE:</strong> <?php echo htmlspecialchars((string) $meta['pse_email'], ENT_QUOTES, 'UTF-8'); ?></p>
            <?php endif; ?>
            <?php if (isset($meta['xfer_reference'])): ?>
                <p><strong>Banco origen:</strong> <?php echo htmlspecialchars((string) ($meta['xfer_bank'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></p>
                <p><strong>Referencia:</strong> <?php echo htmlspecialchars((string) $meta['xfer_reference'], ENT_QUOTES, 'UTF-8'); ?></p>
                <p><strong>Fecha consignacion:</strong> <?php echo htmlspecialchars((string) ($meta['xfer_date'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></p>
                <?php if (($meta['xfer_notes'] ?? '') !== ''): ?>
                    <p><strong>Notas:</strong> <?php echo htmlspecialchars((string) $meta['xfer_notes'], ENT_QUOTES, 'UTF-8'); ?></p>
                <?php endif; ?>
            <?php endif; ?>
        </section>

        <table class="invoice-table">
            <thead>
                <tr>
                    <th>Descripcion</th>
                    <th>Cant.</th>
                    <th>V. unitario</th>
                    <th>Total linea</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($order['items'] ?? [] as $row): ?>
                    <?php if (!is_array($row)) {
                        continue;
                    } ?>
                    <tr>
                        <td><?php echo htmlspecialchars((string) ($row['title'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo (int) ($row['quantity'] ?? 0); ?></td>
                        <td>$<?php echo number_format((float) ($row['price'] ?? 0), 2); ?></td>
                        <td>$<?php echo number_format((float) ($row['line_total'] ?? 0), 2); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div class="invoice-totals">
            <div class="invoice-total-row"><span>Subtotal</span><strong>$<?php echo number_format((float) ($order['subtotal'] ?? 0), 2); ?></strong></div>
            <div class="invoice-total-row"><span>IVA 19%</span><strong>$<?php echo number_format((float) ($order['taxes'] ?? 0), 2); ?></strong></div>
            <div class="invoice-total-row invoice-total-row--grand"><span>Total</span><strong>$<?php echo number_format((float) ($order['total'] ?? 0), 2); ?></strong></div>
        </div>

        <footer class="invoice-footer">
            <p>Gracias por tu compra en ByteStore Academy (proyecto educativo).</p>
        </footer>
    </article>
</section>

<?php include __DIR__ . '/../include/footer.php'; ?>
