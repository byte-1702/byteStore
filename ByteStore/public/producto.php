<?php
session_start();

$pageTitle = "Catálogo de Productos - ByteStore Academy";
$cssPath   = "../CSS/productos.css";
include __DIR__ . '/../include/header.php';

require_once(__DIR__ . '/../config/database.php');
require_once(__DIR__ . '/../models/producto.php');

if (!isset($pdo)) {
    die("Error crítico: No se pudo conectar a la base de datos");
}

try {
    $productoModel = new Producto($pdo);
    $productos = $productoModel->todos();
} catch (Exception $e) {
    $productos = [];
    $error = $e->getMessage();
}
?>

<section class="productos-hero">
    <div class="productos-hero__content">
        <h1 class="productos-hero__title">Catálogo de Videojuegos</h1>
        <p class="productos-hero__subtitle">Descubre nuestro amplio catálogo de juegos para todas las plataformas</p>
    </div>
</section>

<section class="productos-section">
    <div class="productos-container">
        <?php if (!empty($error)): ?>
            <div class="alert alert--error">
                Error: <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <?php if (empty($productos)): ?>
            <div class="productos-empty">
                <p class="productos-empty__icon">📭</p>
                <h2 class="productos-empty__title">No hay productos disponibles</h2>
                <p class="productos-empty__text">Vuelve pronto para ver nuestro catálogo actualizado</p>
                <a class="home-btn home-btn--primary" href="index.php">
                    Volver al inicio
                </a>
            </div>
        <?php else: ?>
            <div class="productos-grid">
                <?php foreach ($productos as $p): ?>
                    <article class="producto-card">
                        <div class="producto-card__header">
                            <span class="producto-badge"><?php echo htmlspecialchars($p['nombre_categoria']); ?></span>
                        </div>

                        <div class="producto-card__body">
                            <h3 class="producto-card__title">
                                <?php echo htmlspecialchars($p['nombre_producto']); ?>
                            </h3>

                            <p class="producto-card__price">
                                $<?php echo number_format($p['precio'], 2); ?>
                            </p>

                            <div class="producto-card__actions">
                                <button class="btn btn--primary btn-agregar-carrito" data-producto-id="<?php echo $p['id_producto']; ?>">
                                    Agregar al carrito
                                </button>
                                <button class="btn btn--secondary btn-ver-detalles" data-producto-id="<?php echo $p['id_producto']; ?>">
                                    Ver detalles
                                </button>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>

            <div class="productos-stats">
                <p>Total de productos: <strong><?php echo count($productos); ?></strong></p>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php include __DIR__ . '/../include/footer.php'; ?>