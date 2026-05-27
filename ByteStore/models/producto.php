<?php
require_once __DIR__ . '/../config/database.php';

class Producto {
    private PDO $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    public function todos(): array {
        // CORREGIDO: El INNER JOIN ahora usa 'id_categoria'
        return $this->pdo->query("
            SELECT p.*, c.nombre_categoria AS categoria_nombre
            FROM PRODUCTOS p
            INNER JOIN CATEGORIA c ON p.id_categoria = c.id
            ORDER BY p.nombre
        ")->fetchAll();
    }

    public function porCategoria(int $categoriaId): array {
        // CORREGIDO: Filtro y JOIN adaptados a tu .sql real
        $stmt = $this->pdo->prepare("
            SELECT p.*, c.nombre_categoria AS categoria_nombre
            FROM PRODUCTOS p
            INNER JOIN CATEGORIA c ON p.id_categoria = c.id
            WHERE p.id_categoria = :categoria_id
        ");
        $stmt->execute([':categoria_id' => $categoriaId]);
        return $stmt->fetchAll();
    }

    public function crear(string $nombre, float $precio, int $categoriaId): int {
        // CORREGIDO: Insertar en 'valor' e 'id_categoria'
        $stmt = $this->pdo->prepare("
            INSERT INTO PRODUCTOS (nombre, valor, id_categoria)
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
        // CORREGIDO: Modificar la columna 'valor'
        $stmt = $this->pdo->prepare("
            UPDATE PRODUCTOS 
            SET valor = :precio 
            WHERE id = :id
        ");
        return $stmt->execute([
            ':precio' => $precio,
            ':id'     => $id,
        ]);
    }

    public function eliminar(int $id): bool {
        $stmt = $this->pdo->prepare("
            DELETE FROM PRODUCTOS 
            WHERE id = :id
        ");
        return $stmt->execute([':id' => $id]);
    }
}