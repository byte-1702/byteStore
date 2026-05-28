<?php

require_once(__DIR__ . '/../config/database.php');

class Producto {
    private PDO $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    /**
     * Obtiene todos los productos con su categoría
     */
    public function todos(): array {
        try {
            $sql = "
                SELECT 
                    p.id_producto,
                    p.nombre_producto,
                    p.precio,
                    p.descripcion,
                    p.imagen_url,
                    p.id_categoria,
                    c.nombre_categoria
                FROM productos p
                INNER JOIN categoria c ON p.id_categoria = c.id_categoria
                ORDER BY p.nombre_producto ASC
            ";

            $stmt = $this->pdo->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        } catch (PDOException $e) {
            throw new Exception("Error al obtener productos: " . $e->getMessage());
        }
    }

    /**
     * Obtiene productos por categoría
     */
    public function porCategoria(int $id_categoria): array {
        try {
            $sql = "
                SELECT 
                    p.id_producto,
                    p.nombre_producto,
                    p.precio,
                    p.descripcion,
                    p.imagen_url,
                    p.id_categoria,
                    c.nombre_categoria
                FROM productos p
                INNER JOIN categoria c ON p.id_categoria = c.id_categoria
                WHERE p.id_categoria = :id_categoria
                ORDER BY p.nombre_producto ASC
            ";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':id_categoria' => $id_categoria]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        } catch (PDOException $e) {
            throw new Exception("Error al obtener productos por categoría: " . $e->getMessage());
        }
    }

    /**
     * Obtiene un producto por su ID
     */
    public function porId(int $id_producto): array|false {
        try {
            $sql = "
                SELECT 
                    p.id_producto,
                    p.nombre_producto,
                    p.precio,
                    p.descripcion,
                    p.imagen_url,
                    p.id_categoria,
                    c.nombre_categoria
                FROM productos p
                INNER JOIN categoria c ON p.id_categoria = c.id_categoria
                WHERE p.id_producto = :id_producto
            ";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':id_producto' => $id_producto]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Error al obtener producto: " . $e->getMessage());
        }
    }

    /**
     * Crea un nuevo producto
     */
    public function crear(string $nombre, float $precio, int $id_categoria, string $descripcion = '', string $imagen_url = ''): int {
        try {
            $sql = "
                INSERT INTO productos (nombre_producto, precio, id_categoria, descripcion, imagen_url)
                VALUES (:nombre_producto, :precio, :id_categoria, :descripcion, :imagen_url)
            ";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                ':nombre_producto' => $nombre,
                ':precio'          => $precio,
                ':id_categoria'    => $id_categoria,
                ':descripcion'     => $descripcion,
                ':imagen_url'      => $imagen_url,
            ]);

            return (int) $this->pdo->lastInsertId();
        } catch (PDOException $e) {
            throw new Exception("Error al crear producto: " . $e->getMessage());
        }
    }

    /**
     * Actualiza el precio de un producto
     */
    public function actualizarPrecio(int $id_producto, float $precio): bool {
        try {
            $stmt = $this->pdo->prepare(
                "UPDATE productos SET precio = :precio WHERE id_producto = :id_producto"
            );
            return $stmt->execute([
                ':precio'      => $precio,
                ':id_producto' => $id_producto
            ]);
        } catch (PDOException $e) {
            throw new Exception("Error al actualizar precio: " . $e->getMessage());
        }
    }

    /**
     * Elimina un producto
     */
    public function eliminar(int $id_producto): bool {
        try {
            $stmt = $this->pdo->prepare(
                "DELETE FROM productos WHERE id_producto = :id_producto"
            );
            return $stmt->execute([':id_producto' => $id_producto]);
        } catch (PDOException $e) {
            throw new Exception("Error al eliminar producto: " . $e->getMessage());
        }
    }
}
