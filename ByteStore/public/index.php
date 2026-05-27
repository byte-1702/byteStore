<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/producto.php';

$productoModel = new Producto($pdo);

$productos = $productoModel->todos();

foreach ($productos as $p) {
    echo "{$p['nombre']} - \${$p['precio']} ({$p['categoria_nombre']})<br>";
}