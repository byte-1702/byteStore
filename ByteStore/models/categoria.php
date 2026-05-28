<?php

require_once(__DIR__ . '/../config/database.php');

class Categoria {
    private PDO $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    /**
     * Obtiene todas las categorías
     */
    public function todas(): array {
        try {
            $sql = "SELECT * FROM categoria ORDER BY nombre_categoria ASC";
            $stmt = $this->pdo->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        } catch (PDOException $e) {
            throw new Exception("Error al obtener categorías: " . $e->getMessage());
        }
    }

    /**
     * Busca una categoría por ID
     */
    public function porId(int $id_categoria): array|false {
        try {
            $stmt = $this->pdo->prepare(
                "SELECT * FROM categoria WHERE id_categoria = :id_categoria"
            );
            $stmt->execute([':id_categoria' => $id_categoria]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Error al obtener categoría: " . $e->getMessage());
        }
    }

    /**
     * Crea una nueva categoría
     */
    public function crear(string $nombre): int {
        try {
            $stmt = $this->pdo->prepare(
                "INSERT INTO categoria (nombre_categoria) VALUES (:nombre_categoria)"
            );
            $stmt->execute([':nombre_categoria' => $nombre]);
            return (int) $this->pdo->lastInsertId();
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) { // Violación de restricción UNIQUE
                throw new Exception("La categoría ya existe");
            }
            throw new Exception("Error al crear categoría: " . $e->getMessage());
        }
    }

    /**
     * Actualiza una categoría
     */
    public function actualizar(int $id_categoria, string $nombre): bool {
        try {
            $stmt = $this->pdo->prepare(
                "UPDATE categoria SET nombre_categoria = :nombre_categoria WHERE id_categoria = :id_categoria"
            );
            return $stmt->execute([
                ':nombre_categoria' => $nombre,
                ':id_categoria'     => $id_categoria
            ]);
        } catch (PDOException $e) {
            throw new Exception("Error al actualizar categoría: " . $e->getMessage());
        }
    }

    /**
     * Elimina una categoría (si no tiene productos asociados)
     */
    public function eliminar(int $id_categoria): bool {
        try {
            $stmt = $this->pdo->prepare("DELETE FROM categoria WHERE id_categoria = :id_categoria");
            return $stmt->execute([':id_categoria' => $id_categoria]);
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) { // Restricción de clave foránea
                throw new Exception("No se puede eliminar: hay productos asociados a esta categoría");
            }
            throw new Exception("Error al eliminar categoría: " . $e->getMessage());
        }
    }
}
