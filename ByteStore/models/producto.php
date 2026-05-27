<?php
require_once __DIR__ . '/../config/database.php';

class Producto {
    private PDO $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    public function todos(): array {
        return $this->pdo->query("
            SELECT p.*, c.NOMBRE AS categoria_nombre
            FROM PRODUCTOS p
            INNER JOIN CATEGORIA c ON p.categoria_id = c.id
            ORDER BY p.nombre
        ")->fetchAll();
    }

    public function porCategoria(int $categoriaId): array {
        $stmt = $this->pdo->prepare("
            SELECT p.*, c.NOMBRE AS categoria_nombre
            FROM PRODUCTOS p
            INNER JOIN CATEGORIA c ON p.categoria_id = c.id
            WHERE p.categoria_id = :categoria_id
        ");
        $stmt->execute([':categoria_id' => $categoriaId]);
        return $stmt->fetchAll();
    }

    public function crear(string $nombre, float $precio, int $categoriaId): int {
        $stmt = $this->pdo->prepare("
            INSERT INTO PRODUCTOS (nombre, precio, categoria_id)
            VALUES (:nombre, :precio, :categoria_id)
        ");
        $stmt->execute([
            ':nombre'       => $nombre,
            ':precio'       => $precio,
            ':categoria_id' => $categoriaId,
        ]);
        return (int) $this->pdo->lastInsertId();
    }

    public function actualizarPrecio(int $id, float $precio): bool {
        $stmt = $this->pdo->prepare(
            "UPDATE PRODUCTOS SET precio = :precio WHERE id = :id"
        );
        return $stmt->execute([':precio' => $precio, ':id' => $id]);
    }
}