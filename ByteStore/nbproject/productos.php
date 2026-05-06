<?php
declare(strict_types=1);

session_start();

$pageTitle = "Productos - ByteStore";
$cssPath = "../CSS/productos.css";

$games = [
    ['id' => 1, 'title' => 'Elden Ring', 'genre' => 'RPG', 'platform' => 'PC', 'price' => 59.99, 'image' => '../assets/elden_ring.webp'],
    ['id' => 2, 'title' => 'Forza Horizon 5', 'genre' => 'Carreras', 'platform' => 'Xbox', 'price' => 49.99, 'image' => '../assets/forza_horizon_5.webp'],
    ['id' => 3, 'title' => 'The Last of Us Part I', 'genre' => 'Accion', 'platform' => 'PlayStation', 'price' => 54.99, 'image' => '../assets/the_last_of_part1.webp'],
    ['id' => 4, 'title' => 'Hades', 'genre' => 'Roguelike', 'platform' => 'PC', 'price' => 24.99, 'image' => '../assets/hades.webp'],
    ['id' => 5, 'title' => 'FC 26', 'genre' => 'Deportes', 'platform' => 'PC', 'price' => 39.99, 'image' => '../assets/ea_sports_fc_26.webp'],
    ['id' => 6, 'title' => 'Cyberpunk 2077', 'genre' => 'RPG', 'platform' => 'PC', 'price' => 44.99, 'image' => '../assets/cyberpunk.webp'],
    ['id' => 7, 'title' => 'Mario Kart 8 Deluxe', 'genre' => 'Carreras', 'platform' => 'Nintendo', 'price' => 59.99, 'image' => '../assets/maria_8_deluxe.webp'],
    ['id' => 8, 'title' => 'Zelda: Tears of the Kingdom', 'genre' => 'Aventura', 'platform' => 'Nintendo', 'price' => 69.99, 'image' => '../assets/zelda.webp'],
    ['id' => 9, 'title' => 'Resident Evil 4 Remake', 'genre' => 'Terror', 'platform' => 'PlayStation', 'price' => 49.99, 'image' => '../assets/resident_evil_4_remake.webp'],
    ['id' => 10, 'title' => 'Hollow Knight', 'genre' => 'Metroidvania', 'platform' => 'PC', 'price' => 14.99, 'image' => '../assets/hollow_knight.webp'],
    ['id' => 11, 'title' => 'Street Fighter 6', 'genre' => 'Pelea', 'platform' => 'PlayStation', 'price' => 44.99, 'image' => '../assets/street_fighter.webp'],
    ['id' => 12, 'title' => 'Minecraft', 'genre' => 'Sandbox', 'platform' => 'Multi', 'price' => 29.99, 'image' => '../assets/minecraft.webp'],
];

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['game_id'])) {
    $gameId = (int) $_POST['game_id'];
    $selected = null;

    foreach ($games as $game) {
        if ($game['id'] === $gameId) {
            $selected = $game;
            break;
        }
    }

    if ($selected !== null) {
        if (isset($_SESSION['cart'][$gameId])) {
            $_SESSION['cart'][$gameId]['quantity']++;
        } else {
            $_SESSION['cart'][$gameId] = [
                'id' => $selected['id'],
                'title' => $selected['title'],
                'price' => $selected['price'],
                'quantity' => 1,
            ];
        }
    }

    $redirectQuery = isset($_POST['return_query']) ? trim((string) $_POST['return_query']) : '';
    $target = 'productos.php' . ($redirectQuery !== '' ? '?' . $redirectQuery . '&added=1' : '?added=1');
    header('Location: ' . $target);
    exit;
}

$selectedGenre = isset($_GET['genre']) ? trim((string) $_GET['genre']) : '';
$selectedPlatform = isset($_GET['platform']) ? trim((string) $_GET['platform']) : '';
$selectedPrice = isset($_GET['price']) ? trim((string) $_GET['price']) : '';
$search = isset($_GET['search']) ? trim((string) $_GET['search']) : '';

$filteredGames = array_filter($games, static function (array $game) use ($selectedGenre, $selectedPlatform, $selectedPrice, $search): bool {
    if ($selectedGenre !== '' && $game['genre'] !== $selectedGenre) {
        return false;
    }

    if ($selectedPlatform !== '' && $game['platform'] !== $selectedPlatform) {
        return false;
    }

    if ($selectedPrice === 'under-30' && $game['price'] >= 30) {
        return false;
    }

    if ($selectedPrice === '30-50' && ($game['price'] < 30 || $game['price'] > 50)) {
        return false;
    }

    if ($selectedPrice === 'over-50' && $game['price'] <= 50) {
        return false;
    }

    if ($search !== '' && stripos($game['title'], $search) === false) {
        return false;
    }

    return true;
});

$cartItems = array_sum(array_column($_SESSION['cart'], 'quantity'));
$cartTotal = 0.0;
foreach ($_SESSION['cart'] as $item) {
    $cartTotal += ((float) $item['price']) * ((int) $item['quantity']);
}

$genres = array_values(array_unique(array_map(static fn(array $g): string => $g['genre'], $games)));
sort($genres);
$platforms = array_values(array_unique(array_map(static fn(array $g): string => $g['platform'], $games)));
sort($platforms);

