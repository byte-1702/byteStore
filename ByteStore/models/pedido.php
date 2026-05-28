<?php
require_once __DIR__ . '/../config/database.php';

class Pedido {
    private PDO $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    // Crear pedido — usa transacción para seguridad
    // $items = [['producto_id'=>1, 'cantidad'=>2, 'precio_unitario'=>9.99], ...]
    public function crear(int $usuarioId, array $items): int {
        $total = array_sum(array_map(
            fn($i) => $i['cantidad'] * $i['precio_unitario'], $items
        ));

        $this->pdo->beginTransaction();
        try {
            // Insertar cabecera del pedido
            $stmt = $this->pdo->prepare("
                INSERT INTO PEDIDO (usuario_id, fecha_pedido, total, estado)
                VALUES (:usuario_id, CURDATE(), :total, 'pendiente')
            ");
            $stmt->execute([':usuario_id' => $usuarioId, ':total' => $total]);
            $pedidoId = (int) $this->pdo->lastInsertId();

            // Insertar cada línea de detalle
            $stmtDetalle = $this->pdo->prepare("
                INSERT INTO DETALLE_PEDIDO (pedido_id, producto_id, cantidad, precio_unitario)
                VALUES (:pedido_id, :producto_id, :cantidad, :precio_unitario)
            ");
            foreach ($items as $item) {
                $stmtDetalle->execute([
                    ':pedido_id'      => $pedidoId,
                    ':producto_id'    => $item['producto_id'],
                    ':cantidad'       => $item['cantidad'],
                    ':precio_unitario'=> $item['precio_unitario'],
                ]);
            }

            $this->pdo->commit();
            return $pedidoId;

        } catch (Exception $e) {
            $this->pdo->rollBack(); // si algo falla, nada se guarda
            throw $e;
        }
    }

    // Pedidos de un usuario con sus detalles
    public function porUsuario(int $usuarioId): array {
        $stmt = $this->pdo->prepare("
            SELECT pe.*, 
                   dp.producto_id, dp.cantidad, dp.precio_unitario,
                   pr.nombre AS producto_nombre
            FROM PEDIDO pe
            INNER JOIN DETALLE_PEDIDO dp ON dp.pedido_id = pe.id
            INNER JOIN PRODUCTOS pr      ON pr.id = dp.producto_id
            WHERE pe.usuario_id = :usuario_id
            ORDER BY pe.fecha_pedido DESC
        ");
        $stmt->execute([':usuario_id' => $usuarioId]);
        return $stmt->fetchAll();
    }

    // Cambiar estado (pendiente → enviado → entregado)
    public function cambiarEstado(int $pedidoId, string $estado): bool {
        $estados = ['pendiente', 'enviado', 'entregado', 'cancelado'];
        if (!in_array($estado, $estados)) {
            throw new InvalidArgumentException("Estado inválido: $estado");
        }
        $stmt = $this->pdo->prepare(
            "UPDATE PEDIDO SET estado = :estado WHERE id = :id"
        );
        return $stmt->execute([':estado' => $estado, ':id' => $pedidoId]);
    }
}