<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Producto.php';
require_once __DIR__ . '/../models/Pedido.php';
require_once __DIR__ . '/../models/Usuario.php';

// Instanciar modelos pasando $pdo
$productoModel = new Producto($pdo);
$pedidoModel   = new Pedido($pdo);
$usuarioModel  = new Usuario($pdo);

// --- Listar productos ---
$productos = $productoModel->todos();

// --- Registrar usuario ---
// $id = $usuarioModel->crear('Juan', 'juan@mail.com', '123456');

// --- Login ---
// $usuario = $usuarioModel->login('juan@mail.com', '123456');

// --- Crear pedido (carrito de compras) ---
// $pedidoModel->crear($usuario['id'], [
//     ['producto_id' => 1, 'cantidad' => 2, 'precio_unitario' => 29.99],
//     ['producto_id' => 3, 'cantidad' => 1, 'precio_unitario' => 15.00],
// ]);

foreach ($productos as $p) {
    echo "{$p['nombre']} - \${$p['precio']} ({$p['categoria_nombre']})<br>";
}