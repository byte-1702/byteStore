<?php
declare(strict_types=1);

/**
 * Catálogo persistente (JSON) — demo académica: inventario y tienda.
 */

function products_json_path(): string
{
    return dirname(__DIR__) . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'products.json';
}

/**
 * @return list<array{id:int,title:string,genre:string,platform:string,price:float,image:string,stock:int}>
 */
function products_default_catalog(): array
{
    return [
        ['id' => 1, 'title' => 'Elden Ring', 'genre' => 'RPG', 'platform' => 'PC', 'price' => 59.99, 'image' => '../assets/elden_ring.webp', 'stock' => 80],
        ['id' => 2, 'title' => 'Forza Horizon 5', 'genre' => 'Carreras', 'platform' => 'Xbox', 'price' => 49.99, 'image' => '../assets/forza_horizon_5.webp', 'stock' => 60],
        ['id' => 3, 'title' => 'The Last of Us Part I', 'genre' => 'Accion', 'platform' => 'PlayStation', 'price' => 54.99, 'image' => '../assets/the_last_of_part1.webp', 'stock' => 45],
        ['id' => 4, 'title' => 'Hades', 'genre' => 'Roguelike', 'platform' => 'PC', 'price' => 24.99, 'image' => '../assets/hades.webp', 'stock' => 100],
        ['id' => 5, 'title' => 'FC 26', 'genre' => 'Deportes', 'platform' => 'PC', 'price' => 39.99, 'image' => '../assets/ea_sports_fc_26.webp', 'stock' => 70],
        ['id' => 6, 'title' => 'Cyberpunk 2077', 'genre' => 'RPG', 'platform' => 'PC', 'price' => 44.99, 'image' => '../assets/cyberpunk.webp', 'stock' => 90],
        ['id' => 7, 'title' => 'Mario Kart 8 Deluxe', 'genre' => 'Carreras', 'platform' => 'Nintendo', 'price' => 59.99, 'image' => '../assets/maria_8_deluxe.webp', 'stock' => 55],
        ['id' => 8, 'title' => 'Zelda: Tears of the Kingdom', 'genre' => 'Aventura', 'platform' => 'Nintendo', 'price' => 69.99, 'image' => '../assets/zelda.webp', 'stock' => 40],
        ['id' => 9, 'title' => 'Resident Evil 4 Remake', 'genre' => 'Terror', 'platform' => 'PlayStation', 'price' => 49.99, 'image' => '../assets/resident_evil_4_remake.webp', 'stock' => 50],
        ['id' => 10, 'title' => 'Hollow Knight', 'genre' => 'Metroidvania', 'platform' => 'PC', 'price' => 14.99, 'image' => '../assets/hollow_knight.webp', 'stock' => 120],
        ['id' => 11, 'title' => 'Street Fighter 6', 'genre' => 'Pelea', 'platform' => 'PlayStation', 'price' => 44.99, 'image' => '../assets/street_fighter.webp', 'stock' => 65],
        ['id' => 12, 'title' => 'Minecraft', 'genre' => 'Sandbox', 'platform' => 'Multi', 'price' => 29.99, 'image' => '../assets/minecraft.webp', 'stock' => 200],
    ];
}

/**
 * @return list<array<string, mixed>>
 */
function products_load(): array
{
    $path = products_json_path();
    if (!is_readable($path)) {
        $defaults = products_default_catalog();
        products_save($defaults);
        return $defaults;
    }
    $raw = file_get_contents($path);
    if ($raw === false) {
        return products_default_catalog();
    }
    $data = json_decode($raw, true);
    if (!is_array($data)) {
        return products_default_catalog();
    }
    $out = [];
    foreach ($data as $row) {
        if (!is_array($row)) {
            continue;
        }
        $out[] = [
            'id' => (int) ($row['id'] ?? 0),
            'title' => (string) ($row['title'] ?? ''),
            'genre' => (string) ($row['genre'] ?? ''),
            'platform' => (string) ($row['platform'] ?? ''),
            'price' => (float) ($row['price'] ?? 0),
            'image' => (string) ($row['image'] ?? ''),
            'stock' => max(0, (int) ($row['stock'] ?? 0)),
        ];
    }
    return $out;
}

/**
 * @param list<array<string, mixed>> $list
 */
function products_save(array $list): bool
{
    $path = products_json_path();
    $dir = dirname($path);
    if (!is_dir($dir)) {
        if (!mkdir($dir, 0755, true) && !is_dir($dir)) {
            return false;
        }
    }
    $json = json_encode(array_values($list), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    if ($json === false) {
        return false;
    }
    return file_put_contents($path, $json) !== false;
}

/**
 * @return array<string, mixed>|null
 */
function products_find_by_id(int $id): ?array
{
    foreach (products_load() as $g) {
        if ((int) ($g['id'] ?? 0) === $id) {
            return $g;
        }
    }
    return null;
}

/**
 * @param list<array{id:int,quantity:int}> $lines
 */
function products_stock_sufficient(array $lines): bool
{
    $byId = [];
    foreach (products_load() as $g) {
        $byId[(int) $g['id']] = (int) $g['stock'];
    }
    foreach ($lines as $line) {
        $id = (int) ($line['id'] ?? 0);
        $qty = (int) ($line['quantity'] ?? 0);
        if ($id <= 0 || $qty <= 0) {
            continue;
        }
        if (($byId[$id] ?? 0) < $qty) {
            return false;
        }
    }
    return true;
}

/**
 * @param list<array{id:int,quantity:int}> $lines
 */
function products_decrement_stock(array $lines): bool
{
    $list = products_load();
    $index = [];
    foreach ($list as $i => $g) {
        $index[(int) $g['id']] = $i;
    }
    foreach ($lines as $line) {
        $id = (int) ($line['id'] ?? 0);
        $qty = (int) ($line['quantity'] ?? 0);
        if ($id <= 0 || $qty <= 0 || !isset($index[$id])) {
            continue;
        }
        $i = $index[$id];
        $list[$i]['stock'] = max(0, (int) $list[$i]['stock'] - $qty);
    }
    return products_save($list);
}
