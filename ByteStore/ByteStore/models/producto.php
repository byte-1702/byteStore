<?php
require_once __DIR__ . '/../config/database.php';

class Producto {
    private PDO $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    public function todos(): array {
        return $this->pdo->query("
        SELECT p.*, c.nombre AS categoria_nombre
        FROM PRODUCTOS p
        INNER JOIN CATEGORIA c ON p.id_categoria = c.id
        ORDER BY p.nombre
        ")->fetchALL();
    }

    public function porCategoria(int $categoriaId): array {
        $stmt = $this->pdo->prepare("
        SELECT p.*, c.nombre AS nombre_categoria
        FROM PRODUCTOS p
        INNER JOIN CATEGORIA c ON p.id_categoria = c.id
        WHERE p.id_categoria = :id_categoria
        ");
        $stmt->execute(['id_categoria' => $categoriaId]);
        return $stmt->fetch();
    }

    public function crear(string $nombre, float $precio, int $categoriaId): int {
        $stmt = $this->pdo->prepare("
        INSERT INTO PRODUCTOS (nombre, precios, id_categoria)
        VALUES (:nombre, :precio, :id_categoria)
        ");
        $stmt->execute([
            ':nombre' = $nombre,
            ':precio' => $precio,
            'id_categoria' => $categoriaId,
        ]);
        return (int) $this->pdo->lastInsertId();
    }

    public function actualizarPrecio(int $id, float $precio): bool {
        $stmt = $this->pdo->prepare(
            "UPDATE PRODUCTOS SET precio = :precio WHERE id_categoria = id_categoria"
        );
        return $stmt->execute([':precio' => $precio, ':id_categoria' => $id]);
    }

}