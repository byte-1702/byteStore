<?php

require_once(__DIR__ . '/../config/database.php');

class Pedido {
    private PDO $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    /**
     * Crea un pedido con sus detalles (usa transacción)
     * 
     * $items = [
     *     ['id_producto' => 1, 'cantidad' => 2, 'precio_unitario' => 59.99],
     *     ['id_producto' => 3, 'cantidad' => 1, 'precio_unitario' => 29.99],
     * ]
     */
    public function crear(int $id_usuario, array $items): int {
        try {
            // Calcular total
            $total = array_sum(array_map(
                fn($item) => $item['cantidad'] * $item['precio_unitario'],
                $items
            ));

            if ($total <= 0) {
                throw new Exception("El total del pedido debe ser mayor a 0");
            }

            $this->pdo->beginTransaction();

            // 1. Insertar cabecera del pedido
            $stmt = $this->pdo->prepare("
                INSERT INTO pedido (id_usuario, fecha_pedido, total, estado)
                VALUES (:id_usuario, CURDATE(), :total, 'pendiente')
            ");
            $stmt->execute([
                ':id_usuario' => $id_usuario,
                ':total'      => $total
            ]);
            $id_pedido = (int) $this->pdo->lastInsertId();

            // 2. Insertar detalles del pedido
            $stmtDetalle = $this->pdo->prepare("
                INSERT INTO detalle_pedido (id_pedido, id_producto, cantidad, precio_unitario)
                VALUES (:id_pedido, :id_producto, :cantidad, :precio_unitario)
            ");

            foreach ($items as $item) {
                $stmtDetalle->execute([
                    ':id_pedido'       => $id_pedido,
                    ':id_producto'     => $item['id_producto'],
                    ':cantidad'        => $item['cantidad'],
                    ':precio_unitario' => $item['precio_unitario'],
                ]);
            }

            $this->pdo->commit();
            return $id_pedido;

        } catch (Exception $e) {
            $this->pdo->rollBack();
            throw new Exception("Error al crear pedido: " . $e->getMessage());
        }
    }

    /**
     * Obtiene todos los pedidos de un usuario
     */
    public function porUsuario(int $id_usuario): array {
        try {
            $sql = "
                SELECT 
                    p.id_pedido,
                    p.fecha_pedido,
                    p.total,
                    p.estado,
                    COUNT(dp.id_detalle) as cantidad_items
                FROM pedido p
                LEFT JOIN detalle_pedido dp ON p.id_pedido = dp.id_pedido
                WHERE p.id_usuario = :id_usuario
                GROUP BY p.id_pedido
                ORDER BY p.fecha_pedido DESC
            ";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':id_usuario' => $id_usuario]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        } catch (PDOException $e) {
            throw new Exception("Error al obtener pedidos del usuario: " . $e->getMessage());
        }
    }

    /**
     * Obtiene los detalles de un pedido específico
     */
    public function detalles(int $id_pedido): array {
        try {
            $sql = "
                SELECT 
                    dp.id_detalle,
                    dp.id_producto,
                    dp.cantidad,
                    dp.precio_unitario,
                    pr.nombre_producto,
                    (dp.cantidad * dp.precio_unitario) as subtotal
                FROM detalle_pedido dp
                INNER JOIN productos pr ON dp.id_producto = pr.id_producto
                WHERE dp.id_pedido = :id_pedido
                ORDER BY dp.id_detalle ASC
            ";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':id_pedido' => $id_pedido]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        } catch (PDOException $e) {
            throw new Exception("Error al obtener detalles del pedido: " . $e->getMessage());
        }
    }

    /**
     * Obtiene un pedido por su ID
     */
    public function porId(int $id_pedido): array|false {
        try {
            $stmt = $this->pdo->prepare("
                SELECT * FROM pedido WHERE id_pedido = :id_pedido
            ");
            $stmt->execute([':id_pedido' => $id_pedido]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Error al obtener pedido: " . $e->getMessage());
        }
    }

    /**
     * Cambia el estado de un pedido
     */
    public function cambiarEstado(int $id_pedido, string $estado): bool {
        try {
            $estadosValidos = ['pendiente', 'enviado', 'entregado', 'cancelado'];
            if (!in_array($estado, $estadosValidos)) {
                throw new Exception("Estado inválido: $estado");
            }

            $stmt = $this->pdo->prepare(
                "UPDATE pedido SET estado = :estado WHERE id_pedido = :id_pedido"
            );
            return $stmt->execute([
                ':estado'    => $estado,
                ':id_pedido' => $id_pedido
            ]);
        } catch (PDOException $e) {
            throw new Exception("Error al cambiar estado del pedido: " . $e->getMessage());
        }
    }

    /**
     * Cancela un pedido
     */
    public function cancelar(int $id_pedido): bool {
        try {
            $stmt = $this->pdo->prepare(
                "UPDATE pedido SET estado = 'cancelado' WHERE id_pedido = :id_pedido AND estado = 'pendiente'"
            );
            return $stmt->execute([':id_pedido' => $id_pedido]);
        } catch (PDOException $e) {
            throw new Exception("Error al cancelar pedido: " . $e->getMessage());
        }
    }

    /**
     * Elimina un pedido (solo si está pendiente)
     */
    public function eliminar(int $id_pedido): bool {
        try {
            $this->pdo->beginTransaction();

            // Eliminar detalles
            $stmt = $this->pdo->prepare("DELETE FROM detalle_pedido WHERE id_pedido = :id_pedido");
            $stmt->execute([':id_pedido' => $id_pedido]);

            // Eliminar pedido
            $stmt = $this->pdo->prepare("DELETE FROM pedido WHERE id_pedido = :id_pedido AND estado = 'pendiente'");
            $resultado = $stmt->execute([':id_pedido' => $id_pedido]);

            $this->pdo->commit();
            return $resultado;
        } catch (Exception $e) {
            $this->pdo->rollBack();
            throw new Exception("Error al eliminar pedido: " . $e->getMessage());
        }
    }
}
