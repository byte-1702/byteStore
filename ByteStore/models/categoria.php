<?php
require_once __DIR__ . '/../config/database.php';

class Categoria {
    private PDO $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    public function todas(): array {
        // Corregido: NOMBRE cambia a nombre_categoria
        return $this->pdo->query("SELECT * FROM CATEGORIA ORDER BY nombre_categoria")->fetchAll();
    }

    public function crear(string $nombre): int {
        // Corregido: NOMBRE cambia a nombre_categoria
        $stmt = $this->pdo->prepare(
            "INSERT INTO CATEGORIA (nombre_categoria) VALUES (:nombre)"
        );
        $stmt->execute([':nombre' => $nombre]);
        return (int) $this->pdo->lastInsertId();
    }

    public function buscarPorId(int $id): array|false {
        $stmt = $this->pdo->prepare(
            "SELECT * FROM CATEGORIA WHERE id = :id"
        );
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }
}