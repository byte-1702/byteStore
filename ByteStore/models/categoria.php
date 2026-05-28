<?php
require_once __DIR__ . '/../config/database.php';

class Categoria {
    private PDO $pdo;

    // Recibe la conexión correcta ($conexion o $pdo) detectada en index.php
    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    public function todas(): array {
        // Asegúrate de que 'nombre_categoria' exista tal cual en tu BD
        return $this->pdo->query("SELECT * FROM CATEGORIA ORDER BY nombre_categoria")->fetchAll(PDO::FETCH_ASSOC);
    }

    public function crear(string $nombre): int {
        $stmt = $this->pdo->prepare(
            "INSERT INTO CATEGORIA (nombre_categoria) VALUES (:nombre)"
        );
        $stmt->execute([':nombre' => $nombre]);
        return (int) $this->pdo->lastInsertId();
    }

    public function buscarPorId(int $id): array|false {
        $stmt = $this->pdo->prepare(
            "SELECT * FROM CATEGORIA WHERE id_categoria = :id" // ¡Corregido id -> id_categoria!
        );
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}