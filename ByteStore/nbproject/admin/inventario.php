<?php
declare(strict_types=1);

session_start();

if (!isset($_SESSION['user']) || ($_SESSION['user']['role'] ?? '') !== 'admin') {
    header('Location: ../login.php');
    exit;
}

require_once __DIR__ . '/../../include/products_store.php';

$flashOk = '';
$flashErr = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = isset($_POST['csrf_token']) ? (string) $_POST['csrf_token'] : '';
    if (!hash_equals($_SESSION['csrf_token'] ?? '', $token)) {
        $flashErr = 'Token de seguridad invalido.';
    } else {
        $action = isset($_POST['action']) ? trim((string) $_POST['action']) : '';
        $list = products_load();

        if ($action === 'save') {
            $id = (int) ($_POST['id'] ?? 0);
            $title = trim((string) ($_POST['title'] ?? ''));
            $genre = trim((string) ($_POST['genre'] ?? ''));
            $platform = trim((string) ($_POST['platform'] ?? ''));
            $price = (float) str_replace(',', '.', (string) ($_POST['price'] ?? '0'));
            $stock = (int) ($_POST['stock'] ?? 0);
            $image = trim((string) ($_POST['image'] ?? ''));

            if ($id <= 0 || $title === '' || $genre === '' || $platform === '') {
                $flashErr = 'Completa titulo, genero y plataforma.';
            } elseif ($price < 0 || $stock < 0) {
                $flashErr = 'Precio y stock deben ser valores validos.';
            } else {
                $found = false;
                foreach ($list as $i => $row) {
                    if ((int) ($row['id'] ?? 0) === $id) {
                        $list[$i]['title'] = $title;
                        $list[$i]['genre'] = $genre;
                        $list[$i]['platform'] = $platform;
                        $list[$i]['price'] = round($price, 2);
                        $list[$i]['stock'] = $stock;
                        $list[$i]['image'] = $image !== '' ? $image : '../assets/minecraft.webp';
                        $found = true;
                        break;
                    }
                }
                if (!$found) {
                    $flashErr = 'Producto no encontrado.';
                } elseif (!products_save($list)) {
                    $flashErr = 'No se pudo guardar (revisa permisos de la carpeta data/).';
                } else {
                    $flashOk = 'Producto actualizado.';
                }
            }
        } elseif ($action === 'delete') {
            $id = (int) ($_POST['id'] ?? 0);
            $newList = array_values(array_filter($list, static fn(array $r): bool => (int) ($r['id'] ?? 0) !== $id));
            if (count($newList) === count($list)) {
                $flashErr = 'No se encontro el producto a eliminar.';
            } elseif (!products_save($newList)) {
                $flashErr = 'No se pudo guardar tras eliminar.';
            } else {
                $flashOk = 'Producto eliminado.';
            }
        } elseif ($action === 'create') {
            $title = trim((string) ($_POST['new_title'] ?? ''));
            $genre = trim((string) ($_POST['new_genre'] ?? ''));
            $platform = trim((string) ($_POST['new_platform'] ?? ''));
            $price = (float) str_replace(',', '.', (string) ($_POST['new_price'] ?? '0'));
            $stock = (int) ($_POST['new_stock'] ?? 0);
            $image = trim((string) ($_POST['new_image'] ?? ''));

            if ($title === '' || $genre === '' || $platform === '') {
                $flashErr = 'Completa los campos del nuevo producto.';
            } else {
                $maxId = 0;
                foreach ($list as $row) {
                    $maxId = max($maxId, (int) ($row['id'] ?? 0));
                }
                $list[] = [
                    'id' => $maxId + 1,
                    'title' => $title,
                    'genre' => $genre,
                    'platform' => $platform,
                    'price' => round(max(0, $price), 2),
                    'stock' => max(0, $stock),
                    'image' => $image !== '' ? $image : '../assets/minecraft.webp',
                ];
                if (!products_save($list)) {
                    $flashErr = 'No se pudo crear el producto.';
                } else {
                    $flashOk = 'Producto creado.';
                }
            }
        }
    }
}

$games = products_load();

$pageTitle = 'Inventario - ByteStore Admin';
$cssPath = '../../CSS/admin.css';
include __DIR__ . '/../../include/header.php';
?>