$currentQuery = $_GET;
unset($currentQuery['added']);
$returnQuery = http_build_query($currentQuery);

include __DIR__ . '/../include/header.php';
?>

<section class="home-hero">
    <div class="home-hero__content">
        <p class="home-hero__kicker">Catalogo</p>
        <h2 class="home-hero__title">Videojuegos disponibles</h2>
        <p class="home-hero__subtitle">
            Explora el catalogo principal de 12 juegos. Puedes filtrar por genero, plataforma y precio, y agregar cada titulo al carrito con su valor.
        </p>
        <div class="home-hero__actions">
            <a class="home-btn home-btn--primary" href="carrito.php">Ir al carrito (<?php echo $cartItems; ?>)</a>
            <a class="home-btn home-btn--ghost" href="index.php">Volver al inicio</a>
        </div>
        <p class="catalog-total">Total carrito: $<?php echo number_format($cartTotal, 2); ?></p>
    </div>
</section>

<section class="home-section">
    <div class="home-container products-layout">
        <aside class="filters-panel">
            <h3 class="home-section__title">Filtros</h3>
            <form method="get" action="productos.php" class="filters-form">
                <label for="search">Buscar juego</label>
                <input type="text" id="search" name="search" value="<?php echo htmlspecialchars($search, ENT_QUOTES, 'UTF-8'); ?>" placeholder="Ejemplo: Zelda">

                <label for="genre">Genero</label>
                <select id="genre" name="genre">
                    <option value="">Todos</option>
                    <?php foreach ($genres as $genre): ?>
                        <option value="<?php echo htmlspecialchars($genre, ENT_QUOTES, 'UTF-8'); ?>" <?php echo $selectedGenre === $genre ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($genre, ENT_QUOTES, 'UTF-8'); ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <label for="platform">Plataforma</label>
                <select id="platform" name="platform">
                    <option value="">Todas</option>
                    <?php foreach ($platforms as $platform): ?>
                        <option value="<?php echo htmlspecialchars($platform, ENT_QUOTES, 'UTF-8'); ?>" <?php echo $selectedPlatform === $platform ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($platform, ENT_QUOTES, 'UTF-8'); ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <label for="price">Rango de precio</label>
                <select id="price" name="price">
                    <option value="">Todos</option>
                    <option value="under-30" <?php echo $selectedPrice === 'under-30' ? 'selected' : ''; ?>>Menos de $30</option>
                    <option value="30-50" <?php echo $selectedPrice === '30-50' ? 'selected' : ''; ?>>$30 a $50</option>
                    <option value="over-50" <?php echo $selectedPrice === 'over-50' ? 'selected' : ''; ?>>Mas de $50</option>
                </select>

                <div class="filters-actions">
                    <button type="submit" class="home-btn home-btn--primary">Aplicar</button>
                    <a class="home-btn home-btn--ghost" href="productos.php">Limpiar</a>
                </div>
            </form>
        </aside>

        <div>
            <h3 class="home-section__title">Todos los juegos</h3>
            <?php if (isset($_GET['added'])): ?>
                <p class="catalog-success">Juego agregado al carrito correctamente.</p>
            <?php endif; ?>
            <div class="products-grid">
                <?php if (count($filteredGames) === 0): ?>
                    <article class="home-card">
                        <h4 class="home-card__title">Sin resultados</h4>
                        <p class="home-card__text">No hay juegos que coincidan con los filtros seleccionados.</p>
                    </article>
                <?php endif; ?>

                <?php foreach ($filteredGames as $game): ?>
                    <article class="product-card">
                        <div class="product-card__image-wrap">
                            <img
                                class="product-card__image"
                                src="<?php echo htmlspecialchars((string) ($game['image'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>"
                                alt="Portada de <?php echo htmlspecialchars($game['title'], ENT_QUOTES, 'UTF-8'); ?>"
                                loading="lazy"
                                onerror="this.style.display='none'; this.parentElement.classList.add('is-fallback');"
                            >
                            <span class="product-card__fallback"><?php echo htmlspecialchars($game['title'], ENT_QUOTES, 'UTF-8'); ?></span>
                        </div>
                        <p class="product-card__tag"><?php echo htmlspecialchars($game['platform'], ENT_QUOTES, 'UTF-8'); ?></p>
                        <h4 class="product-card__title"><?php echo htmlspecialchars($game['title'], ENT_QUOTES, 'UTF-8'); ?></h4>
                        <p class="product-card__meta"><?php echo htmlspecialchars($game['genre'], ENT_QUOTES, 'UTF-8'); ?></p>
                        <p class="product-card__price">$<?php echo number_format((float) $game['price'], 2); ?></p>
                        <form method="post" action="productos.php">
                            <input type="hidden" name="game_id" value="<?php echo (int) $game['id']; ?>">
                            <input type="hidden" name="return_query" value="<?php echo htmlspecialchars($returnQuery, ENT_QUOTES, 'UTF-8'); ?>">
                            <button type="submit" class="home-btn home-btn--primary product-card__button">Agregar al carrito</button>
                        </form>
                    </article>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</section>

<?php include __DIR__ . '/../include/footer.php'; ?>
