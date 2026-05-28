<?php

require_once(__DIR__ . '/../config/database.php');

class Producto {
    private PDO $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    public function todos(): array {
        try {
            $sql = "
                SELECT 
                    p.id_producto,
                    p.nombre_producto,
                    p.precio,
                    p.id_categoria,
                    c.nombre_categoria
                FROM productos p
                INNER JOIN categoria c 
                    ON p.id_categoria = c.id_categoria
                ORDER BY p.nombre_producto
            ";

            $stmt = $this->pdo->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        } catch (PDOException $e) {
            throw new Exception("Error al obtener productos: " . $e->getMessage());
        }
    }

    public function porId(int $id): array|false {
        try {
            $stmt = $this->pdo->prepare("
                SELECT p.*, c.nombre_categoria 
                FROM productos p
                INNER JOIN categoria c ON p.id_categoria = c.id_categoria
                WHERE p.id_producto = :id
            ");
            $stmt->execute([':id' => $id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Error al obtener producto: " . $e->getMessage());
        }
    }
}

?>