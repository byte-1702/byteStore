<?php
require_once __DIR__ . '/../config/database.php';

class Categoria {
    private PDO $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    public function todas(): array {
        return $this->pdo->query("SELECT * FROM CATEGORIA ORDER BY NOMBRE")->fetchAll();
    }

    public function crear(string $nombre): int {
        $stmt = $this->pdo->prepare(
            "INSERT INTO CATEGORIA (NOMBRE) VALUES (:nombre)"
        );
        $stmt->execute([':nombre' => $nombre]);
        return (int) $this->pdo->lastInsertId();
    }

    public function buscarPorId(int $id): array|false {
        $stmt = $this->pdo->prepare(
            "SELECT * FROM CATEGORIA WHERE id = :id"  // ✅ comillas corregidas
        );
        $stmt->execute([':id' => $id]);               // ✅ parámetro separado
        return $stmt->fetch();
    }
}