<section class="home-hero">
    <div class="home-hero__content">
        <p class="home-hero__kicker">Inventario</p>
        <h2 class="home-hero__title">Productos y stock</h2>
        <p class="home-hero__subtitle">
            Los cambios se reflejan de inmediato en la pagina de productos y en el control de stock al pagar.
        </p>
        <div class="home-hero__actions">
            <a class="home-btn home-btn--ghost" href="index.php">Volver al panel</a>
        </div>
    </div>
</section>

<section class="admin-section">
    <div class="admin-container">
        <?php if ($flashOk !== ''): ?>
            <p class="admin-flash admin-flash--ok" role="status"><?php echo htmlspecialchars($flashOk, ENT_QUOTES, 'UTF-8'); ?></p>
        <?php endif; ?>
        <?php if ($flashErr !== ''): ?>
            <p class="admin-flash admin-flash--err" role="alert"><?php echo htmlspecialchars($flashErr, ENT_QUOTES, 'UTF-8'); ?></p>
        <?php endif; ?>

        <div class="admin-list">
            <?php foreach ($games as $g): ?>
                <div class="admin-card">
                    <form method="post" action="inventario.php" class="admin-edit-form">
                        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8'); ?>">
                        <input type="hidden" name="action" value="save">
                        <input type="hidden" name="id" value="<?php echo (int) $g['id']; ?>">
                        <div class="admin-edit-grid">
                            <span class="admin-id-pill">ID <?php echo (int) $g['id']; ?></span>
                            <label class="admin-field">Titulo
                                <input class="admin-input" type="text" name="title" value="<?php echo htmlspecialchars((string) $g['title'], ENT_QUOTES, 'UTF-8'); ?>" required>
                            </label>
                            <label class="admin-field">Genero
                                <input class="admin-input" type="text" name="genre" value="<?php echo htmlspecialchars((string) $g['genre'], ENT_QUOTES, 'UTF-8'); ?>" required>
                            </label>
                            <label class="admin-field">Plataforma
                                <input class="admin-input" type="text" name="platform" value="<?php echo htmlspecialchars((string) $g['platform'], ENT_QUOTES, 'UTF-8'); ?>" required>
                            </label>
                            <label class="admin-field">Precio
                                <input class="admin-input" type="number" step="0.01" min="0" name="price" value="<?php echo htmlspecialchars((string) $g['price'], ENT_QUOTES, 'UTF-8'); ?>">
                            </label>
                            <label class="admin-field">Stock
                                <input class="admin-input" type="number" min="0" name="stock" value="<?php echo (int) $g['stock']; ?>">
                            </label>
                            <label class="admin-field admin-field--wide">Imagen (ruta)
                                <input class="admin-input" type="text" name="image" value="<?php echo htmlspecialchars((string) $g['image'], ENT_QUOTES, 'UTF-8'); ?>">
                            </label>
                        </div>
                        <div class="admin-card-actions">
                            <button type="submit" class="home-btn home-btn--primary admin-btn">Guardar cambios</button>
                        </div>
                    </form>
                    <form method="post" action="inventario.php" class="admin-delete-form" onsubmit="return confirm('Eliminar este producto?');">
                        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8'); ?>">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="id" value="<?php echo (int) $g['id']; ?>">
                        <button type="submit" class="home-btn home-btn--ghost admin-btn admin-btn--danger">Eliminar</button>
                    </form>
                </div>
            <?php endforeach; ?>
        </div>

        <h3 class="admin-subtitle">Nuevo producto</h3>
        <form method="post" action="inventario.php" class="admin-create-form">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8'); ?>">
            <input type="hidden" name="action" value="create">
            <div class="admin-create-grid">
                <label>Titulo <input class="admin-input" type="text" name="new_title" required></label>
                <label>Genero <input class="admin-input" type="text" name="new_genre" required></label>
                <label>Plataforma <input class="admin-input" type="text" name="new_platform" required></label>
                <label>Precio <input class="admin-input" type="number" step="0.01" min="0" name="new_price" value="29.99"></label>
                <label>Stock <input class="admin-input" type="number" min="0" name="new_stock" value="10"></label>
                <label class="admin-create-span2">Ruta imagen <input class="admin-input" type="text" name="new_image" placeholder="../assets/ejemplo.webp"></label>
            </div>
            <button type="submit" class="home-btn home-btn--primary">Crear producto</button>
        </form>
    </div>
</section>

<?php include __DIR__ . '/../../include/footer.php'; ?>
