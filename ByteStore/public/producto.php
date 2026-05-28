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

<section class="products-layout">
    <div class="products-grid">
        <?php if (!empty($error)): ?>
            <div class="catalog-warning">
                Error: <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <?php if (empty($productos)): ?>
            <div class="catalog-warning">
                <p>No hay productos disponibles</p>
            </div>
        <?php else: ?>
            <?php foreach ($productos as $p): ?>
                <article class="product-card">
                    <div class="product-card__tag">
                        <?php echo htmlspecialchars($p['nombre_categoria']); ?>
                    </div>

                    <h3 class="product-card__title">
                        <?php echo htmlspecialchars($p['nombre_producto']); ?>
                    </h3>

                    <p class="product-card__price">
                        $<?php echo number_format($p['precio'], 2); ?>
                    </p>

                    <div class="product-card__button">
                        <!-- Agregar al carrito -->
                        <form action="carrito.php" method="post">
                            <input type="hidden" name="id_producto" value="<?php echo $p['id_producto']; ?>">
                            <button type="submit" class="btn btn--primary">Agregar al carrito</button>
                        </form>

                        <!-- Ver detalles -->
                        <form action="detalle.php" method="get">
                            <input type="hidden" name="id" value="<?php echo $p['id_producto']; ?>">
                            <button type="submit" class="btn btn--secondary">Ver detalles</button>
                        </form>
                    </div>
                </article>
            <?php endforeach; ?>

            <div class="catalog-total">
                Total de productos: <strong><?php echo count($productos); ?></strong>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php include __DIR__ . '/../include/footer.php'; ?>
