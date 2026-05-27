<?php
require_once __DIR__ . '/../config/database.php';

class Categoria {
    private PDO $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    public function todas(): array {
        return $this->pdo->query("select * from categoria order by NOMBRE")->fetchALL();
    }

    public function crear(string $nombre): int {
        $stmt = $this->pdo->prepare(
            "INSERT INTO CATEGORIA (nombre) VALUES (:nombre)"
        );
        $stmt->execute([':nombre' => $nombre]);
        return (int) $this->pdo->lastInsertId();
    }

    public function buscarPorId(int $id_categoria): array|false {
        $stmt = $this->pdo->prepare(
            "SELECT * FROM CATEGORIA WHERE id_categoria = :id_categoria"
        );
        $stmt->execute([':id_categoria => $id_categoria']);
        return $stmt->fetch();
    }
}