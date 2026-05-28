<?php

/**
 * ByteStore - Database Configuration
 * 
 * Archivo centralizado para la conexión PDO a MySQL
 * Se requiere en todos los modelos
 */

$host     = 'localhost';
$dbname   = 'db_bytestore';
$user     = 'root';
$password = '';
$charset  = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$dbname;charset=$charset";

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $password, $options);
} catch (PDOException $e) {
    // En producción: registrar en log, nunca mostrar al usuario
    error_log("Database Connection Error: " . $e->getMessage());
    
    // Mostrar mensaje genérico
    http_response_code(500);
    die(json_encode([
        'error' => 'Error de conexión a la base de datos',
        'message' => 'Por favor, intenta más tarde.'
    ]));
}
